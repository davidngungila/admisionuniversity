@extends('layouts.admin')
@section('title','Edit User')
@section('content')
<div class="page-head">
    <div><h1>Edit — {{ $user->name }}</h1><p class="page-sub">Update user profile and role.</p></div>
    <div class="page-actions"><a href="{{ route('admin.users.index') }}" class="btn btn-ghost">← Back to Users</a></div>
</div>
<div class="panel">
    <div class="panel-head"><div class="panel-title">User Details</div></div>
    <div class="panel-body">
        <form method="POST" action="{{ route('admin.users.update', encId($user->id)) }}" onsubmit="event.preventDefault(); const _f=this; confirmModal('Update user','Are you sure you want to update this user?',()=>_f.submit())">
            @csrf @method('PUT')
            <div class="form-grid">
                <div class="field">
                    <label class="field-label">Name *</label>
                    <input name="name" value="{{ old('name', $user->name) }}" required>
                    @error('name')<span class="field-err">{{ $message }}</span>@enderror
                </div>
                <div class="field">
                    <label class="field-label">Email *</label>
                    <input name="email" type="email" value="{{ old('email', $user->email) }}" required>
                    @error('email')<span class="field-err">{{ $message }}</span>@enderror
                </div>
                <div class="field">
                    <label class="field-label">Phone</label>
                    <input name="phone" value="{{ old('phone', $user->phone) }}">
                    @error('phone')<span class="field-err">{{ $message }}</span>@enderror
                </div>
                <div class="field">
                    <label class="field-label">Role *</label>
                    <select name="role" required>
                        @foreach(['super_admin','admin','staff','applicant'] as $r)<option value="{{ $r }}" @selected(old('role', $user->role)==$r)>{{ $r==='staff' ? 'Admission Officer' : $r }}</option>@endforeach
                    </select>
                    @error('role')<span class="field-err">{{ $message }}</span>@enderror
                </div>
                <div class="field">
                    <label class="field-label">New Password (leave blank to keep)</label>
                    <input name="password" type="password">
                    @error('password')<span class="field-err">{{ $message }}</span>@enderror
                </div>
                <div class="field">
                    <label class="field-label">Confirm</label>
                    <input name="password_confirmation" type="password">
                </div>
            </div>
            <div class="field" style="margin-top:16px">
                <label class="check-row"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $user->is_active))> Active</label>
            </div>
            <div class="form-actions">
                <a href="{{ route('admin.users.index') }}" class="btn btn-ghost">Cancel</a>
                <button class="btn btn-primary">Update User</button>
            </div>
        </form>
    </div>
</div>
@endsection
