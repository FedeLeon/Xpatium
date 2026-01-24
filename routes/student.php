<?php

use App\Http\Controllers\MeetingRoomController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Student\AnnouncementController as StudentAnnouncementController;
use App\Http\Controllers\Student\CourseController as StudentCourseController;
use App\Http\Controllers\Student\DashboardController;
use App\Http\Controllers\Student\LessonController as StudentLessonController;
use App\Http\Controllers\Student\LessonQuizController as StudentLessonQuizController;
use App\Http\Controllers\Student\MeetingController as StudentMeetingController;
use App\Http\Controllers\Student\ProfileController as StudentProfileController;
use App\Http\Controllers\Student\TaskController as StudentTaskController;
use Illuminate\Support\Facades\Route;

Route::get('/alumno', [DashboardController::class, 'index'])
    ->middleware(['auth', 'role:student'])
    ->name('student.dashboard');

Route::middleware(['auth', 'role:student'])->prefix('alumno')->name('student.')->group(function () {
    Route::get('/cursos', [StudentCourseController::class, 'index'])->name('courses.index');
    Route::get('/cursos/{course}', [StudentCourseController::class, 'show'])->name('courses.show');
    Route::get('/lecciones/{lesson}', [StudentLessonController::class, 'show'])->name('lessons.show');
    Route::post('/lecciones/{lesson}/completada', [StudentLessonController::class, 'toggleCompletion'])->name('lessons.toggleCompletion');
    Route::post('/lecciones/{lesson}/cuestionario', [StudentLessonQuizController::class, 'store'])->name('lessons.quiz.store');
    Route::post('/anuncios/{announcement}/leido', [StudentAnnouncementController::class, 'markRead'])->name('announcements.read');
    Route::get('/notificaciones', [NotificationController::class, 'index'])->name('notifications.index');
    Route::delete('/notificaciones/{notificationId}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::delete('/notificaciones', [NotificationController::class, 'destroyAll'])->name('notifications.destroyAll');
    Route::get('/tareas/pendientes', [StudentTaskController::class, 'pending'])->name('tasks.pending');
    Route::get('/tareas', [StudentTaskController::class, 'index'])->name('tasks.index');
    Route::get('/perfil', [StudentProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/perfil', [StudentProfileController::class, 'update'])->name('profile.update');
    Route::get('/reuniones', [StudentMeetingController::class, 'index'])->name('meetings.index');
    Route::get('/reuniones/{meeting}', [MeetingRoomController::class, 'show'])->name('meetings.show');
});
