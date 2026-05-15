<?php

namespace App\Enums;

enum ArticleStatus: string
{
    case Draft = 'draft';
    case Review = 'review';
    case Published = 'published';
    case Scheduled = 'scheduled';

    #[\NoDiscard]
    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Review => 'Review',
            self::Published => 'Published',
            self::Scheduled => 'Scheduled',
        };
    }

    #[\NoDiscard]
    public function color(): string
    {
        return match ($this) {
            self::Draft => 'gray',
            self::Review => 'warning',
            self::Published => 'success',
            self::Scheduled => 'info',
        };
    }

    /**
     * @return array<string, string>
     */
    #[\NoDiscard]
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $status): array => [$status->value => $status->label()])
            ->all();
    }
}
