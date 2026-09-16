@extends('layouts.admin')
@section('title','Edit Campus')
@section('content')
<div class="page-head">
    <div><h1>Edit — {{ $campus->name }}</h1><p class="page-sub">Update campus details.</p></div>
    <div class="page-actions"><a href="{{ route('admin.campuses.index') }}" class="btn btn-ghost">← Back to Campuses</a></div>
</div>
<div class="panel">
    <div class="panel-head"><div class="panel-title">Campus Details</div></div>
    <div class="panel-body">
        <form method="POST" action="{{ route('admin.campuses.update', encId($campus->id)) }}" onsubmit="event.preventDefault(); const _f=this; confirmModal('Update campus','Are you sure you want to update this campus?',()=>_f.submit())">
            @csrf @method('PUT')
            <div class="field">
                <label class="field-label">Name *</label>
                <input name="name" value="{{ old('name', $campus->name) }}" required>
                @error('name')<span class="field-err">{{ $message }}</span>@enderror
            </div>
            <div class="form-grid" style="margin-top:16px">
                <div class="field">
                    <label class="field-label">Code</label>
                    <input name="code" value="{{ old('code', $campus->code) }}">
                    @error('code')<span class="field-err">{{ $message }}</span>@enderror
                </div>
                <div class="field">
                    <label class="field-label">Location</label>
                    <input name="location" value="{{ old('location', $campus->location) }}">
                    @error('location')<span class="field-err">{{ $message }}</span>@enderror
                </div>
            </div>
            <div class="field" style="margin-top:16px">
                <label class="check-row"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $campus->is_active))> Active</label>
            </div>
            <div class="form-actions">
                <a href="{{ route('admin.campuses.index') }}" class="btn btn-ghost">Cancel</a>
                <button class="btn btn-primary">Update Campus</button>
            </div>
        </form>
    </div>
</div>
@endsection
