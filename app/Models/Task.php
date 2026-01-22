<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'task_type',
    ];

    public function items()
    {
        return $this->hasMany(TaskItem::class);
    }
}
