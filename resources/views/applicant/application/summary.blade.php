@extends('layouts.applicant')
@section('title','Application Summary')
@section('content')
<div class="page-head">
    <div>
        <h1>Application Summary</h1>
        <p class="page-sub">{{ $application->application_number ?? 'Draft' }} · {{ $application->admissionWindow->admissionLevel->name }} · Round {{ $application->admissionWindow->applicationRound->round_number }} · {{ $application->academicYear->name }}</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('applicant.application.status', encId($application->id)) }}" class="btn btn-primary btn-sm">Status</a>
        <a href="{{ route('applicant.dashboard') }}" class="btn btn-ghost btn-sm">Dashboard</a>
    </div>
</div>

<div class="panel">
    <div class="panel-head"><div class="panel-title">Personal Information</div></div>
    <div class="panel-body">
        <div class="kv">
            <div class="kv-row"><span class="k">Name</span><span class="v">{{ $application->applicant->fullName() }}</span></div>
            <div class="kv-row"><span class="k">DOB</span><span class="v">{{ $application->applicant->date_of_birth?->format('d M Y') ?? '—' }}</span></div>
            <div class="kv-row"><span class="k">Gender</span><span class="v">{{ $application->applicant->gender ?? '—' }}</span></div>
            <div class="kv-row"><span class="k">Citizenship</span><span class="v">{{ $application->applicant->citizenship->name ?? '—' }}</span></div>
            <div class="kv-row"><span class="k">Phone</span><span class="v">{{ $application->applicant->phone }}</span></div>
            <div class="kv-row"><span class="k">Email</span><span class="v">{{ $application->applicant->email }}</span></div>
        </div>
    </div>
</div>

<div class="panel" style="margin-top:16px;">
    <div class="panel-head"><div class="panel-title">Programmes Applied</div><span class="tag tag-grey">{{ $application->selectedProgrammes->count() }} programme(s)</span></div>
    <div class="panel-body" style="display:flex;flex-direction:column;gap:10px;">
        @forelse($application->selectedProgrammes->sortBy('preference_order') as $ap)
            <div class="panel" style="margin:0;box-shadow:none;border:1px solid var(--line);background:var(--sand-50);">
                <div class="panel-body" style="padding:12px 14px;">
                    <div style="display:flex;justify-content:space-between;gap:12px;align-items:center;flex-wrap:wrap;">
                        <div style="font-weight:700;font-size:13.5px;color:var(--coffee-900);">{{ $ap->preference_order }}. {{ $ap->programme->name }}</div>
                        <span class="tag tag-grey">{{ $ap->programme->code }}</span>
                    </div>
                    <div class="field-hint" style="margin-top:4px;">{{ $ap->programme->campus->name }} · {{ $ap->programme->duration_years }}y · TZS {{ number_format($ap->programme->tuition_fee) }}</div>
                </div>
            </div>
        @empty
            <div class="empty-state" style="padding:24px 20px;"><strong>No programmes.</strong></div>
        @endforelse
    </div>
</div>

<div class="grid-2" style="margin-top:16px;">
    <div class="panel">
        <div class="panel-head"><div class="panel-title">Payments</div><span class="tag tag-grey">{{ $application->payments->count() }}</span></div>
        <div class="panel-body">
            @forelse($application->payments as $p)
                <div style="display:flex;justify-content:space-between;gap:12px;padding:10px 0;border-bottom:1px solid var(--line);font-size:13px;align-items:center;flex-wrap:wrap;">
                    <span class="mono" style="font-size:12px;color:var(--coffee-700);">{{ $p->payment_reference }}</span>
                    <span class="tag {{ $p->status==='CONFIRMED' ? 'tag-green' : ($p->status==='FREE' ? 'tag-blue' : 'tag-gold') }}">{{ $p->status }} — {{ $p->currency }} {{ number_format($p->amount) }}</span>
                </div>
            @empty
                <div class="empty-state" style="padding:20px 0;"><strong>None.</strong></div>
            @endforelse
        </div>
    </div>

    <div class="panel">
        <div class="panel-head"><div class="panel-title">Academic Results</div><span class="tag tag-grey">{{ $application->academicResults->count() }}</span></div>
        <div class="panel-body">
            @forelse($application->academicResults as $r)
                <div style="display:flex;justify-content:space-between;gap:12px;padding:10px 0;border-bottom:1px solid var(--line);font-size:13px;align-items:center;">
                    <span style="font-weight:600;color:var(--coffee-900);font-size:13px;">{{ $r->exam_type }} {{ $r->index_number }} ({{ $r->exam_year }})</span>
                    <span class="tag tag-grey">{{ count($r->results ?? []) }} subjects</span>
                </div>
            @empty
                <div class="empty-state" style="padding:20px 0;"><strong>None.</strong></div>
            @endforelse
        </div>
    </div>
</div>

<div class="panel" style="margin-top:16px;">
    <div class="panel-head"><div class="panel-title">Documents</div><span class="tag tag-grey">{{ $application->documents->count() }} file(s)</span></div>
    <div class="panel-body">
        @forelse($application->documents as $d)
            <div style="display:flex;justify-content:space-between;gap:12px;padding:8px 0;border-bottom:1px solid var(--line);font-size:13px;align-items:center;">
                <span style="font-weight:600;color:var(--coffee-900);">{{ $d->document_type }}</span>
                <span class="field-hint" style="max-width:60%;word-break:break-all;text-align:right;">{{ $d->file_name }}</span>
            </div>
        @empty
            <div class="empty-state" style="padding:20px 0;"><strong>None.</strong></div>
        @endforelse
    </div>
</div>
@endsection
