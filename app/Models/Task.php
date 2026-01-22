<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'task_type',
        'due_date',
        'commission_id',
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    public function items()
    {
        return $this->hasMany(TaskItem::class);
    }

    public function commission()
    {
        return $this->belongsTo(Commission::class);
    }

    public function students()
    {
        return $this->belongsToMany(User::class, 'task_student', 'task_id', 'student_id');
    }
}
