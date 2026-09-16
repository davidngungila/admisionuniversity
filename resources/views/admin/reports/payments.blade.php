@extends('layouts.admin')
@section('title','Payments Report')
@section('content')
<div class="page-head">
    <div><h1>Payments Report</h1><p class="page-sub">Total confirmed revenue: <span style="color:var(--acacia-600);font-weight:800">TZS {{ number_format($total) }}</span></p></div>
    <div class="page-actions"><a href="{{ route('admin.reports.index') }}" class="btn btn-ghost">← Reports Overview</a></div>
</div>

<div class="stat-grid">
    <div class="stat-card">
        <div class="stat-top"><div class="stat-icon ic-green"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg></div><span class="tag tag-green">Confirmed</span></div>
        <div class="stat-label">Total Confirmed Revenue</div><div class="stat-value">TZS {{ number_format($total) }}</div><div class="stat-sub">Across all confirmed payments</div>
    </div>
</div>

<div class="table-card">
    <div class="table-scroll">
        <table>
            <thead><tr><th>Ref</th><th>Applicant</th><th class="right">Amount</th><th class="center">Status</th><th class="center">Date</th></tr></thead>
            <tbody>
                @forelse($payments as $p)
                    <tr>
                        <td><span class="cell-mono">{{ $p->payment_reference }}</span></td>
                        <td><div class="cell-main"><div class="thumb thumb-coffee">{{ substr($p->application->applicant->fullName(),0,1) }}</div><div><div class="cell-title" style="font-size:13px">{{ $p->application->applicant->fullName() }}</div></div></div></td>
                        <td class="right"><strong>TZS {{ number_format($p->amount) }}</strong></td>
                        <td class="center"><span class="tag {{ $p->status==='CONFIRMED' ? 'tag-green' : ($p->status==='FAILED' ? 'tag-red' : 'tag-gold') }}">{{ $p->status }}</span></td>
                        <td class="center"><span class="cell-sub">{{ $p->created_at->format('d M Y') }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="5"><div class="empty-state" style="padding:32px"><div class="es-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg></div><strong>No payments</strong><p>No payment records found.</p></div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if(method_exists($payments,'links'))
        <div style="padding:14px 18px;border-top:1px solid var(--line)">{{ $payments->links() }}</div>
    @endif
</div>
@endsection
