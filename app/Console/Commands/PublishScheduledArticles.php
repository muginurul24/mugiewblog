<?php

namespace App\Console\Commands;

use App\Enums\ArticleStatus;
use App\Models\Article;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('articles:publish-scheduled')]
#[Description('Publish scheduled articles whose scheduled time has passed.')]
class PublishScheduledArticles extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $published = 0;

        Article::query()
            ->where('status', ArticleStatus::Scheduled->value)
            ->whereNotNull('scheduled_at')
            ->where('scheduled_at', '<=', now())
            ->chunkById(100, function ($articles) use (&$published): void {
                foreach ($articles as $article) {
                    $article->update([
                        'status' => ArticleStatus::Published,
                        'published_at' => $article->published_at ?? $article->scheduled_at ?? now(),
                    ]);

                    $published++;
                }
            });

        $this->info("Published {$published} scheduled articles.");

        return self::SUCCESS;
    }
}
