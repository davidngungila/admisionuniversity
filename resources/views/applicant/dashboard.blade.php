@extends('layouts.applicant')
@section('title','Dashboard')
@section('content')
<div class="page-head">
    <div>
        <h1>Welcome, {{ auth()->user()->name }}</h1>
        <p class="page-sub">Academic Year: {{ $academicYear?->name ?? '—' }} · Manage your admission applications</p>
    </div>
    <div class="page-actions">
        @if($applications->isEmpty())
            <span class="tag tag-gold">No applications yet</span>
        @else
            <span class="tag tag-blue">{{ $applications->count() }} application(s)</span>
        @endif
    </div>
</div>

@if($applications->isEmpty())
    <div class="panel">
        <div class="panel-body">
            <div class="empty-state">
                <div class="es-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                </div>
                <strong>No applications yet</strong>
                <p>Start by choosing an admission window below. Your applications will appear here once created.</p>
            </div>
        </div>
    </div>
@endif

@if($applications->isNotEmpty())
    @foreach($applications as $app)
        @php
            $steps = $app->admissionWindow->admissionLevel->workflowSteps()->where('is_active',true)->orderBy('step_number')->get();
            $completed = $app->stepCompletions()->whereNotNull('completed_at')->count();
            $total = $steps->count();
            $pct = $total ? round($completed/$total*100) : 0;
        @endphp
        <div class="panel" style="margin-bottom:16px;">
            <div class="panel-head">
                <div>
                    <div class="panel-title">{{ $app->application_number ?? 'Draft — '.$app->admissionWindow->admissionLevel->name }}</div>
                    <div class="panel-sub">{{ $app->academicYear->name }} · Round {{ $app->admissionWindow->applicationRound->round_number }} · {{ $app->admissionWindow->applicant_category }}</div>
                </div>
                <span class="tag
                    @if($app->status==='SUBMITTED') tag-blue
                    @elseif($app->status==='ADMITTED' || $app->status==='SELECTED') tag-green
                    @elseif($app->status==='REJECTED') tag-red
                    @else tag-gold @endif
                ">{{ $app->status }}</span>
            </div>
            <div class="panel-body">
                <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;">
                    <span class="tag tag-grey">Application Progress</span>
                    <span class="tag tag-blue">{{ $completed }} of {{ $total }} steps — {{ $pct }}%</span>
                </div>
                <div style="margin-top:10px;height:8px;background:var(--sand-100);border:1px solid var(--line);border-radius:20px;overflow:hidden;">
                    <div style="height:100%;background:var(--acacia-600);width:{{ $pct }}%;transition:width .3s;"></div>
                </div>

                <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:16px;">
                    @foreach($steps as $st)
                        @php $done = $app->stepCompletions()->where('workflow_step_id',$st->id)->whereNotNull('completed_at')->exists(); @endphp
                        <span class="tag {{ $done ? 'tag-green' : 'tag-grey' }}" style="display:inline-flex;align-items:center;gap:6px;">
                            <span style="width:18px;height:18px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-size:10px;font-weight:800;flex:none;background:{{ $done ? 'var(--acacia-600)' : 'var(--sand-200)' }};color:{{ $done ? '#fff' : 'var(--coffee-700)' }};border:1px solid {{ $done ? 'var(--acacia-600)' : 'var(--line)' }};">{{ $done ? '✓' : $st->step_number }}</span>
                            {{ $st->step_name }}
                        </span>
                    @endforeach
                </div>

                @if($app->selectedProgrammes->count())
                    <div class="kv" style="margin-top:16px;">
                        <div class="kv-row" style="background:var(--sand-50);">
                            <span class="k" style="font-weight:800;color:var(--coffee-900);">Programmes Applied</span>
                            <span class="v" style="font-weight:600;color:var(--ink-soft);">{{ $app->selectedProgrammes->count() }} programme(s)</span>
                        </div>
                        @foreach($app->selectedProgrammes->sortBy('preference_order') as $ap)
                            <div class="kv-row">
                                <span class="k">{{ $ap->preference_order }}. {{ $ap->programme->name }}</span>
                                <span class="v" style="color:var(--ink-soft);font-weight:600;">{{ $ap->programme->code }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div style="margin-top:16px;padding:10px 14px;border:1px dashed var(--line);border-radius:10px;background:var(--sand-50);font-size:13px;color:var(--ink-soft);">No programmes selected yet — continue to programme selection step.</div>
                @endif

                <div style="display:flex;gap:10px;margin-top:18px;flex-wrap:wrap;">
                    @if(in_array($app->status, ['SELECTED','ADMITTED']))
                        <a href="{{ route('applicant.result.show', encId($app->id)) }}" class="btn btn-primary btn-sm" style="background:var(--acacia-600)"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex:none"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg> View Admission Letter</a>
                        <a href="{{ route('applicant.application.form', encId($app->id)) }}" class="btn btn-ghost btn-sm"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex:none"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg> Preview Application Form</a>
                        <a href="{{ route('applicant.application.status', encId($app->id)) }}" class="btn btn-ghost btn-sm">View Status</a>
                    @elseif(in_array($app->status, ['SUBMITTED','UNDER_REVIEW']))
                        <a href="{{ route('applicant.application.form', encId($app->id)) }}" class="btn btn-primary btn-sm"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex:none"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg> Preview Application Form</a>
                        <a href="{{ route('applicant.application.status', encId($app->id)) }}" class="btn btn-ghost btn-sm">View Status</a>
                        <a href="{{ route('applicant.result.show', encId($app->id)) }}" class="btn btn-ghost btn-sm">Track Progress</a>
                    @elseif($app->status === 'REJECTED')
                        <a href="{{ route('applicant.application.form', encId($app->id)) }}" class="btn btn-ghost btn-sm"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex:none"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg> Preview Application Form</a>
                        <a href="{{ route('applicant.result.show', encId($app->id)) }}" class="btn btn-ghost btn-sm">View Result</a>
                    @else
                        <a href="{{ route('applicant.application.show', encId($app->id)) }}" class="btn btn-primary btn-sm">Continue Application</a>
                        <a href="{{ route('applicant.application.form', encId($app->id)) }}" class="btn btn-ghost btn-sm"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex:none"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg> Preview Application Form</a>
                        <a href="{{ route('applicant.application.status', encId($app->id)) }}" class="btn btn-ghost btn-sm">View Status</a>
                    @endif
                </div>
            </div>
        </div>
    @endforeach

    @if($applications->count() > 1)
        <div class="panel">
            <div class="panel-head"><div class="panel-title">Programmes Applied — History</div><div class="panel-sub">Grouping across rounds and academic years</div></div>
            <div class="panel-body">
                @php $grouped = $applications->groupBy(fn($a)=>$a->academicYear->name.' — Round '.$a->admissionWindow->applicationRound->round_number); @endphp
                @foreach($grouped as $label => $apps)
                    <div style="margin-bottom:18px;">
                        <div style="font-size:11px;font-weight:800;letter-spacing:.08em;text-transform:uppercase;color:var(--ink-soft);margin-bottom:8px;">{{ $label }}</div>
                        <div class="kv">
                            @foreach($apps as $app)
                                @foreach($app->selectedProgrammes->sortBy('preference_order') as $ap)
                                    <div class="kv-row">
                                        <span class="k">{{ $ap->programme->name }}</span>
                                        <span class="v" style="color:var(--ink-soft);">{{ $ap->programme->code }} · {{ $app->application_number ?? '#'.$app->id }}</span>
                                    </div>
                                @endforeach
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    @php $selectedApps = $applications->whereIn('status', ['SELECTED','ADMITTED']); @endphp
    @if($selectedApps->isNotEmpty())
        @foreach($selectedApps as $selApp)
            @php
                $selProgramme = $selApp->selectionResults->first()?->programme ?? $selApp->selectedProgrammes->first()?->programme;
                $letter = $selApp->admissionLetters->first();
            @endphp
            <div class="panel" style="margin-top:24px;margin-bottom:16px;border:1px solid var(--line);overflow:hidden;">
                <div style="background:var(--acacia-100);border-bottom:1px solid #c8d7a8;padding:16px 20px;display:flex;gap:14px;align-items:center;">
                    <div style="width:48px;height:48px;border-radius:12px;background:var(--acacia-600);display:flex;align-items:center;justify-content:center;flex:none;color:#fff;"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg></div>
                    <div>
                        <div style="font-weight:800;font-size:16px;color:var(--acacia-600);">Congratulations, {{ auth()->user()->name }}! You have been selected.</div>
                        <div style="font-size:13px;color:var(--coffee-700);margin-top:2px;">Welcome to the {{ \App\Models\Setting::getValue('university_name','University of Dodoma') }} — {{ $selApp->academicYear->name }} · {{ $selApp->admissionWindow->admissionLevel->name }}</div>
                    </div>
                    <span class="tag" style="margin-left:auto;background:var(--acacia-600);color:#fff;">{{ $selApp->status }}</span>
                </div>
                <div class="panel-body">
                    <p style="font-size:13.5px;line-height:1.6;color:var(--coffee-800);margin:0;">We are pleased to inform you that you have been <strong>selected</strong> to join the {{ \App\Models\Setting::getValue('university_name','University of Dodoma') }}. Your hard work has paid off — welcome to the {{ \App\Models\Setting::getValue('university_acronym','UDOM') }} family! Please download your admission letter and joining instructions below and follow the reporting instructions.</p>
                    @if($selProgramme)
                        <div class="kv" style="margin-top:14px;">
                            <div class="kv-row"><span class="k">Selected Programme</span><span class="v" style="font-weight:800;color:var(--coffee-900)">{{ $selProgramme->name }} ({{ $selProgramme->code }})</span></div>
                            <div class="kv-row"><span class="k">Application No</span><span class="v mono">{{ $selApp->application_number ?? '#'.$selApp->id }}</span></div>
                            <div class="kv-row"><span class="k">Academic Year</span><span class="v">{{ $selApp->academicYear->name }}</span></div>
                        </div>
                    @endif
                    <div style="margin-top:16px;display:flex;gap:10px;flex-wrap:wrap;">
                        @if($letter)
                            <a href="{{ route('applicant.application.letter', encId($selApp->id)) }}" class="btn btn-primary btn-sm" style="background:var(--acacia-600)"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex:none"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg> Preview Admission Letter</a>
                        @else
                            <a href="{{ route('applicant.application.letter', encId($selApp->id)) }}" class="btn btn-primary btn-sm"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex:none"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg> View Admission Letter</a>
                        @endif
                        <a href="{{ route('applicant.application.joining', encId($selApp->id)) }}" class="btn btn-ghost btn-sm" style="border-color:var(--acacia-600);color:var(--acacia-600)"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex:none"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg> Preview Joining</a>
                        <a href="{{ route('applicant.application.status', encId($selApp->id)) }}" class="btn btn-ghost btn-sm">View Status</a>
                    </div>
                    <div style="margin-top:14px;padding:10px 14px;border:1px solid var(--acacia-100);background:var(--acacia-100);border-radius:10px;font-size:12.5px;color:var(--acacia-600);line-height:1.5;"><strong>Next steps:</strong> Download your admission letter, read the joining instructions carefully, confirm your admission, and report on the date indicated in the letter. For help, use the {{ \App\Models\Setting::getValue('university_acronym','UDOM') }} Support helpline float button.</div>
                </div>
            </div>
        @endforeach
    @endif
@endif

{{-- Available windows — filtered by what you want to apply for — hidden if already selected --}}
@if(empty($hasSelected) && isset($openWindows) && $openWindows->isNotEmpty())
<div class="table-card" style="margin-top:22px">
    <div class="panel-head">
        <div>
            <div class="panel-title">Start New Application @if(!empty($intendedLevelName))<span class="tag tag-terracotta" style="margin-left:8px">You selected: {{ $intendedLevelName }}</span>@endif</div>
            <div class="panel-sub">Choose an admission window to start @if(!empty($intendedLevelName)) — highlighted for {{ $intendedLevelName }} @endif</div>
        </div>
        <span class="tag tag-grey">{{ $openWindows->count() }} windows open</span>
    </div>
    <div class="table-scroll">
        <table>
            <thead><tr><th>Level</th><th>Round</th><th>Category</th><th>Period</th><th class="right">Fee</th><th class="center">Status</th><th class="right">Action</th></tr></thead>
            <tbody>
                @foreach($openWindows as $w)
                @php
                    $isIntended = !empty($intendedLevelId) && $w->admission_level_id == $intendedLevelId;
                    $existingApp = $applications->firstWhere('admission_window_id', $w->id);
                @endphp
                <tr @if($isIntended) style="background:var(--terracotta-100)" @endif>
                    <td><div class="cell-main"><div class="thumb {{ $isIntended ? 'thumb-terracotta' : 'thumb-grey' }}">{{ substr($w->admissionLevel->short_name,0,1) }}</div><div><div class="cell-title" style="font-size:13px">{{ $w->admissionLevel->name }}</div><div class="cell-sub">{{ $w->academicYear->name }}</div></div></div></td>
                    <td class="center"><span class="tag tag-blue">R{{ $w->applicationRound->round_number }}</span></td>
                    <td class="center"><span class="tag tag-grey">{{ $w->applicant_category }}</span></td>
                    <td><div class="cell-sub">{{ $w->opens_at->format('d M Y') }} → {{ $w->closes_at->format('d M Y') }}</div></td>
                    <td class="right"><span class="tag {{ $w->application_fee <= 0 ? 'tag-green' : 'tag-gold' }}">{{ $w->feeLabel() }}</span></td>
                    <td class="center">@if($w->statusLabel()==='OPEN')<span class="tag tag-green">Open</span>@elseif($w->statusLabel()==='CLOSING SOON')<span class="tag tag-gold">Closing soon</span>@else<span class="tag tag-grey">{{ $w->statusLabel() }}</span>@endif</td>
                    <td class="right">
                        @if($existingApp)
                            @php $st = $existingApp->status; @endphp
                            @if(in_array($st, ['SUBMITTED','UNDER_REVIEW','SELECTED','ADMITTED','REJECTED']))
                                <span class="tag {{ $st==='SUBMITTED' ? 'tag-blue' : ($st==='REJECTED' ? 'tag-red' : ($st==='ADMITTED' || $st==='SELECTED' ? 'tag-green' : 'tag-gold')) }}" style="margin-right:6px">{{ $st }}</span>
                                <a href="{{ route('applicant.application.status', encId($existingApp->id)) }}" class="btn btn-ghost btn-sm">View Status</a>
                            @else
                                <a href="{{ route('applicant.application.show', encId($existingApp->id)) }}" class="btn btn-primary btn-sm">Continue</a>
                            @endif
                        @else
                            <form method="POST" action="{{ route('applicant.application.start', encId($w->id)) }}">@csrf<button class="btn {{ $isIntended ? 'btn-primary' : 'btn-ghost' }} btn-sm" style="display:inline-flex;align-items:center;gap:6px">@if($isIntended)<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex:none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>@endif {{ $isIntended ? 'Apply' : 'Apply' }}</button></form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@elseif(empty($hasSelected) && isset($openWindows))
<div class="panel" style="margin-top:22px"><div class="empty-state" style="padding:24px"><strong>No open windows</strong><p>Check back later for new admission windows.</p></div></div>
@endif
@endsection
