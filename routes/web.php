<?php

use App\Http\Controllers\Student\LoginController;
use App\Http\Controllers\WelcomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', WelcomeController::class)->name('welcome');

Route::middleware('guest')->group(function () {
    Route::get('student/login', [LoginController::class, 'create'])->name('student.login');
    Route::post('student/login', [LoginController::class, 'store'])->name('student.login.store');
});
