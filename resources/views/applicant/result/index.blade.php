@extends('layouts.applicant')
@section('title','Admission Result')
@section('content')
<div class="page-head">
    <div>
        <h1>Admission Results</h1>
        <p class="page-sub">Check selection, admission letters and verification.</p>
    </div>
</div>

<div class="panel">
    <div class="panel-head"><div class="panel-title">Your Applications</div><span class="tag tag-grey">{{ $applications->count() }} application(s)</span></div>
    <div class="panel-body" style="display:flex;flex-direction:column;gap:12px;">
        @forelse($applications as $app)
            <div class="panel" style="margin:0;box-shadow:none;border:1px solid var(--line);background:var(--white);">
                <div class="panel-body" style="padding:14px 16px;">
                    <div style="display:flex;justify-content:space-between;gap:12px;align-items:flex-start;flex-wrap:wrap;">
                        <div>
                            <div style="font-weight:800;font-size:13.5px;color:var(--coffee-900);">{{ $app->application_number ?? 'Draft #'.$app->id }}</div>
                            <div class="field-hint">{{ $app->academicYear->name }} · Round {{ $app->admissionWindow->applicationRound->round_number }} · {{ $app->admissionWindow->admissionLevel->name }}</div>
                        </div>
                        @php $tag = match($app->status){ 'ADMITTED'=>'tag-green','SELECTED'=>'tag-green','WAITLISTED'=>'tag-gold','REJECTED'=>'tag-red', 'SUBMITTED'=>'tag-blue', default=>'tag-grey'}; @endphp
                        <span class="tag {{ $tag }}">{{ $app->status }}</span>
                    </div>
                    <div class="field-hint" style="margin-top:8px;">Programmes: {{ $app->selectedProgrammes->map(fn($ap)=>$ap->programme->name)->join(', ') ?: '—' }}</div>
                    <a href="{{ route('applicant.result.show', encId($app->id)) }}" class="btn btn-ghost btn-sm" style="margin-top:12px;">View Result</a>
                </div>
            </div>
        @empty
            <div class="empty-state">
                <div class="es-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg></div>
                <strong>No applications found</strong>
                <p>Your admission results will appear here once available.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
