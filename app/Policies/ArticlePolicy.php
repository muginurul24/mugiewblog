<?php

namespace App\Policies;

use App\Enums\ArticleStatus;
use App\Models\Article;
use App\Models\User;

class ArticlePolicy
{
    public function before(User $user): ?bool
    {
        return $user->isAdmin() ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return $user->isEditor() || $user->isAuthor();
    }

    public function view(User $user, Article $article): bool
    {
        return $article->status === ArticleStatus::Published
            || $article->user_id === $user->id
            || $user->isEditor();
    }

    public function create(User $user): bool
    {
        return $user->isEditor() || $user->isAuthor();
    }

    public function update(User $user, Article $article): bool
    {
        return $user->isEditor() || $article->user_id === $user->id;
    }

    public function delete(User $user, Article $article): bool
    {
        return $user->isEditor() || $article->user_id === $user->id;
    }

    public function restore(User $user, Article $article): bool
    {
        return $user->isEditor();
    }

    public function forceDelete(User $user, Article $article): bool
    {
        return false;
    }

    public function publish(User $user, Article $article): bool
    {
        return $user->isEditor();
    }
}
