<?php

namespace App\Notifications;

use App\Models\Course;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class CourseAccessNotification extends Notification
{
    use Queueable;

    public function __construct(private User $teacher, private Course $course)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $teacherName = trim($this->teacher->name.' '.$this->teacher->last_name);

        return [
            'message' => 'El lider '.$teacherName.' te dio acceso al contenido de ayuda '.$this->course->name.'.',
            'teacher_id' => $this->teacher->id,
            'course_id' => $this->course->id,
        ];
    }
}
