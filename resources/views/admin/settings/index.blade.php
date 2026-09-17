@extends('layouts.admin')
@section('title','Settings')
@section('content')
<div class="page-head">
    <div><h1>Settings</h1><p class="page-sub">Configure university identity, SMS provider, and system settings.</p></div>
</div>

@php
    $logoPath = \App\Models\Setting::getValue('university_logo');
    $uniName = \App\Models\Setting::getValue('university_name','Moshi Co-operative University');
    $uniAcronym = \App\Models\Setting::getValue('university_acronym','MoCU');

    $identityKeys = ['university_name','university_acronym','university_logo'];
    $smsKeys = ['sms_enabled','sms_provider','sms_sender_id','sms_api_key','sms_test_mode'];
    $integrationGroups = ['gepg','necta','nactvet','tcu'];
    $otherSettings = $settings->filter(fn($s)=> !in_array($s->key, array_merge($identityKeys,$smsKeys)) && !in_array($s->group, $integrationGroups));

    $sel = fn($k)=>$settings->search(fn($x)=>$x->key===$k);
    $ival = function($k,$def='') use($settings,$sel){ $i=$sel($k); return $i!==false ? $settings[$i]->value : $def; };
    $idx = fn($k)=>($sel($k)!==false ? $sel($k) : $settings->count());

    $gepgFields = [
        ['key'=>'gepg_enabled','label'=>'Enable GePG','type'=>'select','options'=>['1'=>'Enabled — generate control numbers via GePG','0'=>'Disabled — manual control number entry'],'hint'=>'Master switch for the GePG billing gateway.'],
        ['key'=>'gepg_test_mode','label'=>'Test Mode','type'=>'select','options'=>['1'=>'Sandbox / test environment','0'=>'Production'],'hint'=>'Always use Sandbox until GePG confirms your credentials are live.'],
        ['key'=>'gepg_biller_code','label'=>'Biller Code *','type'=>'text','placeholder'=>'e.g. MoCU','hint'=>'GePG Biller Code issued to the university (BIL PAY service).'],
        ['key'=>'gepg_sp_system_code','label'=>'SP System Code *','type'=>'text','placeholder'=>'SP123','hint'=>'Service provider system code used when requesting a bill.'],
        ['key'=>'gepg_psp_username','label'=>'PSP Username *','type'=>'text','placeholder'=>'','hint'=>'Web-service username issued by GePG to the PSP.'],
        ['key'=>'gepg_psp_password','label'=>'PSP Password *','type'=>'password','placeholder'=>'••••••••','hint'=>'Web-service password issued by GePG to the PSP.'],
        ['key'=>'gepg_test_url','label'=>'Sandbox Endpoint','type'=>'text','placeholder'=>'https://gfs.tegov.go.tz/gfs_billrequest','hint'=>'Bill-request SOAP endpoint for GePG sandbox.'],
        ['key'=>'gepg_production_url','label'=>'Production Endpoint','type'=>'text','placeholder'=>'https://gfs.gepg.go.tz/gfs_billrequest','hint'=>'Bill-request SOAP endpoint for GePG production.'],
        ['key'=>'gepg_currency','label'=>'Currency','type'=>'text','placeholder'=>'TZS','full'=>true,'hint'=>'ISO currency code for generated control numbers (TZS).'],
        ['key'=>'gepg_bill_payment_window','label'=>'Payment Window (hours)','type'=>'text','placeholder'=>'240','hint'=>'How long a generated control number stays valid before expiry.'],
        ['key'=>'gepg_psp_certificate','label'=>'PSP Certificate / Public Key','type'=>'textarea','full'=>true,'hint'=>'Paste the GePG PSP certificate or public key (PEM) used to encrypt/sign bill request payloads.'],
    ];

    $nectaFields = [
        ['key'=>'necta_enabled','label'=>'Enable Verification','type'=>'select','options'=>['1'=>'Enabled — verify results via NECTA API','0'=>'Disabled'],'hint'=>'Master switch for NECTA O/A-Level result checks.'],
        ['key'=>'necta_test_mode','label'=>'Test Mode','type'=>'select','options'=>['1'=>'Test / staging API','0'=>'Production API'],'hint'=>'Keep enabled until the integration is signed off.'],
        ['key'=>'necta_base_url','label'=>'Base URL *','type'=>'text','placeholder'=>'https://ws.necta.go.tz','full'=>true,'hint'=>'NECTA web-service base endpoint.'],
        ['key'=>'necta_api_token','label'=>'API Token *','type'=>'password','placeholder'=>'token','full'=>true,'hint'=>'Token / credentials for the NECTA verification endpoint.'],
    ];

    $nactvetFields = [
        ['key'=>'nactvet_enabled','label'=>'Enable Verification','type'=>'select','options'=>['1'=>'Enabled — verify results via NACTVET API','0'=>'Disabled'],'hint'=>'Master switch for NACTVET (VETA) result checks.'],
        ['key'=>'nactvet_test_mode','label'=>'Test Mode','type'=>'select','options'=>['1'=>'Test / staging API','0'=>'Production API'],'hint'=>'Keep enabled until the integration is signed off.'],
        ['key'=>'nactvet_base_url','label'=>'Base URL *','type'=>'text','placeholder'=>'https://ws.nactvet.go.tz','full'=>true,'hint'=>'NACTVET web-service base endpoint.'],
        ['key'=>'nactvet_api_token','label'=>'API Token *','type'=>'password','placeholder'=>'token','full'=>true,'hint'=>'Token / credentials for the NACTVET verification endpoint.'],
    ];

    $tcuFields = [
        ['key'=>'tcu_enabled','label'=>'Enable Integration','type'=>'select','options'=>['1'=>'Enabled — query TCU records','0'=>'Disabled'],'hint'=>'Master switch for the TCU integration.'],
        ['key'=>'tcu_test_mode','label'=>'Test Mode','type'=>'select','options'=>['1'=>'Test / staging API','0'=>'Production API'],'hint'=>'Keep enabled until the integration is signed off.'],
        ['key'=>'tcu_system_code','label'=>'System Code *','type'=>'text','placeholder'=>'','hint'=>'TCU system code identifying this university.'],
        ['key'=>'tcu_base_url','label'=>'Base URL *','type'=>'text','placeholder'=>'https://www.tcu.go.tz','full'=>true,'hint'=>'TCU web-service base endpoint.'],
        ['key'=>'tcu_api_token','label'=>'API Token *','type'=>'password','placeholder'=>'token','full'=>true,'hint'=>'Token / credentials for the TCU endpoint.'],
    ];

    $integrationTabs = [
        ['id'=>'tab-gepg','tab'=>'GePG','title'=>'GePG Payment Gateway','icon'=>'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>','desc'=>'Government ePayment Gateway — generate and reconcile payment control numbers in real time.','fields'=>$gepgFields],
        ['id'=>'tab-necta','tab'=>'NECTA','title'=>'NECTA Verification','icon'=>'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>','desc'=>'National Examinations Council of Tanzania — verify O-Level and A-Level results.','fields'=>$nectaFields],
        ['id'=>'tab-nactvet','tab'=>'NACTVET','title'=>'NACTVET Verification','icon'=>'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>','desc'=>'National Council for Technical and Vocational Education — verify diploma and certificate results.','fields'=>$nactvetFields],
        ['id'=>'tab-tcu','tab'=>'TCU','title'=>'TCU Integration','icon'=>'<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M2 12h20"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>','desc'=>'Tanzania Commission for Universities — verify higher-education records and credentials.','fields'=>$tcuFields],
    ];
@endphp

{{-- Tabbed layout --}}
    <div class="settings-layout" style="display:grid;grid-template-columns:200px 1fr;gap:0;align-items:start;border:1.5px solid var(--line);border-radius:14px;background:#fff;overflow:hidden;">

        {{-- Left tabs --}}
        <div class="settings-tabs-col" style="background:var(--sand-50);border-right:1px solid var(--line);padding:14px 0;">
            <div style="padding:0 16px 12px;font-size:11px;font-weight:800;letter-spacing:.08em;text-transform:uppercase;color:var(--ink-soft);">Categories</div>
            <button type="button" class="settings-tab active" onclick="switchTab(this,'tab-identity')">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M22 10v6"/><path d="M2 17l10 5 10-5"/></svg>
                University Identity
            </button>
            <button type="button" class="settings-tab" onclick="switchTab(this,'tab-sms')">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                SMS Notifications
            </button>
            @foreach($integrationTabs as $it)
            <button type="button" class="settings-tab" onclick="switchTab(this,'{{ $it['id'] }}')">{!! $it['icon'] !!} {{ $it['tab'] }}</button>
            @endforeach
            <button type="button" class="settings-tab" onclick="switchTab(this,'tab-general')">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33"/></svg>
                General Settings
            </button>
        </div>

        {{-- Right content --}}
        <div style="padding:0;min-height:400px;">

            {{-- Tab: Identity --}}
            <div id="tab-identity" class="settings-tab-panel" style="display:block;">
                <div style="padding:20px 24px 0;">
                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M22 10v6"/><path d="M2 17l10 5 10-5"/></svg>
                        <h2 style="margin:0;font-size:17px;font-weight:800;color:var(--coffee-900);">University Identity</h2>
                    </div>
                    <p style="font-size:13px;color:var(--ink-soft);margin:0 0 18px 26px;">Controls what appears in the Admin Panel header, sidebar brand, loading screen and admission documents.</p>
                </div>
                <div style="padding:0 24px 24px;">
                    <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data" onsubmit="event.preventDefault(); const _f=this; confirmModal('Save identity settings','Save University Identity changes?',()=>_f.submit())">
                    @csrf
                    {{-- Preview --}}
                    <div style="display:flex;gap:16px;align-items:center;padding:16px;border:1px solid var(--line);border-radius:12px;background:var(--sand-50);margin-bottom:18px;">
                        <div style="width:72px;height:72px;border-radius:14px;border:1.5px solid var(--line);background:#fff;display:flex;align-items:center;justify-content:center;overflow:hidden;flex:none;">
                            @if($logoPath)<img src="{{ asset($logoPath) }}" style="width:100%;height:100%;object-fit:cover;" alt="Logo">@else<div style="width:100%;height:100%;background:linear-gradient(155deg,var(--terracotta-600),var(--gold-500));display:flex;align-items:center;justify-content:center;color:#fff;font-weight:800;font-size:22px;">{{ substr($uniAcronym,0,1) }}</div>@endif
                        </div>
                        <div style="flex:1;min-width:0;">
                            <div style="font-weight:800;font-size:15px;color:var(--coffee-900);">{{ $uniName }} <span style="color:var(--terracotta-600);">({{ $uniAcronym }})</span></div>
                            <div style="font-size:12px;color:var(--ink-soft);margin-top:2px;">Admin Panel header — sidebar brand, top bar, loading screen.</div>
                            <div style="margin-top:6px;display:flex;gap:6px;flex-wrap:wrap;">
                                <span class="cell-mono" style="font-size:11px;">{{ $logoPath ?: 'No logo — using '.$uniAcronym[0].' mark' }}</span>
                                @if($logoPath)<span class="tag tag-green">Logo Active</span>@endif
                            </div>
                        </div>
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;" class="settings-2col">
                        @php
                            $uniNameIdx = $settings->search(fn($s)=>$s->key==='university_name');
                            $uniAcrIdx  = $settings->search(fn($s)=>$s->key==='university_acronym');
                        @endphp
                        <div class="field">
                            <label class="field-label">University Name *</label>
                            <input name="settings[{{ $uniNameIdx !== false ? $uniNameIdx : 0 }}][key]" type="hidden" value="university_name">
                            <input name="settings[{{ $uniNameIdx !== false ? $uniNameIdx : 0 }}][value]" value="{{ $uniName }}" style="width:100%;padding:10px 12px;border:1.5px solid var(--terracotta-600);border-radius:10px;font-size:13px;background:#fff;font-weight:600;">
                            <span class="field-hint">e.g. Moshi Co-operative University</span>
                        </div>
                        <div class="field">
                            <label class="field-label">Acronym *</label>
                            <input name="settings[{{ $uniAcrIdx !== false ? $uniAcrIdx : 1 }}][key]" type="hidden" value="university_acronym">
                            <input name="settings[{{ $uniAcrIdx !== false ? $uniAcrIdx : 1 }}][value]" value="{{ $uniAcronym }}" style="width:100%;padding:10px 12px;border:1.5px solid var(--terracotta-600);border-radius:10px;font-size:13px;background:#fff;font-weight:700;letter-spacing:.06em;text-transform:uppercase;">
                            <span class="field-hint">e.g. MoCU — for logo mark & short display</span>
                        </div>
                    </div>
                    <div class="field" style="margin-top:14px;">
                        <label class="field-label">University Logo — Upload New (PNG/JPG/SVG/WEBP, max 2MB)</label>
                        <input type="file" name="university_logo" accept="image/*" style="width:100%;padding:10px 12px;border:1.5px solid var(--line);border-radius:10px;font-size:13px;background:#fff;">
                        <span class="field-hint">Leave empty to keep current. Replaces the mark in the Admin Panel, sidebar and documents.</span>
                    </div>
                    @php $contactCityIdx = $settings->search(fn($s)=>$s->key==='contact_city'); $contactCityVal = $contactCityIdx !== false ? $settings[$contactCityIdx]->value : 'Dodoma, Tanzania'; @endphp
                    <div class="field" style="margin-top:14px;">
                        <label class="field-label">Top Header Location</label>
                        <input name="settings[{{ $contactCityIdx !== false ? $contactCityIdx : $settings->count() }}][key]" type="hidden" value="contact_city">
                        <input name="settings[{{ $contactCityIdx !== false ? $contactCityIdx : $settings->count() }}][value]" value="{{ $contactCityVal }}" style="width:100%;padding:10px 12px;border:1.5px solid var(--line);border-radius:10px;font-size:13px;background:#fff;" placeholder="Dodoma, Tanzania">
                        <span class="field-hint">Shown in the top bar next to email/phone (e.g. Dodoma, Tanzania). Stored in database — edit and save to update the header.</span>
                    </div>
                    <div style="display:flex;justify-content:flex-end;margin-top:18px;">
                        <button class="btn btn-primary" style="min-width:170px;justify-content:center;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex:none;"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                            Save Identity
                        </button>
                    </div>
                    </form>
                </div>
            </div>

            {{-- Tab: SMS --}}
            <div id="tab-sms" class="settings-tab-panel" style="display:none;">
                <div style="padding:20px 24px 0;">
                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                        <h2 style="margin:0;font-size:17px;font-weight:800;color:var(--coffee-900);">SMS Notifications</h2>
                    </div>
                    <p style="font-size:13px;color:var(--ink-soft);margin:0 0 18px 26px;">Powered by <strong>messaging-service.co.tz</strong> API V2. Sends SMS on application submission, selection, admission and confirmation codes.</p>
                </div>
                <div style="padding:0 24px 24px;">
                    <form method="POST" action="{{ route('admin.settings.update') }}" onsubmit="event.preventDefault(); const _f=this; confirmModal('Save SMS settings','Save SMS Notifications changes?',()=>_f.submit())">
                    @csrf
                    {{-- Bearer token callout --}}
                    <div style="padding:14px 16px;border-radius:12px;background:linear-gradient(135deg,#fdf8f1,#f7f0e8);border:1.5px solid var(--gold-500);margin-bottom:18px;">
                        <div style="display:flex;align-items:flex-start;gap:10px;">
                            <div style="width:36px;height:36px;border-radius:10px;background:var(--gold-100);display:flex;align-items:center;justify-content:center;flex:none;">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--gold-500)" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                            </div>
                            <div style="flex:1;">
                                <div style="font-weight:800;font-size:13px;color:var(--coffee-900);">Authorization: Bearer Token</div>
                                <div style="font-size:12.5px;color:var(--ink-soft);margin-top:2px;">Paste your Bearer Token below. Found in your <a href="https://messaging-service.co.tz" target="_blank" style="color:var(--terracotta-600);font-weight:700;text-decoration:underline;">messaging-service.co.tz</a> dashboard → Customer Info (top-right) → Customization → API Keys.</div>
                                <div style="margin-top:6px;font-size:11.5px;color:var(--ink-soft);line-height:1.5;font-family:monospace;padding:6px 10px;border-radius:6px;background:#fff;border:1px solid var(--line);">Authorization: Bearer YOUR_TOKEN_HERE</div>
                            </div>
                        </div>
                    </div>

                    @php
                        $smsEnabledIdx   = $settings->search(fn($s)=>$s->key==='sms_enabled');
                        $smsProviderIdx  = $settings->search(fn($s)=>$s->key==='sms_provider');
                        $smsSenderIdx    = $settings->search(fn($s)=>$s->key==='sms_sender_id');
                        $smsApiIdx       = $settings->search(fn($s)=>$s->key==='sms_api_key');
                        $smsTestIdx      = $settings->search(fn($s)=>$s->key==='sms_test_mode');
                    @endphp

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;" class="settings-2col">
                        {{-- Enabled --}}
                        <div class="field">
                            <label class="field-label">SMS Enabled</label>
                            <input type="hidden" name="settings[{{ $smsEnabledIdx !== false ? $smsEnabledIdx : $settings->count() }}][key]" value="sms_enabled">
                            <select name="settings[{{ $smsEnabledIdx !== false ? $smsEnabledIdx : $settings->count() }}][value]" style="width:100%;padding:10px 12px;border:1.5px solid var(--line);border-radius:10px;font-size:13px;background:#fff;">
                                @php $enVal = $smsEnabledIdx !== false ? $settings[$smsEnabledIdx]->value : '1'; @endphp
                                <option value="1" @selected($enVal==='1'||$enVal===1)>On — Send SMS notifications</option>
                                <option value="0" @selected($enVal==='0'||$enVal===0)>Off — Disable all SMS</option>
                            </select>
                            <span class="field-hint">Master switch. Turn off to stop all outgoing SMS.</span>
                        </div>

                        {{-- Provider --}}
                        <div class="field">
                            <label class="field-label">SMS Provider</label>
                            <input type="hidden" name="settings[{{ $smsProviderIdx !== false ? $smsProviderIdx : $settings->count() }}][key]" value="sms_provider">
                            @php $pvVal = $smsProviderIdx !== false ? $settings[$smsProviderIdx]->value : 'log'; @endphp
                            <select name="settings[{{ $smsProviderIdx !== false ? $smsProviderIdx : $settings->count() }}][value]" style="width:100%;padding:10px 12px;border:1.5px solid var(--line);border-radius:10px;font-size:13px;background:#fff;">
                                <option value="log" @selected($pvVal==='log')>Log only (dev — records without sending)</option>
                                <option value="messaging" @selected($pvVal==='messaging')>messaging-service.co.tz (live delivery)</option>
                            </select>
                            <span class="field-hint">Use <strong>Log</strong> during development, <strong>messaging</strong> for live SMS delivery.</span>
                        </div>

                        {{-- Sender ID --}}
                        <div class="field">
                            <label class="field-label">Sender ID (FROM)</label>
                            <input type="hidden" name="settings[{{ $smsSenderIdx !== false ? $smsSenderIdx : $settings->count() }}][key]" value="sms_sender_id">
                            @php $srVal = $smsSenderIdx !== false ? $settings[$smsSenderIdx]->value : 'TANZANIATIP'; @endphp
                            <input name="settings[{{ $smsSenderIdx !== false ? $smsSenderIdx : $settings->count() }}][value]" value="{{ $srVal }}" style="width:100%;padding:10px 12px;border:1.5px solid var(--line);border-radius:10px;font-size:13px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;background:#fff;" placeholder="TANZANIATIP">
                            <span class="field-hint">Must be a registered Sender ID on your messaging-service account.</span>
                        </div>

                        {{-- Test mode --}}
                        <div class="field">
                            <label class="field-label">Test Mode</label>
                            <input type="hidden" name="settings[{{ $smsTestIdx !== false ? $smsTestIdx : $settings->count() }}][key]" value="sms_test_mode">
                            @php $tVal = $smsTestIdx !== false ? $settings[$smsTestIdx]->value : '1'; @endphp
                            <select name="settings[{{ $smsTestIdx !== false ? $smsTestIdx : $settings->count() }}][value]" style="width:100%;padding:10px 12px;border:1.5px solid var(--line);border-radius:10px;font-size:13px;background:#fff;">
                                <option value="1" @selected($tVal==='1')>Test — free dummy responses, no credits charged</option>
                                <option value="0" @selected($tVal==='0')>Live — real delivery, credits deducted</option>
                            </select>
                            <span class="field-hint">Always use <strong>Test</strong> first to verify your integration before going live.</span>
                        </div>
                    </div>

                    {{-- Bearer token (full width, prominent) --}}
                    @php $apiVal = $smsApiIdx !== false ? $settings[$smsApiIdx]->value : ''; @endphp
                    <div class="field" style="margin-top:14px;">
                        <label class="field-label" style="font-size:13.5px;display:flex;align-items:center;gap:6px;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="var(--terracotta-600)" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                            Authorization Bearer Token *
                        </label>
                        <input type="hidden" name="settings[{{ $smsApiIdx !== false ? $smsApiIdx : $settings->count() }}][key]" value="sms_api_key">
                        <input name="settings[{{ $smsApiIdx !== false ? $smsApiIdx : $settings->count() }}][value]"
                               value="{{ $apiVal }}"
                               type="password"
                               placeholder="e.g. d983d9d1d54176047e68547aba079ba4"
                               onfocus="this.type='text'" onblur="if(!this.value)this.type='password'"
                               style="width:100%;padding:12px 14px;border:2px solid var(--terracotta-600);border-radius:12px;font-size:14px;font-family:monospace;background:#fff;letter-spacing:.04em;">
                        <div style="margin-top:6px;display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
                            <span class="field-hint" style="margin:0;">Your API Bearer Token from messaging-service.co.tz → Customer Info → API Keys</span>
                            @if($apiVal)
                                <span class="tag tag-green" style="font-size:11px;">Token configured</span>
                            @else
                                <span class="tag tag-red" style="font-size:11px;">No token set</span>
                            @endif
                        </div>
                        <span class="field-hint" style="margin-top:4px;">Sent as: <code style="background:var(--sand-100);padding:2px 6px;border-radius:4px;font-size:11.5px;">Authorization: Bearer {{ $apiVal ? substr($apiVal,0,6).'…'.substr($apiVal,-4) : 'YOUR_TOKEN' }}</code></span>
                    </div>

                    {{-- Send test SMS --}}
                    @php
                        $pvNow = \App\Models\Setting::getValue('sms_provider','log');
                        $tNow  = (bool) \App\Models\Setting::getValue('sms_test_mode', true);
                        $eNow  = (bool) \App\Models\Setting::getValue('sms_enabled', true);
                        $kNow  = (string) \App\Models\Setting::getValue('sms_api_key','') !== '';
                    @endphp
                    <div id="smsTestBox" style="margin-top:18px;padding:16px;border:1.5px dashed var(--terracotta-600);border-radius:12px;background:#fff;">
                        <div style="display:flex;align-items:center;gap:8px;">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="var(--terracotta-600)" stroke-width="2"><polyline points="22 2 11 13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                            <div style="font-weight:800;font-size:13.5px;color:var(--coffee-900);">Send a Test SMS</div>
                        </div>
                        <p style="font-size:12.5px;color:var(--ink-soft);margin:8px 0 0;line-height:1.6;">Verify your integration without touching the application flow. Pick a phone number (format <strong>2557xxxxxxxx</strong>) and press send.</p>
                        <div style="display:flex;gap:10px;align-items:flex-end;margin-top:12px;flex-wrap:wrap;">
                            <div class="field" style="margin:0;flex:1;min-width:200px;">
                                <label class="field-label">Send to (phone number)</label>
                                <input id="smsTestTo" type="text" inputmode="tel" required placeholder="255716718040" value="{{ request('to') }}" style="width:100%;padding:10px 12px;border:2px solid var(--terracotta-600);border-radius:10px;font-size:13px;font-weight:700;letter-spacing:.04em;background:#fff;">
                            </div>
                            <button type="button" class="btn btn-primary" id="smsTestBtn" onclick="sendTestSms()"><span id="smsTestBtnLabel">Send Test SMS</span></button>
                        </div>
                        <div id="smsTestResult" style="display:none;margin-top:12px;padding:10px 12px;border-radius:8px;font-size:12.5px;line-height:1.5;"></div>
                        <div style="margin-top:12px;display:flex;gap:8px;flex-wrap:wrap;">
                            <span class="cell-sub" style="font-size:11.5px;align-self:center;">Current: </span>
                            <span class="tag {{ $eNow ? 'tag-green' : 'tag-red' }}" style="font-size:11px;">{{ $eNow ? 'SMS On' : 'SMS Off' }}</span>
                            <span class="tag tag-blue" style="font-size:11px;">Provider: {{ $pvNow }}</span>
                            <span class="tag {{ $tNow ? 'tag-gold' : 'tag-green' }}" style="font-size:11px;">{{ $tNow ? 'TEST endpoint' : 'LIVE endpoint' }}</span>
                            <span class="tag {{ $kNow ? 'tag-green' : 'tag-red' }}" style="font-size:11px;">{{ $kNow ? 'Token set' : 'No token' }}</span>
                            @if(auth()->user()->isAdministrator())<a href="{{ route('admin.sms-logs.index') }}" class="cell-sub" style="font-size:11.5px;align-self:center;color:var(--terracotta-600);font-weight:700;text-decoration:underline;">View SMS Logs →</a>@endif
                        </div>
                    </div>
                    <div style="display:flex;justify-content:flex-end;margin-top:18px;">
                        <button class="btn btn-primary" style="min-width:170px;justify-content:center;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex:none;"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                            Save SMS Settings
                        </button>
                    </div>
                    </form>
                </div>
            </div>

            {{-- Integration tabs: GePG / NECTA / NACTVET / TCU --}}
            @foreach($integrationTabs as $it)
            <div id="{{ $it['id'] }}" class="settings-tab-panel" style="display:none;">
                <div style="padding:20px 24px 0;">
                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">{!! $it['icon'] !!}</svg>
                        <h2 style="margin:0;font-size:17px;font-weight:800;color:var(--coffee-900);">{{ $it['title'] }}</h2>
                    </div>
                    <p style="font-size:13px;color:var(--ink-soft);margin:0 0 18px 26px;">{{ $it['desc'] }}</p>
                </div>
                <div style="padding:0 24px 24px;">
                    <form method="POST" action="{{ route('admin.settings.update') }}" onsubmit="event.preventDefault(); const _f=this; confirmModal('Save {{ $it['title'] }}','Save {{ $it['tab'] }} settings changes?',()=>_f.submit())">
                    @csrf

                    {{-- Provider status --}}
                    @php
                        $enField = collect($it['fields'])->first(fn($f)=>str_ends_with($f['key'],'_enabled'));
                        $tmField = collect($it['fields'])->first(fn($f)=>str_ends_with($f['key'],'_test_mode'));
                        $kv = function($k,$def='') use($settings){ $i=$settings->search(fn($x)=>$x->key===$k); return $i!==false ? $settings[$i]->value : $def; };
                        $enVal = $enField ? $kv($enField['key'],'0') : null;
                        $tmVal = $tmField ? $kv($tmField['key'],'1') : null;
                    @endphp
                    <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;margin-bottom:18px;padding:12px 16px;border:1px solid var(--line);border-radius:12px;background:var(--sand-50);">
                        <span style="font-size:12px;font-weight:800;letter-spacing:.06em;text-transform:uppercase;color:var(--ink-soft);margin-right:4px;">Status:</span>
                        @if($enField)
                            <span class="tag {{ $enVal==='1' ? 'tag-green' : 'tag-red' }}" style="font-size:11px;">{{ $enVal==='1' ? 'Enabled' : 'Disabled' }}</span>
                        @endif
                        @if($tmField)
                            <span class="tag {{ $tmVal==='1' ? 'tag-gold' : 'tag-green' }}" style="font-size:11px;">{{ $tmVal==='1' ? 'TEST / staging' : 'LIVE / production' }}</span>
                        @endif
                        <span class="cell-sub" style="font-size:11.5px;margin-left:auto;">Changes apply immediately after saving.</span>
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;" class="settings-2col">
                        @foreach($it['fields'] as $f)
                            @php
                                $fIdx = $settings->search(fn($x)=>$x->key===$f['key']);
                                $fVal = $fIdx !== false ? $settings[$fIdx]->value : ($f['def'] ?? '');
                                $styleFull = ($f['full'] ?? false) ? 'grid-column:1/-1;' : '';
                            @endphp
                            <div class="field" style="{{ $styleFull }}">
                                <label class="field-label">{{ $f['label'] }}</label>
                                <input type="hidden" name="settings[{{ $fIdx !== false ? $fIdx : $settings->count() }}][key]" value="{{ $f['key'] }}">
                                @if(($f['type'] ?? 'text') === 'select')
                                    <select name="settings[{{ $fIdx !== false ? $fIdx : $settings->count() }}][value]" style="width:100%;padding:10px 12px;border:1.5px solid var(--line);border-radius:10px;font-size:13px;background:#fff;">
                                        @foreach($f['options'] as $ov=>$ol)
                                            <option value="{{ $ov }}" @selected((string)$fVal===(string)$ov)>{{ $ol }}</option>
                                        @endforeach
                                    </select>
                                @elseif(($f['type'] ?? 'text') === 'textarea')
                                    <textarea name="settings[{{ $fIdx !== false ? $fIdx : $settings->count() }}][value]" rows="4" placeholder="{{ $f['placeholder'] ?? '' }}" style="width:100%;padding:10px 12px;border:1.5px solid var(--line);border-radius:10px;font-size:12px;font-family:monospace;background:#fff;resize:vertical;line-height:1.5;">{{ $fVal }}</textarea>
                                @else
                                    <input name="settings[{{ $fIdx !== false ? $fIdx : $settings->count() }}][value]" value="{{ $fVal }}" @if(($f['type'] ?? 'text') === 'password') type="password" onfocus="this.type='text'" onblur="if(!this.value)this.type='password'" @endif placeholder="{{ $f['placeholder'] ?? '' }}" style="width:100%;padding:10px 12px;border:1.5px solid var(--line);border-radius:10px;font-size:13px;@if(($f['type'] ?? 'text') === 'password') font-family:monospace;letter-spacing:.04em;@endif background:#fff;">
                                @endif
                                @if(!empty($f['hint']))<span class="field-hint">{{ $f['hint'] }}</span>@endif
                            </div>
                        @endforeach
                    </div>

                    <div style="display:flex;justify-content:flex-end;margin-top:18px;">
                        <button class="btn btn-primary" style="min-width:170px;justify-content:center;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex:none;"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                            Save {{ $it['tab'] }} Settings
                        </button>
                    </div>
                    </form>
                </div>
            </div>
            @endforeach

            {{-- Tab: General --}}
            <div id="tab-general" class="settings-tab-panel" style="display:none;">
                <div style="padding:20px 24px 0;">
                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33"/></svg>
                        <h2 style="margin:0;font-size:17px;font-weight:800;color:var(--coffee-900);">General Settings</h2>
                    </div>
                    <p style="font-size:13px;color:var(--ink-soft);margin:0 0 18px 26px;">All other key-value settings. Add custom settings at the bottom.</p>
                </div>
                <div style="padding:0 24px 24px;">
                    <form method="POST" action="{{ route('admin.settings.update') }}" onsubmit="event.preventDefault(); const _f=this; confirmModal('Save general settings','Save General Settings changes?',()=>_f.submit())">
                    @csrf
                    @forelse($otherSettings as $s)
                        @php $idx = $s->getKey('id') ?? $loop->index; $formIdx = $settings->search(fn($x)=>$x->key===$s->key); @endphp
                        <div style="padding:12px 14px;border:1px solid var(--line);border-radius:10px;background:#fff;margin-bottom:10px;display:grid;grid-template-columns:1fr 1fr auto;gap:12px;align-items:center;" class="settings-row">
                            <div style="min-width:0;">
                                <div class="cell-mono" style="font-weight:800;word-break:break-all;font-size:12.5px;">{{ $s->key }}</div>
                                <div class="cell-sub" style="font-size:11.5px;">{{ $s->group }} — {{ $s->description ?? '—' }}</div>
                            </div>
                            <div>
                                <input type="hidden" name="settings[{{ $formIdx }}][key]" value="{{ $s->key }}">
                                <input name="settings[{{ $formIdx }}][value]" value="{{ $s->value }}" style="width:100%;padding:10px 12px;border:1.5px solid var(--line);border-radius:10px;font-size:13px;background:#fff;">
                            </div>
                            <div>
                                <button type="button" class="btn-icon danger" title="Delete" data-delete-url="{{ route('admin.settings.destroy', encId($s->id)) }}" onclick="confirmDeleteSetting(this)"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/></svg></button>
                            </div>
                        </div>
                    @empty
                        <div class="empty-state" style="padding:24px;"><strong>No additional settings yet.</strong><p>Add one below.</p></div>
                    @endforelse

                    {{-- Add new --}}
                    <div style="margin-top:16px;padding:16px;border:1.5px dashed var(--line);border-radius:12px;background:var(--sand-50);">
                        <div style="font-weight:800;font-size:13px;color:var(--coffee-900);margin-bottom:10px;">Add New Setting</div>
                        <div class="form-grid-3 settings-3col" style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;">
                            <div class="field"><label class="field-label">Key *</label><input name="new_key" placeholder="e.g. contact_email" style="width:100%;padding:10px 12px;border:1.5px solid var(--line);border-radius:10px;font-size:13px;background:#fff;"></div>
                            <div class="field"><label class="field-label">Value</label><input name="new_value" placeholder="value" style="width:100%;padding:10px 12px;border:1.5px solid var(--line);border-radius:10px;font-size:13px;background:#fff;"></div>
                            <div class="field"><label class="field-label">Group</label><input name="new_group" placeholder="general" value="general" style="width:100%;padding:10px 12px;border:1.5px solid var(--line);border-radius:10px;font-size:13px;background:#fff;"></div>
                        </div>
                    </div>
                    <div style="display:flex;justify-content:flex-end;margin-top:16px;">
                        <button class="btn btn-primary" style="min-width:170px;justify-content:center;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex:none;"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                            Save General Settings
                        </button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.settings-tab{
    display:flex;align-items:center;gap:10px;width:100%;padding:10px 16px;border:none;background:none;font-size:13px;font-weight:600;color:var(--ink-soft);cursor:pointer;text-align:left;font-family:inherit;
    border-left:3px solid transparent;transition:all .15s;
}
.settings-tab:hover{background:var(--sand-100);color:var(--coffee-900);}
.settings-tab.active{border-left-color:var(--terracotta-600);background:#fff;color:var(--terracotta-600);font-weight:700;}
.settings-tab svg{width:16px;height:16px;flex:none;}
.settings-tab-panel{animation:tabFadeIn .2s ease;}
@keyframes tabFadeIn{from{opacity:0;transform:translateY(4px)}to{opacity:1;transform:none}}

@media (max-width:1024px){
    .settings-layout{grid-template-columns:180px 1fr !important;}
}
@media (max-width:900px){
    .settings-layout{grid-template-columns:1fr !important;}
    .settings-tabs-col{border-right:none !important;border-bottom:1px solid var(--line);padding:10px 12px !important;display:flex;align-items:center;gap:6px;overflow-x:auto;}
    .settings-tabs-col > div{display:none;}
    .settings-tab{width:auto;flex:0 0 auto;padding:9px 14px;border-left:none;border-bottom:3px solid transparent;white-space:nowrap;justify-content:center;border-radius:8px 8px 0 0;}
    .settings-tab:hover{background:var(--sand-100);}
    .settings-tab.active{border-bottom-color:var(--terracotta-600);background:var(--sand-100);}
    .settings-2col{grid-template-columns:1fr !important;}
    .settings-3col{grid-template-columns:1fr !important;}
    .settings-row{grid-template-columns:1fr auto !important;}
    .settings-row > div:first-child{grid-column:span 2;}
}
@media (max-width:640px){
    .settings-row{grid-template-columns:1fr !important;}
    .settings-row > div:first-child{grid-column:auto;}
}
</style>

<script>
function switchTab(btn, id){
    document.querySelectorAll('.settings-tab').forEach(t=>t.classList.remove('active'));
    document.querySelectorAll('.settings-tab-panel').forEach(p=>p.style.display='none');
    btn.classList.add('active');
    document.getElementById(id).style.display='block';
}
function confirmDeleteSetting(btn){
    const url = btn.getAttribute('data-delete-url');
    confirmModal('Delete setting','Are you sure you want to delete this setting? This action cannot be undone.',()=>{
        const f=document.createElement('form');
        f.method='POST'; f.action=url;
        const token=document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        f.innerHTML='<input type="hidden" name="_token" value="'+token+'"><input type="hidden" name="_method" value="DELETE">';
        document.body.appendChild(f); f.submit();
    });
}
function sendTestSms(){
    const to = document.getElementById('smsTestTo').value.trim();
    if(!to){ toast('Please enter a phone number first.','error'); return; }
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const btn = document.getElementById('smsTestBtn');
    const btnLabel = document.getElementById('smsTestBtnLabel');
    const res = document.getElementById('smsTestResult');
    const old = btnLabel.textContent;
    btn.disabled = true;
    btnLabel.textContent = 'Sending…';
    res.style.display = 'none';
    confirmModal('Send test SMS','A test message will be sent to '+to+' using the saved SMS settings.',()=>{
        fetch('{{ route('admin.settings.sms-test') }}', {
            method: 'POST',
            headers: {'Content-Type':'application/x-www-form-urlencoded','X-CSRF-TOKEN':token,'Accept':'application/json'},
            body: 'to='+encodeURIComponent(to)
        })
        .then(r=>r.json())
        .then(d=>{
            btn.disabled = false;
            btnLabel.textContent = old;
            if(d && d.ok){
                res.style.display = 'block';
                res.style.background = d.success ? 'var(--acacia-100)' : 'var(--danger-soft, #fdeceb)';
                res.style.border = '1px solid ' + (d.success ? '#c8d7a8' : '#f3c4c0');
                res.style.color = d.success ? 'var(--acacia-600)' : '#b3402f';
                res.textContent = d.message || (d.success ? 'Sent successfully.' : 'Failed.');
                if(d.success){ toast(d.message, 'success'); } else { toast(d.message, 'error'); }
            }
        })
        .catch(()=>{
            btn.disabled = false;
            btnLabel.textContent = old;
            res.style.display = 'block';
            res.style.background = '#fdeceb';
            res.style.border = '1px solid #f3c4c0';
            res.style.color = '#b3402f';
            res.textContent = 'Network error — the request did not reach the server.';
            toast('Network error while sending test SMS.','error');
        });
    });
}
</script>
@endsection