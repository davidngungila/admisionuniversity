@extends('layouts.app')
@section('title','Register')
@section('content')
<div style="max-width:1100px;margin:0 auto;padding:28px 24px 40px">
    <div class="panel" style="overflow:hidden;">
        <div style="display:flex;gap:0;border-bottom:1.5px solid var(--line);background:var(--sand-50);">
            <button type="button" class="reg-tab active" onclick="switchRegTab(this,'tab-tanzanian')" data-tab="tab-tanzanian">Tanzanian Applicant</button>
            <button type="button" class="reg-tab" onclick="switchRegTab(this,'tab-international')" data-tab="tab-international">International Applicant</button>
            <button type="button" class="reg-tab" onclick="switchRegTab(this,'tab-postdoctoral')" data-tab="tab-postdoctoral">Post-Doctoral Applicant</button>
        </div>

        {{-- Tanzanian Applicant --}}
        <div id="tab-tanzanian" class="reg-tab-panel" style="display:block;">
            <div style="padding:20px 24px 0;">
                <div class="panel-title">Tanzanian Applicant Registration</div>
                <div class="panel-sub">Please fill in the required information to create your account</div>
            </div>
            <div style="padding:18px 24px 24px;">
                <form method="POST" action="{{ route('register') }}" id="reg-form-tz">
                    @csrf
                    <input type="hidden" name="applicant_category" value="tanzanian">
                    <div class="reg-grid" style="display:grid;grid-template-columns:repeat(3,1fr);gap:18px;">
                        <div class="field @error('entry_type') err @enderror">
                            <label class="field-label">Entry type *</label>
                            <select name="entry_type" required>
                                <option value="">— Select entry type —</option>
                                <option value="direct" @selected(old('entry_type')==='direct')>Direct</option>
                                <option value="equivalent" @selected(old('entry_type')==='equivalent')>Equivalent</option>
                            </select>
                            @error('entry_type')<span class="field-err">{{ $message }}</span>@enderror
                        </div>
                        <div class="field @error('application_type') err @enderror">
                            <label class="field-label" style="display:flex;align-items:center;gap:6px;">Application Type * <span class="info-icon" tabindex="0">i<span class="info-tip">Certificate, Diploma, Bachelor, PGD, Masters &amp; PhD</span></span></label>
                            <select name="application_type" required>
                                <option value="">— Select level —</option>
                                @foreach(($levels ?? []) as $lv)
                                    <option value="{{ $lv->id }}" @selected((string)old('application_type')===(string)$lv->id)>{{ $lv->name }} @if($lv->short_name) ({{ $lv->short_name }}) @endif</option>
                                @endforeach
                            </select>
                            @error('application_type')<span class="field-err">{{ $message }}</span>@enderror
                        </div>
                        <div class="field @error('email') err @enderror">
                            <label class="field-label">Email Address *</label>
                            <input name="email" type="email" value="{{ old('email') }}" required placeholder="you@example.com">
                            <span class="field-hint">Please enter email address (Enter a Valid/Working Email )</span>
                            @error('email')<span class="field-err">{{ $message }}</span>@enderror
                        </div>
                        <div class="field @error('index_number') err @enderror">
                            <label class="field-label" style="display:flex;align-items:center;gap:6px;">Index Number (Username): * <span class="info-icon" tabindex="0">i<span class="info-tip">e.g. S0001-0001-2015 or P0001-0001-2015 — used as your username to sign in.</span></span></label>
                            <input name="index_number" value="{{ old('index_number') }}" required placeholder="S0001-0001-2015 or P0001-0001-2015" style="font-family:'Consolas',monospace;letter-spacing:.02em;">
                            @error('index_number')<span class="field-err">{{ $message }}</span>@enderror
                        </div>
                        <div class="field @error('phone') err @enderror">
                            <label class="field-label">Phone Number *</label>
                            <input name="phone" value="{{ old('phone') }}" required placeholder="+255 715000001">
                            <span class="field-hint">Eg. +255 715000001</span>
                            @error('phone')<span class="field-err">{{ $message }}</span>@enderror
                        </div>
                        <div></div>
                        <div class="field @error('password') err @enderror">
                            <label class="field-label">Password *</label>
                            <input name="password" type="password" required placeholder="••••••••">
                            <span class="field-hint">Please enter password</span>
                            @error('password')<span class="field-err">{{ $message }}</span>@enderror
                        </div>
                        <div class="field">
                            <label class="field-label">Confirm Password *</label>
                            <input name="password_confirmation" type="password" required placeholder="••••••••">
                            <span class="field-hint">Please confirm password</span>
                        </div>
                        <div></div>
                    </div>
                    <div style="margin-top:22px;padding-top:18px;border-top:1.5px solid var(--line);display:flex;gap:10px;justify-content:center;flex-wrap:wrap;">
                        <button type="submit" class="btn btn-primary btn-sm">Create Account</button>
                        <a href="{{ route('login') }}" class="btn btn-ghost btn-sm">Go back to sign in</a>
                    </div>
                    <p style="text-align:center;margin-top:10px;"><a href="{{ route('home') }}" style="font-size:12px;color:var(--ink-soft);">← Back to home</a></p>
                </form>
            </div>
        </div>

        {{-- International Applicant --}}
        <div id="tab-international" class="reg-tab-panel" style="display:none;">
            <div style="padding:20px 24px 0;">
                <div class="panel-title">International Applicant Registration</div>
                <div class="panel-sub">Please fill in the required information to create your account</div>
            </div>
            <div style="padding:18px 24px 24px;">
                <form method="POST" action="{{ route('register') }}" id="reg-form-intl">
                    @csrf
                    <input type="hidden" name="applicant_category" value="international">
                    <div class="reg-grid" style="display:grid;grid-template-columns:repeat(3,1fr);gap:18px;">
                        <div class="field @error('first_name') err @enderror">
                            <label class="field-label">First Name: *</label>
                            <input name="first_name" value="{{ old('first_name') }}" required placeholder="e.g. John">
                            <span class="field-hint">Please enter your first name</span>
                            @error('first_name')<span class="field-err">{{ $message }}</span>@enderror
                        </div>
                        <div class="field @error('surname') err @enderror">
                            <label class="field-label">Surname: *</label>
                            <input name="surname" value="{{ old('surname') }}" required placeholder="e.g. Doe">
                            <span class="field-hint">Please enter your surname</span>
                            @error('surname')<span class="field-err">{{ $message }}</span>@enderror
                        </div>
                        <div class="field @error('passport_number') err @enderror">
                            <label class="field-label" style="display:flex;align-items:center;gap:6px;">Passport Number (Username): * <span class="info-icon" tabindex="0">i<span class="info-tip">e.g. AB1234567 — used as your username to sign in.</span></span></label>
                            <input name="passport_number" value="{{ old('passport_number') }}" required placeholder="e.g. AB1234567" style="font-family:'Consolas',monospace;">
                            @error('passport_number')<span class="field-err">{{ $message }}</span>@enderror
                        </div>
                        <div class="field @error('scholarship_category') err @enderror">
                            <label class="field-label" style="display:flex;align-items:center;gap:6px;">Scholarship Category * <span class="info-icon" tabindex="0">i<span class="info-tip">Select No Scholarship if you are applying through the normal admission process.</span></span></label>
                            <select name="scholarship_category" required>
                                <option value="">— Select category —</option>
                                <option value="none" @selected(old('scholarship_category')==='none')>No Scholarship</option>
                                <option value="government" @selected(old('scholarship_category')==='government')>Government Scholarship</option>
                                <option value="private" @selected(old('scholarship_category')==='private')>Private Scholarship</option>
                                <option value="heslb" @selected(old('scholarship_category')==='heslb')>HESLB Loan</option>
                                <option value="other" @selected(old('scholarship_category')==='other')>Other</option>
                            </select>
                            @error('scholarship_category')<span class="field-err">{{ $message }}</span>@enderror
                        </div>
                        <div class="field @error('application_type') err @enderror">
                            <label class="field-label" style="display:flex;align-items:center;gap:6px;">Application Type * <span class="info-icon" tabindex="0">i<span class="info-tip">Certificate, Diploma, Bachelor, PGD, Masters &amp; PhD</span></span></label>
                            <select name="application_type" required>
                                <option value="">— Select level —</option>
                                @foreach(($levels ?? []) as $lv)
                                    <option value="{{ $lv->id }}" @selected((string)old('application_type')===(string)$lv->id)>{{ $lv->name }} @if($lv->short_name) ({{ $lv->short_name }}) @endif</option>
                                @endforeach
                            </select>
                            @error('application_type')<span class="field-err">{{ $message }}</span>@enderror
                        </div>
                        <div class="field @error('email') err @enderror">
                            <label class="field-label">Email Address *</label>
                            <input name="email" type="email" value="{{ old('email') }}" required placeholder="you@example.com">
                            <span class="field-hint">Please enter your email address (Enter a Valid/Working Email )</span>
                            @error('email')<span class="field-err">{{ $message }}</span>@enderror
                        </div>
                        <div class="field @error('phone') err @enderror">
                            <label class="field-label">Phone Number *</label>
                            <input name="phone" value="{{ old('phone') }}" required placeholder="+255 715000001">
                            <span class="field-hint">Eg. +255 715000001<br>Your phone number is unique to our system</span>
                            @error('phone')<span class="field-err">{{ $message }}</span>@enderror
                        </div>
                        <div class="field @error('password') err @enderror">
                            <label class="field-label">Password *</label>
                            <input name="password" type="password" required placeholder="••••••••">
                            <span class="field-hint">Please enter password</span>
                            @error('password')<span class="field-err">{{ $message }}</span>@enderror
                        </div>
                        <div class="field">
                            <label class="field-label">Confirm Password *</label>
                            <input name="password_confirmation" type="password" required placeholder="••••••••">
                            <span class="field-hint">Please confirm password</span>
                        </div>
                    </div>
                    <div style="margin-top:22px;padding-top:18px;border-top:1.5px solid var(--line);display:flex;gap:10px;justify-content:center;flex-wrap:wrap;">
                        <button type="submit" class="btn btn-primary btn-sm">Create Account</button>
                        <a href="{{ route('login') }}" class="btn btn-ghost btn-sm">Go back to sign in</a>
                    </div>
                    <p style="text-align:center;margin-top:10px;"><a href="{{ route('home') }}" style="font-size:12px;color:var(--ink-soft);">← Back to home</a></p>
                </form>
            </div>
        </div>

        {{-- Post-Doctoral Applicant --}}
        <div id="tab-postdoctoral" class="reg-tab-panel" style="display:none;">
            <div style="padding:20px 24px 0;">
                <div class="panel-title">Post-Doctoral Applicant Registration</div>
                <div class="panel-sub">Please fill in the required information to create your account</div>
            </div>
            <div style="padding:18px 24px 24px;">
                <div style="margin-bottom:16px;padding:12px 16px;border-radius:10px;background:#fef3f2;border:1.5px solid #fecdca;color:#7a271a;font-size:12.5px;line-height:1.5;">
                    <strong>Eligibility Requirement:</strong> Postdoc applicants must have completed their PhD within the last 5 years (graduated in or after 2021).
                </div>
                <form method="POST" action="{{ route('register') }}" id="reg-form-pd">
                    @csrf
                    <input type="hidden" name="applicant_category" value="postdoctoral">
                    <div class="reg-grid" style="display:grid;grid-template-columns:repeat(3,1fr);gap:18px;">
                        <div class="field @error('first_name') err @enderror">
                            <label class="field-label">First Name: *</label>
                            <input name="first_name" value="{{ old('first_name') }}" required placeholder="Enter your first name">
                            <span class="field-hint">Enter your first name</span>
                            @error('first_name')<span class="field-err">{{ $message }}</span>@enderror
                        </div>
                        <div class="field @error('surname') err @enderror">
                            <label class="field-label">Surname / Last Name: *</label>
                            <input name="surname" value="{{ old('surname') }}" required placeholder="Enter your surname">
                            <span class="field-hint">Enter your surname</span>
                            @error('surname')<span class="field-err">{{ $message }}</span>@enderror
                        </div>
                        <div class="field @error('username') err @enderror">
                            <label class="field-label" style="display:flex;align-items:center;gap:6px;">Username: * <span class="info-icon" tabindex="0">i<span class="info-tip">Choose a username — used for signing into your account.</span></span></label>
                            <input name="username" value="{{ old('username') }}" required placeholder="Choose a username">
                            @error('username')<span class="field-err">{{ $message }}</span>@enderror
                        </div>
                        <div class="field @error('phd_graduation_year') err @enderror">
                            <label class="field-label">PhD Graduation Year: *</label>
                            <input name="phd_graduation_year" type="number" min="2021" max="{{ date('Y')+1 }}" value="{{ old('phd_graduation_year') }}" required placeholder="e.g. 2023">
                            @error('phd_graduation_year')<span class="field-err">{{ $message }}</span>@enderror
                        </div>
                        <div class="field @error('email') err @enderror">
                            <label class="field-label">Email Address: *</label>
                            <input name="email" type="email" value="{{ old('email') }}" required placeholder="Enter a working email address">
                            <span class="field-hint">Enter a working email address</span>
                            @error('email')<span class="field-err">{{ $message }}</span>@enderror
                        </div>
                        <div class="field @error('phone') err @enderror">
                            <label class="field-label">Phone Number (e.g. +255 715000001): *</label>
                            <input name="phone" value="{{ old('phone') }}" required placeholder="+255 715000001">
                            <span class="field-hint">Used for communications and SMS updates.</span>
                            @error('phone')<span class="field-err">{{ $message }}</span>@enderror
                        </div>
                        <div class="field @error('scholarship_category') err @enderror">
                            <label class="field-label" style="display:flex;align-items:center;gap:6px;">Scholarship Category * <span class="info-icon" tabindex="0">i<span class="info-tip">Select No Scholarship if you are applying through the normal admission process.</span></span></label>
                            <select name="scholarship_category" required>
                                <option value="">— Select category —</option>
                                <option value="none" @selected(old('scholarship_category')==='none')>No Scholarship</option>
                                <option value="government" @selected(old('scholarship_category')==='government')>Government Scholarship</option>
                                <option value="private" @selected(old('scholarship_category')==='private')>Private Scholarship</option>
                                <option value="heslb" @selected(old('scholarship_category')==='heslb')>HESLB Loan</option>
                                <option value="other" @selected(old('scholarship_category')==='other')>Other</option>
                            </select>
                            @error('scholarship_category')<span class="field-err">{{ $message }}</span>@enderror
                        </div>
                        <div class="field @error('password') err @enderror">
                            <label class="field-label">Password: *</label>
                            <input name="password" type="password" required placeholder="Enter password">
                            <span class="field-hint">Enter password</span>
                            @error('password')<span class="field-err">{{ $message }}</span>@enderror
                        </div>
                        <div class="field">
                            <label class="field-label">Confirm Password: *</label>
                            <input name="password_confirmation" type="password" required placeholder="Confirm password">
                            <span class="field-hint">Confirm password</span>
                        </div>
                    </div>
                    <div style="margin-top:22px;padding-top:18px;border-top:1.5px solid var(--line);display:flex;gap:10px;justify-content:center;flex-wrap:wrap;">
                        <button type="submit" class="btn btn-primary btn-sm">Create Account</button>
                        <a href="{{ route('login') }}" class="btn btn-ghost btn-sm">Go back to sign in</a>
                    </div>
                    <p style="text-align:center;margin-top:10px;"><a href="{{ route('home') }}" style="font-size:12px;color:var(--ink-soft);">← Back to home</a></p>
                </form>
            </div>
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
function regAlert(msg) {
    document.getElementById('regAlertMsg').textContent = msg;
    openModal('regAlertBackdrop');
}
function switchRegTab(btn, id) {
    document.querySelectorAll('.reg-tab').forEach(t=>t.classList.remove('active'));
    document.querySelectorAll('.reg-tab-panel').forEach(p=>p.style.display='none');
    btn.classList.add('active');
    document.getElementById(id).style.display='block';
}
function req(field){ return (field.value||'').trim(); }
function validateTz(f){
    if(!req(f.entry_type)) return regAlert('Please select your entry type.');
    if(!req(f.application_type)) return regAlert('Please select your application type.');
    if(!req(f.email)) return regAlert('Please enter your email address.');
    if(!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(req(f.email))) return regAlert('Please enter a valid / working email address.');
    if(!req(f.index_number)) return regAlert('Please enter your Form IV index number.');
    if(!req(f.phone)) return regAlert('Please enter your phone number.');
    if(!/^\+?[0-9]{9,15}$/.test(req(f.phone))) return regAlert('Please enter a valid phone number, e.g. +255715000001.');
    if(!req(f.password)) return regAlert('Please enter your password.');
    if(req(f.password) !== req(f.password_confirmation)) return regAlert('Passwords do not match.');
    return true;
}
function validateIntl(f){
    if(!req(f.first_name)) return regAlert('Please enter your first name.');
    if(!req(f.surname)) return regAlert('Please enter your surname.');
    if(!req(f.passport_number)) return regAlert('Please enter your passport number.');
    if(!req(f.scholarship_category)) return regAlert('Please select your scholarship category.');
    if(!req(f.application_type)) return regAlert('Please select your application type.');
    if(!req(f.email)) return regAlert('Please enter your email address.');
    if(!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(req(f.email))) return regAlert('Please enter a valid / working email address.');
    if(!req(f.phone)) return regAlert('Please enter your phone number.');
    if(!/^\+?[0-9]{9,15}$/.test(req(f.phone))) return regAlert('Please enter a valid phone number, e.g. +255715000001.');
    if(!req(f.password)) return regAlert('Please enter your password.');
    if(req(f.password) !== req(f.password_confirmation)) return regAlert('Passwords do not match.');
    return true;
}
function validatePd(f){
    if(!req(f.first_name)) return regAlert('Please enter your first name.');
    if(!req(f.surname)) return regAlert('Please enter your surname.');
    if(!req(f.username)) return regAlert('Please choose a username.');
    if(!req(f.phd_graduation_year)) return regAlert('Please enter your PhD graduation year.');
    const yr = parseInt(req(f.phd_graduation_year),10);
    if(isNaN(yr) || yr < 2021 || yr > (new Date().getFullYear()+1)) return regAlert('PhD graduation year must be 2021 or later (within the last 5 years).');
    if(!req(f.email)) return regAlert('Please enter your email address.');
    if(!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(req(f.email))) return regAlert('Please enter a valid / working email address.');
    if(!req(f.phone)) return regAlert('Please enter your phone number.');
    if(!/^\+?[0-9]{9,15}$/.test(req(f.phone))) return regAlert('Please enter a valid phone number, e.g. +255715000001.');
    if(!req(f.scholarship_category)) return regAlert('Please select your scholarship category.');
    if(!req(f.password)) return regAlert('Please enter your password.');
    if(req(f.password) !== req(f.password_confirmation)) return regAlert('Passwords do not match.');
    return true;
}
document.getElementById('reg-form-tz')?.addEventListener('submit', function(e){ if(validateTz(e.target) !== true) e.preventDefault(); });
document.getElementById('reg-form-intl')?.addEventListener('submit', function(e){ if(validateIntl(e.target) !== true) e.preventDefault(); });
document.getElementById('reg-form-pd')?.addEventListener('submit', function(e){ if(validatePd(e.target) !== true) e.preventDefault(); });

// Re-open tab that has server validation errors
(function(){
    const errFields = document.querySelectorAll('.field.err');
    if(!errFields.length) return;
    const panel = errFields[0].closest('.reg-tab-panel');
    if(!panel) return;
    const tabBtn = document.querySelector('.reg-tab[data-tab="'+panel.id+'"]');
    if(tabBtn) switchRegTab(tabBtn, panel.id);
})();
</script>
<style>
.reg-tab{flex:1;padding:12px 16px;border:none;background:none;font-size:13px;font-weight:700;color:var(--ink-soft);cursor:pointer;text-align:center;border-bottom:3px solid transparent;transition:all .15s;font-family:inherit;}
.reg-tab:hover{color:var(--coffee-900);background:#fff;}
.reg-tab.active{color:var(--terracotta-600);border-bottom-color:var(--terracotta-600);background:#fff;}
.info-icon{position:relative;display:inline-flex;align-items:center;justify-content:center;width:16px;height:16px;border-radius:50%;background:var(--terracotta-100);color:var(--terracotta-600);border:1px solid var(--line);font-size:10px;font-weight:800;cursor:help;flex:none;}
.info-icon .info-tip{position:absolute;left:50%;bottom:calc(100% + 8px);transform:translateX(-50%);background:var(--coffee-900);color:#fff;padding:8px 10px;border-radius:8px;font-size:11px;line-height:1.4;white-space:normal;max-width:240px;width:max-content;box-shadow:0 4px 12px rgba(0,0,0,.15);display:none;z-index:10;text-align:left;font-weight:500;letter-spacing:0;}
.info-icon .info-tip::after{content:"";position:absolute;top:100%;left:50%;margin-left:-5px;border-width:5px;border-style:solid;border-color:var(--coffee-900) transparent transparent transparent;}
.info-icon:hover .info-tip, .info-icon:focus .info-tip, .info-icon:focus-within .info-tip{display:block;}
@media(max-width:900px){ .reg-grid{grid-template-columns:1fr !important;} .reg-tab{font-size:12px;padding:10px 8px;} }
</style>
@endsection