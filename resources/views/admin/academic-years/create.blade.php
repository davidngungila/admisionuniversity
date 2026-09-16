@extends('layouts.admin')
@section('title','Create Academic Year')
@section('content')
<div class="page-head">
    <div><h1>Create Academic Year</h1><p class="page-sub">Add a new academic year for admissions.</p></div>
    <div class="page-actions"><a href="{{ route('admin.academic-years.index') }}" class="btn btn-ghost">← Back to Academic Years</a></div>
</div>
<div class="panel">
    <div class="panel-head"><div class="panel-title">Academic Year Details</div></div>
    <div class="panel-body">
        <form method="POST" action="{{ route('admin.academic-years.store') }}" onsubmit="event.preventDefault(); const _f=this; confirmModal('Create academic year','Are you sure you want to create this academic year?',()=>_f.submit())">
            @csrf
            <div class="field">
                <label class="field-label">Name *</label>
                <input name="name" required placeholder="2026/2027" value="{{ old('name') }}">
                @error('name')<span class="field-err">{{ $message }}</span>@enderror
            </div>
            <div class="form-grid" style="margin-top:16px">
                <div class="field">
                    <label class="field-label">Start Date</label>
                    <input name="start_date" type="date" value="{{ old('start_date') }}">
                    @error('start_date')<span class="field-err">{{ $message }}</span>@enderror
                </div>
                <div class="field">
                    <label class="field-label">End Date</label>
                    <input name="end_date" type="date" value="{{ old('end_date') }}">
                    @error('end_date')<span class="field-err">{{ $message }}</span>@enderror
                </div>
            </div>
            <div class="field" style="margin-top:16px">
                <label class="check-row"><input type="checkbox" name="is_active" value="1" @checked(old('is_active'))> Set as active year</label>
            </div>
            <div class="form-actions">
                <a href="{{ route('admin.academic-years.index') }}" class="btn btn-ghost">Cancel</a>
                <button class="btn btn-primary">Create Academic Year</button>
            </div>
        </form>
    </div>
</div>
@endsection
