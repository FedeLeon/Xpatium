<?php

use App\Models\Conversation;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('presence-online', function ($user) {
    return [
        'id' => $user->id,
        'name' => $user->name,
        'last_name' => $user->last_name,
    ];
});

Broadcast::channel('chat.{conversation}', function ($user, Conversation $conversation) {
    return $conversation->participants->contains($user->id);
});
