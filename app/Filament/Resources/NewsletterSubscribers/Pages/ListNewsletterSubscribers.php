<?php

namespace App\Filament\Resources\NewsletterSubscribers\Pages;

use App\Filament\Resources\NewsletterSubscribers\NewsletterSubscriberResource;
use App\Models\NewsletterSubscriber;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Collection;

class ListNewsletterSubscribers extends ListRecords
{
    protected static string $resource = NewsletterSubscriberResource::class;

    protected string $view = 'filament.resources.newsletter-subscribers.pages.list-newsletter-subscribers';

    /**
     * @return array{total: int, subscribed: int, pending: int, unsubscribed: int, this_week: int}
     */
    #[\NoDiscard]
    public function overviewStats(): array
    {
        return [
            'total' => NewsletterSubscriber::query()->count(),
            'subscribed' => NewsletterSubscriber::query()->where('status', 'subscribed')->count(),
            'pending' => NewsletterSubscriber::query()->where('status', 'pending')->count(),
            'unsubscribed' => NewsletterSubscriber::query()->where('status', 'unsubscribed')->count(),
            'this_week' => NewsletterSubscriber::query()
                ->where('created_at', '>=', now()->startOfWeek())
                ->count(),
        ];
    }

    /**
     * @return Collection<int, NewsletterSubscriber>
     */
    #[\NoDiscard]
    public function recentSubscribers(): Collection
    {
        return NewsletterSubscriber::query()
            ->latest()
            ->limit(6)
            ->get();
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
