@extends('layouts.applicant')
@section('title','Result')
@section('content')
<div class="page-head">
    <div>
        <h1>Admission Result</h1>
        <p class="page-sub">{{ $application->application_number ?? 'Draft #'.$application->id }} · {{ $application->admissionWindow->admissionLevel->name }}</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('applicant.results') }}" class="btn btn-ghost btn-sm">← Back to results</a>
    </div>
</div>

<div class="panel">
    <div class="panel-head"><div class="panel-title">Selection Results</div></div>
    <div class="panel-body">
        @if($application->selectionResults->count())
            <div style="display:flex;flex-direction:column;gap:10px;">
                @foreach($application->selectionResults as $sr)
                    <div class="panel" style="margin:0;box-shadow:none;border:1px solid var(--line);">
                        <div class="panel-body" style="display:flex;justify-content:space-between;gap:12px;align-items:center;flex-wrap:wrap;padding:14px 16px;">
                            <div>
                                <div style="font-weight:700;font-size:13.5px;color:var(--coffee-900);">{{ $sr->programme->name }} <span style="color:var(--ink-soft);font-weight:600;">({{ $sr->programme->code }})</span></div>
                                <div class="field-hint">Batch: {{ $sr->selectionBatch->name }} · Score: {{ $sr->rank_score }}</div>
                            </div>
                            @php $tag = match($sr->status){ 'SELECTED'=>'tag-green','WAITLISTED'=>'tag-gold','REJECTED'=>'tag-red', default=>'tag-grey'}; @endphp
                            <span class="tag {{ $tag }}">{{ $sr->status }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div style="padding:14px 16px;border-radius:10px;background:var(--gold-100);border:1px solid #e9d4a0;color:#8a6418;font-size:13px;line-height:1.6;">No selection result yet. Please wait for the selection result after reviewing.</div>
        @endif
    </div>
</div>

@if(in_array($application->status, ['SELECTED','ADMITTED']) && ! $application->acceptance_confirmed)
    @php
        $mySelections = $application->applicant->applications()->whereIn('status',['SELECTED','ADMITTED'])->count();
        $needCode = $mySelections > 1;
        $phone = $application->applicant->phone ?? auth()->user()->phone;
    @endphp
    <div class="panel" style="margin-top:16px;border:1.5px solid var(--gold-500);">
        <div class="panel-head" style="background:var(--gold-100);border-bottom:1px solid #e9d4a0;">
            <div class="panel-title" style="color:#8a6418;display:flex;align-items:center;gap:8px;"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg> Confirm Your Acceptance</div>
            @if($needCode)<span class="tag tag-gold">SMS verification required</span>@else<span class="tag tag-green">Single selection</span>@endif
        </div>
        <div class="panel-body">
            @if($needCode)
                <p style="font-size:13.5px;line-height:1.6;color:var(--coffee-800);margin:0;">You have been selected for <strong>{{ $mySelections }} programmes</strong>. To secure this offer, request a <strong>confirmation code by SMS</strong> to {{ $phone ? substr($phone,0,4).'****'.substr($phone,-2) : 'your phone' }} and enter it below.</p>
                @if(! $phone)
                    <p class="field-err" style="margin-top:8px;">No phone number on file — update it in your <a href="{{ route('applicant.profile') }}" style="font-weight:700;text-decoration:underline;">profile</a> first.</p>
                @endif
                <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end;margin-top:14px;">
                    <form method="POST" action="{{ route('applicant.application.confirm.send-code', encId($application->id)) }}" onsubmit="event.preventDefault(); const _f=this; confirmModal('Send confirmation code','A 6-digit code will be sent to {{ $phone ? substr($phone,0,4).'****'.substr($phone,-2) : 'your phone' }} by SMS.',()=>_f.submit())">
                        @csrf
                        <button class="btn btn-primary btn-sm" title="Send 6-digit confirmation code to the phone number on file"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex:none"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg> Send Confirmation Code via SMS</button>
                    </form>
                    <form method="POST" action="{{ route('applicant.application.confirm.code', encId($application->id)) }}" class="field" style="margin:0;display:flex;gap:8px;align-items:flex-end;">
                        @csrf
                        <div>
                            <label class="field-label">Enter 6-digit code *</label>
                            <input name="code" type="text" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" required placeholder="000000" style="width:130px;letter-spacing:.25em;font-weight:800;text-align:center;">
                        </div>
                        <button class="btn btn-ghost btn-sm" style="border-color:var(--gold-500);color:#8a6418;">Confirm by Entering Code</button>
                    </form>
                </div>
            @else
                <p style="font-size:13.5px;line-height:1.6;color:var(--coffee-800);margin:0;">You have a single selection. Confirm your acceptance directly to secure your place.</p>
                <div style="margin-top:14px;">
                    <form method="POST" action="{{ route('applicant.application.confirm.direct', encId($application->id)) }}" onsubmit="event.preventDefault(); const _f=this; confirmModal('Confirm acceptance','Confirm that you accept this admission offer?',()=>_f.submit())">
                        @csrf
                        <button class="btn btn-primary btn-sm" style="background:var(--acacia-600)"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex:none"><polyline points="20 6 9 17 4 12"/></svg> Confirm Acceptance</button>
                    </form>
                </div>
            @endif
        </div>
    </div>
@elseif(in_array($application->status, ['SELECTED','ADMITTED']) && $application->acceptance_confirmed)
    <div class="panel" style="margin-top:16px;border:1.5px solid var(--acacia-600);">
        <div class="panel-head" style="background:var(--acacia-100);border-bottom:1px solid #c8d7a8;">
            <div class="panel-title" style="color:var(--acacia-600);display:flex;align-items:center;gap:8px;"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg> Acceptance Confirmed</div>
            <span class="tag tag-green">Verified</span>
        </div>
        <div class="panel-body">
            <p style="font-size:13.5px;line-height:1.6;color:var(--coffee-800);margin:0;">You confirmed your acceptance on <strong>{{ $application->acceptance_confirmed_at?->format('d M Y, H:i') }}</strong>. Your place is secured — download your admission letter and joining instructions below.</p>
        </div>
    </div>
@endif

@if($application->admissionLetters->count())
    <div class="panel" style="margin-top:16px;">
        <div class="panel-head"><div class="panel-title">Admission Letters</div><span class="tag tag-green">{{ $application->admissionLetters->count() }} issued</span></div>
        <div class="panel-body" style="display:flex;flex-direction:column;gap:10px;">
            @foreach($application->admissionLetters as $letter)
                <div style="display:flex;justify-content:space-between;gap:12px;align-items:center;padding:14px 16px;border:1px solid var(--line);border-radius:12px;background:var(--sand-50);flex-wrap:wrap;">
                    <div>
                        <div class="mono" style="font-weight:800;font-size:13.5px;color:var(--coffee-900);">{{ $letter->letter_number }}</div>
                        <div class="field-hint">Issued {{ $letter->issued_at?->format('d M Y') ?? $letter->created_at->format('d M Y') }}</div>
                    </div>
                    <div style="display:flex;gap:8px;flex-wrap:wrap;">
                        <a href="{{ route('applicant.application.letter', encId($application->id)) }}" class="btn btn-primary btn-sm">Preview Letter</a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@elseif(in_array($application->status, ['SELECTED','ADMITTED']))
    <div class="panel" style="margin-top:16px;border:1.5px solid var(--acacia-600);">
        <div class="panel-head" style="background:var(--acacia-100);border-bottom:1px solid #c8d7a8;">
            <div class="panel-title" style="color:var(--acacia-600);display:flex;align-items:center;gap:8px;"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg> Admission Letter Ready</div>
            <span class="tag tag-green">Selected</span>
        </div>
        <div class="panel-body">
            <p style="font-size:13.5px;line-height:1.6;color:var(--coffee-800);margin:0;">Congratulations! You have been <strong>selected</strong>. Your admission letter is ready for preview and download. Please also review the joining instructions.</p>
            <div style="margin-top:14px;display:flex;gap:10px;flex-wrap:wrap;">
                <a href="{{ route('applicant.application.letter', encId($application->id)) }}" class="btn btn-primary btn-sm" style="background:var(--acacia-600)"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex:none"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg> Preview Admission Letter</a>
                <a href="{{ route('applicant.application.joining', encId($application->id)) }}" class="btn btn-ghost btn-sm" style="border-color:var(--acacia-600);color:var(--acacia-600)"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex:none"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg> Joining Instructions</a>
            </div>
        </div>
    </div>
@endif
{{-- Joining Instructions — independent document like Admission Letter --}}
@if(in_array($application->status, ['SELECTED','ADMITTED']))
    @php $jiProgramme = $application->selectedProgrammes->first()?->programme ?? $application->selectionResults->first()?->programme; @endphp
    <div class="panel" style="margin-top:16px;">
        <div class="panel-head"><div class="panel-title" style="display:flex;align-items:center;gap:8px;"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg> Joining Instructions</div><span class="tag tag-gold">1 document</span></div>
        <div class="panel-body">
            <div style="display:flex;justify-content:space-between;gap:12px;align-items:center;padding:14px 16px;border:1px solid var(--line);border-radius:12px;background:var(--sand-50);flex-wrap:wrap;">
                <div>
                    <div class="mono" style="font-weight:800;font-size:13.5px;color:var(--coffee-900);">{{ \App\Models\Setting::getValue('university_acronym','UDOM') }}/JI/{{ $application->application_number }}/{{ $application->academicYear->name }}</div>
                    <div class="field-hint">For {{ $jiProgramme?->name ?? $application->admissionWindow->admissionLevel->name }} ({{ $jiProgramme?->code ?? '—' }}) · Issued {{ now()->format('d M Y') }}</div>
                    <div class="field-hint" style="margin-top:4px;">Includes: Documents, Fees, Reporting, Accommodation, Health &amp; Conduct</div>
                </div>
                <div style="display:flex;gap:8px;flex-wrap:wrap;">
                    <a href="{{ route('applicant.application.joining', encId($application->id)) }}" class="btn btn-primary btn-sm"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex:none"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg> Preview Joining</a>
                </div>
            </div>
            <p style="margin-top:12px;font-size:12.5px;color:var(--ink-soft);line-height:1.6;">This is an <strong>independent document</strong> from your admission letter. Please print both and bring them on reporting day.</p>
        </div>
    </div>
@endif
@endsection
