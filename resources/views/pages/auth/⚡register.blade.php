<?php

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

new class extends Component
{
    public string $name = '';

    public string $username = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    /**
     * @throws ValidationException
     */
    public function register(): mixed
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:120'],
            'username' => ['required', 'alpha_dash:ascii', 'max:60', 'unique:users,username'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $key = 'register:'.sha1(request()->ip().'|'.Str::lower($validated['email']));

        if (RateLimiter::tooManyAttempts($key, 3)) {
            throw ValidationException::withMessages([
                'email' => 'Terlalu banyak pendaftaran dari alamat ini. Coba lagi nanti.',
            ]);
        }

        RateLimiter::hit($key, 3600);

        $user = User::create([
            'name' => Str::squish($validated['name']),
            'username' => Str::lower($validated['username']),
            'email' => Str::lower($validated['email']),
            'password' => $validated['password'],
            'role' => UserRole::User,
            'is_active' => true,
        ]);

        event(new Registered($user));
        Auth::login($user);
        session()->regenerate();

        return redirect()->route('verification.notice');
    }
};
?>

<div>
    <x-slot:title>Daftar — MugiewBlog</x-slot:title>
    <x-slot:metaDescription>Buat akun pembaca MugiewBlog.</x-slot:metaDescription>
    <x-slot:canonical>{{ route('register') }}</x-slot:canonical>

    <section class="mx-auto grid min-h-[calc(100vh-12rem)] max-w-7xl items-center gap-10 px-4 py-10 sm:px-6 lg:grid-cols-[1fr_460px] lg:px-8">
        <div class="max-w-2xl">
            <p class="text-sm font-bold uppercase tracking-wide text-accent">Reader Account</p>
            <h1 class="mt-3 font-display text-4xl font-bold leading-tight sm:text-5xl">Buat akun pembaca yang terverifikasi.</h1>
            <p class="mt-5 text-lg leading-8 text-surface-600 dark:text-surface-300">Akun dipakai untuk interaksi pembaca, komentar terhubung profil, dan pengembangan fitur personalisasi berikutnya.</p>
        </div>

        <form wire:submit="register" class="rounded-lg border border-surface-200 bg-white p-6 shadow-sm dark:border-surface-800 dark:bg-surface-900">
            <div>
                <label for="name" class="mb-1 block text-sm font-semibold">Nama</label>
                <input id="name" wire:model="name" type="text" autocomplete="name" class="h-11 w-full rounded-lg border-surface-200 focus:border-accent focus:ring-accent/30 dark:border-surface-800 dark:bg-surface-950">
                @error('name') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
            </div>

            <div class="mt-4">
                <label for="username" class="mb-1 block text-sm font-semibold">Username</label>
                <input id="username" wire:model="username" type="text" autocomplete="username" class="h-11 w-full rounded-lg border-surface-200 focus:border-accent focus:ring-accent/30 dark:border-surface-800 dark:bg-surface-950">
                @error('username') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
            </div>

            <div class="mt-4">
                <label for="email" class="mb-1 block text-sm font-semibold">Email</label>
                <input id="email" wire:model="email" type="email" autocomplete="email" class="h-11 w-full rounded-lg border-surface-200 focus:border-accent focus:ring-accent/30 dark:border-surface-800 dark:bg-surface-950">
                @error('email') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
            </div>

            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="password" class="mb-1 block text-sm font-semibold">Password</label>
                    <input id="password" wire:model="password" type="password" autocomplete="new-password" class="h-11 w-full rounded-lg border-surface-200 focus:border-accent focus:ring-accent/30 dark:border-surface-800 dark:bg-surface-950">
                    @error('password') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="password_confirmation" class="mb-1 block text-sm font-semibold">Konfirmasi</label>
                    <input id="password_confirmation" wire:model="password_confirmation" type="password" autocomplete="new-password" class="h-11 w-full rounded-lg border-surface-200 focus:border-accent focus:ring-accent/30 dark:border-surface-800 dark:bg-surface-950">
                </div>
            </div>

            <button type="submit" wire:loading.attr="disabled" class="mt-6 inline-flex h-11 w-full items-center justify-center gap-2 rounded-lg bg-accent px-4 text-sm font-bold text-white transition hover:bg-accent-hover disabled:opacity-60">
                <i class="fas fa-user-plus h-4 w-4" aria-hidden="true"></i>
                Daftar
            </button>

            <p class="mt-5 text-center text-sm text-surface-500 dark:text-surface-400">
                Sudah punya akun?
                <a href="{{ route('login') }}" wire:navigate class="font-semibold text-accent hover:text-accent-hover">Masuk</a>
            </p>
        </form>
    </section>
</div>
