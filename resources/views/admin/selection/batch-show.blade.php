@extends('layouts.admin')
@section('title','Batch — '.$batch->name)
@section('content')
<div class="page-head">
    <div><h1>{{ $batch->name }}</h1><p class="page-sub">{{ $batch->academicYear->name }} · Round {{ $batch->applicationRound->round_number }} — <span class="tag {{ $batch->status==='PROCESSED' ? 'tag-green' : 'tag-gold' }}">{{ $batch->status }}</span></p></div>
    <div class="page-actions"><a href="{{ route('admin.selection.batches') }}" class="btn btn-ghost">← Back to Batches</a></div>
</div>

<div class="panel">
    <div class="panel-head"><div class="panel-title">Batch Overview</div><span class="tag {{ $batch->status==='PROCESSED' ? 'tag-green' : 'tag-gold' }}">{{ $batch->status }}</span></div>
    <div class="panel-body">
        <div class="kv">
            <div class="kv-row"><span class="k">Name</span><span class="v">{{ $batch->name }}</span></div>
            <div class="kv-row"><span class="k">Academic Year</span><span class="v">{{ $batch->academicYear->name }}</span></div>
            <div class="kv-row"><span class="k">Round</span><span class="v">Round {{ $batch->applicationRound->round_number }} — {{ $batch->applicationRound->name }}</span></div>
            <div class="kv-row"><span class="k">Status</span><span class="v"><span class="tag {{ $batch->status==='PROCESSED' ? 'tag-green' : 'tag-gold' }}">{{ $batch->status }}</span></span></div>
        </div>
        @if($batch->status==='PENDING')
            <div style="margin-top:18px">
                <form method="POST" action="{{ route('admin.selection.run', encId($batch->id)) }}" onsubmit="event.preventDefault(); const _f=this; confirmModal('Run selection','Are you sure you want to run selection for this batch? This will rank and assign applicants.',()=>_f.submit())">@csrf<button class="btn btn-primary" style="background:var(--acacia-600)">▶ Run Selection (rank & assign first-choice)</button></form>
                <span class="field-hint" style="display:block;margin-top:8px">Ranks SUBMITTED applications by average grade and selects for first-choice programme.</span>
            </div>
        @endif
    </div>
</div>

<div class="table-card" style="margin-top:18px">
    <div class="panel-head"><div class="panel-title">Results ({{ $batch->results->count() }})</div></div>
    <div class="table-scroll">
        <table>
            <thead><tr><th>Applicant</th><th>Programme</th><th class="center">Score</th><th class="center">Status</th><th class="right">Actions</th></tr></thead>
            <tbody>
                @forelse($batch->results as $r)
                    <tr>
                        <td><div class="cell-main"><div class="thumb thumb-coffee">{{ substr($r->application->applicant->fullName(),0,1) }}</div><div><div class="cell-title" style="font-size:13px">{{ $r->application->applicant->fullName() }}</div><div class="cell-sub mono">{{ $r->application->application_number }}</div></div></div></td>
                        <td><span class="cell-title" style="font-size:13px">{{ $r->programme->name }}</span></td>
                        <td class="center"><strong>{{ $r->rank_score }}</strong></td>
                        <td class="center"><span class="tag {{ $r->status==='SELECTED' ? 'tag-green' : ($r->status==='REJECTED' ? 'tag-red' : 'tag-grey') }}">{{ $r->status }}</span></td>
                        <td class="right">
                            <form method="POST" action="{{ route('admin.selection.result.update', encId($r->id)) }}" class="form-row" style="justify-content:flex-end" onsubmit="event.preventDefault(); const _f=this; confirmModal('Update result','Are you sure you want to update this selection result?',()=>_f.submit())">
                                @csrf @method('PATCH')
                                <select name="status" style="padding:7px 10px;border:1.5px solid var(--line);border-radius:8px;font-size:12.5px">
                                    <option value="SELECTED" @selected($r->status=='SELECTED')>SELECTED</option>
                                    <option value="WAITLISTED" @selected($r->status=='WAITLISTED')>WAITLISTED</option>
                                    <option value="REJECTED" @selected($r->status=='REJECTED')>REJECTED</option>
                                </select>
                                <button class="btn btn-ghost btn-sm">Update</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5"><div class="empty-state" style="padding:32px"><div class="es-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg></div><strong>No results</strong><p>Run selection to generate results.</p></div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
