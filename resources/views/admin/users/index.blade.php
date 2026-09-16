@extends('layouts.admin')
@section('title','Users')
@section('content')
<div class="page-head">
    <div><h1>Users</h1><p class="page-sub">System users and roles.</p></div>
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">+ New User</a>
</div>
<form method="GET" class="panel" style="padding:14px 16px;display:flex;gap:10px;flex-wrap:wrap;align-items:center;margin-bottom:16px">
    <div class="table-search" style="flex:1;min-width:180px"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg><input name="search" value="{{ request('search') }}" placeholder="Search name / email…" style="width:100%"></div>
    <select name="role" style="padding:10px 12px;border:1.5px solid var(--line);border-radius:10px;background:#fff;font-size:13px"><option value="">All roles</option><option value="super_admin" @selected(request('role')=='super_admin')>super_admin</option><option value="admin" @selected(request('role')=='admin')>admin</option><option value="staff" @selected(request('role')=='staff')>Admission Officer</option><option value="applicant" @selected(request('role')=='applicant')>applicant</option></select>
    <button class="btn btn-ghost btn-sm">Filter</button>
    @if(request('search')||request('role'))<a href="{{ route('admin.users.index') }}" class="btn btn-ghost btn-sm">Clear</a>@endif
</form>
<div class="table-card">
    <div class="table-toolbar">
        <div class="chip-filters">
            <button class="chip active" data-filter="all">All</button>
            <button class="chip" data-filter="super_admin">Super Admin</button>
            <button class="chip" data-filter="admin">Admin</button>
            <button class="chip" data-filter="staff">Admission Officers</button>
            <button class="chip" data-filter="applicant">Applicant</button>
        </div>
        <div class="table-search">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input id="tableSearch" type="text" placeholder="Filter in table…">
        </div>
    </div>
    <div class="table-scroll">
        <table>
            <thead><tr><th>User</th><th class="center">Role</th><th class="center">Active</th><th class="right">Actions</th></tr></thead>
            <tbody>
                @forelse($users as $u)
                <tr data-filter="{{ $u->role }}">
                    <td><div class="cell-main"><div class="thumb thumb-blue">{{ substr($u->name,0,1) }}</div><div><div class="cell-title" style="font-size:13px">{{ $u->name }}</div><div class="cell-sub">{{ $u->email }}</div></div></div></td>
                    <td class="center">@if($u->role==='super_admin')<span class="tag tag-terracotta">{{ $u->role }}</span>@elseif($u->role==='admin')<span class="tag tag-blue">{{ $u->role }}</span>@elseif($u->role==='staff')<span class="tag tag-gold">Admission Officer</span>@else<span class="tag tag-grey">{{ $u->role }}</span>@endif</td>
                    <td class="center">@if($u->is_active)<span class="tag tag-green">Active</span>@else<span class="tag tag-red">Inactive</span>@endif</td>
                    <td class="right"><div class="row-actions">
                        <a href="{{ route('admin.users.edit', encId($u->id)) }}" class="btn-icon" title="Edit"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></a>
                        <form method="POST" action="{{ route('admin.users.destroy', encId($u->id)) }}" onsubmit="event.preventDefault();confirmModal('Delete User','Are you sure you want to delete user \'{{ $u->name }}\'? This action cannot be undone.',()=>this.submit())">@csrf @method('DELETE')<button class="btn-icon danger" title="Delete"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg></button></form>
                    </div></td>
                </tr>
                @empty<tr><td colspan="4"><div class="empty-state"><div class="es-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg></div><strong>No users</strong><p>No matching records.</p></div></td></tr>@endforelse
            </tbody>
        </table>
    </div>
    @include('layouts.partials.pagination',['paginator'=>$users])
</div>
@endsection
