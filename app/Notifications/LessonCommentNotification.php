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
        $actor = $this->comment->user?->name ?? 'Alguien';
        $parentId = $this->comment->parent_id;

        return [
            'message' => $parentId
                ? $actor.' respondio a tu comentario en '.$lesson->title.'.'
                : 'Nuevo comentario en '.$lesson->title.' ('.$course->name.').',
            'lesson_id' => $lesson->id,
            'course_id' => $course->id,
            'comment_id' => $this->comment->id,
            'parent_id' => $parentId,
        ];
    }
}
