@extends('layouts.admin')
@section('title','Create Campus')
@section('content')
<div class="page-head">
    <div><h1>Create Campus</h1><p class="page-sub">Add a new campus location.</p></div>
    <div class="page-actions"><a href="{{ route('admin.campuses.index') }}" class="btn btn-ghost">← Back to Campuses</a></div>
</div>
<div class="panel">
    <div class="panel-head"><div class="panel-title">Campus Details</div></div>
    <div class="panel-body">
        <form method="POST" action="{{ route('admin.campuses.store') }}" onsubmit="event.preventDefault(); const _f=this; confirmModal('Create campus','Are you sure you want to create this campus?',()=>_f.submit())">
            @csrf
            <div class="field">
                <label class="field-label">Name *</label>
                <input name="name" required value="{{ old('name') }}">
                @error('name')<span class="field-err">{{ $message }}</span>@enderror
            </div>
            <div class="form-grid" style="margin-top:16px">
                <div class="field">
                    <label class="field-label">Code</label>
                    <input name="code" value="{{ old('code') }}">
                    @error('code')<span class="field-err">{{ $message }}</span>@enderror
                </div>
                <div class="field">
                    <label class="field-label">Location</label>
                    <input name="location" value="{{ old('location') }}">
                    @error('location')<span class="field-err">{{ $message }}</span>@enderror
                </div>
            </div>
            <div class="field" style="margin-top:16px">
                <label class="check-row"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', true))> Active</label>
            </div>
            <div class="form-actions">
                <a href="{{ route('admin.campuses.index') }}" class="btn btn-ghost">Cancel</a>
                <button class="btn btn-primary">Create Campus</button>
            </div>
        </form>
    </div>
</div>
@endsection
