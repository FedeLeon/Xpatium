<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Teacher\CourseController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    Route::get('/registro', [AuthController::class, 'showRegisterRole'])->name('register');
    Route::post('/registro/rol', [AuthController::class, 'storeRegisterRole'])->name('register.role');
    Route::get('/registro/formulario', [AuthController::class, 'showRegister'])->name('register.form');
    Route::post('/registro', [AuthController::class, 'register'])->name('register.submit');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::view('/admin', 'dashboards.admin')->middleware(['auth', 'role:admin'])->name('admin.dashboard');
Route::view('/profesor', 'dashboards.teacher')->middleware(['auth', 'role:teacher'])->name('teacher.dashboard');
Route::view('/alumno', 'dashboards.student')->middleware(['auth', 'role:student'])->name('student.dashboard');

Route::middleware(['auth', 'role:teacher'])->prefix('profesor')->name('teacher.')->group(function () {
    Route::get('/cursos', [CourseController::class, 'index'])->name('courses.index');
    Route::get('/cursos/crear', [CourseController::class, 'create'])->name('courses.create');
    Route::post('/cursos', [CourseController::class, 'store'])->name('courses.store');
    Route::get('/cursos/{course}', [CourseController::class, 'show'])->name('courses.show');
    Route::get('/cursos/{course}/editar', [CourseController::class, 'edit'])->name('courses.edit');
    Route::put('/cursos/{course}', [CourseController::class, 'update'])->name('courses.update');
});
