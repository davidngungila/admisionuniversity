<?php

namespace App\Http\Controllers\Applicant;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\AdmissionWindow;
use App\Models\Application;

class DashboardController extends Controller
{
    public function index()
    {
        $user     = auth()->user();
        $applicant = $user->applicant;

        $academicYear  = AcademicYear::current();
        $applications  = collect();

        if ($applicant) {
            $applications = $applicant->applications()
                ->with(['academicYear', 'admissionWindow.admissionLevel', 'admissionWindow.applicationRound', 'selectedProgrammes.programme', 'admissionLetters', 'selectionResults.programme', 'selectionResults.selectionBatch'])
                ->latest()
                ->get();
        }

        $openApplications = $applications->filter(
            fn (Application $a) => in_array($a->status, [
                Application::STATUS_DRAFT,
                Application::STATUS_PAYMENT_PENDING,
                Application::STATUS_PAYMENT_CONFIRMED,
                Application::STATUS_APPLICATION_IN_PROGRESS,
            ], true)
        );

        $openWindows = AdmissionWindow::with(['admissionLevel', 'applicationRound', 'academicYear'])
            ->where('status', 'active')
            ->whereHas('academicYear', fn ($q) => $q->where('is_active', true))
            ->get();

        $intendedLevelId   = session('intended_level_id');
        $intendedLevelName = session('intended_level_name');

        // If already selected/admitted, don't show Start New Application at all
        $hasSelected = $applications->whereIn('status', ['SELECTED','ADMITTED'])->isNotEmpty();

        // Do not allow the same user to start a new application while they already have one.
        $hasActiveApplication = $applications->whereNotIn('status', [
            Application::STATUS_REJECTED,
            Application::STATUS_INELIGIBLE,
            Application::STATUS_WITHDRAWN,
        ])->isNotEmpty();

        if ($hasSelected || $hasActiveApplication) {
            $openWindows = collect();
        } else {
            // If applicant selected a level at registration, show ONLY matching windows
            // Also fallback: if applicant already has an application, filter to that level
            $filterLevelId = $intendedLevelId;
            if (! $filterLevelId && $applications->isNotEmpty()) {
                $filterLevelId = $applications->first()->admissionWindow->admission_level_id ?? null;
                $intendedLevelName = $filterLevelId ? \App\Models\AdmissionLevel::find($filterLevelId)?->name : $intendedLevelName;
            }
            if ($filterLevelId) {
                $openWindows = $openWindows->where('admission_level_id', $filterLevelId)->values();
            }
        }

        return view('applicant.dashboard', [
            'applicant'         => $applicant,
            'applications'      => $applications,
            'openApplications'  => $openApplications,
            'openWindows'       => $openWindows,
            'academicYear'      => $academicYear,
            'intendedLevelId'   => $intendedLevelId,
            'intendedLevelName' => $intendedLevelName,
            'hasSelected'       => $hasSelected,
        ]);
    }
}