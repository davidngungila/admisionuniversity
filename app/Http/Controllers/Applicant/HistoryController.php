<?php

namespace App\Http\Controllers\Applicant;

use App\Http\Controllers\Controller;

class HistoryController extends Controller
{
    public function index()
    {
        $applicant = auth()->user()->applicant;

        $applications = $applicant
            ? $applicant->applications()
                ->with(['academicYear', 'admissionWindow.admissionLevel', 'admissionWindow.applicationRound', 'selectedProgrammes.programme'])
                ->orderByDesc('created_at')
                ->get()
                ->groupBy(fn ($a) => $a->academicYear->name.'|Round '.$a->admissionWindow->applicationRound->round_number)
            : collect();

        return view('applicant.history.index', ['grouped' => $applications]);
    }
}