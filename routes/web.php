<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::view('/admin', 'dashboards.admin')->middleware('role:admin')->name('admin.dashboard');
Route::view('/profesor', 'dashboards.teacher')->middleware('role:teacher')->name('teacher.dashboard');
Route::view('/alumno', 'dashboards.student')->middleware('role:student')->name('student.dashboard');
