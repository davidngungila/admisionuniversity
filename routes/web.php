<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Applicant\DashboardController as ApplicantDashboard;
use App\Http\Controllers\Applicant\ApplicationController as ApplicantApplication;
use App\Http\Controllers\Applicant\StatusController as ApplicantStatus;
use App\Http\Controllers\Applicant\ProfileController as ApplicantProfile;
use App\Http\Controllers\Applicant\ResultController as ApplicantResult;
use App\Http\Controllers\Applicant\SmsConfirmationController as ApplicantSmsConfirm;
use App\Http\Controllers\Applicant\CalendarController as ApplicantCalendar;
use App\Http\Controllers\Applicant\HistoryController as ApplicantHistory;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\AcademicYearController;
use App\Http\Controllers\Admin\ApplicationRoundController;
use App\Http\Controllers\Admin\AdmissionWindowController;
use App\Http\Controllers\Admin\CampusController;
use App\Http\Controllers\Admin\FacultyController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\ProgrammeController as AdminProgramme;
use App\Http\Controllers\Admin\WorkflowController;
use App\Http\Controllers\Admin\ApplicantController as AdminApplicant;
use App\Http\Controllers\Admin\ApplicationManageController;
use App\Http\Controllers\Admin\PaymentAdminController;
use App\Http\Controllers\Admin\DocumentAdminController;
use App\Http\Controllers\Admin\SelectionController;
use App\Http\Controllers\Admin\UserController as AdminUser;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SmsLogController;
use App\Http\Controllers\Admin\SignatoryController;
use App\Http\Controllers\Admin\SupportOfficerController;

// ── Public ──────────────────────────────────────────────────────────
Route::get('/', [PublicController::class, 'home'])->name('home');
Route::get('/admission-calendar', [PublicController::class, 'calendar'])->name('public.calendar');
Route::get('/programmes', [PublicController::class, 'programmes'])->name('public.programmes');
Route::get('/programmes/{programme}', [PublicController::class, 'programmeShow'])->name('public.programmes.show');
Route::get('/requirements', [PublicController::class, 'requirements'])->name('public.requirements');
Route::get('/fees', [PublicController::class, 'fees'])->name('public.fees');
Route::get('/guidelines', [PublicController::class, 'guidelines'])->name('public.guidelines');
Route::get('/contact', [PublicController::class, 'contact'])->name('public.contact');
Route::get('/verify-admission', [PublicController::class, 'verify'])->name('public.verify');

// ── Auth (guest) ────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/register/fetch-name', [AuthController::class, 'fetchName'])->name('register.fetch-name');
});

Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// ── Applicant (auth) ────────────────────────────────────────────────
Route::middleware(['auth', 'active'])->prefix('applicant')->name('applicant.')->group(function () {

    Route::get('/dashboard', [ApplicantDashboard::class, 'index'])->name('dashboard');

    // Start application for a window
    Route::post('/apply/{window}', [ApplicantApplication::class, 'start'])->name('application.start');

    // Application workflow (dynamic steps)
Route::get('/applications/{application}', [ApplicantApplication::class, 'show'])->name('application.show');
    Route::get('/applications/{application}/step/{route}', [ApplicantApplication::class, 'step'])->name('application.step');
    Route::post('/applications/{application}/step/{route}', [ApplicantApplication::class, 'save'])->name('application.save');
    Route::post('/applications/{application}/results/fetch', [ApplicantApplication::class, 'fetchResult'])->name('application.results.fetch');

    // Status / summary / history
    Route::get('/applications/{application}/status', [ApplicantStatus::class, 'show'])->name('application.status');
    Route::get('/applications/{application}/summary', [ApplicantStatus::class, 'summary'])->name('application.summary');
    Route::get('/applications/{application}/form', [ApplicantStatus::class, 'form'])->name('application.form');
    Route::get('/history', [ApplicantHistory::class, 'index'])->name('history');
    Route::get('/calendar', [ApplicantCalendar::class, 'index'])->name('calendar');

    // Profile & password
    Route::get('/profile', [ApplicantProfile::class, 'show'])->name('profile');
    Route::put('/profile', [ApplicantProfile::class, 'update'])->name('profile.update');
    Route::put('/password', [ApplicantProfile::class, 'updatePassword'])->name('password.update');

    // Results & letters
    Route::get('/results', [ApplicantResult::class, 'index'])->name('results');
    Route::get('/results/{application}', [ApplicantResult::class, 'show'])->name('result.show');
    Route::get('/letters/{letter}/download', [ApplicantResult::class, 'downloadLetter'])->name('letter.download');
Route::get('/applications/{application}/letter', [ApplicantResult::class, 'letter'])->name('application.letter');
    Route::get('/applications/{application}/joining', [ApplicantResult::class, 'joining'])->name('application.joining');

    // SMS confirmation of selection acceptance
    Route::post('/applications/{application}/sms/send-code', [ApplicantSmsConfirm::class, 'sendCode'])->name('application.confirm.send-code');
    Route::post('/applications/{application}/sms/confirm', [ApplicantSmsConfirm::class, 'confirm'])->name('application.confirm.code');
    Route::post('/applications/{application}/sms/confirm-direct', [ApplicantSmsConfirm::class, 'confirmDirect'])->name('application.confirm.direct');
});

// ── Admin (auth + role) ─────────────────────────────────────────────
Route::middleware(['auth', 'active', 'role:super_admin,admin,staff'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/', [AdminDashboard::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [AdminDashboard::class, 'index']);

    // Academic years
    Route::resource('academic-years', AcademicYearController::class)->except(['show']);
    Route::post('academic-years/{academicYear}/activate', [AcademicYearController::class, 'activate'])->name('academic-years.activate');

    // Rounds
    Route::resource('rounds', ApplicationRoundController::class)->except(['show']);

    // Windows
    Route::resource('windows', AdmissionWindowController::class)->except(['show']);
    Route::post('windows/{window}/toggle', [AdmissionWindowController::class, 'toggle'])->name('windows.toggle');

    // Workflow steps
    Route::get('workflow', [WorkflowController::class, 'index'])->name('workflow.index');
    Route::get('workflow/create', [WorkflowController::class, 'create'])->name('workflow.create');
    Route::post('workflow', [WorkflowController::class, 'store'])->name('workflow.store');
    Route::get('workflow/{workflow}/edit', [WorkflowController::class, 'edit'])->name('workflow.edit');
    Route::put('workflow/{workflow}', [WorkflowController::class, 'update'])->name('workflow.update');
    Route::delete('workflow/{workflow}', [WorkflowController::class, 'destroy'])->name('workflow.destroy');

    // Academic management
    Route::resource('campuses', CampusController::class)->except(['show']);
    Route::resource('faculties', FacultyController::class)->except(['show']);
    Route::resource('departments', DepartmentController::class)->except(['show']);
    Route::resource('programmes', AdminProgramme::class);
    Route::post('programmes/{programme}/requirements', function (\Illuminate\Http\Request $r, \App\Models\Programme $programme) {
        $d = $r->validate(['subject'=>['nullable','string','max:191'],'minimum_grade'=>['nullable','string','max:10'],'requirement_text'=>['nullable','string']]);
        if (empty($d['subject']) && empty($d['requirement_text'])) return back()->with('error','Provide subject or requirement text.');
        $programme->requirements()->create($d);
        return back()->with('success','Requirement added.');
    })->name('programmes.requirements.store');

    Route::post('programmes/{programme}/courses', function (\Illuminate\Http\Request $r, \App\Models\Programme $programme) {
        $d = $r->validate([
            'year'           => ['required','integer','min:1','max:10'],
            'semester'       => ['required','in:1,2'],
            'course_code'    => ['nullable','string','max:30'],
            'course_name'    => ['required','string','max:191'],
            'credit_hours'   => ['nullable','numeric','min:0','max:99'],
        ]);
        $programme->courses()->create($d);
        return back()->with('success','Course added to curriculum.');
    })->name('programmes.courses.store');

    Route::delete('courses/{course}', function (\App\Models\ProgrammeCourse $course) {
        $course->delete();
        return back()->with('success','Course removed from curriculum.');
    })->name('courses.destroy');

    // Applicants & applications
    Route::get('applicants', [AdminApplicant::class, 'index'])->name('applicants.index');
    Route::get('applicants/{applicant}', [AdminApplicant::class, 'show'])->name('applicants.show');

    Route::get('applications', [ApplicationManageController::class, 'index'])->name('applications.index');
    Route::get('applications/{application}', [ApplicationManageController::class, 'show'])->name('applications.show');
    Route::patch('applications/{application}/status', [ApplicationManageController::class, 'updateStatus'])->name('applications.status');
    Route::delete('applications/{application}', [ApplicationManageController::class, 'destroy'])->name('applications.destroy');

    // Finance — payments
    Route::get('payments', [PaymentAdminController::class, 'index'])->name('payments.index');
    Route::get('payments/{payment}', [PaymentAdminController::class, 'show'])->name('payments.show');
    Route::post('payments/{payment}/verify', [PaymentAdminController::class, 'verify'])->name('payments.verify');

    // Documents
    Route::get('documents', [DocumentAdminController::class, 'index'])->name('documents.index');
    Route::get('documents/{document}/preview', [DocumentAdminController::class, 'preview'])->name('documents.preview');
    Route::post('documents/{document}/verify', [DocumentAdminController::class, 'verify'])->name('documents.verify');

    // Selection
    Route::get('selection/batches', [SelectionController::class, 'batches'])->name('selection.batches');
    Route::get('selection/batches/create', [SelectionController::class, 'createBatch'])->name('selection.create');
    Route::post('selection/batches', [SelectionController::class, 'storeBatch'])->name('selection.store');
    Route::get('selection/batches/{batch}', [SelectionController::class, 'batchShow'])->name('selection.show');
    Route::get('selection/batches/{batch}/pdf', [SelectionController::class, 'batchPdf'])->name('selection.pdf');
    Route::post('selection/batches/{batch}/run', [SelectionController::class, 'run'])->name('selection.run');
    Route::patch('selection/results/{result}', [SelectionController::class, 'updateResult'])->name('selection.result.update');

// Users
    Route::resource('users', AdminUser::class)->except(['show']);

    // Signatories (university officers signing admission documents)
    Route::resource('signatories', SignatoryController::class)->except(['show']);

    // Support officers (public helpline panel)
    Route::resource('support-officers', SupportOfficerController::class)->except(['show']);

    // Reports
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/applications', [ReportController::class, 'applications'])->name('reports.applications');
    Route::get('reports/payments', [ReportController::class, 'payments'])->name('reports.payments');
    Route::get('reports/programmes', [ReportController::class, 'programmes'])->name('reports.programmes');
    Route::get('reports/statistics', [ReportController::class, 'statistics'])->name('reports.statistics');

    // Settings
Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('settings', [SettingController::class, 'update'])->name('settings.update');
    Route::post('settings/sms-test', [SettingController::class, 'smsTest'])->name('settings.sms-test');
    Route::delete('settings/{setting}', [SettingController::class, 'destroy'])->name('settings.destroy');

// Audit logs
    Route::get('audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');

    // SMS logs
    Route::get('sms-logs', [SmsLogController::class, 'index'])->name('sms-logs.index');
});

Route::get('/test-decrypt/{enc}', function($enc){ try { return 'dec:'.decrypt($enc); } catch(Exception $e){ return 'fail:'.$e->getMessage(); } });
