@props([
    'icon' => 'fa-inbox',
    'title',
    'description' => null,
])

<div {{ $attributes->class('rounded-lg border border-dashed border-surface-300 bg-white px-6 py-14 text-center dark:border-surface-800 dark:bg-surface-900') }}>
    <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-lg bg-surface-100 text-surface-400 dark:bg-surface-800">
        <i class="fas {{ $icon }} h-5 w-5" aria-hidden="true"></i>
    </div>
    <h2 class="font-display text-lg font-bold">{{ $title }}</h2>
    @if ($description)
        <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-surface-500 dark:text-surface-400">{{ $description }}</p>
    @endif
</div>
