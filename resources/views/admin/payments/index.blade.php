@extends('layouts.admin')
@section('title','Payments')
@section('content')
<div class="page-head">
    <div><h1>Payments</h1><p class="page-sub">Application fee payments by status.</p></div>
</div>
<form method="GET" class="panel" style="padding:14px 16px;display:flex;gap:10px;align-items:center;margin-bottom:16px">
    <select name="status" style="padding:10px 12px;border:1.5px solid var(--line);border-radius:10px;background:#fff;font-size:13px"><option value="">All statuses</option><option value="PENDING" @selected(request('status')=='PENDING')>PENDING</option><option value="CONFIRMED" @selected(request('status')=='CONFIRMED')>CONFIRMED</option><option value="FAILED" @selected(request('status')=='FAILED')>FAILED</option></select>
    <button class="btn btn-ghost btn-sm">Filter</button>
    @if(request('status'))<a href="{{ route('admin.payments.index') }}" class="btn btn-ghost btn-sm">Clear</a>@endif
</form>
<div class="table-card">
    <div class="table-toolbar">
        <div class="chip-filters">
            <button class="chip active" data-filter="all">All</button>
            <button class="chip" data-filter="confirmed">Confirmed</button>
            <button class="chip" data-filter="pending">Pending</button>
            <button class="chip" data-filter="failed">Failed</button>
        </div>
        <div class="table-search">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input id="tableSearch" type="text" placeholder="Search payments…">
        </div>
    </div>
    <div class="table-scroll">
        <table>
            <thead><tr><th>Reference</th><th>Applicant</th><th class="right">Amount</th><th class="center">Method</th><th class="center">Status</th><th class="right">Actions</th></tr></thead>
            <tbody>
                @forelse($payments as $p)
                <tr data-filter="{{ strtolower($p->status) }}">
                    <td><span class="cell-mono">{{ $p->payment_reference }}</span></td>
                    <td><div class="cell-main"><div class="thumb thumb-gold">{{ substr($p->application->applicant->fullName(),0,1) }}</div><div><div class="cell-title" style="font-size:13px">{{ $p->application->applicant->fullName() }}</div><div class="cell-sub">{{ $p->application->application_number ?? '#'.$p->application->id }}</div></div></div></td>
                    <td class="right"><span class="tag tag-gold">{{ $p->currency }} {{ number_format($p->amount) }}</span></td>
                    <td class="center"><span class="tag tag-grey">{{ $p->payment_method }}</span></td>
                    <td class="center">@if($p->status==='CONFIRMED')<span class="tag tag-green">Confirmed</span>@elseif($p->status==='PENDING')<span class="tag tag-gold">Pending</span>@else<span class="tag tag-red">Failed</span>@endif</td>
                    <td class="right"><div class="row-actions"><a href="{{ route('admin.payments.show', encId($p->id)) }}" class="btn btn-ghost btn-sm">View</a></div></td>
                </tr>
                @empty<tr><td colspan="6"><div class="empty-state"><div class="es-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg></div><strong>No payments</strong><p>No matching records.</p></div></td></tr>@endforelse
            </tbody>
        </table>
    </div>
    @include('layouts.partials.pagination',['paginator'=>$payments])
</div>
@endsection
