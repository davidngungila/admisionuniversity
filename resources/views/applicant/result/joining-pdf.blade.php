<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
@page { margin: 20mm 15mm 20mm 25mm; }
body { font-family: DejaVu Sans, sans-serif; font-size: 9.5pt; line-height: 1.6; color: #07364F; border-left: 3px solid #5E6E3F; padding-left: 12px; }
.header { text-align: center; border-bottom: 3px double #07364F; padding-bottom: 10px; }
.logo { width: 50px; height: 50px; background: #C2592B; color: #fff; display: inline-block; text-align: center; line-height: 50px; font-weight: 800; border-radius: 10px; }
.card { border: 1px solid #E4D7C2; border-radius: 8px; padding: 10px; background: #FBF7EF; margin-top: 10px; }
</style>
</head>
<body>
@php
  $fullName = trim($application->applicant->first_name.' '.$application->applicant->middle_name.' '.$application->applicant->last_name);
  $reporting = $letter?->issued_at?->copy()->addDays(14)->format('d F Y') ?? now()->addDays(14)->format('d F Y');
@endphp
<div class="header">
  @php $pdfLogo = \App\Models\Setting::getValue('university_logo'); @endphp
  @if($pdfLogo)
    <img src="{{ public_path($pdfLogo) }}" style="width:50px;height:50px;object-fit:contain;border-radius:10px;background:#fff;padding:3px;border:1px solid #E4D7C2;" alt="Logo">
  @else
    <div class="logo">{{ substr(\App\Models\Setting::getValue('university_acronym','UDOM'),0,1) }}</div>
  @endif
  <div style="font-weight:800; font-size:13pt; margin-top:4px;">{{ strtoupper(\App\Models\Setting::getValue('university_name','University of Dodoma')) }}</div>
  <div style="font-size:7pt; letter-spacing:0.8px; color:#6B5A48;">{{ \App\Models\Setting::getValue('university_name','University') }} &middot; {{ \App\Models\Setting::getValue('admissions_office','Directorate of Undergraduate Studies') }}</div>
  <div style="margin-top:6px; background:#07364F; color:#E8B82F; display:inline-block; padding:4px 12px; border-radius:12px; font-size:7pt; font-weight:800;">JOINING INSTRUCTIONS &mdash; {{ $application->academicYear->name }}</div>
</div>
<div style="margin-top:10px; font-size:9pt;">
  <table width="100%"><tr>
    <td>Ref: <strong style="color:#C2592B;">{{ $letter?->letter_number ?? \App\Models\Setting::getValue('university_acronym','UDOM').'/JI/'.$application->application_number }}</strong></td>
    <td align="right">{{ $letter?->issued_at?->format('d F Y') ?? now()->format('d F Y') }}</td>
  </tr></table>
</div>
<div style="margin-top:10px;">
  <div style="font-weight:700;">{{ $fullName }}</div>
  <div style="font-size:8.5pt; color:#6B5A48;">{{ $programme?->name ?? $application->admissionWindow->admissionLevel->name }} ({{ $programme?->code ?? '—' }})</div>
  <p style="text-align:justify;">Congratulations on your selection to join <strong>{{ $programme?->name ?? $application->admissionWindow->admissionLevel->name }}</strong> at <strong>{{ $programme?->campus->name ?? 'Main Campus' }}</strong> for <strong>{{ $application->academicYear->name }}</strong>. Read these instructions carefully.</p>
</div>
<div class="card">
  <strong>1. Documents to Bring</strong>
  <ul style="margin:4px 0 0 14px;">
    <li>Original admission letter (this letter)</li>
    <li>O-Level &amp; A-Level / Diploma / Degree certificates</li>
    <li>Birth certificate, NIDA / Passport</li>
    <li>4 passport photos &amp; Medical examination form (dully filled)</li>
    <li>Proof of payment (control number receipt)</li>
  </ul>
</div>
<div class="card">
  <strong>2. Fees &amp; Payments</strong><br>
  Tuition: <strong>TZS {{ $programme ? number_format($programme->tuition_fee) : '1,200,000' }}</strong> per year &middot; Pay via control number at NMB/CRDB/TCB &middot; Keep receipt &middot; NHIF required
</div>
<div class="card">
  <strong>3. Reporting &amp; Registration</strong><br>
  Report to <strong>{{ $programme?->department->faculty->name ?? 'College' }} &mdash; {{ $programme?->campus->name ?? 'Main Campus' }}</strong> on <strong>{{ $reporting }} 08:00 AM</strong>. Orientation follows. Late reporting requires written permission.
</div>
<div class="card">
  <strong>4. Accommodation, Health &amp; Conduct</strong><br>
  Hostel via {{ \App\Models\Setting::getValue('university_acronym','UDOM') }} accommodation portal &middot; Adhere to {{ \App\Models\Setting::getValue('university_acronym','UDOM') }} rules &middot; Dress code as per handbook &middot; Medical exam at Health Centre
</div>
<div style="margin-top:12px; background:#E4F0FA; border:1px solid #E4D7C2; padding:8px; border-radius:6px; font-size:8.5pt; color:#C2592B;"><strong>Note:</strong> Failure to report on time may lead to forfeiture. Contact {{ \App\Models\Setting::getValue('admissions_email') ?? 'admissions@university.ac.tz' }} &middot; {{ \App\Models\Setting::getValue('admissions_phone','+255 26 231 0300') }}</div>
  @php $joinSignatories = \App\Models\Signatory::where('is_active',1)->orderBy('order_index')->get(); @endphp
  @if($joinSignatories->count())
    <div style="margin-top:14px; display:flex; justify-content:space-between; gap:18px; font-size:8.5pt;">
      @foreach($joinSignatories as $sig)
        <div style="flex:1; text-align:center; max-width:48%;">
          @if($sig->signature_image && file_exists(public_path($sig->signature_image)))
            <img src="{{ public_path($sig->signature_image) }}" style="height:38px; object-fit:contain; margin:4px auto 2px;" alt="Signature">
          @else
            <div style="height:38px; border-bottom:1px solid #07364F; margin:10px 0 4px;"></div>
          @endif
          <div style="font-weight:800;">{{ $sig->title ? $sig->title.' ' : '' }}{{ $sig->name }}</div>
          <div style="color:#6B5A48;">{{ $sig->designation ?? '' }}</div>
        </div>
      @endforeach
    </div>
  @endif
  <div style="margin-top:14px; font-size:8pt; color:#6B5A48; border-top:1px dashed #E4D7C2; padding-top:6px;">Ref: {{ $letter?->letter_number ?? \App\Models\Setting::getValue('university_acronym','UDOM').'/JI/'.$application->application_number }} &middot; {{ $application->application_number }} &middot; Verify at {{ url('/verify-admission') }}</div>
</body>
</html>
