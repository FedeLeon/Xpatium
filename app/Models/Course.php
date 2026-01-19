<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\CourseModule;
use App\Models\Lesson;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'subtitle',
        'description',
        'price_ars',
        'price_usd',
        'start_date',
        'image_path',
        'duration',
        'level',
        'payment_link',
        'landing_custom_text',
        'landing_custom_image_path',
        'landing_video_url',
    ];

    protected $casts = [
        'start_date' => 'date',
    ];

    public function teacher()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function modules()
    {
        return $this->hasMany(CourseModule::class);
    }

    public function lessons()
    {
        return $this->hasManyThrough(Lesson::class, CourseModule::class, 'course_id', 'module_id');
    }

    public function students()
    {
        return $this->belongsToMany(User::class, 'course_user');
    }
}
