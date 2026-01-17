<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LessonQuizQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_id',
        'prompt',
        'position',
    ];

    public function quiz()
    {
        return $this->belongsTo(LessonQuiz::class, 'quiz_id');
    }

    public function options()
    {
        return $this->hasMany(LessonQuizOption::class, 'question_id')->orderBy('position');
    }
}
