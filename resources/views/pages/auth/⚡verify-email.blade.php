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

    <section class="page-hero hero-grid">
        <div class="mx-auto grid min-h-[calc(100vh-12rem)] max-w-6xl items-center gap-8 px-4 py-10 sm:px-6 lg:grid-cols-[minmax(0,1fr)_420px] lg:px-8">
            <div class="max-w-xl">
                <p class="eyebrow">
                    <i class="fas fa-envelope-open-text h-3.5 w-3.5" aria-hidden="true"></i>
                    Verifikasi
                </p>
                <h1 class="mt-5 font-display text-4xl font-bold leading-tight">Satu langkah lagi sebelum akun aktif penuh.</h1>
                <p class="mt-4 leading-7 text-surface-600 dark:text-surface-300">Verifikasi email membuka komentar dan membantu menjaga interaksi pembaca tetap berkualitas.</p>
            </div>

            <div class="form-panel w-full p-6">
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-accent-muted text-accent">
                    <i class="fas fa-envelope-circle-check h-5 w-5" aria-hidden="true"></i>
                </div>
                <h2 class="mt-5 font-display text-2xl font-bold">Verifikasi email</h2>

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
        </div>
    </section>
</div>
