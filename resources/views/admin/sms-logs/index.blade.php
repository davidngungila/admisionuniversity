@extends('layouts.admin')
@section('title','SMS Logs')
@section('content')
<div class="page-head">
    <div><h1>SMS Logs</h1><p class="page-sub">Every SMS the system sends — application submitted, selected, admitted and confirmation codes.</p></div>
</div>
<form method="GET" class="panel" style="padding:14px 16px;display:flex;gap:10px;align-items:center;margin-bottom:16px;flex-wrap:wrap">
    <div class="table-search" style="flex:1;min-width:220px;position:relative">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="position:absolute;left:14px;top:50%;transform:translateY(-50%);width:15px;height:15px;opacity:.6"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input name="search" type="text" value="{{ request('search') }}" placeholder="Search recipient, template, subject…" style="width:100%;padding:10px 12px 10px 38px;border:1.5px solid var(--line);border-radius:10px;background:#fff;font-size:13px;font-family:inherit;">
    </div>
    <select name="status" style="padding:10px 12px;border:1.5px solid var(--line);border-radius:10px;background:#fff;font-size:13px">
        <option value="">All statuses</option>
        @foreach(['SENT','FAILED'] as $st)<option value="{{ $st }}" @selected(request('status')==$st)>{{ $st }}</option>@endforeach
    </select>
    <button class="btn btn-ghost btn-sm">Filter</button>
    @if(request('search') || request('status'))<a href="{{ route('admin.sms-logs.index') }}" class="btn btn-ghost btn-sm">Clear</a>@endif
</form>
<div class="table-card">
    <div class="table-toolbar">
        <div class="chip-filters"><button class="chip active">Auto-recorded</button></div>
        <div class="cell-sub">Channel <strong>sms</strong> · Provider {{ \App\Models\Setting::getValue('sms_provider','log') }} @if(\App\Models\Setting::getValue('sms_enabled')) · Enabled @else · Disabled @endif</div>
    </div>
    <div class="table-scroll">
        <table>
            <thead><tr><th>Time</th><th>Template</th><th>Recipient</th><th>Subject</th><th class="center">Status</th></tr></thead>
            <tbody>
                @forelse($logs as $l)
                <tr>
                    <td><div class="cell-sub" style="white-space:nowrap">{{ $l->sent_at?->format('d M Y H:i') ?? $l->created_at->format('d M Y H:i') }}</div></td>
                    <td><span class="tag tag-blue">{{ $l->template ?? '—' }}</span></td>
                    <td><span class="cell-mono">{{ $l->recipient }}</span></td>
                    <td style="max-width:300px;min-width:160px"><div class="cell-title" style="font-size:13px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis" title="{{ $l->subject }}">{{ $l->subject ?? '—' }}</div><div class="cell-sub" style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:300px" title="{{ $l->body }}">{{ $l->body }}</div></td>
                    <td class="center"><span class="tag {{ $l->status==='SENT' ? 'tag-green' : 'tag-red' }}">{{ $l->status }}</span></td>
                </tr>
                @empty<tr><td colspan="5"><div class="empty-state"><div class="es-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg></div><strong>No SMS records</strong><p>No SMS have been sent yet.</p></div></td></tr>@endforelse
            </tbody>
        </table>
    </div>
    @include('layouts.partials.pagination',['paginator'=>$logs])
</div>
@endsection