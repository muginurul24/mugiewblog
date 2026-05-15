<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class SocialiteController extends Controller
{
    /**
     * @var array<int, string>
     */
    private const PROVIDERS = ['github', 'google'];

    public function redirect(string $provider): RedirectResponse
    {
        abort_unless(in_array($provider, self::PROVIDERS, true), 404);

        if (blank(config("services.{$provider}.client_id")) || blank(config("services.{$provider}.client_secret"))) {
            return redirect()
                ->route('login')
                ->with('status', ucfirst($provider).' login belum dikonfigurasi.');
        }

        return Socialite::driver($provider)->redirect();
    }

    public function callback(string $provider): RedirectResponse
    {
        abort_unless(in_array($provider, self::PROVIDERS, true), 404);

        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (Throwable $exception) {
            report($exception);

            return redirect()
                ->route('login')
                ->with('status', ucfirst($provider).' login dibatalkan atau gagal.');
        }
        $email = Str::lower((string) $socialUser->getEmail());

        abort_if(blank($email), 422, 'Provider tidak mengirim alamat email.');

        $user = User::query()
            ->where('oauth_provider', $provider)
            ->where('oauth_provider_id', $socialUser->getId())
            ->first();

        if ($user === null) {
            $user = User::query()->firstOrNew(['email' => $email]);
            $user->forceFill([
                'name' => $socialUser->getName() ?: $socialUser->getNickname() ?: Str::before($email, '@'),
                'username' => $user->username ?: $this->uniqueUsername($socialUser->getNickname() ?: Str::before($email, '@')),
                'password' => $user->password ?: Str::password(48),
                'avatar' => $socialUser->getAvatar() ?: $user->avatar,
                'oauth_provider' => $provider,
                'oauth_provider_id' => $socialUser->getId(),
                'role' => $user->role ?? UserRole::User,
                'is_active' => $user->is_active ?? true,
                'email_verified_at' => $user->email_verified_at ?? now(),
            ])->save();
        }

        Auth::login($user, remember: true);
        request()->session()->regenerate();

        return redirect()->intended(route('home'));
    }

    private function uniqueUsername(string $candidate): string
    {
        $base = Str::slug($candidate) ?: 'reader';
        $username = $base;
        $suffix = 2;

        while (User::query()->where('username', $username)->exists()) {
            $username = "{$base}-{$suffix}";
            $suffix++;
        }

        return $username;
    }
}
