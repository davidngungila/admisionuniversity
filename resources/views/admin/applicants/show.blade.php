@extends('layouts.admin')
@section('title', $applicant->fullName())
@section('content')
<div class="page-head">
    <div><h1>{{ $applicant->fullName() }}</h1><p class="page-sub">{{ $applicant->applicant_number ?? 'No number' }} · {{ $applicant->user->email }} · {{ $applicant->phone }}</p></div>
    <div class="page-actions"><a href="{{ route('admin.applicants.index') }}" class="btn btn-ghost">← Back to Applicants</a></div>
</div>

<div class="panel">
    <div class="panel-head"><div class="panel-title">Applicant Details</div><span class="tag tag-blue">{{ $applicant->applicant_number ?? 'No number' }}</span></div>
    <div class="panel-body">
        <div class="kv">
            <div class="kv-row"><span class="k">Full Name</span><span class="v">{{ $applicant->fullName() }}</span></div>
            <div class="kv-row"><span class="k">Email</span><span class="v">{{ $applicant->user->email }}</span></div>
            <div class="kv-row"><span class="k">Phone</span><span class="v">{{ $applicant->phone ?? '—' }}</span></div>
            <div class="kv-row"><span class="k">Gender</span><span class="v">{{ $applicant->gender ?? '—' }}</span></div>
            <div class="kv-row"><span class="k">Date of Birth</span><span class="v">{{ $applicant->date_of_birth?->format('d M Y') ?? '—' }}</span></div>
            <div class="kv-row"><span class="k">Citizenship</span><span class="v">{{ $applicant->citizenship->name ?? '—' }}</span></div>
            <div class="kv-row"><span class="k">NIDA</span><span class="v mono">{{ $applicant->nida_number ?? '—' }}</span></div>
            <div class="kv-row"><span class="k">Disability</span><span class="v">{{ $applicant->disability_status ? 'Yes ('.$applicant->disability_type.')' : 'No' }}</span></div>
        </div>
        @if($applicant->addresses->count())
            <div style="margin-top:18px">
                <div class="panel-title" style="margin-bottom:10px">Addresses</div>
                <div class="kv">
                    @foreach($applicant->addresses as $addr)
                        <div class="kv-row"><span class="k">{{ $addr->address_type }}</span><span class="v">{{ $addr->fullAddress() }}</span></div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>

<div class="table-card" style="margin-top:18px">
    <div class="panel-head"><div class="panel-title">Applications ({{ $applicant->applications->count() }})</div></div>
    <div class="table-scroll">
        <table>
            <thead><tr><th>No</th><th>Year / Round</th><th class="center">Level</th><th class="center">Status</th><th class="right">Actions</th></tr></thead>
            <tbody>
                @forelse($applicant->applications as $app)
                    <tr>
                        <td><span class="cell-mono">{{ $app->application_number ?? '#'.$app->id }}</span></td>
                        <td><span class="cell-sub">{{ $app->academicYear->name }} · R{{ $app->admissionWindow->applicationRound->round_number }}</span></td>
                        <td class="center"><span class="tag tag-blue">{{ $app->admissionWindow->admissionLevel->name }}</span></td>
                        <td class="center"><span class="tag tag-grey">{{ $app->status }}</span></td>
                        <td class="right"><a href="{{ route('admin.applications.show', encId($app->id)) }}" class="btn btn-ghost btn-sm">View</a></td>
                    </tr>
                @empty
                    <tr><td colspan="5"><div class="empty-state" style="padding:28px"><div class="es-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg></div><strong>No applications</strong><p>This applicant has no applications yet.</p></div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
