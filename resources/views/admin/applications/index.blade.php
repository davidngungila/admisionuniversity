@extends('layouts.admin')
@section('title','Applications')
@section('content')
<div class="page-head">
    <div><h1>Applications</h1><p class="page-sub">All submitted and draft applications.</p></div>
</div>
<form method="GET" class="panel" style="padding:14px 16px;display:flex;gap:10px;flex-wrap:wrap;align-items:center;margin-bottom:16px">
    <input name="search" value="{{ request('search') }}" placeholder="Application no…" style="padding:10px 12px;border:1.5px solid var(--line);border-radius:10px;font-size:13px;min-width:160px">
    <select name="status" style="padding:10px 12px;border:1.5px solid var(--line);border-radius:10px;background:#fff;font-size:13px"><option value="">All statuses</option>@foreach($statuses as $s)<option @selected(request('status')==$s)>{{ $s }}</option>@endforeach</select>
    <select name="window" style="padding:10px 12px;border:1.5px solid var(--line);border-radius:10px;background:#fff;font-size:13px"><option value="">All windows</option>@foreach($windows as $w)<option value="{{ $w->id }}" @selected(request('window')==$w->id)>{{ $w->admissionLevel->name }} R{{ $w->applicationRound->round_number }}</option>@endforeach</select>
    <button class="btn btn-ghost btn-sm">Filter</button>
    @if(request('search')||request('status')||request('window'))<a href="{{ route('admin.applications.index') }}" class="btn btn-ghost btn-sm">Clear</a>@endif
</form>
<div class="table-card">
    <div class="table-toolbar">
        <div class="chip-filters">
            <button class="chip active" data-filter="all">All</button>
            <button class="chip" data-filter="submitted">Submitted</button>
            <button class="chip" data-filter="under_review">Under Review</button>
            <button class="chip" data-filter="selected">Selected</button>
        </div>
        <div class="table-search">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input id="tableSearch" type="text" placeholder="Filter in table…">
        </div>
    </div>
    <div class="table-scroll">
        <table>
            <thead><tr><th>No</th><th>Applicant</th><th class="center">Year / Round</th><th class="center">Level</th><th class="center">Progress</th><th class="center">Status</th><th class="right">Actions</th></tr></thead>
            <tbody>
                @forelse($applications as $app)
                <tr data-filter="{{ strtolower($app->status) }}">
                    <td><span class="cell-mono">{{ $app->application_number ?? '#'.$app->id }}</span></td>
                    <td><div class="cell-main"><div class="thumb thumb-coffee">{{ substr($app->applicant->fullName(),0,1) }}</div><div><div class="cell-title" style="font-size:13px">{{ $app->applicant->fullName() }}</div><div class="cell-sub">{{ $app->applicant->email }}</div></div></div></td>
                    <td class="center"><span class="tag tag-grey">{{ $app->academicYear->name }} · R{{ $app->admissionWindow->applicationRound->round_number }}</span></td>
                    <td class="center"><span class="tag tag-blue">{{ $app->admissionWindow->admissionLevel->short_name }}</span></td>
                    <td class="center"><span class="tag tag-gold">{{ $app->progress_percentage }}%</span></td>
                    <td class="center">@php $s=$app->status; @endphp @if(in_array($s,['SELECTED','ADMITTED']))<span class="tag tag-green">{{ $s }}</span>@elseif($s==='SUBMITTED')<span class="tag tag-blue">{{ $s }}</span>@elseif($s==='UNDER_REVIEW')<span class="tag tag-gold">{{ $s }}</span>@elseif($s==='REJECTED')<span class="tag tag-red">{{ $s }}</span>@else<span class="tag tag-grey">{{ $s }}</span>@endif</td>
                    <td class="right"><div class="row-actions">
                        <a href="{{ route('admin.applications.show', encId($app->id)) }}" class="btn btn-ghost btn-sm">View</a>
                        <button type="button" class="btn-icon danger" title="Delete" onclick="openDeleteModal('{{ route('admin.applications.destroy', encId($app->id)) }}','{{ addslashes($app->application_number ?? '#'.$app->id) }}','{{ addslashes($app->applicant->fullName()) }}')"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6M14 11v6"/></svg></button>
                    </div></td>
                </tr>
                @empty<tr><td colspan="7"><div class="empty-state"><div class="es-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg></div><strong>No applications</strong><p>No matching records.</p></div></td></tr>@endforelse
            </tbody>
        </table>
    </div>
    @include('layouts.partials.pagination',['paginator'=>$applications])
</div>

{{-- Delete with reason modal --}}
<div class="modal-backdrop" id="deleteModal" onclick="if(event.target===this)closeModal('deleteModal')">
    <div class="modal" style="max-width:480px;">
        <div class="modal-head"><div><h3>Delete Application</h3><p id="deleteAppNo"></p></div><button class="modal-close" onclick="closeModal('deleteModal')">×</button></div>
        <div class="modal-body">
            <div style="padding:12px 14px;border-radius:10px;background:var(--danger-soft, #fdeceb);border:1px solid #f3c4c0;font-size:12.5px;line-height:1.6;color:var(--danger, #b3402f);">
                You are about to delete the application of <strong id="deleteApplicant"></strong>. This action <strong>cannot be undone</strong> and will permanently remove the application and all related records.
            </div>
            <form id="deleteForm" method="POST" style="margin-top:14px;">
                @csrf @method('DELETE')
                <div class="field">
                    <label class="field-label">Reason for deletion *</label>
                    <textarea name="reason" id="deleteReason" rows="3" required maxlength="500" placeholder="e.g. Duplicate application, submitted by mistake, incorrect data, applicant requested removal…" style="width:100%;padding:10px 12px;border:1.5px solid var(--line);border-radius:10px;font-size:13px;font-family:inherit;resize:vertical;background:#fff;"></textarea>
                    <span class="field-err" id="deleteReasonErr" style="display:none;">Please provide a reason for deleting this application.</span>
                </div>
            </form>
        </div>
        <div class="modal-foot" style="padding:12px 16px;border-top:1px solid var(--line);display:flex;justify-content:flex-end;gap:8px;">
            <button class="btn btn-ghost btn-sm" onclick="closeModal('deleteModal')">Cancel</button>
            <button class="btn btn-primary btn-sm" style="background:var(--danger);" onclick="submitDelete()">Delete Application</button>
        </div>
    </div>
</div>
<script>
function openDeleteModal(url, number, applicant){
    document.getElementById('deleteForm').action = url;
    document.getElementById('deleteForm').reset();
    document.getElementById('deleteAppNo').textContent = number;
    document.getElementById('deleteApplicant').textContent = applicant || 'this applicant';
    document.getElementById('deleteReasonErr').style.display = 'none';
    openModal('deleteModal');
}
function submitDelete(){
    const reason = document.getElementById('deleteReason').value.trim();
    if(!reason){ document.getElementById('deleteReasonErr').style.display='block'; return; }
    closeModal('deleteModal');
    document.getElementById('deleteForm').submit();
}
</script>
@endsection
