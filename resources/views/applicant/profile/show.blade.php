@extends('layouts.applicant')
@section('title','Profile')
@section('content')
<div class="page-head">
    <div>
        <h1>Profile</h1>
        <p class="page-sub">Manage your personal account and security settings</p>
    </div>
</div>

<div class="grid-2">
    <div class="panel">
        <div class="panel-head"><div class="panel-title">Profile</div></div>
        <div class="panel-body">
            <form method="POST" action="{{ route('applicant.profile.update') }}" style="display:flex;flex-direction:column;gap:14px;" onsubmit="event.preventDefault(); const _f=this; confirmModal('Update profile','Are you sure you want to update your profile?',()=>_f.submit())">
                @csrf @method('PUT')
                <div class="field @error('name') err @enderror">
                    <label class="field-label">Name</label>
                    <input name="name" value="{{ old('name', $user->name) }}" required>
                    @error('name')<span class="field-err">{{ $message }}</span>@enderror
                </div>
                <div class="field @error('email') err @enderror">
                    <label class="field-label">Email</label>
                    <input name="email" type="email" value="{{ old('email', $user->email) }}" required>
                    @error('email')<span class="field-err">{{ $message }}</span>@enderror
                </div>
                <div class="field @error('phone') err @enderror">
                    <label class="field-label">Phone</label>
                    <input name="phone" value="{{ old('phone', $user->phone) }}" required>
                    @error('phone')<span class="field-err">{{ $message }}</span>@enderror
                </div>
                @if($applicant)
                    <div class="field @error('nida_number') err @enderror">
                        <label class="field-label">NIDA Number</label>
                        <input name="nida_number" value="{{ old('nida_number', $applicant->nida_number) }}">
                        @error('nida_number')<span class="field-err">{{ $message }}</span>@enderror
                    </div>
                @endif
                <button class="btn btn-primary" style="width:100%;justify-content:center;">Save Profile</button>
            </form>
        </div>
    </div>

    <div class="panel">
        <div class="panel-head"><div class="panel-title">Change Password</div></div>
        <div class="panel-body">
            <form method="POST" action="{{ route('applicant.password.update') }}" style="display:flex;flex-direction:column;gap:14px;" onsubmit="event.preventDefault(); const _f=this; confirmModal('Change password','Are you sure you want to change your password?',()=>_f.submit())">
                @csrf @method('PUT')
                <div class="field @error('current_password') err @enderror">
                    <label class="field-label">Current Password</label>
                    <input name="current_password" type="password" required>
                    @error('current_password')<span class="field-err">{{ $message }}</span>@enderror
                </div>
                <div class="field @error('password') err @enderror">
                    <label class="field-label">New Password</label>
                    <input name="password" type="password" required>
                    @error('password')<span class="field-err">{{ $message }}</span>@enderror
                </div>
                <div class="field">
                    <label class="field-label">Confirm New Password</label>
                    <input name="password_confirmation" type="password" required>
                </div>
                <button class="btn btn-primary" style="width:100%;justify-content:center;">Change Password</button>
            </form>
        </div>
    </div>
</div>

@if($applicant)
    <div class="panel" style="margin-top:16px;">
        <div class="panel-head"><div class="panel-title">Applicant Details</div><span class="tag tag-grey">{{ $applicant->applicant_number ?? 'No number' }}</span></div>
        <div class="panel-body">
            <div class="kv">
                <div class="kv-row"><span class="k">Applicant No</span><span class="v mono">{{ $applicant->applicant_number ?? '—' }}</span></div>
                <div class="kv-row"><span class="k">Nationality</span><span class="v">{{ $applicant->citizenship->name ?? '—' }}</span></div>
                <div class="kv-row"><span class="k">Gender</span><span class="v">{{ $applicant->gender ?? '—' }}</span></div>
                <div class="kv-row"><span class="k">DOB</span><span class="v">{{ $applicant->date_of_birth?->format('d M Y') ?? '—' }}</span></div>
                <div class="kv-row"><span class="k">Phone</span><span class="v">{{ $applicant->phone ?? '—' }}</span></div>
                <div class="kv-row"><span class="k">Email</span><span class="v" style="word-break:break-all;">{{ $applicant->email ?? '—' }}</span></div>
            </div>
        </div>
    </div>
@endif
@endsection
