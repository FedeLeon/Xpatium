<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LessonQuiz extends Model
{
    use HasFactory;

    protected $fillable = [
        'lesson_id',
        'title',
        'description',
    ];

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function questions()
    {
        return $this->hasMany(LessonQuizQuestion::class, 'quiz_id')->orderBy('position');
    }

    public function attempts()
    {
        return $this->hasMany(LessonQuizAttempt::class, 'quiz_id');
    }
}
