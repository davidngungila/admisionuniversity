<?php

namespace App\Http\Controllers\Applicant;

use App\Http\Controllers\Controller;
use App\Models\AdmissionLetter;
use App\Models\Application;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class ResultController extends Controller
{
    public function index()
    {
        $applications = auth()->user()->applicant
            ? auth()->user()->applicant->applications()
                ->with(['admissionWindow.admissionLevel', 'admissionWindow.applicationRound', 'academicYear', 'selectedProgrammes.programme'])
                ->latest()->get()
            : collect();

        return view('applicant.result.index', ['applications' => $applications]);
    }

    public function show(Application $application)
    {
        $this->authorize($application);

        return view('applicant.result.show', [
            'application' => $application->load([
                'admissionWindow.admissionLevel',
                'academicYear',
                'selectionResults.selectionBatch',
                'selectionResults.programme',
                'admissionLetters',
            ]),
        ]);
    }

    public function letter(Application $application)
    {
        $this->authorize($application);

        $letter = $application->admissionLetters()->latest()->first();

        // If selected/admitted but no letter yet, generate a preview letter on the fly
        if (! $letter && in_array($application->status, ['SELECTED','ADMITTED'])) {
            $programme = $application->selectedProgrammes->first()?->programme ?? $application->selectionResults->first()?->programme;
            $letter = new AdmissionLetter([
                'letter_number' => 'UDOM/AL/'.($application->application_number ?? 'APP-'.$application->id).'/'.date('Y'),
                'content'       => 'Congratulations! You have been selected to join the University of Dodoma for '.($programme?->name ?? $application->admissionWindow->admissionLevel->name).' ('.($programme?->code ?? '—').') for academic year '.$application->academicYear->name.'. Please report as per joining instructions.',
                'issued_at'     => now(),
                'status'        => 'ISSUED',
            ]);
            $letter->id = 0;
            $letter->exists = false;
            $application->load(['applicant','selectedProgrammes.programme','admissionWindow.admissionLevel','academicYear']);
        }

        abort_if(! $letter, 404, 'Admission letter not yet issued.');

        // PDF download if requested
        if (request()->has('download') || request()->is('*/download')) {
            if ($letter->file_path && Storage::disk('public')->exists(str_replace('storage/', '', $letter->file_path))) {
                $letter->update(['downloaded_at' => now()]);
                return Storage::disk('public')->download(str_replace('storage/', '', $letter->file_path), $letter->letter_number.'.pdf');
            }
            $pdf = Pdf::loadView('applicant.result.letter-pdf', ['letter'=>$letter,'application'=>$application]);
            $pdf->setPaper('a4','portrait');
            return $pdf->download($letter->letter_number.'.pdf');
        }

        if ($letter->file_path && Storage::disk('public')->exists(str_replace('storage/', '', $letter->file_path)) && !request()->has('preview')) {
            // If file exists and not preview, still show HTML preview with download option
        }

        return view('applicant.result.letter', ['letter' => $letter, 'application' => $application]);
    }

    public function downloadLetter(AdmissionLetter $letter)
    {
        $application = $letter->application;
        $this->authorize($application);

        if ($letter->file_path && Storage::disk('public')->exists(str_replace('storage/', '', $letter->file_path))) {
            $letter->update(['downloaded_at' => now()]);
            return Storage::disk('public')->download(str_replace('storage/', '', $letter->file_path), $letter->letter_number.'.pdf');
        }

        // Generate PDF from HTML view if no file
        $pdf = Pdf::loadView('applicant.result.letter-pdf', ['letter'=>$letter,'application'=>$application]);
        $pdf->setPaper('a4','portrait');
        $letter->update(['downloaded_at' => now()]);
        return $pdf->download($letter->letter_number.'.pdf');
    }

    public function joining(Application $application)
    {
        $this->authorize($application);
        $application->load(['applicant','selectedProgrammes.programme','admissionWindow.admissionLevel','academicYear','admissionLetters']);
        $programme = $application->selectedProgrammes->first()?->programme ?? $application->selectionResults->first()?->programme;
        $letter = $application->admissionLetters()->latest()->first();
        if (! $letter && in_array($application->status, ['SELECTED','ADMITTED'])) {
            $letter = new AdmissionLetter([
                'letter_number' => 'UDOM/AL/'.($application->application_number ?? 'APP-'.$application->id).'/'.date('Y'),
                'issued_at' => now(),
            ]);
        }

        if (request()->has('download')) {
            $pdf = Pdf::loadView('applicant.result.joining-pdf', ['application'=>$application,'programme'=>$programme,'letter'=>$letter]);
            $pdf->setPaper('a4','portrait');
            $filename = 'UDOM-Joining-'.($application->application_number ?? $application->id).'.pdf';
            return $pdf->download($filename);
        }

        return view('applicant.result.joining', compact('application','programme','letter'));
    }

    protected function authorize(Application $application): void
    {
        if ($application->applicant->user_id !== auth()->id() && ! auth()->user()->isAdmin()) {
            abort(403);
        }
    }
}