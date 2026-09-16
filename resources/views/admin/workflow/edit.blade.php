@extends('layouts.admin')
@section('title','Edit Step')
@section('content')
<div class="page-head">
    <div><h1>Edit — {{ $step->step_name }}</h1><p class="page-sub">Update workflow step details.</p></div>
    <div class="page-actions"><a href="{{ route('admin.workflow.index') }}" class="btn btn-ghost">← Back to Workflow</a></div>
</div>
<div class="panel">
    <div class="panel-head"><div class="panel-title">Step Details</div></div>
    <div class="panel-body">
        <form method="POST" action="{{ route('admin.workflow.update', encId($step->id)) }}" onsubmit="event.preventDefault(); const _f=this; confirmModal('Update workflow step','Are you sure you want to update this workflow step?',()=>_f.submit())">
            @csrf @method('PUT')
            <div class="field">
                <label class="field-label">Level *</label>
                <select name="admission_level_id" required>
                    @foreach($levels as $lv)<option value="{{ $lv->id }}" @selected(old('admission_level_id', $step->admission_level_id)==$lv->id)>{{ $lv->name }}</option>@endforeach
                </select>
                @error('admission_level_id')<span class="field-err">{{ $message }}</span>@enderror
            </div>
            <div class="form-grid" style="margin-top:16px">
                <div class="field">
                    <label class="field-label">Step Number *</label>
                    <input name="step_number" type="number" value="{{ old('step_number', $step->step_number) }}" required>
                    @error('step_number')<span class="field-err">{{ $message }}</span>@enderror
                </div>
                <div class="field">
                    <label class="field-label">Route *</label>
                    <input name="route" value="{{ old('route', $step->route) }}" required>
                    @error('route')<span class="field-err">{{ $message }}</span>@enderror
                </div>
            </div>
            <div class="field" style="margin-top:16px">
                <label class="field-label">Step Name *</label>
                <input name="step_name" value="{{ old('step_name', $step->step_name) }}" required>
                @error('step_name')<span class="field-err">{{ $message }}</span>@enderror
            </div>
            <div class="field" style="margin-top:16px">
                <label class="field-label">Description</label>
                <input name="description" value="{{ old('description', $step->description) }}">
                @error('description')<span class="field-err">{{ $message }}</span>@enderror
            </div>
            <div class="form-grid" style="margin-top:16px">
                <div class="field"><label class="check-row"><input type="checkbox" name="is_required" value="1" @checked(old('is_required', $step->is_required))> Required</label></div>
                <div class="field"><label class="check-row"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $step->is_active))> Active</label></div>
            </div>
            <div class="form-actions">
                <a href="{{ route('admin.workflow.index') }}" class="btn btn-ghost">Cancel</a>
                <button class="btn btn-primary">Update Step</button>
            </div>
        </form>
    </div>
</div>
@endsection
