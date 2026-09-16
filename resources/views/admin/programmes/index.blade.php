@extends('layouts.admin')
@section('title','Programmes')
@section('content')
<div class="page-head">
    <div><h1>Programmes</h1><p class="page-sub">Academic programmes by level and campus.</p></div>
    <a href="{{ route('admin.programmes.create') }}" class="btn btn-primary">+ New Programme</a>
</div>
<form method="GET" class="panel" style="padding:14px 16px;display:flex;gap:10px;flex-wrap:wrap;align-items:center;margin-bottom:16px">
    <input name="search" value="{{ request('search') }}" placeholder="Search name…" style="flex:1;min-width:160px;padding:10px 12px;border:1.5px solid var(--line);border-radius:10px;font-size:13px">
    <select name="level" style="padding:10px 12px;border:1.5px solid var(--line);border-radius:10px;background:#fff;font-size:13px"><option value="">All levels</option>@foreach($levels as $lv)<option value="{{ $lv->id }}" @selected(request('level')==$lv->id)>{{ $lv->name }}</option>@endforeach</select>
    <select name="campus" style="padding:10px 12px;border:1.5px solid var(--line);border-radius:10px;background:#fff;font-size:13px"><option value="">All campuses</option>@foreach($campuses as $c)<option value="{{ $c->id }}" @selected(request('campus')==$c->id)>{{ $c->name }}</option>@endforeach</select>
    <button class="btn btn-ghost btn-sm">Filter</button>
    @if(request('search')||request('level')||request('campus'))<a href="{{ route('admin.programmes.index') }}" class="btn btn-ghost btn-sm">Clear</a>@endif
</form>
<div class="table-card">
    <div class="table-toolbar">
        <div class="chip-filters">
            <button class="chip active" data-filter="all">All</button>
            <button class="chip" data-filter="bachelor">Bachelor</button>
            <button class="chip" data-filter="diploma">Diploma</button>
            <button class="chip" data-filter="certificate">Certificate</button>
        </div>
        <div class="table-search">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input id="tableSearch" type="text" placeholder="Search programmes…">
        </div>
    </div>
    <div class="table-scroll">
        <table>
            <thead><tr><th>Code</th><th>Programme</th><th class="center">Level</th><th class="center">Duration</th><th class="right">Tuition</th><th class="center">Capacity</th><th class="right">Actions</th></tr></thead>
            <tbody>
                @forelse($programmes as $p)
                <tr data-filter="{{ strtolower($p->admissionLevel->short_name) }}">
                    <td><span class="cell-mono">{{ $p->code }}</span></td>
                    <td><div class="cell-main"><div class="thumb thumb-coffee">{{ substr($p->name,0,1) }}</div><div><div class="cell-title" style="font-size:13px">{{ $p->name }}</div><div class="cell-sub">{{ $p->department->name ?? '—' }}</div></div></div></td>
                    <td class="center"><span class="tag tag-blue">{{ $p->admissionLevel->short_name }}</span></td>
                    <td class="center"><span class="tag tag-grey">{{ $p->duration_years }}y · {{ $p->study_mode }}</span></td>
                    <td class="right"><span class="tag tag-gold">TZS {{ number_format($p->tuition_fee) }}</span></td>
                    <td class="center"><span class="tag tag-grey">{{ $p->capacity ?? '—' }}</span></td>
                    <td class="right"><div class="row-actions">
                        <a href="{{ route('admin.programmes.show', encId($p->id)) }}" class="btn-icon" title="View"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></a>
                        <a href="{{ route('admin.programmes.edit', encId($p->id)) }}" class="btn-icon" title="Edit"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></a>
                        <form method="POST" action="{{ route('admin.programmes.destroy', encId($p->id)) }}" onsubmit="event.preventDefault();confirmModal('Delete Programme','Are you sure you want to delete programme \'{{ $p->name }}\'? This action cannot be undone.',()=>this.submit())">@csrf @method('DELETE')<button class="btn-icon danger" title="Delete"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg></button></form>
                    </div></td>
                </tr>
                @empty<tr><td colspan="7"><div class="empty-state"><div class="es-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg></div><strong>No programmes</strong><p>Create your first programme.</p></div></td></tr>@endforelse
            </tbody>
        </table>
    </div>
    @include('layouts.partials.pagination',['paginator'=>$programmes])
</div>
@endsection
