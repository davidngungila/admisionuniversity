@extends('layouts.admin')
@section('title','Campuses')
@section('content')
<div class="page-head">
    <div><h1>Campuses</h1><p class="page-sub">University campuses and locations.</p></div>
    <a href="{{ route('admin.campuses.create') }}" class="btn btn-primary">+ New Campus</a>
</div>
<div class="table-card">
    <div class="table-toolbar">
        <div class="chip-filters"><button class="chip active" data-filter="all">All</button></div>
        <div class="table-search">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input id="tableSearch" type="text" placeholder="Search campuses…">
        </div>
    </div>
    <div class="table-scroll">
        <table>
            <thead><tr><th>Campus</th><th class="center">Code</th><th>Location</th><th class="right">Actions</th></tr></thead>
            <tbody>
                @forelse($campuses as $c)
                <tr>
                    <td><div class="cell-main"><div class="thumb thumb-gold">{{ substr($c->name,0,1) }}</div><div><div class="cell-title">{{ $c->name }}</div><div class="cell-sub">#{{ $c->id }}</div></div></div></td>
                    <td class="center"><span class="tag tag-grey">{{ $c->code ?? '—' }}</span></td>
                    <td><div class="cell-sub">{{ $c->location ?? '—' }}</div></td>
                    <td class="right"><div class="row-actions">
                        <a href="{{ route('admin.campuses.edit', encId($c->id)) }}" class="btn-icon" title="Edit"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></a>
                        <form method="POST" action="{{ route('admin.campuses.destroy', encId($c->id)) }}" onsubmit="event.preventDefault();confirmModal('Delete Campus','Are you sure you want to delete campus \'{{ $c->name }}\'? This action cannot be undone.',()=>this.submit())">@csrf @method('DELETE')<button class="btn-icon danger" title="Delete"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg></button></form>
                    </div></td>
                </tr>
                @empty<tr><td colspan="4"><div class="empty-state"><div class="es-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg></div><strong>No campuses</strong><p>Add the first campus.</p></div></td></tr>@endforelse
            </tbody>
        </table>
    </div>
    @include('layouts.partials.pagination',['paginator'=>$campuses])
</div>
@endsection
