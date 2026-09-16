@extends('layouts.app')
@section('title','Verify Admission')
@section('content')
@php
    $uniName = \App\Models\Setting::getValue('university_name','University of Dodoma');
    $uniAcro = \App\Models\Setting::getValue('university_acronym','UDOM');
    $logo    = \App\Models\Setting::getValue('university_logo');
@endphp
<div style="max-width:1000px;margin:0 auto;padding:24px 24px 48px">
    <div class="page-head">
        <div>
            <h1>Verify Admission</h1>
            <p class="page-sub">Enter an application number to verify the admission record online.</p>
        </div>
        <div class="page-actions"><span class="tag tag-terracotta"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:12px;height:12px"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg> Secure official verification</span></div>
    </div>

    <div class="panel" style="max-width:720px;margin:0 auto;background:linear-gradient(135deg,var(--white),var(--sand-50));">
        <div class="panel-head">
            <div class="panel-title">Check Application Number</div>
            <span class="tag tag-blue">Public</span>
        </div>
        <div class="panel-body">
            <form method="GET">
                <div class="form-grid" style="grid-template-columns:1fr auto;align-items:end;">
                    <div class="field">
                        <label class="field-label">Application Number</label>
                        <input name="application_number" value="{{ request('application_number') }}" placeholder="UNI-2627-R02-BSC-000125" style="font-family:monospace;" required>
                        <span class="field-hint">e.g. UNI-2627-R02-BSC-000125</span>
                    </div>
                    <div class="field">
                        <label class="field-label">&nbsp;</label>
                        <button class="btn btn-primary"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:15px;height:15px"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg> Verify</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if(request()->filled('application_number'))
        @if($result && $result->applicant)
            <div class="panel" style="margin-top:22px;overflow:hidden;border:1px solid #c8d7a8;">
                <div style="position:relative;padding:26px 28px 18px;background:linear-gradient(180deg,var(--sand-100),var(--white));text-align:center;border-bottom:2px solid var(--gold-500);">
                    <div style="display:flex;align-items:center;justify-content:center;gap:14px;">
                        <div class="p-mark" style="width:52px;height:52px;border-radius:12px;background:linear-gradient(155deg,var(--terracotta-600),var(--gold-500));color:#fff;font-weight:800;display:flex;align-items:center;justify-content:center;overflow:hidden;@if($logo)background:#fff;@endif">@if($logo)<img src="{{ asset($logo) }}" style="width:100%;height:100%;object-fit:contain;border-radius:12px;" alt="Logo">@else {{ substr($uniAcro,0,1) }} @endif</div>
                        <div style="text-align:left;">
                            <div style="font-weight:800;font-size:16px;letter-spacing:.02em;color:var(--coffee-900);">{{ $uniName }}</div>
                            <div style="font-size:11px;letter-spacing:.14em;text-transform:uppercase;color:var(--terracotta-600);font-weight:800;">Online Admission Verification</div>
                        </div>
                    </div>
                    <div style="margin-top:14px;display:inline-flex;align-items:center;gap:8px;background:var(--acacia-100);color:var(--acacia-600);border:1px solid #c8d7a8;padding:6px 14px;border-radius:24px;font-weight:800;font-size:12px;letter-spacing:.06em;text-transform:uppercase;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px"><path d="M20 6L9 17l-5-5"/></svg> Record Verified Authentic
                    </div>
                    <div style="position:absolute;top:14px;right:14px;transform:rotate(9deg);border:2.5px solid var(--acacia-600);color:var(--acacia-600);font-weight:800;letter-spacing:.12em;font-size:12px;padding:5px 12px;border-radius:8px;opacity:.85;">VERIFIED</div>
                </div>

                <div class="panel-body" style="padding:22px 28px;">
                    <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;background:var(--sand-50);border:1px solid var(--line);border-radius:12px;padding:14px 16px;margin-bottom:8px;">
                        <div>
                            <div class="cell-sub" style="font-size:11px;letter-spacing:.1em;text-transform:uppercase;font-weight:700;">Application Number</div>
                            <div style="font-family:monospace;font-weight:800;font-size:18px;color:var(--coffee-900);margin-top:2px;">{{ $result->application_number }}</div>
                        </div>
                        <div style="text-align:right;">
                            <div class="cell-sub" style="font-size:11px;letter-spacing:.1em;text-transform:uppercase;font-weight:700;">Current Status</div>
                            @php
                                $ok = in_array($result->status, ['SELECTED','ADMITTED','SUBMITTED','PAYMENT_CONFIRMED']);
                                $bad = in_array($result->status, ['REJECTED','INELIGIBLE','WITHDRAWN','PAYMENT_FAILED']);
                            @endphp
                            <span class="tag {{ $ok ? 'tag-green' : ($bad ? 'tag-red' : 'tag-gold') }}" style="margin-top:3px;font-size:12px;">{{ $result->status }}</span>
                        </div>
                    </div>

                    <div class="grid-2 v-grid2" style="margin-top:16px;grid-template-columns:repeat(2,1fr)">
                        <div>
                            <div class="panel-title" style="font-size:13px;margin-bottom:10px;">Applicant Details</div>
                            <div class="kv">
                                <div class="kv-row"><span class="k">Full Name</span><span class="v" style="font-weight:700;">{{ $result->applicant->fullName() }}</span></div>
                                <div class="kv-row"><span class="k">Date of Birth</span><span class="v">{{ $result->applicant->date_of_birth?->format('d M Y') ?? '—' }}</span></div>
                                <div class="kv-row"><span class="k">Gender</span><span class="v">{{ $result->applicant->gender ?? '—' }}</span></div>
                                <div class="kv-row"><span class="k">Citizenship</span><span class="v">{{ $result->applicant->citizenship?->name ?? '—' }}</span></div>
                                <div class="kv-row"><span class="k">NIDA No.</span><span class="v mono">{{ $result->applicant->nida_number ?? '—' }}</span></div>
                                <div class="kv-row"><span class="k">Phone</span><span class="v">{{ $result->applicant->phone ?? '—' }}</span></div>
                                <div class="kv-row"><span class="k">Email</span><span class="v" style="font-size:12.5px">{{ $result->applicant->email ?? ($result->applicant->user->email ?? '—') }}</span></div>
                            </div>
                        </div>
                        <div>
                            <div class="panel-title" style="font-size:13px;margin-bottom:10px;">Admission Application</div>
                            <div class="kv">
                                <div class="kv-row"><span class="k">Academic Year</span><span class="v">{{ $result->admissionWindow?->academicYear?->name ?? '—' }}</span></div>
                                <div class="kv-row"><span class="k">Admission Level</span><span class="v">{{ $result->admissionWindow?->admissionLevel?->name ?? '—' }}</span></div>
                                <div class="kv-row"><span class="k">Round</span><span class="v">Round {{ $result->admissionWindow?->applicationRound?->round_number ?? '—' }}</span></div>
                                <div class="kv-row"><span class="k">Applicant Category</span><span class="v">{{ $result->admissionWindow?->applicant_category ?? '—' }}</span></div>
                                <div class="kv-row"><span class="k">Window</span><span class="v" style="font-size:12px">{{ $result->admissionWindow?->opens_at?->format('d M Y') ?? '—' }} → {{ $result->admissionWindow?->closes_at?->format('d M Y') ?? '—' }}</span></div>
                                <div class="kv-row"><span class="k">Submitted</span><span class="v">{{ $result->submitted_at?->format('d M Y H:i') ?? '—' }}</span></div>
                            </div>
                        </div>
                    </div>

                    <div class="panel-title" style="font-size:13px;margin:18px 0 10px;">Programme Selection</div>
                    @if($result->selectedProgrammes->count())
                        @foreach($result->selectedProgrammes as $i => $sp)
                            <div style="display:flex;align-items:center;gap:12px;padding:10px 14px;border:1px solid var(--line);border-radius:10px;margin-bottom:8px;background:var(--sand-50);">
                                <span class="tag {{ $i===0 ? 'tag-terracotta' : 'tag-grey' }}">{{ $i+1 }}</span>
                                <div style="flex:1;">
                                    <div style="font-weight:700;font-size:13.5px;color:var(--coffee-900);">{{ $sp->programme?->name ?? '—' }}</div>
                                    <div class="cell-sub">{{ $sp->programme?->code }} · {{ $sp->programme?->admissionLevel?->name }} · {{ optional($sp->programme?->campus)->name }}</div>
                                </div>
                                <span class="tag {{ $i===0 ? 'tag-green' : 'tag-grey' }}">{{ $sp->status ?? ($i===0 ? 'Primary' : 'Alternative') }}</span>
                            </div>
                        @endforeach
                    @else
                        <div class="cell-sub">No programme selection recorded.</div>
                    @endif

                    @if($result->selectionResults->count())
                        <div class="panel-title" style="font-size:13px;margin:18px 0 10px;">Selection Outcome</div>
                        @foreach($result->selectionResults as $sr)
                            <div style="display:flex;align-items:center;gap:12px;padding:12px 14px;border:1px solid #c8d7a8;border-radius:10px;margin-bottom:8px;background:var(--acacia-100);">
                                <div class="thumb thumb-green" style="flex:none;"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px"><path d="M20 6L9 17l-5-5"/></svg></div>
                                <div style="flex:1;">
                                    <div style="font-weight:800;color:var(--acacia-600);">{{ $sr->status }}</div>
                                    <div style="font-size:13px;color:var(--coffee-800);">{{ $sr->programme?->name }}</div>
                                    <div class="cell-sub">{{ $sr->selectionBatch?->name ?? 'Selection' }} · position {{ $sr->position ?? '—' }} · score {{ $sr->rank_score ?? '—' }}</div>
                                </div>
                                @if($sr->remarks)<span class="tag tag-gold">{{ $sr->remarks }}</span>@endif
                            </div>
                        @endforeach
                    @endif

                    @if($result->admissionLetters->count())
                        <div class="panel-title" style="font-size:13px;margin:18px 0 10px;">Admission Letter</div>
                        @foreach($result->admissionLetters as $letter)
                            <div style="display:flex;align-items:center;gap:12px;padding:12px 14px;border:1px solid #c8d7a8;border-radius:10px;background:var(--acacia-100);">
                                <div class="thumb thumb-green" style="flex:none;"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px"><path d="M22 4H2v16h20z"/><path d="M22 4L12 13 2 4"/></svg></div>
                                <div style="flex:1;">
                                    <div style="font-weight:800;color:var(--acacia-600);">Admission letter issued</div>
                                    <div class="cell-sub" style="color:var(--coffee-800);">Letter No: <strong class="mono">{{ $letter->letter_number }}</strong></div>
                                </div>
                                <span class="tag tag-green">ISSUED {{ $letter->issued_at?->format('d M Y') }}</span>
                            </div>
                        @endforeach
                    @endif

                    @if($result->payments->count())
                        <div class="panel-title" style="font-size:13px;margin:18px 0 10px;">Fee & Payment</div>
                        @foreach($result->payments as $pay)
                            <div style="display:flex;align-items:center;gap:12px;padding:12px 14px;border:1px solid var(--line);border-radius:10px;background:var(--sand-50);">
                                <div style="flex:1;">
                                    <div style="font-weight:700;font-size:13.5px;color:var(--coffee-900);">{{ $pay->payment_reference }}</div>
                                    <div class="cell-sub">Amount: <strong>{{ $pay->currency }} {{ number_format((float)$pay->amount) }}</strong> · {{ $pay->payment_method ?? '—' }} @if($pay->control_number) · Control: <span class="mono">{{ $pay->control_number }}</span> @endif</div>
                                </div>
                                <span class="tag {{ $pay->status==='CONFIRMED' ? 'tag-green' : ($pay->status==='FREE' ? 'tag-blue' : 'tag-gold') }}">{{ $pay->status }}</span>
                            </div>
                        @endforeach
                    @endif

                    @if($result->statusHistory->count())
                        <div class="panel-title" style="font-size:13px;margin:18px 0 10px;">Status Timeline</div>
                        <div style="display:flex;flex-direction:column;gap:0;">
                            @foreach($result->statusHistory->sortByDesc('created_at') as $h)
                                <div style="display:flex;gap:12px;position:relative;padding-bottom:12px;">
                                    <div style="display:flex;flex-direction:column;align-items:center;">
                                        <div style="width:12px;height:12px;border-radius:50%;background:var(--terracotta-600);border:2px solid var(--gold-500);"></div>
                                        @if(!$loop->last)<div style="width:2px;flex:1;background:var(--line);"></div>@endif
                                    </div>
                                    <div style="flex:1;padding-top:0;padding-bottom:6px;">
                                        <div style="font-weight:700;font-size:12.5px;color:var(--coffee-900);">{{ $h->new_status }} <span class="cell-sub">on {{ $h->created_at->format('d M Y H:i') }}</span></div>
                                        <div class="cell-sub" style="font-size:11.5px">{{ $h->remarks ?? ($h->old_status ? 'Changed from '.$h->old_status : 'Status updated') }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div style="padding:16px 28px;border-top:1px solid var(--line);background:var(--sand-50);display:flex;justify-content:space-between;gap:12px;flex-wrap:wrap;align-items:center;">
                    <div class="cell-sub" style="font-size:11.5px;">Certificate generated electronically on <strong>{{ now()->format('d M Y H:i:s') }}</strong> by the {{ $uniName }} Online Admission System.</div>
                    <div style="display:flex;gap:10px;align-items:center;">
                        <span class="tag tag-green">✔ AUTHENTIC</span>
                        <a href="javascript:window.print()" class="btn btn-ghost btn-sm"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg> Print</a>
                    </div>
                </div>
            </div>
        @else
            <div class="panel" style="margin-top:22px;max-width:720px;margin-left:auto;margin-right:auto;border-color:#e8b4b0;background:var(--danger-100);">
                <div class="panel-body" style="display:flex;gap:14px;align-items:center;padding:22px;">
                    <div class="thumb" style="background:#fff;border:1px solid #e8b4b0;color:var(--danger);"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:20px;height:20px"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg></div>
                    <div>
                        <div style="font-weight:800;color:var(--danger);">Application not found</div>
                        <p style="margin:4px 0 0;font-size:13px;color:var(--coffee-800);line-height:1.6;">No admission record matches the number <strong class="mono">{{ request('application_number') }}</strong>. Please re-check the number or contact {{ \App\Models\Setting::getValue('admissions_email','admissions@udom.ac.tz') }}.</p>
                    </div>
                </div>
            </div>
        @endif
    @else
        <div class="panel" style="margin-top:22px;max-width:720px;margin-left:auto;margin-right:auto;">
            <div class="panel-body" style="text-align:center;padding:28px;">
                <div class="stat-icon ic-green" style="margin:0 auto 10px;"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M12 8v4M12 16h.01"/></svg></div>
                <div style="font-weight:800;color:var(--coffee-900);">How it works</div>
                <p style="font-size:13px;color:var(--ink-soft);max-width:520px;margin:8px auto 0;line-height:1.6;">Enter the application number printed on the admission letter to instantly verify the applicant's record, admission status and selection outcome with {{ $uniAcro }}.</p>
            </div>
        </div>
    @endif
</div>
<style>@media(max-width:700px){ .v-grid2{grid-template-columns:1fr !important;} }</style>
@endsection