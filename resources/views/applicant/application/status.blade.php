@extends('layouts.applicant')
@section('title','Application Status')
@section('content')
<div class="page-head">
    <div>
        <h1>Application Status</h1>
        <p class="page-sub">Application {{ $application->application_number ?? '— (generated on submit)' }} · {{ $application->admissionWindow->admissionLevel->name }} · Round {{ $application->admissionWindow->applicationRound->round_number }} · {{ $application->academicYear->name }}</p>
    </div>
    <div class="page-actions" style="display:flex;gap:8px;flex-wrap:wrap;">
        <a href="{{ route('applicant.application.form', encId($application->id)) }}" class="btn btn-primary btn-sm"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex:none"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg> Preview Application Form</a>
        <a href="{{ route('applicant.application.form', encId($application->id)) }}?download=1" class="btn btn-ghost btn-sm"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex:none"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg> Download PDF</a>
        <a href="{{ route('applicant.application.summary', encId($application->id)) }}" class="btn btn-ghost btn-sm">View Summary</a>
        <a href="{{ route('applicant.dashboard') }}" class="btn btn-ghost btn-sm">Back to Dashboard</a>
    </div>
</div>

<div class="panel" style="text-align:center;">
    <div class="panel-body" style="padding:28px 20px;">
        <div class="panel-title" style="letter-spacing:.08em;text-transform:uppercase;color:var(--ink-soft);font-size:11px;">Application Status</div>
        <div style="margin-top:12px;">
            @php
                $statusTag = match($application->status) {
                    'SUBMITTED' => 'tag-blue',
                    'SELECTED','ADMITTED' => 'tag-green',
                    'REJECTED' => 'tag-red',
                    'WAITLISTED' => 'tag-gold',
                    default => 'tag-grey',
                };
            @endphp
            <span class="tag {{ $statusTag }}" style="font-size:13px;padding:8px 16px;font-weight:800;">✓ {{ $application->status }}</span>
        </div>
        <div style="margin-top:12px;font-size:13.5px;color:var(--coffee-800);">Application No: <span class="mono" style="font-weight:800;color:var(--coffee-900);background:var(--sand-50);border:1px solid var(--line);padding:3px 8px;border-radius:8px;">{{ $application->application_number ?? '— (generated on submit)' }}</span></div>
        <div class="field-hint" style="margin-top:6px;">Round: {{ $application->admissionWindow->applicationRound->round_number }} · {{ $application->academicYear->name }} · {{ $application->admissionWindow->admissionLevel->name }}</div>
        @if($application->submitted_at)<div class="field-hint">Submitted {{ $application->submitted_at->format('d M Y H:i') }}</div>@endif
    </div>
</div>

<div class="panel" style="margin-top:16px;">
    <div class="panel-head"><div class="panel-title">Application Progress</div></div>
    <div class="panel-body">
        @php $completed = $completed ?? collect(); $total = $steps->count(); $done = $completed->count(); $pct = $total ? round($done/$total*100) : 0; @endphp
        <div style="display:flex;justify-content:space-between;gap:12px;align-items:center;flex-wrap:wrap;">
            <span class="tag tag-grey">Progress</span>
            <span class="tag tag-blue">{{ $done }} of {{ $total }} steps — {{ $pct }}%</span>
        </div>
        <div style="margin-top:10px;height:8px;background:var(--sand-100);border:1px solid var(--line);border-radius:20px;overflow:hidden;">
            <div style="height:100%;background:var(--acacia-600);width:{{ $pct }}%"></div>
        </div>
        <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:16px;">
            @foreach($steps as $st)
                @php $isDone = $completed->contains($st->id); @endphp
                <span class="tag {{ $isDone ? 'tag-green' : 'tag-grey' }}" style="display:inline-flex;align-items:center;gap:6px;">
                    <span style="width:18px;height:18px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-size:10px;font-weight:800;background:{{ $isDone ? 'var(--acacia-600)' : 'var(--sand-200)' }};color:{{ $isDone ? '#fff' : 'var(--coffee-700)' }};border:1px solid {{ $isDone ? 'var(--acacia-600)' : 'var(--line)' }};">{{ $isDone ? '✓' : $st->step_number }}</span>
                    {{ $st->step_name }}
                </span>
            @endforeach
        </div>
    </div>
</div>

<div class="panel" style="margin-top:16px;">
    <div class="panel-head"><div class="panel-title">Status History</div></div>
    <div class="panel-body">
        @forelse($application->statusHistory as $h)
            <div style="display:flex;justify-content:space-between;gap:12px;align-items:center;padding:12px 14px;border:1px solid var(--line);border-radius:10px;background:var(--sand-50);margin-bottom:8px;flex-wrap:wrap;">
                <div style="font-size:13px;">
                    <span class="tag tag-grey">{{ $h->old_status ?? '—' }}</span>
                    <span style="margin:0 6px;color:var(--ink-soft);font-weight:700;">→</span>
                    @php $newTag = match($h->new_status){ 'SUBMITTED'=>'tag-blue','SELECTED'=>'tag-green','ADMITTED'=>'tag-green','REJECTED'=>'tag-red','WAITLISTED'=>'tag-gold', default=>'tag-grey'}; @endphp
                    <span class="tag {{ $newTag }}">{{ $h->new_status }}</span>
                    @if($h->remarks)<span style="margin-left:8px;color:var(--ink-soft);font-size:12.5px;">— {{ $h->remarks }}</span>@endif
                </div>
                <span class="field-hint" style="white-space:nowrap;">{{ $h->created_at->format('d M Y H:i') }}</span>
            </div>
        @empty
            <div class="empty-state" style="padding:28px 20px;">
                <div class="es-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg></div>
                <strong>No status changes yet</strong>
                <p>Your application history will appear here once updated.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
