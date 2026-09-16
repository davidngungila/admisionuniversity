@extends('layouts.admin')
@section('title','Departments')
@section('content')
<div class="page-head">
    <div><h1>Departments</h1><p class="page-sub">Departments under each faculty.</p></div>
    <a href="{{ route('admin.departments.create') }}" class="btn btn-primary">+ New Department</a>
</div>
<div class="table-card">
    <div class="table-toolbar">
        <div class="chip-filters"><button class="chip active" data-filter="all">All</button></div>
        <div class="table-search">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input id="tableSearch" type="text" placeholder="Search departments…">
        </div>
    </div>
    <div class="table-scroll">
        <table>
            <thead><tr><th>Department</th><th>Faculty</th><th class="center">Code</th><th class="right">Actions</th></tr></thead>
            <tbody>
                @forelse($departments as $d)
                <tr>
                    <td><div class="cell-main"><div class="thumb thumb-green">{{ substr($d->name,0,1) }}</div><div><div class="cell-title">{{ $d->name }}</div><div class="cell-sub">#{{ $d->id }}</div></div></div></td>
                    <td><div class="cell-sub">{{ $d->faculty->name }}</div></td>
                    <td class="center"><span class="tag tag-grey">{{ $d->code ?? '—' }}</span></td>
                    <td class="right"><div class="row-actions">
                        <a href="{{ route('admin.departments.edit', encId($d->id)) }}" class="btn-icon" title="Edit"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></a>
                        <form method="POST" action="{{ route('admin.departments.destroy', encId($d->id)) }}" onsubmit="event.preventDefault();confirmModal('Delete Department','Are you sure you want to delete department \'{{ $d->name }}\'? This action cannot be undone.',()=>this.submit())">@csrf @method('DELETE')<button class="btn-icon danger" title="Delete"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg></button></form>
                    </div></td>
                </tr>
                @empty<tr><td colspan="4"><div class="empty-state"><div class="es-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg></div><strong>No departments</strong><p>Add the first department.</p></div></td></tr>@endforelse
            </tbody>
        </table>
    </div>
    @include('layouts.partials.pagination',['paginator'=>$departments])
</div>
@endsection
