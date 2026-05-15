<?php

namespace Database\Factories;

use App\Enums\ArticleStatus;
use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Article>
 */
class ArticleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->unique()->sentence($this->faker->numberBetween(5, 9));
        $content = $this->markdownContent($title);

        return [
            'user_id' => User::factory()->author(),
            'category_id' => Category::factory(),
            'title' => Str::headline($title),
            'slug' => Str::slug($title),
            'excerpt' => $this->faker->paragraph(2),
            'content_md' => $content,
            'content_html' => Article::renderMarkdown($content),
            'featured_image' => 'https://images.unsplash.com/photo-1515879218367-8466d910aaa4?auto=format&fit=crop&w=1400&q=80',
            'featured_image_alt' => 'Developer workspace with code editor',
            'status' => ArticleStatus::Draft,
            'published_at' => null,
            'scheduled_at' => null,
            'reading_time' => Article::estimateReadingTime($content),
            'meta_title' => Str::limit($title, 60, ''),
            'meta_description' => $this->faker->sentence(18),
            'view_count' => $this->faker->numberBetween(25, 6000),
            'is_featured' => false,
        ];
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ArticleStatus::Published,
            'published_at' => $this->faker->dateTimeBetween('-8 months', '-1 day'),
        ]);
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ArticleStatus::Draft,
            'published_at' => null,
            'scheduled_at' => null,
        ]);
    }

    public function scheduled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ArticleStatus::Scheduled,
            'published_at' => null,
            'scheduled_at' => $this->faker->dateTimeBetween('+1 day', '+2 months'),
        ]);
    }

    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
            'status' => ArticleStatus::Published,
            'published_at' => now()->subDays(2),
        ]);
    }

    #[\NoDiscard]
    private function markdownContent(string $title): string
    {
        return <<<MARKDOWN
        ## Pendahuluan

        {$title} menjadi topik penting untuk developer modern karena menyentuh produktivitas, performa, dan cara tim membangun sistem yang tahan lama.

        ## Pola Implementasi

        Mulai dari constraint yang jelas, ukur baseline, lalu pilih perubahan yang memberi dampak terbesar. Hindari abstraksi terlalu awal dan pastikan setiap optimasi bisa dijelaskan lewat data.

        ```php
        final class ExamplePipeline
        {
            public function handle(array \$payload): array
            {
                return collect(\$payload)
                    ->filter()
                    ->values()
                    ->all();
            }
        }
        ```

        ## Praktik Produksi

        Gunakan observability, pengujian regresi, dan rollout bertahap. Untuk aplikasi Laravel, kombinasikan Eloquent scope, cache yang mudah diinvalidasi, dan queue untuk pekerjaan yang tidak perlu memblokir request.

        ## Kesimpulan

        Keputusan teknis yang baik selalu memiliki konteks. Fokus pada sistem yang mudah dirawat, aman, cepat, dan cukup sederhana untuk dikembangkan tim.
        MARKDOWN;
    }
}
