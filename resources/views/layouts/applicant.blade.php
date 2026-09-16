<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title','Applicant') — {{ \App\Models\Setting::getValue('university_name','University OAS') }}</title>
    @include('layouts.partials.favicon')
    <link href="https://fonts.googleapis.com/css2?family=Raleway:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root{--sand-50:#F5F8FC;--sand-100:#ECF1F6;--sand-200:#DCE5EE;--coffee-900:#052B3D;--coffee-800:#07364F;--coffee-700:#0A4260;--coffee-500:#285B78;--coffee-300:#5E88A3;--terracotta-600:#0066CC;--terracotta-100:#E4F0FA;--acacia-600:#2E7D6B;--acacia-100:#E1EFEA;--gold-500:#E8B82F;--gold-100:#F9EFD2;--ink:#052C3F;--ink-soft:#40637A;--line:#C9D6E2;--white:#fff;--danger:#B33A3A;--danger-100:#F6DCDA;--sidebar-w:264px;--sidebar-w-collapsed:76px;--topbar-h:72px;--radius-md:14px;--shadow-sm:0 1px 2px rgba(42,27,16,.08);}
        *{box-sizing:border-box;}html,body{height:100%;}body{margin:0;font-family:'Raleway',sans-serif;background:var(--sand-50);color:var(--ink);-webkit-font-smoothing:antialiased;overflow-x:hidden;}
        h1,h2,h3{margin:0;color:var(--coffee-900);}a{color:inherit;text-decoration:none;}button{font-family:inherit;cursor:pointer;}input,select,textarea{font-family:inherit;}
        ::-webkit-scrollbar{width:9px;height:9px;}::-webkit-scrollbar-thumb{background:var(--coffee-300);border-radius:10px;}
        .sidebar{position:fixed;top:0;left:0;bottom:0;width:var(--sidebar-w);z-index:200;background:var(--coffee-900);background-image:radial-gradient(circle at 0% 0%, rgba(212,162,76,.10), transparent 55%);display:flex;flex-direction:column;transition:width .25s, transform .25s;border-right:1px solid rgba(255,255,255,.06);}
        .sidebar.collapsed{width:var(--sidebar-w-collapsed);}
        .sb-brand{display:flex;align-items:center;gap:12px;padding:22px 20px;border-bottom:1px solid rgba(255,255,255,.08);min-height:var(--topbar-h);}
        .sb-mark{width:38px;height:38px;border-radius:10px;flex:none;background:linear-gradient(155deg,var(--terracotta-600),var(--gold-500));display:flex;align-items:center;justify-content:center;color:#fff;font-weight:800;}
        .sb-brand-text{overflow:hidden;white-space:nowrap;}
        .sb-brand-text strong{display:block;color:#fff;font-size:15.5px;line-height:1.1;}
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
        .sb-item .dot{width:22px;height:22px;border-radius:50%;border:1.5px solid rgba(255,255,255,.25);display:flex;align-items:center;justify-content:center;font-size:11px;flex:none;}
        .sb-item.active .dot{border-color:var(--gold-500);}
        .sb-item.done .dot{background:var(--acacia-600);border-color:var(--acacia-600);color:#fff;}
        .sidebar.collapsed .sb-item span:not(.dot){display:none;}
        .sidebar.collapsed .sb-item{justify-content:center;}
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
        @media(max-width:768px){.tb-ay{font-size:13px;}.tb-progress{font-size:11px;padding:5px 10px;}.tb-progress div[style*="width:60px"]{display:none;}}
@media(max-width:640px){.view-wrap{padding:16px;}.topbar{padding:8px 14px;gap:8px;flex-wrap:nowrap;}.tb-right{gap:8px;flex-wrap:nowrap;}.tb-profile-text{max-width:96px;overflow:hidden;}.tb-ay{display:none;}.tb-progress{padding:4px 9px;font-size:10.5px;gap:6px;}.tb-profile{padding-left:8px;margin-left:4px;gap:8px;}.tb-profile .sb-avatar{width:32px;height:32px;}}
@media(max-width:480px){.tb-profile-email{display:none;}.tb-progress .p-label{display:none;}}
        .btn{padding:12px 20px;border-radius:8px;border:none;font-weight:600;font-size:14px;display:inline-flex;align-items:center;gap:8px;cursor:pointer;}
        .btn-primary{background:var(--terracotta-600);color:#fff;box-shadow:0 6px 16px rgba(194,89,43,.32);}
        .btn-primary:hover{background:#285B78;}.btn-ghost{background:transparent;border:1.5px solid var(--line);color:var(--coffee-700);}
        .tag{padding:4px 10px;border-radius:20px;font-size:11px;font-weight:700;}
        .tag-green{background:var(--acacia-100);color:var(--acacia-600);}.tag-gold{background:var(--gold-100);color:#8a6418;}
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
            @php $sbLogo2 = \App\Models\Setting::getValue('university_logo'); @endphp
            <div class="sb-mark" style="overflow:hidden;@if($sbLogo2)background:#fff;padding:4px;@endif">@if($sbLogo2)<img src="{{ asset($sbLogo2) }}" style="width:100%;height:100%;object-fit:contain;" alt="Logo">@else {{ substr(\App\Models\Setting::getValue('university_acronym','UDOM'),0,1) }} @endif</div>
            <div class="sb-brand-text"><strong>{{ \App\Models\Setting::getValue('university_name','University OAS') }}</strong><span>Applicant Portal</span></div>
        </div>
        <nav class="sb-nav">
            <div class="sb-label">Dashboard</div>
            <a href="{{ route('applicant.dashboard') }}" class="sb-item {{ request()->routeIs('applicant.dashboard') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="9" rx="1.5"/><rect x="14" y="3" width="7" height="5" rx="1.5"/><rect x="14" y="12" width="7" height="9" rx="1.5"/><rect x="3" y="16" width="7" height="5" rx="1.5"/></svg>
                <span>Dashboard</span>
            </a>

            <div class="sb-label">My Application</div>
            @php $firstApp = auth()->user()->applicant?->applications()->latest()->first(); @endphp
            @if($firstApp)
                @php $steps = $firstApp->admissionWindow->admissionLevel->workflowSteps()->where('is_active',true)->orderBy('step_number')->get(); @endphp
                @php $locked = $blocked = false; @endphp
                @foreach($steps as $st)
                    @php
                        $done = $firstApp->stepCompletions()->where('workflow_step_id',$st->id)->whereNotNull('completed_at')->exists();
                        $isActive = request()->is('applicant/applications/*/step/'.$st->route);
                        if (! $done && ! $isActive && ! $blocked) $done = false;
                        $locked = $blocked && ! $done && ! $isActive;
                        if (! $done) $blocked = true;
                    @endphp
                    @if($locked)
                        <div class="sb-item locked" style="cursor:not-allowed;opacity:.5;pointer-events:none;">
                            <span class="dot">{{ $done ? '✓' : '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:11px;height:11px;"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>' }}</span>
                            <span>{{ $st->step_name }}</span>
                        </div>
                    @else
                        <a href="{{ route('applicant.application.step', [encId($firstApp->id), $st->route]) }}" class="sb-item {{ $isActive ? 'active' : '' }} {{ $done ? 'done' : '' }}">
                            <span class="dot">{{ $done ? '✓' : $st->step_number }}</span>
                            <span>{{ $st->step_name }}</span>
                        </a>
                    @endif
                @endforeach
            @else
                <div style="padding:8px 12px;color:rgba(255,255,255,.4);font-size:12px;">Start an application from dashboard</div>
            @endif

            <div class="sb-label">Application</div>
            <a href="{{ route('applicant.history') }}" class="sb-item {{ request()->routeIs('applicant.history') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                <span>Programmes Applied</span>
            </a>
            @if($firstApp)
                <a href="{{ route('applicant.application.status', encId($firstApp->id)) }}" class="sb-item {{ request()->routeIs('applicant.application.status') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    <span>Application Status</span>
                </a>
                <a href="{{ route('applicant.application.summary', encId($firstApp->id)) }}" class="sb-item {{ request()->routeIs('applicant.application.summary') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                    <span>Summary</span>
                </a>
            @endif
            <a href="{{ route('applicant.results') }}" class="sb-item {{ request()->routeIs('applicant.results*') || request()->routeIs('applicant.result*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                <span>Admission Result</span>
            </a>

            <div class="sb-label">Account</div>
            <a href="{{ route('applicant.profile') }}" class="sb-item {{ request()->routeIs('applicant.profile') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                <span>Profile</span>
            </a>
            <a href="{{ route('applicant.calendar') }}" class="sb-item {{ request()->routeIs('applicant.calendar') ? 'active' : '' }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg><span>Calendar</span></a>
            <form method="POST" action="{{ route('logout') }}">@csrf<button class="sb-item" style="width:100%;background:none;border:none;font-family:inherit;cursor:pointer;"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg><span>Logout</span></button></form>
        </nav>
    </aside>

    <div class="main" id="mainArea">
        <header class="topbar">
            <button class="tb-toggle" onclick="toggleSidebar()" aria-label="Toggle">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="4" y1="7" x2="20" y2="7"/><line x1="4" y1="12" x2="20" y2="12"/><line x1="4" y1="17" x2="20" y2="17"/></svg>
            </button>
            <div class="tb-ay" style="font-weight:700;color:var(--coffee-900);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">Academic Year: {{ \App\Models\AcademicYear::current()?->name ?? '—' }}</div>
            <div class="tb-right">
                @php $firstApp = $firstApp ?? auth()->user()->applicant?->applications()->latest()->first(); @endphp
                @if($firstApp)
                    @php $steps = $steps ?? $firstApp->admissionWindow->admissionLevel->workflowSteps()->where('is_active',true)->orderBy('step_number')->get(); $done = $firstApp->stepCompletions()->whereNotNull('completed_at')->count(); $total = $steps->count(); $pct = $total ? round($done/$total*100) : 0; @endphp
                    <div class="tb-progress" style="display:flex;align-items:center;gap:8px;background:var(--terracotta-600);color:#fff;padding:7px 14px;border-radius:20px;font-size:12px;font-weight:800;white-space:nowrap;box-shadow:0 4px 12px rgba(194,89,43,.25);">
                        <span class="p-label">Progress —</span> <span class="p-val">{{ $done }}/{{ $total }} ({{ $pct }}%)</span>
                        <div style="width:60px;height:6px;background:rgba(255,255,255,.28);border-radius:10px;overflow:hidden;flex:none;"><div style="height:100%;background:var(--gold-500);width:{{ $pct }}%"></div></div>
                    </div>
                @endif
                <div class="tb-profile" style="display:flex;align-items:center;gap:10px;margin-left:8px;padding-left:12px;border-left:1px solid var(--line);min-width:0;">
                    <div class="sb-avatar" style="width:36px;height:36px;border-radius:50%;flex:none;background:var(--sand-100);border:1.5px solid var(--line);display:flex;align-items:center;justify-content:center;color:var(--coffee-700);">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    </div>
                    <div class="tb-profile-text" style="line-height:1.2;min-width:0;overflow:hidden;">
                        <div style="font-weight:700;font-size:13px;color:var(--coffee-900);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ auth()->user()->name }}</div>
                        <div class="tb-profile-email" style="font-size:11px;color:var(--ink-soft);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ auth()->user()->email }}</div>
                    </div>
                </div>
            </div>
        </header>
        <div class="view-wrap">
            @if(session('success'))<div style="background:var(--acacia-100);border:1px solid #c8d7a8;color:var(--acacia-600);padding:12px 16px;border-radius:10px;font-size:13px;font-weight:600;margin-bottom:16px;">{{ session('success') }}</div>@endif
            @if(session('error'))<div style="background:var(--danger-100);border:1px solid #e8b4b0;color:var(--danger);padding:12px 16px;border-radius:10px;font-size:13px;font-weight:600;margin-bottom:16px;">{{ session('error') }}</div>@endif
            @if(session('info'))<div style="background:#e8f0fe;border:1px solid #b6c8f0;color:#1a4da1;padding:12px 16px;border-radius:10px;font-size:13px;font-weight:600;margin-bottom:16px;">{{ session('info') }}</div>@endif
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