@extends('layouts.admin')
@section('title','Admission Windows')
@section('content')
<div class="page-head">
    <div><h1>Admission Windows</h1><p class="page-sub">Control opening periods, fees and applicant categories.</p></div>
    <a href="{{ route('admin.windows.create') }}" class="btn btn-primary">+ Create Window</a>
</div>
<form method="GET" class="panel" style="padding:14px 16px;display:flex;gap:10px;flex-wrap:wrap;align-items:center;margin-bottom:16px">
    <select name="year" class="field" style="flex:1;min-width:160px"><option value="">All years</option>@foreach($years as $y)<option value="{{ $y->id }}" @selected(request('year')==$y->id)>{{ $y->name }}</option>@endforeach</select>
    <select name="level" style="flex:1;min-width:160px;padding:10px 12px;border:1.5px solid var(--line);border-radius:10px;background:#fff;font-size:13px"><option value="">All levels</option>@foreach($levels as $lv)<option value="{{ $lv->id }}" @selected(request('level')==$lv->id)>{{ $lv->name }}</option>@endforeach</select>
    <button class="btn btn-ghost btn-sm">Filter</button>
    @if(request('year')||request('level'))<a href="{{ route('admin.windows.index') }}" class="btn btn-ghost btn-sm">Clear</a>@endif
</form>
<div class="table-card">
    <div class="table-toolbar">
        <div class="chip-filters">
            <button class="chip active" data-filter="all">All</button>
            <button class="chip" data-filter="open">Open</button>
            <button class="chip" data-filter="closed">Closed</button>
            <button class="chip" data-filter="upcoming">Upcoming</button>
        </div>
        <div class="table-search">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input id="tableSearch" type="text" placeholder="Search windows…">
        </div>
    </div>
    <div class="table-scroll">
        <table>
            <thead><tr><th>Level / Round</th><th class="center">Category</th><th>Year</th><th>Deadline</th><th class="right">Fee</th><th class="center">Status</th><th class="right">Actions</th></tr></thead>
            <tbody>
                @forelse($windows as $w)
                @php $st=strtolower($w->statusLabel()); @endphp
                <tr data-filter="{{ $st }}">
                    <td><div class="cell-main"><div class="thumb thumb-terracotta">{{ substr($w->admissionLevel->short_name,0,1) }}</div><div><div class="cell-title">{{ $w->admissionLevel->name }}</div><div class="cell-sub">Round {{ $w->applicationRound->round_number }} · {{ $w->applicationRound->name }}</div></div></div></td>
                    <td class="center"><span class="tag tag-grey">{{ $w->applicant_category }}</span></td>
                    <td><div class="cell-title" style="font-size:13px">{{ $w->academicYear->name }}</div></td>
                    <td><div class="cell-sub">{{ $w->closes_at->format('d M Y H:i') }}</div></td>
                    <td class="right"><span class="{{ $w->application_fee <= 0 ? 'tag tag-green' : 'tag tag-gold' }}">{{ $w->feeLabel() }}</span></td>
                    <td class="center">@if($w->statusLabel()==='OPEN')<span class="tag tag-green">Open</span>@elseif($w->statusLabel()==='CLOSED')<span class="tag tag-red">Closed</span>@elseif($w->statusLabel()==='CLOSING SOON')<span class="tag tag-gold">Closing soon</span>@else<span class="tag tag-grey">Upcoming</span>@endif</td>
                    <td class="right"><div class="row-actions">
                        @php $active = $w->status === \App\Models\AdmissionWindow::STATUS_ACTIVE; @endphp
                        <form method="POST" action="{{ route('admin.windows.toggle', encId($w->id)) }}" onsubmit="event.preventDefault(); const _f=this; confirmModal('{{ $active ? 'Turn OFF window' : 'Turn ON window' }}','Are you sure you want to turn {{ $active ? 'OFF' : 'ON' }} this admission window? {{ $active ? 'Applicants will no longer be able to apply.' : 'Applicants will be able to apply again.' }}',()=>_f.submit())">@csrf
                            <button class="btn-icon {{ $active ? 'success' : 'danger' }}" title="{{ $active ? 'Turn OFF' : 'Turn ON' }}" style="{{ $active ? 'color:var(--acacia-600)' : 'color:var(--terracotta-600)' }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18.36 6.64a9 9 0 1 1-12.73 0"/><line x1="12" y1="2" x2="12" y2="12"/></svg></button>
                        </form>
                        <a href="{{ route('admin.windows.edit', encId($w->id)) }}" class="btn-icon" title="Edit"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></a>
                        <form method="POST" action="{{ route('admin.windows.destroy', encId($w->id)) }}" onsubmit="event.preventDefault(); const _f=this; confirmModal('Delete Admission Window','Are you sure you want to delete this admission window? This action cannot be undone.',()=>_f.submit())">@csrf @method('DELETE')<button class="btn-icon danger" title="Delete"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg></button></form>
                    </div></td>
                </tr>
                @empty<tr><td colspan="7"><div class="empty-state"><div class="es-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/></svg></div><strong>No admission windows</strong><p>Create a window to open applications.</p></div></td></tr>@endforelse
            </tbody>
        </table>
    </div>
    @include('layouts.partials.pagination',['paginator'=>$windows])
</div>
@endsection
