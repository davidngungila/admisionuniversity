<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
@page { margin: 15mm 12mm 15mm 12mm; }
body { font-family: DejaVu Sans, sans-serif; font-size: 8.5pt; line-height: 1.5; color: #07364F; }
.header { text-align: center; border-bottom: 3px double #07364F; padding-bottom: 10px; }
.logo { width: 48px; height: 48px; background: #C2592B; color: #fff; display: inline-block; text-align: center; line-height: 48px; font-weight: 800; border-radius: 10px; font-size: 16pt; }
.meta { margin-top: 10px; font-size: 8pt; color: #07364F; }
.meta table { width: 100%; }
.details { margin-top: 10px; border: 1px solid #E4D7C2; border-radius: 8px; overflow: hidden; }
.details-head { background: #07364F; color: #fff; padding: 6px 10px; font-weight: 700; font-size: 8.5pt; letter-spacing: 0.5px; }
.k { color: #6B5A48; font-weight: 600; width: 28%; background: #FBF7EF; padding: 6px 8px; border-bottom: 1px solid #E4D7C2; font-size: 8pt; }
.v { color: #07364F; font-weight: 700; padding: 6px 8px; border-bottom: 1px solid #E4D7C2; font-size: 8.5pt; }
.results-table { width: 100%; border-collapse: collapse; font-size: 7.5pt; margin-top: 10px; }
.results-table th { background: #07364F; color: #fff; padding: 6px 6px; text-align: left; font-weight: 700; font-size: 7.5pt; }
.results-table td { padding: 5px 6px; border-bottom: 1px solid #E4D7C2; }
.results-table tr:nth-child(even) td { background: #FBF7EF; }
.tag { display: inline-block; padding: 1px 6px; border-radius: 8px; font-size: 7pt; font-weight: 700; }
.tag-green { background: #E1EFEA; color: #2E7D6B; }
.tag-gold { background: #F9EFD2; color: #8a6418; }
.tag-red { background: #F6DCDA; color: #B33A3A; }
.tag-grey { background: #DCE5EE; color: #40637A; }
</style>
</head>
<body>
@php
  $applicantCount = $batch->results->count();
@endphp
<div class="header">
  @php $pdfLogo = \App\Models\Setting::getValue('university_logo'); @endphp
  @if($pdfLogo)
    <img src="{{ public_path($pdfLogo) }}" style="width:48px;height:48px;object-fit:contain;border-radius:10px;background:#fff;padding:3px;border:1px solid #E4D7C2;" alt="Logo">
  @else
    <div class="logo">{{ substr(\App\Models\Setting::getValue('university_acronym','UDOM'),0,1) }}</div>
  @endif
  <div style="font-weight:800; font-size:13pt; margin-top:6px;">{{ strtoupper(\App\Models\Setting::getValue('university_name','University of Dodoma')) }}</div>
  <div style="font-size:7pt; letter-spacing:1px; color:#6B5A48;">Chuo Kikuu Cha Dodoma</div>
  <div style="font-size:7pt; color:#6B5A48; margin-top:4px;">{{ \App\Models\Setting::getValue('contact_box','P.O. Box 259') }}, {{ \App\Models\Setting::getValue('contact_city','Dodoma, Tanzania') }} &middot; Tel: {{ \App\Models\Setting::getValue('admissions_phone','+255 26 231 0300') }} &middot; {{ \App\Models\Setting::getValue('admissions_email','admissions@university.ac.tz') }}</div>
</div>

<div class="meta">
  <table><tr>
    <td>Ref No: <strong style="color:#C2592B;">SEL/{{ $batch->id }}/{{ \Carbon\Carbon::parse($batch->created_at)->format('Y') }}</strong><br>Batch: <strong>{{ $batch->name }}</strong></td>
    <td style="text-align:right;">Date: {{ \Carbon\Carbon::parse($batch->created_at)->format('d F Y') }}<br>Academic Year: {{ $batch->academicYear->name }} &middot; Round {{ $batch->applicationRound->round_number }}</td>
  </tr></table>
</div>

<div style="text-align:center; margin-top:10px; font-weight:800; font-size:10pt; border:1px solid #E4D7C2; background:#FBF7EF; padding:6px; border-radius:6px;">
  SELECTION BATCH REPORT &mdash; {{ strtoupper($batch->name) }}
</div>

<div class="details">
  <div class="details-head">BATCH OVERVIEW</div>
  <table width="100%" cellpadding="0" cellspacing="0">
    <tr><td class="k">Batch Name</td><td class="v">{{ $batch->name }}</td></tr>
    <tr><td class="k">Academic Year</td><td class="v">{{ $batch->academicYear->name }}</td></tr>
    <tr><td class="k">Application Round</td><td class="v">Round {{ $batch->applicationRound->round_number }} — {{ $batch->applicationRound->name }}</td></tr>
    <tr><td class="k">Admission Level</td><td class="v">{{ $batch->admissionLevel->name ?? 'All Levels' }} @if($batch->admissionLevel) ({{ $batch->admissionLevel->code }}) @endif</td></tr>
    <tr><td class="k">Status</td><td class="v"><span class="tag {{ $batch->status==='PROCESSED' ? 'tag-green' : 'tag-gold' }}">{{ $batch->status }}</span> @if($batch->processed_at) &middot; Processed: {{ \Carbon\Carbon::parse($batch->processed_at)->format('d F Y H:i') }} @endif</td></tr>
    <tr><td class="k">Total Selected</td><td class="v">{{ $applicantCount }} applicant{{ $applicantCount===1 ? '' : 's' }}</td></tr>
    <tr><td class="k">Generated</td><td class="v">{{ now()->format('d F Y H:i') }} by {{ $batch->createdByUser->name ?? 'System' }}</td></tr>
  </table>
</div>

<div style="margin-top:12px;">
  <div style="font-weight:800; font-size:9pt; color:#07364F; margin-bottom:6px;">SELECTED APPLICANTS ({{ $applicantCount }})</div>
  <table class="results-table">
    <thead>
      <tr>
        <th style="width:28px; text-align:center;">#</th>
        <th>Applicant Name</th>
        <th>Application No</th>
        <th>Programme</th>
        <th style="width:55px; text-align:center;">Score</th>
        <th style="width:75px; text-align:center;">Status</th>
      </tr>
    </thead>
    <tbody>
      @forelse($batch->results->sortBy(fn($r) => $r->application->applicant->fullName()) as $idx => $r)
        @php
          $applicant = $r->application->applicant;
          $fullName = $applicant->fullName();
        @endphp
        <tr>
          <td style="text-align:center; color:#6B5A48;">{{ $loop->iteration }}</td>
          <td><strong>{{ $fullName }}</strong><br><span style="color:#6B5A48; font-size:7pt;">{{ $applicant->phone }} &middot; {{ $applicant->email }}</span></td>
          <td style="font-family: DejaVu Sans Mono, monospace; font-size:7pt; color:#C2592B; font-weight:700;">{{ $r->application->application_number }}</td>
          <td><strong style="font-size:7.5pt;">{{ $r->programme->name }}</strong><br><span style="color:#6B5A48; font-size:6.5pt;">{{ $r->programme->code }} &middot; {{ $r->programme->campus->name ?? 'Main Campus' }}</span></td>
          <td style="text-align:center; font-weight:800;">{{ number_format($r->rank_score,2) }}</td>
          <td style="text-align:center;"><span class="tag {{ $r->status==='SELECTED' ? 'tag-green' : ($r->status==='REJECTED' ? 'tag-red' : ($r->status==='WAITLISTED' ? 'tag-gold' : 'tag-grey')) }}">{{ $r->status }}</span></td>
        </tr>
      @empty
        <tr><td colspan="6" style="text-align:center; padding:14px; color:#6B5A48;">No selection results in this batch.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>

@php $batchSignatories = \App\Models\Signatory::where('is_active',1)->orderBy('order_index')->get(); @endphp
@if($batchSignatories->count())
  <div style="margin-top:18px; display:flex; justify-content:space-between; gap:18px; font-size:8.5pt;">
    @foreach($batchSignatories as $sig)
      <div style="flex:1; text-align:center; max-width:48%;">
        @if($sig->signature_image && file_exists(public_path($sig->signature_image)))
          <img src="{{ public_path($sig->signature_image) }}" style="height:36px; object-fit:contain; margin:4px auto 2px;" alt="Signature">
        @else
          <div style="height:36px; border-bottom:1px solid #07364F; margin:10px 0 4px;"></div>
        @endif
        <div style="font-weight:800;">{{ $sig->title ? $sig->title.' ' : '' }}{{ $sig->name }}</div>
        <div style="font-size:7pt; color:#6B5A48;">{{ $sig->designation ?? '' }}<br>{{ now()->format('d F Y') }}</div>
      </div>
    @endforeach
  </div>
@else
  <div style="margin-top:18px; display:flex; justify-content:space-between; font-size:8.5pt; color:#07364F;">
    <div>Prepared by: Admissions Office<br><strong>{{ \App\Models\Setting::getValue('university_name','University of Dodoma') }}</strong></div>
    <div style="text-align:center;"><div style="width:110px; height:1px; background:#07364F; margin:22px auto 4px;"></div>Registrar &mdash; Academic<br>{{ now()->format('d F Y') }}</div>
  </div>
@endif

<div style="margin-top:14px; border-top:1px dashed #E4D7C2; padding-top:6px; font-size:7pt; color:#6B5A48; text-align:center;">
  System-generated Selection Batch Report &middot; Ref: SEL/{{ $batch->id }}/{{ \Carbon\Carbon::parse($batch->created_at)->format('Y') }} &middot; Verify at {{ url('/verify-admission') }} &middot; {{ $applicantCount }} record{{ $applicantCount===1 ? '' : 's' }}
</div>
</body>
</html>