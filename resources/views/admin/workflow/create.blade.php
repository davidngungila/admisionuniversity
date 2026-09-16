@extends('layouts.admin')
@section('title','Create Workflow Step')
@section('content')
<div class="page-head">
    <div><h1>Create Workflow Step</h1><p class="page-sub">Add a new step to an admission level workflow.</p></div>
    <div class="page-actions"><a href="{{ route('admin.workflow.index') }}" class="btn btn-ghost">← Back to Workflow</a></div>
</div>
<div class="panel">
    <div class="panel-head"><div class="panel-title">Step Details</div></div>
    <div class="panel-body">
        <form method="POST" action="{{ route('admin.workflow.store') }}" onsubmit="event.preventDefault(); const _f=this; confirmModal('Create workflow step','Are you sure you want to create this workflow step?',()=>_f.submit())">
            @csrf
            <div class="field">
                <label class="field-label">Admission Level *</label>
                <select name="admission_level_id" required>
                    <option value="">—</option>
                    @foreach($levels as $lv)<option value="{{ $lv->id }}" @selected(old('admission_level_id')==$lv->id)>{{ $lv->name }}</option>@endforeach
                </select>
                @error('admission_level_id')<span class="field-err">{{ $message }}</span>@enderror
            </div>
            <div class="form-grid" style="margin-top:16px">
                <div class="field">
                    <label class="field-label">Step Number *</label>
                    <input name="step_number" type="number" min="1" required value="{{ old('step_number') }}">
                    @error('step_number')<span class="field-err">{{ $message }}</span>@enderror
                </div>
                <div class="field">
                    <label class="field-label">Route *</label>
                    <input name="route" required placeholder="personal-info" value="{{ old('route') }}">
                    @error('route')<span class="field-err">{{ $message }}</span>@enderror
                </div>
            </div>
            <div class="field" style="margin-top:16px">
                <label class="field-label">Step Name *</label>
                <input name="step_name" required placeholder="Personal Information" value="{{ old('step_name') }}">
                @error('step_name')<span class="field-err">{{ $message }}</span>@enderror
            </div>
            <div class="field" style="margin-top:16px">
                <label class="field-label">Description</label>
                <input name="description" value="{{ old('description') }}">
                @error('description')<span class="field-err">{{ $message }}</span>@enderror
            </div>
            <div class="form-grid" style="margin-top:16px">
                <div class="field"><label class="check-row"><input type="checkbox" name="is_required" value="1" @checked(old('is_required', true))> Required</label></div>
                <div class="field"><label class="check-row"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', true))> Active</label></div>
            </div>
            <div class="form-actions">
                <a href="{{ route('admin.workflow.index') }}" class="btn btn-ghost">Cancel</a>
                <button class="btn btn-primary">Create Step</button>
            </div>
        </form>
    </div>
</div>
@endsection
