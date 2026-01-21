<?php

use App\Http\Controllers\MeetingRoomController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Teacher\AnnouncementController;
use App\Http\Controllers\Teacher\ContentController;
use App\Http\Controllers\Teacher\CourseController;
use App\Http\Controllers\Teacher\CourseModuleController;
use App\Http\Controllers\Teacher\CommissionController;
use App\Http\Controllers\Teacher\DashboardController;
use App\Http\Controllers\Teacher\LessonController;
use App\Http\Controllers\Teacher\LessonQuizController;
use App\Http\Controllers\Teacher\MeetingController as TeacherMeetingController;
use App\Http\Controllers\Teacher\StudentController;
use Illuminate\Support\Facades\Route;

Route::get('/profesor', [DashboardController::class, 'index'])
    ->middleware(['auth', 'role:teacher'])
    ->name('teacher.dashboard');

Route::middleware(['auth', 'role:teacher'])->prefix('profesor')->name('teacher.')->group(function () {
    Route::get('/cursos', [CourseController::class, 'index'])->name('courses.index');
    Route::get('/cursos/crear', [CourseController::class, 'create'])->name('courses.create');
    Route::post('/cursos', [CourseController::class, 'store'])->name('courses.store');
    Route::get('/cursos/{course}', [CourseController::class, 'show'])->name('courses.show');
    Route::get('/cursos/{course}/editar', [CourseController::class, 'edit'])->name('courses.edit');
    Route::put('/cursos/{course}', [CourseController::class, 'update'])->name('courses.update');
    Route::delete('/cursos/{course}', [CourseController::class, 'destroy'])->name('courses.destroy');
    Route::get('/cursos/{course}/contenido', [CourseModuleController::class, 'index'])->name('courses.modules.index');
    Route::post('/cursos/{course}/modulos', [CourseModuleController::class, 'storeModule'])->name('courses.modules.store');
    Route::put('/modulos/{module}', [CourseModuleController::class, 'updateModule'])->name('courses.modules.update');
    Route::post('/modulos/{module}/lecciones', [CourseModuleController::class, 'storeLesson'])->name('courses.lessons.store');
    Route::delete('/modulos/{module}', [CourseModuleController::class, 'destroyModule'])->name('courses.modules.destroy');
    Route::delete('/lecciones/{lesson}', [CourseModuleController::class, 'destroyLesson'])->name('courses.lessons.destroy');
    Route::get('/gestor-contenidos', [ContentController::class, 'index'])->name('assets.index');
    Route::post('/gestor-contenidos', [ContentController::class, 'store'])->name('assets.store');
    Route::post('/gestor-contenidos/chunk', [ContentController::class, 'uploadChunk'])->name('assets.chunk');
    Route::delete('/gestor-contenidos/{asset}', [ContentController::class, 'destroy'])->name('assets.destroy');
    Route::delete('/gestor-contenidos', [ContentController::class, 'bulkDestroy'])->name('assets.bulkDestroy');
    Route::get('/lecciones/{lesson}', [LessonController::class, 'show'])->name('lessons.show');
    Route::post('/lecciones/{lesson}/contenido', [LessonController::class, 'storeContent'])->name('lessons.content.store');
    Route::delete('/lecciones/contenido/{content}', [LessonController::class, 'destroyContent'])->name('lessons.content.destroy');
    Route::put('/lecciones/contenido/{content}', [LessonController::class, 'updateContent'])->name('lessons.content.update');
    Route::post('/lecciones/{lesson}/cuestionario', [LessonQuizController::class, 'store'])->name('lessons.quiz.store');
    Route::put('/lecciones/{lesson}/cuestionario', [LessonQuizController::class, 'update'])->name('lessons.quiz.update');
    Route::delete('/lecciones/{lesson}/cuestionario', [LessonQuizController::class, 'destroy'])->name('lessons.quiz.destroy');
    Route::post('/lecciones/{lesson}/cuestionario/preguntas', [LessonQuizController::class, 'storeQuestion'])->name('lessons.quiz.questions.store');
    Route::put('/cuestionario/preguntas/{question}', [LessonQuizController::class, 'updateQuestion'])->name('lessons.quiz.questions.update');
    Route::delete('/cuestionario/preguntas/{question}', [LessonQuizController::class, 'destroyQuestion'])->name('lessons.quiz.questions.destroy');
    Route::get('/alumnos', [StudentController::class, 'index'])->name('students.index');
    Route::get('/alumnos/comision', [CommissionController::class, 'index'])->name('students.commission');
    Route::post('/alumnos/comision', [CommissionController::class, 'store'])->name('students.commission.store');
    Route::put('/alumnos/comision/{commission}/alumnos', [CommissionController::class, 'updateStudents'])->name('students.commission.students.update');
    Route::delete('/alumnos/comision/{commission}', [CommissionController::class, 'destroy'])->name('students.commission.destroy');
    Route::post('/alumnos', [StudentController::class, 'store'])->name('students.store');
    Route::put('/alumnos/{studentProfile}/estado', [StudentController::class, 'updateStatus'])->name('students.status');
    Route::put('/alumnos/{studentProfile}/cursos', [StudentController::class, 'updateCourseAccess'])->name('students.courses.access');
    Route::delete('/alumnos/{studentProfile}', [StudentController::class, 'destroy'])->name('students.destroy');
    Route::get('/anuncios', [AnnouncementController::class, 'index'])->name('announcements.index');
    Route::post('/anuncios', [AnnouncementController::class, 'store'])->name('announcements.store');
    Route::put('/anuncios/{announcement}/estado', [AnnouncementController::class, 'updateStatus'])->name('announcements.status');
    Route::delete('/anuncios/{announcement}', [AnnouncementController::class, 'destroy'])->name('announcements.destroy');
    Route::get('/reuniones', [TeacherMeetingController::class, 'index'])->name('meetings.index');
    Route::post('/reuniones', [TeacherMeetingController::class, 'store'])->name('meetings.store');
    Route::get('/reuniones/{meeting}', [MeetingRoomController::class, 'show'])->name('meetings.show');
    Route::delete('/reuniones/{meeting}', [TeacherMeetingController::class, 'destroy'])->name('meetings.destroy');
    Route::get('/notificaciones', [NotificationController::class, 'index'])->name('notifications.index');
    Route::delete('/notificaciones/{notificationId}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::delete('/notificaciones', [NotificationController::class, 'destroyAll'])->name('notifications.destroyAll');
    Route::get('/perfil', [ProfileController::class, 'edit'])
        ->defaults('update_route', 'teacher.profile.update')
        ->name('profile.edit');
    Route::put('/perfil', [ProfileController::class, 'update'])->name('profile.update');
});
