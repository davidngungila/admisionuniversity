@extends('layouts.admin')
@section('title','Edit Department')
@section('content')
<div class="page-head">
    <div><h1>Edit — {{ $department->name }}</h1><p class="page-sub">Update department details.</p></div>
    <div class="page-actions"><a href="{{ route('admin.departments.index') }}" class="btn btn-ghost">← Back to Departments</a></div>
</div>
<div class="panel">
    <div class="panel-head"><div class="panel-title">Department Details</div></div>
    <div class="panel-body">
        <form method="POST" action="{{ route('admin.departments.update', encId($department->id)) }}" onsubmit="event.preventDefault(); const _f=this; confirmModal('Update department','Are you sure you want to update this department?',()=>_f.submit())">
            @csrf @method('PUT')
            <div class="field">
                <label class="field-label">Faculty *</label>
                <select name="faculty_id" required>
                    @foreach($faculties as $f)<option value="{{ $f->id }}" @selected(old('faculty_id', $department->faculty_id)==$f->id)>{{ $f->name }}</option>@endforeach
                </select>
                @error('faculty_id')<span class="field-err">{{ $message }}</span>@enderror
            </div>
            <div class="form-grid" style="margin-top:16px">
                <div class="field">
                    <label class="field-label">Name *</label>
                    <input name="name" value="{{ old('name', $department->name) }}" required>
                    @error('name')<span class="field-err">{{ $message }}</span>@enderror
                </div>
                <div class="field">
                    <label class="field-label">Code</label>
                    <input name="code" value="{{ old('code', $department->code) }}">
                    @error('code')<span class="field-err">{{ $message }}</span>@enderror
                </div>
            </div>
            <div class="field" style="margin-top:16px">
                <label class="check-row"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $department->is_active))> Active</label>
            </div>
            <div class="form-actions">
                <a href="{{ route('admin.departments.index') }}" class="btn btn-ghost">Cancel</a>
                <button class="btn btn-primary">Update Department</button>
            </div>
        </form>
    </div>
</div>
@endsection
