<?php

use Illuminate\Support\Facades\Password;
use Livewire\Component;

new class extends Component
{
    public string $email = '';

    public function sendResetLink(): void
    {
        $validated = $this->validate([
            'email' => ['required', 'email'],
        ]);

        $status = Password::sendResetLink($validated);

        if ($status === Password::RESET_LINK_SENT) {
            session()->flash('status', __($status));
            $this->reset('email');

            return;
        }

        $this->addError('email', __($status));
    }
};
?>

<div>
    <x-slot:title>Reset Password — MugiewBlog</x-slot:title>
    <x-slot:metaDescription>Kirim link reset password MugiewBlog.</x-slot:metaDescription>
    <x-slot:canonical>{{ route('password.request') }}</x-slot:canonical>

    <section class="page-hero hero-grid">
        <div class="mx-auto grid min-h-[calc(100vh-12rem)] max-w-6xl items-center gap-8 px-4 py-10 sm:px-6 lg:grid-cols-[minmax(0,1fr)_420px] lg:px-8">
            <div class="max-w-xl">
                <p class="eyebrow">
                    <i class="fas fa-lock h-3.5 w-3.5" aria-hidden="true"></i>
                    Pemulihan akun
                </p>
                <h1 class="mt-5 font-display text-4xl font-bold leading-tight">Reset password tanpa menebak langkah berikutnya.</h1>
                <p class="mt-4 leading-7 text-surface-600 dark:text-surface-300">Masukkan email akun dan kami kirimkan tautan aman untuk membuat password baru.</p>
            </div>

            <form wire:submit="sendResetLink" class="form-panel w-full p-6">
                <h2 class="font-display text-2xl font-bold">Reset password</h2>

            @if (session('status'))
                <p class="mt-4 rounded-lg bg-accent-muted px-3 py-2 text-sm font-medium text-accent">{{ session('status') }}</p>
            @endif

            <div class="mt-5">
                <label for="email" class="mb-1 block text-sm font-semibold">Email</label>
                <input id="email" wire:model="email" type="email" autocomplete="email" class="h-11 w-full rounded-lg border-surface-200 focus:border-accent focus:ring-accent/30 dark:border-surface-800 dark:bg-surface-950">
                @error('email') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
            </div>

            <button type="submit" wire:loading.attr="disabled" class="mt-6 inline-flex h-11 w-full items-center justify-center gap-2 rounded-lg bg-accent px-4 text-sm font-bold text-white transition hover:bg-accent-hover disabled:opacity-60">
                <i class="fas fa-paper-plane h-4 w-4" aria-hidden="true"></i>
                Kirim link
            </button>
            </form>
        </div>
    </section>
</div>
