@extends('layouts.app')
@section('title','Application Guidelines')
@section('content')
<div style="max-width:1100px;margin:0 auto;padding:24px 24px 40px">
    <div class="page-head">
        <div>
            <h1>Application Guidelines</h1>
            <p class="page-sub">Steps are dynamic per admission level (Bachelor: 5 steps; Masters/PhD: 7–8 steps) and driven by workflow configuration.</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Create Account</a>
            <a href="{{ route('public.calendar') }}" class="btn btn-ghost btn-sm">View Calendar</a>
        </div>
    </div>

    <div class="panel">
        <div class="panel-head">
            <div class="panel-title">How to Apply</div>
            <span class="tag tag-terracotta">7 Steps</span>
        </div>
        <div class="panel-body">
            <div style="display:flex;flex-direction:column;gap:12px;">
                <div style="display:flex;gap:14px;align-items:flex-start;padding:14px;border:1px solid var(--line);border-radius:12px;background:var(--white);">
                    <div class="thumb thumb-terracotta" style="flex:none;width:36px;height:36px;">1</div>
                    <div><div style="font-weight:700;color:var(--coffee-900);">Create an account and verify your email.</div><div class="muted" style="font-size:12.5px;margin-top:2px;">Use a valid email and phone number.</div></div>
                </div>
                <div style="display:flex;gap:14px;align-items:flex-start;padding:14px;border:1px solid var(--line);border-radius:12px;background:var(--white);">
                    <div class="thumb thumb-gold" style="flex:none;width:36px;height:36px;">2</div>
                    <div><div style="font-weight:700;color:var(--coffee-900);">Complete Personal Information (Step 1).</div><div class="muted" style="font-size:12.5px;margin-top:2px;">Provide accurate personal details.</div></div>
                </div>
                <div style="display:flex;gap:14px;align-items:flex-start;padding:14px;border:1px solid var(--line);border-radius:12px;background:var(--white);">
                    <div class="thumb thumb-green" style="flex:none;width:36px;height:36px;">3</div>
                    <div><div style="font-weight:700;color:var(--coffee-900);">Pay the application fee (Step 2) — a control number is issued.</div><div class="muted" style="font-size:12.5px;margin-top:2px;">For mobile/bank payment.</div></div>
                </div>
                <div style="display:flex;gap:14px;align-items:flex-start;padding:14px;border:1px solid var(--line);border-radius:12px;background:var(--white);">
                    <div class="thumb thumb-blue" style="flex:none;width:36px;height:36px;">4</div>
                    <div><div style="font-weight:700;color:var(--coffee-900);">Enter Academic Results (Step 3).</div><div class="muted" style="font-size:12.5px;margin-top:2px;">Add O-Level, A-Level or diploma results.</div></div>
                </div>
                <div style="display:flex;gap:14px;align-items:flex-start;padding:14px;border:1px solid var(--line);border-radius:12px;background:var(--white);">
                    <div class="thumb thumb-grey" style="flex:none;width:36px;height:36px;">5</div>
                    <div><div style="font-weight:700;color:var(--coffee-900);">Choose up to 3 programmes in order of preference (Step 4).</div><div class="muted" style="font-size:12.5px;margin-top:2px;">Rank by priority.</div></div>
                </div>
                <div style="display:flex;gap:14px;align-items:flex-start;padding:14px;border:1px solid var(--line);border-radius:12px;background:var(--white);">
                    <div class="thumb thumb-coffee" style="flex:none;width:36px;height:36px;">6</div>
                    <div><div style="font-weight:700;color:var(--coffee-900);">Upload required documents (Step 5, where applicable).</div><div class="muted" style="font-size:12.5px;margin-top:2px;">Certificates, IDs, photos.</div></div>
                </div>
                <div style="display:flex;gap:14px;align-items:flex-start;padding:14px;border:1px solid var(--line);border-radius:12px;background:var(--sand-100);">
                    <div class="thumb thumb-terracotta" style="flex:none;width:36px;height:36px;">7</div>
                    <div><div style="font-weight:700;color:var(--coffee-900);">Review and Submit (final step).</div><div class="muted" style="font-size:12.5px;margin-top:2px;">Your application number is then generated. Keep it safe for verification.</div></div>
                </div>
            </div>
            <div style="margin-top:16px;padding:12px 14px;background:var(--sand-100);border:1px solid var(--line);border-radius:10px;font-size:12.5px;color:var(--ink-soft);line-height:1.5;">
                The steps shown are dynamic per admission level (Bachelor: 5 steps; Masters/PhD: 7–8 steps) and driven by the workflow configuration.
            </div>
        </div>
    </div>

    <div class="stat-grid" style="margin-top:18px;">
        <div class="stat-card">
            <div class="stat-icon ic-terracotta"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/></svg></div>
            <div class="stat-label">Need Help?</div>
            <div class="stat-sub">Contact admissions for guidance.</div>
            <a href="{{ route('public.contact') }}" class="btn btn-ghost btn-sm" style="margin-top:10px;">Contact Us</a>
        </div>
        <div class="stat-card">
            <div class="stat-icon ic-gold"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/></svg></div>
            <div class="stat-label">Requirements</div>
            <div class="stat-sub">Check entry criteria first.</div>
            <a href="{{ route('public.requirements') }}" class="btn btn-ghost btn-sm" style="margin-top:10px;">View Requirements</a>
        </div>
        <div class="stat-card">
            <div class="stat-icon ic-green"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg></div>
            <div class="stat-label">Calendar</div>
            <div class="stat-sub">Check deadlines &amp; windows.</div>
            <a href="{{ route('public.calendar') }}" class="btn btn-ghost btn-sm" style="margin-top:10px;">View Calendar</a>
        </div>
    </div>
</div>
@endsection
