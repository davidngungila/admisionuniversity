<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;

/**
 * External academic-result verification.
 *
 * Providers:
 *  - NECTA   (O-Level / A-Level)  -> settings group `necta`, keyed by index number.
 *  - NACTVET (Certificate / Diploma) -> settings group `nactvet`, keyed by
 *       registration number (+ year of graduation) for Certificate and AVN number
 *       for Diploma.
 *
 * The HTTP contract is intentionally simple so it can be adapted to the real
 * provider once credentials/contract details are confirmed.
 *
 * Request  (POST {base_url}/api/verification/verify):
 *   {
 *     "index_number": "S0105/0013/2023",      // or "registration_number" / "avn_number"
 *     "exam_year": 2023,                       // optional for Diploma
 *     "first_name": "Tausila",                 // helps NECTA match the applicant
 *     "exam_type": "A-Level"                   // NECTA: O-Level|A-Level ; NACTVET: Certificate|Diploma
 *   }
 *
 * Expected responses:
 *   HTTP 200: { "success": true, "data": { "candidate_name": "TAUSILA MACARIUS MLULA",
 *              "index_number": "...", "exam_year": 2023,
 *              "school_name": "...",
 *              "subjects": [ { "subject": "MATHEMATICS", "grade": "A" } ] } }
 *   HTTP 4xx/5xx or success:false -> surfaced as a clear error to the applicant.
 *
 * When the provider is in test mode (`*_test_mode` = 1) representative stub data
 * is returned so the end-to-end flow works before live credentials exist.
 */
class ResultVerificationService
{
    /**
     * Verify a result against the matching external provider.
     *
     * @param array $payload keys vary by exam type:
     *               O-Level/A-Level: index_number, exam_year
     *               Certificate:     registration_number, exam_year (optional)
     *               Diploma:         avn_number
     * @return array normalized, always includes `ok`
     */
    public function verify(string $examType, array $payload): array
    {
        return in_array($examType, ['O-Level', 'A-Level'], true)
            ? $this->fetchFromNecta($examType, $payload)
            : $this->fetchFromNactvet($examType, $payload);
    }

    protected function fetchFromNecta(string $examType, array $payload): array
    {
        if (Setting::getValue('necta_enabled', '0') !== '1') {
            return $this->error('NECTA verification is currently unavailable. Please try again later.');
        }

        $identifier = $payload['index_number'] ?? '';
        $year       = $payload['exam_year'] ?? null;
        $firstName  = $payload['first_name'] ?? '';

        if (Setting::getValue('necta_test_mode', '1') === '1') {
            return $this->stubNecta($examType, $identifier, $year, $firstName);
        }

        return $this->callProvider('necta', 'NECTA', $examType, [
            'index_number' => $identifier,
            'exam_year'    => (int) $year,
            'first_name'   => $firstName,
            'exam_type'    => $examType,
        ]);
    }

    protected function fetchFromNactvet(string $examType, array $payload): array
    {
        if (Setting::getValue('nactvet_enabled', '0') !== '1') {
            return $this->error('NACTVET verification is currently unavailable. Please try again later.');
        }

        $registration = $payload['registration_number'] ?? ($payload['avn_number'] ?? '');
        $year         = $payload['exam_year'] ?? null;

        if (Setting::getValue('nactvet_test_mode', '1') === '1') {
            return $this->stubNactvet($examType, $registration, $year);
        }

        return $this->callProvider('nactvet', 'NACTVET', $examType, [
            'registration_number' => $registration,
            'avn_number'          => $payload['avn_number'] ?? '',
            'exam_year'           => $year ? (int) $year : null,
            'exam_type'           => $examType,
        ]);
    }

    protected function callProvider(string $group, string $provider, string $examType, array $body): array
    {
        $base   = Setting::getValue($group.'_base_url', '');
        $token  = Setting::getValue($group.'_api_token', '');

        if (empty($base)) {
            return $this->error($provider.' base URL is not configured.');
        }

        try {
            $response = Http::timeout(15)
                ->withToken($token)
                ->acceptJson()
                ->post(rtrim($base, '/').'/api/verification/verify', $body);
        } catch (\Throwable $e) {
            return $this->error($provider.' is unreachable. Please try again later.');
        }

        if (! $response->successful()) {
            return $this->error($provider.' could not verify these results (HTTP '.$response->status().'). Check the details and try again.');
        }

        $json = $response->json();
        if (($json['success'] ?? false) !== true) {
            return $this->error($json['message'] ?? ($json['error'] ?? $provider.' could not verify these results.'));
        }

        $data    = $json['data'] ?? [];
        $subjects = $this->subjectsFrom($data['subjects'] ?? []);

        if (empty($subjects)) {
            return $this->error($provider.' returned no subjects for this record.');
        }

        return [
            'ok'         => true,
            'provider'   => $provider,
            'exam_type'  => $examType,
            'identifier' => $data['index_number'] ?? $data['registration_number'] ?? $body['index_number'] ?? $body['registration_number'] ?? '',
            'full_name'  => $data['candidate_name'] ?? $data['full_name'] ?? $data['name'] ?? '',
            'exam_year'  => $data['exam_year'] ?? $body['exam_year'] ?? null,
            'school_name'=> $data['school_name'] ?? $data['institution_name'] ?? '',
            'subjects'   => $subjects,
        ];
    }

    protected function subjectsFrom(array $raw): array
    {
        $subjects = [];
        foreach ($raw as $row) {
            if (is_array($row) && ! empty($row['subject'])) {
                $subjects[] = [
                    'subject' => (string) $row['subject'],
                    'grade'   => strtoupper((string) ($row['grade'] ?? '')),
                ];
            }
        }

        return $subjects;
    }

    protected function stubNecta(string $examType, string $identifier, ?int $year, string $firstName = ''): array
    {
        $subjects = [
            ['subject' => 'MATHEMATICS',  'grade' => 'A'],
            ['subject' => 'ENGLISH',      'grade' => 'B'],
            ['subject' => 'KISWAHILI',    'grade' => 'A'],
            ['subject' => 'BIOLOGY',      'grade' => 'B'],
            ['subject' => 'PHYSICS',      'grade' => 'B'],
            ['subject' => 'CHEMISTRY',    'grade' => 'C'],
            ['subject' => 'GEOGRAPHY',    'grade' => 'C'],
            ['subject' => 'HISTORY',      'grade' => 'C'],
        ];

        return [
            'ok'         => true,
            'provider'   => 'NECTA',
            'exam_type'  => $examType,
            'identifier' => $identifier,
            'full_name'  => trim(ucwords(strtolower($firstName)).' Joseph Mwakaseke'),
            'exam_year'  => $year ?? (int) date('Y') - 2,
            'school_name'=> 'STUB SECONDARY SCHOOL',
            'subjects'   => $subjects,
        ];
    }

    protected function stubNactvet(string $examType, string $identifier, ?int $year): array
    {
        $subjects = $examType === 'Diploma'
            ? [
                ['subject' => 'CIVIL ENGINEERING DRAWING', 'grade' => 'A'],
                ['subject' => 'STRUCTURAL MECHANICS',      'grade' => 'B'],
                ['subject' => 'FLUID MECHANICS',           'grade' => 'A'],
                ['subject' => 'CONSTRUCTION TECHNOLOGY',   'grade' => 'B'],
                ['subject' => 'ENGINEERING MATHEMATICS',   'grade' => 'A'],
            ]
            : [
                ['subject' => 'ELECTRICAL INSTALLATION',   'grade' => 'A'],
                ['subject' => 'ENGINEERING MATHEMATICS',   'grade' => 'B'],
                ['subject' => 'ENGINEERING SCIENCE',       'grade' => 'A'],
                ['subject' => 'WORKSHOP TECHNOLOGY',       'grade' => 'B'],
            ];

        return [
            'ok'         => true,
            'provider'   => 'NACTVET',
            'exam_type'  => $examType,
            'identifier' => $identifier,
            'exam_year'  => $year ?? null,
            'school_name'=> $examType === 'Diploma' ? 'STUB POLYTECHNIC COLLEGE' : 'STUB VOCATIONAL TRAINING CENTRE',
            'subjects'   => $subjects,
        ];
    }

    protected function error(string $message): array
    {
        return ['ok' => false, 'error' => $message];
    }
}