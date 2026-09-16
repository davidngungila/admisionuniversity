<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\AdmissionLevel;
use App\Models\AdmissionWindow;
use App\Models\ApplicationRound;
use Illuminate\Http\Request;

class AdmissionWindowController extends Controller
{
    public function index(Request $request)
    {
        $q = AdmissionWindow::with(['academicYear', 'admissionLevel', 'applicationRound'])->latest();

        if ($request->filled('year')) {
            $q->where('academic_year_id', $request->year);
        }
        if ($request->filled('level')) {
            $q->where('admission_level_id', $request->level);
        }

        return view('admin.windows.index', [
            'windows' => $q->paginate(15)->withQueryString(),
            'years'   => AcademicYear::orderByDesc('name')->get(),
            'levels'  => AdmissionLevel::orderBy('sort_order')->get(),
        ]);
    }

    public function create()
    {
        return view('admin.windows.create', $this->formData());
    }

    public function store(Request $request)
    {
        $d = $this->validateWindow($request);

        AdmissionWindow::create([
            'academic_year_id'    => $d['academic_year_id'],
            'admission_level_id'  => $d['admission_level_id'],
            'application_round_id'=> $d['application_round_id'],
            'applicant_category'  => $d['applicant_category'],
            'opens_at'            => $d['opening_date'].' '.$d['opening_time'],
            'closes_at'           => $d['closing_date'].' '.$d['closing_time'],
            'timezone'            => $d['timezone'] ?? 'Africa/Dar_es_Salaam',
            'application_fee'     => $d['application_fee'],
            'currency'            => $d['currency'] ?? 'TZS',
            'status'              => $d['status'] ?? 'active',
        ]);

        return redirect()->route('admin.windows.index')->with('success', 'Admission window created.');
    }

    public function edit(AdmissionWindow $window)
    {
        return view('admin.windows.edit', array_merge($this->formData(), ['window' => $window]));
    }

    public function update(Request $request, AdmissionWindow $window)
    {
        $d = $this->validateWindow($request);

        $window->update([
            'academic_year_id'    => $d['academic_year_id'],
            'admission_level_id'  => $d['admission_level_id'],
            'application_round_id'=> $d['application_round_id'],
            'applicant_category'  => $d['applicant_category'],
            'opens_at'            => $d['opening_date'].' '.$d['opening_time'],
            'closes_at'           => $d['closing_date'].' '.$d['closing_time'],
            'timezone'            => $d['timezone'] ?? 'Africa/Dar_es_Salaam',
            'application_fee'     => $d['application_fee'],
            'currency'            => $d['currency'] ?? 'TZS',
            'status'              => $d['status'] ?? 'active',
        ]);

        return redirect()->route('admin.windows.index')->with('success', 'Window updated.');
    }

    public function destroy(AdmissionWindow $window)
    {
        if ($window->applications()->exists()) {
            return back()->with('error', 'Cannot delete — applications exist for this window.');
        }

        $window->delete();

        return back()->with('success', 'Deleted.');
    }

    public function toggle(AdmissionWindow $window)
    {
        $window->status = $window->status === AdmissionWindow::STATUS_ACTIVE
            ? AdmissionWindow::STATUS_INACTIVE
            : AdmissionWindow::STATUS_ACTIVE;
        $window->save();

        return back()->with('success', $window->status === AdmissionWindow::STATUS_ACTIVE
            ? 'Window turned ON — applications are now open.'
            : 'Window turned OFF — applications are now closed.');
    }

    protected function validateWindow(Request $request): array
    {
        return $request->validate([
            'academic_year_id'     => ['required', 'exists:academic_years,id'],
            'admission_level_id'   => ['required', 'exists:admission_levels,id'],
            'application_round_id' => ['required', 'exists:application_rounds,id'],
            'applicant_category'   => ['required', 'in:Tanzanian,Foreign'],
            'opening_date'         => ['required', 'date'],
            'opening_time'         => ['required', 'regex:/^\d{2}:\d{2}(:\d{2})?$/'],
            'closing_date'         => ['required', 'date', 'after_or_equal:opening_date'],
            'closing_time'         => ['required', 'regex:/^\d{2}:\d{2}(:\d{2})?$/'],
            'timezone'             => ['nullable', 'string'],
            'application_fee'      => ['required', 'numeric', 'min:0'],
            'currency'             => ['nullable', 'string', 'max:10'],
            'status'               => ['nullable', 'in:active,inactive'],
        ]);
    }

    protected function formData(): array
    {
        return [
            'years'  => AcademicYear::orderByDesc('name')->get(),
            'levels' => AdmissionLevel::orderBy('sort_order')->get(),
            'rounds' => ApplicationRound::with('academicYear')->orderByDesc('round_number')->get(),
        ];
    }
}