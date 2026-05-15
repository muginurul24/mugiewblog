<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

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
        ];
    }

    /**
     * Check if the user has the admin role.
     */
    #[\NoDiscard]
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if the user has the editor role.
     */
    #[\NoDiscard]
    public function isEditor(): bool
    {
        return $this->role === 'editor';
    }

    /**
     * Check if the user has the author role.
     */
    #[\NoDiscard]
    public function isAuthor(): bool
    {
        return $this->role === 'author';
    }

    /**
     * Check if the user can access the Filament admin panel.
     */
    #[\NoDiscard]
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->isAdmin();
    }
}
