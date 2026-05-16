<?php

use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;

new class extends Component
{
    public string $name = '';

    public string $username = '';

    public string $bio = '';

    public string $github_url = '';

    public string $twitter_url = '';

    public string $website_url = '';

    public string $current_password = '';

    public string $password = '';

    public string $password_confirmation = '';

    public function mount(): void
    {
        $user = $this->user();

        $this->name = $user->name;
        $this->username = (string) $user->username;
        $this->bio = (string) $user->bio;
        $this->github_url = (string) $user->github_url;
        $this->twitter_url = (string) $user->twitter_url;
        $this->website_url = (string) $user->website_url;
    }

    public function saveProfile(): void
    {
        $user = $this->user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:120'],
            'username' => ['required', 'alpha_dash:ascii', 'max:60', Rule::unique('users', 'username')->ignore($user)],
            'bio' => ['nullable', 'string', 'max:1000'],
            'github_url' => ['nullable', 'url', 'max:255'],
            'twitter_url' => ['nullable', 'url', 'max:255'],
            'website_url' => ['nullable', 'url', 'max:255'],
        ]);

        $user->update($validated);
        session()->flash('profile_status', 'Profil disimpan.');
    }

    public function updatePassword(): void
    {
        $validated = $this->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $this->user()->update([
            'password' => $validated['password'],
            'remember_token' => str()->random(60),
        ]);

        $this->reset(['current_password', 'password', 'password_confirmation']);
        session()->flash('password_status', 'Password diperbarui.');
    }

    private function user(): User
    {
        return auth()->user();
    }
};
?>

<div>
    <x-slot:title>Profil — MugiewBlog</x-slot:title>
    <x-slot:metaDescription>Kelola profil akun MugiewBlog.</x-slot:metaDescription>
    <x-slot:canonical>{{ route('profile') }}</x-slot:canonical>

    <section class="page-hero hero-grid">
        <div class="mx-auto max-w-6xl px-4 py-10 sm:px-6 lg:px-8">
            <p class="eyebrow">
                <i class="fas fa-id-card h-3.5 w-3.5" aria-hidden="true"></i>
                Profil pembaca
            </p>
            <div class="mt-5 flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h1 class="font-display text-4xl font-bold">{{ auth()->user()->name }}</h1>
                    <p class="mt-2 text-surface-600 dark:text-surface-300">{{ auth()->user()->email }}</p>
                </div>
                <span class="metadata-pill w-fit">
                    <i class="fas fa-circle-check h-3.5 w-3.5" aria-hidden="true"></i>
                    {{ auth()->user()->hasVerifiedEmail() ? 'Email terverifikasi' : 'Menunggu verifikasi email' }}
                </span>
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-6xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_360px]">
            <form wire:submit="saveProfile" class="form-panel p-6">
                @if (session('profile_status'))
                    <p class="mb-4 rounded-lg bg-accent-muted px-3 py-2 text-sm font-medium text-accent">{{ session('profile_status') }}</p>
                @endif

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="name" class="mb-1 block text-sm font-semibold">Nama</label>
                        <input id="name" wire:model="name" type="text" class="h-11 w-full rounded-lg border-surface-200 focus:border-accent focus:ring-accent/30 dark:border-surface-800 dark:bg-surface-950">
                        @error('name') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="username" class="mb-1 block text-sm font-semibold">Username</label>
                        <input id="username" wire:model="username" type="text" class="h-11 w-full rounded-lg border-surface-200 focus:border-accent focus:ring-accent/30 dark:border-surface-800 dark:bg-surface-950">
                        @error('username') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="mt-4">
                    <label for="bio" class="mb-1 block text-sm font-semibold">Bio</label>
                    <textarea id="bio" wire:model="bio" rows="5" class="w-full rounded-lg border-surface-200 focus:border-accent focus:ring-accent/30 dark:border-surface-800 dark:bg-surface-950"></textarea>
                    @error('bio') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </div>

                <div class="mt-4 grid gap-4 sm:grid-cols-3">
                    <div>
                        <label for="github_url" class="mb-1 block text-sm font-semibold">GitHub</label>
                        <input id="github_url" wire:model="github_url" type="url" class="h-11 w-full rounded-lg border-surface-200 focus:border-accent focus:ring-accent/30 dark:border-surface-800 dark:bg-surface-950">
                        @error('github_url') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="twitter_url" class="mb-1 block text-sm font-semibold">X/Twitter</label>
                        <input id="twitter_url" wire:model="twitter_url" type="url" class="h-11 w-full rounded-lg border-surface-200 focus:border-accent focus:ring-accent/30 dark:border-surface-800 dark:bg-surface-950">
                        @error('twitter_url') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="website_url" class="mb-1 block text-sm font-semibold">Website</label>
                        <input id="website_url" wire:model="website_url" type="url" class="h-11 w-full rounded-lg border-surface-200 focus:border-accent focus:ring-accent/30 dark:border-surface-800 dark:bg-surface-950">
                        @error('website_url') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>
                </div>

                <button type="submit" wire:loading.attr="disabled" class="mt-6 inline-flex h-11 items-center justify-center gap-2 rounded-lg bg-accent px-4 text-sm font-bold text-white transition hover:bg-accent-hover disabled:opacity-60">
                    <i class="fas fa-floppy-disk h-4 w-4" aria-hidden="true"></i>
                    Simpan profil
                </button>
            </form>

            <form wire:submit="updatePassword" class="form-panel p-6">
                <h2 class="font-display text-xl font-bold">Password</h2>

                @if (session('password_status'))
                    <p class="mt-4 rounded-lg bg-accent-muted px-3 py-2 text-sm font-medium text-accent">{{ session('password_status') }}</p>
                @endif

                <div class="mt-5">
                    <label for="current_password" class="mb-1 block text-sm font-semibold">Password saat ini</label>
                    <input id="current_password" wire:model="current_password" type="password" autocomplete="current-password" class="h-11 w-full rounded-lg border-surface-200 focus:border-accent focus:ring-accent/30 dark:border-surface-800 dark:bg-surface-950">
                    @error('current_password') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </div>

                <div class="mt-4">
                    <label for="new_password" class="mb-1 block text-sm font-semibold">Password baru</label>
                    <input id="new_password" wire:model="password" type="password" autocomplete="new-password" class="h-11 w-full rounded-lg border-surface-200 focus:border-accent focus:ring-accent/30 dark:border-surface-800 dark:bg-surface-950">
                    @error('password') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </div>

                <div class="mt-4">
                    <label for="password_confirmation" class="mb-1 block text-sm font-semibold">Konfirmasi</label>
                    <input id="password_confirmation" wire:model="password_confirmation" type="password" autocomplete="new-password" class="h-11 w-full rounded-lg border-surface-200 focus:border-accent focus:ring-accent/30 dark:border-surface-800 dark:bg-surface-950">
                </div>

                <button type="submit" wire:loading.attr="disabled" class="mt-6 inline-flex h-11 w-full items-center justify-center gap-2 rounded-lg border border-surface-200 px-4 text-sm font-bold transition hover:border-accent hover:text-accent disabled:opacity-60 dark:border-surface-800">
                    <i class="fas fa-key h-4 w-4" aria-hidden="true"></i>
                    Ubah password
                </button>
            </form>
        </div>
    </section>
</div>
