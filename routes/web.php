<?php

use App\Http\Controllers\Admin\BatchController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\ProgrammeController;
use App\Http\Controllers\Admin\Student\StudentDocumentController;
use App\Http\Controllers\Admin\Student\StudentPaperSelectionController;
use App\Http\Controllers\Admin\Student\StudentPaymentController;
use App\Http\Controllers\Admin\Student\StudentProfileController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\Auth\AdminPasswordChangeController;
use App\Http\Controllers\Auth\AdminForgotPasswordController;
use App\Http\Controllers\Auth\StudentLoginController;
use App\Http\Controllers\Auth\StudentPasswordChangeController;
use App\Http\Controllers\Auth\StudentForgotPasswordController;
use App\Http\Controllers\Student\StudentDashboardController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware('guest')->group(function () {
    Route::get('/', fn () => Inertia::render('Welcome'))->name('welcome');

    Route::get('student/login', [StudentLoginController::class, 'create'])->name('student.login');
    Route::post('student/login', [StudentLoginController::class, 'store'])->name('student.login.store');
    
    Route::get('student/forgot-password', [StudentForgotPasswordController::class, 'create'])->name('student.password.request');
    Route::post('student/forgot-password', [StudentForgotPasswordController::class, 'store'])->name('student.password.email');

    Route::get('/admin/login', [AdminLoginController::class, 'create'])->name('admin.login');
    Route::post('/admin/login', [AdminLoginController::class, 'store'])->name('admin.login.store');

    Route::get('/admin/forgot-password', [AdminForgotPasswordController::class, 'create'])->name('admin.password.request');
    Route::post('/admin/forgot-password', [AdminForgotPasswordController::class, 'store'])->name('admin.password.email');
});

Route::middleware(['auth', 'can:access-admin-panel'])->group(function () {
    // Admin Password Change
    Route::get('/admin/password/change', [AdminPasswordChangeController::class, 'create'])->name('admin.password.change');
    Route::post('/admin/password/change', [AdminPasswordChangeController::class, 'store'])->name('admin.password.update');

    // Batches
    Route::get('/admin/batches', [BatchController::class, 'index'])->name('admin.batches.index');
    Route::post('/admin/batches', [BatchController::class, 'store'])->name('admin.batches.store');
    Route::patch('/admin/batches/bulk-update-status', [BatchController::class, 'bulkUpdateStatus'])->name('admin.batches.bulk-update-status');
    Route::patch('/admin/batches/{batch_id}', [BatchController::class, 'update'])->name('admin.batches.update');
    Route::patch('/admin/batches/{batch_id}/status', [BatchController::class, 'updateStatus'])->name('admin.batches.update-status');

    // Programmes
    Route::get('/admin/programmes', [ProgrammeController::class, 'index'])->name('admin.programmes.index');
    Route::post('/admin/programmes', [ProgrammeController::class, 'store'])->name('admin.programmes.store');
    Route::patch('/admin/programmes/bulk-update-status', [ProgrammeController::class, 'bulkUpdateStatus'])->name('admin.programmes.bulk-update-status');
    Route::patch('/admin/programmes/{programme_id}', [ProgrammeController::class, 'update'])->name('admin.programmes.update');
    Route::patch('/admin/programmes/{programme_id}/status', [ProgrammeController::class, 'updateStatus'])->name('admin.programmes.update-status');

    // Courses
    Route::get('/admin/courses', [CourseController::class, 'index'])->name('admin.courses.index');
    Route::post('/admin/courses', [CourseController::class, 'store'])->name('admin.courses.store');
    Route::patch('/admin/courses/bulk-update-status', [CourseController::class, 'bulkUpdateStatus'])->name('admin.courses.bulk-update-status');
    Route::patch('/admin/courses/{course_id}', [CourseController::class, 'update'])->name('admin.courses.update');
    Route::patch('/admin/courses/{course_id}/status', [CourseController::class, 'updateStatus'])->name('admin.courses.update-status');

    // Subjects
    Route::get('/admin/subjects', [SubjectController::class, 'index'])->name('admin.subjects.index');
    Route::get('/admin/subjects/create', [SubjectController::class, 'create'])->name('admin.subjects.create');
    Route::post('/admin/subjects', [SubjectController::class, 'store'])->name('admin.subjects.store');
    Route::patch('/admin/subjects/bulk-update-status', [SubjectController::class, 'bulkUpdateStatus'])->name('admin.subjects.bulk-update-status');
    Route::get('/admin/subjects/{id}', [SubjectController::class, 'show'])->name('admin.subjects.show');
    Route::get('/admin/subjects/{id}/edit', [SubjectController::class, 'edit'])->name('admin.subjects.edit');
    Route::patch('/admin/subjects/{id}', [SubjectController::class, 'update'])->name('admin.subjects.update');
    Route::patch('/admin/subjects/{id}/status', [SubjectController::class, 'updateStatus'])->name('admin.subjects.update-status');

    // Students
    Route::get('/admin/students', [StudentProfileController::class, 'index'])->name('admin.students.index');
    Route::get('/admin/students/create', [StudentProfileController::class, 'create'])->name('admin.students.create');
    Route::post('/admin/students', [StudentProfileController::class, 'store'])->name('admin.students.store');
    Route::patch('/admin/students/bulk-update-status', [StudentProfileController::class, 'bulkUpdateStatus'])->name('admin.students.bulk-update-status');
    Route::get('/admin/students/{id}', [StudentProfileController::class, 'show'])->name('admin.students.show');
    Route::get('/admin/students/{id}/edit', [StudentProfileController::class, 'edit'])->name('admin.students.edit');
    Route::patch('/admin/students/{id}', [StudentProfileController::class, 'update'])->name('admin.students.update');
    Route::patch('/admin/students/{id}/status', [StudentProfileController::class, 'updateStatus'])->name('admin.students.update-status');

    Route::patch('/admin/students/{id}/papers', [StudentPaperSelectionController::class, 'update'])->name('admin.students.papers.update');

    Route::patch('/admin/students/{id}/documents', [StudentDocumentController::class, 'update'])->name('admin.students.documents.update');

    Route::patch('/admin/students/{id}/payments', [StudentPaymentController::class, 'update'])->name('admin.students.payments.update');

    Route::post('/admin/logout', [AdminLoginController::class, 'destroy'])->name('admin.logout');
});

Route::middleware(['auth', 'can:access-student-panel'])->group(function () {
    Route::get('/student/password/change', [StudentPasswordChangeController::class, 'create'])->name('student.password.change');
    Route::post('/student/password/change', [StudentPasswordChangeController::class, 'store'])->name('student.password.update');

    Route::middleware('password.changed')->group(function () {
        Route::get('/student/dashboard', [StudentDashboardController::class, 'index'])->name('student.dashboard');
        Route::get('/student/payment-receipt/download', [StudentDashboardController::class, 'downloadReceipt'])->name('student.payment.receipt');
    });

    Route::post('/student/logout', [StudentLoginController::class, 'destroy'])->name('student.logout');
});
