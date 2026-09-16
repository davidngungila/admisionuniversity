@extends('layouts.admin')
@section('title','Applicants')
@section('content')
<div class="page-head">
    <div><h1>Applicants</h1><p class="page-sub">Registered applicants and their application counts.</p></div>
</div>
<form method="GET" class="panel" style="padding:14px 16px;display:flex;gap:10px;margin-bottom:16px">
    <div class="table-search" style="flex:1"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg><input name="search" value="{{ request('search') }}" placeholder="Search name / email / applicant no…" style="width:100%"></div>
    <button class="btn btn-ghost btn-sm">Search</button>
    @if(request('search'))<a href="{{ route('admin.applicants.index') }}" class="btn btn-ghost btn-sm">Clear</a>@endif
</form>
<div class="table-card">
    <div class="table-toolbar">
        <div class="chip-filters"><button class="chip active" data-filter="all">All</button></div>
        <div class="table-search">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input id="tableSearch" type="text" placeholder="Filter in table…">
        </div>
    </div>
    <div class="table-scroll">
        <table>
            <thead><tr><th>Applicant</th><th class="center">Phone</th><th class="center">Nationality</th><th class="center">Apps</th><th class="right">Actions</th></tr></thead>
            <tbody>
                @forelse($applicants as $a)
                <tr>
                    <td><div class="cell-main"><div class="thumb thumb-terracotta">{{ substr($a->fullName(),0,1) }}</div><div><div class="cell-title">{{ $a->fullName() }}</div><div class="cell-sub">{{ $a->email }} · {{ $a->applicant_number ?? '—' }}</div></div></div></td>
                    <td class="center"><span class="cell-mono">{{ $a->phone }}</span></td>
                    <td class="center"><span class="tag tag-grey">{{ $a->citizenship->name ?? '—' }}</span></td>
                    <td class="center"><span class="tag tag-gold">{{ $a->applications_count ?? $a->applications()->count() }}</span></td>
                    <td class="right"><div class="row-actions"><a href="{{ route('admin.applicants.show', encId($a->id)) }}" class="btn btn-ghost btn-sm">View</a></div></td>
                </tr>
                @empty<tr><td colspan="5"><div class="empty-state"><div class="es-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg></div><strong>No applicants</strong><p>No matching records.</p></div></td></tr>@endforelse
            </tbody>
        </table>
    </div>
    @include('layouts.partials.pagination',['paginator'=>$applicants])
</div>
@endsection
