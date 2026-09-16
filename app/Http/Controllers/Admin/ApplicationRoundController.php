<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\ApplicationRound;
use Illuminate\Http\Request;

class ApplicationRoundController extends Controller
{
    public function index()
    {
        return view('admin.rounds.index', [
            'rounds' => ApplicationRound::with('academicYear')->latest()->paginate(15),
        ]);
    }

    public function create()
    {
        return view('admin.rounds.create', ['years' => AcademicYear::orderByDesc('name')->get()]);
    }

    public function store(Request $request)
    {
        $d = $request->validate([
            'academic_year_id'          => ['required', 'exists:academic_years,id'],
            'round_number'              => ['required', 'integer', 'min:1', 'max:50'],
            'name'                      => ['required', 'string', 'max:191'],
            'is_current'                => ['sometimes', 'boolean'],
            'allow_multiple_applications' => ['sometimes', 'boolean'],
            'is_active'                 => ['sometimes', 'boolean'],
        ]);

        $d['is_current'] = $request->boolean('is_current');
        $d['allow_multiple_applications'] = $request->boolean('allow_multiple_applications');
        $d['is_active'] = $request->boolean('is_active', true);

        $exists = ApplicationRound::where('academic_year_id', $d['academic_year_id'])
            ->where('round_number', $d['round_number'])->exists();

        if ($exists) {
            return back()->withErrors(['round_number' => 'This round number already exists for the selected academic year.'])->withInput();
        }

        ApplicationRound::create($d);

        return redirect()->route('admin.rounds.index')->with('success', 'Round created.');
    }

    public function edit(ApplicationRound $round)
    {
        return view('admin.rounds.edit', ['round' => $round, 'years' => AcademicYear::orderByDesc('name')->get()]);
    }

    public function update(Request $request, ApplicationRound $round)
    {
        $d = $request->validate([
            'academic_year_id'          => ['required', 'exists:academic_years,id'],
            'round_number'              => ['required', 'integer', 'min:1', 'max:50'],
            'name'                      => ['required', 'string', 'max:191'],
            'is_current'                => ['sometimes', 'boolean'],
            'allow_multiple_applications' => ['sometimes', 'boolean'],
            'is_active'                 => ['sometimes', 'boolean'],
        ]);

        $d['is_current'] = $request->boolean('is_current');
        $d['allow_multiple_applications'] = $request->boolean('allow_multiple_applications');
        $d['is_active'] = $request->boolean('is_active');

        $round->update($d);

        return redirect()->route('admin.rounds.index')->with('success', 'Round updated.');
    }

    public function destroy(ApplicationRound $round)
    {
        if ($round->admissionWindows()->exists()) {
            return back()->with('error', 'Cannot delete — admission windows exist for this round.');
        }

        $round->delete();

        return back()->with('success', 'Deleted.');
    }
}