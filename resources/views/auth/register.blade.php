@extends('layouts.app')
@section('title','Create Account')
@section('content')
<div style="max-width:1100px;margin:0 auto;padding:28px 24px 40px">
    <div style="display:grid;grid-template-columns:1.05fr .95fr;gap:24px;align-items:start;">
        {{-- Left — info --}}
        <div>
            <div style="display:inline-flex;align-items:center;gap:8px;background:var(--terracotta-100);color:var(--terracotta-600);padding:6px 12px;border-radius:20px;font-size:11px;font-weight:800;letter-spacing:.08em;text-transform:uppercase;">{{ \App\Models\Setting::getValue('university_acronym','UDOM') }} Online Admission 2026/2027</div>
            <h1 style="margin-top:14px;font-size:30px;font-weight:800;line-height:1.1;color:var(--coffee-900);">Create your<br><span style="color:var(--terracotta-600)">admission account</span></h1>
            <p style="margin-top:10px;color:var(--ink-soft);font-size:14px;line-height:1.6;">Your name is pulled automatically from <b>NECTA</b> using your Form Four index number — no need to type it in.</p>
            <div class="panel" style="margin-top:18px;">
                <div class="panel-body" style="display:flex;flex-direction:column;gap:12px;">
                    <div style="display:flex;gap:12px;align-items:center;"><div class="thumb thumb-coffee" style="width:36px;height:36px">1</div><div><div class="cell-title" style="font-size:13px">Choose your level &amp; entry type</div><div class="cell-sub">Certificate, Diploma, Bachelor, PG Diploma, Master &amp; PhD</div></div></div>
                    <div style="display:flex;gap:12px;align-items:center;"><div class="thumb thumb-terracotta" style="width:36px;height:36px">2</div><div><div class="cell-title" style="font-size:13px">NECTA verifies your name</div><div class="cell-sub">Matched by index number + first name</div></div></div>
                    <div style="display:flex;gap:12px;align-items:center;"><div class="thumb thumb-green" style="width:36px;height:36px">3</div><div><div class="cell-title" style="font-size:13px">SMS alerts</div><div class="cell-sub">Get texted when your application is submitted or selected</div></div></div>
                    <div style="display:flex;gap:12px;align-items:center;"><div class="thumb thumb-gold" style="width:36px;height:36px">4</div><div><div class="cell-title" style="font-size:13px">Secure &amp; verifiable</div><div class="cell-sub">Encrypted IDs and instant public verification of your admission</div></div></div>
                </div>
            </div>
            <div style="margin-top:14px;display:flex;gap:8px;flex-wrap:wrap;">
                <span class="tag tag-green">6 Study Levels</span><span class="tag tag-gold">{{ \App\Models\Programme::count() }}+ Programmes</span><span class="tag tag-blue">Africa/Dar_es_Salaam</span>
            </div>
        </div>

        {{-- Right — registration wizard --}}
        <div class="panel">
            <div class="panel-head">
                <div>
                    <div class="panel-title">Register</div>
                    <div class="panel-sub">Complete the steps below — one at a time</div>
                </div>
                <span class="tag tag-terracotta">New</span>
            </div>
            <div class="panel-body">
                <form method="POST" action="{{ route('register') }}" id="reg-form">
                    @csrf
                    {{-- Step indicator --}}
                    <div style="display:flex;gap:6px;margin-bottom:18px;">
                        @foreach([['1','Study Level'],['2','NECTA Check'],['3','Contact'],['4','Password']] as [$n,$label])
                        <div class="reg-marker" data-step="{{ $n }}" style="flex:1;text-align:center;background:var(--sand-100);border-radius:8px;padding:6px 4px;font-size:10.5px;font-weight:800;color:var(--coffee-500);letter-spacing:.04em;">{{ $n }}. {{ $label }}</div>
                        @endforeach
                    </div>

                    {{-- Step 1 — Study level & entry --}}
                    <div class="reg-step" data-step="1">
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
                        <div class="field @error('olevel_completion_date') err @enderror" style="margin-top:14px;">
                            <label class="field-label">O'Level (CSEE) Completion Date *</label>
                            <input name="olevel_completion_date" type="date" value="{{ old('olevel_completion_date') }}" max="{{ date('Y-m-d') }}" required>
                            @error('olevel_completion_date')<span class="field-err">{{ $message }}</span>@enderror
                        </div>
                        <div class="field @error('index_number') err @enderror" style="margin-top:14px;">
                            <label class="field-label">Form Four / Equivalent Index No. *</label>
                            <input name="index_number" value="{{ old('index_number') }}" required placeholder="S0001-0001-2024, P0001-0001-2024, or EQ2024000028-2024" style="font-family:'Consolas',monospace;letter-spacing:.02em;">
                            <span class="field-hint">Used by NECTA to fetch your official name</span>
                            @error('index_number')<span class="field-err">{{ $message }}</span>@enderror
                        </div>
                        <button type="button" class="btn btn-primary" style="width:100%;margin-top:18px;" onclick="nextStep(1)">Continue →</button>
                    </div>

                    {{-- Step 2 — NECTA name verification --}}
                    <div class="reg-step" data-step="2" style="display:none;">
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
                        <button type="button" class="btn btn-primary" style="width:100%;margin-top:10px;" id="reg-step2-next" disabled onclick="nextStep(2)">Continue →</button>
                    </div>

                    {{-- Step 3 — Contact details --}}
                    <div class="reg-step" data-step="3" style="display:none;">
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
                        <button type="button" class="btn btn-primary" style="width:100%;margin-top:18px;" onclick="nextStep(3)">Continue →</button>
                    </div>

                    {{-- Step 4 — Password --}}
                    <div class="reg-step" data-step="4" style="display:none;">
                        <div class="field @error('password') err @enderror">
                            <label class="field-label">Password *</label>
                            <input name="password" type="password" required placeholder="••••••••">
                            @error('password')<span class="field-err">{{ $message }}</span>@enderror
                        </div>
                        <div class="field" style="margin-top:14px;">
                            <label class="field-label">Confirm Password *</label>
                            <input name="password_confirmation" type="password" required placeholder="••••••••">
                        </div>
                        <button type="submit" class="btn btn-primary" style="width:100%;margin-top:18px;">Create Account</button>
                    </div>

                    <p style="font-size:13px;text-align:center;margin-top:14px;color:var(--ink-soft);">Already have an account? <a href="{{ route('login') }}" style="color:var(--terracotta-600);font-weight:700;">Sign in</a></p>
                    <p style="text-align:center;margin-top:8px;"><a href="{{ route('home') }}" style="font-size:12px;color:var(--ink-soft);">← Back to home</a></p>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
const FETCH_NAME_ROUTE = "{{ route('register.fetch-name') }}";
let verifiedName = '';

function currentStep() {
    return parseInt(document.querySelector('.reg-step:not([style*="display:none"])')?.dataset.step || '1', 10);
}
function showStep(n) {
    document.querySelectorAll('.reg-step').forEach(s => s.style.display = (parseInt(s.dataset.step, 10) === n) ? '' : 'none');
    document.querySelectorAll('.reg-marker').forEach(m => {
        const s = parseInt(m.dataset.step, 10);
        m.style.background = s === n ? 'var(--terracotta-100)' : (s < n ? 'var(--acacia-100)' : 'var(--sand-100)');
        m.style.color = s === n ? 'var(--terracotta-600)' : (s < n ? 'var(--acacia-600)' : 'var(--coffee-400)');
    });
}
function req(field) { return (field.value || '').trim(); }
function nextStep(n) {
    const f = document.getElementById('reg-form');
    if (n === 1) {
        const lvl = req(f.intended_level_id), at = req(f.application_type), od = req(f.olevel_completion_date), ix = req(f.index_number);
        if (!lvl) return alert('Please select the level you want to study.');
        if (!at) return alert('Please choose your entry / application type.');
        if (!od) return alert('Enter your O\'Level (CSEE) completion date.');
        if (!ix) return alert('Enter your Form Four / Equivalent index number.');
        if (!/^(S\d{4}-\d{4}-\d{4}|P\d{4}-\d{4}-\d{4}|EQ\d{10}-\d{4})$/i.test(ix)) return alert('Index number format should be e.g. S0001-0001-2024, P0001-0001-2024 or EQ2024000028-2024.');
    } else if (n === 2) {
        if (!verifiedName) return alert('Please verify your name with NECTA first.');
    } else if (n === 3) {
        const ph = req(f.phone), em = req(f.email);
        if (!ph) return alert('Enter your phone number.');
        if (!/^\+?[0-9]{9,15}$/.test(ph)) return alert('Enter a valid phone number, e.g. +255715000001.');
        if (!em) return alert('Enter your email address.');
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(em)) return alert('Enter a valid / working email address.');
    }
    showStep(n + 1);
}

async function verifyWithNecta() {
    const f = document.getElementById('reg-form');
    const index = req(f.index_number);
    const firstName = req(f.first_name);
    const errEl = document.getElementById('reg-verify-err');
    const resEl = document.getElementById('reg-verify-result');
    errEl.style.display = 'none'; resEl.style.display = 'none';

    if (!index) return alert('Enter your Form Four / Equivalent index number first (Step 1).');
    if (!firstName) return alert('Please enter your first name.');

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
        document.getElementById('reg-step2-next').disabled = false;
        btn.textContent = 'Verified ✓'; btn.disabled = true;
    } catch (e) {
        errEl.textContent = 'Network error. Please check your connection and try again.';
        errEl.style.display = ''; btn.disabled = false; btn.textContent = original;
    }
}

// Re-open the step that has a server validation error (e.g. email already exists)
(function () {
    const errStep = document.querySelector('.reg-step .field.err')?.closest('.reg-step');
    showStep(errStep ? parseInt(errStep.dataset.step, 10) : 1);
})();
</script>
<style>
@media(max-width:900px){ div[style*="grid-template-columns:1.05fr"]{grid-template-columns:1fr !important;} }
</style>
@endsection