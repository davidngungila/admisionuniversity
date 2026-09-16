<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentAdminController extends Controller
{
    public function index(Request $request)
    {
        $q = Payment::with(['application.applicant'])->latest();
        if ($request->filled('status')) $q->where('status', $request->status);

        return view('admin.payments.index', ['payments'=>$q->paginate(20)->withQueryString()]);
    }

    public function show(Payment $payment)
    {
        $payment->load(['application.applicant','application.admissionWindow']);
        return view('admin.payments.show', compact('payment'));
    }

    public function verify(Request $request, Payment $payment)
    {
        $request->validate(['action'=>['required','in:confirm,reject']]);

        if ($request->action === 'confirm') {
            $payment->update([
                'status'      => Payment::STATUS_CONFIRMED,
                'verified_by' => auth()->id(),
                'verified_at' => now(),
                'paid_at'     => $payment->paid_at ?? now(),
                'amount_paid' => $payment->amount,
                'receipt_number' => 'RCP-'.date('Ymd').'-'.strtoupper(bin2hex(random_bytes(3))),
            ]);

            if ($payment->application->status === Application::STATUS_PAYMENT_PENDING) {
                $payment->application->advanceStatus(Application::STATUS_PAYMENT_CONFIRMED, auth()->id(), 'Payment confirmed by finance.');
            }
            return back()->with('success','Payment confirmed.');
        }

        $payment->update(['status'=>Payment::STATUS_FAILED, 'verified_by'=>auth()->id(), 'verified_at'=>now()]);
        return back()->with('success','Payment marked as failed.');
    }
}