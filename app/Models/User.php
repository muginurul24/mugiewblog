<?php

namespace App\Models;

use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Filament\Auth\MultiFactor\App\Concerns\InteractsWithAppAuthentication;
use Filament\Auth\MultiFactor\App\Concerns\InteractsWithAppAuthenticationRecovery;
use Filament\Auth\MultiFactor\App\Contracts\HasAppAuthentication;
use Filament\Auth\MultiFactor\App\Contracts\HasAppAuthenticationRecovery;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable([
    'name',
    'username',
    'email',
    'password',
    'avatar',
    'bio',
    'github_url',
    'twitter_url',
    'website_url',
    'oauth_provider',
    'oauth_provider_id',
    'role',
    'is_active',
    'two_factor_enabled',
    'two_factor_secret',
    'app_authentication_secret',
    'app_authentication_recovery_codes',
])]
#[Hidden(['password', 'remember_token', 'two_factor_secret', 'app_authentication_secret', 'app_authentication_recovery_codes'])]
class User extends Authenticatable implements FilamentUser, HasAppAuthentication, HasAppAuthenticationRecovery, MustVerifyEmailContract
{
    /** @use HasFactory<UserFactory> */
    use HasFactory;

    use InteractsWithAppAuthentication;
    use InteractsWithAppAuthenticationRecovery;
    use MustVerifyEmail;
    use Notifiable;
    use SoftDeletes;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'is_active' => 'boolean',
            'two_factor_enabled' => 'boolean',
            'app_authentication_recovery_codes' => 'encrypted:array',
        ];
    }

    /**
     * @return HasMany<Article, $this>
     */
    #[\NoDiscard]
    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }

    /**
     * @return HasMany<Comment, $this>
     */
    #[\NoDiscard]
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * @return HasMany<Bookmark, $this>
     */
    #[\NoDiscard]
    public function bookmarks(): HasMany
    {
        return $this->hasMany(Bookmark::class);
    }

    /**
     * @return HasMany<Media, $this>
     */
    #[\NoDiscard]
    public function media(): HasMany
    {
        return $this->hasMany(Media::class);
    }

    /**
     * Check if the user has the admin role.
     */
    #[\NoDiscard]
    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin;
    }

    /**
     * Check if the user has the editor role.
     */
    #[\NoDiscard]
    public function isEditor(): bool
    {
        return $this->role === UserRole::Editor;
    }

    /**
     * Check if the user has the author role.
     */
    #[\NoDiscard]
    public function isAuthor(): bool
    {
        return $this->role === UserRole::Author;
    }

    /**
     * Check if the user can access the Filament admin panel.
     */
    #[\NoDiscard]
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_active && ($this->isAdmin() || $this->isEditor());
    }
}
