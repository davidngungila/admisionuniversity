@extends('layouts.admin')
@section('title','Statistics')
@section('content')
<div class="page-head">
    <div><h1>Statistics</h1><p class="page-sub">Breakdown by status, admission level and revenue.</p></div>
    <div class="page-actions"><a href="{{ route('admin.reports.index') }}" class="btn btn-ghost">← Reports Overview</a></div>
</div>

<div class="stat-grid">
    <div class="stat-card">
        <div class="stat-top"><div class="stat-icon ic-green"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg></div><span class="tag tag-green">Revenue</span></div>
        <div class="stat-label">Confirmed Revenue</div><div class="stat-value">TZS {{ number_format($revenue) }}</div><div class="stat-sub">Total from confirmed payments</div>
    </div>
    <div class="stat-card">
        <div class="stat-top"><div class="stat-icon ic-blue"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg></div><span class="tag tag-blue">{{ array_sum($byStatus->toArray()) }} total</span></div>
        <div class="stat-label">Total Applications</div><div class="stat-value">{{ array_sum($byStatus->toArray()) }}</div><div class="stat-sub">Across all statuses</div>
    </div>
</div>

<div class="grid-2">
    <div class="panel">
        <div class="panel-head"><div class="panel-title">By Status</div></div>
        <div class="panel-body">
            @forelse($byStatus as $status=>$total)
                <div class="kv" style="margin-bottom:8px"><div class="kv-row"><span class="k">{{ $status }}</span><span class="v"><span class="tag tag-grey">{{ $total }}</span></span></div></div>
            @empty
                <div class="empty-state" style="padding:20px"><strong>No data</strong><p>No status breakdown available.</p></div>
            @endforelse
        </div>
    </div>
    <div class="panel">
        <div class="panel-head"><div class="panel-title">By Admission Level</div></div>
        <div class="panel-body">
            @forelse($byLevel as $row)
                <div class="kv" style="margin-bottom:8px"><div class="kv-row"><span class="k">{{ $row->level }}</span><span class="v"><span class="tag tag-blue">{{ $row->total }}</span></span></div></div>
            @empty
                <div class="empty-state" style="padding:20px"><strong>No data</strong><p>No level data available.</p></div>
            @endforelse
            <div class="kv" style="margin-top:16px;background:var(--sand-50)">
                <div class="kv-row"><span class="k">Total confirmed revenue</span><span class="v" style="color:var(--acacia-600)">TZS {{ number_format($revenue) }}</span></div>
            </div>
        </div>
    </div>
</div>
@endsection
