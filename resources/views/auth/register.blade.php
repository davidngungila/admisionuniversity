@extends('layouts.app')
@section('title','Create Account')
@section('content')
<div style="max-width:1100px;margin:0 auto;padding:28px 24px 40px">
    <div style="display:grid;grid-template-columns:1.05fr .95fr;gap:24px;align-items:start;">
        {{-- Left — info (2-part left) --}}
        <div>
            <div style="display:inline-flex;align-items:center;gap:8px;background:var(--terracotta-100);color:var(--terracotta-600);padding:6px 12px;border-radius:20px;font-size:11px;font-weight:800;letter-spacing:.08em;text-transform:uppercase;">{{ \App\Models\Setting::getValue('university_acronym','UDOM') }} Online Admission 2026/2027</div>
            <h1 style="margin-top:14px;font-size:30px;font-weight:800;line-height:1.1;color:var(--coffee-900);">Create your<br><span style="color:var(--terracotta-600)">admission account</span></h1>
            <p style="margin-top:10px;color:var(--ink-soft);font-size:14px;line-height:1.6;">Join {{ \App\Models\Setting::getValue('university_acronym','UDOM') }} OAS to apply for Certificate, Diploma, Bachelor, PGD, Masters &amp; PhD. One account, full application tracking, instant verification.</p>
            <div class="panel" style="margin-top:18px;">
                <div class="panel-body" style="display:flex;flex-direction:column;gap:12px;">
                    <div style="display:flex;gap:12px;align-items:center;"><div class="thumb thumb-coffee" style="width:36px;height:36px">1</div><div><div class="cell-title" style="font-size:13px">Multiple study levels</div><div class="cell-sub">Certificate, Diploma, Bachelor, PG Diploma, Master &amp; PhD</div></div></div>
                    <div style="display:flex;gap:12px;align-items:center;"><div class="thumb thumb-terracotta" style="width:36px;height:36px">2</div><div><div class="cell-title" style="font-size:13px">One account, everything</div><div class="cell-sub">Apply, pay, upload documents and track status in one place</div></div></div>
                    <div style="display:flex;gap:12px;align-items:center;"><div class="thumb thumb-green" style="width:36px;height:36px">3</div><div><div class="cell-title" style="font-size:13px">SMS alerts</div><div class="cell-sub">Get texted when your application is submitted or selected</div></div></div>
                    <div style="display:flex;gap:12px;align-items:center;"><div class="thumb thumb-gold" style="width:36px;height:36px">4</div><div><div class="cell-title" style="font-size:13px">Secure &amp; verifiable</div><div class="cell-sub">Encrypted IDs and instant public verification of your admission</div></div></div>
                </div>
            </div>
            <div style="margin-top:14px;display:flex;gap:8px;flex-wrap:wrap;">
                <span class="tag tag-green">6 Study Levels</span><span class="tag tag-gold">{{ \App\Models\Programme::count() }}+ Programmes</span><span class="tag tag-blue">Africa/Dar_es_Salaam</span>
            </div>
        </div>

        {{-- Right — form (2-part right) --}}
        <div class="panel">
            <div class="panel-head">
                <div>
                    <div class="panel-title">Register</div>
                    <div class="panel-sub">Start your application — 2 fields per row</div>
                </div>
                <span class="tag tag-terracotta">New</span>
            </div>
            <div class="panel-body">
                <form method="POST" action="{{ route('register') }}">
                    @csrf
                    <div class="form-grid" style="gap:14px;">
                        <div class="field @error('name') err @enderror">
                            <label class="field-label">Full Name *</label>
                            <input name="name" value="{{ old('name') }}" required placeholder="e.g. Tausila Macarius Mlula">
                            @error('name')<span class="field-err">{{ $message }}</span>@enderror
                        </div>
                        <div class="field @error('email') err @enderror">
                            <label class="field-label">Email *</label>
                            <input name="email" type="email" value="{{ old('email') }}" required placeholder="you@example.com">
                            @error('email')<span class="field-err">{{ $message }}</span>@enderror
                        </div>
                    </div>
                    <div class="form-grid" style="gap:14px;margin-top:14px;">
                        <div class="field @error('phone') err @enderror">
                            <label class="field-label">Phone *</label>
                            <input name="phone" value="{{ old('phone') }}" required placeholder="+2557xxxxxxxx">
                            <span class="field-hint">Include country code</span>
                            @error('phone')<span class="field-err">{{ $message }}</span>@enderror
                        </div>
                        <div class="field @error('intended_level_id') err @enderror">
                            <label class="field-label">I want to apply for *</label>
                            <select name="intended_level_id" required>
                                <option value="">— Select level —</option>
                                @foreach(($levels ?? []) as $lv)
                                    <option value="{{ $lv->id }}" @selected(old('intended_level_id')==$lv->id)>{{ $lv->name }} @if($lv->short_name) ({{ $lv->short_name }}) @endif</option>
                                @endforeach
                            </select>
                            <span class="field-hint">Certificate → PhD</span>
                            @error('intended_level_id')<span class="field-err">{{ $message }}</span>@enderror
                        </div>
                    </div>
                    <div class="form-grid" style="gap:14px;margin-top:14px;">
                        <div class="field @error('password') err @enderror">
                            <label class="field-label">Password *</label>
                            <input name="password" type="password" required placeholder="••••••••">
                            @error('password')<span class="field-err">{{ $message }}</span>@enderror
                        </div>
                        <div class="field">
                            <label class="field-label">Confirm Password *</label>
                            <input name="password_confirmation" type="password" required placeholder="••••••••">
                        </div>
                    </div>
                    <button class="btn btn-primary" style="width:100%;margin-top:18px;">Create Account</button>
                    <p style="font-size:13px;text-align:center;margin-top:14px;color:var(--ink-soft);">Already have an account? <a href="{{ route('login') }}" style="color:var(--terracotta-600);font-weight:700;">Sign in</a></p>
                    <p style="text-align:center;margin-top:8px;"><a href="{{ route('home') }}" style="font-size:12px;color:var(--ink-soft);">← Back to home</a></p>
                </form>
            </div>
        </div>
    </div>
</div>
<style>
@media(max-width:900px){ div[style*="grid-template-columns:1.05fr"]{grid-template-columns:1fr !important;} }
</style>
@endsection
