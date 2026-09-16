<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdmissionWindow;
use App\Models\Application;
use App\Models\Payment;
use App\Models\Programme;
use App\Models\AcademicYear;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'applications'      => Application::count(),
            'submitted'         => Application::where('status', 'SUBMITTED')->count(),
            'under_review'      => Application::where('status', 'UNDER_REVIEW')->count(),
            'selected'          => Application::whereIn('status', ['SELECTED', 'ADMITTED'])->count(),
            'payments_confirmed'=> Payment::where('status', 'CONFIRMED')->count(),
            'revenue'           => (float) Payment::where('status', 'CONFIRMED')->sum('amount_paid'),
            'programmes'        => Programme::count(),
            'open_windows'      => AdmissionWindow::where('status', 'active')->where('closes_at', '>', now())->count(),
        ];

        $recentApplications = Application::with(['applicant', 'admissionWindow.admissionLevel'])
            ->latest()->limit(8)->get();

        $windowStats = AdmissionWindow::with(['admissionLevel', 'academicYear'])
            ->withCount('applications')
            ->latest()->limit(6)->get();

        $levelStats = Programme::selectRaw('admission_level_id, count(*) as total')
            ->groupBy('admission_level_id')
            ->with('admissionLevel')
            ->get();

        return view('admin.dashboard', compact('stats', 'recentApplications', 'windowStats', 'levelStats'));
    }
}