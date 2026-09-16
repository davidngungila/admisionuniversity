<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdmissionLevel;
use App\Models\Campus;
use App\Models\Department;
use App\Models\Programme;
use Illuminate\Http\Request;

class ProgrammeController extends Controller
{
    public function index(Request $request)
    {
        $q = Programme::with(['department.faculty', 'campus', 'admissionLevel']);

        if ($request->filled('level'))  $q->where('admission_level_id', $request->level);
        if ($request->filled('campus')) $q->where('campus_id', $request->campus);
        if ($request->filled('search')) $q->where('name','like','%'.$request->search.'%');

        return view('admin.programmes.index', [
            'programmes' => $q->latest()->paginate(15)->withQueryString(),
            'levels'     => AdmissionLevel::orderBy('sort_order')->get(),
            'campuses'   => Campus::orderBy('name')->get(),
        ]);
    }

    public function create()
    {
        return view('admin.programmes.create', $this->formData());
    }

    public function store(Request $request)
    {
        $d = $request->validate([
            'department_id'      => ['required','exists:departments,id'],
            'campus_id'          => ['required','exists:campuses,id'],
            'admission_level_id' => ['required','exists:admission_levels,id'],
            'name'               => ['required','string','max:191'],
            'code'               => ['required','string','max:30','unique:programmes'],
            'duration_years'     => ['required','integer','min:1','max:10'],
            'study_mode'         => ['required','string','max:30'],
            'tuition_fee'        => ['required','numeric','min:0'],
            'capacity'           => ['nullable','integer','min:1'],
            'description'        => ['nullable','string'],
            'status'             => ['nullable','in:active,inactive'],
        ]);

        $d['status'] = $d['status'] ?? 'active';
        Programme::create($d);

        return redirect()->route('admin.programmes.index')->with('success','Programme created.');
    }

    public function show(Programme $programme)
    {
        $programme->load(['department.faculty','campus','admissionLevel','requirements']);
        return view('admin.programmes.show', compact('programme'));
    }

    public function edit(Programme $programme)
    {
        return view('admin.programmes.edit', array_merge($this->formData(), ['programme'=>$programme]));
    }

    public function update(Request $request, Programme $programme)
    {
        $d = $request->validate([
            'department_id'      => ['required','exists:departments,id'],
            'campus_id'          => ['required','exists:campuses,id'],
            'admission_level_id' => ['required','exists:admission_levels,id'],
            'name'               => ['required','string','max:191'],
            'code'               => ['required','string','max:30','unique:programmes,code,'.$programme->id],
            'duration_years'     => ['required','integer','min:1','max:10'],
            'study_mode'         => ['required','string','max:30'],
            'tuition_fee'        => ['required','numeric','min:0'],
            'capacity'           => ['nullable','integer','min:1'],
            'description'        => ['nullable','string'],
            'status'             => ['nullable','in:active,inactive'],
        ]);

        $programme->update($d);

        return redirect()->route('admin.programmes.index')->with('success','Updated.');
    }

    public function destroy(Programme $programme)
    {
        if ($programme->applicationProgrammes()->exists()) return back()->with('error','Cannot delete — applications linked.');
        $programme->delete();
        return back()->with('success','Deleted.');
    }

    protected function formData(): array
    {
        return [
            'departments' => Department::with('faculty')->orderBy('name')->get(),
            'campuses'    => Campus::orderBy('name')->get(),
            'levels'      => AdmissionLevel::orderBy('sort_order')->get(),
        ];
    }
}