<?php

namespace App\Models;

use Database\Factories\BookmarkFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'article_id'])]
class Bookmark extends Model
{
    /** @use HasFactory<BookmarkFactory> */
    use HasFactory;

    /**
     * @return BelongsTo<User, $this>
     */
    #[\NoDiscard]
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<Article, $this>
     */
    #[\NoDiscard]
    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }
}
