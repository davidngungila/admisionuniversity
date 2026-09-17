@extends('layouts.applicant')
@section('title','Submit Application')
@section('content')
<div class="page-head">
    <div>
        <h1>Submit Application</h1>
        <p class="page-sub">Step {{ $currentStep->step_number }} of {{ $steps->count() }} · Final review — verify all information before submitting.</p>
    </div>
    <div class="page-actions">
        <span class="tag tag-blue">{{ $progress['completed'] }} of {{ $progress['total'] }} — {{ $progress['percent'] }}%</span>
        <span class="tag tag-gold">Final Review</span>
    </div>
</div>

<div class="panel" style="margin-bottom:16px;">
    <div class="panel-body" style="padding:14px 18px;">
        <div style="height:6px;background:var(--sand-100);border:1px solid var(--line);border-radius:20px;overflow:hidden;">
            <div style="height:100%;background:var(--acacia-600);width:{{ $progress['percent'] }}%"></div>
        </div>
        <p class="field-hint" style="margin-top:8px;">Once submitted, your application will be queued for review. Application number is generated on submit.</p>
    </div>
</div>

<div class="grid-2">
    <div class="panel">
        <div class="panel-head"><div class="panel-title">Applicant</div></div>
        <div class="panel-body">
            <div class="kv">
                <div class="kv-row"><span class="k">Name</span><span class="v">{{ $summary['applicant']->fullName() }}</span></div>
                <div class="kv-row"><span class="k">DOB / Gender</span><span class="v">{{ $summary['applicant']->date_of_birth?->format('d M Y') ?? '—' }} · {{ $summary['applicant']->gender }}</span></div>
                <div class="kv-row"><span class="k">Phone</span><span class="v">{{ $summary['applicant']->phone }}</span></div>
                <div class="kv-row"><span class="k">Citizenship</span><span class="v">{{ $summary['applicant']->citizenship->name ?? '—' }}</span></div>
            </div>
        </div>
    </div>

    <div class="panel">
        <div class="panel-head"><div class="panel-title">Admission Window</div></div>
        <div class="panel-body">
            <div class="kv">
                <div class="kv-row"><span class="k">Level / Round</span><span class="v">{{ $summary['window']->admissionLevel->name }} — Round {{ $summary['window']->applicationRound->round_number }}</span></div>
                <div class="kv-row"><span class="k">Academic Year</span><span class="v">{{ $summary['academicYear']->name }}</span></div>
                <div class="kv-row"><span class="k">Fee / Category</span><span class="v">{{ $summary['window']->feeLabel() }} · {{ $summary['window']->applicant_category }}</span></div>
            </div>
        </div>
    </div>

    <div class="panel">
        <div class="panel-head"><div class="panel-title">Payments</div><span class="tag tag-grey">{{ $summary['payments']->count() }} record(s)</span></div>
        <div class="panel-body">
            @forelse($summary['payments'] as $pay)
                <div style="display:flex;justify-content:space-between;gap:12px;padding:10px 0;border-bottom:1px solid var(--line);font-size:13px;">
                    <span class="mono" style="font-size:12px;color:var(--coffee-700);">{{ $pay->payment_reference }} — {{ $pay->currency }} {{ number_format($pay->amount) }}</span>
                    <span class="tag {{ $pay->status==='CONFIRMED' ? 'tag-green' : ($pay->status==='FREE' ? 'tag-blue' : 'tag-gold') }}">{{ $pay->status }}</span>
                </div>
            @empty
                <div class="empty-state" style="padding:20px 0;"><strong>No payment record.</strong></div>
            @endforelse
        </div>
    </div>

    <div class="panel">
        <div class="panel-head"><div class="panel-title">Academic Results</div><span class="tag tag-grey">{{ $summary['results']->count() }} sitting(s)</span></div>
        <div class="panel-body">
            @forelse($summary['results'] as $r)
                <div style="display:flex;justify-content:space-between;gap:12px;padding:10px 0;border-bottom:1px solid var(--line);font-size:13px;">
                    <span style="font-weight:600;color:var(--coffee-900);">{{ $r->exam_type }} {{ $r->index_number }} ({{ $r->exam_year }})</span>
                    <span class="tag tag-grey">{{ count($r->results ?? []) }} subjects</span>
                </div>
            @empty
                <div class="empty-state" style="padding:20px 0;"><strong>No results.</strong></div>
            @endforelse
        </div>
    </div>
</div>

<div class="panel" style="margin-top:16px;">
    <div class="panel-head"><div class="panel-title">Programmes</div><span class="tag tag-grey">{{ $summary['programmes']->count() }} selected</span></div>
    <div class="panel-body">
        @forelse($summary['programmes'] as $ap)
            <div class="kv" style="margin-bottom:8px;">
                <div class="kv-row">
                    <span class="k">{{ $ap->preference_order }}. {{ $ap->programme->name }}</span>
                    <span class="v" style="color:var(--ink-soft);">{{ $ap->programme->code }}</span>
                </div>
            </div>
        @empty
            <div style="padding:12px 14px;border-radius:10px;background:var(--danger-100);border:1px solid #e8b4b0;color:var(--danger);font-size:13px;font-weight:600;">No programme selected — go back and select at least one.</div>
        @endforelse
    </div>
</div>

<div class="panel" style="margin-top:16px;">
    <div class="panel-head"><div class="panel-title">Documents</div><span class="tag tag-grey">{{ $summary['documents']->count() }} file(s)</span></div>
    <div class="panel-body">
        @forelse($summary['documents'] as $d)
            <div style="display:flex;justify-content:space-between;gap:12px;padding:8px 0;border-bottom:1px solid var(--line);font-size:13px;">
                <span style="font-weight:600;color:var(--coffee-900);">{{ $d->document_type }}</span>
                <span class="field-hint" style="text-align:right;max-width:60%;word-break:break-all;">{{ $d->file_name }}</span>
            </div>
        @empty
            <div class="empty-state" style="padding:20px 0;"><strong>No documents uploaded.</strong></div>
        @endforelse
    </div>
</div>

<div class="panel" style="margin-top:16px;background:var(--gold-100);border-color:#e9d4a0;">
    <div class="panel-body">
        <p style="margin:0;font-size:13px;line-height:1.6;color:#8a6418;font-weight:600;">By submitting, you declare that the information provided is true and correct. False information may lead to disqualification.</p>
    </div>
</div>

<div class="panel" style="margin-top:16px;background:#e8f0fe;border-color:#b6c8f0;">
    <div class="panel-body" style="display:flex;gap:10px;align-items:flex-start;">
        <div style="width:32px;height:32px;border-radius:8px;background:var(--terracotta-100);border:1px solid var(--line);display:flex;align-items:center;justify-content:center;flex:none;color:var(--terracotta-600);">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 5.07 12.81 19.79 19.79 0 0 1 2 4.18 2 2 0 0 1 4 2h3a2 2 0 0 1 2 1.72c.12 1.2.4 2.37.82 3.5a2 2 0 0 1-.57 2.11L8.09 10.49a16 16 0 0 0 5.42 5.42l1.16-1.16a2 2 0 0 1 2.11-.57c1.13.42 2.3.7 3.5.82A2 2 0 0 1 22 16.92z"/></svg>
        </div>
        <div style="flex:1;">
            <div style="font-weight:800;font-size:13px;color:#1a4da1;">SMS Confirmation</div>
            <div style="font-size:12.5px;color:var(--ink-soft);margin-top:4px;line-height:1.5;">A successful submission message will be sent instantly to <strong style="color:var(--coffee-900);">{{ $summary['applicant']->phone }}</strong> with your application number. Keep your phone on and track progress from your dashboard.</div>
        </div>
    </div>
</div>

<div class="panel" style="margin-top:16px;">
    <div class="panel-body">
        <form method="POST" action="{{ route('applicant.application.save', [encId($application->id), $currentStep->route]) }}" style="display:flex;justify-content:space-between;gap:12px;flex-wrap:wrap;" onsubmit="event.preventDefault(); const _f=this; confirmModal('Submit application','Are you sure you want to submit your application? This cannot be undone.',()=>_f.submit())">
            @csrf
            <a href="{{ route('applicant.application.show', encId($application->id)) }}" class="btn btn-ghost">Back</a>
            <button class="btn btn-primary" style="background:var(--acacia-600);">✓ Submit Application</button>
        </form>
    </div>
</div>
@endsection
