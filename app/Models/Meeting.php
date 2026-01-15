<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Meeting extends Model
{
    use HasFactory;

    public const TYPE_INDIVIDUAL = 'individual';
    public const TYPE_GROUP = 'group';

    protected $fillable = [
        'teacher_id',
        'title',
        'description',
        'meeting_type',
        'scheduled_for',
        'duration_minutes',
        'daily_room_name',
        'daily_room_url',
    ];

    protected $casts = [
        'scheduled_for' => 'datetime',
    ];

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function attendees()
    {
        return $this->belongsToMany(User::class, 'meeting_user')
            ->withTimestamps();
    }
}
