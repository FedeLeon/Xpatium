<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\LessonCommentController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'landing')->name('home');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');

Route::middleware('guest')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    Route::get('/registro', [AuthController::class, 'showRegisterRole'])->name('register');
    Route::post('/registro/rol', [AuthController::class, 'storeRegisterRole'])->name('register.role');
    Route::get('/registro/formulario', [AuthController::class, 'showRegister'])->name('register.form');
    Route::post('/registro', [AuthController::class, 'register'])->name('register.submit');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/{conversation}', [ChatController::class, 'show'])->name('chat.show');
    Route::post('/chat/{conversation}/messages', [ChatController::class, 'storeMessage'])->name('chat.messages.store');
    Route::post('/chat/directo', [ChatController::class, 'storeDirect'])->name('chat.direct.store');
    Route::post('/chat/grupo', [ChatController::class, 'storeGroup'])->name('chat.group.store');
    Route::post('/lecciones/{lesson}/comentarios', [LessonCommentController::class, 'store'])->name('lessons.comments.store');
    Route::delete('/comentarios/{comment}', [LessonCommentController::class, 'destroy'])->name('lessons.comments.destroy');
    Route::post('/notificaciones/leidas', [NotificationController::class, 'markAllRead'])->name('notifications.read');
});
