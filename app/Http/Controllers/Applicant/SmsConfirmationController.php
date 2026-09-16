<?php

namespace App\Http\Controllers\Applicant;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\ApplicationStatusHistory;
use App\Services\SmsService;
use Illuminate\Http\Request;

class SmsConfirmationController extends Controller
{
    protected function owner(Application $application): void
    {
        abort_if($application->applicant?->user_id !== auth()->id() && ! auth()->user()->isAdmin(), 403);
        abort_if(! in_array($application->status, [Application::STATUS_SELECTED, Application::STATUS_ADMITTED]), 403, 'No active selection to confirm.');
        abort_if($application->acceptance_confirmed, 403, 'Selection already confirmed.');
    }

    protected function phoneFor(Application $application): string
    {
        $phone = $application->applicant?->phone ?? auth()->user()->phone;

        abort_if(! $phone, 403, 'No phone number on file. Update it in your profile first.');

        return $phone;
    }

    public function sendCode(Request $request, Application $application)
    {
        $this->owner($application);
        $phone = $this->phoneFor($application);

        $code = app(SmsService::class)->sendVerificationCode(
            $phone,
            SmsService::PURPOSE_SELECTION_ACCEPTANCE,
            $application->applicant?->user_id,
            $application->id,
        );

        abort_if(! $code, 500, 'SMS could not be sent. The SMS service may be disabled or not configured.');

        $masked = substr($phone, 0, 4).'****'.substr($phone, -2);

        return back()->with('success', 'Confirmation code sent to '.$masked.'. Enter it below — it expires in 10 minutes.');
    }

    public function confirm(Request $request, Application $application)
    {
        $this->owner($application);

        $d = $request->validate(['code' => ['required', 'string', 'size:6']]);
        $phone = $this->phoneFor($application);

        if (! app(SmsService::class)->verifyCode($phone, SmsService::PURPOSE_SELECTION_ACCEPTANCE, $d['code'])) {
            return back()->with('error', 'Invalid or expired confirmation code. Please request a new one.');
        }

        $application->update([
            'acceptance_confirmed'      => true,
            'acceptance_confirmed_at'   => now(),
        ]);

        ApplicationStatusHistory::create([
            'application_id' => $application->id,
            'old_status'     => $application->status,
            'new_status'     => $application->status,
            'changed_by'     => auth()->id(),
            'remarks'        => 'Acceptance confirmed by applicant via SMS verification code.',
        ]);

        return back()->with('success', 'Acceptance confirmed successfully.');
    }

    public function confirmDirect(Application $application)
    {
        $this->owner($application);

        $selections = $application->applicant->applications()
            ->whereIn('status', [Application::STATUS_SELECTED, Application::STATUS_ADMITTED])
            ->count();

        abort_if($selections > 1, 403, 'You have multiple selections — confirm each one with an SMS code.');

        $application->update([
            'acceptance_confirmed'      => true,
            'acceptance_confirmed_at'   => now(),
        ]);

        ApplicationStatusHistory::create([
            'application_id' => $application->id,
            'old_status'     => $application->status,
            'new_status'     => $application->status,
            'changed_by'     => auth()->id(),
            'remarks'        => 'Acceptance confirmed by applicant (single selection).',
        ]);

        return back()->with('success', 'Acceptance confirmed successfully.');
    }
}