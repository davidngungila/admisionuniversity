@extends('layouts.app')
@section('title','Home')
@section('content')
<div style="max-width:1100px;margin:0 auto;padding:24px 24px 40px">

    <div style="margin-top:28px;" class="panel">
        <div class="panel-body" style="text-align:center;padding:32px 24px;">
            <div style="display:inline-flex;align-items:center;gap:8px;background:var(--terracotta-100);color:var(--terracotta-600);padding:6px 12px;border-radius:20px;font-size:11px;font-weight:800;letter-spacing:.08em;text-transform:uppercase;">Welcome to {{ \App\Models\Setting::getValue('university_acronym','UDOM') }} Online Application System</div>
            <h2 style="margin-top:14px;font-size:22px;font-weight:800;color:var(--coffee-900);">Your gateway to academic excellence at the University of Dodoma</h2>
            <p style="margin-top:10px;color:var(--ink-soft);font-size:14px;line-height:1.6;max-width:640px;margin-left:auto;margin-right:auto;">Join a thriving academic community with exceptional opportunities, modern facilities and supportive learning environment.</p>
            <div style="margin-top:18px;display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:14px;text-align:left;">
                <div style="border:1px solid var(--line);border-radius:12px;padding:16px;background:var(--sand-50);"><div style="font-weight:800;color:var(--coffee-900);">Application Process</div><div class="cell-sub" style="margin-top:4px;">Simple and straightforward application process. Create an account, fill in your details, select your preferred programme, and submit your application online.</div></div>
                <div style="border:1px solid var(--line);border-radius:12px;padding:16px;background:var(--sand-50);"><div style="font-weight:800;color:var(--coffee-900);">Admission Requirements</div><div class="cell-sub" style="margin-top:4px;">Clear admission criteria for all programmes. Check specific requirements for certificates, diplomas, bachelor's, master's, and PhD programmes.</div></div>
                <div style="border:1px solid var(--line);border-radius:12px;padding:16px;background:var(--sand-50);"><div style="font-weight:800;color:var(--coffee-900);">Important Dates</div><div class="cell-sub" style="margin-top:4px;">Stay updated with application deadlines, examination dates, and important academic calendar events for the current admission cycle.</div></div>
            </div>
            {{-- Admission Guidance after Important Dates — centered --}}
            <div style="margin-top:22px;max-width:900px;margin-left:auto;margin-right:auto;text-align:center;">
                <div style="display:flex;flex-direction:column;align-items:center;gap:4px;margin-bottom:14px;">
                    <h3 style="font-size:18px;font-weight:800;color:var(--coffee-900);margin:0;">Admission Guidance</h3>
                    <p style="font-size:12.5px;color:var(--ink-soft);margin:0;">Quick access — like guidelines page, now on home</p>
                </div>
                <div class="stat-grid" style="text-align:left;">
                    <a href="{{ route('public.programmes') }}" class="stat-card" style="text-decoration:none;align-items:center;text-align:center;">
                        <div class="stat-icon ic-blue" style="align-self:center;"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg></div>
                        <div class="stat-label">1. Admission Info</div>
                        <div class="stat-sub">Programmes and application process</div>
                        <span class="tag tag-blue" style="margin-top:8px;width:fit-content;align-self:center">Learn More</span>
                    </a>
                    <a href="{{ route('public.requirements') }}" class="stat-card" style="text-decoration:none;align-items:center;text-align:center;">
                        <div class="stat-icon ic-gold" style="align-self:center;"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/></svg></div>
                        <div class="stat-label">2. Requirements</div>
                        <div class="stat-sub">Check entry criteria first.</div>
                        <span class="tag tag-gold" style="margin-top:8px;width:fit-content;align-self:center">View Requirements</span>
                    </a>
                    <a href="{{ route('public.calendar') }}" class="stat-card" style="text-decoration:none;align-items:center;text-align:center;">
                        <div class="stat-icon ic-green" style="align-self:center;"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg></div>
                        <div class="stat-label">3. Calendar</div>
                        <div class="stat-sub">Check deadlines &amp; windows.</div>
                        <span class="tag tag-green" style="margin-top:8px;width:fit-content;align-self:center">View Calendar</span>
                    </a>
                </div>
                <div class="home-2col" style="margin-top:14px;display:grid;grid-template-columns:1fr 1fr;gap:14px;">
                    <a href="{{ route('register') }}" class="panel" style="padding:18px;display:block;text-decoration:none;">
                        <div style="font-weight:800;color:var(--terracotta-600);font-size:12px;letter-spacing:.06em;text-transform:uppercase;">Create Account</div>
                        <div style="font-weight:700;color:var(--coffee-900);margin-top:4px;">Register for a new applicant account</div>
                        <div class="cell-sub" style="margin-top:4px;">Start your application in minutes</div>
                    </a>
                    <a href="{{ route('login') }}" class="panel" style="padding:18px;display:block;text-decoration:none;">
                        <div style="font-weight:800;color:var(--acacia-600);font-size:12px;letter-spacing:.06em;text-transform:uppercase;">Login</div>
                        <div style="font-weight:700;color:var(--coffee-900);margin-top:4px;">Access your application dashboard</div>
                        <div class="cell-sub" style="margin-top:4px;">Track status &amp; download letters</div>
                    </a>
                </div>
            </div>
            <div style="margin-top:22px;padding:18px;border:1px solid var(--line);border-radius:12px;background:var(--white);text-align:left;">
                <div style="font-weight:800;color:var(--coffee-900);text-align:center;">Why Choose {{ \App\Models\Setting::getValue('university_acronym','UDOM') }}?</div>
                <div class="why-grid" style="margin-top:10px;display:grid;grid-template-columns:repeat(4,1fr);gap:12px;text-align:center;">
                    <div><div style="font-weight:800;font-size:18px;color:var(--terracotta-600)">50+</div><div style="font-size:11px;letter-spacing:.06em;text-transform:uppercase;color:var(--ink-soft);font-weight:700">Academic Programmes</div></div>
                    <div><div style="font-weight:800;font-size:18px;color:var(--terracotta-600)">15,000+</div><div style="font-size:11px;letter-spacing:.06em;text-transform:uppercase;color:var(--ink-soft);font-weight:700">Current Students</div></div>
                    <div><div style="font-weight:800;font-size:18px;color:var(--terracotta-600)">200+</div><div style="font-size:11px;letter-spacing:.06em;text-transform:uppercase;color:var(--ink-soft);font-weight:700">Qualified Faculty</div></div>
                    <div><div style="font-weight:800;font-size:18px;color:var(--terracotta-600)">100%</div><div style="font-size:11px;letter-spacing:.06em;text-transform:uppercase;color:var(--ink-soft);font-weight:700">Online Application</div></div>
                </div>
            </div>
            <div style="margin-top:18px;padding:18px;border-radius:12px;background:linear-gradient(135deg,var(--coffee-900),var(--terracotta-600));color:#fff;">
                <div style="font-weight:800;font-size:16px;">Ready to Start Your Academic Journey?</div>
                <div style="font-size:13px;opacity:.85;margin-top:4px;">Take the first step towards your dream career. Join thousands of students who have chosen {{ \App\Models\Setting::getValue('university_acronym','UDOM') }} for quality education and bright futures.</div>
                <div style="margin-top:12px;display:flex;gap:10px;justify-content:center;flex-wrap:wrap;">
                    <a href="{{ route('register') }}" class="btn btn-primary" style="background:var(--gold-500);">Create Account</a>
                    <a href="{{ route('public.calendar') }}" class="btn" style="background:#fff;color:var(--coffee-900);">View Calendar</a>
                </div>
            </div>
        </div>
    </div>

    {{-- Modals like guidelines --}}
    <div class="modal-backdrop" id="homeNeedHelpModal" onclick="if(event.target===this)closeModal('homeNeedHelpModal')"><div class="modal" style="max-width:480px;"><div class="modal-head"><div><h3>Need Help?</h3><p>Contact admissions for guidance.</p></div><button class="modal-close" onclick="closeModal('homeNeedHelpModal')">×</button></div><div class="modal-body"><p style="font-size:13.5px;line-height:1.6;color:var(--coffee-800)">Our admissions team is ready to help. Reach us via the UDOM Support float button, email {{ \App\Models\Setting::getValue('admissions_email','admissions@university.ac.tz') }} or {{ \App\Models\Setting::getValue('admissions_phone','+255 26 231 0300') }}. Office hours Mon–Fri 08:00–17:00.</p><div style="margin-top:14px;display:flex;gap:8px;flex-wrap:wrap;"><a href="{{ route('public.contact') }}" class="btn btn-primary btn-sm">Contact Us</a><button class="btn btn-ghost btn-sm" onclick="closeModal('homeNeedHelpModal')">Close</button></div></div></div></div>
    <div class="modal-backdrop" id="homeReqModal" onclick="if(event.target===this)closeModal('homeReqModal')"><div class="modal" style="max-width:480px;"><div class="modal-head"><div><h3>Requirements</h3><p>Check entry criteria first.</p></div><button class="modal-close" onclick="closeModal('homeReqModal')">×</button></div><div class="modal-body"><p style="font-size:13.5px;line-height:1.6;color:var(--coffee-800)">Each programme has specific entry requirements (O-Level, A-Level, diploma, degree). Check the Requirements page for detailed criteria per level.</p><div style="margin-top:14px;display:flex;gap:8px;"><a href="{{ route('public.requirements') }}" class="btn btn-primary btn-sm">View Requirements</a><button class="btn btn-ghost btn-sm" onclick="closeModal('homeReqModal')">Close</button></div></div></div></div>
    <div class="modal-backdrop" id="homeCalModal" onclick="if(event.target===this)closeModal('homeCalModal')"><div class="modal" style="max-width:480px;"><div class="modal-head"><div><h3>Calendar</h3><p>Check deadlines &amp; windows.</p></div><button class="modal-close" onclick="closeModal('homeCalModal')">×</button></div><div class="modal-body"><p style="font-size:13.5px;line-height:1.6;color:var(--coffee-800)">Application windows are time-bound. Check the calendar table above for OPEN / CLOSING SOON / CLOSED status and apply before deadline.</p><div style="margin-top:14px;display:flex;gap:8px;"><a href="{{ route('public.calendar') }}" class="btn btn-primary btn-sm">View Calendar</a><button class="btn btn-ghost btn-sm" onclick="closeModal('homeCalModal')">Close</button></div></div></div></div>
    <div class="modal-backdrop" id="homeAppInfoModal" onclick="if(event.target===this)closeModal('homeAppInfoModal')"><div class="modal" style="max-width:520px;"><div class="modal-head"><div><h3>Admission Info</h3><p>Programmes and application process</p></div><button class="modal-close" onclick="closeModal('homeAppInfoModal')">×</button></div><div class="modal-body"><p style="font-size:13.5px;line-height:1.6;color:var(--coffee-800)">UDOM offers 50+ programmes from Certificate to PhD. Create an account, complete 5–8 dynamic steps (personal info, payment, results, programmes, documents, submit) and track your status.</p><div style="margin-top:14px;display:flex;gap:8px;flex-wrap:wrap;"><a href="{{ route('public.programmes') }}" class="btn btn-primary btn-sm">Browse Programmes</a><a href="{{ route('register') }}" class="btn btn-ghost btn-sm">Create Account</a></div></div></div></div>

<style>
    @media(max-width:900px){ .why-grid{grid-template-columns:repeat(2,1fr)!important;} .home-2col{grid-template-columns:1fr!important;} }
    @media(max-width:420px){ .why-grid{grid-template-columns:1fr!important;} }
</style>
</div>
@endsection
