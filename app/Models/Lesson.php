<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\CourseModule;
use App\Models\LessonContent;
use App\Models\LessonComment;
use App\Models\LessonQuiz;
use App\Models\User;

class Lesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'module_id',
        'title',
        'description',
    ];

    public function module()
    {
        return $this->belongsTo(CourseModule::class, 'module_id');
    }

    public function contents()
    {
        return $this->hasMany(LessonContent::class);
    }

    public function comments()
    {
        return $this->hasMany(LessonComment::class);
    }

    public function completedBy()
    {
        return $this->belongsToMany(User::class, 'lesson_user')
            ->withPivot('completed_at')
            ->withTimestamps();
    }

    public function quiz()
    {
        return $this->hasOne(LessonQuiz::class);
    }
}
