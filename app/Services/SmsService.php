<?php

namespace App\Services;

use App\Models\Application;
use App\Models\NotificationLog;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    public const PURPOSE_SELECTION_ACCEPTANCE = 'SELECTION_ACCEPTANCE';

    protected bool $enabled;
    protected string $provider;
    protected string $senderId;

    public function __construct()
    {
        $this->enabled  = (bool) Setting::getValue('sms_enabled', true);
        $this->provider = (string) Setting::getValue('sms_provider', 'log');
        $this->senderId = (string) Setting::getValue('sms_sender_id', 'TANZANIATIP');
    }

    public function enabled(): bool
    {
        return $this->enabled;
    }

    /**
     * Send an SMS and record it in the notifications log.
     */
    public function send(
        string $phone,
        string $message,
        ?string $template = null,
        ?string $subject = null,
        ?int $userId = null,
        ?int $applicationId = null,
    ): bool {
        if (! $this->enabled) {
            return false;
        }

        if ($this->provider === 'messaging') {
            $delivered = $this->sendViaMessagingService($phone, $message);
        } else {
            $delivered = true;
            Log::info('SMS [log driver] to '.$phone.': '.$message);
        }

        NotificationLog::create([
            'user_id'        => $userId,
            'application_id' => $applicationId,
            'channel'        => 'sms',
            'template'       => $template,
            'recipient'      => $phone,
            'subject'        => $subject,
            'body'           => $message,
            'status'         => $delivered ? 'SENT' : 'FAILED',
            'sent_at'        => now(),
        ]);

        return $delivered;
    }

    protected function sendViaMessagingService(string $phone, string $message): bool
    {
        $token = (string) Setting::getValue('sms_api_key', '');
        $test  = (bool) Setting::getValue('sms_test_mode', true);

        if ($token === '') {
            Log::warning('SMS [messaging-service.co.tz] skipped: api key not configured.', ['phone' => $phone]);

            return false;
        }

        $endpoint = $test
            ? 'https://messaging-service.co.tz/api/sms/v2/test/text/single'
            : 'https://messaging-service.co.tz/api/sms/v2/text/single';

        try {
            $response = Http::timeout(15)->withHeaders([
                'Authorization' => 'Bearer '.$token,
                'Content-Type'  => 'application/json',
                'Accept'        => 'application/json',
            ])->post($endpoint, [
                'from' => $this->senderId,
                'to'   => ltrim($phone, '+'),
                'text' => $message,
            ]);

            $body = $response->json() ?? [];
            $ok   = $response->successful() && isset($body['messages'][0]['status']['groupId']);

            if (! $ok) {
                Log::warning('SMS [messaging-service.co.tz] failed.', ['phone' => $phone, 'response' => $body]);
            }

            return $ok;
        } catch (\Throwable $e) {
            Log::error('SMS [messaging-service.co.tz] exception: '.$e->getMessage(), ['phone' => $phone]);

            return false;
        }
    }

    /**
     * Send a short-lived numeric verification code to the phone.
     * Returns the code so callers can present it in dev mode.
     */
    public function sendVerificationCode(
        string $phone,
        string $purpose,
        ?int $userId = null,
        ?int $applicationId = null,
    ): ?string {
        $code    = (string) random_int(100000, 999999);
        $message = $this->codeMessage($purpose, $code);

        if (! $this->send($phone, $message, 'otp', 'Confirmation code', $userId, $applicationId)) {
            return null;
        }

        Cache::put(
            'sms_otp:'.sha1($phone.'|'.$purpose),
            ['code' => bcrypt($code), 'attempts' => 0],
            now()->addMinutes(10),
        );

        return $code;
    }

    public function verifyCode(string $phone, string $purpose, string $code): bool
    {
        $cacheKey = 'sms_otp:'.sha1($phone.'|'.$purpose);
        $stored   = Cache::get($cacheKey);

        if (! is_array($stored) || ! password_verify($code, $stored['code'])) {
            if (is_array($stored)) {
                $stored['attempts'] = ($stored['attempts'] ?? 0) + 1;
                if ($stored['attempts'] >= 5) {
                    Cache::forget($cacheKey);
                } else {
                    Cache::put($cacheKey, $stored, now()->addMinutes(10));
                }
            }

            return false;
        }

        Cache::forget($cacheKey);

        return true;
    }

    protected function codeMessage(string $purpose, string $code): string
    {
        $label = match ($purpose) {
            self::PURPOSE_SELECTION_ACCEPTANCE => 'selection acceptance',
            default                             => 'confirmation',
        };

        return 'Your '.$label.' code is '.$code.'. Enter it on the portal to complete the action. Do not share it. It expires in 10 minutes.';
    }

    /**
     * Notify a candidate that they have been selected / admitted.
     */
    public function notifySelection(Application $application, string $status): bool
    {
        if (! in_array($status, [Application::STATUS_SELECTED, Application::STATUS_ADMITTED])) {
            return false;
        }

        $application->loadMissing(['applicant', 'academicYear', 'admissionWindow.admissionLevel', 'selectedProgrammes.programme']);

        $phone = $application->applicant?->phone ?? $application->applicant?->user?->phone;
        if (! $phone) {
            return false;
        }

        $programme = $application->selectedProgrammes->first()?->programme
            ?? $application->selectionResults->first()?->programme;
        $university = Setting::getValue('university_name', 'the University');
        $year       = $application->academicYear?->name ?? '';

        $message = $status === Application::STATUS_ADMITTED
            ? 'Congratulations '.$application->applicant->fullName().', you have been ADMITTED to '.$university.' for '.($programme?->name ?? $application->admissionWindow?->admissionLevel?->name).' ('.$programme?->code.') '.$year.'. Please log in to confirm and download your admission letter.'
            : 'Congratulations '.$application->applicant->fullName().', you have been SELECTED to join '.$university.' for '.($programme?->name ?? $application->admissionWindow?->admissionLevel?->name).' ('.$programme?->code.') '.$year.'. Please log in to confirm your acceptance via SMS code.';

        return $this->send(
            phone: $phone,
            message: $message,
            template: $status === Application::STATUS_ADMITTED ? 'admitted' : 'selected',
            subject: $status === Application::STATUS_ADMITTED ? 'Admission Status' : 'Selection Status',
            userId: $application->applicant?->user_id,
            applicationId: $application->id,
        );
    }

    /**
     * Notify a candidate that their application was submitted successfully.
     */
    public function notifySubmitted(Application $application): bool
    {
        $application->loadMissing(['applicant', 'academicYear', 'admissionWindow.admissionLevel']);

        $phone = $application->applicant?->phone ?? $application->applicant?->user?->phone;
        if (! $phone) {
            return false;
        }

        $university = Setting::getValue('university_acronym', 'OAS');
        $message    = 'Dear '.$application->applicant->fullName().', your '.$university.' application '.$application->application_number.' has been submitted successfully for '.$application->admissionWindow?->admissionLevel?->name.' ('.$application->academicYear?->name.'). Track your status from your account.';

        return $this->send(
            phone: $phone,
            message: $message,
            template: 'submitted',
            subject: 'Application Submitted',
            userId: $application->applicant?->user_id,
            applicationId: $application->id,
        );
    }
}