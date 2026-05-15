<?php

namespace App\Notifications;

use App\Models\NewsletterSubscriber;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class ConfirmNewsletterSubscription extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public NewsletterSubscriber $subscriber)
    {
        $this->onQueue('notifications');
        $this->afterCommit();
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $confirmUrl = URL::temporarySignedRoute(
            'newsletter.confirm',
            now()->addDays(7),
            ['subscriber' => $this->subscriber, 'token' => $this->subscriber->verification_token],
        );

        return (new MailMessage)
            ->subject('Konfirmasi newsletter MugiewBlog')
            ->line('Klik tombol di bawah untuk mengaktifkan newsletter MugiewBlog.')
            ->action('Konfirmasi newsletter', $confirmUrl)
            ->line('Link ini berlaku selama 7 hari.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'subscriber_id' => $this->subscriber->id,
            'email' => $this->subscriber->email,
        ];
    }
}
