<?php

use App\Http\Controllers\Admin\BatchController;
use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\Auth\StudentLoginController;
use App\Http\Controllers\Student\StudentDashboardController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware('guest')->group(function () {
    Route::get('/', fn() => Inertia::render('Welcome'))->name('welcome');

    Route::get('student/login', [StudentLoginController::class, 'create'])->name('student.login');
    Route::post('student/login', [StudentLoginController::class, 'store'])->name('student.login.store');

    Route::get('/admin/login', [AdminLoginController::class, 'create'])->name('admin.login');
    Route::post('/admin/login', [AdminLoginController::class, 'store'])->name('admin.login.store');
});

Route::middleware(['auth', 'can:access-admin-panel'])->group(function () {
    Route::get('/admin/batches', [BatchController::class, 'index'])->name('admin.batches.index');
    Route::post('/admin/batches', [BatchController::class, 'store'])->name('admin.batches.store');
    Route::patch('/admin/batches/{batch}', [BatchController::class, 'update'])->name('admin.batches.update');
    Route::post('/admin/batches/bulk-status', [BatchController::class, 'bulkStatus'])->name('admin.batches.bulk-status');
    Route::post('/admin/logout', [AdminLoginController::class, 'destroy'])->name('admin.logout');
});

Route::middleware(['auth', 'can:access-student-panel'])->group(function () {
    Route::get('/student/dashboard', StudentDashboardController::class)->name('student.dashboard');
    Route::post('/student/logout', [StudentLoginController::class, 'destroy'])->name('student.logout');
});
