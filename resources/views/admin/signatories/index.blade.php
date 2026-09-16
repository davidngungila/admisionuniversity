@extends('layouts.admin')
@section('title','Signatories')
@section('content')
<div class="page-head">
    <div><h1>Signatories</h1><p class="page-sub">University officers whose signatures appear on admission and joining letters.</p></div>
    <a href="{{ route('admin.signatories.create') }}" class="btn btn-primary">+ New Signatory</a>
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
            <input id="tableSearch" type="text" placeholder="Search signatories…">
        </div>
    </div>
    <div class="table-scroll">
        <table>
            <thead><tr><th>Signatory</th><th class="center">Signature</th><th class="center">Status</th><th>Order</th><th class="right">Actions</th></tr></thead>
            <tbody>
                @forelse($signatories as $s)
                <tr data-filter="{{ $s->is_active ? 'active' : 'inactive' }}">
                    <td>
                        <div class="cell-main">
                            <div class="thumb thumb-gold">{{ substr($s->name,0,1) }}</div>
                            <div>
                                <div class="cell-title" style="font-size:13px">{{ $s->title ? $s->title.' '.$s->name : $s->name }}</div>
                                <div class="cell-sub">{{ $s->designation ?? '—' }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="center">
                        @if($s->signature_image && file_exists(public_path($s->signature_image)))
                            <img src="{{ asset($s->signature_image) }}" style="height:32px;max-width:110px;object-fit:contain;" alt="Signature">
                        @else<span class="tag tag-red">No image</span>@endif
                    </td>
                    <td class="center">@if($s->is_active)<span class="tag tag-green">Active</span>@else<span class="tag tag-grey">Inactive</span>@endif</td>
                    <td><span class="tag tag-grey">{{ $s->order_index }}</span></td>
                    <td class="right"><div class="row-actions">
                        <a href="{{ route('admin.signatories.edit', encId($s->id)) }}" class="btn-icon" title="Edit"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></a>
                        <form method="POST" action="{{ route('admin.signatories.destroy', encId($s->id)) }}" onsubmit="event.preventDefault();confirmModal('Delete Signatory','Are you sure you want to delete \'{{ $s->name }}\'? Signature files will be removed.',()=>this.submit())">@csrf @method('DELETE')<button class="btn-icon danger" title="Delete"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg></button></form>
                    </div></td>
                </tr>
                @empty<tr><td colspan="5"><div class="empty-state"><div class="es-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg></div><strong>No signatories</strong><p>Add signatories so they appear on admission documents.</p></div></td></tr>@endforelse
            </tbody>
        </table>
    </div>
    @include('layouts.partials.pagination',['paginator'=>$signatories])
</div>
@endsection