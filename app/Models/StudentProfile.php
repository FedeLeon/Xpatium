<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class StudentProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'student_id',
        'status',
        'has_children',
        'children_count',
        'sentimental_status',
        'family_situation',
        'current_challenges',
        'leader_notes',
        'pastoral_updated_at',
    ];

    protected $casts = [
        'has_children' => 'boolean',
        'pastoral_updated_at' => 'datetime',
    ];

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
