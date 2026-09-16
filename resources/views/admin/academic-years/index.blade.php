@extends('layouts.admin')
@section('title','Academic Years')
@section('content')
<div class="page-head">
    <div><h1>Academic Years</h1><p class="page-sub">Manage admission academic years and active year.</p></div>
    <a href="{{ route('admin.academic-years.create') }}" class="btn btn-primary">+ New Year</a>
</div>
<div class="table-card">
    <div class="table-toolbar">
        <div class="chip-filters">
            <button class="chip active" data-filter="all">All</button>
            <button class="chip" data-filter="active">Active</button>
            <button class="chip" data-filter="inactive">Inactive</button>
        </div>
        <div class="table-search">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input id="tableSearch" type="text" placeholder="Search academic years…">
        </div>
    </div>
    <div class="table-scroll">
        <table>
            <thead><tr><th>Year</th><th>Period</th><th class="center">Status</th><th class="right">Actions</th></tr></thead>
            <tbody>
                @forelse($years as $y)
                <tr data-filter="{{ $y->is_active ? 'active' : 'inactive' }}" @if($y->is_active) style="background:var(--sand-100)" @endif>
                    <td><div class="cell-main"><div class="thumb {{ $y->is_active ? 'thumb-green' : 'thumb-grey' }}">{{ substr($y->name,0,1) }}</div><div><div class="cell-title">{{ $y->name }} @if($y->is_active)<span class="tag tag-green" style="margin-left:6px">ACTIVE</span>@endif</div><div class="cell-sub">#{{ $y->id }}</div></div></div></td>
                    <td><div class="cell-sub">{{ $y->start_date?->format('d M Y') ?? '—' }} → {{ $y->end_date?->format('d M Y') ?? '—' }}</div></td>
                    <td class="center">@if($y->is_active)<span class="tag tag-green">Active</span>@else<span class="tag tag-grey">Inactive</span>@endif</td>
                    <td class="right"><div class="row-actions">
                        <a href="{{ route('admin.academic-years.edit', encId($y->id)) }}" class="btn-icon" title="Edit"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></a>
                        @if(!$y->is_active)<form method="POST" action="{{ route('admin.academic-years.activate', encId($y->id)) }}" class="inline" onsubmit="event.preventDefault(); const _f=this; confirmModal('Activate year','Are you sure you want to activate this year?',()=>_f.submit())">@csrf<button class="btn-icon" title="Activate"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg></button></form>@endif
                        <form method="POST" action="{{ route('admin.academic-years.destroy', encId($y->id)) }}" onsubmit="event.preventDefault();confirmModal('Delete Academic Year','Are you sure you want to delete academic year \'{{ $y->name }}\'? This action cannot be undone.',()=>this.submit())">@csrf @method('DELETE')<button class="btn-icon danger" title="Delete"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/></svg></button></form>
                    </div></td>
                </tr>
                @empty<tr><td colspan="4"><div class="empty-state"><div class="es-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></div><strong>No academic years</strong><p>Create your first academic year.</p></div></td></tr>@endforelse
            </tbody>
        </table>
    </div>
    @include('layouts.partials.pagination',['paginator'=>$years])
</div>
@endsection
