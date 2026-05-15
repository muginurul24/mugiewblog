<?php

namespace App\Models;

use Database\Factories\NewsletterSubscriberFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

#[Fillable(['email', 'name', 'status', 'source', 'verification_token', 'verified_at', 'subscribed_at', 'unsubscribed_at'])]
class NewsletterSubscriber extends Model
{
    /** @use HasFactory<NewsletterSubscriberFactory> */
    use HasFactory;

    protected static function booted(): void
    {
        static::creating(function (self $subscriber): void {
            $subscriber->verification_token ??= Str::random(48);
        });
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'verified_at' => 'datetime',
            'subscribed_at' => 'datetime',
            'unsubscribed_at' => 'datetime',
        ];
    }

    #[\NoDiscard]
    public function isSubscribed(): bool
    {
        return $this->status === 'subscribed' && $this->unsubscribed_at === null;
    }
}
