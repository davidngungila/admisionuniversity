@extends('layouts.admin')
@section('title','Edit Window')
@section('content')
<div class="page-head">
    <div><h1>Edit Window</h1><p class="page-sub">Update admission window configuration.</p></div>
    <div class="page-actions"><a href="{{ route('admin.windows.index') }}" class="btn btn-ghost">← Back to Windows</a></div>
</div>
<div class="panel">
    <div class="panel-head"><div class="panel-title">Window Details</div></div>
    <div class="panel-body">
        <form method="POST" action="{{ route('admin.windows.update', encId($window->id)) }}" onsubmit="event.preventDefault(); const _f=this; confirmModal('Update admission window','Are you sure you want to update this admission window?',()=>_f.submit())">
            @csrf @method('PUT')
            <div class="form-grid">
                <div class="field">
                    <label class="field-label">Academic Year *</label>
                    <select name="academic_year_id" required>
                        @foreach($years as $y)<option value="{{ $y->id }}" @selected(old('academic_year_id', $window->academic_year_id)==$y->id)>{{ $y->name }}</option>@endforeach
                    </select>
                    @error('academic_year_id')<span class="field-err">{{ $message }}</span>@enderror
                </div>
                <div class="field">
                    <label class="field-label">Level *</label>
                    <select name="admission_level_id" required>
                        @foreach($levels as $lv)<option value="{{ $lv->id }}" @selected(old('admission_level_id', $window->admission_level_id)==$lv->id)>{{ $lv->name }}</option>@endforeach
                    </select>
                    @error('admission_level_id')<span class="field-err">{{ $message }}</span>@enderror
                </div>
                <div class="field">
                    <label class="field-label">Round *</label>
                    <select name="application_round_id" required>
                        @foreach($rounds as $r)<option value="{{ $r->id }}" @selected(old('application_round_id', $window->application_round_id)==$r->id)>{{ $r->academicYear->name }} — Round {{ $r->round_number }}</option>@endforeach
                    </select>
                    @error('application_round_id')<span class="field-err">{{ $message }}</span>@enderror
                </div>
                <div class="field">
                    <label class="field-label">Category *</label>
                    <select name="applicant_category" required>
                        <option value="Tanzanian" @selected(old('applicant_category', $window->applicant_category)=='Tanzanian')>Tanzanian</option>
                        <option value="Foreign" @selected(old('applicant_category', $window->applicant_category)=='Foreign')>Foreign</option>
                    </select>
                    @error('applicant_category')<span class="field-err">{{ $message }}</span>@enderror
                </div>
                <div class="field">
                    <label class="field-label">Opening Date *</label>
                    <input name="opening_date" type="date" value="{{ old('opening_date', $window->opens_at->format('Y-m-d')) }}" required>
                    @error('opening_date')<span class="field-err">{{ $message }}</span>@enderror
                </div>
                <div class="field">
                    <label class="field-label">Opening Time *</label>
                    <input name="opening_time" type="time" value="{{ old('opening_time', $window->opens_at->format('H:i')) }}" required>
                    @error('opening_time')<span class="field-err">{{ $message }}</span>@enderror
                </div>
                <div class="field">
                    <label class="field-label">Closing Date *</label>
                    <input name="closing_date" type="date" value="{{ old('closing_date', $window->closes_at->format('Y-m-d')) }}" required>
                    @error('closing_date')<span class="field-err">{{ $message }}</span>@enderror
                </div>
                <div class="field">
                    <label class="field-label">Closing Time *</label>
                    <input name="closing_time" type="time" value="{{ old('closing_time', $window->closes_at->format('H:i')) }}" required>
                    @error('closing_time')<span class="field-err">{{ $message }}</span>@enderror
                </div>
                <div class="field">
                    <label class="field-label">Fee *</label>
                    <input name="application_fee" type="number" step="0.01" value="{{ old('application_fee', $window->application_fee) }}" required>
                    @error('application_fee')<span class="field-err">{{ $message }}</span>@enderror
                </div>
                <div class="field">
                    <label class="field-label">Currency</label>
                    <input name="currency" value="{{ old('currency', $window->currency) }}">
                    @error('currency')<span class="field-err">{{ $message }}</span>@enderror
                </div>
                <div class="field">
                    <label class="field-label">Timezone</label>
                    <input name="timezone" value="{{ old('timezone', $window->timezone) }}">
                    @error('timezone')<span class="field-err">{{ $message }}</span>@enderror
                </div>
                <div class="field">
                    <label class="field-label">Status</label>
                    <select name="status">
                        <option value="active" @selected(old('status', $window->status)=='active')>Active</option>
                        <option value="inactive" @selected(old('status', $window->status)=='inactive')>Inactive</option>
                    </select>
                    @error('status')<span class="field-err">{{ $message }}</span>@enderror
                </div>
            </div>
            <div class="form-actions">
                <a href="{{ route('admin.windows.index') }}" class="btn btn-ghost">Cancel</a>
                <button class="btn btn-primary">Update Window</button>
            </div>
        </form>
    </div>
</div>
@endsection
