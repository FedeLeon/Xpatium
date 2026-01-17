<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LessonQuizAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'attempt_id',
        'question_id',
        'option_id',
        'is_correct',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
    ];

    public function attempt()
    {
        return $this->belongsTo(LessonQuizAttempt::class, 'attempt_id');
    }

    public function question()
    {
        return $this->belongsTo(LessonQuizQuestion::class, 'question_id');
    }

    public function option()
    {
        return $this->belongsTo(LessonQuizOption::class, 'option_id');
    }
}
