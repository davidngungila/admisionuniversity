@extends('layouts.admin')
@section('title','Edit Round')
@section('content')
<div class="page-head">
    <div><h1>Edit — {{ $round->name }}</h1><p class="page-sub">Update round details.</p></div>
    <div class="page-actions"><a href="{{ route('admin.rounds.index') }}" class="btn btn-ghost">← Back to Rounds</a></div>
</div>
<div class="panel">
    <div class="panel-head"><div class="panel-title">Round Details</div></div>
    <div class="panel-body">
        <form method="POST" action="{{ route('admin.rounds.update', encId($round->id)) }}" onsubmit="event.preventDefault(); const _f=this; confirmModal('Update round','Are you sure you want to update this round?',()=>_f.submit())">
            @csrf @method('PUT')
            <div class="field">
                <label class="field-label">Academic Year *</label>
                <select name="academic_year_id" required>
                    @foreach($years as $y)<option value="{{ $y->id }}" @selected(old('academic_year_id', $round->academic_year_id)==$y->id)>{{ $y->name }}</option>@endforeach
                </select>
                @error('academic_year_id')<span class="field-err">{{ $message }}</span>@enderror
            </div>
            <div class="form-grid" style="margin-top:16px">
                <div class="field">
                    <label class="field-label">Round Number *</label>
                    <input name="round_number" type="number" value="{{ old('round_number', $round->round_number) }}" required>
                    @error('round_number')<span class="field-err">{{ $message }}</span>@enderror
                </div>
                <div class="field">
                    <label class="field-label">Name *</label>
                    <input name="name" value="{{ old('name', $round->name) }}" required>
                    @error('name')<span class="field-err">{{ $message }}</span>@enderror
                </div>
            </div>
            <div class="form-grid" style="margin-top:16px">
                <div class="field"><label class="check-row"><input type="checkbox" name="is_current" value="1" @checked(old('is_current', $round->is_current))> Current</label></div>
                <div class="field"><label class="check-row"><input type="checkbox" name="allow_multiple_applications" value="1" @checked(old('allow_multiple_applications', $round->allow_multiple_applications))> Allow multiple applications</label></div>
            </div>
            <div class="form-actions">
                <a href="{{ route('admin.rounds.index') }}" class="btn btn-ghost">Cancel</a>
                <button class="btn btn-primary">Update Round</button>
            </div>
        </form>
    </div>
</div>
@endsection
