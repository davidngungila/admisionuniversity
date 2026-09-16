<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\AdmissionWindow;
use Illuminate\Http\Request;

class ApplicationManageController extends Controller
{
    public function index(Request $request)
    {
        $q = Application::with(['applicant','academicYear','admissionWindow.admissionLevel'])->latest();

        if ($request->filled('status'))  $q->where('status', $request->status);
        if ($request->filled('window'))  $q->where('admission_window_id', $request->window);
        if ($request->filled('search'))  $q->where('application_number','like','%'.$request->search.'%');

        return view('admin.applications.index', [
            'applications'=>$q->paginate(20)->withQueryString(),
            'windows'=>AdmissionWindow::with('admissionLevel')->latest()->get(),
            'statuses'=>['DRAFT','PAYMENT_PENDING','PAYMENT_CONFIRMED','APPLICATION_IN_PROGRESS','SUBMITTED','UNDER_REVIEW','ELIGIBLE','SELECTED','ADMITTED','REJECTED','WAITLISTED'],
        ]);
    }

    public function show(Application $application)
    {
        $application->load(['applicant.citizenship','applicant.addresses','academicYear','admissionWindow.admissionLevel','admissionWindow.applicationRound','selectedProgrammes.programme','payments','academicResults','documents','statusHistory.changedByUser']);

        return view('admin.applications.show', compact('application'));
    }

    public function updateStatus(Request $request, Application $application)
    {
        $d = $request->validate([
            'status'  => ['required','string'],
            'remarks' => ['nullable','string','max:500'],
        ]);

        $application->advanceStatus($d['status'], auth()->id(), $d['remarks'] ?? null);

        // Auto-create admission letter when selected/admitted and none exists
        if (in_array($d['status'], ['SELECTED','ADMITTED']) && $application->admissionLetters()->count() === 0) {
            $programme = $application->selectedProgrammes()->first()?->programme;
            \App\Models\AdmissionLetter::create([
                'application_id' => $application->id,
                'letter_number'  => 'UDOM/AL/'.($application->application_number ?? 'APP-'.$application->id).'/'.date('Y'),
                'content'        => 'Congratulations! You have been selected to join the University of Dodoma for '.($programme?->name ?? $application->admissionWindow->admissionLevel->name).' ('.($programme?->code ?? '—').') for academic year '.$application->academicYear->name.'. Please report as per joining instructions.',
                'issued_at'      => now(),
                'status'         => 'ISSUED',
            ]);
        }

        if (in_array($d['status'], ['SELECTED','ADMITTED'])) {
            app(\App\Services\SmsService::class)->notifySelection($application, $d['status']);
        }

        return back()->with('success','Status updated to '.$d['status']);
    }

    public function destroy(Request $request, Application $application)
    {
        $d = $request->validate([
            'reason' => ['required', 'string', 'max:500'],
        ]);

        $application->loadMissing('applicant');

        \App\Models\ApplicationDeletion::create([
            'application_id'     => $application->id,
            'application_number' => $application->application_number,
            'applicant_id'       => $application->applicant_id,
            'applicant_name'     => $application->applicant?->fullName(),
            'applicant_email'    => $application->applicant?->email,
            'deleted_by'         => auth()->id(),
            'reason'             => trim($d['reason']),
        ]);

        $application->loadMissing('documents');

        foreach ($application->documents as $doc) {
            $path = str_replace('storage/', '', (string) $doc->file_path);
            if ($path && \Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($path);
            }
        }

        $application->delete();

        return redirect()->route('admin.applications.index')->with('success', 'Application deleted successfully.');
    }
}