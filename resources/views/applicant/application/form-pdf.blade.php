<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
@page { margin: 18mm 14mm 18mm 20mm; }
body { font-family: DejaVu Sans, sans-serif; font-size: 9pt; line-height: 1.55; color: #07364F; border-left: 3px solid #0066CC; padding-left: 12px; }
.header { text-align: center; border-bottom: 3px double #07364F; padding-bottom: 10px; }
.logo { width: 50px; height: 50px; background: #0066CC; color: #fff; display: inline-block; text-align: center; line-height: 50px; font-weight: 800; border-radius: 10px; }
.meta { margin-top: 10px; font-size: 8pt; }
.meta table { width: 100%; }
.details { margin-top: 12px; border: 1px solid #E4D7C2; border-radius: 8px; overflow: hidden; }
.details-head { background: #07364F; color: #fff; padding: 7px 10px; font-weight: 700; font-size: 8.5pt; letter-spacing: 0.5px; }
.tbl { width: 100%; border-collapse: collapse; font-size: 8.5pt; }
.tbl td { padding: 6px 8px; border-bottom: 1px solid #E4D7C2; }
.tbl tr:last-child td { border-bottom: none; }
.k { color: #6B5A48; font-weight: 600; width: 32%; background: #FBF7EF; }
.v { color: #07364F; font-weight: 700; }
.section { margin-top: 14px; border: 1px solid #E4D7C2; border-radius: 8px; overflow: hidden; }
.section-head { background: #FBF7EF; padding: 7px 10px; font-weight: 800; font-size: 8.5pt; color: #07364F; border-bottom: 1px solid #E4D7C2; }
.item { padding: 7px 10px; border-bottom: 1px solid #E4D7C2; font-size: 8pt; }
.item:last-child { border-bottom: none; }
.tag { display: inline-block; padding: 1px 6px; border-radius: 8px; font-size: 7pt; font-weight: 700; }
</style>
</head>
<body>
@php
  $applicant = $application->applicant;
  $fullName = trim($applicant->first_name.' '.$applicant->middle_name.' '.$applicant->last_name);
  $phone = $applicant->phone ?? $applicant->user?->phone ?? '—';
@endphp
<div class="header">
  @php $pdfLogo = \App\Models\Setting::getValue('university_logo'); @endphp
  @if($pdfLogo)
    <img src="{{ public_path($pdfLogo) }}" style="width:50px;height:50px;object-fit:contain;border-radius:10px;background:#fff;padding:3px;border:1px solid #E4D7C2;" alt="Logo">
  @else
    <div class="logo">{{ substr(\App\Models\Setting::getValue('university_acronym','UDOM'),0,1) }}</div>
  @endif
  <div style="font-weight:800; font-size:13pt; margin-top:6px;">{{ strtoupper(\App\Models\Setting::getValue('university_name','University of Dodoma')) }}</div>
  <div style="font-size:7pt; letter-spacing:1px; color:#6B5A48;">{{ \App\Models\Setting::getValue('university_name_sw','Chuo Kikuu Cha Dodoma') }}</div>
  <div style="font-size:7pt; color:#6B5A48; margin-top:4px;">{{ \App\Models\Setting::getValue('contact_box','P.O. Box 259') }}, {{ \App\Models\Setting::getValue('contact_city','Dodoma, Tanzania') }} &middot; Tel: {{ \App\Models\Setting::getValue('admissions_phone','+255 26 231 0300') }} &middot; {{ \App\Models\Setting::getValue('admissions_email','admissions@university.ac.tz') }}</div>
</div>

@php $verifyUrl = url('/verify-admission?application_number='.($application->application_number ?? '')); $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=90x90&data='.urlencode($verifyUrl); @endphp
<div style="position:relative; margin-top:10px; padding:10px; background:#E1EFEA; border:1px solid #c8d7a8; border-radius:8px; text-align:center;">
  <div style="position:absolute;top:6px;right:10px;transform:rotate(7deg);border:2px solid #2E7D6B;color:#2E7D6B;font-weight:800;letter-spacing:.12em;font-size:7pt;padding:3px 8px;border-radius:6px;">VERIFIED</div>
  <div style="display:inline-flex;align-items:center;gap:6px;background:#fff;color:#2E7D6B;border:1px solid #c8d7a8;padding:4px 10px;border-radius:16px;font-weight:800;font-size:7pt;letter-spacing:.06em;text-transform:uppercase;"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:10px;height:10px"><path d="M20 6L9 17l-5-5"/></svg> Record Verified Authentic</div>
  <div style="margin-top:6px; font-size:7pt; color:#6B5A48;">Application <strong style="color:#07364F;">{{ $application->application_number ?? '—' }}</strong> &middot; {{ $application->status }}</div>
  <div style="margin-top:6px; display:flex; align-items:center; justify-content:center; gap:8px;">
    <img src="{{ $qrUrl }}" alt="QR" style="width:60px;height:60px; border:1px solid #E4D7C2; border-radius:6px; background:#fff; padding:3px;">
    <div style="text-align:left; font-size:6.5pt; color:#6B5A48; line-height:1.3;">Verify at<br><span style="color:#07364F; font-weight:700; word-break:break-all;">{{ $verifyUrl }}</span></div>
  </div>
</div>

<div class="meta">
  <table><tr>
    <td>Application No: <strong style="color:#0066CC;">{{ $application->application_number ?? 'DRAFT-'.$application->id }}</strong><br>Status: <strong>{{ $application->status }}</strong> @if($application->submitted_at) &middot; Submitted: {{ \Carbon\Carbon::parse($application->submitted_at)->format('d F Y H:i') }} @endif</td>
    <td style="text-align:right;">Academic Year: {{ $application->academicYear->name }}<br>{{ $application->admissionWindow->admissionLevel->name }} &middot; Round {{ $application->admissionWindow->applicationRound->round_number }} &middot; {{ $application->admissionWindow->applicant_category }}</td>
  </tr></table>
</div>

<div style="text-align:center; margin-top:10px; font-weight:800; font-size:10pt; border:1px solid #E4D7C2; background:#FBF7EF; padding:6px; border-radius:6px;">
  APPLICATION FORM &mdash; {{ strtoupper($applicant->fullName()) }}
</div>

<div class="details">
  <div class="details-head">APPLICANT DETAILS</div>
  <table class="tbl">
    <tr><td class="k">Full Name</td><td class="v">{{ $fullName }} ({{ $applicant->gender ?? '—' }})</td></tr>
    <tr><td class="k">Date of Birth</td><td class="v">{{ $applicant->date_of_birth?->format('d M Y') ?? '—' }}</td></tr>
    <tr><td class="k">Citizenship</td><td class="v">{{ $applicant->citizenship->name ?? '—' }} @if($applicant->nida_number) &middot; NIDA: {{ $applicant->nida_number }} @endif</td></tr>
    <tr><td class="k">Phone / Email</td><td class="v">{{ $phone }} &middot; {{ $applicant->email }}</td></tr>
    <tr><td class="k">Address</td><td class="v">{{ $applicant->currentAddress()?->street ?? '' }} {{ $applicant->currentAddress()?->ward->name ?? '' }}, {{ $applicant->currentAddress()?->district->name ?? '' }}, {{ $applicant->currentAddress()?->region->name ?? '' }}</td></tr>
    <tr><td class="k">Exam Index / Passport</td><td class="v">{{ $applicant->exam_index_number ?? $applicant->passport_number ?? $applicant->username ?? '—' }}</td></tr>
    <tr><td class="k">Entry / Scholarship</td><td class="v">{{ $applicant->entry_type ?? '—' }} @if($applicant->scholarship_category) &middot; {{ $applicant->scholarship_category }} @endif &middot; {{ $applicant->application_type ?? '—' }}</td></tr>
  </table>
</div>

<div class="section">
  <div class="section-head">ACADEMIC RESULTS ({{ $application->academicResults->count() }} sitting{{ $application->academicResults->count()===1 ? '' : 's' }})</div>
  @forelse($application->academicResults as $r)
    <div class="item">
      <div style="display:flex;justify-content:space-between;gap:8px;">
        <strong>{{ $r->exam_type }} {{ $r->index_number }} ({{ $r->exam_year ?? '—' }})</strong>
        <span style="color:#6B5A48;">{{ $r->school_name ?? '' }}</span>
      </div>
      <div style="margin-top:4px; display:flex; flex-wrap:wrap; gap:6px;">
        @foreach(($r->results ?? []) as $subj)
          <span style="border:1px solid #E4D7C2; background:#fff; padding:2px 6px; border-radius:6px; font-size:7.5pt;"><strong>{{ $subj['subject'] ?? '—' }}</strong>: {{ $subj['grade'] ?? '—' }}</span>
        @endforeach
      </div>
    </div>
  @empty
    <div class="item" style="color:#6B5A48; text-align:center;">No academic results recorded.</div>
  @endforelse
</div>

<div class="section">
  <div class="section-head">PROGRAMMES APPLIED ({{ $application->selectedProgrammes->count() }})</div>
  @forelse($application->selectedProgrammes->sortBy('preference_order') as $ap)
    <div class="item" style="display:flex;justify-content:space-between;gap:8px;">
      <div><strong>{{ $ap->preference_order }}. {{ $ap->programme->name }}</strong> <span style="color:#6B5A48;">({{ $ap->programme->code }})</span><br><span style="color:#6B5A48; font-size:7pt;">{{ $ap->programme->campus->name ?? '' }} &middot; {{ $ap->programme->duration_years }}y &middot; TZS {{ number_format($ap->programme->tuition_fee) }}</span></div>
      <span class="tag" style="background:#E4F0FA; color:#0066CC; align-self:center;">Choice {{ $ap->preference_order }}</span>
    </div>
  @empty
    <div class="item" style="color:#6B5A48; text-align:center;">No programmes selected.</div>
  @endforelse
</div>

<div style="display:flex; gap:10px;">
  <div class="section" style="flex:1;">
    <div class="section-head">PAYMENTS ({{ $application->payments->count() }})</div>
    @forelse($application->payments as $p)
      <div class="item" style="display:flex;justify-content:space-between;">
        <span style="font-family: DejaVu Sans Mono, monospace; font-size:7pt; color:#0066CC;">{{ $p->payment_reference }}</span>
        <span><strong>{{ $p->currency }} {{ number_format($p->amount) }}</strong> <span class="tag" style="background:#E1EFEA; color:#2E7D6B;">{{ $p->status }}</span></span>
      </div>
    @empty
      <div class="item" style="color:#6B5A48; text-align:center;">No payment record.</div>
    @endforelse
  </div>
  <div class="section" style="flex:1;">
    <div class="section-head">DOCUMENTS ({{ $application->documents->count() }})</div>
    @forelse($application->documents as $d)
      <div class="item"><strong>{{ $d->document_type }}</strong><br><span style="color:#6B5A48; font-size:7pt; word-break:break-all;">{{ $d->file_name }}</span></div>
    @empty
      <div class="item" style="color:#6B5A48; text-align:center;">No documents uploaded.</div>
    @endforelse
  </div>
</div>

<div style="margin-top:12px; padding:8px 10px; border:1px solid #E4D7C2; background:#FBF7EF; border-radius:6px; font-size:8pt; color:#6B5A48;">
  <strong>Declaration:</strong> I declare that the information provided is true and correct. I understand that false information may lead to disqualification. Submitted on {{ $application->submitted_at ? \Carbon\Carbon::parse($application->submitted_at)->format('d F Y H:i') : '—' }} via the Online Admission System.
</div>

@php $activeSignatory = \App\Models\Signatory::where('is_active',1)->orderBy('order_index')->first(); @endphp
@if($activeSignatory)
  <div style="margin-top:16px; display:flex; justify-content:space-between; gap:18px; font-size:8pt;">
    <div style="flex:1; text-align:center; max-width:48%;">
      <div style="height:36px; border-bottom:1px solid #07364F; margin:10px 0 4px;"></div>
      <div style="font-weight:800;">For: Vice Chancellor</div>
      <div style="color:#6B5A48;">{{ \App\Models\Setting::getValue('university_name','University of Dodoma') }}<br>17 September 2026</div>
    </div>
    <div style="flex:1; text-align:center; max-width:48%;">
      @if($activeSignatory->signature_image && file_exists(public_path($activeSignatory->signature_image)))
        <img src="{{ public_path($activeSignatory->signature_image) }}" style="height:36px; object-fit:contain; margin:4px auto 2px;" alt="Signature">
      @else
        <div style="height:36px; border-bottom:1px solid #07364F; margin:10px 0 4px;"></div>
      @endif
      <div style="font-weight:800;">For: {{ $activeSignatory->title ? $activeSignatory->title.' ' : '' }}{{ $activeSignatory->name }}</div>
      <div style="color:#6B5A48;">{{ $activeSignatory->designation ?: 'Registrar — Academic' }}<br>17 September 2026</div>
    </div>
  </div>
@else
  <div style="margin-top:16px; display:flex; justify-content:space-between; gap:18px; font-size:8pt;">
    <div style="flex:1; text-align:center; max-width:48%;">
      <div style="height:36px; border-bottom:1px solid #07364F; margin:10px 0 4px;"></div>
      <div style="font-weight:800;">For: Vice Chancellor</div>
      <div style="color:#6B5A48;">{{ \App\Models\Setting::getValue('university_name','University of Dodoma') }}<br>17 September 2026</div>
    </div>
    <div style="flex:1; text-align:center; max-width:48%;">
      <div style="height:36px; border-bottom:1px solid #07364F; margin:10px 0 4px;"></div>
      <div style="font-weight:800;">Registrar — Academic</div>
      <div style="color:#6B5A48;">17 September 2026</div>
    </div>
  </div>
@endif

<div style="margin-top:14px; border-top:1px dashed #E4D7C2; padding-top:6px; font-size:7pt; color:#6B5A48; text-align:center;">
  System-generated Application Form &middot; {{ $application->application_number ?? 'DRAFT' }} &middot; Verify at {{ url('/verify-admission') }} &middot; Generated: {{ now()->format('d F Y H:i') }}
</div>
</body>
</html>