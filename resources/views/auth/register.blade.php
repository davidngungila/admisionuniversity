@extends('layouts.app')
@section('title','Tanzanian Applicant Registration')
@section('content')
<div style="max-width:1100px;margin:0 auto;padding:28px 24px 40px">
    <div class="panel">
        <div class="panel-head">
            <div>
                <div class="panel-title">Tanzanian Applicant Registration</div>
                <div class="panel-sub">Please fill in the required information to create your account</div>
            </div>
        </div>
        <div class="panel-body">
            <form method="POST" action="{{ route('register') }}" id="reg-form">
                @csrf
                <div class="reg-grid" style="display:grid;grid-template-columns:repeat(3,1fr);gap:18px;">

                    {{-- Entry type --}}
                    <div class="field @error('entry_type') err @enderror">
                        <label class="field-label">Entry type *</label>
                        <select name="entry_type" required>
                            <option value="">— Select entry type —</option>
                            <option value="direct" @selected(old('entry_type')==='direct')>Direct Entry</option>
                            <option value="equivalent" @selected(old('entry_type')==='equivalent')>Equivalent Entry</option>
                            <option value="mature_age" @selected(old('entry_type')==='mature_age')>Mature Age</option>
                            <option value="transfer" @selected(old('entry_type')==='transfer')>Transfer</option>
                            <option value="other" @selected(old('entry_type')==='other')>Other</option>
                        </select>
                        @error('entry_type')<span class="field-err">{{ $message }}</span>@enderror
                    </div>

                    {{-- Scholarship Category --}}
                    <div class="field @error('scholarship_category') err @enderror">
                        <label class="field-label">Scholarship Category *</label>
                        <select name="scholarship_category" required>
                            <option value="">— Select category —</option>
                            <option value="none" @selected(old('scholarship_category')==='none')>No Scholarship</option>
                            <option value="government" @selected(old('scholarship_category')==='government')>Government Scholarship</option>
                            <option value="private" @selected(old('scholarship_category')==='private')>Private Scholarship</option>
                            <option value="heslb" @selected(old('scholarship_category')==='heslb')>HESLB Loan</option>
                            <option value="other" @selected(old('scholarship_category')==='other')>Other</option>
                        </select>
                        <span class="field-hint">Select No Scholarship if you are applying through the normal admission process.</span>
                        @error('scholarship_category')<span class="field-err">{{ $message }}</span>@enderror
                    </div>

                    {{-- Application Type --}}
                    <div class="field @error('application_type') err @enderror">
                        <label class="field-label">Application Type *</label>
                        <select name="application_type" required>
                            <option value="">— Select application type —</option>
                            <option value="direct" @selected(old('application_type')==='direct')>Direct Application</option>
                            <option value="equivalent" @selected(old('application_type')==='equivalent')>Equivalent Application</option>
                            <option value="transfer" @selected(old('application_type')==='transfer')>Transfer</option>
                            <option value="mature_age" @selected(old('application_type')==='mature_age')>Mature Age</option>
                            <option value="other" @selected(old('application_type')==='other')>Other</option>
                        </select>
                        @error('application_type')<span class="field-err">{{ $message }}</span>@enderror
                    </div>

                    {{-- Email Address --}}
                    <div class="field @error('email') err @enderror">
                        <label class="field-label">Email Address *</label>
                        <input name="email" type="email" value="{{ old('email') }}" required placeholder="you@example.com">
                        <span class="field-hint">Please enter email address (Enter a Valid/Working Email )</span>
                        @error('email')<span class="field-err">{{ $message }}</span>@enderror
                    </div>

                    {{-- Index Number --}}
                    <div class="field @error('index_number') err @enderror">
                        <label class="field-label">First Sitting Form IV or Equivalent Form IV Index No. Eg. S0001-0001-2022 (Username): *</label>
                        <input name="index_number" value="{{ old('index_number') }}" required placeholder="S0001-0001-2015 or P0001-0001-2015" style="font-family:'Consolas',monospace;letter-spacing:.02em;">
                        <span class="field-hint">e.g. S0001-0001-2015 or P0001-0001-2015</span>
                        @error('index_number')<span class="field-err">{{ $message }}</span>@enderror
                    </div>

                    {{-- Phone Number --}}
                    <div class="field @error('phone') err @enderror">
                        <label class="field-label">Phone Number *</label>
                        <input name="phone" value="{{ old('phone') }}" required placeholder="+255 715000001">
                        <span class="field-hint">Eg. +255 715000001</span>
                        @error('phone')<span class="field-err">{{ $message }}</span>@enderror
                    </div>

                    {{-- Password --}}
                    <div class="field @error('password') err @enderror">
                        <label class="field-label">Password *</label>
                        <input name="password" type="password" required placeholder="••••••••">
                        <span class="field-hint">Please enter password</span>
                        @error('password')<span class="field-err">{{ $message }}</span>@enderror
                    </div>

                    {{-- Confirm Password --}}
                    <div class="field">
                        <label class="field-label">Confirm Password *</label>
                        <input name="password_confirmation" type="password" required placeholder="••••••••">
                        <span class="field-hint">Please confirm password</span>
                    </div>

                </div>

                <div style="margin-top:22px;padding-top:18px;border-top:1.5px solid var(--line);">
                    <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;">Create Account</button>
                </div>

                <p style="font-size:13px;text-align:center;margin-top:14px;color:var(--ink-soft);">Already have an account? <a href="{{ route('login') }}" style="color:var(--terracotta-600);font-weight:700;">Sign in</a></p>
                <p style="text-align:center;margin-top:8px;"><a href="{{ route('home') }}" style="font-size:12px;color:var(--ink-soft);">← Back to home</a></p>
            </form>
        </div>
    </div>
</div>

<div class="modal-backdrop" id="regAlertBackdrop" onclick="if(event.target===this)closeModal('regAlertBackdrop')">
    <div class="modal modal-sm">
        <div class="modal-body" style="text-align:center;padding:28px 22px 18px">
            <div class="es-icon" style="background:var(--terracotta-100);border-color:#e8b4b0;color:var(--terracotta-600);margin-bottom:14px">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            </div>
            <h3 style="font-size:15px;font-weight:800;color:var(--coffee-900)">Please check</h3>
            <p id="regAlertMsg" style="font-size:13px;color:var(--ink-soft);margin-top:6px;line-height:1.5"></p>
        </div>
        <div class="modal-foot" style="justify-content:center">
            <button type="button" class="btn btn-primary btn-sm" onclick="closeModal('regAlertBackdrop')">OK</button>
        </div>
    </div>
</div>

<script>
function req(field) { return (field.value || '').trim(); }
function regAlert(msg) {
    document.getElementById('regAlertMsg').textContent = msg;
    openModal('regAlertBackdrop');
}
document.getElementById('reg-form')?.addEventListener('submit', function (e) {
    const f = e.target;
    const entry = req(f.entry_type), sch = req(f.scholarship_category), appType = req(f.application_type);
    const email = req(f.email), idx = req(f.index_number), phone = req(f.phone);
    if (!entry) { e.preventDefault(); return regAlert('Please select your entry type.'); }
    if (!sch) { e.preventDefault(); return regAlert('Please select your scholarship category.'); }
    if (!appType) { e.preventDefault(); return regAlert('Please select your application type.'); }
    if (!email) { e.preventDefault(); return regAlert('Please enter your email address.'); }
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) { e.preventDefault(); return regAlert('Please enter a valid / working email address.'); }
    if (!idx) { e.preventDefault(); return regAlert('Please enter your Form IV index number.'); }
    if (!phone) { e.preventDefault(); return regAlert('Please enter your phone number.'); }
    if (!/^\+?[0-9]{9,15}$/.test(phone)) { e.preventDefault(); return regAlert('Please enter a valid phone number, e.g. +255715000001.'); }
    if (!req(f.password)) { e.preventDefault(); return regAlert('Please enter your password.'); }
    if (req(f.password) !== req(f.password_confirmation)) { e.preventDefault(); return regAlert('Passwords do not match.'); }
});
</script>
<style>
@media(max-width:900px){ .reg-grid{grid-template-columns:1fr !important;} }
</style>
@endsection