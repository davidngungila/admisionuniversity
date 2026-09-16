@extends('layouts.admin')
@section('title','Edit Academic Year')
@section('content')
<div class="page-head">
    <div><h1>Edit — {{ $year->name }}</h1><p class="page-sub">Update academic year details.</p></div>
    <div class="page-actions"><a href="{{ route('admin.academic-years.index') }}" class="btn btn-ghost">← Back to Academic Years</a></div>
</div>
<div class="panel">
    <div class="panel-head"><div class="panel-title">Academic Year Details</div></div>
    <div class="panel-body">
        <form method="POST" action="{{ route('admin.academic-years.update', encId($year->id)) }}" onsubmit="event.preventDefault(); const _f=this; confirmModal('Update academic year','Are you sure you want to update this academic year?',()=>_f.submit())">
            @csrf @method('PUT')
            <div class="field">
                <label class="field-label">Name *</label>
                <input name="name" value="{{ old('name', $year->name) }}" required>
                @error('name')<span class="field-err">{{ $message }}</span>@enderror
            </div>
            <div class="form-grid" style="margin-top:16px">
                <div class="field">
                    <label class="field-label">Start Date</label>
                    <input name="start_date" type="date" value="{{ old('start_date', $year->start_date?->format('Y-m-d')) }}">
                    @error('start_date')<span class="field-err">{{ $message }}</span>@enderror
                </div>
                <div class="field">
                    <label class="field-label">End Date</label>
                    <input name="end_date" type="date" value="{{ old('end_date', $year->end_date?->format('Y-m-d')) }}">
                    @error('end_date')<span class="field-err">{{ $message }}</span>@enderror
                </div>
            </div>
            <div class="field" style="margin-top:16px">
                <label class="check-row"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $year->is_active))> Active</label>
            </div>
            <div class="form-actions">
                <a href="{{ route('admin.academic-years.index') }}" class="btn btn-ghost">Cancel</a>
                <button class="btn btn-primary">Update Academic Year</button>
            </div>
        </form>
    </div>
</div>
@endsection
