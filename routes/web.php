<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Teacher\CourseController;
use App\Http\Controllers\Teacher\CourseModuleController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\LessonCommentController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Teacher\ContentController;
use App\Http\Controllers\Teacher\AnnouncementController;
use App\Http\Controllers\Teacher\LessonController;
use App\Http\Controllers\Teacher\StudentController;
use App\Http\Controllers\Admin\ErrorLogController;
use App\Http\Controllers\Teacher\MeetingController as TeacherMeetingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Student\CourseController as StudentCourseController;
use App\Http\Controllers\Student\LessonController as StudentLessonController;
use App\Http\Controllers\Student\AnnouncementController as StudentAnnouncementController;
use App\Http\Controllers\Student\MeetingController as StudentMeetingController;
use App\Http\Controllers\MeetingRoomController;
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

Route::get('/admin', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])
    ->middleware(['auth', 'role:admin'])
    ->name('admin.dashboard');
Route::get('/admin/planes', [\App\Http\Controllers\Admin\PlanController::class, 'index'])
    ->middleware(['auth', 'role:admin'])
    ->name('admin.plans.index');
Route::put('/admin/planes/{user}', [\App\Http\Controllers\Admin\PlanController::class, 'update'])
    ->middleware(['auth', 'role:admin'])
    ->name('admin.plans.update');
Route::get('/admin/errores', [ErrorLogController::class, 'index'])
    ->middleware(['auth', 'role:admin'])
    ->name('admin.errors.index');
Route::get('/admin/errores/{errorLog}', [ErrorLogController::class, 'show'])
    ->middleware(['auth', 'role:admin'])
    ->name('admin.errors.show');
Route::get('/profesor', [\App\Http\Controllers\Teacher\DashboardController::class, 'index'])
    ->middleware(['auth', 'role:teacher'])
    ->name('teacher.dashboard');
Route::get('/alumno', [\App\Http\Controllers\Student\DashboardController::class, 'index'])
    ->middleware(['auth', 'role:student'])
    ->name('student.dashboard');

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
    Route::get('/alumnos', [StudentController::class, 'index'])->name('students.index');
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
    Route::get('/perfil', [ProfileController::class, 'edit'])
        ->defaults('update_route', 'teacher.profile.update')
        ->name('profile.edit');
    Route::put('/perfil', [ProfileController::class, 'update'])->name('profile.update');
});

Route::middleware(['auth', 'role:student'])->prefix('alumno')->name('student.')->group(function () {
    Route::get('/cursos', [StudentCourseController::class, 'index'])->name('courses.index');
    Route::get('/cursos/{course}', [StudentCourseController::class, 'show'])->name('courses.show');
    Route::get('/lecciones/{lesson}', [StudentLessonController::class, 'show'])->name('lessons.show');
    Route::post('/lecciones/{lesson}/completada', [StudentLessonController::class, 'toggleCompletion'])->name('lessons.toggleCompletion');
    Route::post('/anuncios/{announcement}/leido', [StudentAnnouncementController::class, 'markRead'])->name('announcements.read');
    Route::get('/notificaciones', [NotificationController::class, 'index'])->name('notifications.index');
    Route::delete('/notificaciones/{notificationId}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::delete('/notificaciones', [NotificationController::class, 'destroyAll'])->name('notifications.destroyAll');
    Route::get('/perfil', [\App\Http\Controllers\Student\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/perfil', [\App\Http\Controllers\Student\ProfileController::class, 'update'])->name('profile.update');
    Route::get('/reuniones', [StudentMeetingController::class, 'index'])->name('meetings.index');
    Route::get('/reuniones/{meeting}', [MeetingRoomController::class, 'show'])->name('meetings.show');
});

Route::get('/admin/perfil', [ProfileController::class, 'edit'])
    ->defaults('update_route', 'admin.profile.update')
    ->middleware(['auth', 'role:admin'])
    ->name('admin.profile.edit');
Route::put('/admin/perfil', [ProfileController::class, 'update'])
    ->middleware(['auth', 'role:admin'])
    ->name('admin.profile.update');
