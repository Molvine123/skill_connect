<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\InstitutionController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\VirtualClassController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\EmployerController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\JobApplicationController;
use App\Http\Controllers\ChatController;

// ── Public / Auth Routes ───────────────────────────────────────────────────────
Route::get('/verify/certificate/{code}', [ProgramController::class, 'verifyCertificate'])->name('certificates.verify');

Route::middleware('guest')->group(function () {
    Route::get('/',              [AuthController::class, 'showLogin'])->name('home');
    Route::get('/login',        [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',       [AuthController::class, 'login']);
    Route::get('/register',     [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register',    [AuthController::class, 'register']);

    // Password Reset
    Route::get('/forgot-password',           [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password',          [AuthController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}',    [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/reset-password',           [AuthController::class, 'resetPassword'])->name('password.update');
});

// ── Logout ─────────────────────────────────────────────────────────────────────
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ── Admin Dashboard ────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard',                      [AdminController::class, 'dashboard'])->name('dashboard');
    Route::post('/approve/{type}/{id}',           [AdminController::class, 'approve'])->name('approve');
    Route::post('/reject/{type}/{id}',            [AdminController::class, 'reject'])->name('reject');
    Route::post('/toggle-user/{id}',              [AdminController::class, 'toggleUserStatus'])->name('toggle-user');
    Route::post('/categories',                    [AdminController::class, 'storeCategory'])->name('categories.store');
    Route::put('/categories/{id}',                [AdminController::class, 'updateCategory'])->name('categories.update');
    Route::delete('/categories/{id}',             [AdminController::class, 'destroyCategory'])->name('categories.destroy');

    // Institution Management (admin view)
    Route::get('/institutions',                   [InstitutionController::class, 'index'])->name('institutions.index');
    Route::get('/institutions/{id}',              [InstitutionController::class, 'show'])->name('institutions.show');

    // Organization Management (admin view)
    Route::get('/organizations',                  [OrganizationController::class, 'index'])->name('organizations.index');
    Route::get('/organizations/{id}',             [OrganizationController::class, 'show'])->name('organizations.show');

    // Student Management (admin view)
    Route::get('/students',                       [StudentController::class, 'index'])->name('students.index');
    Route::get('/students/{id}',                  [StudentController::class, 'show'])->name('students.show');
    Route::get('/students/{id}/edit',             [StudentController::class, 'edit'])->name('students.edit');
    Route::put('/students/{id}',                  [StudentController::class, 'update'])->name('students.update');
    Route::post('/students/{id}/assign-institution', [StudentController::class, 'assignInstitution'])->name('students.assign-institution');

    // Program Management (admin view)
    Route::get('/programs',                       [ProgramController::class, 'adminIndex'])->name('programs.index');
    Route::get('/programs/{id}',                  [ProgramController::class, 'adminShow'])->name('programs.show');

    // Employer Management (admin view)
    Route::get('/employers',                      [EmployerController::class, 'adminIndex'])->name('employers.index');
    Route::get('/employers/{id}',                 [EmployerController::class, 'adminShow'])->name('employers.show');
    Route::post('/approve/employer/{id}',         [AdminController::class, 'approveEmployer'])->name('approve.employer');
    Route::post('/reject/employer/{id}',          [AdminController::class, 'rejectEmployer'])->name('reject.employer');
});

// ── Institution Dashboard ──────────────────────────────────────────────────────
Route::middleware(['auth', 'role:institution'])->prefix('institution')->name('institution.')->group(function () {
    Route::get('/dashboard',        [InstitutionController::class, 'dashboard'])->name('dashboard');
    Route::get('/profile/edit',     [InstitutionController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/update',  [InstitutionController::class, 'updateProfile'])->name('profile.update');

    // Student Management (institution view)
    Route::get('/students',                       [StudentController::class, 'institutionIndex'])->name('students.index');
    Route::get('/students/add',                   [StudentController::class, 'institutionAddForm'])->name('students.add');
    Route::post('/students/store',                [StudentController::class, 'institutionStoreStudent'])->name('students.store');
    Route::post('/students/assign',               [StudentController::class, 'institutionAssignStudent'])->name('students.assign');
    Route::get('/students/{id}',                  [StudentController::class, 'institutionShow'])->name('students.show');

    // Program Browsing & Enrollment
    Route::get('/programs',                       [StudentController::class, 'browsePrograms'])->name('programs.index');
    Route::post('/programs/{id}/enroll',          [StudentController::class, 'enrollInProgram'])->name('programs.enroll');
});

// ── Organization Dashboard ─────────────────────────────────────────────────────
Route::middleware(['auth', 'role:organization'])->prefix('organization')->name('organization.')->group(function () {
    Route::get('/dashboard',        [OrganizationController::class, 'dashboard'])->name('dashboard');
    Route::get('/profile/edit',     [OrganizationController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/update',  [OrganizationController::class, 'updateProfile'])->name('profile.update');

    // Program Browsing (Global)
    Route::get('/programs/browse',                 [StudentController::class, 'browsePrograms'])->name('programs.browse');
    Route::post('/programs/{id}/enroll',           [StudentController::class, 'enrollInProgram'])->name('programs.enroll');

    // Program CRUD
    Route::get('/programs',                        [ProgramController::class, 'index'])->name('programs.index');
    Route::get('/programs/create',                 [ProgramController::class, 'create'])->name('programs.create');
    Route::post('/programs',                       [ProgramController::class, 'store'])->name('programs.store');
    Route::get('/programs/{id}',                   [ProgramController::class, 'show'])->name('programs.show');
    Route::get('/programs/{id}/edit',              [ProgramController::class, 'edit'])->name('programs.edit');
    Route::put('/programs/{id}',                   [ProgramController::class, 'update'])->name('programs.update');
    Route::delete('/programs/{id}',                [ProgramController::class, 'destroy'])->name('programs.destroy');
// Organization-wide Quick Action routes
    Route::get('/sessions', function(){ return redirect()->route('organization.dashboard'); })->name('sessions.index');
    Route::get('/enrollments', function(){ return redirect()->route('organization.dashboard'); })->name('enrollments.index');
    Route::get('/certificates', [OrganizationController::class, 'certificatesIndex'])->name('certificates.index');

    // Session Management
    Route::get('/programs/{id}/sessions',          [ProgramController::class, 'sessions'])->name('programs.sessions');
    Route::post('/programs/{id}/sessions',         [ProgramController::class, 'storeSession'])->name('programs.sessions.store');
    Route::delete('/programs/{programId}/sessions/{sessionId}', [ProgramController::class, 'destroySession'])->name('programs.sessions.destroy');
// removed placeholder - moved into organization group
    // Enrollment Management
    Route::get('/programs/{id}/enrollments',                              [ProgramController::class, 'enrollments'])->name('programs.enrollments');
    Route::post('/programs/{programId}/enrollments/{enrollmentId}/approve', [ProgramController::class, 'approveEnrollment'])->name('programs.enrollments.approve');
    Route::post('/programs/{programId}/enrollments/{enrollmentId}/reject',  [ProgramController::class, 'rejectEnrollment'])->name('programs.enrollments.reject');
    Route::post('/programs/{programId}/enrollments/{enrollmentId}/complete', [ProgramController::class, 'completeEnrollment'])->name('programs.enrollments.complete');

    // Attendance Tracking
    Route::get('/attendance', [OrganizationController::class, 'attendanceIndex'])->name('attendance');
    Route::get('/programs/{programId}/sessions/{sessionId}/attendance',  [ProgramController::class, 'attendance'])->name('programs.attendance');
    Route::post('/programs/{programId}/sessions/{sessionId}/attendance', [ProgramController::class, 'saveAttendance'])->name('programs.attendance.save');
    Route::get('/programs/{programId}/sessions/{sessionId}/qr',          [ProgramController::class, 'showSessionQr'])->name('programs.sessions.qr');

    // Certificate Issuance
    Route::get('/programs/{id}/certificates',                                  [ProgramController::class, 'certificates'])->name('programs.certificates');
    Route::post('/programs/{programId}/certificates/{enrollmentId}/issue',     [ProgramController::class, 'issueCertificate'])->name('programs.certificates.issue');
    Route::post('/programs/{programId}/certificates/issue-all',                [ProgramController::class, 'issueAllCertificates'])->name('programs.certificates.issue-all');
    Route::get('/programs/{programId}/certificates/{enrollmentId}/download',   [ProgramController::class, 'downloadCertificate'])->name('programs.certificates.download');
});

// ── Student Dashboard ──────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard',        [DashboardController::class, 'student'])->name('dashboard');
    
    // Profile Management
    Route::get('/profile/edit',     [StudentController::class, 'editProfile'])->name('profile.edit');
    Route::post('/profile/update',  [StudentController::class, 'updateProfile'])->name('profile.update');

    // Program Browsing & Enrollment
    Route::get('/programs',         [StudentController::class, 'browsePrograms'])->name('programs.index');
    Route::post('/programs/{id}/enroll', [StudentController::class, 'enrollInProgram'])->name('programs.enroll');
    Route::delete('/programs/{id}/enroll', [StudentController::class, 'cancelEnrollment'])->name('programs.cancel-enrollment');

    // Portal sections
    Route::get('/enrollments',      [StudentController::class, 'myEnrollments'])->name('enrollments.index');
    Route::get('/sessions',         [StudentController::class, 'upcomingSessions'])->name('sessions.index');
    Route::post('/sessions/{id}/attend', [StudentController::class, 'markAttendanceViaQr'])->name('sessions.attend');
    Route::get('/certificates',     [StudentController::class, 'myCertificates'])->name('certificates.index');
    Route::get('/certificates/{id}/download', [StudentController::class, 'downloadCertificate'])->name('certificates.download');
    Route::get('/payments',                       [StudentController::class, 'myPayments'])->name('payments.index');
    Route::get('/payment/{id}/checkout',            [PaymentController::class, 'show'])->name('payment.checkout');
    Route::post('/payment/{id}/initiate',           [PaymentController::class, 'initiate'])->name('payment.initiate');
    Route::get('/payment/{id}/pending',             [PaymentController::class, 'pending'])->name('payment.pending');
    Route::get('/payment/{id}/status',              [PaymentController::class, 'status'])->name('payment.status');

    // Job Board
    Route::get('/jobs',                          [JobController::class, 'browse'])->name('jobs.index');
    Route::get('/jobs/{id}',                     [JobController::class, 'show'])->name('jobs.show');
    Route::post('/jobs/{id}/apply',              [JobApplicationController::class, 'apply'])->name('jobs.apply');
    Route::get('/my-applications',               [JobApplicationController::class, 'myApplications'])->name('applications.index');
    Route::delete('/applications/{id}/withdraw', [JobApplicationController::class, 'withdraw'])->name('applications.withdraw');

    // Student Portfolio
    Route::get('/portfolio/edit',   [StudentController::class, 'editPortfolio'])->name('portfolio.edit');
    Route::post('/portfolio/update',[StudentController::class, 'updatePortfolio'])->name('portfolio.update');
});

// ── Virtual Classroom Routes ───────────────────────────────────────────────────
// Organization: create/manage rooms (role:organization)
Route::middleware(['auth', 'role:organization'])->prefix('virtual-class')->name('virtual-class.')->group(function () {
    Route::post('/session/{session}/create', [VirtualClassController::class, 'create'])->name('create');
    Route::post('/{virtualClass}/open',      [VirtualClassController::class, 'open'])->name('open');
    Route::post('/{virtualClass}/close',     [VirtualClassController::class, 'close'])->name('close');
    Route::post('/{virtualClass}/materials', [VirtualClassController::class, 'uploadMaterial'])->name('materials.upload');
});

// Shared room view: both organizations and students can access
Route::middleware('auth')->prefix('virtual-class')->name('virtual-class.')->group(function () {
    Route::get('/{virtualClass}/room',          [VirtualClassController::class, 'room'])->name('room');
    Route::post('/{virtualClass}/chat',         [VirtualClassController::class, 'sendMessage'])->name('chat.send');
    Route::get('/{virtualClass}/chat',          [VirtualClassController::class, 'getMessages'])->name('chat.get');
    Route::post('/{virtualClass}/attend/join',  [VirtualClassController::class, 'recordJoin'])->name('attend.join');
Route::post('/{virtualClass}/attend/leave', [VirtualClassController::class, 'recordLeave'])->name('attend.leave');
    // Attendance report view
    Route::get('/{virtualClass}/attendance-report', [VirtualClassController::class, 'attendanceReport'])->name('attendance.report');
});

// ── Employer Dashboard ─────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:employer'])->prefix('employer')->name('employer.')->group(function () {
    Route::get('/dashboard',        [EmployerController::class, 'dashboard'])->name('dashboard');
    Route::get('/profile/edit',     [EmployerController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/update',  [EmployerController::class, 'updateProfile'])->name('profile.update');

    // Job Management
    Route::get('/jobs',                      [JobController::class, 'index'])->name('jobs.index');
    Route::get('/jobs/create',               [JobController::class, 'create'])->name('jobs.create');
    Route::post('/jobs',                     [JobController::class, 'store'])->name('jobs.store');
    Route::get('/jobs/{id}/edit',            [JobController::class, 'edit'])->name('jobs.edit');
    Route::put('/jobs/{id}',                 [JobController::class, 'update'])->name('jobs.update');
    Route::delete('/jobs/{id}',              [JobController::class, 'destroy'])->name('jobs.destroy');
    Route::get('/jobs/{id}/applications',    [JobController::class, 'applications'])->name('jobs.applications');

    // Application Management
    Route::post('/applications/{id}/status',    [JobApplicationController::class, 'updateStatus'])->name('applications.status');
    Route::post('/applications/{id}/interview', [JobApplicationController::class, 'scheduleInterview'])->name('applications.interview');
    Route::post('/employment/{id}/status',      [JobApplicationController::class, 'updateEmploymentStatus'])->name('employment.status');

    // Candidate Search & Portfolio
    Route::get('/search',              [EmployerController::class, 'search'])->name('search');
    Route::get('/portfolio/{id}',      [EmployerController::class, 'viewPortfolio'])->name('portfolio');
});

// ── AI Chat Assistant ──────────────────────────────────────────────────────────
Route::middleware(['auth'])->prefix('chat')->name('chat.')->group(function () {
    Route::get('/',        [ChatController::class, 'index'])->name('index');
    Route::post('/send',   [ChatController::class, 'sendMessage'])->middleware('throttle:30,1')->name('send');
    Route::post('/clear',  [ChatController::class, 'clearSession'])->name('clear');
});
