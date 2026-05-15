<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;

class CommentPolicy
{
    public function before(User $user): ?bool
    {
        return $user->isAdmin() ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return $user->isEditor() || $user->isAuthor();
    }

    public function view(User $user, Comment $comment): bool
    {
        return $user->isEditor()
            || $comment->user_id === $user->id
            || $comment->article?->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->is_active && $user->hasVerifiedEmail();
    }

    public function update(User $user, Comment $comment): bool
    {
        return $comment->user_id === $user->id
            && $comment->created_at?->gt(now()->subMinutes(30));
    }

    public function delete(User $user, Comment $comment): bool
    {
        return $user->isEditor() || $comment->user_id === $user->id;
    }

    public function restore(User $user, Comment $comment): bool
    {
        return $user->isEditor();
    }

    public function forceDelete(User $user, Comment $comment): bool
    {
        return false;
    }

    public function approve(User $user, Comment $comment): bool
    {
        return $user->isEditor();
    }

    public function reject(User $user, Comment $comment): bool
    {
        return $user->isEditor();
    }
}
