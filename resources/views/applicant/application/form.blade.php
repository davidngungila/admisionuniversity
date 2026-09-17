@extends('layouts.applicant')
@section('title','Application Form — '.$application->application_number)
@section('content')
<div class="page-head no-print">
    <div>
        <h1>Application Form</h1>
        <p class="page-sub">{{ $application->application_number ?? 'DRAFT' }} · {{ $application->admissionWindow->admissionLevel->name }} · {{ $application->academicYear->name }} · {{ $application->status }}</p>
    </div>
    <div class="page-actions" style="display:flex;gap:8px;flex-wrap:wrap;">
        <a href="{{ route('applicant.application.form', encId($application->id)) }}?download=1" class="btn btn-primary btn-sm"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex:none"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg> Download PDF</a>
        <button class="btn btn-ghost btn-sm" onclick="window.print()"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex:none"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg> Print</button>
        <a href="{{ route('applicant.application.status', encId($application->id)) }}" class="btn btn-ghost btn-sm">← Back to Status</a>
        <a href="{{ route('applicant.dashboard') }}" class="btn btn-ghost btn-sm">Dashboard</a>
    </div>
</div>

<div class="panel" style="max-width:900px;margin:24px auto;overflow:visible;box-shadow:0 8px 32px rgba(42,27,16,.12);">
    <div class="panel-body" style="padding:0;">
        <div style="background:#fff;padding:36px 48px 36px 56px;font-size:13.5px;line-height:1.7;color:var(--coffee-800);border-left:4px solid var(--terracotta-600);">
            @php
              $applicant = $application->applicant;
              $fullName = trim($applicant->first_name.' '.$applicant->middle_name.' '.$applicant->last_name);
              $phone = $applicant->phone ?? $applicant->user?->phone ?? '—';
            @endphp
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

            {{-- Meta --}}
            <div style="margin-top:18px;display:flex;justify-content:space-between;gap:16px;flex-wrap:wrap;font-size:12px;">
                <div>
                    <div><span style="color:var(--ink-soft);font-weight:600;">Application No:</span> <span class="mono" style="font-weight:800;color:var(--terracotta-600)">{{ $application->application_number ?? 'DRAFT-'.$application->id }}</span></div>
                    <div><span style="color:var(--ink-soft);font-weight:600;">Status:</span> <span class="tag {{ $application->status==='SUBMITTED' ? 'tag-blue' : ($application->status==='SELECTED' ? 'tag-green' : 'tag-grey') }}">{{ $application->status }}</span> @if($application->submitted_at) <span style="color:var(--ink-soft);font-size:11px;">· {{ $application->submitted_at->format('d F Y H:i') }}</span> @endif</div>
                </div>
                <div style="text-align:right;">
                    <div><span style="color:var(--ink-soft);font-weight:600;">Academic Year:</span> {{ $application->academicYear->name }}</div>
                    <div><span style="color:var(--ink-soft);font-weight:600;">Level / Round:</span> {{ $application->admissionWindow->admissionLevel->name }} · Round {{ $application->admissionWindow->applicationRound->round_number }} · {{ $application->admissionWindow->applicant_category }}</div>
                </div>
            </div>

            <div style="text-align:center;margin-top:16px;font-weight:800;font-size:15px;color:var(--coffee-900);letter-spacing:.06em;text-transform:uppercase;border:1px solid var(--line);background:var(--sand-50);padding:8px 12px;border-radius:8px;">APPLICATION FORM — {{ strtoupper($fullName) }}</div>

            {{-- Applicant details --}}
            <div style="margin-top:16px;border:1px solid var(--line);border-radius:12px;overflow:hidden;">
                <div style="background:var(--coffee-900);color:#fff;padding:10px 14px;font-weight:700;font-size:12px;letter-spacing:.06em;text-transform:uppercase;">Applicant Details — Fully Filled</div>
                <table style="width:100%;border-collapse:collapse;font-size:13px;">
                    <tr style="background:var(--sand-50)"><td style="padding:10px 14px;width:34%;color:var(--ink-soft);font-weight:600;border-bottom:1px solid var(--line)">Full Name</td><td style="padding:10px 14px;font-weight:700;color:var(--coffee-900);border-bottom:1px solid var(--line)">{{ $fullName }} ({{ $applicant->gender ?? '—' }})</td></tr>
                    <tr><td style="padding:10px 14px;color:var(--ink-soft);font-weight:600;border-bottom:1px solid var(--line)">Date of Birth</td><td style="padding:10px 14px;border-bottom:1px solid var(--line)">{{ $applicant->date_of_birth?->format('d M Y') ?? '—' }}</td></tr>
                    <tr style="background:var(--sand-50)"><td style="padding:10px 14px;color:var(--ink-soft);font-weight:600;border-bottom:1px solid var(--line)">Citizenship</td><td style="padding:10px 14px;border-bottom:1px solid var(--line)">{{ $applicant->citizenship->name ?? '—' }} @if($applicant->nida_number) · NIDA: {{ $applicant->nida_number }} @endif</td></tr>
                    <tr><td style="padding:10px 14px;color:var(--ink-soft);font-weight:600;border-bottom:1px solid var(--line)">Phone / Email</td><td style="padding:10px 14px;border-bottom:1px solid var(--line)">{{ $phone }} · {{ $applicant->email }}</td></tr>
                    <tr style="background:var(--sand-50)"><td style="padding:10px 14px;color:var(--ink-soft);font-weight:600;border-bottom:1px solid var(--line)">Address</td><td style="padding:10px 14px;border-bottom:1px solid var(--line)">{{ $applicant->currentAddress()?->street ?? '' }} {{ $applicant->currentAddress()?->ward->name ?? '' }}, {{ $applicant->currentAddress()?->district->name ?? '' }}, {{ $applicant->currentAddress()?->region->name ?? '' }}</td></tr>
                    <tr><td style="padding:10px 14px;color:var(--ink-soft);font-weight:600;border-bottom:1px solid var(--line)">Exam Index / Passport</td><td style="padding:10px 14px;border-bottom:1px solid var(--line)">{{ $applicant->exam_index_number ?? $applicant->passport_number ?? $applicant->username ?? '—' }}</td></tr>
                    <tr style="background:var(--sand-50)"><td style="padding:10px 14px;color:var(--ink-soft);font-weight:600;">Entry / Scholarship</td><td style="padding:10px 14px;">{{ $applicant->entry_type ?? '—' }} @if($applicant->scholarship_category) · {{ $applicant->scholarship_category }} @endif · {{ $applicant->application_type ?? '—' }}</td></tr>
                </table>
            </div>

            {{-- Academic results --}}
            <div style="margin-top:16px;border:1px solid var(--line);border-radius:12px;overflow:hidden;">
                <div style="background:var(--sand-100);padding:10px 14px;font-weight:700;font-size:12px;letter-spacing:.06em;text-transform:uppercase;color:var(--coffee-700);border-bottom:1px solid var(--line);display:flex;justify-content:space-between;align-items:center;"><span>Academic Results — {{ $application->academicResults->count() }} sitting(s)</span><span class="tag tag-grey">{{ $application->academicResults->count() }}</span></div>
                @forelse($application->academicResults as $r)
                    <div style="padding:12px 14px;border-bottom:1px solid var(--line);">
                        <div style="display:flex;justify-content:space-between;gap:8px;flex-wrap:wrap;"><strong style="color:var(--coffee-900);">{{ $r->exam_type }} {{ $r->index_number }} ({{ $r->exam_year ?? '—' }})</strong><span style="color:var(--ink-soft);font-size:12px;">{{ $r->school_name ?? '' }}</span></div>
                        <div style="margin-top:6px;display:flex;flex-wrap:wrap;gap:6px;">
                            @foreach(($r->results ?? []) as $subj)
                                <span style="border:1px solid var(--line);background:var(--sand-50);padding:4px 8px;border-radius:8px;font-size:12px;"><strong>{{ $subj['subject'] ?? '—' }}</strong>: {{ $subj['grade'] ?? '—' }}</span>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div style="padding:16px;text-align:center;color:var(--ink-soft);font-size:13px;">No academic results recorded.</div>
                @endforelse
            </div>

            {{-- Programmes --}}
            <div style="margin-top:16px;border:1px solid var(--line);border-radius:12px;overflow:hidden;">
                <div style="background:var(--sand-100);padding:10px 14px;font-weight:700;font-size:12px;letter-spacing:.06em;text-transform:uppercase;color:var(--coffee-700);border-bottom:1px solid var(--line);display:flex;justify-content:space-between;align-items:center;"><span>Programmes Applied — {{ $application->selectedProgrammes->count() }}</span><span class="tag tag-grey">{{ $application->selectedProgrammes->count() }}</span></div>
                @forelse($application->selectedProgrammes->sortBy('preference_order') as $ap)
                    <div style="padding:12px 14px;border-bottom:1px solid var(--line);display:flex;justify-content:space-between;gap:12px;align-items:center;flex-wrap:wrap;">
                        <div>
                            <div style="font-weight:700;color:var(--coffee-900);">{{ $ap->preference_order }}. {{ $ap->programme->name }}</div>
                            <div style="color:var(--ink-soft);font-size:12px;">{{ $ap->programme->campus->name ?? '' }} · {{ $ap->programme->duration_years }}y · TZS {{ number_format($ap->programme->tuition_fee) }}</div>
                        </div>
                        <span class="tag tag-grey">{{ $ap->programme->code }}</span>
                    </div>
                @empty
                    <div style="padding:16px;text-align:center;color:var(--ink-soft);font-size:13px;">No programmes selected.</div>
                @endforelse
            </div>

            {{-- Payments & Documents --}}
            <div style="margin-top:16px;display:grid;grid-template-columns:1fr 1fr;gap:12px;" class="doc-grid2">
                <div style="border:1px solid var(--line);border-radius:12px;overflow:hidden;">
                    <div style="background:var(--sand-100);padding:10px 14px;font-weight:700;font-size:12px;letter-spacing:.06em;text-transform:uppercase;color:var(--coffee-700);border-bottom:1px solid var(--line);">Payments — {{ $application->payments->count() }}</div>
                    @forelse($application->payments as $p)
                        <div style="padding:10px 14px;border-bottom:1px solid var(--line);display:flex;justify-content:space-between;gap:8px;align-items:center;flex-wrap:wrap;font-size:13px;">
                            <span class="mono" style="font-size:11px;color:var(--terracotta-600);font-weight:700;">{{ $p->payment_reference }}</span>
                            <span style="font-weight:700;">{{ $p->currency }} {{ number_format($p->amount) }} <span class="tag tag-green" style="margin-left:6px">{{ $p->status }}</span></span>
                        </div>
                    @empty
                        <div style="padding:16px;text-align:center;color:var(--ink-soft);font-size:13px;">No payment record.</div>
                    @endforelse
                </div>
                <div style="border:1px solid var(--line);border-radius:12px;overflow:hidden;">
                    <div style="background:var(--sand-100);padding:10px 14px;font-weight:700;font-size:12px;letter-spacing:.06em;text-transform:uppercase;color:var(--coffee-700);border-bottom:1px solid var(--line);">Documents — {{ $application->documents->count() }}</div>
                    @forelse($application->documents as $d)
                        <div style="padding:10px 14px;border-bottom:1px solid var(--line);font-size:13px;">
                            <div style="font-weight:700;color:var(--coffee-900);">{{ $d->document_type }}</div>
                            <div style="color:var(--ink-soft);font-size:11px;word-break:break-all;">{{ $d->file_name }}</div>
                        </div>
                    @empty
                        <div style="padding:16px;text-align:center;color:var(--ink-soft);font-size:13px;">No documents uploaded.</div>
                    @endforelse
                </div>
            </div>

            <div style="margin-top:16px;padding:12px 14px;border:1px solid var(--acacia-100);background:var(--acacia-100);border-radius:10px;font-size:12.5px;color:var(--acacia-600);line-height:1.6;">
                <strong>Declaration:</strong> I declare that the information provided is true and correct. I understand that false information may lead to disqualification. Submitted on {{ $application->submitted_at?->format('d F Y H:i') ?? '—' }} via the Online Admission System.
            </div>

            <div style="margin-top:28px;display:flex;justify-content:space-between;gap:24px;flex-wrap:wrap;">
                <div style="font-size:12px;color:var(--ink-soft);">
                    <div>Wishing you a successful application.</div>
                    <div style="margin-top:18px;font-weight:700;color:var(--coffee-900);">For: Vice Chancellor</div>
                    <div>{{ \App\Models\Setting::getValue('university_name','University of Dodoma') }}</div>
                </div>
                <div style="text-align:center;">
                    <div style="width:120px;height:1px;background:var(--coffee-900);margin:32px auto 6px;"></div>
                    <div style="font-size:11px;letter-spacing:.06em;text-transform:uppercase;font-weight:700;color:var(--coffee-700)">Registrar — Academic</div>
                    <div style="font-size:11px;color:var(--ink-soft)">{{ $application->submitted_at?->format('d F Y') ?? now()->format('d F Y') }}</div>
                </div>
            </div>

            <div style="margin-top:24px;padding-top:12px;border-top:1px dashed var(--line);display:flex;justify-content:space-between;gap:12px;flex-wrap:wrap;font-size:11px;color:var(--ink-soft);">
                <span>This form is system-generated and valid without signature when verified online at {{ url('/verify-admission') }}.</span>
                <span class="mono">Ref: {{ $application->application_number ?? 'DRAFT-'.$application->id }}</span>
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
@media(max-width:700px){ .doc-grid2{grid-template-columns:1fr !important;} }
</style>
@endsection