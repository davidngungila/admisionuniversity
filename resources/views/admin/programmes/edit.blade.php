@extends('layouts.admin')
@section('title','Edit Programme')
@section('content')
<div class="page-head">
    <div><h1>Edit — {{ $programme->name }}</h1><p class="page-sub">Update programme details.</p></div>
    <div class="page-actions"><a href="{{ route('admin.programmes.index') }}" class="btn btn-ghost">← Back to Programmes</a></div>
</div>
<div class="panel">
    <div class="panel-head"><div class="panel-title">Programme Details</div></div>
    <div class="panel-body">
        <form method="POST" action="{{ route('admin.programmes.update', encId($programme->id)) }}" onsubmit="event.preventDefault(); const _f=this; confirmModal('Update programme','Are you sure you want to update this programme?',()=>_f.submit())">
            @csrf @method('PUT')
            <div class="form-grid">
                <div class="field">
                    <label class="field-label">Department *</label>
                    <select name="department_id" required>
                        @foreach($departments as $d)<option value="{{ $d->id }}" @selected(old('department_id', $programme->department_id)==$d->id)>{{ $d->name }} ({{ $d->faculty->name }})</option>@endforeach
                    </select>
                    @error('department_id')<span class="field-err">{{ $message }}</span>@enderror
                </div>
                <div class="field">
                    <label class="field-label">Campus *</label>
                    <select name="campus_id" required>
                        @foreach($campuses as $c)<option value="{{ $c->id }}" @selected(old('campus_id', $programme->campus_id)==$c->id)>{{ $c->name }}</option>@endforeach
                    </select>
                    @error('campus_id')<span class="field-err">{{ $message }}</span>@enderror
                </div>
                <div class="field">
                    <label class="field-label">Level *</label>
                    <select name="admission_level_id" required>
                        @foreach($levels as $lv)<option value="{{ $lv->id }}" @selected(old('admission_level_id', $programme->admission_level_id)==$lv->id)>{{ $lv->name }}</option>@endforeach
                    </select>
                    @error('admission_level_id')<span class="field-err">{{ $message }}</span>@enderror
                </div>
                <div class="field">
                    <label class="field-label">Code *</label>
                    <input name="code" value="{{ old('code', $programme->code) }}" required>
                    @error('code')<span class="field-err">{{ $message }}</span>@enderror
                </div>
            </div>
            <div class="field" style="margin-top:16px">
                <label class="field-label">Name *</label>
                <input name="name" value="{{ old('name', $programme->name) }}" required>
                @error('name')<span class="field-err">{{ $message }}</span>@enderror
            </div>
            <div class="form-grid" style="margin-top:16px">
                <div class="field">
                    <label class="field-label">Duration *</label>
                    <input name="duration_years" type="number" value="{{ old('duration_years', $programme->duration_years) }}" required>
                    @error('duration_years')<span class="field-err">{{ $message }}</span>@enderror
                </div>
                <div class="field">
                    <label class="field-label">Study Mode *</label>
                    <select name="study_mode" required>
                        <option value="Full Time" @selected(old('study_mode', $programme->study_mode)=='Full Time')>Full Time</option>
                        <option value="Part Time" @selected(old('study_mode', $programme->study_mode)=='Part Time')>Part Time</option>
                        <option value="Distance" @selected(old('study_mode', $programme->study_mode)=='Distance')>Distance</option>
                    </select>
                    @error('study_mode')<span class="field-err">{{ $message }}</span>@enderror
                </div>
                <div class="field">
                    <label class="field-label">Tuition *</label>
                    <input name="tuition_fee" type="number" step="0.01" value="{{ old('tuition_fee', $programme->tuition_fee) }}" required>
                    @error('tuition_fee')<span class="field-err">{{ $message }}</span>@enderror
                </div>
                <div class="field">
                    <label class="field-label">Capacity</label>
                    <input name="capacity" type="number" value="{{ old('capacity', $programme->capacity) }}">
                    @error('capacity')<span class="field-err">{{ $message }}</span>@enderror
                </div>
            </div>
            <div class="field" style="margin-top:16px">
                <label class="field-label">Description</label>
                <textarea name="description" rows="3">{{ old('description', $programme->description) }}</textarea>
                @error('description')<span class="field-err">{{ $message }}</span>@enderror
            </div>
            <div class="field" style="margin-top:16px">
                <label class="field-label">Status</label>
                <select name="status">
                    <option value="active" @selected(old('status', $programme->status)=='active')>Active</option>
                    <option value="inactive" @selected(old('status', $programme->status)=='inactive')>Inactive</option>
                </select>
                @error('status')<span class="field-err">{{ $message }}</span>@enderror
            </div>
            <div class="form-actions">
                <a href="{{ route('admin.programmes.index') }}" class="btn btn-ghost">Cancel</a>
                <button class="btn btn-primary">Update Programme</button>
            </div>
        </form>
    </div>
</div>
@endsection
