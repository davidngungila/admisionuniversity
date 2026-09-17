<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
@page { margin: 20mm 15mm 20mm 25mm; }
body { font-family: DejaVu Sans, sans-serif; font-size: 10pt; line-height: 1.6; color: #07364F; border-left: 3px solid #C2592B; padding-left: 12px; }
.header { text-align: center; border-bottom: 3px double #07364F; padding-bottom: 12px; }
.logo { width: 50px; height: 50px; background: #C2592B; color: #fff; display: inline-block; text-align: center; line-height: 50px; font-weight: 800; border-radius: 10px; }
.meta { margin-top: 12px; font-size: 9pt; }
.meta table { width: 100%; }
.details { margin-top: 14px; border: 1px solid #E4D7C2; border-radius: 8px; overflow: hidden; }
.details table { width: 100%; border-collapse: collapse; font-size: 9.5pt; }
.details td { padding: 7px 10px; border-bottom: 1px solid #E4D7C2; }
.details tr:last-child td { border-bottom: none; }
.k { color: #6B5A48; font-weight: 600; width: 38%; background: #FBF7EF; }
.v { color: #07364F; font-weight: 700; }
.tag { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 8pt; font-weight: 700; }
</style>
</head>
<body>
@php
  $applicant = $application->applicant;
  $fullName = trim($applicant->first_name.' '.$applicant->middle_name.' '.$applicant->last_name);
  $programme = $application->selectedProgrammes->first()?->programme ?? $application->selectionResults->first()?->programme;
@endphp
<div class="header">
  @php $pdfLogo2 = \App\Models\Setting::getValue('university_logo'); @endphp
  @if($pdfLogo2)
    <img src="{{ public_path($pdfLogo2) }}" style="width:50px;height:50px;object-fit:contain;border-radius:10px;background:#fff;padding:3px;border:1px solid #E4D7C2;" alt="Logo">
  @else
    <div class="logo">{{ substr(\App\Models\Setting::getValue('university_acronym','UDOM'),0,1) }}</div>
  @endif
  <div style="font-weight:800; font-size:14pt; margin-top:6px;">{{ strtoupper(\App\Models\Setting::getValue('university_name','University of Dodoma')) }}</div>
  <div style="font-size:8pt; letter-spacing:1px; color:#6B5A48;">{{ \App\Models\Setting::getValue('university_name_sw','Chuo Kikuu Cha Dodoma') }}</div>
  <div style="font-size:7.5pt; color:#6B5A48; margin-top:4px;">{{ \App\Models\Setting::getValue('contact_box','P.O. Box 259') }}, {{ \App\Models\Setting::getValue('contact_city','Dodoma, Tanzania') }} &middot; Tel: {{ \App\Models\Setting::getValue('admissions_phone','+255 26 231 0300') }} &middot; {{ \App\Models\Setting::getValue('admissions_email','admissions@university.ac.tz') }}</div>
</div>
<div class="meta">
  <table><tr>
    <td>Ref No: <strong style="color:#C2592B;">{{ $letter->letter_number }}</strong><br>Application No: <strong>{{ $application->application_number }}</strong></td>
    <td style="text-align:right;">Date: {{ $letter->issued_at?->format('d F Y') ?? $letter->created_at->format('d F Y') }}<br>Academic Year: {{ $application->academicYear->name }}</td>
  </tr></table>
</div>
<div style="margin-top:14px;">
  <div style="font-weight:700;">{{ $fullName }}</div>
  <div style="font-size:9pt; color:#6B5A48;">{{ $applicant->phone }} &middot; {{ $applicant->email }}</div>
  <div style="margin-top:8px; font-weight:700;">Dear {{ $fullName }},</div>
  <div style="text-align:center; margin-top:8px; font-weight:800; font-size:10pt; border:1px solid #E4D7C2; background:#FBF7EF; padding:6px; border-radius:6px;">RE: ADMISSION TO {{ strtoupper(\App\Models\Setting::getValue('university_name','University of Dodoma')) }} &mdash; {{ $application->academicYear->name }}</div>
  <p style="text-align:justify; margin-top:10px;">We are pleased to inform you that you have been <strong>selected</strong> to join the {{ \App\Models\Setting::getValue('university_name','University of Dodoma') }} for the <strong>{{ $application->academicYear->name }}</strong> academic year as detailed below. Congratulations and welcome to the {{ \App\Models\Setting::getValue('university_acronym','UDOM') }} family!</p>
  <div class="details">
    <div style="background:#07364F; color:#fff; padding:7px 10px; font-weight:700; font-size:9pt; letter-spacing:0.5px;">ADMISSION DETAILS</div>
    <table>
      <tr><td class="k">Full Name</td><td class="v">{{ $fullName }} ({{ $applicant->gender }})</td></tr>
      <tr><td class="k">Date of Birth / NIDA</td><td>{{ $applicant->date_of_birth?->format('d M Y') ?? '—' }} &middot; {{ $applicant->nida_number ?? '—' }}</td></tr>
      <tr><td class="k">Citizenship</td><td>{{ $applicant->citizenship->name ?? 'Tanzanian' }}</td></tr>
      <tr><td class="k">Application Number</td><td style="color:#C2592B; font-weight:800;">{{ $application->application_number }}</td></tr>
      <tr><td class="k">Selected Programme</td><td><strong>{{ $programme?->name ?? '—' }}</strong> ({{ $programme?->code ?? '—' }})</td></tr>
      <tr><td class="k">Campus / Level</td><td>{{ $programme?->campus->name ?? 'Main Campus' }} &middot; {{ $application->admissionWindow->admissionLevel->name }}</td></tr>
      <tr><td class="k">Duration / Mode</td><td>{{ $programme?->duration_years ?? '—' }} Years &middot; {{ $programme?->study_mode ?? 'Full Time' }}</td></tr>
      <tr><td class="k">Reporting Date</td><td><strong>{{ $letter->issued_at?->addDays(14)->format('d F Y') ?? now()->addDays(14)->format('d F Y') }} 08:00 AM</strong></td></tr>
    </table>
  </div>
  <div style="margin-top:10px; background:#E2E7D4; border:1px solid #c8d7a8; padding:8px 10px; border-radius:6px; font-size:9pt; color:#5E6E3F;"><strong>Letter:</strong> {{ $letter->content }}</div>
  <p style="margin-top:10px;">You are required to confirm your admission and report with originals and copies of this letter, academic certificates, birth certificate, NIDA/passport and four passport photos.</p>
  @php $letterSignatories = \App\Models\Signatory::where('is_active',1)->orderBy('order_index')->get(); @endphp
  @if($letterSignatories->count())
    <div style="margin-top:16px; display:flex; justify-content:space-between; gap:18px; font-size:9pt;">
      @foreach($letterSignatories as $sig)
        <div style="flex:1; text-align:center; max-width:48%;">
          @if($sig->signature_image && file_exists(public_path($sig->signature_image)))
            <img src="{{ public_path($sig->signature_image) }}" style="height:42px; object-fit:contain; margin:4px auto 2px;" alt="Signature">
          @else
            <div style="height:42px; border-bottom:1px solid #07364F; margin:14px 0 4px;"></div>
          @endif
          <div style="font-weight:800;">For: {{ $sig->title ? $sig->title.' ' : '' }}{{ $sig->name }}</div>
          <div style="font-size:8pt; color:#6B5A48;">{{ $sig->designation ?? '' }}<br>{{ $letter->issued_at?->format('d F Y') ?? $letter->created_at->format('d F Y') }}</div>
        </div>
      @endforeach
    </div>
  @else
    <div style="margin-top:16px; display:flex; justify-content:space-between; font-size:9pt;">
      <div>Wishing you success.<br><strong>For: Vice Chancellor</strong><br>{{ \App\Models\Setting::getValue('university_name','University of Dodoma') }}</div>
      <div style="text-align:center;"><div style="width:100px; height:1px; background:#07364F; margin:24px auto 4px;"></div>Registrar &mdash; Academic<br>{{ $letter->issued_at?->format('d F Y') ?? $letter->created_at->format('d F Y') }}</div>
    </div>
  @endif
  <div style="margin-top:16px; border-top:1px dashed #E4D7C2; padding-top:6px; font-size:7.5pt; color:#6B5A48;">System-generated valid without signature when verified at {{ url('/verify-admission') }} &middot; Ref: {{ $letter->letter_number }} &middot; {{ $application->application_number }}</div>
</div>
</body>
</html>
