@extends('layouts.admin')
@section('title','Create Department')
@section('content')
<div class="page-head">
    <div><h1>Create Department</h1><p class="page-sub">Add a new department under a faculty.</p></div>
    <div class="page-actions"><a href="{{ route('admin.departments.index') }}" class="btn btn-ghost">← Back to Departments</a></div>
</div>
<div class="panel">
    <div class="panel-head"><div class="panel-title">Department Details</div></div>
    <div class="panel-body">
        <form method="POST" action="{{ route('admin.departments.store') }}" onsubmit="event.preventDefault(); const _f=this; confirmModal('Create department','Are you sure you want to create this department?',()=>_f.submit())">
            @csrf
            <div class="field">
                <label class="field-label">Faculty *</label>
                <select name="faculty_id" required>
                    <option value="">—</option>
                    @foreach($faculties as $f)<option value="{{ $f->id }}" @selected(old('faculty_id')==$f->id)>{{ $f->name }}</option>@endforeach
                </select>
                @error('faculty_id')<span class="field-err">{{ $message }}</span>@enderror
            </div>
            <div class="form-grid" style="margin-top:16px">
                <div class="field">
                    <label class="field-label">Name *</label>
                    <input name="name" required value="{{ old('name') }}">
                    @error('name')<span class="field-err">{{ $message }}</span>@enderror
                </div>
                <div class="field">
                    <label class="field-label">Code</label>
                    <input name="code" value="{{ old('code') }}">
                    @error('code')<span class="field-err">{{ $message }}</span>@enderror
                </div>
            </div>
            <div class="field" style="margin-top:16px">
                <label class="check-row"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', true))> Active</label>
            </div>
            <div class="form-actions">
                <a href="{{ route('admin.departments.index') }}" class="btn btn-ghost">Cancel</a>
                <button class="btn btn-primary">Create Department</button>
            </div>
        </form>
    </div>
</div>
@endsection
