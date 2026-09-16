@extends('layouts.admin')
@section('title','Payment')
@section('content')
<div class="page-head">
    <div><h1 class="mono">{{ $payment->payment_reference }}</h1><p class="page-sub">Payment details and verification.</p></div>
    <div class="page-actions">
        <a href="{{ route('admin.payments.index') }}" class="btn btn-ghost">← Back to Payments</a>
        <span class="tag {{ $payment->status==='CONFIRMED' ? 'tag-green' : ($payment->status==='FAILED' ? 'tag-red' : 'tag-gold') }}">{{ $payment->status }}</span>
    </div>
</div>

<div class="panel">
    <div class="panel-head"><div class="panel-title">Payment Information</div><span class="tag {{ $payment->status==='CONFIRMED' ? 'tag-green' : 'tag-gold' }}">{{ $payment->status }}</span></div>
    <div class="panel-body">
        <div class="kv">
            <div class="kv-row"><span class="k">Reference</span><span class="v mono">{{ $payment->payment_reference }}</span></div>
            <div class="kv-row"><span class="k">Applicant</span><span class="v">{{ $payment->application->applicant->fullName() }} <span class="muted">({{ $payment->application->applicant->phone }})</span></span></div>
            <div class="kv-row"><span class="k">Application</span><span class="v mono">{{ $payment->application->application_number ?? '#'.$payment->application->id }}</span></div>
            <div class="kv-row"><span class="k">Amount</span><span class="v"><strong>{{ $payment->currency }} {{ number_format($payment->amount) }}</strong></span></div>
            <div class="kv-row"><span class="k">Paid</span><span class="v">{{ $payment->amount_paid ? $payment->currency.' '.number_format($payment->amount_paid) : '—' }}</span></div>
            <div class="kv-row"><span class="k">Method</span><span class="v">{{ $payment->payment_method ?? '—' }}</span></div>
            <div class="kv-row"><span class="k">Status</span><span class="v"><span class="tag {{ $payment->status==='CONFIRMED' ? 'tag-green' : ($payment->status==='FAILED' ? 'tag-red' : 'tag-gold') }}">{{ $payment->status }}</span></span></div>
            <div class="kv-row"><span class="k">Control No</span><span class="v mono">{{ $payment->control_number ?? '—' }}</span></div>
            <div class="kv-row"><span class="k">Receipt</span><span class="v mono">{{ $payment->receipt_number ?? '—' }}</span></div>
        </div>

        @if($payment->status==='PENDING')
            <div class="form-actions" style="justify-content:flex-start">
                <form method="POST" action="{{ route('admin.payments.verify', encId($payment->id)) }}" onsubmit="event.preventDefault(); const _f=this; confirmModal('Verify payment','Are you sure you want to confirm this payment?',()=>_f.submit())">@csrf<input type="hidden" name="action" value="confirm"><button type="submit" class="btn btn-primary" style="background:var(--acacia-600)">✓ Confirm Payment</button></form>
                <form method="POST" action="{{ route('admin.payments.verify', encId($payment->id)) }}" onsubmit="event.preventDefault(); const _f=this; confirmModal('Mark payment failed','Are you sure you want to mark this payment as failed?',()=>_f.submit())">@csrf<input type="hidden" name="action" value="reject"><button type="submit" class="btn btn-danger">Mark Failed</button></form>
            </div>
        @endif
    </div>
</div>
@endsection
