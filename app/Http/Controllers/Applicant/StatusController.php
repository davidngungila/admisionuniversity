<?php

namespace App\Http\Controllers\Applicant;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\District;
use App\Models\Region;
use App\Models\Ward;
use Barryvdh\DomPDF\Facade\Pdf;

class StatusController extends Controller
{
    public function show(Application $application)
    {
        $this->authorize($application);

        $steps = $application->admissionWindow->admissionLevel->workflowSteps()
            ->where('is_active', true)
            ->orderBy('step_number')
            ->get();

        $completed = $application->stepCompletions()
            ->whereNotNull('completed_at')
            ->pluck('workflow_step_id');

        return view('applicant.application.status', [
            'application' => $application->load([
                'applicant',
                'academicYear',
                'admissionWindow.admissionLevel',
                'admissionWindow.applicationRound',
                'selectedProgrammes.programme',
                'payments',
                'academicResults',
                'documents',
                'statusHistory.changedByUser',
            ]),
            'steps'     => $steps,
            'completed' => $completed,
        ]);
    }

    public function summary(Application $application)
    {
        $this->authorize($application);

        return view('applicant.application.summary', [
            'application' => $application->load([
                'applicant.citizenship',
                'academicYear',
                'admissionWindow.admissionLevel',
                'admissionWindow.applicationRound',
                'selectedProgrammes.programme.campus',
                'payments',
                'academicResults',
                'documents',
            ]),
        ]);
    }

    public function form(Application $application)
    {
        $this->authorize($application);

        $application->load([
            'applicant.citizenship',
            'applicant.currentAddress.region',
            'applicant.currentAddress.district',
            'applicant.currentAddress.ward',
            'academicYear',
            'admissionWindow.admissionLevel',
            'admissionWindow.applicationRound',
            'selectedProgrammes.programme.campus',
            'selectedProgrammes.programme.department.faculty',
            'payments',
            'academicResults',
            'documents',
        ]);

        if (request()->has('download')) {
            $pdf = Pdf::loadView('applicant.application.form-pdf', compact('application'));
            $pdf->setPaper('a4', 'portrait');
            $filename = 'Application-Form-'.($application->application_number ?? 'DRAFT-'.$application->id).'.pdf';
            return $pdf->download($filename);
        }

        return view('applicant.application.form', compact('application'));
    }

    public function programmesApplied(Application $application)
    {
        $this->authorize($application);

        return view('applicant.application.programmes', [
            'application' => $application,
        ]);
    }

    protected function authorize(Application $application): void
    {
        $user = auth()->user();

        if (! $user->isAdmin() && $application->applicant->user_id !== $user->id) {
            abort(403);
        }
    }
}