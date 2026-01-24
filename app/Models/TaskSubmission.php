<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'student_id',
        'content',
        'file_path',
        'submitted_at',
        'corrected_at',
        'feedback',
        'score',
    ];

    protected $casts = [
        'content' => 'array',
        'submitted_at' => 'datetime',
        'corrected_at' => 'datetime',
        'score' => 'decimal:2',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
