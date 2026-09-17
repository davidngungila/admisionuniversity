@extends('layouts.app')
@section('title','Login')
@section('content')
<div style="max-width:1100px;margin:0 auto;padding:28px 24px 40px">
    <div style="display:grid;grid-template-columns:.95fr 1.05fr;gap:24px;align-items:start;">
        {{-- Left — info (hidden on small screens) --}}
        <div class="login-left">
            <div style="display:inline-flex;align-items:center;gap:8px;background:var(--acacia-100);color:var(--acacia-600);padding:6px 12px;border-radius:20px;font-size:11px;font-weight:800;letter-spacing:.08em;text-transform:uppercase;">Welcome back</div>
            <h1 style="margin-top:14px;font-size:30px;font-weight:800;line-height:1.1;color:var(--coffee-900);">Sign in to<br><span style="color:var(--terracotta-600)">your admission account</span></h1>
            <p style="margin-top:10px;color:var(--ink-soft);font-size:14px;line-height:1.6;">Access your dashboard, continue your application, track status and download letters. Same header as homepage (top bar, nav, marquee, bottom bar).</p>
            <div class="panel" style="margin-top:18px;">
                <div class="panel-body" style="display:flex;flex-direction:column;gap:12px;">
                    <div style="display:flex;gap:12px;align-items:center;"><div class="thumb thumb-coffee" style="width:36px;height:36px">1</div><div><div class="cell-title" style="font-size:13px">Resume where you left off</div><div class="cell-sub">Continue a saved draft or an in-progress application anytime</div></div></div>
                    <div style="display:flex;gap:12px;align-items:center;"><div class="thumb thumb-terracotta" style="width:36px;height:36px">2</div><div><div class="cell-title" style="font-size:13px">Real-time status tracking</div><div class="cell-sub">Follow every application from submission to selection</div></div></div>
                    <div style="display:flex;gap:12px;align-items:center;"><div class="thumb thumb-green" style="width:36px;height:36px">3</div><div><div class="cell-title" style="font-size:13px">SMS alerts</div><div class="cell-sub">Get texted when your application is submitted or selected</div></div></div>
                    <div style="display:flex;gap:12px;align-items:center;"><div class="thumb thumb-gold" style="width:36px;height:36px">4</div><div><div class="cell-title" style="font-size:13px">Secure &amp; verifiable</div><div class="cell-sub">Encrypted IDs and instant public verification of your admission</div></div></div>
                </div>
            </div>
        </div>

        {{-- Right — form --}}
        <div class="panel">
            <div class="panel-head">
                <div>
                    <div class="panel-title">Login</div>
                    <div class="panel-sub">Sign in to continue</div>
                </div>
                <span class="tag tag-green">Secure</span>
            </div>
            <div class="panel-body">
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="form-grid" style="gap:14px;">
                        <div class="field @error('email') @error('login') err @enderror">
                            <label class="field-label">Email or Index Number (Username) *</label>
                            <input name="login" type="text" value="{{ old('email', old('login')) }}" required placeholder="you@example.com or S0001-0001-2015">
                            @error('email')<span class="field-err">{{ $message }}</span>@enderror
                            @error('login')<span class="field-err">{{ $message }}</span>@enderror
                        </div>
                        <div class="field @error('password') err @enderror">
                            <label class="field-label">Password *</label>
                            <input name="password" type="password" required placeholder="••••••••">
                            @error('password')<span class="field-err">{{ $message }}</span>@enderror
                        </div>
                    </div>
                    <label class="check-row" style="margin-top:14px;"><input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> Remember me</label>
                    @if($errors->has('email') && str_contains($errors->first('email'),'credentials'))
                        <div class="field-err" style="margin-top:10px;">Invalid credentials. Please try again.</div>
                    @endif
                    <button class="btn btn-primary" style="width:100%;margin-top:16px;">Sign In</button>
                    <p style="font-size:13px;text-align:center;margin-top:14px;color:var(--ink-soft);">No account? <a href="{{ route('register') }}" style="color:var(--terracotta-600);font-weight:700;">Create one</a></p>
                    <p style="text-align:center;margin-top:8px;"><a href="{{ route('home') }}" style="font-size:12px;color:var(--ink-soft);">← Back to home</a></p>
                </form>
            </div>
        </div>
    </div>
</div>
<style>
@media(max-width:900px){ div[style*="grid-template-columns:.95fr"]{grid-template-columns:1fr !important;} .login-left{display:none !important;} }
</style>
@endsection
