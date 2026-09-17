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
            <thead><tr><th>Time</th><th>Template</th><th>Recipient</th><th>Subject</th><th class="center">Status</th><th class="right">Actions</th></tr></thead>
            <tbody>
                @forelse($logs as $l)
                <tr>
                    <td><div class="cell-sub" style="white-space:nowrap">{{ $l->sent_at?->format('d M Y H:i') ?? $l->created_at->format('d M Y H:i') }}</div></td>
                    <td><span class="tag tag-blue">{{ $l->template ?? '—' }}</span></td>
                    <td><span class="cell-mono">{{ $l->recipient }}</span></td>
                    <td style="max-width:300px;min-width:160px"><div class="cell-title" style="font-size:13px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis" title="{{ $l->subject }}">{{ $l->subject ?? '—' }}</div><div class="cell-sub" style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:300px" title="{{ $l->body }}">{{ Str::limit($l->body, 80) }}</div></td>
                    <td class="center"><span class="tag {{ $l->status==='SENT' ? 'tag-green' : 'tag-red' }}">{{ $l->status }}</span></td>
                    <td class="right"><button type="button" class="btn btn-ghost btn-sm" onclick="viewSms({{ $l->id }})">View</button></td>
                </tr>
                @empty<tr><td colspan="6"><div class="empty-state"><div class="es-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg></div><strong>No SMS records</strong><p>No SMS have been sent yet.</p></div></td></tr>@endforelse
            </tbody>
        </table>
    </div>
    @include('layouts.partials.pagination',['paginator'=>$logs])
</div>

<div class="modal-backdrop" id="smsViewBackdrop" onclick="if(event.target===this)closeModal('smsViewBackdrop')">
    <div class="modal" style="max-width:560px;width:92%;">
        <div class="modal-head" style="display:flex;align-items:center;justify-content:space-between;gap:12px;padding:16px 20px;border-bottom:1px solid var(--line);">
            <div style="font-weight:800;color:var(--coffee-900);">SMS — Full Message</div>
            <button type="button" class="btn-icon" onclick="closeModal('smsViewBackdrop')" aria-label="Close"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
        </div>
        <div class="modal-body" style="padding:16px 20px;">
            <div class="kv" style="margin-bottom:14px;">
                <div class="kv-row"><span class="k">Time</span><span class="v" id="smsViewTime">—</span></div>
                <div class="kv-row"><span class="k">Template</span><span class="v" id="smsViewTemplate">—</span></div>
                <div class="kv-row"><span class="k">Recipient</span><span class="v cell-mono" id="smsViewRecipient">—</span></div>
                <div class="kv-row"><span class="k">Subject</span><span class="v" id="smsViewSubject">—</span></div>
                <div class="kv-row"><span class="k">Status</span><span class="v" id="smsViewStatus">—</span></div>
            </div>
            <div style="font-size:11px;font-weight:800;letter-spacing:.06em;text-transform:uppercase;color:var(--ink-soft);margin-bottom:6px;">Message Body</div>
            <div id="smsViewBody" style="padding:12px 14px;border:1.5px solid var(--line);border-radius:10px;background:var(--sand-50);font-size:13px;line-height:1.6;white-space:pre-wrap;word-break:break-word;max-height:260px;overflow:auto;"></div>
        </div>
        <div class="modal-foot" style="display:flex;justify-content:space-between;gap:10px;padding:12px 20px;border-top:1px solid var(--line);">
            <button type="button" class="btn btn-ghost btn-sm" onclick="copySmsBody()">Copy Message</button>
            <button type="button" class="btn btn-primary btn-sm" onclick="closeModal('smsViewBackdrop')">Close</button>
        </div>
    </div>
</div>

<script>
const smsLogsData = @json($logs->getCollection()->keyBy('id')->map(fn($l)=>['time'=>($l->sent_at?->format('d M Y H:i')??$l->created_at->format('d M Y H:i')),'template'=>$l->template,'recipient'=>$l->recipient,'subject'=>$l->subject,'status'=>$l->status,'body'=>$l->body]));
function viewSms(id){
    const d = smsLogsData[id];
    if(!d) return;
    document.getElementById('smsViewTime').textContent = d.time ?? '—';
    document.getElementById('smsViewTemplate').textContent = d.template ?? '—';
    document.getElementById('smsViewRecipient').textContent = d.recipient ?? '—';
    document.getElementById('smsViewSubject').textContent = d.subject ?? '—';
    const st = document.getElementById('smsViewStatus');
    st.textContent = d.status ?? '—';
    st.className = 'tag ' + (d.status==='SENT' ? 'tag-green' : 'tag-red');
    document.getElementById('smsViewBody').textContent = d.body ?? '';
    openModal('smsViewBackdrop');
}
function copySmsBody(){
    const t = document.getElementById('smsViewBody').textContent || '';
    if(!t) return;
    navigator.clipboard.writeText(t).then(()=>toast('Message copied','success')).catch(()=>toast('Copy failed','error'));
}
</script>
@endsection