<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ErrorLogController;
use App\Http\Controllers\Admin\PlanController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/admin', [DashboardController::class, 'index'])
    ->middleware(['auth', 'role:admin'])
    ->name('admin.dashboard');
Route::get('/admin/planes', [PlanController::class, 'index'])
    ->middleware(['auth', 'role:admin'])
    ->name('admin.plans.index');
Route::put('/admin/planes/{user}', [PlanController::class, 'update'])
    ->middleware(['auth', 'role:admin'])
    ->name('admin.plans.update');
Route::get('/admin/errores', [ErrorLogController::class, 'index'])
    ->middleware(['auth', 'role:admin'])
    ->name('admin.errors.index');
Route::get('/admin/errores/{errorLog}', [ErrorLogController::class, 'show'])
    ->middleware(['auth', 'role:admin'])
    ->name('admin.errors.show');
Route::get('/admin/perfil', [ProfileController::class, 'edit'])
    ->defaults('update_route', 'admin.profile.update')
    ->middleware(['auth', 'role:admin'])
    ->name('admin.profile.edit');
Route::put('/admin/perfil', [ProfileController::class, 'update'])
    ->middleware(['auth', 'role:admin'])
    ->name('admin.profile.update');
