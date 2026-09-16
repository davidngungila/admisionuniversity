<?php

namespace App\Http\Controllers\Applicant;

use App\Http\Controllers\Controller;
use App\Models\AcademicResult;
use App\Models\AdmissionWindow;
use App\Models\Application;
use App\Models\ApplicationDocument;
use App\Models\ApplicationProgramme;
use App\Models\ApplicationStepCompletion;
use App\Models\ApplicationWorkflowStep;
use App\Models\District;
use App\Models\Payment;
use App\Models\Programme;
use App\Models\Region;
use App\Models\Setting;
use App\Models\Ward;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ApplicationController extends Controller
{
    /**
     * Map a workflow step route to the method that handles it.
     */
    protected const STEP_HANDLERS = [
        'personal-info'         => 'personalInfo',
        'payment'               => 'payment',
        'academic-results'      => 'academicResults',
        'programme-application' => 'programmeApplication',
        'documents'             => 'documents',
        'submit'                => 'submit',
    ];

    public function start(AdmissionWindow $window)
    {
        if (! $window->isOpen()) {
            return back()->with('error', 'This admission window is not open for applications.');
        }

        $user      = auth()->user();
        $applicant = $user->applicant;

        if (! $applicant) {
            $applicant = $this->ensureApplicant($user);
        }

        $existing = Application::where('applicant_id', $applicant->id)
            ->where('admission_window_id', $window->id)
            ->first();

        if ($existing) {
            return redirect()->route('applicant.application.show', encId($existing->id))
                ->with('info', 'You already have an application for this admission window.');
        }

        // Do not allow the same user to start a new application while one is still in progress.
        $active = Application::where('applicant_id', $applicant->id)
            ->whereNotIn('status', [
                Application::STATUS_REJECTED,
                Application::STATUS_INELIGIBLE,
                Application::STATUS_WITHDRAWN,
            ])
            ->latest()
            ->first();

        if ($active) {
            return redirect()->route('applicant.application.show', encId($active->id))
                ->with('info', 'You already have an application in progress. You cannot start a new one until it is completed or rejected.');
        }

        // Only one application per applicant per academic year.
        $yearExisting = Application::where('applicant_id', $applicant->id)
            ->where('academic_year_id', $window->academic_year_id)
            ->latest()
            ->first();

        if ($yearExisting) {
            return redirect()->route('applicant.application.show', encId($yearExisting->id))
                ->with('info', 'You already have an application for the '.($window->academicYear->name ?? 'same academic year').'. Only one application per academic year is allowed.');
        }

        $application = Application::create([
            'applicant_id'         => $applicant->id,
            'academic_year_id'     => $window->academic_year_id,
            'admission_window_id'  => $window->id,
            'status'               => Application::STATUS_PAYMENT_PENDING,
            'progress_percentage'  => 0,
        ]);

        return redirect()->route('applicant.application.show', encId($application->id));
    }

    protected function ensureApplicant($user)
    {
        $parts = preg_split('/\s+/', trim($user->name), 3);

        return \App\Models\Applicant::create([
            'user_id'     => $user->id,
            'first_name'  => $parts[0] ?? $user->name,
            'middle_name' => $parts[1] ?? null,
            'last_name'   => $parts[2] ?? ($parts[1] ?? ''),
            'phone'       => $user->phone,
            'email'       => $user->email,
            'exam_index_number' => session('reg_index_number'),
            'application_type'  => session('reg_application_type'),
        ]);
    }

    /**
     * Show the application scaffold (sidebar + current step).
     */
    public function show(Application $application)
    {
        $this->authorizeApplication($application);

        $steps = $this->workflowSteps($application);
        $current = $this->firstIncompleteStep($application, $steps);

        return $this->renderStep($application, $steps, $current);
    }

    public function step(Application $application, string $route)
    {
        $this->authorizeApplication($application);

        $steps = $this->workflowSteps($application);
        $step  = $steps->firstWhere('route', $route);

        abort_if(! $step, 404, 'Step not found.');

        $this->ensureStepReachable($application, $steps, $step);

        return $this->renderStep($application, $steps, $step);
    }

    public function save(Request $request, Application $application, string $route)
    {
        $this->authorizeApplication($application);

        $steps = $this->workflowSteps($application);
        $step  = $steps->firstWhere('route', $route);

        abort_if(! $step, 404, 'Step not found.');

        $this->ensureStepReachable($application, $steps, $step);

        $handler = self::STEP_HANDLERS[$route] ?? null;
        abort_if(! $handler, 404, 'Step not configured.');

        return $this->{$handler}($request, $application, $step);
    }

    protected function renderStep(Application $application, $steps, $step)
    {
        $view = "applicant.application.{$step->route}";

        if (! view()->exists($view)) {
            abort(404, "View for step '{$step->route}' not found.");
        }

        $data = $this->stepData($application, $step->route);

        return view($view, array_merge($data, [
            'application' => $application,
            'steps'       => $steps,
            'currentStep' => $step,
            'progress'    => [
                'completed' => $this->completedCount($application, $steps),
                'total'     => $steps->count(),
                'percent'   => $this->progressPercent($application, $steps),
            ],
        ]));
    }

    protected function stepData(Application $application, string $route): array
    {
        $applicant = $application->applicant;

        switch ($route) {
            case 'personal-info':
                return [
                    'applicant' => $applicant->load(['citizenship']),
                    'countries' => \App\Models\Country::where('is_active', true)->orderBy('name')->get(),
                    'regions'   => Region::where('is_active', true)->orderBy('name')->get(),
                    'districts' => District::where('is_active', true)->orderBy('name')->get(),
                    'wards'     => Ward::where('is_active', true)->orderBy('name')->get(),
                    'address'   => $applicant->currentAddress(),
                ];
            case 'payment':
                $window = $application->admissionWindow;
                $payment = $application->payments()->latest()->first();

                return [
                    'window'  => $window,
                    'payment' => $payment,
                    'controlNumberHint' => Setting::getValue('payment_control_number_hint', ''),
                ];
            case 'academic-results':
                return [
                    'results' => $application->academicResults()->latest()->get(),
                ];
            case 'programme-application':
                return [
                    'programmesByLevel' => Programme::where('admission_level_id', $application->admissionWindow->admission_level_id)
                        ->where('status', 'active')
                        ->with(['campus', 'department', 'admissionLevel'])
                        ->orderBy('name')
                        ->get(),
                    'selected' => $application->selectedProgrammes()->with('programme')->orderBy('preference_order')->get(),
                ];
            case 'documents':
                return [
                    'documents' => $application->documents()->latest()->get(),
                ];
            default:
                return [
                    'summary' => $this->buildSummary($application),
                ];
        }
    }

    protected function buildSummary(Application $application): array
    {
        return [
            'applicant'    => $application->applicant,
            'window'       => $application->admissionWindow->load('admissionLevel', 'applicationRound'),
            'academicYear' => $application->academicYear,
            'payments'     => $application->payments()->latest()->get(),
            'results'      => $application->academicResults()->latest()->get(),
            'programmes'   => $application->selectedProgrammes()->with('programme')->orderBy('preference_order')->get(),
            'documents'    => $application->documents()->latest()->get(),
        ];
    }

    /* ---- Workflow helpers ---- */

    protected function workflowSteps(Application $application)
    {
        return $application->admissionWindow->admissionLevel->workflowSteps()
            ->where('is_active', true)
            ->orderBy('step_number')
            ->get();
    }

    protected function completedStepRoutes(Application $application): array
    {
        return $application->stepCompletions()
            ->with('workflowStep')
            ->whereNotNull('completed_at')
            ->get()
            ->map(fn ($c) => $c->workflowStep->route)
            ->filter()
            ->all();
    }

    protected function completedCount(Application $application, $steps): int
    {
        $completed = $application->stepCompletions()->whereNotNull('completed_at')->pluck('workflow_step_id');

        return $steps->whereIn('id', $completed)->count();
    }

    protected function progressPercent(Application $application, $steps): int
    {
        $total = max(1, $steps->count());
        $done  = $this->completedCount($application, $steps);

        return (int) round(($done / $total) * 100);
    }

    protected function firstIncompleteStep(Application $application, $steps)
    {
        $completed = $application->stepCompletions()->whereNotNull('completed_at')->pluck('workflow_step_id');

        return $steps->first(fn ($step) => ! $completed->contains($step->id)) ?? $steps->last();
    }

    protected function isStepCompleted(Application $application, $step): bool
    {
        return $application->stepCompletions()
            ->where('workflow_step_id', $step->id)
            ->whereNotNull('completed_at')
            ->exists();
    }

    protected function ensureStepReachable(Application $application, $steps, $step): void
    {
        $completed = $application->stepCompletions()->whereNotNull('completed_at')->pluck('workflow_step_id');

        foreach ($steps->where('step_number', '<', $step->step_number) as $previous) {
            if (! $completed->contains($previous->id)) {
                abort(403, 'Complete previous steps before accessing this one.');
            }
        }
    }

    protected function markCompleted(Application $application, $step): void
    {
        ApplicationStepCompletion::updateOrCreate(
            ['application_id' => $application->id, 'workflow_step_id' => $step->id],
            ['completed_at' => now()],
        );

        $steps = $this->workflowSteps($application);
        $application->update([
            'current_step' => $step->route,
            'progress_percentage' => $this->progressPercent($application, $steps),
        ]);
    }

    protected function authorizeApplication(Application $application): void
    {
        $user = auth()->user();

        if (! $user->isAdmin() && $application->applicant->user_id !== $user->id) {
            abort(403, 'You do not own this application.');
        }
    }

    /* ---- Step: Personal Information ---- */

    public function personalInfo(Request $request, Application $application, $step)
    {
        $validated = $request->validate([
            'first_name'        => ['required', 'string', 'max:255'],
            'middle_name'       => ['nullable', 'string', 'max:255'],
            'last_name'         => ['required', 'string', 'max:255'],
            'date_of_birth'     => ['required', 'date', 'before:today'],
            'gender'            => ['required', 'in:Male,Female'],
            'citizenship_id'    => ['required', 'exists:countries,id'],
            'phone'             => ['required', 'string', 'max:30'],
            'nida_number'       => ['nullable', 'string', 'max:40'],
            'marital_status'    => ['nullable', 'string', 'max:30'],
            'disability_status' => ['sometimes', 'boolean'],
            'disability_type'   => ['nullable', 'string', 'max:191'],
            'exam_index_number' => ['nullable', 'string', 'max:40'],
            'region_id'         => ['nullable', 'exists:regions,id'],
            'district_id'       => ['nullable', 'exists:districts,id'],
            'ward_id'           => ['nullable', 'exists:wards,id'],
            'street'            => ['nullable', 'string', 'max:191'],
            'postal_address'    => ['nullable', 'string', 'max:191'],
        ]);

        $applicant = $application->applicant;

        DB::transaction(function () use ($applicant, $validated, $request) {
            $applicant->update([
                'first_name'         => $validated['first_name'],
                'middle_name'        => $validated['middle_name'],
                'last_name'          => $validated['last_name'],
                'date_of_birth'      => $validated['date_of_birth'],
                'gender'             => $validated['gender'],
                'citizenship_id'     => $validated['citizenship_id'],
                'phone'              => $validated['phone'],
                'nida_number'        => $validated['nida_number'] ?? null,
                'marital_status'     => $validated['marital_status'] ?? null,
                'disability_status'  => $request->boolean('disability_status'),
                'disability_type'    => $request->boolean('disability_status') ? ($validated['disability_type'] ?? null) : null,
                'exam_index_number'  => $validated['exam_index_number'] ?? null,
            ]);

            $applicant->addresses()->updateOrCreate(
                ['address_type' => 'Current'],
                [
                    'region_id'      => $validated['region_id'] ?? null,
                    'district_id'    => $validated['district_id'] ?? null,
                    'ward_id'        => $validated['ward_id'] ?? null,
                    'street'         => $validated['street'] ?? null,
                    'postal_address' => $validated['postal_address'] ?? null,
                ]
            );
        });

        $this->markCompleted($application, $step);

        return $this->redirectToNextStep($application);
    }

    /* ---- Step: Payment ---- */

    public function payment(Request $request, Application $application, $step)
    {
        $window = $application->admissionWindow;
        $fee    = (float) $window->application_fee;

        $validated = $request->validate([
            'payment_method' => [$fee > 0 ? 'required' : 'nullable', 'string', 'max:40'],
            'phone_number'   => ['required_if:payment_method,Mobile Money', 'nullable', 'string', 'max:30'],
        ]);

        DB::transaction(function () use ($application, $window, $validated, $fee) {
            $payment = Payment::create([
                'application_id'    => $application->id,
                'payment_reference' => Payment::generateReference(),
                'amount'            => $fee,
                'currency'          => $window->currency,
                'status'            => $fee <= 0 ? Payment::STATUS_FREE : Payment::STATUS_PENDING,
                'payment_method'    => $validated['payment_method'] ?? ($fee <= 0 ? 'Free' : null),
                'phone_number'      => $validated['phone_number'] ?? null,
            ]);

            if ($fee <= 0) {
                $payment->update(['paid_at' => now(), 'verified_at' => now()]);
            } else {
                $payment->update([
                    'control_number' => $this->generateControlNumber($application, $payment),
                ]);
            }
        });

        $this->markCompleted($application, $step);

        if ($fee <= 0) {
            $application->update(['status' => Application::STATUS_APPLICATION_IN_PROGRESS]);
        }

        return $this->redirectToNextStep($application);
    }

    protected function generateControlNumber(Application $application, Payment $payment): string
    {
        $format = $application->admissionWindow->payment_reference_format ?? Setting::getValue('payment_reference_format', 'UNI');

        return strtoupper($format.'-'.$application->application_number ?? $application->id.'-'.$payment->id);
    }

    /* ---- Step: Academic Results ---- */

    public function academicResults(Request $request, Application $application, $step)
    {
        $validated = $request->validate([
            'results' => ['required', 'array', 'min:1'],
            'results.*.exam_type'   => ['required', 'string', 'max:40'],
            'results.*.exam_body'   => ['required', 'string', 'max:80'],
            'results.*.index_number'=> ['required', 'string', 'max:40'],
            'results.*.exam_year'   => ['nullable', 'integer', 'min:1980', 'max:'.(date('Y') + 1)],
            'results.*.school_name' => ['nullable', 'string', 'max:191'],
            'results.*.subjects'    => ['required', 'array', 'min:1'],
            'results.*.subjects.*.subject' => ['required', 'string', 'max:191'],
            'results.*.subjects.*.grade'   => ['required', 'string', 'max:10'],
        ]);

        $verify = app(\App\Services\ResultVerificationService::class);

        DB::transaction(function () use ($application, $validated, $verify) {
            foreach ($validated['results'] as $key => $item) {
                $provider = strtoupper($item['exam_body'] ?? '');

                if (! in_array($provider, ['NECTA', 'NACTVET'], true)) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'results.'.$key.'.exam_body' => 'Every result must be fetched and verified from NECTA or NACTVET.',
                    ]);
                }

                $examType   = $item['exam_type'];
                $identifier = $item['index_number'];
                $year       = $item['exam_year'] ?? null;

                $payload = in_array($examType, ['O-Level', 'A-Level'], true)
                    ? ['index_number' => $identifier, 'exam_year' => $year]
                    : ($examType === 'Certificate'
                        ? ['registration_number' => $identifier, 'exam_year' => $year]
                        : ['avn_number' => $identifier]);

                // Auto-verify the entered details against the official source before saving.
                $fetched = $verify->verify($examType, $payload);

                if (empty($fetched['ok']) || empty($fetched['subjects'])) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'results.'.$key.'.index_number' => 'We could not verify this result with '.$provider.'. Please re-fetch from the official source and try again.',
                    ]);
                }

                if ($this->normalizeSubjects($item['subjects']) !== $this->normalizeSubjects($fetched['subjects'])) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'results.'.$key.'.subjects' => 'The subjects/grades entered do not match the official '.$provider.' record. Please re-fetch from the official source.',
                    ]);
                }

                $passes = collect($item['subjects'])
                    ->filter(fn ($s) => in_array(strtoupper($s['grade']), ['A', 'B', 'C', 'D', 'E'], true))
                    ->count();

                AcademicResult::create([
                    'application_id'  => $application->id,
                    'exam_type'       => $examType,
                    'exam_body'       => $provider,
                    'index_number'    => $identifier,
                    'exam_year'       => $year,
                    'school_name'     => $fetched['school_name'] ?? ($item['school_name'] ?? null),
                    'results'         => $fetched['subjects'],
                    'total_subjects'  => count($fetched['subjects']),
                    'passes_count'    => $passes,
                    'overall_grade'   => $this->overallGrade($fetched['subjects']),
                    'is_verified'     => true,
                    'verified_at'     => now(),
                ]);
            }
        });

        $this->markCompleted($application, $step);

        return $this->redirectToNextStep($application);
    }

    protected function normalizeSubjects(array $subjects): array
    {
        $canonical = array_map(
            fn ($s) => strtoupper(trim($s['subject'] ?? '')).'|'.strtoupper(trim($s['grade'] ?? '')),
            $subjects
        );
        sort($canonical);

        return $canonical;
    }

    public function fetchResult(Request $request, Application $application)
    {
        $this->authorizeApplication($application);

        $validated = $request->validate([
            'exam_type'    => ['required', 'in:O-Level,A-Level,Certificate,Diploma'],
            'index_number' => ['nullable', 'string', 'max:40'],
            'registration_number' => ['nullable', 'string', 'max:40'],
            'avn_number'   => ['nullable', 'string', 'max:40'],
            'exam_year'    => ['nullable', 'integer', 'min:1980', 'max:'.(date('Y') + 1)],
        ]);

        $type = $validated['exam_type'];

        if (in_array($type, ['O-Level', 'A-Level'], true)) {
            if (empty($validated['index_number'])) {
                return response()->json(['ok' => false, 'error' => 'Enter your NECTA index number to fetch your results.'], 422);
            }
            $payload = ['index_number' => $validated['index_number'], 'exam_year' => $validated['exam_year'] ?? null];
        } elseif ($type === 'Certificate') {
            if (empty($validated['registration_number']) || empty($validated['exam_year'])) {
                return response()->json(['ok' => false, 'error' => 'Enter your NACTVET registration number and year of graduation.'], 422);
            }
            $payload = ['registration_number' => $validated['registration_number'], 'exam_year' => $validated['exam_year']];
        } else {
            if (empty($validated['avn_number'])) {
                return response()->json(['ok' => false, 'error' => 'Enter your AVN number to fetch your diploma results.'], 422);
            }
            $payload = ['avn_number' => $validated['avn_number']];
        }

        $result = app(\App\Services\ResultVerificationService::class)->verify($type, $payload);

        if (! $result['ok']) {
            return response()->json(['ok' => false, 'error' => $result['error']], 422);
        }

        return response()->json($result);
    }

    protected function overallGrade(array $subjects): string
    {
        $grades = array_map(fn ($s) => strtoupper($s['grade']), $subjects);

        foreach (['A', 'B', 'C', 'D', 'E'] as $letter) {
            if (in_array($letter, $grades, true)) {
                return $letter;
            }
        }

        return 'F';
    }

    /* ---- Step: Programme Application ---- */

    public function programmeApplication(Request $request, Application $application, $step)
    {
        $validated = $request->validate([
            'programmes'   => ['required', 'array', 'min:1', 'max:3'],
            'programmes.*' => ['required', 'integer', 'exists:programmes,id'],
        ]);

        $programmeIds = array_values(array_unique($validated['programmes']));
        $levelId      = $application->admissionWindow->admission_level_id;

        $valid = Programme::whereIn('id', $programmeIds)
            ->where('admission_level_id', $levelId)
            ->where('status', 'active')
            ->pluck('id')
            ->all();

        if (count($valid) !== count($programmeIds)) {
            return back()->withErrors(['programmes' => 'One or more programmes are invalid for this admission level.']);
        }

        DB::transaction(function () use ($application, $programmeIds) {
            $application->selectedProgrammes()->delete();

            foreach ($programmeIds as $index => $programmeId) {
                ApplicationProgramme::create([
                    'application_id'    => $application->id,
                    'programme_id'      => $programmeId,
                    'preference_order'  => $index + 1,
                ]);
            }
        });

        $this->markCompleted($application, $step);

        return $this->redirectToNextStep($application);
    }

    /* ---- Step: Documents ---- */

    public function documents(Request $request, Application $application, $step)
    {
        // If skip flag is set, skip document validation entirely
        if ($request->input('skip_docs')) {
            $this->markCompleted($application, $step);
            return $this->redirectToNextStep($application)->with('success','Documents step skipped — you can upload later.');
        }

        // Documents are optional — allow skipping this step
        $hasAnyFile = false;
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $doc) {
                if (!empty($doc['file'])) { $hasAnyFile = true; break; }
            }
        }
        if (! $hasAnyFile) {
            $this->markCompleted($application, $step);
            return $this->redirectToNextStep($application)->with('success','Documents step skipped — you can upload later.');
        }

        $validated = $request->validate([
            'documents' => ['nullable', 'array'],
            'documents.*.document_type' => ['nullable', 'string', 'max:80'],
            'documents.*.file'          => ['nullable', 'file', 'max:5120', 'mimetypes:application/pdf,image/jpeg,image/png,image/jpg,application/octet-stream', 'mimes:pdf,jpg,jpeg,png'],
        ]);

        DB::transaction(function () use ($application, $step, $validated) {
            foreach ($validated['documents'] ?? [] as $item) {
                if (empty($item['file'])) continue;
                $file = $item['file'];

                $path = $file->store('applications/'.$application->id, 'public');

                ApplicationDocument::create([
                    'application_id'  => $application->id,
                    'workflow_step_id'=> $step->id,
                    'document_type'   => $item['document_type'] ?? 'Other',
                    'file_path'       => 'storage/'.$path,
                    'file_name'       => $file->getClientOriginalName(),
                    'file_size'       => $file->getSize(),
                    'mime_type'       => $file->getMimeType(),
                ]);
            }
        });

        $this->markCompleted($application, $step);

        return $this->redirectToNextStep($application);
    }

    /* ---- Step: Submit ---- */

    public function submit(Request $request, Application $application, $step)
    {
        $application->update([
            'application_number' => Application::generateApplicationNumber($application),
            'status'             => Application::STATUS_SUBMITTED,
            'submitted_at'       => now(),
            'progress_percentage'=> 100,
        ]);

        $this->markCompleted($application, $step);

        \App\Models\NotificationLog::create([
            'user_id'       => $application->applicant->user_id,
            'application_id'=> $application->id,
            'channel'       => 'system',
            'subject'       => 'Application Submitted',
            'body'          => 'Your application '.$application->application_number.' has been received successfully.',
            'status'        => 'SENT',
            'sent_at'       => now(),
        ]);

        app(\App\Services\SmsService::class)->notifySubmitted($application);

        return redirect()->route('applicant.application.status', encId($application->id))
            ->with('success', 'Your application has been submitted successfully.');
    }

    /* ---- Redirects ---- */

    protected function redirectToNextStep(Application $application)
    {
        $steps   = $this->workflowSteps($application);
        $current = $this->firstIncompleteStep($application, $steps);

        return redirect()->route('applicant.application.step', [encId($application->id), $current->route])
            ->with('success', 'Step saved.');
    }
}