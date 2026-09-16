<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Payment;
use App\Models\Programme;
use App\Models\AdmissionWindow;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        $summary = [
            'applications'        => Application::count(),
            'applications_active' => Application::whereNotIn('status', [Application::STATUS_REJECTED, Application::STATUS_INELIGIBLE, Application::STATUS_WITHDRAWN])->count(),
            'applications_submitted' => Application::whereNotIn('status', [Application::STATUS_DRAFT, Application::STATUS_PAYMENT_PENDING, Application::STATUS_APPLICATION_IN_PROGRESS, Application::STATUS_PAYMENT_FAILED])->count(),
            'selected'            => Application::whereIn('status', [Application::STATUS_SELECTED, Application::STATUS_ADMITTED])->count(),
            'admitted'            => Application::where('status', Application::STATUS_ADMITTED)->count(),
            'payments'            => Payment::count(),
            'payments_confirmed'  => Payment::where('status', Payment::STATUS_CONFIRMED)->count(),
            'payments_pending'    => Payment::where('status', Payment::STATUS_PENDING)->count(),
            'payments_free'       => Payment::where('status', Payment::STATUS_FREE)->count(),
            'revenue'             => (float) Payment::where('status', Payment::STATUS_CONFIRMED)->sum('amount_paid'),
            'issued_control'      => Payment::whereNotNull('control_number')->where('status', '!=', Payment::STATUS_CONFIRMED)->count(),
            'programmes'          => Programme::count(),
            'windows'             => AdmissionWindow::count(),
            'users'               => \App\Models\User::count(),
            'applicants'          => \App\Models\Applicant::count(),
            'logins'              => \App\Models\AuditLog::where('action', 'New Login (Successful)')->count(),
            'logins_today'        => \App\Models\AuditLog::where('action', 'New Login (Successful)')->whereDate('created_at', today())->count(),
            'logins_failed'       => \App\Models\AuditLog::where('action', 'Login Failed')->count(),
            'logins_failed_today' => \App\Models\AuditLog::where('action', 'Login Failed')->whereDate('created_at', today())->count(),
            'logins_unique'       => \App\Models\AuditLog::where('action', 'New Login (Successful)')->whereNotNull('user_id')->distinct()->count('user_id'),
        ];

        $recentLogins = \App\Models\AuditLog::with('user')
            ->whereIn('action', ['New Login (Successful)', 'Login Failed'])
            ->latest()->limit(15)->get();

        return view('admin.reports.index', ['s' => $summary, 'recentLogins' => $recentLogins]);
    }

    public function applications(Request $request)
    {
        $q = Application::with(['applicant','admissionWindow.admissionLevel','academicYear'])->latest();
        if ($request->filled('window')) $q->where('admission_window_id',$request->window);
        if ($request->filled('status')) $q->where('status',$request->status);
        return view('admin.reports.applications', ['applications'=>$q->paginate(30)->withQueryString(), 'windows'=>AdmissionWindow::with('admissionLevel')->get()]);
    }

    public function payments(Request $request)
    {
        $q = Payment::with(['application.applicant'])->latest();
        if ($request->filled('status')) $q->where('status',$request->status);
        return view('admin.reports.payments', ['payments'=>$q->paginate(30)->withQueryString(), 'total'=>(float)Payment::where('status','CONFIRMED')->sum('amount_paid')]);
    }

    public function programmes()
    {
        $programmes = Programme::withCount('applicationProgrammes')->with(['admissionLevel','campus'])->get();
        return view('admin.reports.programmes', compact('programmes'));
    }

    public function statistics()
    {
        $byStatus = Application::selectRaw('status,count(*) as total')->groupBy('status')->pluck('total','status');
        $byLevel  = Application::join('admission_windows','applications.admission_window_id','=','admission_windows.id')
                    ->join('admission_levels','admission_windows.admission_level_id','=','admission_levels.id')
                    ->selectRaw('admission_levels.name as level, count(*) as total')
                    ->groupBy('admission_levels.name')->get();
        $revenue  = (float) Payment::where('status','CONFIRMED')->sum('amount_paid');
        return view('admin.reports.statistics', compact('byStatus','byLevel','revenue'));
    }
}