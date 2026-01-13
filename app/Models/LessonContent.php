<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Asset;
use App\Models\Lesson;

class LessonContent extends Model
{
    use HasFactory;

    protected $fillable = [
        'lesson_id',
        'type',
        'asset_id',
        'text_content',
    ];

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }
}
