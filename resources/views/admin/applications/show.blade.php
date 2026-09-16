@extends('layouts.admin')
@section('title','Application '.$application->application_number)
@section('content')
<div class="page-head">
    <div><h1>{{ $application->application_number ?? 'Draft #'.$application->id }}</h1><p class="page-sub">{{ $application->academicYear->name }} · Round {{ $application->admissionWindow->applicationRound->round_number }} · {{ $application->admissionWindow->admissionLevel->name }} · {{ $application->admissionWindow->applicant_category }}</p></div>
    <div class="page-actions">
        <a href="{{ route('admin.applications.index') }}" class="btn btn-ghost">← Back to Applications</a>
        <span class="tag {{ $application->status==='SELECTED' || $application->status==='ADMITTED' ? 'tag-green' : ($application->status==='REJECTED' ? 'tag-red' : 'tag-grey') }}">{{ $application->status }}</span>
    </div>
</div>

<div class="panel">
    <div class="panel-head"><div class="panel-title">Application Progress</div><span class="tag tag-blue">{{ $application->progress_percentage }}%</span></div>
    <div class="panel-body">
        <div style="height:8px;background:var(--sand-100);border-radius:20px;overflow:hidden;border:1px solid var(--line)"><div style="height:100%;background:var(--terracotta-600);width: {{ $application->progress_percentage }}%"></div></div>
        <div class="muted" style="font-size:12px;margin-top:8px">{{ $application->progress_percentage }}% — {{ $application->current_step ?? '—' }}</div>
    </div>
</div>

<div class="grid-2" style="margin-top:18px">
    <div class="panel">
        <div class="panel-head"><div class="panel-title">Applicant</div></div>
        <div class="panel-body">
            <div class="kv">
                <div class="kv-row"><span class="k">Name</span><span class="v">{{ $application->applicant->fullName() }}</span></div>
                <div class="kv-row"><span class="k">Email</span><span class="v">{{ $application->applicant->email }}</span></div>
                <div class="kv-row"><span class="k">Phone</span><span class="v">{{ $application->applicant->phone ?? '—' }}</span></div>
                <div class="kv-row"><span class="k">Gender</span><span class="v">{{ $application->applicant->gender ?? '—' }}</span></div>
                <div class="kv-row"><span class="k">DOB</span><span class="v">{{ $application->applicant->date_of_birth?->format('d M Y') ?? '—' }}</span></div>
            </div>
        </div>
    </div>
    <div class="panel">
        <div class="panel-head"><div class="panel-title">Programmes</div></div>
        <div class="panel-body">
            @forelse($application->selectedProgrammes->sortBy('preference_order') as $ap)
                <div class="kv" style="margin-bottom:8px"><div class="kv-row"><span class="k">#{{ $ap->preference_order }}</span><span class="v">{{ $ap->programme->name }} <span class="muted">({{ $ap->programme->code }})</span></span></div></div>
            @empty
                <div class="empty-state" style="padding:20px"><strong>No programmes</strong><p>No programmes selected.</p></div>
            @endforelse
        </div>
    </div>
</div>

<div class="grid-2" style="margin-top:18px">
    <div class="panel">
        <div class="panel-head"><div class="panel-title">Payments</div></div>
        <div class="panel-body">
            @forelse($application->payments as $p)
                <div class="kv" style="margin-bottom:8px"><div class="kv-row"><span class="k mono">{{ $p->payment_reference }}</span><span class="v"><span class="tag {{ $p->status==='CONFIRMED' ? 'tag-green' : 'tag-gold' }}">{{ $p->status }}</span></span></div></div>
            @empty
                <div class="empty-state" style="padding:20px"><strong>No payments</strong><p>No payment records.</p></div>
            @endforelse
        </div>
    </div>
    <div class="panel">
        <div class="panel-head"><div class="panel-title">Results</div></div>
        <div class="panel-body">
            @forelse($application->academicResults as $r)
                <div class="kv" style="margin-bottom:8px"><div class="kv-row"><span class="k">{{ $r->exam_type }} {{ $r->index_number }} ({{ $r->exam_year }})</span><span class="v"><span class="tag {{ $r->is_verified ? 'tag-green' : 'tag-grey' }}">{{ $r->is_verified ? 'Auto-verified · '.($r->exam_body ?? 'External') : 'Pending verification' }}</span> · {{ count($r->results ?? []) }} subjects</span></div></div>
            @empty
                <div class="empty-state" style="padding:20px"><strong>No results</strong><p>No academic results submitted.</p></div>
            @endforelse
        </div>
    </div>
</div>

<div class="panel" style="margin-top:18px">
    <div class="panel-head"><div class="panel-title">Documents ({{ $application->documents->count() }})</div></div>
    <div class="panel-body">
        @forelse($application->documents as $d)
            <div class="kv" style="margin-bottom:8px"><div class="kv-row"><span class="k">{{ $d->document_type }} — {{ $d->file_name }}</span><span class="v"><span class="tag {{ $d->is_verified ? 'tag-green' : 'tag-grey' }}">{{ $d->is_verified ? 'Verified' : 'Pending' }}</span></span></div></div>
        @empty
            <div class="empty-state" style="padding:20px"><strong>No documents</strong><p>No documents uploaded.</p></div>
        @endforelse
    </div>
</div>

<div class="panel" style="margin-top:18px">
    <div class="panel-head"><div class="panel-title">Update Status</div></div>
    <div class="panel-body">
        <form method="POST" action="{{ route('admin.applications.status', encId($application->id)) }}" onsubmit="event.preventDefault(); const _f=this; confirmModal('Update status','Are you sure you want to update this application status?',()=>_f.submit())">
            @csrf @method('PATCH')
            <div class="form-grid">
                <div class="field">
                    <label class="field-label">Status *</label>
                    <select name="status" required>
                        <option value="UNDER_REVIEW">UNDER_REVIEW</option>
                        <option value="ELIGIBILITY_CHECKED">ELIGIBILITY_CHECKED</option>
                        <option value="ELIGIBLE">ELIGIBLE</option>
                        <option value="SELECTED">SELECTED</option>
                        <option value="ADMITTED">ADMITTED</option>
                        <option value="REJECTED">REJECTED</option>
                        <option value="WAITLISTED">WAITLISTED</option>
                        <option value="INELIGIBLE">INELIGIBLE</option>
                    </select>
                </div>
                <div class="field">
                    <label class="field-label">Remarks (optional)</label>
                    <input name="remarks" placeholder="Remarks (optional)" value="{{ old('remarks') }}">
                </div>
            </div>
            <div class="form-actions" style="justify-content:flex-start">
                <button class="btn btn-primary">Update Status</button>
            </div>
        </form>
    </div>
</div>

<div class="panel" style="margin-top:18px">
    <div class="panel-head"><div class="panel-title">Status History</div></div>
    <div class="panel-body">
        @forelse($application->statusHistory as $h)
            <div class="kv" style="margin-bottom:8px"><div class="kv-row"><span class="k">{{ $h->old_status ?? '—' }} → <strong>{{ $h->new_status }}</strong> @if($h->remarks) — {{ $h->remarks }} @endif</span><span class="v muted">{{ $h->created_at->format('d M Y H:i') }}</span></div></div>
        @empty
            <div class="empty-state" style="padding:20px"><strong>No history</strong><p>No status changes recorded.</p></div>
        @endforelse
    </div>
</div>
@endsection
