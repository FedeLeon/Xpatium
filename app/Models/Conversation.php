<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Message;
use App\Models\User;

class Conversation extends Model
{
    use HasFactory;

    public const TYPE_DIRECT = 'direct';
    public const TYPE_GROUP = 'group';

    protected $fillable = [
        'type',
        'title',
        'created_by',
    ];

    public function participants()
    {
        return $this->belongsToMany(User::class, 'conversation_user')
            ->withPivot('last_read_at');
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }
}
