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

    <section class="mx-auto flex min-h-[calc(100vh-12rem)] max-w-xl items-center px-4 py-10 sm:px-6 lg:px-8">
        <form wire:submit="sendResetLink" class="w-full rounded-lg border border-surface-200 bg-white p-6 shadow-sm dark:border-surface-800 dark:bg-surface-900">
            <h1 class="font-display text-3xl font-bold">Reset password</h1>

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
    </section>
</div>
