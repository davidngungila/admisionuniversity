@extends('layouts.admin')
@section('title','Dashboard')
@section('content')
<div class="page-head">
    <div><h1>Dashboard</h1><p class="page-sub">Overview of admissions, revenue and windows.</p></div>
    <span class="tag tag-green">Live</span>
</div>
<div class="stat-grid">
    <div class="stat-card">
        <div class="stat-top"><div class="stat-icon ic-blue"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg></div><span class="tag tag-blue">{{ $stats['submitted'] }} submitted</span></div>
        <div class="stat-label">Applications</div><div class="stat-value">{{ number_format($stats['applications']) }}</div><div class="stat-sub">{{ $stats['submitted'] }} submitted overall</div>
    </div>
    <div class="stat-card">
        <div class="stat-top"><div class="stat-icon ic-green"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg></div><span class="tag tag-green">{{ $stats['under_review'] }} under review</span></div>
        <div class="stat-label">Selected / Admitted</div><div class="stat-value" style="color:var(--acacia-600)">{{ number_format($stats['selected']) }}</div><div class="stat-sub">{{ $stats['under_review'] }} under review</div>
    </div>
    <div class="stat-card">
        <div class="stat-top"><div class="stat-icon ic-gold"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg></div><span class="tag tag-gold">{{ $stats['payments_confirmed'] }} payments</span></div>
        <div class="stat-label">Revenue (Confirmed)</div><div class="stat-value">TZS {{ number_format($stats['revenue']) }}</div><div class="stat-sub">{{ $stats['payments_confirmed'] }} confirmed payments</div>
    </div>
    <div class="stat-card">
        <div class="stat-top"><div class="stat-icon ic-terracotta"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/></svg></div><span class="tag tag-terracotta">{{ $stats['programmes'] }} programmes</span></div>
        <div class="stat-label">Open Windows</div><div class="stat-value">{{ $stats['open_windows'] }}</div><div class="stat-sub">{{ $stats['programmes'] }} programmes available</div>
    </div>
</div>
<div style="display:grid;grid-template-columns:repeat(2,1fr);gap:16px">
    <div class="table-card">
        <div class="panel-head"><div class="panel-title">Recent Applications</div><a href="{{ route('admin.applications.index') }}" class="btn btn-ghost btn-sm">View all</a></div>
        <div class="table-scroll">
            <table>
                <thead><tr><th>No</th><th>Applicant</th><th class="center">Level</th><th class="center">Status</th></tr></thead>
                <tbody>
                    @forelse($recentApplications as $a)<tr><td><span class="cell-mono">{{ $a->application_number ?? '#'.$a->id }}</span></td><td><div class="cell-main"><div class="thumb thumb-coffee">{{ substr($a->applicant->fullName(),0,1) }}</div><div><div class="cell-title" style="font-size:13px">{{ $a->applicant->fullName() }}</div><div class="cell-sub">{{ $a->admissionWindow->admissionLevel->short_name }}</div></div></div></td><td class="center"><span class="tag tag-blue">{{ $a->admissionWindow->admissionLevel->short_name }}</span></td><td class="center"><span class="tag tag-grey">{{ $a->status }}</span></td></tr>@empty<tr><td colspan="4"><div class="empty-state" style="padding:24px"><strong>No applications.</strong></div></td></tr>@endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="table-card">
        <div class="panel-head"><div class="panel-title">Admission Windows</div><a href="{{ route('admin.windows.index') }}" class="btn btn-ghost btn-sm">Manage</a></div>
        <div class="table-scroll">
            <table>
                <thead><tr><th>Level</th><th class="center">Round</th><th class="right">Apps</th><th class="center">Deadline</th></tr></thead>
                <tbody>
                    @forelse($windowStats as $w)<tr><td><div class="cell-title" style="font-size:13px">{{ $w->admissionLevel->name }}</div></td><td class="center"><span class="tag tag-gold">R{{ $w->applicationRound->round_number }}</span></td><td class="right"><span class="tag tag-blue">{{ $w->applications_count }}</span></td><td class="center"><span class="cell-sub">{{ $w->closes_at->format('d M Y') }}</span></td></tr>@empty<tr><td colspan="4"><div class="empty-state" style="padding:24px"><strong>No windows.</strong></div></td></tr>@endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
<style>@media(max-width:900px){ div[style*="grid-template-columns:repeat(2,1fr)"]{grid-template-columns:1fr !important} }</style>
@endsection
