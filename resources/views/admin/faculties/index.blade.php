@extends('layouts.admin')
@section('title','Faculties')
@section('content')
<div class="page-head">
    <div><h1>Faculties / Schools</h1><p class="page-sub">Faculties grouped by campus.</p></div>
    <a href="{{ route('admin.faculties.create') }}" class="btn btn-primary">+ New Faculty</a>
</div>
<div class="table-card">
    <div class="table-toolbar">
        <div class="chip-filters"><button class="chip active" data-filter="all">All</button></div>
        <div class="table-search">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input id="tableSearch" type="text" placeholder="Search faculties…">
        </div>
    </div>
    <div class="table-scroll">
        <table>
            <thead><tr><th>Faculty</th><th class="center">Code</th><th class="center">Departments</th><th class="right">Actions</th></tr></thead>
            <tbody>
                @forelse($faculties as $f)
                <tr>
                    <td><div class="cell-main"><div class="thumb thumb-blue">{{ substr($f->name,0,1) }}</div><div><div class="cell-title">{{ $f->name }}</div><div class="cell-sub">Campus: {{ $f->campus->name ?? '—' }}</div></div></div></td>
                    <td class="center"><span class="tag tag-grey">{{ $f->code ?? '—' }}</span></td>
                    <td class="center"><span class="tag tag-gold">{{ $f->departments_count }}</span></td>
                    <td class="right"><div class="row-actions">
                        <a href="{{ route('admin.faculties.edit', encId($f->id)) }}" class="btn-icon" title="Edit"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></a>
                        <form method="POST" action="{{ route('admin.faculties.destroy', encId($f->id)) }}" onsubmit="event.preventDefault();confirmModal('Delete Faculty','Are you sure you want to delete faculty \'{{ $f->name }}\'? This action cannot be undone.',()=>this.submit())">@csrf @method('DELETE')<button class="btn-icon danger" title="Delete"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg></button></form>
                    </div></td>
                </tr>
                @empty<tr><td colspan="4"><div class="empty-state"><div class="es-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/></svg></div><strong>No faculties</strong><p>Create your first faculty.</p></div></td></tr>@endforelse
            </tbody>
        </table>
    </div>
    @include('layouts.partials.pagination',['paginator'=>$faculties])
</div>
@endsection
