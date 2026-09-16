@extends('layouts.admin')
@section('title','Create Round')
@section('content')
<div class="page-head">
    <div><h1>Create Application Round</h1><p class="page-sub">Add a new round for an academic year.</p></div>
    <div class="page-actions"><a href="{{ route('admin.rounds.index') }}" class="btn btn-ghost">← Back to Rounds</a></div>
</div>
<div class="panel">
    <div class="panel-head"><div class="panel-title">Round Details</div></div>
    <div class="panel-body">
        <form method="POST" action="{{ route('admin.rounds.store') }}" onsubmit="event.preventDefault(); const _f=this; confirmModal('Create round','Are you sure you want to create this round?',()=>_f.submit())">
            @csrf
            <div class="field">
                <label class="field-label">Academic Year *</label>
                <select name="academic_year_id" required>
                    <option value="">— Select —</option>
                    @foreach($years as $y)<option value="{{ $y->id }}" @selected(old('academic_year_id')==$y->id)>{{ $y->name }}</option>@endforeach
                </select>
                @error('academic_year_id')<span class="field-err">{{ $message }}</span>@enderror
            </div>
            <div class="form-grid" style="margin-top:16px">
                <div class="field">
                    <label class="field-label">Round Number *</label>
                    <input name="round_number" type="number" min="1" required value="{{ old('round_number') }}">
                    @error('round_number')<span class="field-err">{{ $message }}</span>@enderror
                </div>
                <div class="field">
                    <label class="field-label">Name *</label>
                    <input name="name" required placeholder="Round 2" value="{{ old('name') }}">
                    @error('name')<span class="field-err">{{ $message }}</span>@enderror
                </div>
            </div>
            <div class="form-grid" style="margin-top:16px">
                <div class="field"><label class="check-row"><input type="checkbox" name="is_current" value="1" @checked(old('is_current'))> Current round</label></div>
                <div class="field"><label class="check-row"><input type="checkbox" name="allow_multiple_applications" value="1" @checked(old('allow_multiple_applications'))> Allow multiple applications per window</label></div>
            </div>
            <div class="form-actions">
                <a href="{{ route('admin.rounds.index') }}" class="btn btn-ghost">Cancel</a>
                <button class="btn btn-primary">Create Round</button>
            </div>
        </form>
    </div>
</div>
@endsection
