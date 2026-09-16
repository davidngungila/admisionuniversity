<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use Illuminate\Http\Request;

class AcademicYearController extends Controller
{
    public function index()
    {
        return view('admin.academic-years.index', ['years' => AcademicYear::latest()->paginate(15)]);
    }

    public function create()
    {
        return view('admin.academic-years.create');
    }

    public function store(Request $request)
    {
        $d = $request->validate([
            'name'       => ['required', 'string', 'max:20', 'unique:academic_years'],
            'start_date' => ['nullable', 'date'],
            'end_date'   => ['nullable', 'date', 'after:start_date'],
            'is_active'  => ['sometimes', 'boolean'],
        ]);

        $d['is_active'] = $request->boolean('is_active');
        $d['status']    = $d['is_active'] ? 'active' : 'inactive';

        if ($d['is_active']) {
            AcademicYear::query()->update(['is_active' => false, 'status' => 'inactive']);
        }

        AcademicYear::create($d);

        return redirect()->route('admin.academic-years.index')->with('success', 'Academic year created.');
    }

    public function edit(AcademicYear $academicYear)
    {
        return view('admin.academic-years.edit', ['year' => $academicYear]);
    }

    public function update(Request $request, AcademicYear $academicYear)
    {
        $d = $request->validate([
            'name'       => ['required', 'string', 'max:20', 'unique:academic_years,name,'.$academicYear->id],
            'start_date' => ['nullable', 'date'],
            'end_date'   => ['nullable', 'date', 'after:start_date'],
            'is_active'  => ['sometimes', 'boolean'],
        ]);

        $d['is_active'] = $request->boolean('is_active');
        $d['status']    = $d['is_active'] ? 'active' : 'inactive';

        if ($d['is_active']) {
            AcademicYear::where('id', '!=', $academicYear->id)->update(['is_active' => false, 'status' => 'inactive']);
        }

        $academicYear->update($d);

        return redirect()->route('admin.academic-years.index')->with('success', 'Academic year updated.');
    }

    public function destroy(AcademicYear $academicYear)
    {
        if ($academicYear->admissionWindows()->exists() || $academicYear->applications()->exists()) {
            return back()->with('error', 'Cannot delete — linked records exist.');
        }

        $academicYear->delete();

        return back()->with('success', 'Deleted.');
    }

    public function activate(AcademicYear $academicYear)
    {
        AcademicYear::query()->update(['is_active' => false, 'status' => 'inactive']);
        $academicYear->update(['is_active' => true, 'status' => 'active']);

        return back()->with('success', $academicYear->name.' is now the active year.');
    }
}