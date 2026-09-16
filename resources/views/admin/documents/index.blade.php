@extends('layouts.admin')
@section('title','Documents')
@section('content')
<div class="page-head">
    <div><h1>Documents</h1><p class="page-sub">Verify applicant uploaded documents.</p></div>
</div>
<form method="GET" class="panel" style="padding:14px 16px;display:flex;gap:10px;align-items:center;margin-bottom:16px">
    <select name="verified" style="padding:10px 12px;border:1.5px solid var(--line);border-radius:10px;background:#fff;font-size:13px"><option value="">All</option><option value="yes" @selected(request('verified')=='yes')>Verified</option><option value="no" @selected(request('verified')=='no')>Pending</option></select>
    <button class="btn btn-ghost btn-sm">Filter</button>
    @if(request('verified'))<a href="{{ route('admin.documents.index') }}" class="btn btn-ghost btn-sm">Clear</a>@endif
</form>
<div class="table-card">
    <div class="table-toolbar">
        <div class="chip-filters">
            <button class="chip active" data-filter="all">All</button>
            <button class="chip" data-filter="verified">Verified</button>
            <button class="chip" data-filter="pending">Pending</button>
        </div>
        <div class="table-search">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input id="tableSearch" type="text" placeholder="Search documents…">
        </div>
    </div>
    <div class="table-scroll">
        <table>
            <thead><tr><th>Applicant</th><th class="center">Type</th><th>File</th><th class="center">Verified</th><th class="right">Actions</th></tr></thead>
            <tbody>
                @forelse($documents as $d)
                <tr data-filter="{{ $d->is_verified ? 'verified' : 'pending' }}">
                    <td><div class="cell-main"><div class="thumb thumb-coffee">{{ substr($d->application->applicant->fullName(),0,1) }}</div><div><div class="cell-title" style="font-size:13px">{{ $d->application->applicant->fullName() }}</div><div class="cell-sub">{{ $d->application->application_number ?? '#'.$d->application->id }}</div></div></div></td>
                    <td class="center"><span class="tag tag-blue">{{ $d->document_type }}</span></td>
                    <td><div class="cell-sub">{{ $d->file_name }}</div></td>
                    <td class="center">@if($d->is_verified)<span class="tag tag-green">Verified</span>@else<span class="tag tag-gold">Pending</span>@endif</td>
                    <td class="right"><div class="row-actions" style="gap:6px">
                        <button type="button" class="btn btn-ghost btn-sm" style="padding:6px 10px;font-size:12px" onclick="previewDoc('{{ route('admin.documents.preview', encId($d->id)) }}','{{ addslashes($d->file_name) }}','{{ $d->mime_type }}')"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex:none"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg> Preview</button>
                        <form method="POST" action="{{ route('admin.documents.verify', encId($d->id)) }}" onsubmit="event.preventDefault(); const _f=this; confirmModal('Verify document','Are you sure you want to verify this document?',()=>_f.submit())">@csrf<input type="hidden" name="is_verified" value="1"><button type="submit" class="btn btn-soft btn-sm" style="padding:6px 10px;font-size:12px">Verify</button></form>
                        <form method="POST" action="{{ route('admin.documents.verify', encId($d->id)) }}" onsubmit="event.preventDefault(); const _f=this; confirmModal('Unverify document','Are you sure you want to unverify this document?',()=>_f.submit())">@csrf<input type="hidden" name="is_verified" value="0"><button type="submit" class="btn btn-ghost btn-sm" style="padding:6px 10px;font-size:12px">Unverify</button></form>
                    </div></td>
                </tr>
                @empty<tr><td colspan="5"><div class="empty-state"><div class="es-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg></div><strong>No documents</strong><p>No matching records.</p></div></td></tr>@endforelse
            </tbody>
        </table>
    </div>
    @include('layouts.partials.pagination',['paginator'=>$documents])
</div>

{{-- Preview within system — right drawer --}}
<style>
.drawer-overlay{position:fixed;inset:0;background:rgba(36,20,8,.32);z-index:400;display:none;}
.drawer-overlay.open{display:block;}
.drawer{position:fixed;top:0;right:0;bottom:0;width:560px;max-width:92%;background:var(--white);z-index:401;display:flex;flex-direction:column;box-shadow:-12px 0 32px rgba(42,27,16,.18);transform:translateX(100%);transition:transform .28s cubic-bezier(.4,0,.2,1);}
.drawer.open{transform:translateX(0);}
.drawer-head{padding:16px 18px;border-bottom:1px solid var(--line);display:flex;align-items:center;justify-content:space-between;gap:12px;flex:none;background:var(--sand-50);}
.drawer-body{flex:1;background:var(--sand-50);overflow:hidden;display:flex;}
.drawer-foot{padding:12px 16px;border-top:1px solid var(--line);display:flex;justify-content:flex-end;gap:8px;flex:none;background:var(--white);}
</style>
<div class="drawer-overlay" id="drawerOverlay" onclick="closePreview()"></div>
<div class="drawer" id="previewDrawer" role="dialog" aria-label="Document preview">
    <div class="drawer-head">
        <div style="min-width:0;">
            <h3 id="previewTitle" style="font-size:15px;font-weight:800;color:var(--coffee-900);margin:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">Document Preview</h3>
            <p id="previewSub" style="font-size:12px;color:var(--ink-soft);margin:2px 0 0;word-break:break-all;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"></p>
        </div>
        <button class="modal-close" onclick="closePreview()" aria-label="Close" style="flex:none;"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
    </div>
    <div class="drawer-body">
        <div id="previewContainer" style="flex:1;display:flex;align-items:center;justify-content:center;overflow:auto;background:#fff;"></div>
    </div>
    <div class="drawer-foot">
        <a id="previewDownload" href="#" target="_blank" class="btn btn-ghost btn-sm">Open in new tab</a>
        <button class="btn btn-ghost btn-sm" onclick="closePreview()">Close</button>
    </div>
</div>
<script>
function previewDoc(url, name, mime){
    mime = (mime || '').toLowerCase();
    var container = document.getElementById('previewContainer');
    var fallback = document.getElementById('previewFallback');
    if (fallback) fallback.remove();
    document.getElementById('previewTitle').textContent = 'Preview — ' + name;
    document.getElementById('previewSub').textContent = name;
    document.getElementById('previewDownload').href = url;
    container.innerHTML = '';
    if (mime.indexOf('image/') === 0) {
        var img = document.createElement('img');
        img.src = url;
        img.alt = name;
        img.style.maxWidth = '100%';
        img.style.maxHeight = '100%';
        img.style.objectFit = 'contain';
        container.appendChild(img);
    } else if (mime === 'application/pdf') {
        var embed = document.createElement('embed');
        embed.src = url;
        embed.type = 'application/pdf';
        embed.style.width = '100%';
        embed.style.height = '100%';
        embed.setAttribute('id', 'pdfEmbed');
        container.appendChild(embed);
    } else {
        var el = document.createElement('div');
        el.id = 'previewFallback';
        el.style.cssText = 'max-width:420px;margin:auto;text-align:center;padding:24px;';
        el.innerHTML = '<div style="font-size:13px;color:var(--ink-soft);line-height:1.6;">This file type ('
            + (mime || 'unknown') + ') cannot be displayed in the preview panel. Use <strong>Open in new tab</strong> to view it without downloading.</div>';
        container.appendChild(el);
    }
    document.getElementById('drawerOverlay').classList.add('open');
    document.getElementById('previewDrawer').classList.add('open');
    document.body.style.overflow='hidden';
}
function closePreview(){
    document.getElementById('drawerOverlay').classList.remove('open');
    document.getElementById('previewDrawer').classList.remove('open');
    document.body.style.overflow='';
    setTimeout(()=>{ document.getElementById('previewContainer').innerHTML=''; }, 250);
}
</script>
@endsection
