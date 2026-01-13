<?php

namespace App\Providers;

use App\Models\Conversation;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view) {
            $user = auth()->user();
            if (! $user) {
                return;
            }

            $notificationUnreadCount = $user->unreadNotifications()->count();

            $chatUnreadCount = 0;
            $conversations = Conversation::whereHas('participants', function ($query) use ($user) {
                $query->where('users.id', $user->id);
            })->with([
                'participants' => function ($query) use ($user) {
                    $query->where('users.id', $user->id);
                },
                'messages' => function ($query) {
                    $query->latest()->limit(1);
                },
            ])->get();

            foreach ($conversations as $conversation) {
                $lastMessage = $conversation->messages->first();
                $pivot = $conversation->participants->first()?->pivot;
                if (! $lastMessage) {
                    continue;
                }
                if ($lastMessage->user_id === $user->id) {
                    continue;
                }
                if (! $pivot?->last_read_at || $lastMessage->created_at->gt($pivot->last_read_at)) {
                    $chatUnreadCount++;
                }
            }

            $view->with('notificationUnreadCount', $notificationUnreadCount);
            $view->with('chatUnreadCount', $chatUnreadCount);
        });
    }
}
