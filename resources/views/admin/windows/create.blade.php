@extends('layouts.admin')
@section('title','Create Admission Window')
@section('content')
<div class="page-head">
    <div><h1>Create Admission Window</h1><p class="page-sub">Configure a new admission window for applicants.</p></div>
    <div class="page-actions"><a href="{{ route('admin.windows.index') }}" class="btn btn-ghost">← Back to Windows</a></div>
</div>
<div class="panel">
    <div class="panel-head"><div class="panel-title">Window Details</div></div>
    <div class="panel-body">
        <form method="POST" action="{{ route('admin.windows.store') }}" onsubmit="event.preventDefault(); const _f=this; confirmModal('Create admission window','Are you sure you want to create this admission window?',()=>_f.submit())">
            @csrf
            <div class="form-grid">
                <div class="field">
                    <label class="field-label">Academic Year *</label>
                    <select name="academic_year_id" required>
                        <option value="">—</option>
                        @foreach($years as $y)<option value="{{ $y->id }}" @selected(old('academic_year_id')==$y->id)>{{ $y->name }}</option>@endforeach
                    </select>
                    @error('academic_year_id')<span class="field-err">{{ $message }}</span>@enderror
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
                    <label class="field-label">Round *</label>
                    <select name="application_round_id" required>
                        <option value="">—</option>
                        @foreach($rounds as $r)<option value="{{ $r->id }}" @selected(old('application_round_id')==$r->id)>{{ $r->academicYear->name }} — Round {{ $r->round_number }} ({{ $r->name }})</option>@endforeach
                    </select>
                    @error('application_round_id')<span class="field-err">{{ $message }}</span>@enderror
                </div>
                <div class="field">
                    <label class="field-label">Applicant Category *</label>
                    <select name="applicant_category" required>
                        <option value="Tanzanian" @selected(old('applicant_category')=='Tanzanian')>Tanzanian</option>
                        <option value="Foreign" @selected(old('applicant_category')=='Foreign')>Foreign</option>
                    </select>
                    @error('applicant_category')<span class="field-err">{{ $message }}</span>@enderror
                </div>
                <div class="field">
                    <label class="field-label">Opening Date *</label>
                    <input name="opening_date" type="date" required value="{{ old('opening_date') }}">
                    @error('opening_date')<span class="field-err">{{ $message }}</span>@enderror
                </div>
                <div class="field">
                    <label class="field-label">Opening Time *</label>
                    <input name="opening_time" type="time" value="{{ old('opening_time','00:00') }}" required>
                    @error('opening_time')<span class="field-err">{{ $message }}</span>@enderror
                </div>
                <div class="field">
                    <label class="field-label">Closing Date *</label>
                    <input name="closing_date" type="date" required value="{{ old('closing_date') }}">
                    @error('closing_date')<span class="field-err">{{ $message }}</span>@enderror
                </div>
                <div class="field">
                    <label class="field-label">Closing Time *</label>
                    <input name="closing_time" type="time" value="{{ old('closing_time','23:59') }}" required>
                    @error('closing_time')<span class="field-err">{{ $message }}</span>@enderror
                </div>
                <div class="field">
                    <label class="field-label">Application Fee *</label>
                    <input name="application_fee" type="number" step="0.01" min="0" required value="{{ old('application_fee','10000') }}">
                    @error('application_fee')<span class="field-err">{{ $message }}</span>@enderror
                </div>
                <div class="field">
                    <label class="field-label">Currency</label>
                    <input name="currency" value="{{ old('currency','TZS') }}">
                    @error('currency')<span class="field-err">{{ $message }}</span>@enderror
                </div>
                <div class="field">
                    <label class="field-label">Timezone</label>
                    <input name="timezone" value="{{ old('timezone','Africa/Dar_es_Salaam') }}">
                    @error('timezone')<span class="field-err">{{ $message }}</span>@enderror
                </div>
                <div class="field">
                    <label class="field-label">Status</label>
                    <select name="status">
                        <option value="active" @selected(old('status')=='active')>Active</option>
                        <option value="inactive" @selected(old('status')=='inactive')>Inactive</option>
                    </select>
                    @error('status')<span class="field-err">{{ $message }}</span>@enderror
                </div>
            </div>
            <div class="form-actions">
                <a href="{{ route('admin.windows.index') }}" class="btn btn-ghost">Cancel</a>
                <button class="btn btn-primary">Create Window</button>
            </div>
        </form>
    </div>
</div>
@endsection
