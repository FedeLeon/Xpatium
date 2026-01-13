<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\CourseModule;

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
}
