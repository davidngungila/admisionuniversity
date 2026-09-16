<?php

namespace App\Http\Controllers\Applicant;

use App\Http\Controllers\Controller;
use App\Models\AdmissionWindow;

class CalendarController extends Controller
{
    public function index()
    {
        $windows = AdmissionWindow::with(['admissionLevel','applicationRound','academicYear'])
            ->whereHas('academicYear', fn($q)=>$q->where('is_active',true))
            ->orderBy('opens_at')
            ->get();

        $applicant = auth()->user()->applicant;
        $myAppWindowIds = $applicant ? $applicant->applications()->pluck('admission_window_id')->toArray() : [];
        $myAppYearIds   = $applicant ? $applicant->applications()->pluck('academic_year_id')->toArray() : [];

        return view('applicant.calendar', compact('windows','myAppWindowIds','myAppYearIds'));
    }
}
