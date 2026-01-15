<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ErrorLog extends Model
{
    protected $fillable = [
        'level',
        'exception_class',
        'message',
        'file',
        'line',
        'status_code',
        'url',
        'method',
        'user_id',
        'ip',
        'user_agent',
        'trace',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
