<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\AdmissionLevel;
use App\Models\Application;
use App\Models\ApplicationRound;
use App\Models\SelectionBatch;
use App\Models\SelectionResult;
use Illuminate\Http\Request;

class SelectionController extends Controller
{
    public function batches()
    {
        return view('admin.selection.batches', ['batches'=>SelectionBatch::with(['academicYear','applicationRound','admissionLevel'])->latest()->paginate(15)]);
    }

    public function createBatch()
    {
        return view('admin.selection.create-batch', [
            'years'=>AcademicYear::orderByDesc('name')->get(),
            'rounds'=>ApplicationRound::with('academicYear')->latest()->get(),
            'levels'=>AdmissionLevel::orderBy('sort_order')->get(),
        ]);
    }

    public function storeBatch(Request $request)
    {
        $d = $request->validate([
            'academic_year_id'=>['required','exists:academic_years,id'],
            'application_round_id'=>['required','exists:application_rounds,id'],
            'admission_level_id'=>['nullable','exists:admission_levels,id'],
            'name'=>['required','string','max:191'],
        ]);

        $d['created_by']=auth()->id();
        $d['status']='PENDING';
        SelectionBatch::create($d);

        return redirect()->route('admin.selection.batches')->with('success','Selection batch created.');
    }

    public function batchShow(SelectionBatch $batch)
    {
        $batch->load(['academicYear','applicationRound','admissionLevel','results.application.applicant','results.programme']);
        return view('admin.selection.batch-show', compact('batch'));
    }

    public function run(SelectionBatch $batch)
    {
        $applications = Application::where('academic_year_id',$batch->academic_year_id)
            ->whereHas('admissionWindow', fn($q)=>$q->where('application_round_id',$batch->application_round_id))
            ->where('status','SUBMITTED')
            ->with(['academicResults','selectedProgrammes.programme'])
            ->get();

        foreach ($applications as $app) {
            $score = $this->scoreFor($app);
            $firstChoice = $app->selectedProgrammes()->orderBy('preference_order')->first();

            if (!$firstChoice) continue;

            SelectionResult::updateOrCreate(
                ['selection_batch_id'=>$batch->id,'application_id'=>$app->id,'programme_id'=>$firstChoice->programme_id],
                ['status'=>'SELECTED','rank_score'=>$score,'position'=>null]
            );

            $app->advanceStatus(Application::STATUS_SELECTED, auth()->id(), 'Selected via batch '.$batch->name);

            app(\App\Services\SmsService::class)->notifySelection($app, Application::STATUS_SELECTED);
        }

        $batch->update(['status'=>'PROCESSED','processed_at'=>now()]);

        return back()->with('success','Selection run completed. '.$applications->count().' applications processed.');
    }

    protected function scoreFor(Application $app): float
    {
        $result = $app->academicResults->first();
        if (!$result) return 0;

        $map = ['A'=>5,'B'=>4,'C'=>3,'D'=>2,'E'=>1,'S'=>1,'F'=>0];
        $subjects = is_array($result->results) ? $result->results : [];
        $total = 0; $count = 0;

        foreach ($subjects as $s) {
            $total += $map[strtoupper($s['grade'] ?? 'F')] ?? 0;
            $count++;
        }

        return $count ? round($total / $count * 20, 2) : 0;
    }

    public function updateResult(Request $request, SelectionResult $result)
    {
        $d = $request->validate(['status'=>['required','in:SELECTED,WAITLISTED,REJECTED,NOT_SELECTED']]);
        $result->update($d);

        if ($d['status'] === 'SELECTED') {
            $result->loadMissing('application');
            app(\App\Services\SmsService::class)->notifySelection($result->application, Application::STATUS_SELECTED);
        }

        return back()->with('success','Result updated.');
    }
}