<?php

namespace App\Listeners;

use App\Enums\UserRole;
use App\Events\CommentCreated;
use App\Models\User;
use App\Notifications\NewCommentNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendCommentNotifications implements ShouldQueue
{
    public string $queue = 'notifications';

    /**
     * Handle the event.
     */
    public function handle(CommentCreated $event): void
    {
        User::query()
            ->where('is_active', true)
            ->whereIn('role', [UserRole::Admin->value, UserRole::Editor->value])
            ->each(fn (User $user) => $user->notify(new NewCommentNotification($event->comment)));
    }
}
