@extends('layouts.admin')
@section('title','Create Faculty')
@section('content')
<div class="page-head">
    <div><h1>Create Faculty / School</h1><p class="page-sub">Add a new faculty or school.</p></div>
    <div class="page-actions"><a href="{{ route('admin.faculties.index') }}" class="btn btn-ghost">← Back to Faculties</a></div>
</div>
<div class="panel">
    <div class="panel-head"><div class="panel-title">Faculty Details</div></div>
    <div class="panel-body">
        <form method="POST" action="{{ route('admin.faculties.store') }}" onsubmit="event.preventDefault(); const _f=this; confirmModal('Create faculty','Are you sure you want to create this faculty?',()=>_f.submit())">
            @csrf
            <div class="form-grid">
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
                <label class="field-label">Description</label>
                <textarea name="description" rows="3">{{ old('description') }}</textarea>
                @error('description')<span class="field-err">{{ $message }}</span>@enderror
            </div>
            <div class="field" style="margin-top:16px">
                <label class="check-row"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', true))> Active</label>
            </div>
            <div class="form-actions">
                <a href="{{ route('admin.faculties.index') }}" class="btn btn-ghost">Cancel</a>
                <button class="btn btn-primary">Create Faculty</button>
            </div>
        </form>
    </div>
</div>
@endsection
