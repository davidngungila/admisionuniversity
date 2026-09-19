<?php

namespace App\Http\Controllers;

use App\Models\AdmissionWindow;
use App\Models\AcademicYear;
use App\Models\AdmissionLevel;
use App\Models\Campus;
use App\Models\Programme;
use App\Models\Setting;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    public function home()
    {
        $academicYear = AcademicYear::current();
        $openWindows  = AdmissionWindow::with(['admissionLevel', 'applicationRound', 'academicYear'])
            ->where('status', 'active')
            ->whereHas('academicYear', fn ($q) => $q->where('is_active', true))
            ->orderBy('admission_level_id')
            ->get();

        $programmes = Programme::with(['admissionLevel', 'campus'])
            ->where('status', 'active')
            ->inRandomOrder()
            ->limit(6)
            ->get();

        return view('public.home', compact('academicYear', 'openWindows', 'programmes'));
    }

    public function calendar()
    {
        $windows = AdmissionWindow::with(['admissionLevel', 'applicationRound', 'academicYear'])
            ->orderBy('opens_at')
            ->get();

        $levels = AdmissionLevel::where('is_active', true)->orderBy('sort_order')->get();

        // Windows/years where the logged-in applicant already has an application.
// Once applied in an academic year, no other window that year is allowed.
        $myApplicationWindowIds = [];
        $myApplicationYearIds   = [];
        $applicant = auth()->user()?->applicant;
        if ($applicant) {
            $applications = $applicant->applications()->get(['admission_window_id', 'academic_year_id']);

            $myApplicationWindowIds = $applications->pluck('admission_window_id')->all();
            $myApplicationYearIds   = $applications->pluck('academic_year_id')->all();
        }

        return view('public.calendar', compact('windows', 'levels', 'myApplicationWindowIds', 'myApplicationYearIds'));
    }

    public function programmes(Request $request)
    {
        $q          = Programme::query()->with(['admissionLevel', 'campus', 'department']);
        $levels     = AdmissionLevel::where('is_active', true)->orderBy('sort_order')->get();
        $campuses   = Campus::where('is_active', true)->orderBy('name')->get();

        if ($request->filled('search')) {
            $q->where(function ($query) use ($request) {
                $query->where('name', 'like', '%'.$request->search.'%')
                    ->orWhere('code', 'like', '%'.$request->search.'%');
            });
        }
        if ($request->filled('level')) {
            $q->where('admission_level_id', $request->level);
        }
        if ($request->filled('campus')) {
            $q->where('campus_id', $request->campus);
        }
        if ($request->filled('study_mode')) {
            $q->where('study_mode', $request->study_mode);
        }
        if ($request->filled('duration')) {
            $q->where('duration_years', $request->duration);
        }

        $programmes = $q->where('status', 'active')->orderBy('name')->paginate(12)->withQueryString();

        return view('public.programmes.index', compact('programmes', 'levels', 'campuses'));
    }

    public function programmeShow(Programme $programme)
    {
        $programme->load([
            'admissionLevel',
            'campus',
            'department.faculty',
            'requirements',
            'courses',
        ]);

        $curriculum = $programme->curriculum;

        return view('public.programmes.show', compact('programme', 'curriculum'));
    }

    public function requirements()
    {
        return view('public.requirements');
    }

    public function fees()
    {
        $windows = AdmissionWindow::with(['admissionLevel', 'applicationRound', 'academicYear'])
            ->orderBy('admission_level_id')
            ->get();

        return view('public.fees', compact('windows'));
    }

    public function guidelines()
    {
        return view('public.guidelines');
    }

    public function contact()
    {
        return view('public.contact');
    }

    public function verify(Request $request)
    {
        $result = null;

        if ($request->filled('application_number')) {
            $result = \App\Models\Application::with([
                'applicant.citizenship',
                'applicant.addresses',
                'admissionWindow.admissionLevel',
                'admissionWindow.applicationRound',
                'admissionWindow.academicYear',
                'selectedProgrammes.programme',
                'selectionResults.programme',
                'selectionResults.selectionBatch',
                'admissionLetters',
                'payments',
                'statusHistory',
            ])
                ->where('application_number', $request->application_number)
                ->first();
        }

        return view('public.verify', compact('result'));
    }
}