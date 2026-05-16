<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

new class extends Component
{
    public string $email = '';

    public string $password = '';

    public bool $remember = false;

    /**
     * @throws ValidationException
     */
    public function login(): mixed
    {
        $validated = $this->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'remember' => ['boolean'],
        ]);

        $key = $this->throttleKey();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            throw ValidationException::withMessages([
                'email' => 'Terlalu banyak percobaan. Coba lagi dalam '.RateLimiter::availableIn($key).' detik.',
            ]);
        }

        if (! Auth::attempt([
            'email' => Str::lower($validated['email']),
            'password' => $validated['password'],
            'is_active' => true,
        ], $validated['remember'])) {
            RateLimiter::hit($key, 60);

            throw ValidationException::withMessages([
                'email' => 'Email atau password tidak sesuai.',
            ]);
        }

        RateLimiter::clear($key);
        session()->regenerate();

        return redirect()->intended(route('home'));
    }

    private function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->email).'|'.request()->ip());
    }
};
?>

<div>
    <x-slot:title>Masuk — MugiewBlog</x-slot:title>
    <x-slot:metaDescription>Masuk ke akun MugiewBlog.</x-slot:metaDescription>
    <x-slot:canonical>{{ route('login') }}</x-slot:canonical>

    <section class="page-hero hero-grid">
        <div class="mx-auto grid min-h-[calc(100vh-12rem)] max-w-7xl items-center gap-10 px-4 py-10 sm:px-6 lg:grid-cols-[minmax(0,1fr)_420px] lg:px-8">
            <div class="max-w-2xl">
                <p class="eyebrow">
                    <i class="fas fa-user-shield h-3.5 w-3.5" aria-hidden="true"></i>
                    Akun pembaca
                </p>
                <h1 class="mt-5 font-display text-4xl font-bold leading-tight sm:text-5xl">Masuk untuk ikut berdiskusi tanpa kehilangan konteks.</h1>
                <p class="mt-5 text-lg leading-8 text-surface-600 dark:text-surface-300">Profil terverifikasi menjaga komentar tetap kredibel, menyimpan identitas pembaca, dan memberi dasar yang rapi untuk fitur personalisasi berikutnya.</p>

                <div class="mt-8 grid gap-3 sm:grid-cols-2">
                    <div class="auth-benefit">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-accent-muted text-accent">
                            <i class="fas fa-comments h-4 w-4" aria-hidden="true"></i>
                        </span>
                        <div>
                            <p class="font-semibold">Komentar terhubung</p>
                            <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">Riwayat diskusi tetap melekat ke profil.</p>
                        </div>
                    </div>
                    <div class="auth-benefit">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-accent-muted text-accent">
                            <i class="fas fa-shield-halved h-4 w-4" aria-hidden="true"></i>
                        </span>
                        <div>
                            <p class="font-semibold">Verifikasi email</p>
                            <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">Interaksi pembaca lebih aman dan tertib.</p>
                        </div>
                    </div>
                </div>
            </div>

            <form wire:submit="login" class="form-panel p-6">
            @if (session('status'))
                <p class="mb-4 rounded-lg bg-accent-muted px-3 py-2 text-sm font-medium text-accent">{{ session('status') }}</p>
            @endif

            <div>
                <label for="email" class="mb-1 block text-sm font-semibold">Email</label>
                <input id="email" wire:model="email" type="email" autocomplete="email" class="h-11 w-full rounded-lg border-surface-200 focus:border-accent focus:ring-accent/30 dark:border-surface-800 dark:bg-surface-950">
                @error('email') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
            </div>

            <div class="mt-4">
                <label for="password" class="mb-1 block text-sm font-semibold">Password</label>
                <input id="password" wire:model="password" type="password" autocomplete="current-password" class="h-11 w-full rounded-lg border-surface-200 focus:border-accent focus:ring-accent/30 dark:border-surface-800 dark:bg-surface-950">
                @error('password') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
            </div>

            <div class="mt-4 flex items-center justify-between gap-3 text-sm">
                <label class="inline-flex items-center gap-2 font-medium text-surface-600 dark:text-surface-300">
                    <input wire:model="remember" type="checkbox" class="rounded border-surface-300 text-accent focus:ring-accent/30 dark:border-surface-700">
                    Ingat saya
                </label>
                <a href="{{ route('password.request') }}" wire:navigate class="font-semibold text-accent hover:text-accent-hover">Lupa password?</a>
            </div>

            <button type="submit" wire:loading.attr="disabled" class="mt-6 inline-flex h-11 w-full items-center justify-center gap-2 rounded-lg bg-accent px-4 text-sm font-bold text-white transition hover:bg-accent-hover disabled:opacity-60">
                <i class="fas fa-right-to-bracket h-4 w-4" aria-hidden="true"></i>
                Masuk
            </button>

            <div class="mt-4 grid gap-2 sm:grid-cols-2">
                <a href="{{ route('oauth.redirect', 'github') }}" class="inline-flex h-10 items-center justify-center gap-2 rounded-lg border border-surface-200 text-sm font-semibold transition hover:border-accent hover:text-accent dark:border-surface-800">
                    <i class="fab fa-github h-4 w-4" aria-hidden="true"></i>
                    GitHub
                </a>
                <a href="{{ route('oauth.redirect', 'google') }}" class="inline-flex h-10 items-center justify-center gap-2 rounded-lg border border-surface-200 text-sm font-semibold transition hover:border-accent hover:text-accent dark:border-surface-800">
                    <i class="fab fa-google h-4 w-4" aria-hidden="true"></i>
                    Google
                </a>
            </div>

            <p class="mt-5 text-center text-sm text-surface-500 dark:text-surface-400">
                Belum punya akun?
                <a href="{{ route('register') }}" wire:navigate class="font-semibold text-accent hover:text-accent-hover">Daftar</a>
            </p>
            </form>
        </div>
    </section>
</div>
