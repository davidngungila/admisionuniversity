@extends('layouts.applicant')
@section('title','Admission Letter')
@section('content')
<div class="page-head no-print">
    <div>
        <h1>Admission Letter</h1>
        <p class="page-sub">{{ $letter->letter_number }} · {{ $application->application_number }}</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('applicant.result.show', encId($application->id)) }}" class="btn btn-ghost btn-sm">← Back</a>
        <a href="{{ route('applicant.application.joining', encId($application->id)) }}" class="btn btn-ghost btn-sm" style="border-color:var(--acacia-600);color:var(--acacia-600)">Joining Instructions</a>
        <button class="btn btn-primary btn-sm" onclick="window.print()"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex:none"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg> Print Letter</button>
    </div>
</div>

<div class="panel" style="max-width:800px;margin:24px auto;overflow:visible;box-shadow:0 8px 32px rgba(42,27,16,.12);">
    <div class="panel-body" style="padding:0;">
        {{-- PDF Preview --}}
        <div style="background:#fff;padding:36px 48px 36px 56px;font-size:13.5px;line-height:1.7;color:var(--coffee-800);border-left:4px solid var(--terracotta-600);">
            {{-- Header --}}
            <div style="text-align:center;padding-bottom:18px;border-bottom:3px double var(--coffee-900);">
                <div style="display:flex;justify-content:center;">
                    @php $docLogo = \App\Models\Setting::getValue('university_logo'); @endphp
                    <div style="width:64px;height:64px;border-radius:14px;@if(!$docLogo)background:linear-gradient(155deg,var(--terracotta-600),var(--gold-500));display:flex;align-items:center;justify-content:center;color:#fff;font-weight:800;font-size:22px;@else background:#fff;padding:4px;overflow:hidden;display:flex;align-items:center;justify-content:center;border:1.5px solid var(--line);@endif flex:none;">@if($docLogo)<img src="{{ asset($docLogo) }}" style="width:100%;height:100%;object-fit:contain;" alt="Logo">@else {{ substr(\App\Models\Setting::getValue('university_acronym','UDOM'),0,1) }} @endif</div>
                </div>
                <div style="margin-top:10px;">
                    <div style="font-weight:800;font-size:18px;color:var(--coffee-900);letter-spacing:.04em;">{{ strtoupper(\App\Models\Setting::getValue('university_name','University of Dodoma')) }}</div>
                    <div style="font-size:10px;letter-spacing:.1em;text-transform:uppercase;color:var(--ink-soft);font-weight:700;">Chuo Kikuu Cha Dodoma</div>
                </div>
                <div style="margin-top:10px;font-size:11px;color:var(--ink-soft);line-height:1.5;">{{ \App\Models\Setting::getValue('contact_box','P.O. Box 259') }}, {{ \App\Models\Setting::getValue('contact_city','Dodoma, Tanzania') }} · Tel: {{ \App\Models\Setting::getValue('admissions_phone','+255 26 231 0300') }} · Email: {{ \App\Models\Setting::getValue('admissions_email','admissions@university.ac.tz') }}<br>{{ \App\Models\Setting::getValue('admissions_office','Directorate of Undergraduate Studies') }} · Admissions Office</div>
            </div>

            {{-- Letter meta --}}
            <div style="margin-top:18px;display:flex;justify-content:space-between;gap:16px;flex-wrap:wrap;font-size:12px;">
                <div>
                    <div><span style="color:var(--ink-soft);font-weight:600;">Ref No:</span> <span class="mono" style="font-weight:800;color:var(--terracotta-600)">{{ $letter->letter_number }}</span></div>
                    <div><span style="color:var(--ink-soft);font-weight:600;">Application No:</span> <span class="mono" style="font-weight:700">{{ $application->application_number }}</span></div>
                </div>
                <div style="text-align:right;">
                    <div><span style="color:var(--ink-soft);font-weight:600;">Date:</span> {{ $letter->issued_at?->format('d F Y') ?? $letter->created_at->format('d F Y') }}</div>
                    <div><span style="color:var(--ink-soft);font-weight:600;">Academic Year:</span> {{ $application->academicYear->name }}</div>
                </div>
            </div>

            {{-- Applicant address --}}
            <div style="margin-top:18px;">
                @php
                    $applicant = $application->applicant;
                    $fullName = trim($applicant->first_name.' '.$applicant->middle_name.' '.$applicant->last_name);
                    $programme = $application->selectedProgrammes->first()?->programme ?? $application->selectionResults->first()?->programme;
                    $campus = $programme?->campus->name ?? $application->admissionWindow->admissionLevel->name;
                @endphp
                <div style="font-weight:700;color:var(--coffee-900);">{{ $fullName }}</div>
                <div style="color:var(--ink-soft);font-size:12.5px;">{{ $applicant->phone }} · {{ $applicant->email }}</div>
                <div style="color:var(--ink-soft);font-size:12.5px;">{{ $applicant->currentAddress()?->district->name ?? '' }} {{ $applicant->currentAddress()?->region->name ?? '' }}</div>
                <div style="margin-top:12px;font-weight:700;">Dear {{ $fullName }},</div>
                <div style="text-align:center;margin-top:10px;font-weight:800;font-size:15px;color:var(--coffee-900);letter-spacing:.06em;text-transform:uppercase;border:1px solid var(--line);background:var(--sand-50);padding:8px 12px;border-radius:8px;">RE: ADMISSION TO {{ strtoupper(\App\Models\Setting::getValue('university_name','University of Dodoma')) }} — {{ $application->academicYear->name }}</div>
            </div>

            {{-- Body --}}
            <div style="margin-top:16px;text-align:justify;">
                <p style="margin:0;">We are pleased to inform you that you have been <strong>selected</strong> to join the {{ \App\Models\Setting::getValue('university_name','University of Dodoma') }} for the <strong>{{ $application->academicYear->name }}</strong> academic year as detailed below. Congratulations on your achievement and welcome to the {{ \App\Models\Setting::getValue('university_acronym','UDOM') }} family!</p>

                <div style="margin-top:16px;border:1px solid var(--line);border-radius:12px;overflow:hidden;">
                    <div style="background:var(--coffee-900);color:#fff;padding:10px 14px;font-weight:700;font-size:12px;letter-spacing:.06em;text-transform:uppercase;">Admission Details — Fully Filled</div>
                    <table style="width:100%;border-collapse:collapse;font-size:13px;">
                        <tr style="background:var(--sand-50)"><td style="padding:10px 14px;width:38%;color:var(--ink-soft);font-weight:600;border-bottom:1px solid var(--line)">Full Name</td><td style="padding:10px 14px;font-weight:700;color:var(--coffee-900);border-bottom:1px solid var(--line)">{{ $fullName }} ({{ $applicant->gender }})</td></tr>
                        <tr><td style="padding:10px 14px;color:var(--ink-soft);font-weight:600;border-bottom:1px solid var(--line)">Date of Birth / NIDA</td><td style="padding:10px 14px;border-bottom:1px solid var(--line)">{{ $applicant->date_of_birth?->format('d M Y') ?? '—' }} · {{ $applicant->nida_number ?? '—' }}</td></tr>
                        <tr style="background:var(--sand-50)"><td style="padding:10px 14px;color:var(--ink-soft);font-weight:600;border-bottom:1px solid var(--line)">Citizenship</td><td style="padding:10px 14px;border-bottom:1px solid var(--line)">{{ $applicant->citizenship->name ?? 'Tanzanian' }} · {{ $applicant->phone }}</td></tr>
                        <tr><td style="padding:10px 14px;color:var(--ink-soft);font-weight:600;border-bottom:1px solid var(--line)">Application Number</td><td style="padding:10px 14px;font-weight:800;color:var(--terracotta-600);border-bottom:1px solid var(--line)" class="mono">{{ $application->application_number }}</td></tr>
                        <tr style="background:var(--sand-50)"><td style="padding:10px 14px;color:var(--ink-soft);font-weight:600;border-bottom:1px solid var(--line)">Selected Programme</td><td style="padding:10px 14px;font-weight:800;color:var(--coffee-900);border-bottom:1px solid var(--line)">{{ $programme?->name ?? '—' }} <span style="color:var(--ink-soft);font-weight:600">({{ $programme?->code ?? '—' }})</span></td></tr>
                        <tr><td style="padding:10px 14px;color:var(--ink-soft);font-weight:600;border-bottom:1px solid var(--line)">Campus / Level</td><td style="padding:10px 14px;border-bottom:1px solid var(--line)">{{ $programme?->campus->name ?? 'Main Campus' }} · {{ $application->admissionWindow->admissionLevel->name }} ({{ $application->admissionWindow->admissionLevel->short_name }})</td></tr>
                        <tr style="background:var(--sand-50)"><td style="padding:10px 14px;color:var(--ink-soft);font-weight:600;border-bottom:1px solid var(--line)">Duration / Mode</td><td style="padding:10px 14px;border-bottom:1px solid var(--line)">{{ $programme?->duration_years ?? '—' }} Years · {{ $programme?->study_mode ?? 'Full Time' }}</td></tr>
                        <tr><td style="padding:10px 14px;color:var(--ink-soft);font-weight:600;">Reporting Date</td><td style="padding:10px 14px;font-weight:700;">{{ $letter->issued_at?->addDays(14)->format('d F Y') ?? now()->addDays(14)->format('d F Y') }} <span style="color:var(--ink-soft);font-weight:600">· 08:00 AM at the respective College</span></td></tr>
                    </table>
                </div>

                <div style="margin-top:14px;padding:12px 14px;border:1px solid var(--acacia-100);background:var(--acacia-100);border-radius:10px;font-size:12.5px;color:var(--acacia-600);line-height:1.6;">
                    <strong>Letter content:</strong> {{ $letter->content ?? 'Congratulations! You have been admitted. Please report to the campus with this letter and required documents.' }}
                </div>

                <p style="margin-top:14px;">You are required to confirm your admission through the admission system and report with the originals and copies of this letter, your academic certificates, birth certificate, NIDA or passport, and four passport-size photographs.</p>
            </div>

            {{-- Joining Instruction --}}
            <div id="joining" style="margin-top:22px;page-break-before:always;">
                <div style="background:var(--sand-100);border:1px solid var(--line);border-radius:12px;padding:14px 16px;display:flex;align-items:center;gap:10px;">
                    <div style="width:36px;height:36px;border-radius:10px;background:var(--terracotta-600);display:flex;align-items:center;justify-content:center;color:#fff;flex:none"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg></div>
                    <div>
                        <div style="font-weight:800;color:var(--coffee-900);font-size:14px;">Joining Instructions</div>
                        <div style="font-size:11px;letter-spacing:.06em;text-transform:uppercase;color:var(--ink-soft);font-weight:700;">Please read carefully before reporting</div>
                    </div>
                    <span class="tag tag-gold" style="margin-left:auto">{{ $programme?->code ?? \App\Models\Setting::getValue('university_acronym','UDOM') }}</span>
                </div>
                <div style="margin-top:12px;display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <div style="border:1px solid var(--line);border-radius:12px;padding:14px;background:var(--sand-50);">
                        <div style="font-weight:700;font-size:12px;letter-spacing:.06em;text-transform:uppercase;color:var(--coffee-700)">1. Documents to Bring</div>
                        <ul style="margin:8px 0 0 16px;font-size:12.5px;line-height:1.7;color:var(--coffee-800)">
                            <li>Original admission letter (this letter)</li>
                            <li>Original O-Level &amp; A-Level certificates / Diploma / Degree as applicable</li>
                            <li>Birth certificate, NIDA / Passport</li>
                            <li>4 passport photos • Medical examination form (dully filled)</li>
                            <li>Proof of payment (control number receipt)</li>
                        </ul>
                    </div>
                    <div style="border:1px solid var(--line);border-radius:12px;padding:14px;background:var(--sand-50);">
                        <div style="font-weight:700;font-size:12px;letter-spacing:.06em;text-transform:uppercase;color:var(--coffee-700)">2. Fees &amp; Payments</div>
                        <ul style="margin:8px 0 0 16px;font-size:12.5px;line-height:1.7;color:var(--coffee-800)">
                            <li>Tuition: <strong>TZS {{ $programme ? number_format($programme->tuition_fee) : '—' }}</strong> per year</li>
                            <li>Pay via control number at NMB/CRDB/TCB</li>
                            <li>Keep receipt for registration</li>
                            <li>NHIF / Health insurance required</li>
                        </ul>
                    </div>
                    <div style="border:1px solid var(--line);border-radius:12px;padding:14px;background:var(--sand-50);">
                        <div style="font-weight:700;font-size:12px;letter-spacing:.06em;text-transform:uppercase;color:var(--coffee-700)">3. Reporting &amp; Registration</div>
                        <ul style="margin:8px 0 0 16px;font-size:12.5px;line-height:1.7;color:var(--coffee-800)">
                            <li>Report to <strong>{{ $programme?->department->faculty->name ?? 'College' }} — {{ $programme?->campus->name ?? 'Main Campus' }}</strong></li>
                            <li>Date: <strong>{{ $letter->issued_at?->addDays(14)->format('d F Y') ?? now()->addDays(14)->format('d F Y') }}</strong> 08:00 AM</li>
                            <li>Orientation week follows reporting</li>
                            <li>Late reporting requires written permission</li>
                        </ul>
                    </div>
                    <div style="border:1px solid var(--line);border-radius:12px;padding:14px;background:var(--sand-50);">
                        <div style="font-weight:700;font-size:12px;letter-spacing:.06em;text-transform:uppercase;color:var(--coffee-700)">4. Accommodation &amp; Conduct</div>
                        <ul style="margin:8px 0 0 16px;font-size:12.5px;line-height:1.7;color:var(--coffee-800)">
                            <li>Hostel application via {{ \App\Models\Setting::getValue('university_acronym','UDOM') }} accommodation portal</li>
                            <li>Adhere to {{ \App\Models\Setting::getValue('university_acronym','UDOM') }} rules &amp; regulations</li>
                            <li>Dress code &amp; student conduct as per handbook</li>
                            <li>Medical exam at University Health Centre</li>
                        </ul>
                    </div>
                </div>
                <div style="margin-top:12px;padding:12px 14px;border:1px solid var(--terracotta-100);background:var(--terracotta-100);border-radius:10px;font-size:12.5px;color:var(--terracotta-600);line-height:1.6;">
                    <strong>Note:</strong> Failure to report on time without official communication may lead to forfeiture of your admission. For issues, contact Admissions: <strong>{{ \App\Models\Setting::getValue('admissions_email','admissions@university.ac.tz') }}</strong> · {{ \App\Models\Setting::getValue('admissions_phone','+255 26 231 0300') }} or use the {{ \App\Models\Setting::getValue('university_acronym','UDOM') }} Support float button.
                </div>
            </div>

            {{-- Signature --}}
            <div style="margin-top:28px;display:flex;justify-content:space-between;gap:24px;flex-wrap:wrap;">
                <div style="font-size:12px;color:var(--ink-soft);">
                    <div>Wishing you a successful academic journey.</div>
                    <div style="margin-top:18px;font-weight:700;color:var(--coffee-900);">For: Vice Chancellor</div>
                    <div>{{ \App\Models\Setting::getValue('university_name','University of Dodoma') }}</div>
                </div>
                <div style="text-align:center;">
                    <div style="width:120px;height:1px;background:var(--coffee-900);margin:32px auto 6px;"></div>
                    <div style="font-size:11px;letter-spacing:.06em;text-transform:uppercase;font-weight:700;color:var(--coffee-700)">Registrar — Academic</div>
                    <div style="font-size:11px;color:var(--ink-soft)">{{ $letter->issued_at?->format('d F Y') ?? $letter->created_at->format('d F Y') }}</div>
                </div>
            </div>

            <div style="margin-top:24px;padding-top:12px;border-top:1px dashed var(--line);display:flex;justify-content:space-between;gap:12px;flex-wrap:wrap;font-size:11px;color:var(--ink-soft);">
                <span>This letter is system-generated and valid without signature when verified online at {{ url('/verify-admission') }}.</span>
                <span class="mono">Ref: {{ $letter->letter_number }} · {{ $application->application_number }}</span>
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
    .p-sticky-header, .p-bottombar, footer, #toastHost, .udom-fab, .udom-panel, .udom-overlay { display:none !important; }
}
</style>
@endsection
