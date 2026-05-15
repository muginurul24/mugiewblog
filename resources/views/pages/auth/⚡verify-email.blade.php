<?php

use Livewire\Component;

new class extends Component
{
    public function resend(): void
    {
        if (auth()->user()?->hasVerifiedEmail()) {
            $this->redirectRoute('home', navigate: true);

            return;
        }

        auth()->user()?->sendEmailVerificationNotification();
        session()->flash('status', 'Link verifikasi baru sudah dikirim.');
    }
};
?>

<div>
    <x-slot:title>Verifikasi Email — MugiewBlog</x-slot:title>
    <x-slot:metaDescription>Verifikasi email akun MugiewBlog.</x-slot:metaDescription>
    <x-slot:canonical>{{ route('verification.notice') }}</x-slot:canonical>

    <section class="mx-auto flex min-h-[calc(100vh-12rem)] max-w-2xl items-center px-4 py-10 sm:px-6 lg:px-8">
        <div class="w-full rounded-lg border border-surface-200 bg-white p-6 shadow-sm dark:border-surface-800 dark:bg-surface-900">
            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-accent-muted text-accent">
                <i class="fas fa-envelope-open-text h-5 w-5" aria-hidden="true"></i>
            </div>
            <h1 class="mt-5 font-display text-3xl font-bold">Verifikasi email</h1>

            @if (session('status'))
                <p class="mt-4 rounded-lg bg-accent-muted px-3 py-2 text-sm font-medium text-accent">{{ session('status') }}</p>
            @endif

            <div class="mt-6 flex flex-wrap gap-3">
                <button type="button" wire:click="resend" wire:loading.attr="disabled" class="inline-flex h-11 items-center justify-center gap-2 rounded-lg bg-accent px-4 text-sm font-bold text-white transition hover:bg-accent-hover disabled:opacity-60">
                    <i class="fas fa-rotate-right h-4 w-4" aria-hidden="true"></i>
                    Kirim ulang
                </button>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="inline-flex h-11 items-center justify-center gap-2 rounded-lg border border-surface-200 px-4 text-sm font-bold transition hover:border-accent hover:text-accent dark:border-surface-800">
                        <i class="fas fa-right-from-bracket h-4 w-4" aria-hidden="true"></i>
                        Keluar
                    </button>
                </form>
            </div>
        </div>
    </section>
</div>
