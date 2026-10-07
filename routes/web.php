<?php

use App\Http\Controllers\Admin\ApplicantController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\InterviewController as AdminInterviewController;
use App\Http\Controllers\Admin\JobVacancyController as AdminJobVacancyController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\JobSeeker\ApplicationController;
use App\Http\Controllers\JobSeeker\DashboardController as JobSeekerDashboardController;
use App\Http\Controllers\JobSeeker\JobController;
use App\Http\Controllers\JobSeeker\NotificationController;
use App\Http\Controllers\JobSeeker\ProfileController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Models\JobVacancy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $featuredJobs = JobVacancy::with('category')
        ->available()
        ->where(function ($q) {
            $q->whereNull('application_deadline')
              ->orWhereDate('application_deadline', '>=', today());
        })
        ->latest()
        ->take(6)
        ->get();

    $jobCount = JobVacancy::where('is_active', true)->count();

    return view('welcome', compact('featuredJobs', 'jobCount'));
})->name('home');

// Admin Routes
Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/chart-data', [AdminDashboardController::class, 'getChartData'])->name('dashboard.chart-data');

    // Job Vacancies (archived must be declared before the resource route)
    Route::get('job-vacancies/archived', [AdminJobVacancyController::class, 'archived'])->name('job-vacancies.archived');
    Route::post('job-vacancies/{id}/restore', [AdminJobVacancyController::class, 'restore'])->name('job-vacancies.restore');
    Route::delete('job-vacancies/{id}/force-delete', [AdminJobVacancyController::class, 'forceDelete'])->name('job-vacancies.force-delete');
    Route::resource('job-vacancies', AdminJobVacancyController::class)->withTrashed(['show']);
    Route::post('job-vacancies/{jobVacancy}/toggle-status', [AdminJobVacancyController::class, 'toggleStatus'])->name('job-vacancies.toggle-status');
    Route::post('job-vacancies/{jobVacancy}/toggle-active', [AdminJobVacancyController::class, 'toggleActive'])->name('job-vacancies.toggle-active');

    // Applicants
    Route::get('/applicants', [ApplicantController::class, 'index'])->name('applicants.index');
    Route::get('/applicants/{application}', [ApplicantController::class, 'show'])->name('applicants.show');
    Route::post('/applicants/{application}/status', [ApplicantController::class, 'updateStatus'])->name('applicants.update-status');
    Route::get('/applicants/{application}/view-resume', [ApplicantController::class, 'viewResume'])->name('applicants.view-resume');
    Route::get('/applicants/{application}/download-resume', [ApplicantController::class, 'downloadResume'])->name('applicants.download-resume');

    // Interviews
    Route::get('/interviews', [AdminInterviewController::class, 'index'])->name('interviews.index');
    Route::get('/interviews/create/{application}', [AdminInterviewController::class, 'create'])->name('interviews.create');
    Route::post('/interviews', [AdminInterviewController::class, 'store'])->name('interviews.store');
    Route::get('/interviews/{interview}', [AdminInterviewController::class, 'show'])->name('interviews.show');
    Route::get('/interviews/{interview}/edit', [AdminInterviewController::class, 'edit'])->name('interviews.edit');
    Route::put('/interviews/{interview}', [AdminInterviewController::class, 'update'])->name('interviews.update');
    Route::post('/interviews/{interview}/status', [AdminInterviewController::class, 'updateStatus'])->name('interviews.update-status');
    Route::delete('/interviews/{interview}', [AdminInterviewController::class, 'destroy'])->name('interviews.destroy');

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export-pdf', [ReportController::class, 'exportPdf'])->name('reports.export-pdf');
    Route::get('/reports/export-excel', [ReportController::class, 'exportExcel'])->name('reports.export-excel');

    // Manage Users (Job Seekers)
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}', [AdminUserController::class, 'show'])->name('users.show');
    Route::post('/users/{user}/toggle-status', [AdminUserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
});

// Job Seeker Routes
Route::middleware(['auth', 'verified', 'role:job_seeker'])->prefix('jobseeker')->name('jobseeker.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [JobSeekerDashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/upload-resume', [ProfileController::class, 'uploadResume'])->name('profile.upload-resume');
    Route::get('/profile/download-resume', [ProfileController::class, 'downloadResume'])->name('profile.download-resume');
    Route::delete('/profile/photo', [ProfileController::class, 'removeProfilePhoto'])->name('profile.remove-photo');

    // Jobs
    Route::get('/jobs', [JobController::class, 'index'])->name('jobs.index');
    Route::get('/jobs/saved', [JobController::class, 'saved'])->name('jobs.saved');
    Route::get('/jobs/{jobVacancy}', [JobController::class, 'show'])->name('jobs.show')->withTrashed();
    Route::post('/jobs/{jobVacancy}/toggle-save', [JobController::class, 'toggleSave'])->name('jobs.toggle-save');

    // Applications
    Route::get('/applications', [ApplicationController::class, 'index'])->name('applications.index');
    Route::get('/applications/create/{jobVacancy}', [ApplicationController::class, 'create'])->name('applications.create');
    Route::post('/applications/{jobVacancy}', [ApplicationController::class, 'store'])->name('applications.store');
    Route::get('/applications/{application}', [ApplicationController::class, 'show'])->name('applications.show');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::get('/notifications/unread-count', [NotificationController::class, 'getUnreadCount'])->name('notifications.unread-count');
});

// Breeze Profile Routes (for admin password/name updates)
Route::middleware('auth')->group(function () {
    Route::get('profile', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::delete('profile', [\App\Http\Controllers\ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Include auth routes from Breeze
require __DIR__ . '/auth.php';
