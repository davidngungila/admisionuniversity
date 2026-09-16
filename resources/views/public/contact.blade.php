@extends('layouts.app')
@section('title','Contact')
@section('content')
<div style="max-width:1100px;margin:0 auto;padding:24px 24px 40px">
    <div class="page-head">
        <div>
            <h1>Contact Us</h1>
            <p class="page-sub">Reach the admissions office or send a message.</p>
        </div>
        <div class="page-actions">
            <span class="tag tag-grey">Africa/Dar_es_Salaam</span>
        </div>
    </div>

    <div class="grid-2" style="align-items:start;">
        <div class="panel">
            <div class="panel-head">
                <div class="panel-title">Admissions Office</div>
                <span class="tag tag-terracotta">Main Campus</span>
            </div>
            <div class="panel-body">
                <div class="kv">
                    <div class="kv-row"><span class="k">Address</span><span class="v" style="text-align:right;">Main Campus<br>University</span></div>
                    <div class="kv-row"><span class="k">Email</span><span class="v">admissions@university.ac.tz</span></div>
                    <div class="kv-row"><span class="k">Phone</span><span class="v">+255 7xx xxx xxx</span></div>
                    <div class="kv-row"><span class="k">Hours</span><span class="v">Mon–Fri 08:00–16:30 (Africa/Dar_es_Salaam)</span></div>
                </div>
                <div style="margin-top:16px;display:flex;gap:10px;">
                    <a href="mailto:admissions@university.ac.tz" class="btn btn-ghost btn-sm" style="flex:1;">Email Us</a>
                    <a href="{{ route('public.calendar') }}" class="btn btn-ghost btn-sm" style="flex:1;">Calendar</a>
                </div>
            </div>
        </div>

        <div class="panel">
            <div class="panel-head">
                <div class="panel-title">Send a Message</div>
                <span class="tag tag-grey">We reply within 24h</span>
            </div>
            <div class="panel-body">
                <form class="form-grid" onsubmit="return false;">
                    <div class="field">
                        <label class="field-label">Name</label>
                        <input placeholder="Your full name">
                    </div>
                    <div class="field">
                        <label class="field-label">Email</label>
                        <input type="email" placeholder="you@example.com">
                    </div>
                    <div class="field" style="grid-column:1/-1;">
                        <label class="field-label">Message</label>
                        <textarea rows="4" placeholder="How can we help?"></textarea>
                        <span class="field-hint">Messages are currently demo-only until SMTP is configured.</span>
                    </div>
                    <div class="form-actions" style="grid-column:1/-1;margin-top:4px;">
                        <button type="button" onclick="toast('Message sending will be enabled after SMTP is configured.','info')" class="btn btn-primary" style="width:100%;">Send Message</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
