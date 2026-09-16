<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title','Admin') — {{ \App\Models\Setting::getValue('university_name','University OAS') }} Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Raleway:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root{--sand-50:#FBF7EF;--sand-100:#F4ECDC;--sand-200:#E9DCC0;--coffee-900:#2A1B10;--coffee-800:#3B2718;--coffee-700:#4D3422;--coffee-500:#7A5C42;--coffee-300:#A98968;--terracotta-600:#C2592B;--terracotta-500:#D06B3A;--terracotta-100:#F6E1D3;--acacia-600:#5E6E3F;--acacia-500:#7A8450;--acacia-100:#E2E7D4;--gold-500:#D4A24C;--gold-100:#F7E9CB;--ink:#241408;--ink-soft:#6B5A48;--line:#E4D7C2;--white:#fff;--danger:#B33A3A;--danger-100:#F6DCDA;--sidebar-w:264px;--sidebar-w-collapsed:76px;--topbar-h:72px;--radius-md:14px;--shadow-sm:0 1px 2px rgba(42,27,16,.08);}
        *{box-sizing:border-box;}html,body{height:100%;}body{margin:0;font-family:'Raleway',sans-serif;background:var(--sand-50);color:var(--ink);-webkit-font-smoothing:antialiased;overflow-x:hidden;}
        h1,h2,h3{margin:0;color:var(--coffee-900);}a{color:inherit;text-decoration:none;}button{font-family:inherit;cursor:pointer;}input,select,textarea{font-family:inherit;}
        ::-webkit-scrollbar{width:9px;height:9px;}::-webkit-scrollbar-thumb{background:var(--coffee-300);border-radius:10px;}
        .sidebar{position:fixed;top:0;left:0;bottom:0;width:var(--sidebar-w);z-index:200;background:var(--coffee-900);background-image:radial-gradient(circle at 0% 0%, rgba(212,162,76,.10), transparent 55%);display:flex;flex-direction:column;transition:width .25s, transform .25s;border-right:1px solid rgba(255,255,255,.06);}
        .sidebar.collapsed{width:var(--sidebar-w-collapsed);}
        .sb-brand{display:flex;align-items:center;gap:12px;padding:22px 20px;border-bottom:1px solid rgba(255,255,255,.08);min-height:var(--topbar-h);}
        .sb-mark{width:38px;height:38px;border-radius:10px;flex:none;background:linear-gradient(155deg,var(--terracotta-600),var(--gold-500));display:flex;align-items:center;justify-content:center;color:#fff;font-weight:800;}
        .sb-brand-text{overflow:hidden;white-space:nowrap;}
        .sb-brand-text strong{display:block;color:#fff;font-size:15px;line-height:1.1;}
        .sb-brand-text span{display:block;color:var(--gold-500);font-size:11px;letter-spacing:.06em;text-transform:uppercase;font-weight:600;}
        .sidebar.collapsed .sb-brand-text{display:none;}
        .sb-nav{flex:1;overflow-y:auto;padding:16px 12px;}
        .sb-label{color:rgba(255,255,255,.32);font-size:10.5px;font-weight:700;letter-spacing:.09em;text-transform:uppercase;padding:14px 12px 8px;}
        .sidebar.collapsed .sb-label{display:none;}
        .sb-item{display:flex;align-items:center;gap:13px;padding:11px 12px;border-radius:10px;color:rgba(255,255,255,.62);font-size:14px;font-weight:500;margin-bottom:2px;position:relative;white-space:nowrap;}
        .sb-item:hover{background:rgba(255,255,255,.06);color:#fff;}
        .sb-item.active{background:rgba(212,162,76,.16);color:var(--gold-500);}
        .sb-item.active::before{content:"";position:absolute;left:-12px;top:8px;bottom:8px;width:3px;border-radius:3px;background:var(--gold-500);}
        .sb-item svg{width:19px;height:19px;flex:none;}
        .sb-item .badge{margin-left:auto;background:var(--terracotta-600);color:#fff;font-size:11px;font-weight:700;padding:1px 7px;border-radius:20px;}
        .sidebar.collapsed .sb-item span:not(.badge){display:none;}
        .sidebar.collapsed .sb-item .badge{display:none;}
        .sidebar.collapsed .sb-item{justify-content:center;}
        .sb-drop{position:relative;}
        .sb-drop-toggle{width:100%;cursor:pointer;background:none;border:none;font-family:inherit;}
        .sb-drop-toggle .chev{margin-left:auto;opacity:.55;transition:transform .25s;width:15px;height:15px;flex:none;}
        .sb-drop.open .sb-drop-toggle .chev{transform:rotate(180deg);}
        .sb-drop-menu{display:none;margin:2px 0 4px;padding-left:12px;}
        .sb-drop.open .sb-drop-menu{display:block;}
        .sidebar.collapsed .sb-drop-menu{display:none;}
        .sb-drop-sub{display:flex;align-items:center;gap:9px;padding:9px 12px;border-radius:8px;margin-bottom:1px;color:rgba(255,255,255,.55);font-size:13px;font-weight:500;}
        .sb-drop-sub:hover{color:#fff;background:rgba(255,255,255,.06);}
        .sb-drop-sub.active{color:var(--gold-500);background:rgba(212,162,76,.12);}
        .sb-drop-sub svg{width:14px;height:14px;flex:none;}
        .sb-footer{padding:14px 20px 20px;border-top:1px solid rgba(255,255,255,.08);}
        .sb-user{display:flex;align-items:center;gap:11px;}
        .sb-avatar{width:36px;height:36px;border-radius:50%;flex:none;background:linear-gradient(155deg,var(--acacia-500),var(--acacia-600));display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:13px;}
        .sb-user-text{overflow:hidden;white-space:nowrap;}
        .sb-user-text strong{display:block;color:#fff;font-size:13.5px;}
        .sb-user-text span{display:block;color:rgba(255,255,255,.5);font-size:11.5px;}
        .sidebar.collapsed .sb-user-text{display:none;}
        .main{margin-left:var(--sidebar-w);transition:margin-left .25s;min-height:100vh;display:flex;flex-direction:column;}
        .sidebar.collapsed ~ .main{margin-left:var(--sidebar-w-collapsed);}
        .topbar{position:sticky;top:0;z-index:100;height:var(--topbar-h);background:rgba(251,247,239,.86);backdrop-filter:blur(10px);border-bottom:1px solid var(--line);display:flex;align-items:center;gap:16px;padding:0 28px;}
        .tb-toggle{width:38px;height:38px;border-radius:10px;border:1.5px solid var(--line);background:var(--white);display:flex;align-items:center;justify-content:center;flex:none;}
        .tb-right{margin-left:auto;display:flex;align-items:center;gap:10px;}
        .tb-live{display:flex;align-items:center;gap:7px;background:var(--acacia-100);color:var(--acacia-600);padding:7px 13px;border-radius:20px;font-size:12.5px;font-weight:700;}
        .tb-live::before{content:"";width:7px;height:7px;border-radius:50%;background:var(--acacia-600);}
        .view-wrap{padding:28px;flex:1;}
        .mobile-overlay{position:fixed;inset:0;background:rgba(36,20,8,.45);z-index:190;display:none;}
        .mobile-overlay.show{display:block;}
        @media(max-width:1100px){.topbar{padding:0 20px;}}
        @media(max-width:900px){.sidebar{transform:translateX(-100%);width:var(--sidebar-w);}.sidebar.mobile-open{transform:translateX(0);}.main{margin-left:0 !important;}.topbar{flex-wrap:wrap;height:auto;min-height:var(--topbar-h);padding:10px 16px;gap:10px;}}
        @media(max-width:640px){.view-wrap{padding:16px;}.topbar{padding:8px 14px;gap:8px;}.tb-right{gap:8px;flex-wrap:wrap;justify-content:flex-end;}.tb-profile-text{max-width:110px;overflow:hidden;}}
        @media(max-width:480px){.tb-profile-email{display:none;}.tb-live{display:none;}}
        .btn{padding:11px 18px;border-radius:8px;border:none;font-weight:600;font-size:14px;display:inline-flex;align-items:center;gap:8px;cursor:pointer;}
        .btn-primary{background:var(--terracotta-600);color:#fff;box-shadow:0 6px 16px rgba(194,89,43,.32);}
        .btn-primary:hover{background:var(--terracotta-500);}
        .btn-ghost{background:transparent;border:1.5px solid var(--line);color:var(--coffee-700);}
        .tag{padding:4px 10px;border-radius:20px;font-size:11px;font-weight:700;}
        .tag-green{background:var(--acacia-100);color:var(--acacia-600);}.tag-gold{background:var(--gold-100);color:#8a6418;}
        .tag-red{background:var(--danger-100);color:var(--danger);}
        .panel{background:var(--white);border:1px solid var(--line);border-radius:14px;box-shadow:0 1px 2px rgba(42,27,16,.08);overflow:hidden;}
    </style>
    @include('layouts.partials.theme-styles')
</head>
<body>
    @include('layouts.partials.loader')
    <div id="toastHost"></div>
    <div class="mobile-overlay" id="mobileOverlay" onclick="closeMobile()"></div>
    <aside class="sidebar" id="sidebar">
        <div class="sb-brand">
            @php $sbLogo = \App\Models\Setting::getValue('university_logo'); @endphp
            <div class="sb-mark" style="overflow:hidden;@if($sbLogo)background:#fff;padding:4px;@endif">@if($sbLogo)<img src="{{ asset($sbLogo) }}" style="width:100%;height:100%;object-fit:contain;" alt="Logo">@else {{ substr(\App\Models\Setting::getValue('university_acronym','UDOM'),0,1) }} @endif</div>
            <div class="sb-brand-text"><strong>{{ \App\Models\Setting::getValue('university_name','University OAS') }}</strong><span>Admin Panel</span></div>
        </div>
        <nav class="sb-nav">
            <div class="sb-label">Overview</div>
            <a href="{{ route('admin.dashboard') }}" class="sb-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="9" rx="1.5"/><rect x="14" y="3" width="7" height="5" rx="1.5"/><rect x="14" y="12" width="7" height="9" rx="1.5"/><rect x="3" y="16" width="7" height="5" rx="1.5"/></svg>
                <span>Dashboard</span>
            </a>

            <div class="sb-label">Admission Management</div>
            <a href="{{ route('admin.academic-years.index') }}" class="sb-item {{ request()->routeIs('admin.academic-years*') ? 'active' : '' }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg><span>Academic Years</span></a>
            <a href="{{ route('admin.rounds.index') }}" class="sb-item {{ request()->routeIs('admin.rounds*') ? 'active' : '' }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg><span>Application Rounds</span></a>
            <a href="{{ route('admin.windows.index') }}" class="sb-item {{ request()->routeIs('admin.windows*') ? 'active' : '' }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/></svg><span>Admission Windows</span></a>
            <a href="{{ route('admin.workflow.index') }}" class="sb-item {{ request()->routeIs('admin.workflow*') ? 'active' : '' }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="4" y1="6" x2="20" y2="6"/><line x1="4" y1="12" x2="20" y2="12"/><line x1="4" y1="18" x2="20" y2="18"/></svg><span>Workflow Steps</span></a>
            <a href="{{ route('admin.applicants.index') }}" class="sb-item {{ request()->routeIs('admin.applicants*') ? 'active' : '' }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg><span>Applicants</span></a>
            <a href="{{ route('admin.applications.index') }}" class="sb-item {{ request()->routeIs('admin.applications*') ? 'active' : '' }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg><span>Applications</span></a>

            <div class="sb-label">Academic Management</div>
            <a href="{{ route('admin.campuses.index') }}" class="sb-item {{ request()->routeIs('admin.campuses*') ? 'active' : '' }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg><span>Campuses</span></a>
            <a href="{{ route('admin.faculties.index') }}" class="sb-item {{ request()->routeIs('admin.faculties*') ? 'active' : '' }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg><span>Faculties</span></a>
            <a href="{{ route('admin.departments.index') }}" class="sb-item {{ request()->routeIs('admin.departments*') ? 'active' : '' }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/></svg><span>Departments</span></a>
            <a href="{{ route('admin.programmes.index') }}" class="sb-item {{ request()->routeIs('admin.programmes*') ? 'active' : '' }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg><span>Programmes</span></a>

            <div class="sb-label">Verification & Finance</div>
            <a href="{{ route('admin.documents.index') }}" class="sb-item {{ request()->routeIs('admin.documents*') ? 'active' : '' }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/></svg><span>Documents</span></a>
            <a href="{{ route('admin.payments.index') }}" class="sb-item {{ request()->routeIs('admin.payments*') ? 'active' : '' }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg><span>Payments</span></a>

            <div class="sb-label">Selection & Admission</div>
            <a href="{{ route('admin.selection.batches') }}" class="sb-item {{ request()->routeIs('admin.selection*') ? 'active' : '' }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><span>Selection</span></a>
            @if(auth()->user()->isAdministrator())
            <a href="{{ route('admin.sms-logs.index') }}" class="sb-item {{ request()->routeIs('admin.sms-logs*') ? 'active' : '' }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg><span>SMS Logs</span></a>
            @endif

            <div class="sb-label">Reports & Admin</div>
            <div class="sb-drop {{ request()->routeIs('admin.reports*') ? 'open' : '' }}">
                <button type="button" class="sb-item sb-drop-toggle" onclick="this.closest('.sb-drop').classList.toggle('open')">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
                    <span>Reports</span>
                    <svg class="chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                </button>
                <div class="sb-drop-menu">
                    <a href="{{ route('admin.reports.index') }}" class="sb-drop-sub {{ request()->routeIs('admin.reports.index') ? 'active' : '' }}">Overview</a>
                    <a href="{{ route('admin.reports.applications') }}" class="sb-drop-sub">Applications</a>
                    <a href="{{ route('admin.reports.payments') }}" class="sb-drop-sub">Payments</a>
                    <a href="{{ route('admin.reports.programmes') }}" class="sb-drop-sub">Programmes</a>
                    <a href="{{ route('admin.reports.statistics') }}" class="sb-drop-sub">Statistics</a>
                </div>
            </div>
            <a href="{{ route('admin.users.index') }}" class="sb-item {{ request()->routeIs('admin.users*') ? 'active' : '' }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg><span>Users</span></a>
            <a href="{{ route('admin.settings.index') }}" class="sb-item {{ request()->routeIs('admin.settings*') ? 'active' : '' }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09a1.65 1.65 0 0 0-1-1.51 1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1-1.51V21a2 2 0 0 1-4 0v-.09 1.65 1.65 0 0 0-1-1.51"/></svg><span>Settings</span></a>
            <a href="{{ route('admin.audit-logs.index') }}" class="sb-item {{ request()->routeIs('admin.audit-logs*') ? 'active' : '' }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/></svg><span>Audit Logs</span></a>

            <div class="sb-label">Session</div>
            <form method="POST" action="{{ route('logout') }}">@csrf<button class="sb-item" style="width:100%;background:none;border:none;font-family:inherit;cursor:pointer;text-align:left;"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg><span>Logout</span></button></form>
        </nav>
        <footer class="sb-footer">
            <div class="sb-user">
                <div class="sb-avatar">{{ strtoupper(substr(auth()->user()->name,0,1)) }}</div>
                <div class="sb-user-text"><strong>{{ auth()->user()->name }}</strong></div>
            </div>
        </footer>
    </aside>

    <div class="main" id="mainArea">
        <header class="topbar">
            <button class="tb-toggle" onclick="toggleSidebar()"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="4" y1="7" x2="20" y2="7"/><line x1="4" y1="12" x2="20" y2="12"/><line x1="4" y1="17" x2="20" y2="17"/></svg></button>
            <div style="font-weight:700;color:var(--coffee-900);white-space:nowrap;">Admin Panel</div>
            <div class="tb-right">
                <div class="tb-live"><span>Live</span></div>
                <a href="{{ route('home') }}" style="width:38px;height:38px;border-radius:10px;border:1.5px solid var(--line);background:#fff;display:flex;align-items:center;justify-content:center;flex:none;" title="Public site">↗</a>
                <div class="tb-profile" style="display:flex;align-items:center;gap:10px;margin-left:8px;padding-left:12px;border-left:1px solid var(--line);min-width:0;">
                    <div class="sb-avatar" style="width:36px;height:36px;border-radius:50%;flex:none;background:var(--sand-100);border:1.5px solid var(--line);display:flex;align-items:center;justify-content:center;color:var(--coffee-700);">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    </div>
                    <div class="tb-profile-text" style="line-height:1.2;min-width:0;overflow:hidden;">
                        <div style="font-weight:700;font-size:13px;color:var(--coffee-900);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ auth()->user()->name }}</div>
                        <div style="font-size:11px;font-weight:600;color:var(--terracotta-600);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ auth()->user()->roleLabel() }}</div>
                    </div>
                </div>
            </div>
        </header>
        <div class="view-wrap">
            @if(session('success'))<div style="background:var(--acacia-100);border:1px solid #c8d7a8;color:var(--acacia-600);padding:12px 16px;border-radius:10px;font-size:13px;font-weight:600;margin-bottom:16px;">{{ session('success') }}</div>@endif
            @if(session('error'))<div style="background:var(--danger-100);border:1px solid #e8b4b0;color:var(--danger);padding:12px 16px;border-radius:10px;font-size:13px;font-weight:600;margin-bottom:16px;">{{ session('error') }}</div>@endif
            @if(isset($errors) && $errors->any())<div style="background:var(--danger-100);border:1px solid #e8b4b0;color:var(--danger);padding:12px 16px;border-radius:10px;font-size:13px;margin-bottom:16px;"><ul style="margin:0 0 0 16px;">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
            @yield('content')
        </div>
    </div>
    @include('layouts.partials.confirm-modal')
    <form id="idleLogoutForm" method="POST" action="{{ route('logout') }}" style="display:none">@csrf</form>
    @include('layouts.partials.theme-scripts')
    @if(session('success'))<script>document.addEventListener('DOMContentLoaded',()=>toast(@json(session('success')),'success'))</script>@endif
    @if(session('error'))<script>document.addEventListener('DOMContentLoaded',()=>toast(@json(session('error')),'error'))</script>@endif
</body>
</html>