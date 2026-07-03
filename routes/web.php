<?php

use App\Http\Controllers\Admin\BatchController;
use App\Http\Controllers\Admin\ProgrammeController;
use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\Auth\StudentLoginController;
use App\Http\Controllers\Student\StudentDashboardController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware('guest')->group(function () {
    Route::get('/', fn () => Inertia::render('Welcome'))->name('welcome');

    Route::get('student/login', [StudentLoginController::class, 'create'])->name('student.login');
    Route::post('student/login', [StudentLoginController::class, 'store'])->name('student.login.store');

    Route::get('/admin/login', [AdminLoginController::class, 'create'])->name('admin.login');
    Route::post('/admin/login', [AdminLoginController::class, 'store'])->name('admin.login.store');
});

Route::middleware(['auth', 'can:access-admin-panel'])->group(function () {
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

    Route::post('/admin/logout', [AdminLoginController::class, 'destroy'])->name('admin.logout');
});

Route::middleware(['auth', 'can:access-student-panel'])->group(function () {
    Route::get('/student/dashboard', StudentDashboardController::class)->name('student.dashboard');
    Route::post('/student/logout', [StudentLoginController::class, 'destroy'])->name('student.logout');
});
