<?php

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    Route::get('/registro', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/registro', [AuthController::class, 'register'])->name('register.submit');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::view('/admin', 'dashboards.admin')->middleware(['auth', 'role:admin'])->name('admin.dashboard');
Route::view('/profesor', 'dashboards.teacher')->middleware(['auth', 'role:teacher'])->name('teacher.dashboard');
Route::view('/alumno', 'dashboards.student')->middleware(['auth', 'role:student'])->name('student.dashboard');
