@extends('layouts.applicant')
@section('title','Payment — '.$application->admissionWindow->admissionLevel->name)
@section('content')
<div class="page-head">
    <div>
        <h1>Payment — Application Fee</h1>
        <p class="page-sub">Step {{ $currentStep->step_number }} of {{ $steps->count() }} · Complete payment to proceed</p>
    </div>
    <div class="page-actions">
        <span class="tag tag-blue">{{ $progress['completed'] }} of {{ $progress['total'] }} — {{ $progress['percent'] }}%</span>
    </div>
</div>

<div class="panel" style="margin-bottom:16px;">
    <div class="panel-body" style="padding:14px 18px;">
        <div style="height:6px;background:var(--sand-100);border:1px solid var(--line);border-radius:20px;overflow:hidden;">
            <div style="height:100%;background:var(--terracotta-600);width:{{ $progress['percent'] }}%"></div>
        </div>
        <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:12px;">
            @foreach($steps as $st)
                @php $done = $application->stepCompletions()->where('workflow_step_id',$st->id)->whereNotNull('completed_at')->exists(); $isCurrent = $st->id===$currentStep->id; @endphp
                <span class="tag {{ $done ? 'tag-green' : ($isCurrent ? 'tag-terracotta' : 'tag-grey') }}" style="display:inline-flex;align-items:center;gap:6px;">
                    <span style="width:16px;height:16px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-size:9px;font-weight:800;background:{{ $done ? 'var(--acacia-600)' : ($isCurrent ? 'var(--terracotta-600)' : 'var(--sand-200)') }};color:{{ $done || $isCurrent ? '#fff' : 'var(--coffee-700)' }};">{{ $done ? '✓' : $st->step_number }}</span>
                    {{ $st->step_name }}
                </span>
            @endforeach
        </div>
    </div>
</div>

@if($payment)
    <div class="panel" style="margin-bottom:16px;border-color:{{ $payment->status==='CONFIRMED' ? '#c8d7a8' : ($payment->status==='FREE' ? '#b6c8f0' : '#f0d9a0') }};background:{{ $payment->status==='CONFIRMED' ? 'var(--acacia-100)' : ($payment->status==='FREE' ? '#e8f0fe' : 'var(--gold-100)') }};">
        <div class="panel-body">
            <div style="display:flex;justify-content:space-between;gap:12px;flex-wrap:wrap;align-items:center;">
                <div>
                    <div style="font-weight:800;font-size:13px;color:var(--coffee-900);">Existing Payment — {{ $payment->payment_reference }}</div>
                    <div style="font-size:13px;margin-top:4px;color:var(--coffee-800);">Amount: <span style="font-weight:800;">{{ $payment->currency }} {{ number_format($payment->amount) }}</span> · Status: <span class="tag {{ $payment->status==='CONFIRMED' ? 'tag-green' : ($payment->status==='FREE' ? 'tag-blue' : 'tag-gold') }}">{{ $payment->status }}</span></div>
                    @if($payment->control_number)
                        <div style="font-size:13px;margin-top:6px;">Control Number: <span class="mono" style="font-weight:800;color:var(--coffee-900);background:#fff;border:1px solid var(--line);padding:3px 8px;border-radius:8px;">{{ $payment->control_number }}</span> <span class="field-hint">(use for mobile/bank payment)</span></div>
                    @endif
                </div>
            </div>
            @if($payment->status==='PENDING')
                <div style="margin-top:12px;padding:10px 14px;border-radius:10px;background:#fff;border:1px solid #f0d9a0;color:#8a6418;font-size:12.5px;line-height:1.5;">Your payment is pending verification. An admin will confirm it. You may still continue — finance verification can happen in parallel, but eligibility/selection requires a confirmed payment.</div>
            @endif
        </div>
    </div>
@endif

<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
    <div class="panel">
        <div class="panel-head"><div class="panel-title">Admission Window</div><span class="tag tag-grey">{{ $window->timezone }}</span></div>
        <div class="panel-body">
            <div style="font-weight:700;font-size:14px;color:var(--coffee-900);line-height:1.3;">{{ $window->admissionLevel->name }} — Round {{ $window->applicationRound->round_number }} ({{ $window->academicYear->name }})</div>
            <div style="font-size:13px;color:var(--ink-soft);margin-top:4px;">{{ $window->applicant_category }} · {{ $window->timezone }}</div>
            <div class="field-hint" style="margin-top:6px;">{{ $window->opens_at->format('d M Y H:i') }} → {{ $window->closes_at->format('d M Y H:i') }}</div>
            <div class="kv" style="margin-top:16px;">
                <div class="kv-row">
                    <span class="k">Application Fee</span>
                    <span class="v" style="font-size:18px;color:{{ $window->application_fee <= 0 ? 'var(--acacia-600)' : 'var(--terracotta-600)' }};">{{ $window->feeLabel() }}</span>
                </div>
            </div>
            @if($window->application_fee <= 0)
                <div style="margin-top:12px;padding:10px 14px;border-radius:10px;background:var(--acacia-100);border:1px solid #c8d7a8;color:var(--acacia-600);font-size:12.5px;line-height:1.5;">This window is FREE for {{ $window->applicant_category }} applicants. No payment is required; the step will be auto-confirmed.</div>
            @else
                <p style="margin-top:12px;font-size:12.5px;line-height:1.6;color:var(--ink-soft);">A control number will be generated. Pay via mobile money or bank using that control number, then the finance team will verify your payment.</p>
            @endif
        </div>
    </div>

    <div class="panel">
        <div class="panel-head"><div class="panel-title">{{ $window->application_fee <= 0 ? 'Confirm Application' : 'Confirm Payment Method' }}</div></div>
        <div class="panel-body">
            <form method="POST" action="{{ route('applicant.application.save', [encId($application->id), $currentStep->route]) }}" style="display:flex;flex-direction:column;gap:14px;" onsubmit="event.preventDefault(); const _f=this; confirmModal('Save payment','Are you sure you want to save this payment method and continue?',()=>_f.submit())">
                @csrf
                @if($window->application_fee > 0)
                    <div class="field @error('payment_method') err @enderror">
                        <label class="field-label">Payment Method *</label>
                        <select name="payment_method" required>
                            <option value="">— Select —</option>
                            <option value="Mobile Money" @selected(old('payment_method')=='Mobile Money')>Mobile Money (M-Pesa / Tigo Pesa / Airtel Money)</option>
                            <option value="Bank" @selected(old('payment_method')=='Bank')>Bank Transfer</option>
                            <option value="Cash" @selected(old('payment_method')=='Cash')>Cash at Finance Office</option>
                        </select>
                        @error('payment_method')<span class="field-err">{{ $message }}</span>@enderror
                    </div>
                    <div class="field @error('phone_number') err @enderror">
                        <label class="field-label">Phone Number (for Mobile Money)</label>
                        <input name="phone_number" value="{{ old('phone_number', $application->applicant->phone) }}" placeholder="+2557xxxxxxxx">
                        <span class="field-hint">Required if paying via mobile money</span>
                        @error('phone_number')<span class="field-err">{{ $message }}</span>@enderror
                    </div>
                @else
                    <div style="padding:10px 14px;border-radius:10px;background:var(--acacia-100);border:1px solid #c8d7a8;color:var(--acacia-600);font-size:12.5px;line-height:1.5;">No payment method is required — this application is <strong>FREE</strong>. Click confirm to continue.</div>
                @endif
                <button class="btn btn-primary" style="width:100%;justify-content:center;">{{ $window->application_fee <= 0 ? 'Confirm — Fee is FREE' : 'Generate Control Number & Continue →' }}</button>
                <p class="field-hint" style="text-align:center;">By continuing you confirm the information is correct.</p>
            </form>
        </div>
    </div>
</div>
<style>@media(max-width:900px){ div[style*="grid-template-columns:1fr 1fr"]{grid-template-columns:1fr !important} }</style>
@endsection
