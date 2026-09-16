@extends('layouts.app')
@section('title','Welcome')
@section('content')
<div style="max-width:1100px;margin:0 auto;padding:24px 24px 40px">
    <div style="background:linear-gradient(135deg,var(--coffee-900),var(--terracotta-600));color:#fff;padding:48px 24px;border-radius:18px;text-align:center;">
        <div style="display:inline-flex;align-items:center;gap:8px;background:rgba(255,255,255,.14);border:1px solid rgba(255,255,255,.18);border-radius:20px;padding:6px 12px;font-size:12px;font-weight:700;"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex:none"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M6 12l6 3 6-3"/><path d="M22 10v6"/></svg> {{ \App\Models\Setting::getValue('university_name', config('app.name','University OAS')) }} — Online Admission</div>
        <h1 style="margin-top:16px;font-size:32px;font-weight:800;line-height:1.15;color:#fff;">Welcome to {{ \App\Models\Setting::getValue('university_name','University OAS') }}</h1>
        <p style="margin-top:10px;color:rgba(255,255,255,.82);font-size:14px;line-height:1.6;max-width:560px;margin-left:auto;margin-right:auto;">Tanzania University Online Admission System — Apply for Certificate, Diploma, Bachelor, PGD, Masters &amp; PhD. Configuration-driven and built for transparency.</p>
        <div style="margin-top:20px;display:flex;gap:10px;justify-content:center;flex-wrap:wrap;">
            <a href="{{ route('public.programmes') }}" class="btn" style="background:#fff;color:var(--coffee-900);">Browse Programmes</a>
            <a href="{{ route('public.calendar') }}" class="btn btn-primary" style="background:var(--gold-500);">View Calendar</a>
            @auth
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-ghost" style="background:rgba(255,255,255,.12);color:#fff;border-color:rgba(255,255,255,.2);">Admin Panel</a>
                @else
                    <a href="{{ route('applicant.dashboard') }}" class="btn btn-ghost" style="background:rgba(255,255,255,.12);color:#fff;border-color:rgba(255,255,255,.2);">My Application</a>
                @endif
            @else
                <a href="{{ route('login') }}" class="btn btn-ghost" style="background:rgba(255,255,255,.12);color:#fff;border-color:rgba(255,255,255,.2);">Login</a>
                <a href="{{ route('register') }}" class="btn" style="background:var(--coffee-900);color:#fff;">Create Account</a>
            @endauth
        </div>
    </div>

    <div class="stat-grid" style="margin-top:22px;">
        <div class="stat-card">
            <div class="stat-top"><div class="stat-icon ic-terracotta"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 10v6a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2v-6"/><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M6 12l6 3 6-3"/></svg></div><span class="tag tag-terracotta">Programmes</span></div>
            <div class="stat-value">{{ \App\Models\Programme::count() }}+</div>
            <div class="stat-label">Academic Programmes</div>
            <div class="stat-sub">Certificate to PhD</div>
        </div>
        <div class="stat-card">
            <div class="stat-top"><div class="stat-icon ic-gold"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg></div><span class="tag tag-gold">Calendar</span></div>
            <div class="stat-value">{{ \App\Models\AdmissionWindow::count() }}</div>
            <div class="stat-label">Admission Windows</div>
            <div class="stat-sub">Rounds &amp; deadlines</div>
        </div>
        <div class="stat-card">
            <div class="stat-top"><div class="stat-icon ic-green"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><path d="M20 8v6"/><path d="M23 11v2"/><path d="M17 11v2"/></svg></div><span class="tag tag-green">Support</span></div>
            <div class="stat-value">24h</div>
            <div class="stat-label">Admissions Support</div>
            <div class="stat-sub">Contact us anytime</div>
        </div>
    </div>

    <div class="panel" style="margin-top:18px;">
        <div class="panel-head">
            <div class="panel-title">Quick Links</div>
            <div class="panel-sub">Jump to what you need</div>
        </div>
        <div class="panel-body">
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:12px;">
                <a href="{{ route('public.calendar') }}" class="panel" style="padding:16px;display:block;">
                    <div style="font-weight:700;color:var(--coffee-900);">Calendar</div>
                    <div class="muted" style="font-size:12.5px;margin-top:4px;">Application windows &amp; deadlines</div>
                </a>
                <a href="{{ route('public.programmes') }}" class="panel" style="padding:16px;display:block;">
                    <div style="font-weight:700;color:var(--coffee-900);">Programmes</div>
                    <div class="muted" style="font-size:12.5px;margin-top:4px;">Browse &amp; view requirements</div>
                </a>
                <a href="{{ route('public.fees') }}" class="panel" style="padding:16px;display:block;">
                    <div style="font-weight:700;color:var(--coffee-900);">Fees</div>
                    <div class="muted" style="font-size:12.5px;margin-top:4px;">Per-window fees</div>
                </a>
                <a href="{{ route('public.verify') }}" class="panel" style="padding:16px;display:block;">
                    <div style="font-weight:700;color:var(--coffee-900);">Verify</div>
                    <div class="muted" style="font-size:12.5px;margin-top:4px;">Check admission letter</div>
                </a>
            </div>
            <div style="margin-top:16px;display:flex;gap:10px;flex-wrap:wrap;">
                <a href="{{ route('home') }}" class="btn btn-primary">Go to Home</a>
                <a href="{{ route('public.guidelines') }}" class="btn btn-ghost">Guidelines</a>
                <a href="{{ route('public.contact') }}" class="btn btn-ghost">Contact</a>
            </div>
        </div>
    </div>

    <p class="muted" style="text-align:center;margin-top:18px;font-size:12px;">v{{ app()->version() }} · <a href="https://laravel.com/docs" target="_blank" style="color:var(--terracotta-600);font-weight:700;">Docs</a> · <a href="https://laracasts.com" target="_blank" style="color:var(--terracotta-600);font-weight:700;">Laracasts</a></p>
</div>
@endsection
