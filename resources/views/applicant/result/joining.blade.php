@extends('layouts.applicant')
@section('title','Joining Instructions')
@section('content')
<div class="page-head no-print">
    <div>
        <h1>Joining Instructions</h1>
        <p class="page-sub">{{ $application->application_number }} · {{ $programme?->name ?? $application->admissionWindow->admissionLevel->name }} · {{ $application->academicYear->name }}</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('applicant.result.show', encId($application->id)) }}" class="btn btn-ghost btn-sm">← Back</a>
        <a href="{{ route('applicant.application.letter', encId($application->id)) }}" class="btn btn-ghost btn-sm">Admission Letter</a>
        <button class="btn btn-primary btn-sm" onclick="window.print()"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex:none"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg> Print Joining</button>
    </div>
</div>

<div class="panel" style="max-width:800px;margin:24px auto;overflow:visible;box-shadow:0 8px 32px rgba(42,27,16,.12);">
    <div class="panel-body" style="padding:0;">
        <div style="background:#fff;padding:36px 48px 36px 56px;font-size:13.5px;line-height:1.7;color:var(--coffee-800);border-left:4px solid var(--acacia-600);">
            {{-- Header — same as admission letter --}}
            <div style="text-align:center;padding-bottom:18px;border-bottom:3px double var(--coffee-900);">
                <div style="display:flex;justify-content:center;">
                    @php $docLogo2 = \App\Models\Setting::getValue('university_logo'); @endphp
                    <div style="width:64px;height:64px;border-radius:14px;@if(!$docLogo2)background:linear-gradient(155deg,var(--terracotta-600),var(--gold-500));display:flex;align-items:center;justify-content:center;color:#fff;font-weight:800;font-size:22px;@else background:#fff;padding:4px;overflow:hidden;display:flex;align-items:center;justify-content:center;border:1.5px solid var(--line);@endif flex:none;">@if($docLogo2)<img src="{{ asset($docLogo2) }}" style="width:100%;height:100%;object-fit:contain;" alt="Logo">@else {{ substr(\App\Models\Setting::getValue('university_acronym','UDOM'),0,1) }} @endif</div>
                </div>
                <div style="margin-top:10px;">
                    <div style="font-weight:800;font-size:18px;color:var(--coffee-900);letter-spacing:.04em;">{{ strtoupper(\App\Models\Setting::getValue('university_name','University of Dodoma')) }}</div>
                    <div style="font-size:10px;letter-spacing:.1em;text-transform:uppercase;color:var(--ink-soft);font-weight:700;">Chuo Kikuu Cha Dodoma</div>
                </div>
                <div style="margin-top:10px;font-size:11px;color:var(--ink-soft);line-height:1.5;">{{ \App\Models\Setting::getValue('contact_box','P.O. Box 259') }}, {{ \App\Models\Setting::getValue('contact_city','Dodoma, Tanzania') }} · Tel: {{ \App\Models\Setting::getValue('admissions_phone','+255 26 231 0300') }} · Email: {{ \App\Models\Setting::getValue('admissions_email','admissions@university.ac.tz') }}<br>{{ \App\Models\Setting::getValue('admissions_office','Directorate of Undergraduate Studies') }} · Admissions Office</div>
            </div>

            @php
                $applicant = $application->applicant;
                $fullName = trim($applicant->first_name.' '.$applicant->middle_name.' '.$applicant->last_name);
                $reporting = $letter?->issued_at?->copy()->addDays(14)->format('d F Y') ?? now()->addDays(14)->format('d F Y');
            @endphp

            {{-- Meta — same as letter --}}
            <div style="margin-top:18px;display:flex;justify-content:space-between;gap:16px;flex-wrap:wrap;font-size:12px;">
                <div>
                    <div><span style="color:var(--ink-soft);font-weight:600;">Ref No:</span> <span class="mono" style="font-weight:800;color:var(--terracotta-600)">{{ $letter?->letter_number ?? \App\Models\Setting::getValue('university_acronym','UDOM').'/JI/'.$application->application_number }}</span></div>
                    <div><span style="color:var(--ink-soft);font-weight:600;">Application No:</span> <span class="mono" style="font-weight:700">{{ $application->application_number }}</span></div>
                </div>
                <div style="text-align:right;">
                    <div><span style="color:var(--ink-soft);font-weight:600;">Date:</span> {{ $letter?->issued_at?->format('d F Y') ?? now()->format('d F Y') }}</div>
                    <div><span style="color:var(--ink-soft);font-weight:600;">Academic Year:</span> {{ $application->academicYear->name }}</div>
                </div>
            </div>

            <div style="margin-top:18px;">
                <div style="font-weight:700;color:var(--coffee-900);">{{ $fullName }}</div>
                <div style="color:var(--ink-soft);font-size:12.5px;">{{ $applicant->phone }} · {{ $applicant->email }}</div>
                <div style="color:var(--ink-soft);font-size:12.5px;">{{ $applicant->currentAddress()?->district->name ?? '' }} {{ $applicant->currentAddress()?->region->name ?? '' }}</div>
                <div style="margin-top:12px;font-weight:700;">Dear {{ $fullName }},</div>
                <div style="text-align:center;margin-top:10px;font-weight:800;font-size:15px;color:var(--coffee-900);letter-spacing:.06em;text-transform:uppercase;border:1px solid var(--line);background:var(--sand-50);padding:8px 12px;border-radius:8px;">RE: JOINING INSTRUCTIONS — {{ $application->academicYear->name }} — {{ $programme?->name ?? $application->admissionWindow->admissionLevel->name }}</div>
                <p style="margin-top:14px;text-align:justify;">Congratulations once again on your selection to join <strong>{{ $programme?->name ?? $application->admissionWindow->admissionLevel->name }}</strong> ({{ $programme?->code ?? '—' }}) at <strong>{{ $programme?->campus->name ?? 'Main Campus' }}</strong>, {{ $programme?->department->faculty->name ?? 'College' }} for <strong>{{ $application->academicYear->name }}</strong>. Please read these joining instructions carefully and comply before and on reporting. This document is issued together with your admission letter.</p>
            </div>

            {{-- Admission Details — same table as letter --}}
            <div style="margin-top:16px;border:1px solid var(--line);border-radius:12px;overflow:hidden;">
                <div style="background:var(--coffee-900);color:#fff;padding:10px 14px;font-weight:700;font-size:12px;letter-spacing:.06em;text-transform:uppercase;">Admission Details — Fully Filled</div>
                <table style="width:100%;border-collapse:collapse;font-size:13px;">
                    <tr style="background:var(--sand-50)"><td style="padding:10px 14px;width:38%;color:var(--ink-soft);font-weight:600;border-bottom:1px solid var(--line)">Full Name</td><td style="padding:10px 14px;font-weight:700;color:var(--coffee-900);border-bottom:1px solid var(--line)">{{ $fullName }} ({{ $applicant->gender }})</td></tr>
                    <tr><td style="padding:10px 14px;color:var(--ink-soft);font-weight:600;border-bottom:1px solid var(--line)">Application Number</td><td style="padding:10px 14px;font-weight:800;color:var(--terracotta-600);border-bottom:1px solid var(--line)" class="mono">{{ $application->application_number }}</td></tr>
                    <tr style="background:var(--sand-50)"><td style="padding:10px 14px;color:var(--ink-soft);font-weight:600;border-bottom:1px solid var(--line)">Selected Programme</td><td style="padding:10px 14px;font-weight:800;color:var(--coffee-900);border-bottom:1px solid var(--line)">{{ $programme?->name ?? '—' }} <span style="color:var(--ink-soft);font-weight:600">({{ $programme?->code ?? '—' }})</span></td></tr>
                    <tr><td style="padding:10px 14px;color:var(--ink-soft);font-weight:600;border-bottom:1px solid var(--line)">Campus / Level</td><td style="padding:10px 14px;border-bottom:1px solid var(--line)">{{ $programme?->campus->name ?? 'Main Campus' }} · {{ $application->admissionWindow->admissionLevel->name }} ({{ $application->admissionWindow->admissionLevel->short_name }})</td></tr>
                    <tr style="background:var(--sand-50)"><td style="padding:10px 14px;color:var(--ink-soft);font-weight:600;border-bottom:1px solid var(--line)">Reporting Date</td><td style="padding:10px 14px;font-weight:700;border-bottom:1px solid var(--line)">{{ $reporting }} <span style="color:var(--ink-soft);font-weight:600">· 08:00 AM at the respective College</span></td></tr>
                </table>
            </div>

            {{-- Joining Instructions — same 4-grid as before but now within admission-letter style --}}
            <div style="margin-top:18px;">
                <div style="background:var(--sand-100);border:1px solid var(--line);border-radius:12px;padding:14px 16px;display:flex;align-items:center;gap:10px;">
                    <div style="width:36px;height:36px;border-radius:10px;background:var(--terracotta-600);display:flex;align-items:center;justify-content:center;color:#fff;flex:none"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg></div>
                    <div>
                        <div style="font-weight:800;color:var(--coffee-900);font-size:14px;">Joining Instructions — Full Details</div>
                        <div style="font-size:11px;letter-spacing:.06em;text-transform:uppercase;color:var(--ink-soft);font-weight:700;">Please read carefully before reporting</div>
                    </div>
                    <span class="tag tag-gold" style="margin-left:auto">{{ $programme?->code ?? \App\Models\Setting::getValue('university_acronym','UDOM') }}</span>
                </div>
                <div style="margin-top:12px;display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <div style="border:1px solid var(--line);border-radius:12px;padding:14px;background:var(--sand-50);">
                        <div style="font-weight:700;font-size:12px;letter-spacing:.06em;text-transform:uppercase;color:var(--coffee-700)">1. Documents to Bring — Original + 2 Copies</div>
                        <ul style="margin:8px 0 0 16px;font-size:12.5px;line-height:1.7;color:var(--coffee-800)">
                            <li>Original admission letter (this letter) — printed and signed</li>
                            <li>Original O-Level &amp; A-Level certificates / Diploma / Degree as applicable + transcripts</li>
                            <li>Birth certificate and NIDA / National ID / Passport</li>
                            <li>4 passport photos • Medical examination form (dully filled)</li>
                            <li>Proof of payment (control number receipt) + NHIF card</li>
                        </ul>
                    </div>
                    <div style="border:1px solid var(--line);border-radius:12px;padding:14px;background:var(--sand-50);">
                        <div style="font-weight:700;font-size:12px;letter-spacing:.06em;text-transform:uppercase;color:var(--coffee-700)">2. Fees &amp; Payments</div>
                        <ul style="margin:8px 0 0 16px;font-size:12.5px;line-height:1.7;color:var(--coffee-800)">
                            <li>Tuition: <strong>TZS {{ $programme ? number_format($programme->tuition_fee) : '1,200,000' }}</strong> per year</li>
                            <li>Pay via control number at NMB/CRDB/TCB (or mobile)</li>
                            <li>Keep receipt for registration</li>
                            <li>NHIF / Health insurance mandatory</li>
                        </ul>
                    </div>
                    <div style="border:1px solid var(--line);border-radius:12px;padding:14px;background:var(--sand-50);">
                        <div style="font-weight:700;font-size:12px;letter-spacing:.06em;text-transform:uppercase;color:var(--coffee-700)">3. Reporting &amp; Registration</div>
                        <ul style="margin:8px 0 0 16px;font-size:12.5px;line-height:1.7;color:var(--coffee-800)">
                            <li>Report to <strong>{{ $programme?->department->faculty->name ?? 'College' }} — {{ $programme?->campus->name ?? 'Main Campus' }}</strong></li>
                            <li>Date: <strong>{{ $reporting }}</strong> 08:00 AM</li>
                            <li>Orientation week follows immediately</li>
                            <li>Late reporting requires written permission</li>
                        </ul>
                    </div>
                    <div style="border:1px solid var(--line);border-radius:12px;padding:14px;background:var(--sand-50);">
                        <div style="font-weight:700;font-size:12px;letter-spacing:.06em;text-transform:uppercase;color:var(--coffee-700)">4. Accommodation, Health &amp; Conduct</div>
                        <ul style="margin:8px 0 0 16px;font-size:12.5px;line-height:1.7;color:var(--coffee-800)">
                            <li>Hostel via {{ \App\Models\Setting::getValue('university_acronym','UDOM') }} accommodation portal</li>
                            <li>Adhere to {{ \App\Models\Setting::getValue('university_acronym','UDOM') }} rules &amp; regulations</li>
                            <li>Dress code &amp; student conduct as per handbook</li>
                            <li>Medical exam at University Health Centre</li>
                        </ul>
                    </div>
                </div>
                <div style="margin-top:12px;padding:12px 14px;border:1px solid var(--terracotta-100);background:var(--terracotta-100);border-radius:10px;font-size:12.5px;color:var(--terracotta-600);line-height:1.6;">
                    <strong>Note:</strong> Failure to report on time without official communication may lead to forfeiture. Contact Admissions: <strong>{{ \App\Models\Setting::getValue('admissions_email','admissions@university.ac.tz') }}</strong> · {{ \App\Models\Setting::getValue('admissions_phone','+255 26 231 0300') }}.
                </div>
            </div>

            <div style="margin-top:24px;display:flex;justify-content:space-between;gap:24px;flex-wrap:wrap;font-size:11px;color:var(--ink-soft);border-top:1px dashed var(--line);padding-top:12px;">
                <span>Issued by Directorate of Undergraduate Studies — Valid with admission letter. Verify at {{ url('/verify-admission') }}.</span>
                <span class="mono">Ref: {{ $letter?->letter_number ?? \App\Models\Setting::getValue('university_acronym','UDOM').'/JI/'.$application->application_number }} · {{ $application->application_number }}</span>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .sidebar, .topbar, .page-head, .no-print { display:none !important; }
    .main { margin-left:0 !important; }
    .view-wrap { padding:0 !important; }
    .panel { border:none !important; box-shadow:none !important; }
    body { background:#fff !important; padding-top:0 !important; }
    .p-sticky-header, footer, #toastHost, .udom-fab, .udom-panel, .udom-overlay { display:none !important; }
}
</style>
@endsection
