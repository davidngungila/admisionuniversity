@extends('layouts.admin')
@section('title','Reports')
@section('content')
<div class="page-head">
    <div><h1>Reports</h1><p class="page-sub">Application, finance and programme reports — live numbers.</p></div>
</div>
<div class="stat-grid" style="grid-template-columns:repeat(auto-fit,minmax(200px,1fr))">
    <a href="{{ route('admin.reports.applications') }}" class="stat-card" style="text-decoration:none">
        <div class="stat-top"><div class="stat-icon ic-terracotta"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg></div><span class="tag tag-terracotta">Report</span></div>
        <div class="stat-label">Applications Report</div>
        <div class="stat-value">{{ $s['applications'] }}</div>
        <div class="stat-sub">{{ $s['applications_active'] }} active · {{ $s['applications_submitted'] }} submitted</div>
    </a>
    <a href="{{ route('admin.reports.payments') }}" class="stat-card" style="text-decoration:none">
        <div class="stat-top"><div class="stat-icon ic-green"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg></div><span class="tag tag-green">Finance</span></div>
        <div class="stat-label">Payments Report</div>
        <div class="stat-value">TZS {{ number_format($s['revenue']) }}</div>
        <div class="stat-sub">{{ $s['payments_confirmed'] }} confirmed · {{ $s['payments_pending'] }} pending · {{ $s['payments_free'] }} free</div>
    </a>
    <a href="{{ route('admin.reports.programmes') }}" class="stat-card" style="text-decoration:none">
        <div class="stat-top"><div class="stat-icon ic-blue"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg></div><span class="tag tag-blue">Programmes</span></div>
        <div class="stat-label">Programmes Report</div>
        <div class="stat-value">{{ $s['programmes'] }}</div>
        <div class="stat-sub">{{ $s['windows'] }} admission windows</div>
    </a>
    <a href="{{ route('admin.reports.statistics') }}" class="stat-card" style="text-decoration:none">
        <div class="stat-top"><div class="stat-icon ic-gold"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg></div><span class="tag tag-gold">Analytics</span></div>
        <div class="stat-label">All Logins</div>
        <div class="stat-value">{{ $s['logins'] }}</div>
        <div class="stat-sub">{{ $s['logins_today'] }} today · {{ $s['logins_unique'] }} unique users · {{ $s['logins_failed'] }} failed</div>
    </a>
    <a href="{{ route('admin.reports.statistics') }}" class="stat-card" style="text-decoration:none">
        <div class="stat-top"><div class="stat-icon ic-gold"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg></div><span class="tag tag-gold">Analytics</span></div>
        <div class="stat-label">Statistics</div>
        <div class="stat-value">{{ $s['selected'] }}</div>
        <div class="stat-sub">{{ $s['admitted'] }} admitted · {{ $s['applicants'] }} applicants · {{ $s['users'] }} users</div>
    </a>
</div>

<div class="panel" style="margin-top:18px">
    <div class="panel-head"><div class="panel-title">Summary Snapshot</div><span class="tag tag-terracotta">Today {{ now()->format('d M Y') }}</span></div>
    <div class="panel-body" style="padding:0">
        <div class="table-scroll">
            <table>
                <thead><tr><th>Metric</th><th class="right">Value</th></tr></thead>
                <tbody>
                    <tr><td>Total applications</td><td class="right"><b>{{ $s['applications'] }}</b></td></tr>
                    <tr><td>Active / in-progress applications</td><td class="right"><b>{{ $s['applications_active'] }}</b></td></tr>
                    <tr><td>Submitted applications (full pipeline)</td><td class="right"><b>{{ $s['applications_submitted'] }}</b></td></tr>
                    <tr><td>Selected + admitted</td><td class="right"><b>{{ $s['selected'] }}</b> <span class="cell-sub">({{ $s['admitted'] }} admitted)</span></td></tr>
                    <tr><td>Total payments</td><td class="right"><b>{{ $s['payments'] }}</b> <span class="cell-sub">({{ $s['payments_confirmed'] }} confirmed, {{ $s['payments_pending'] }} pending, {{ $s['payments_free'] }} free)</span></td></tr>
                    <tr><td>Control numbers issued (awaiting confirmation)</td><td class="right"><b>{{ $s['issued_control'] }}</b></td></tr>
                    <tr><td>Confirmed revenue</td><td class="right"><b style="color:var(--acacia-600)">TZS {{ number_format($s['revenue']) }}</b></td></tr>
                    <tr><td>Programmes</td><td class="right"><b>{{ $s['programmes'] }}</b></td></tr>
                    <tr><td>Admission windows</td><td class="right"><b>{{ $s['windows'] }}</b></td></tr>
                    <tr><td>Applicant profiles</td><td class="right"><b>{{ $s['applicants'] }}</b></td></tr>
                    <tr><td>Registered users</td><td class="right"><b>{{ $s['users'] }}</b></td></tr>
                    <tr><td>Successful logins (all time)</td><td class="right"><b>{{ $s['logins'] }}</b> <span class="cell-sub">({{ $s['logins_today'] }} today, {{ $s['logins_unique'] }} unique users)</span></td></tr>
                    <tr><td>Failed login attempts</td><td class="right"><b style="color:var(--danger)">{{ $s['logins_failed'] }}</b> <span class="cell-sub">({{ $s['logins_failed_today'] }} today)</span></td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="panel" style="margin-top:18px">
    <div class="panel-head"><div class="panel-title">All Logins — Recent Activity</div><span class="tag {{ $s['logins_failed_today'] ? 'tag-red' : 'tag-green' }}">{{ $s['logins_failed_today'] }} failed today</span></div>
    <div class="panel-body" style="padding:0">
        <div class="table-scroll">
            <table>
                <thead><tr><th>Time</th><th>User</th><th class="center">Result</th><th>IP</th></tr></thead>
                <tbody>
                    @forelse($recentLogins as $l)
                    <tr>
                        <td><div class="cell-sub" style="white-space:nowrap">{{ $l->created_at->format('d M Y H:i') }}</div></td>
                        <td><div class="cell-title" style="font-size:13px">{{ $l->user->name ?? ($l->after['email'] ?? 'Unknown') }}</div></td>
                        <td class="center"><span class="tag {{ $l->action==='Login Failed' ? 'tag-red' : 'tag-green' }}">{{ $l->action }}</span></td>
                        <td><span class="cell-mono">{{ $l->ip_address ?? '—' }}</span></td>
                    </tr>
                    @empty<tr><td colspan="4"><div class="empty-state"><div class="es-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></div><strong>No logins</strong><p>No login activity recorded yet.</p></div></td></tr>@endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection