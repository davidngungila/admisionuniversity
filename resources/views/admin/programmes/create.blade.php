@extends('layouts.admin')
@section('title','Create Programme')
@section('content')
<div class="page-head">
    <div><h1>Create Programme</h1><p class="page-sub">Add a new academic programme.</p></div>
    <div class="page-actions"><a href="{{ route('admin.programmes.index') }}" class="btn btn-ghost">← Back to Programmes</a></div>
</div>
<div class="panel">
    <div class="panel-head"><div class="panel-title">Programme Details</div></div>
    <div class="panel-body">
        <form method="POST" action="{{ route('admin.programmes.store') }}" onsubmit="event.preventDefault(); const _f=this; confirmModal('Create programme','Are you sure you want to create this programme?',()=>_f.submit())">
            @csrf
            <div class="form-grid">
                <div class="field">
                    <label class="field-label">Department *</label>
                    <select name="department_id" required>
                        <option value="">—</option>
                        @foreach($departments as $d)<option value="{{ $d->id }}" @selected(old('department_id')==$d->id)>{{ $d->name }} ({{ $d->faculty->name }})</option>@endforeach
                    </select>
                    @error('department_id')<span class="field-err">{{ $message }}</span>@enderror
                </div>
                <div class="field">
                    <label class="field-label">Campus *</label>
                    <select name="campus_id" required>
                        <option value="">—</option>
                        @foreach($campuses as $c)<option value="{{ $c->id }}" @selected(old('campus_id')==$c->id)>{{ $c->name }}</option>@endforeach
                    </select>
                    @error('campus_id')<span class="field-err">{{ $message }}</span>@enderror
                </div>
                <div class="field">
                    <label class="field-label">Admission Level *</label>
                    <select name="admission_level_id" required>
                        <option value="">—</option>
                        @foreach($levels as $lv)<option value="{{ $lv->id }}" @selected(old('admission_level_id')==$lv->id)>{{ $lv->name }}</option>@endforeach
                    </select>
                    @error('admission_level_id')<span class="field-err">{{ $message }}</span>@enderror
                </div>
                <div class="field">
                    <label class="field-label">Code *</label>
                    <input name="code" required placeholder="DM005" value="{{ old('code') }}">
                    @error('code')<span class="field-err">{{ $message }}</span>@enderror
                </div>
            </div>
            <div class="field" style="margin-top:16px">
                <label class="field-label">Name *</label>
                <input name="name" required placeholder="Bachelor of Science in Software Engineering" value="{{ old('name') }}">
                @error('name')<span class="field-err">{{ $message }}</span>@enderror
            </div>
            <div class="form-grid" style="margin-top:16px">
                <div class="field">
                    <label class="field-label">Duration (years) *</label>
                    <input name="duration_years" type="number" min="1" max="10" value="{{ old('duration_years','4') }}" required>
                    @error('duration_years')<span class="field-err">{{ $message }}</span>@enderror
                </div>
                <div class="field">
                    <label class="field-label">Study Mode *</label>
                    <select name="study_mode" required>
                        <option value="Full Time" @selected(old('study_mode')=='Full Time')>Full Time</option>
                        <option value="Part Time" @selected(old('study_mode')=='Part Time')>Part Time</option>
                        <option value="Distance" @selected(old('study_mode')=='Distance')>Distance</option>
                    </select>
                    @error('study_mode')<span class="field-err">{{ $message }}</span>@enderror
                </div>
                <div class="field">
                    <label class="field-label">Tuition Fee *</label>
                    <input name="tuition_fee" type="number" step="0.01" value="{{ old('tuition_fee','1500000') }}" required>
                    @error('tuition_fee')<span class="field-err">{{ $message }}</span>@enderror
                </div>
                <div class="field">
                    <label class="field-label">Capacity</label>
                    <input name="capacity" type="number" min="1" value="{{ old('capacity') }}">
                    @error('capacity')<span class="field-err">{{ $message }}</span>@enderror
                </div>
            </div>
            <div class="field" style="margin-top:16px">
                <label class="field-label">Description</label>
                <textarea name="description" rows="3">{{ old('description') }}</textarea>
                @error('description')<span class="field-err">{{ $message }}</span>@enderror
            </div>
            <div class="field" style="margin-top:16px">
                <label class="field-label">Status</label>
                <select name="status">
                    <option value="active" @selected(old('status')=='active')>Active</option>
                    <option value="inactive" @selected(old('status')=='inactive')>Inactive</option>
                </select>
                @error('status')<span class="field-err">{{ $message }}</span>@enderror
            </div>
            <div class="form-actions">
                <a href="{{ route('admin.programmes.index') }}" class="btn btn-ghost">Cancel</a>
                <button class="btn btn-primary">Create Programme</button>
            </div>
        </form>
    </div>
</div>
@endsection
