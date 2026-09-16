@extends('layouts.admin')
@section('title','Applications Report')
@section('content')
<div class="page-head">
    <div><h1>Applications Report</h1><p class="page-sub">Filter and review applications by admission window.</p></div>
    <div class="page-actions"><a href="{{ route('admin.reports.index') }}" class="btn btn-ghost">← Reports Overview</a></div>
</div>

<div class="panel">
    <div class="panel-body">
        <form method="GET" class="form-row">
            <div class="field" style="min-width:240px;flex:1;max-width:360px">
                <label class="field-label">Admission Window</label>
                <select name="window">
                    <option value="">All windows</option>
                    @foreach($windows as $w)<option value="{{ $w->id }}" @selected(request('window')==$w->id)>{{ $w->admissionLevel->name }} R{{ $w->applicationRound->round_number }}</option>@endforeach
                </select>
            </div>
            <div style="display:flex;align-items:flex-end"><button class="btn btn-primary">Filter</button></div>
        </form>
    </div>
</div>

<div class="table-card" style="margin-top:18px">
    <div class="table-scroll">
        <table>
            <thead><tr><th>No</th><th>Applicant</th><th class="center">Year / Round</th><th class="center">Status</th></tr></thead>
            <tbody>
                @forelse($applications as $a)
                    <tr>
                        <td><span class="cell-mono">{{ $a->application_number ?? '#'.$a->id }}</span></td>
                        <td><div class="cell-main"><div class="thumb thumb-coffee">{{ substr($a->applicant->fullName(),0,1) }}</div><div><div class="cell-title" style="font-size:13px">{{ $a->applicant->fullName() }}</div></div></div></td>
                        <td class="center"><span class="tag tag-blue">{{ $a->academicYear->name }} R{{ $a->admissionWindow->applicationRound->round_number }}</span></td>
                        <td class="center"><span class="tag tag-grey">{{ $a->status }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="4"><div class="empty-state" style="padding:32px"><div class="es-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg></div><strong>No applications</strong><p>No applications found for the selected filter.</p></div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if(method_exists($applications,'links'))
        <div style="padding:14px 18px;border-top:1px solid var(--line)">{{ $applications->withQueryString()->links() }}</div>
    @endif
</div>
@endsection
