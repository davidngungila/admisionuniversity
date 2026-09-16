@extends('layouts.admin')
@section('title','Selection Batches')
@section('content')
<div class="page-head">
    <div><h1>Selection Batches</h1><p class="page-sub">Selection and admission batches.</p></div>
    <a href="{{ route('admin.selection.create') }}" class="btn btn-primary">+ New Batch</a>
</div>
<div class="table-card">
    <div class="table-toolbar">
        <div class="chip-filters">
            <button class="chip active" data-filter="all">All</button>
            <button class="chip" data-filter="processed">Processed</button>
            <button class="chip" data-filter="pending">Pending</button>
        </div>
        <div class="table-search">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input id="tableSearch" type="text" placeholder="Search batches…">
        </div>
    </div>
    <div class="table-scroll">
        <table>
            <thead><tr><th>Batch</th><th class="center">Year / Round</th><th class="center">Status</th><th class="right">Actions</th></tr></thead>
            <tbody>
                @forelse($batches as $b)
                <tr data-filter="{{ strtolower($b->status) }}">
                    <td><div class="cell-main"><div class="thumb thumb-gold">{{ substr($b->name,0,1) }}</div><div><div class="cell-title" style="font-size:13px">{{ $b->name }}</div><div class="cell-sub">#{{ $b->id }}</div></div></div></td>
                    <td class="center"><span class="tag tag-grey">{{ $b->academicYear->name }} · R{{ $b->applicationRound->round_number }}</span></td>
                    <td class="center">@if($b->status==='PROCESSED')<span class="tag tag-green">Processed</span>@else<span class="tag tag-gold">Pending</span>@endif</td>
                    <td class="right"><div class="row-actions"><a href="{{ route('admin.selection.show', encId($b->id)) }}" class="btn btn-ghost btn-sm">View</a></div></td>
                </tr>
                @empty<tr><td colspan="4"><div class="empty-state"><div class="es-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg></div><strong>No batches</strong><p>Create your first selection batch.</p></div></td></tr>@endforelse
            </tbody>
        </table>
    </div>
    @include('layouts.partials.pagination',['paginator'=>$batches])
</div>
@endsection
