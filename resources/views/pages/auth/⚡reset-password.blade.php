<?php

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Livewire\Component;

new class extends Component
{
    public string $token = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public function mount(string $token): void
    {
        $this->token = $token;
        $this->email = (string) request('email', '');
    }

    public function resetPassword(): mixed
    {
        $validated = $this->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', PasswordRule::defaults()],
        ]);

        $status = Password::reset(
            $validated,
            function (User $user, string $password): void {
                $user->forceFill([
                    'password' => $password,
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            },
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login')->with('status', __($status));
        }

        $this->addError('email', __($status));

        return null;
    }
};
?>

<div>
    <x-slot:title>Password Baru — MugiewBlog</x-slot:title>
    <x-slot:metaDescription>Buat password baru untuk akun MugiewBlog.</x-slot:metaDescription>
    <x-slot:canonical>{{ url()->current() }}</x-slot:canonical>

    <section class="mx-auto flex min-h-[calc(100vh-12rem)] max-w-xl items-center px-4 py-10 sm:px-6 lg:px-8">
        <form wire:submit="resetPassword" class="w-full rounded-lg border border-surface-200 bg-white p-6 shadow-sm dark:border-surface-800 dark:bg-surface-900">
            <h1 class="font-display text-3xl font-bold">Password baru</h1>

            <div class="mt-5">
                <label for="email" class="mb-1 block text-sm font-semibold">Email</label>
                <input id="email" wire:model="email" type="email" autocomplete="email" class="h-11 w-full rounded-lg border-surface-200 focus:border-accent focus:ring-accent/30 dark:border-surface-800 dark:bg-surface-950">
                @error('email') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
            </div>

            <div class="mt-4">
                <label for="password" class="mb-1 block text-sm font-semibold">Password</label>
                <input id="password" wire:model="password" type="password" autocomplete="new-password" class="h-11 w-full rounded-lg border-surface-200 focus:border-accent focus:ring-accent/30 dark:border-surface-800 dark:bg-surface-950">
                @error('password') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
            </div>

            <div class="mt-4">
                <label for="password_confirmation" class="mb-1 block text-sm font-semibold">Konfirmasi password</label>
                <input id="password_confirmation" wire:model="password_confirmation" type="password" autocomplete="new-password" class="h-11 w-full rounded-lg border-surface-200 focus:border-accent focus:ring-accent/30 dark:border-surface-800 dark:bg-surface-950">
            </div>

            <button type="submit" wire:loading.attr="disabled" class="mt-6 inline-flex h-11 w-full items-center justify-center gap-2 rounded-lg bg-accent px-4 text-sm font-bold text-white transition hover:bg-accent-hover disabled:opacity-60">
                <i class="fas fa-key h-4 w-4" aria-hidden="true"></i>
                Simpan password
            </button>
        </form>
    </section>
</div>
