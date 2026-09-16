@extends('layouts.applicant')
@section('title','Documents')
@section('content')
<div class="page-head">
    <div>
        <h1>Documents</h1>
        <p class="page-sub">Step {{ $currentStep->step_number }} of {{ $steps->count() }} · Upload required supporting documents</p>
    </div>
    <div class="page-actions">
        <span class="tag tag-blue">{{ $progress['completed'] }} of {{ $progress['total'] }} — {{ $progress['percent'] }}%</span>
    </div>
</div>

<div class="panel" style="margin-bottom:16px;">
    <div class="panel-body" style="padding:14px 18px;">
        <div style="height:6px;background:var(--sand-100);border:1px solid var(--line);border-radius:20px;overflow:hidden;">
            <div style="height:100%;background:var(--terracotta-600);width:{{ $progress['percent'] }}%"></div>
        </div>
        <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:12px;">
            @foreach($steps as $st)
                @php $done = $application->stepCompletions()->where('workflow_step_id',$st->id)->whereNotNull('completed_at')->exists(); $isCurrent = $st->id===$currentStep->id; @endphp
                <span class="tag {{ $done ? 'tag-green' : ($isCurrent ? 'tag-terracotta' : 'tag-grey') }}" style="display:inline-flex;align-items:center;gap:6px;">
                    <span style="width:16px;height:16px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-size:9px;font-weight:800;background:{{ $done ? 'var(--acacia-600)' : ($isCurrent ? 'var(--terracotta-600)' : 'var(--sand-200)') }};color:{{ $done || $isCurrent ? '#fff' : 'var(--coffee-700)' }};">{{ $done ? '✓' : $st->step_number }}</span>
                    {{ $st->step_name }}
                </span>
            @endforeach
        </div>
    </div>
</div>

@if($documents->count())
    <div class="panel" style="margin-bottom:16px;">
        <div class="panel-head">
            <div class="panel-title">Uploaded</div>
            <span class="tag tag-green">{{ $documents->count() }} file(s)</span>
        </div>
        <div class="panel-body" style="display:flex;flex-direction:column;gap:10px;">
            @foreach($documents as $d)
                <div class="doc-item" style="display:flex;align-items:center;justify-content:space-between;gap:12px;padding:12px 14px;border:1px solid var(--line);border-radius:10px;background:var(--sand-50);cursor:pointer;" onclick="openDocPreview('{{ $d->file_path }}', '{{ $d->document_type }}', '{{ $d->mime_type }}', '{{ $d->file_name }}')">
                    <div style="display:flex;align-items:center;gap:12px;min-width:0;">
                        <div class="thumb thumb-coffee" style="width:38px;height:38px;border-radius:10px;flex:none;background:var(--white);border:1px solid var(--line);display:flex;align-items:center;justify-content:center;color:var(--terracotta-600);">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/></svg>
                        </div>
                        <div style="min-width:0;">
                            <div style="font-weight:700;font-size:13.5px;color:var(--coffee-900);">{{ $d->document_type }}</div>
                            <div class="field-hint" style="word-break:break-all;">{{ $d->file_name }} · {{ $d->mime_type }}</div>
                        </div>
                    </div>
                    <span class="tag {{ $d->is_verified ? 'tag-green' : 'tag-grey' }}">{{ $d->is_verified ? 'Verified' : 'Pending' }}</span>
                </div>
            @endforeach
        </div>
    </div>
@endif

<!-- Document Preview Right Drawer -->
<div class="modal-backdrop" id="docPreviewDrawer" onclick="if(event.target===this)closeDocPreview()" style="display:none;z-index:1050;">
    <div class="modal" style="position:fixed;right:0;top:0;height:100vh;width:100%;max-width:600px;margin:0;transform:translateX(100%);transition:transform .3s ease;border-radius:0;border-left:1px solid var(--line);" id="docPreviewModal">
        <div class="modal-head" style="display:flex;align-items:center;justify-content:space-between;padding:16px 20px;border-bottom:1px solid var(--line);background:var(--white);position:sticky;top:0;z-index:10;">
            <div>
                <h3 id="docPreviewTitle" style="margin:0;font-size:16px;font-weight:700;color:var(--coffee-900);">Document</h3>
                <p id="docPreviewSub" style="margin:4px 0 0;font-size:12px;color:var(--coffee-600);"></p>
            </div>
            <button class="modal-close" onclick="closeDocPreview()"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
        </div>
        <div class="modal-body" style="height:calc(100vh - 70px);overflow:auto;padding:0;background:var(--sand-50);">
            <div id="docPreviewContent" style="width:100%;height:100%;"></div>
        </div>
    </div>
</div>

<script>
function openDocPreview(url, type, mime, name) {
    const drawer = document.getElementById('docPreviewDrawer');
    const modal = document.getElementById('docPreviewModal');
    const content = document.getElementById('docPreviewContent');
    const title = document.getElementById('docPreviewTitle');
    const sub = document.getElementById('docPreviewSub');
    title.textContent = type;
    sub.textContent = name;
    content.innerHTML = '';
    if (mime.startsWith('image/')) {
        content.innerHTML = '<img src="' + url + '" alt="' + name + '" style="max-width:100%;height:auto;display:block;margin:0 auto;padding:20px;">';
    } else if (mime === 'application/pdf') {
        content.innerHTML = '<iframe src="' + url + '" style="width:100%;height:100%;border:none;min-height:calc(100vh - 70px);"></iframe>';
    } else {
        content.innerHTML = '<div style="padding:40px;text-align:center;color:var(--coffee-600);">Preview not available for this file type.<br><a href="' + url + '" target="_blank" class="btn btn-primary" style="margin-top:12px;display:inline-block;">Open in new tab</a></div>';
    }
    drawer.style.display = 'block';
    requestAnimationFrame(() => modal.style.transform = 'translateX(0)');
}
function closeDocPreview() {
    const drawer = document.getElementById('docPreviewDrawer');
    const modal = document.getElementById('docPreviewModal');
    modal.style.transform = 'translateX(100%)';
    setTimeout(() => drawer.style.display = 'none', 300);
}
</script>

<div class="panel">
    <div class="panel-head">
        <div>
            <div class="panel-title">Upload Documents <span class="tag tag-grey" style="margin-left:8px">Optional</span></div>
            <div class="panel-sub">PDF / JPG / PNG, max 5 MB each — you may skip and upload later</div>
        </div>
    </div>
    <div class="panel-body">
<form method="POST" action="{{ route('applicant.application.save', [encId($application->id), $currentStep->route]) }}" enctype="multipart/form-data" style="display:flex;flex-direction:column;gap:16px;">
            <input type="hidden" name="skip_docs" value="1">
            @csrf
            <p class="field-hint" style="margin:8px 0;color:var(--coffee-700);">Documents are optional — you may skip and upload later.</p>

            <div class="form-actions">
                <a href="{{ route('applicant.application.show', encId($application->id)) }}" class="btn btn-ghost">Back</a>
                <button type="submit" class="btn btn-primary">Continue →</button>
            </div>
        </form>
    </div>
</div>

@endsection
