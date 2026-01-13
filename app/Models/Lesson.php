<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\CourseModule;
use App\Models\LessonContent;

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
}
