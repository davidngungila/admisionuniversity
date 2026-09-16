@extends('layouts.admin')
@section('title','Audit Logs')
@section('content')
<div class="page-head">
    <div><h1>Audit Logs</h1><p class="page-sub">Every state-changing action, fully recorded.</p></div>
</div>
<form method="GET" class="panel" style="padding:14px 16px;display:flex;gap:10px;align-items:center;margin-bottom:16px;flex-wrap:wrap">
    <div class="table-search" style="flex:1;min-width:220px;position:relative">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="position:absolute;left:14px;top:50%;transform:translateY(-50%);width:15px;height:15px;opacity:.6"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input name="q" type="text" value="{{ request('q') }}" placeholder="Search action, user, IP, model…" style="width:100%;padding:10px 12px 10px 38px;border:1.5px solid var(--line);border-radius:10px;background:#fff;font-size:13px;font-family:inherit;">
    </div>
    <select name="method" style="padding:10px 12px;border:1.5px solid var(--line);border-radius:10px;background:#fff;font-size:13px">
        <option value="">All methods</option>
        @foreach(['POST','PUT','PATCH','DELETE'] as $m)<option value="{{ $m }}" @selected(request('method')==$m)>{{ $m }}</option>@endforeach
    </select>
    <button class="btn btn-ghost btn-sm">Filter</button>
    @if(request('q') || request('method'))<a href="{{ route('admin.audit-logs.index') }}" class="btn btn-ghost btn-sm">Clear</a>@endif
</form>
<div class="table-card">
    <div class="table-toolbar">
        <div class="chip-filters">
            <button class="chip active">Auto-recorded</button>
        </div>
        <div class="cell-sub">All requests by logged-in users are recorded; guest GET/HEAD/OPTIONS are skipped.</div>
    </div>
    <div class="table-scroll">
        <table>
            <thead><tr><th>Time</th><th>User</th><th class="center">Method</th><th>Model</th><th class="center">Status</th><th>IP</th><th class="right">Details</th></tr></thead>
            <tbody>
                @forelse($logs as $l)
                <tr>
                    <td><div class="cell-sub" style="white-space:nowrap">{{ $l->created_at->format('d M Y H:i') }}</div></td>
                    <td><div class="cell-title" style="font-size:13px">{{ $l->user->name ?? 'System' }}</div></td>
                    <td class="center"><span class="tag {{ $l->method==='DELETE' ? 'tag-red' : ($l->method==='POST' ? 'tag-green' : 'tag-gold') }}">{{ $l->method ?? '—' }}</span></td>
                    <td><span class="tag tag-grey">{{ $l->model_type ? class_basename($l->model_type).' #'.$l->model_id : '—' }}</span></td>
                    <td class="center"><span class="tag {{ ($l->response_status ?? 0) < 300 ? 'tag-green' : 'tag-red' }}">{{ $l->response_status ?? '—' }}</span></td>
                    <td><span class="cell-mono">{{ $l->ip_address ?? '—' }}</span></td>
                    <td class="right"><div class="row-actions"><button type="button" class="btn btn-ghost btn-sm" style="padding:6px 10px;font-size:12px" onclick="showLogDetail({{ preg_replace('/\s+/',' ',json_encode(['id'=>$l->id,'time'=>$l->created_at->format('d M Y H:i:s'),'user'=>$l->user->name ?? 'System','method'=>$l->method,'action'=>$l->action,'url'=>$l->url,'status'=>$l->response_status,'model'=>$l->model_type ? class_basename($l->model_type).' ('.$l->model_type.') #'.$l->model_id : null,'ip'=>$l->ip_address,'ua'=>$l->user_agent,'before'=>$l->before,'after'=>$l->after], JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP)) }})"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex:none"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg> View</button></div></td>
                </tr>
                @empty<tr><td colspan="7"><div class="empty-state"><div class="es-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/></svg></div><strong>No logs</strong><p>No audit records match your filter.</p></div></td></tr>@endforelse
            </tbody>
        </table>
    </div>
    @include('layouts.partials.pagination',['paginator'=>$logs])
</div>

{{-- Full record detail modal --}}
<div class="modal-backdrop" id="logDetailModal" onclick="if(event.target===this)closeModal('logDetailModal')">
    <div class="modal" style="max-width:760px;">
        <div class="modal-head"><div><h3>Audit Record</h3><p id="ldTime"></p></div><button class="modal-close" onclick="closeModal('logDetailModal')">×</button></div>
        <div class="modal-body" style="max-height:70vh;overflow:auto;">
            <div class="kv" style="margin-bottom:14px;">
                <div class="kv-row"><span class="k">User</span><span class="v" id="ldUser"></span></div>
                <div class="kv-row"><span class="k">Method</span><span class="v" id="ldMethod"></span></div>
                <div class="kv-row"><span class="k">Action</span><span class="v" id="ldAction" style="font-size:13px"></span></div>
                <div class="kv-row"><span class="k">URL</span><span class="v" id="ldUrl" style="word-break:break-all;font-size:12.5px"></span></div>
                <div class="kv-row"><span class="k">Status</span><span class="v" id="ldStatus"></span></div>
                <div class="kv-row"><span class="k">Model</span><span class="v" id="ldModel"></span></div>
                <div class="kv-row"><span class="k">IP</span><span class="v" id="ldIp"></span></div>
                <div class="kv-row"><span class="k">User Agent</span><span class="v" id="ldUa" style="word-break:break-all;font-size:12.5px"></span></div>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div style="border:1px solid var(--line);border-radius:10px;overflow:hidden;">
                    <div style="padding:8px 12px;font-size:11px;font-weight:800;letter-spacing:.06em;text-transform:uppercase;background:var(--sand-100);color:var(--coffee-700);border-bottom:1px solid var(--line);">Before</div>
                    <pre id="ldBefore" style="margin:0;padding:12px;font-size:11.5px;line-height:1.5;white-space:pre-wrap;word-break:break-all;max-height:220px;overflow:auto;background:#fff;"></pre>
                </div>
                <div style="border:1px solid var(--line);border-radius:10px;overflow:hidden;">
                    <div style="padding:8px 12px;font-size:11px;font-weight:800;letter-spacing:.06em;text-transform:uppercase;background:var(--sand-100);color:var(--coffee-700);border-bottom:1px solid var(--line);">After</div>
                    <pre id="ldAfter" style="margin:0;padding:12px;font-size:11.5px;line-height:1.5;white-space:pre-wrap;word-break:break-all;max-height:220px;overflow:auto;background:#fff;"></pre>
                </div>
            </div>
        </div>
        <div class="modal-foot" style="padding:12px 16px;border-top:1px solid var(--line);display:flex;justify-content:flex-end;"><button class="btn btn-ghost btn-sm" onclick="closeModal('logDetailModal')">Close</button></div>
    </div>
</div>
<style>@media(max-width:900px){div[style*="grid-template-columns:1fr 1fr"]{grid-template-columns:1fr !important}}</style>
<script>
function showLogDetail(d){
    document.getElementById('ldTime').textContent = d.time;
    document.getElementById('ldUser').textContent = d.user || 'System';
    document.getElementById('ldMethod').textContent = d.method || '—';
    document.getElementById('ldAction').textContent = d.action || '—';
    document.getElementById('ldUrl').textContent = d.url || '—';
    document.getElementById('ldStatus').textContent = d.status == null ? '—' : d.status;
    document.getElementById('ldModel').textContent = d.model || '—';
    document.getElementById('ldIp').textContent = d.ip || '—';
    document.getElementById('ldUa').textContent = d.ua || '—';
    document.getElementById('ldBefore').textContent = d.before ? JSON.stringify(d.before, null, 2) : '—';
    document.getElementById('ldAfter').textContent = d.after ? JSON.stringify(d.after, null, 2) : '—';
    openModal('logDetailModal');
}
</script>
@endsection