@extends('layouts.admin')
@section('title','Application Rounds')
@section('content')
<div class="page-head">
    <div><h1>Application Rounds</h1><p class="page-sub">Rounds per academic year.</p></div>
    <a href="{{ route('admin.rounds.create') }}" class="btn btn-primary">+ New Round</a>
</div>
<div class="table-card">
    <div class="table-toolbar">
        <div class="chip-filters">
            <button class="chip active" data-filter="all">All</button>
            <button class="chip" data-filter="current">Current</button>
        </div>
        <div class="table-search">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input id="tableSearch" type="text" placeholder="Search rounds…">
        </div>
    </div>
    <div class="table-scroll">
        <table>
            <thead><tr><th>Academic Year</th><th class="center">Round</th><th>Name</th><th class="center">Current</th><th class="right">Actions</th></tr></thead>
            <tbody>
                @forelse($rounds as $r)
                <tr data-filter="{{ $r->is_current ? 'current' : 'other' }}">
                    <td><div class="cell-main"><div class="thumb thumb-blue">{{ substr($r->academicYear->name,0,1) }}</div><div><div class="cell-title">{{ $r->academicYear->name }}</div><div class="cell-sub">Year #{{ $r->academicYear->id }}</div></div></div></td>
                    <td class="center"><span class="tag tag-blue">R{{ $r->round_number }}</span></td>
                    <td><div class="cell-title" style="font-size:13px">{{ $r->name }}</div></td>
                    <td class="center">@if($r->is_current)<span class="tag tag-green">Current</span>@else<span class="tag tag-grey">—</span>@endif</td>
                    <td class="right"><div class="row-actions">
                        <a href="{{ route('admin.rounds.edit', encId($r->id)) }}" class="btn-icon" title="Edit"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></a>
                        <form method="POST" action="{{ route('admin.rounds.destroy', encId($r->id)) }}" onsubmit="event.preventDefault();confirmModal('Delete Round','Are you sure you want to delete round \'{{ $r->name }}\'? This action cannot be undone.',()=>this.submit())">@csrf @method('DELETE')<button class="btn-icon danger" title="Delete"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg></button></form>
                    </div></td>
                </tr>
                @empty<tr><td colspan="5"><div class="empty-state"><div class="es-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></div><strong>No rounds</strong><p>Create a round to open admission windows.</p></div></td></tr>@endforelse
            </tbody>
        </table>
    </div>
    @include('layouts.partials.pagination',['paginator'=>$rounds])
</div>
@endsection
