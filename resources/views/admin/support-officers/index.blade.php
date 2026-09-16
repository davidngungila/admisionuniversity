@extends('layouts.admin')
@section('title','Support Officers')
@section('content')
<div class="page-head">
    <div><h1>Support Officers</h1><p class="page-sub">Officers shown in the support helpline panel (AVAILABLE OFFICERS) on the public site.</p></div>
    <a href="{{ route('admin.support-officers.create') }}" class="btn btn-primary">+ New Officer</a>
</div>
<div class="panel" style="padding:12px 16px;margin-bottom:14px;font-size:12.5px;color:var(--ink-soft);line-height:1.6;">
    These appear in the floating <strong>Support &amp; Helpline</strong> button on the public and applicant pages, in display order. Only <strong>active</strong> officers are shown.
</div>
<div class="table-card">
    <div class="table-toolbar">
        <div class="chip-filters">
            <button class="chip active" data-filter="all">All</button>
            <button class="chip" data-filter="active">Active</button>
            <button class="chip" data-filter="inactive">Inactive</button>
            <button class="chip" data-filter="online">Online</button>
        </div>
        <div class="table-search">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input id="tableSearch" type="text" placeholder="Search officers…">
        </div>
    </div>
    <div class="table-scroll">
        <table>
            <thead><tr><th>Officer</th><th>Phone</th><th>Group / Category</th><th class="center">Status</th><th>Order</th><th class="right">Actions</th></tr></thead>
            <tbody>
                @forelse($officers as $o)
                <tr data-filter="{{ $o->is_active ? 'active' : 'inactive' }} {{ $o->is_online ? 'online' : '' }}">
                    <td>
                        <div class="cell-main">
                            <div class="thumb thumb-blue">{{ substr($o->name,0,1) }}</div>
                            <div>
                                <div class="cell-title" style="font-size:13px">{{ $o->name }}</div>
                                @if($o->designation)<div class="cell-sub">{{ $o->designation }}</div>@endif
                            </div>
                        </div>
                    </td>
                    <td><a href="tel:+255{{ preg_replace('/\D/','',$o->phone) }}" class="cell-mono" style="font-weight:700;">{{ $o->phone }}</a></td>
                    <td><span class="tag tag-grey">{{ $o->group_name ?? '—' }}</span></td>
                    <td class="center">
                        @if(!$o->is_active)<span class="tag tag-grey">Inactive</span>
                        @elseif($o->is_online)<span class="tag tag-green">Active · Online</span>
                        @else<span class="tag tag-gold">Active</span>@endif
                    </td>
                    <td><span class="tag tag-grey">{{ $o->order_index }}</span></td>
                    <td class="right"><div class="row-actions">
                        <a href="{{ route('admin.support-officers.edit', encId($o->id)) }}" class="btn-icon" title="Edit"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></a>
                        <form method="POST" action="{{ route('admin.support-officers.destroy', encId($o->id)) }}" onsubmit="event.preventDefault();confirmModal('Delete Officer','Remove \'{{ $o->name }}\' from the support panel?',()=>this.submit())">@csrf @method('DELETE')<button class="btn-icon danger" title="Delete"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg></button></form>
                    </div></td>
                </tr>
                @empty<tr><td colspan="6"><div class="empty-state"><div class="es-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/><path d="M16 13h12"/></svg></div><strong>No officers</strong><p>Add an officer to show them in the support panel.</p></div></td></tr>@endforelse
            </tbody>
        </table>
    </div>
    @include('layouts.partials.pagination',['paginator'=>$officers])
</div>
@endsection