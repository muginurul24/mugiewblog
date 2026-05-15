<?php

namespace App\Notifications;

use App\Models\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewCommentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Comment $comment)
    {
        $this->comment->loadMissing(['article', 'author']);
        $this->afterCommit();
        $this->onQueue('notifications');
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Komentar baru menunggu moderasi')
            ->line('Ada komentar baru pada artikel "'.$this->comment->article?->title.'".')
            ->line($this->comment->guest_name ?: $this->comment->author?->name ?: 'Pembaca')
            ->action('Buka komentar', route('filament.backoffice.resources.comments.view', ['record' => $this->comment]));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'comment_id' => $this->comment->id,
            'article_id' => $this->comment->article_id,
            'article_title' => $this->comment->article?->title,
            'commenter' => $this->comment->guest_name ?: $this->comment->author?->name,
            'excerpt' => str($this->comment->content)->limit(140)->toString(),
        ];
    }
}
