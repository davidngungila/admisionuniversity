@extends('layouts.app')
@section('title','Create Account')
@section('content')
<div style="max-width:1100px;margin:0 auto;padding:28px 24px 40px">
    <div class="panel">
        <div class="panel-head">
            <div>
                <div class="panel-title">Create your admission account</div>
                <div class="panel-sub">Complete all sections below — your official name is pulled from NECTA using your Form Four index number.</div>
            </div>
        </div>
        <div class="panel-body">
            <form method="POST" action="{{ route('register') }}" id="reg-form">
                @csrf
                <div class="reg-grid" style="display:grid;grid-template-columns:repeat(3,1fr);gap:22px;">

                    {{-- 1. Study level & entry --}}
                    <div class="reg-step" data-step="1">
                        <div style="font-weight:800;font-size:14px;color:var(--coffee-900);margin-bottom:12px;padding-bottom:8px;border-bottom:1.5px solid var(--line);">1. Study Level &amp; Entry</div>
                        <div class="field @error('intended_level_id') err @enderror">
                            <label class="field-label">Level I want to study *</label>
                            <select name="intended_level_id" required>
                                <option value="">— Select level —</option>
                                @foreach(($levels ?? []) as $lv)
                                    <option value="{{ $lv->id }}" @selected(old('intended_level_id')==$lv->id)>{{ $lv->name }} @if($lv->short_name) ({{ $lv->short_name }}) @endif</option>
                                @endforeach
                            </select>
                            <span class="field-hint">Certificate → PhD</span>
                            @error('intended_level_id')<span class="field-err">{{ $message }}</span>@enderror
                        </div>
                        <div class="field @error('application_type') err @enderror" style="margin-top:14px;">
                            <label class="field-label">Entry Type / Application Type *</label>
                            <select name="application_type" required>
                                <option value="">— Select entry type —</option>
                                @foreach([['direct','Direct Entry — Form Four (CSEE)'],['equivalent','Equivalent Qualification'],['transfer','Transfer'],['mature_age','Mature Age Entry'],['other','Other']] as [$val,$lab])
                                    <option value="{{ $val }}" @selected(old('application_type')==$val)>{{ $lab }}</option>
                                @endforeach
                            </select>
                            @error('application_type')<span class="field-err">{{ $message }}</span>@enderror
                        </div>
                        <div class="field @error('index_number') err @enderror" style="margin-top:14px;">
                            <label class="field-label">Form Four / Equivalent Index No. *</label>
                            <input name="index_number" value="{{ old('index_number') }}" required placeholder="S0001-0001-2024, P0001-0001-2024, or EQ2024000028-2024" style="font-family:'Consolas',monospace;letter-spacing:.02em;">
                            <span class="field-hint">Used by NECTA to fetch your official name</span>
                            @error('index_number')<span class="field-err">{{ $message }}</span>@enderror
                        </div>
                    </div>

                    {{-- 2. NECTA name check --}}
                    <div class="reg-step" data-step="2">
                        <div style="font-weight:800;font-size:14px;color:var(--coffee-900);margin-bottom:12px;padding-bottom:8px;border-bottom:1.5px solid var(--line);">2. NECTA Name Check</div>
                        <div class="field @error('first_name') err @enderror">
                            <label class="field-label">First Name (Required for NECTA fetch) *</label>
                            <input name="first_name" id="reg-first-name" value="{{ old('first_name') }}" required placeholder="Please enter your first name">
                            @error('first_name')<span class="field-err">{{ $message }}</span>@enderror
                        </div>
                        <div id="reg-verify-result" style="display:none;margin-top:14px;padding:10px 12px;border-radius:8px;background:#ecfdf3;border:1px solid #abefc6;color:#067647;font-size:13px;">
                            ✓ Verified with NECTA — <b id="reg-full-name"></b>
                        </div>
                        <div id="reg-verify-err" style="display:none;color:#b42318;background:#fef3f2;border:1px solid #fecdca;border-radius:8px;padding:8px 12px;font-size:12.5px;margin-top:12px;"></div>
                        <button type="button" class="btn btn-soft" style="width:100%;margin-top:16px;" id="reg-verify-btn" onclick="verifyWithNecta()">Verify &amp; Fetch Name from NECTA</button>
                    </div>

                    {{-- 3. Contact details --}}
                    <div class="reg-step" data-step="3">
                        <div style="font-weight:800;font-size:14px;color:var(--coffee-900);margin-bottom:12px;padding-bottom:8px;border-bottom:1.5px solid var(--line);">3. Contact Details</div>
                        <div class="field @error('phone') err @enderror">
                            <label class="field-label">Phone Number *</label>
                            <input name="phone" value="{{ old('phone') }}" required placeholder="+255715000001">
                            <span class="field-hint">e.g. +255715000001 — include country code</span>
                            @error('phone')<span class="field-err">{{ $message }}</span>@enderror
                        </div>
                        <div class="field @error('email') err @enderror" style="margin-top:14px;">
                            <label class="field-label">Email Address *</label>
                            <input name="email" type="email" value="{{ old('email') }}" required placeholder="you@example.com">
                            <span class="field-hint">Enter a valid / working email</span>
                            @error('email')<span class="field-err">{{ $message }}</span>@enderror
                        </div>
                    </div>

                    {{-- 4. Password --}}
                    <div class="reg-step" data-step="4">
                        <div style="font-weight:800;font-size:14px;color:var(--coffee-900);margin-bottom:12px;padding-bottom:8px;border-bottom:1.5px solid var(--line);">4. Set Password</div>
                        <div class="field @error('password') err @enderror">
                            <label class="field-label">Password *</label>
                            <input name="password" type="password" required placeholder="••••••••">
                            @error('password')<span class="field-err">{{ $message }}</span>@enderror
                        </div>
                        <div class="field" style="margin-top:14px;">
                            <label class="field-label">Confirm Password *</label>
                            <input name="password_confirmation" type="password" required placeholder="••••••••">
                        </div>
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
const CSRF = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
const FETCH_NAME_ROUTE = "{{ route('register.fetch-name') }}";
let verifiedName = '';

function req(field) { return (field.value || '').trim(); }
function regAlert(msg) {
    document.getElementById('regAlertMsg').textContent = msg;
    openModal('regAlertBackdrop');
}
function validateRegForm() {
    const f = document.getElementById('reg-form');
    const lvl = req(f.intended_level_id), at = req(f.application_type), ix = req(f.index_number);
    if (!lvl) return regAlert('Please select the level you want to study.');
    if (!at) return regAlert('Please choose your entry / application type.');
    if (!ix) return regAlert('Enter your Form Four / Equivalent index number.');
    if (!/^(S\d{4}-\d{4}-\d{4}|P\d{4}-\d{4}-\d{4}|EQ\d{10}-\d{4})$/i.test(ix)) return regAlert('Index number format should be e.g. S0001-0001-2024, P0001-0001-2024 or EQ2024000028-2024.');
    if (!verifiedName) return regAlert('Please verify your name with NECTA first.');
    const ph = req(f.phone), em = req(f.email);
    if (!ph) return regAlert('Enter your phone number.');
    if (!/^\+?[0-9]{9,15}$/.test(ph)) return regAlert('Enter a valid phone number, e.g. +255715000001.');
    if (!em) return regAlert('Enter your email address.');
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(em)) return regAlert('Enter a valid / working email address.');
    return true;
}

async function verifyWithNecta() {
    const f = document.getElementById('reg-form');
    const index = req(f.index_number);
    const firstName = req(f.first_name);
    const errEl = document.getElementById('reg-verify-err');
    const resEl = document.getElementById('reg-verify-result');
    errEl.style.display = 'none'; resEl.style.display = 'none';

    if (!index) return regAlert('Enter your Form Four / Equivalent index number first.');
    if (!firstName) return regAlert('Please enter your first name.');

    const btn = document.getElementById('reg-verify-btn');
    const original = btn.textContent;
    btn.disabled = true; btn.textContent = 'Fetching…';

    try {
        const res = await fetch(FETCH_NAME_ROUTE, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({ index_number: index, first_name: firstName })
        });
        const data = await res.json();
        if (!res.ok || !data.ok || !data.full_name) {
            errEl.textContent = data.error || 'Could not fetch your name from NECTA. Check the index number and first name and try again.';
            errEl.style.display = ''; btn.disabled = false; btn.textContent = original;
            return;
        }
        verifiedName = data.full_name;
        document.getElementById('reg-full-name').textContent = verifiedName;
        resEl.style.display = '';
        btn.textContent = 'Verified ✓'; btn.disabled = true;
    } catch (e) {
        errEl.textContent = 'Network error. Please check your connection and try again.';
        errEl.style.display = ''; btn.disabled = false; btn.textContent = original;
    }
}

document.getElementById('reg-form')?.addEventListener('submit', function (e) {
    if (!validateRegForm()) e.preventDefault();
});
</script>
<style>
@media(max-width:900px){ .reg-grid{grid-template-columns:1fr !important;} }
</style>
@endsection