<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdmissionLevel;
use App\Models\ApplicationWorkflowStep;
use Illuminate\Http\Request;

class WorkflowController extends Controller
{
    public function index(Request $request)
    {
        $q = ApplicationWorkflowStep::with('admissionLevel')->orderBy('admission_level_id')->orderBy('step_number');

        if ($request->filled('level')) {
            $q->where('admission_level_id', $request->level);
        }

        return view('admin.workflow.index', [
            'steps'  => $q->get()->groupBy(fn ($s) => $s->admissionLevel->name),
            'levels' => AdmissionLevel::orderBy('sort_order')->get(),
        ]);
    }

    public function create()
    {
        return view('admin.workflow.create', ['levels' => AdmissionLevel::orderBy('sort_order')->get()]);
    }

    public function store(Request $request)
    {
        $d = $request->validate([
            'admission_level_id' => ['required','exists:admission_levels,id'],
            'step_number'        => ['required','integer','min:1'],
            'step_name'          => ['required','string','max:191'],
            'route'              => ['required','string','max:191'],
            'description'        => ['nullable','string'],
            'is_required'        => ['sometimes','boolean'],
            'is_active'          => ['sometimes','boolean'],
        ]);

        $d['is_required'] = $request->boolean('is_required', true);
        $d['is_active']   = $request->boolean('is_active', true);

        $exists = ApplicationWorkflowStep::where('admission_level_id',$d['admission_level_id'])->where('step_number',$d['step_number'])->exists();
        if ($exists) return back()->withErrors(['step_number'=>'Step number already taken for this level.']);

        ApplicationWorkflowStep::create($d);

        return redirect()->route('admin.workflow.index')->with('success','Step created.');
    }

    public function edit(ApplicationWorkflowStep $workflow)
    {
        return view('admin.workflow.edit', ['step'=>$workflow,'levels'=>AdmissionLevel::orderBy('sort_order')->get()]);
    }

    public function update(Request $request, ApplicationWorkflowStep $workflow)
    {
        $d = $request->validate([
            'admission_level_id' => ['required','exists:admission_levels,id'],
            'step_number'        => ['required','integer','min:1'],
            'step_name'          => ['required','string','max:191'],
            'route'              => ['required','string','max:191'],
            'description'        => ['nullable','string'],
            'is_required'        => ['sometimes','boolean'],
            'is_active'          => ['sometimes','boolean'],
        ]);

        $d['is_required']=$request->boolean('is_required');
        $d['is_active']=$request->boolean('is_active');

        $workflow->update($d);

        return redirect()->route('admin.workflow.index')->with('success','Updated.');
    }

    public function destroy(ApplicationWorkflowStep $workflow)
    {
        if ($workflow->completions()->exists()) return back()->with('error','Cannot delete — applications have used this step.');
        $workflow->delete();
        return back()->with('success','Deleted.');
    }
}