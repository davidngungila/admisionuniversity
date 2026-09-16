@extends('layouts.admin')
@section('title','Edit Faculty')
@section('content')
<div class="page-head">
    <div><h1>Edit — {{ $faculty->name }}</h1><p class="page-sub">Update faculty details.</p></div>
    <div class="page-actions"><a href="{{ route('admin.faculties.index') }}" class="btn btn-ghost">← Back to Faculties</a></div>
</div>
<div class="panel">
    <div class="panel-head"><div class="panel-title">Faculty Details</div></div>
    <div class="panel-body">
        <form method="POST" action="{{ route('admin.faculties.update', encId($faculty->id)) }}" onsubmit="event.preventDefault(); const _f=this; confirmModal('Update faculty','Are you sure you want to update this faculty?',()=>_f.submit())">
            @csrf @method('PUT')
            <div class="form-grid">
                <div class="field">
                    <label class="field-label">Name *</label>
                    <input name="name" value="{{ old('name', $faculty->name) }}" required>
                    @error('name')<span class="field-err">{{ $message }}</span>@enderror
                </div>
                <div class="field">
                    <label class="field-label">Code</label>
                    <input name="code" value="{{ old('code', $faculty->code) }}">
                    @error('code')<span class="field-err">{{ $message }}</span>@enderror
                </div>
            </div>
            <div class="field" style="margin-top:16px">
                <label class="field-label">Description</label>
                <textarea name="description" rows="3">{{ old('description', $faculty->description) }}</textarea>
                @error('description')<span class="field-err">{{ $message }}</span>@enderror
            </div>
            <div class="field" style="margin-top:16px">
                <label class="check-row"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $faculty->is_active))> Active</label>
            </div>
            <div class="form-actions">
                <a href="{{ route('admin.faculties.index') }}" class="btn btn-ghost">Cancel</a>
                <button class="btn btn-primary">Update Faculty</button>
            </div>
        </form>
    </div>
</div>
@endsection
