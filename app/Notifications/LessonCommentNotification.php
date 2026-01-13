<?php

namespace App\Notifications;

use App\Models\LessonComment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class LessonCommentNotification extends Notification
{
    use Queueable;

    public function __construct(private LessonComment $comment)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $lesson = $this->comment->lesson;
        $course = $lesson->module->course;

        return [
            'message' => 'Nuevo comentario en '.$lesson->title.' ('.$course->name.').',
            'lesson_id' => $lesson->id,
            'course_id' => $course->id,
        ];
    }
}
