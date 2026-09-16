@extends('layouts.applicant')
@section('title','Personal Information — '.$application->admissionWindow->admissionLevel->name)
@section('content')
<div class="page-head">
    <div>
        <h1>Personal Information</h1>
        <p class="page-sub">Step {{ $currentStep->step_number }} of {{ $steps->count() }} · Mandatory · {{ $application->admissionWindow->admissionLevel->name }}</p>
    </div>
    <div class="page-actions">
        <span class="tag tag-blue">{{ $progress['completed'] }} of {{ $progress['total'] }} — {{ $progress['percent'] }}%</span>
        <span class="tag tag-gold">Mandatory</span>
    </div>
</div>

<div class="panel" style="margin-bottom:16px;">
    <div class="panel-body" style="padding:14px 18px;">
        <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
            @foreach($steps as $st)
                @php $done = $application->stepCompletions()->where('workflow_step_id',$st->id)->whereNotNull('completed_at')->exists(); $isCurrent = $st->id===$currentStep->id; @endphp
                <span class="tag {{ $done ? 'tag-green' : ($isCurrent ? 'tag-terracotta' : 'tag-grey') }}" style="display:inline-flex;align-items:center;gap:6px;">
                    <span style="width:18px;height:18px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-size:10px;font-weight:800;flex:none;background:{{ $done ? 'var(--acacia-600)' : ($isCurrent ? 'var(--terracotta-600)' : 'var(--sand-200)') }};color:{{ $done || $isCurrent ? '#fff' : 'var(--coffee-700)' }};">{{ $done ? '✓' : $st->step_number }}</span>
                    {{ $st->step_name }}
                </span>
                @if(!$loop->last)<span style="color:var(--line);font-weight:700;">→</span>@endif
            @endforeach
        </div>
        <div style="margin-top:12px;height:6px;background:var(--sand-100);border:1px solid var(--line);border-radius:20px;overflow:hidden;">
            <div style="height:100%;background:var(--terracotta-600);width:{{ $progress['percent'] }}%"></div>
        </div>
    </div>
</div>

<div class="panel">
    <div class="panel-head">
        <div>
            <div class="panel-title">Your Details</div>
            <div class="panel-sub">Please provide accurate personal information as it appears on official documents.</div>
        </div>
    </div>
    <div class="panel-body">
        <form method="POST" action="{{ route('applicant.application.save', [encId($application->id), $currentStep->route]) }}" onsubmit="event.preventDefault(); const _f=this; confirmModal('Save personal information','Are you sure you want to save your personal information and continue?',()=>_f.submit())">
            @csrf
            <div class="form-grid-3">
                <div class="field @error('first_name') err @enderror">
                    <label class="field-label">First Name *</label>
                    <input name="first_name" value="{{ old('first_name', $applicant->first_name) }}" required>
                    @error('first_name')<span class="field-err">{{ $message }}</span>@enderror
                </div>
                <div class="field @error('middle_name') err @enderror">
                    <label class="field-label">Middle Name</label>
                    <input name="middle_name" value="{{ old('middle_name', $applicant->middle_name) }}">
                    @error('middle_name')<span class="field-err">{{ $message }}</span>@enderror
                </div>
                <div class="field @error('last_name') err @enderror">
                    <label class="field-label">Last Name *</label>
                    <input name="last_name" value="{{ old('last_name', $applicant->last_name) }}" required>
                    @error('last_name')<span class="field-err">{{ $message }}</span>@enderror
                </div>
                <div class="field @error('date_of_birth') err @enderror">
                    <label class="field-label">Date of Birth *</label>
                    <input name="date_of_birth" type="date" value="{{ old('date_of_birth', $applicant->date_of_birth?->format('Y-m-d')) }}" required>
                    @error('date_of_birth')<span class="field-err">{{ $message }}</span>@enderror
                </div>
                <div class="field @error('gender') err @enderror">
                    <label class="field-label">Gender *</label>
                    <select name="gender" required>
                        <option value="">Select</option>
                        <option value="Male" @selected(old('gender',$applicant->gender)=='Male')>Male</option>
                        <option value="Female" @selected(old('gender',$applicant->gender)=='Female')>Female</option>
                    </select>
                    @error('gender')<span class="field-err">{{ $message }}</span>@enderror
                </div>
                <div class="field @error('citizenship_id') err @enderror">
                    <label class="field-label">Citizenship *</label>
                    <select name="citizenship_id" required>
                        @foreach($countries as $c)<option value="{{ $c->id }}" @selected(old('citizenship_id',$applicant->citizenship_id)==$c->id)>{{ $c->name }}</option>@endforeach
                    </select>
                    @error('citizenship_id')<span class="field-err">{{ $message }}</span>@enderror
                </div>
                <div class="field @error('phone') err @enderror">
                    <label class="field-label">Phone *</label>
                    <input name="phone" value="{{ old('phone', $applicant->phone) }}" required placeholder="+2557xxxxxxxx">
                    @error('phone')<span class="field-err">{{ $message }}</span>@enderror
                </div>
                <div class="field @error('nida_number') err @enderror">
                    <label class="field-label">NIDA Number</label>
                    <input name="nida_number" value="{{ old('nida_number', $applicant->nida_number) }}">
                    @error('nida_number')<span class="field-err">{{ $message }}</span>@enderror
                </div>
                <div class="field @error('exam_index_number') err @enderror">
                    <label class="field-label">Exam Index Number</label>
                    <input name="exam_index_number" value="{{ old('exam_index_number', $applicant->exam_index_number) }}">
                    @error('exam_index_number')<span class="field-err">{{ $message }}</span>@enderror
                </div>
                <div class="field @error('marital_status') err @enderror">
                    <label class="field-label">Marital Status</label>
                    <select name="marital_status">
                        <option value="">—</option>
                        <option value="Single" @selected(old('marital_status',$applicant->marital_status)=='Single')>Single</option>
                        <option value="Married" @selected(old('marital_status',$applicant->marital_status)=='Married')>Married</option>
                        <option value="Divorced" @selected(old('marital_status',$applicant->marital_status)=='Divorced')>Divorced</option>
                    </select>
                    @error('marital_status')<span class="field-err">{{ $message }}</span>@enderror
                </div>
                <div class="field @error('disability_status') err @enderror" style="grid-column:span 2;">
                    <label class="field-label">Disability</label>
                    <div style="display:flex;gap:24px;flex-wrap:wrap;align-items:center;margin-top:6px;">
                        <label class="check-row" style="white-space:nowrap;"><input type="radio" name="disability_status" value="0" @checked(!old('disability_status', $applicant->disability_status))> No Disability</label>
                        <label class="check-row" style="white-space:nowrap;"><input type="radio" name="disability_status" value="1" @checked(old('disability_status', $applicant->disability_status))> Person With Disability</label>
                    </div>
                    <input name="disability_type" value="{{ old('disability_type', $applicant->disability_type) }}" placeholder="If yes, specify type" style="margin-top:8px;">
                    <span class="field-hint">Specify disability type only if applicable</span>
                    @error('disability_status')<span class="field-err">{{ $message }}</span>@enderror
                    @error('disability_type')<span class="field-err">{{ $message }}</span>@enderror
                </div>
            </div>

            <div style="margin-top:22px;padding-top:18px;border-top:1px solid var(--line);">
                <div class="panel-title" style="margin-bottom:4px;">Address</div>
                <p class="page-sub" style="margin:0 0 14px;">Your current residential address</p>
                <div class="form-grid-3">
                    <div class="field @error('region_id') err @enderror">
                        <label class="field-label">Region</label>
                        <select name="region_id">
                            <option value="">— Select —</option>
                            @foreach($regions as $r)<option value="{{ $r->id }}" @selected(old('region_id', $address?->region_id)==$r->id)>{{ $r->name }}</option>@endforeach
                        </select>
                        @error('region_id')<span class="field-err">{{ $message }}</span>@enderror
                    </div>
                    <div class="field @error('district_id') err @enderror">
                        <label class="field-label">District</label>
                        <select name="district_id">
                            <option value="">— Select —</option>
                            @foreach($districts as $d)<option value="{{ $d->id }}" @selected(old('district_id', $address?->district_id)==$d->id)>{{ $d->name }}</option>@endforeach
                        </select>
                        @error('district_id')<span class="field-err">{{ $message }}</span>@enderror
                    </div>
                    <div class="field @error('ward_id') err @enderror">
                        <label class="field-label">Ward</label>
                        <select name="ward_id">
                            <option value="">— Select —</option>
                            @foreach($wards as $w)<option value="{{ $w->id }}" @selected(old('ward_id', $address?->ward_id)==$w->id)>{{ $w->name }}</option>@endforeach
                        </select>
                        @error('ward_id')<span class="field-err">{{ $message }}</span>@enderror
                    </div>
                    <div class="field @error('street') err @enderror">
                        <label class="field-label">Street / Village</label>
                        <input name="street" value="{{ old('street', $address?->street) }}">
                        @error('street')<span class="field-err">{{ $message }}</span>@enderror
                    </div>
                    <div class="field @error('postal_address') err @enderror" style="grid-column:span 2;">
                        <label class="field-label">Postal Address</label>
                        <input name="postal_address" value="{{ old('postal_address', $address?->postal_address) }}">
                        @error('postal_address')<span class="field-err">{{ $message }}</span>@enderror
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <a href="{{ route('applicant.dashboard') }}" class="btn btn-ghost">Cancel</a>
                <button class="btn btn-primary">Save &amp; Continue →</button>
            </div>
        </form>
    </div>
</div>
@endsection
