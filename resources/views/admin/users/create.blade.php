@extends('layouts.admin')
@section('title','Create User')
@section('content')
<div class="page-head">
    <div><h1>Create User</h1><p class="page-sub">Add a new system user.</p></div>
    <div class="page-actions"><a href="{{ route('admin.users.index') }}" class="btn btn-ghost">← Back to Users</a></div>
</div>
<div class="panel">
    <div class="panel-head"><div class="panel-title">User Details</div></div>
    <div class="panel-body">
        <form method="POST" action="{{ route('admin.users.store') }}" onsubmit="event.preventDefault(); const _f=this; confirmModal('Create user','Are you sure you want to create this user?',()=>_f.submit())">
            @csrf
            <div class="form-grid">
                <div class="field">
                    <label class="field-label">Name *</label>
                    <input name="name" required value="{{ old('name') }}">
                    @error('name')<span class="field-err">{{ $message }}</span>@enderror
                </div>
                <div class="field">
                    <label class="field-label">Email *</label>
                    <input name="email" type="email" required value="{{ old('email') }}">
                    @error('email')<span class="field-err">{{ $message }}</span>@enderror
                </div>
                <div class="field">
                    <label class="field-label">Phone</label>
                    <input name="phone" value="{{ old('phone') }}">
                    @error('phone')<span class="field-err">{{ $message }}</span>@enderror
                </div>
                <div class="field">
                    <label class="field-label">Role *</label>
                    <select name="role" required>
                        <option value="applicant" @selected(old('role')=='applicant')>applicant</option>
                        <option value="staff" @selected(old('role')=='staff')>Admission Officer</option>
                        <option value="admin" @selected(old('role')=='admin')>admin</option>
                        <option value="super_admin" @selected(old('role')=='super_admin')>super_admin</option>
                    </select>
                    @error('role')<span class="field-err">{{ $message }}</span>@enderror
                </div>
                <div class="field">
                    <label class="field-label">Password *</label>
                    <input name="password" type="password" required>
                    @error('password')<span class="field-err">{{ $message }}</span>@enderror
                </div>
                <div class="field">
                    <label class="field-label">Confirm Password *</label>
                    <input name="password_confirmation" type="password" required>
                </div>
            </div>
            <div class="field" style="margin-top:16px">
                <label class="check-row"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', true))> Active</label>
            </div>
            <div class="form-actions">
                <a href="{{ route('admin.users.index') }}" class="btn btn-ghost">Cancel</a>
                <button class="btn btn-primary">Create User</button>
            </div>
        </form>
    </div>
</div>
@endsection
