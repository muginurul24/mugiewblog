@props([
    'comment',
    'replyingTo' => null,
    'level' => 1,
])

<div class="{{ $level === 1 ? 'border-t border-surface-200 pt-5 dark:border-surface-800' : 'mt-4 border-l border-surface-200 pl-4 dark:border-surface-800' }}">
    <div class="flex gap-3">
        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-surface-100 text-sm font-bold text-accent dark:bg-surface-800">
            {{ mb_strtoupper(mb_substr($comment->author?->name ?? $comment->guest_name ?? 'P', 0, 1)) }}
        </span>
        <div class="min-w-0 flex-1">
            <div class="flex flex-wrap items-center gap-2">
                <p class="font-semibold">{{ $comment->author?->name ?? $comment->guest_name }}</p>
                <span class="text-xs text-surface-400">{{ $comment->approved_at?->diffForHumans() }}</span>
            </div>
            <p class="mt-1 text-sm leading-6 text-surface-600 dark:text-surface-300">{{ $comment->content }}</p>

            @if ($level < 3)
                <button type="button" wire:click="startReply({{ $comment->id }})" class="mt-2 inline-flex items-center gap-1.5 text-xs font-semibold text-accent hover:text-accent-hover">
                    <i class="fas fa-reply h-3 w-3" aria-hidden="true"></i>
                    Balas
                </button>
            @endif

            @if ($replyingTo === $comment->id)
                <form wire:submit="submitReply" class="mt-3 grid gap-3">
                    <label for="reply-content-{{ $comment->id }}" class="sr-only">Balasan</label>
                    <textarea id="reply-content-{{ $comment->id }}" wire:model="replyContent" rows="3" class="w-full rounded-lg border-surface-200 text-sm focus:border-accent focus:ring-accent/30 dark:border-surface-800 dark:bg-surface-950"></textarea>
                    @error('replyContent') <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                    <div class="flex flex-wrap gap-2">
                        <button type="submit" wire:loading.attr="disabled" class="inline-flex items-center gap-2 rounded-lg bg-accent px-3 py-2 text-xs font-bold text-white transition hover:bg-accent-hover disabled:opacity-60">
                            <i class="fas fa-paper-plane h-3 w-3" aria-hidden="true"></i>
                            Kirim
                        </button>
                        <button type="button" wire:click="cancelReply" class="inline-flex items-center gap-2 rounded-lg border border-surface-200 px-3 py-2 text-xs font-bold transition hover:border-accent hover:text-accent dark:border-surface-800">
                            Batal
                        </button>
                    </div>
                </form>
            @endif

            @foreach ($comment->repliesRecursive as $reply)
                <x-comment-thread :comment="$reply" :replying-to="$replyingTo" :level="$level + 1" />
            @endforeach
        </div>
    </div>
</div>
