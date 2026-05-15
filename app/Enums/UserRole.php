<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Editor = 'editor';
    case Author = 'author';
    case User = 'user';

    #[\NoDiscard]
    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Admin',
            self::Editor => 'Editor',
            self::Author => 'Author',
            self::User => 'User',
        };
    }

    #[\NoDiscard]
    public function color(): string
    {
        return match ($this) {
            self::Admin => 'danger',
            self::Editor => 'warning',
            self::Author => 'info',
            self::User => 'gray',
        };
    }

    /**
     * @return array<string, string>
     */
    #[\NoDiscard]
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $role): array => [$role->value => $role->label()])
            ->all();
    }
}
