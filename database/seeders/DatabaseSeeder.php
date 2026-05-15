<?php

namespace Database\Seeders;

use App\Enums\ArticleStatus;
use App\Enums\CommentStatus;
use App\Models\Article;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Media;
use App\Models\NewsletterSubscriber;
use App\Models\Series;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::factory()->admin()->create([
            'name' => 'Rafi Mugiew',
            'username' => 'rafi',
            'email' => 'rafi@example.com',
            'bio' => 'Full-stack developer yang menulis tentang Laravel, cloud, DevOps, dan investasi teknologi.',
            'github_url' => 'https://github.com/muginurul24',
            'website_url' => 'https://mugiew.dev',
        ]);

        $authors = User::factory()->author()->count(3)->create();
        $categories = $this->seedCategories();
        $tags = $this->seedTags();
        $series = Series::create([
            'name' => 'Laravel Production Notes',
            'slug' => 'laravel-production-notes',
            'description' => 'Kumpulan catatan produksi Laravel, Livewire, Filament, dan deployment modern.',
        ]);

        foreach ($this->articleBlueprints() as $index => $blueprint) {
            $category = $categories[$blueprint['category']];
            $content = $this->articleContent($blueprint['title']);

            $article = Article::create([
                'user_id' => $index % 2 === 0 ? $admin->id : $authors->random()->id,
                'category_id' => $category->id,
                'title' => $blueprint['title'],
                'slug' => Str::slug($blueprint['title']),
                'excerpt' => $blueprint['excerpt'],
                'content_md' => $content,
                'featured_image' => $blueprint['image'],
                'featured_image_alt' => $blueprint['image_alt'],
                'status' => ArticleStatus::Published,
                'published_at' => now()->subDays($index * 5 + 1),
                'is_featured' => $index === 0,
                'view_count' => 1500 - ($index * 113),
            ]);

            $article->tags()->sync(
                collect($blueprint['tags'])->map(fn (string $name): int => $tags[$name]->id)->all()
            );

            if ($index < 4) {
                $series->articles()->attach($article->id, ['sort_order' => $index + 1]);
            }

            Media::create([
                'user_id' => $article->user_id,
                'filename' => Str::afterLast($blueprint['image'], '/'),
                'original_name' => Str::slug($blueprint['title']).'.jpg',
                'path' => $blueprint['image'],
                'mime_type' => 'image/jpeg',
                'size' => 450_000 + ($index * 1_000),
                'alt_text' => $blueprint['image_alt'],
                'folder' => 'articles',
            ]);
        }

        Article::factory()
            ->draft()
            ->for($admin, 'author')
            ->for($categories->random())
            ->count(4)
            ->create()
            ->each(fn (Article $article): array => $article->tags()->sync($tags->random(3)->pluck('id')->all()));

        Article::published()
            ->inRandomOrder()
            ->take(8)
            ->get()
            ->each(function (Article $article) use ($authors): void {
                Comment::factory()
                    ->for($article)
                    ->for($authors->random(), 'author')
                    ->count(fake()->numberBetween(1, 4))
                    ->create([
                        'status' => CommentStatus::Approved,
                        'approved_at' => now(),
                    ]);
            });

        NewsletterSubscriber::factory()->count(15)->create();
    }

    /**
     * @return Collection<string, Category>
     */
    private function seedCategories(): Collection
    {
        return collect([
            ['Programming', 'programming', 'Laravel, PHP, JavaScript, dan pola coding modern.', '#D4943A', 'fa-code'],
            ['Cloud', 'cloud', 'Infrastruktur cloud, deployment, dan scaling.', '#2B8A7E', 'fa-cloud'],
            ['DevOps', 'devops', 'CI/CD, observability, containers, dan reliability.', '#5F6F94', 'fa-server'],
            ['Go & Rust', 'go-rust', 'Backend performa tinggi dengan Go dan Rust.', '#B85C38', 'fa-terminal'],
            ['AI Engineering', 'ai-engineering', 'LLM, agent workflow, RAG, dan evaluasi model.', '#6A7D39', 'fa-microchip'],
            ['Investment', 'investment', 'Analisis investasi teknologi, crypto, dan pasar digital.', '#8A6F3E', 'fa-chart-line'],
        ])->mapWithKeys(fn (array $category, int $index): array => [
            $category[1] => Category::create([
                'name' => $category[0],
                'slug' => $category[1],
                'description' => $category[2],
                'color' => $category[3],
                'icon' => $category[4],
                'sort_order' => $index + 1,
            ]),
        ]);
    }

    /**
     * @return Collection<string, Tag>
     */
    private function seedTags(): Collection
    {
        return collect([
            'Laravel',
            'PHP 8.5',
            'Livewire',
            'Filament',
            'MySQL',
            'Redis',
            'Docker',
            'FrankenPHP',
            'Kubernetes',
            'Rust',
            'Go',
            'AI Agents',
            'RAG',
            'Crypto',
            'Bitcoin',
            'DevOps',
            'Security',
            'Performance',
        ])->mapWithKeys(fn (string $name): array => [
            $name => Tag::create([
                'name' => $name,
                'slug' => Str::slug($name),
                'description' => "Artikel seputar {$name}.",
            ]),
        ]);
    }

    /**
     * @return array<int, array{title: string, excerpt: string, category: string, tags: array<int, string>, image: string, image_alt: string}>
     */
    private function articleBlueprints(): array
    {
        return [
            [
                'title' => 'PHP 8.5 Pipe Operator di Aplikasi Laravel Modern',
                'excerpt' => 'Cara memanfaatkan pipe operator untuk membuat pipeline transformasi data yang lebih ekspresif dan tetap mudah dites.',
                'category' => 'programming',
                'tags' => ['Laravel', 'PHP 8.5', 'Performance'],
                'image' => 'https://images.unsplash.com/photo-1555066931-4365d14bab8c?auto=format&fit=crop&w=1400&q=80',
                'image_alt' => 'Kode PHP di layar editor gelap',
            ],
            [
                'title' => 'Livewire 4 dan Filament 5 untuk Workflow Blog Profesional',
                'excerpt' => 'Membangun pengalaman penulisan yang cepat tanpa meninggalkan ekosistem Laravel dan Blade.',
                'category' => 'programming',
                'tags' => ['Laravel', 'Livewire', 'Filament'],
                'image' => 'https://images.unsplash.com/photo-1498050108023-c5249f4df085?auto=format&fit=crop&w=1400&q=80',
                'image_alt' => 'Workspace developer dengan laptop dan monitor',
            ],
            [
                'title' => 'FrankenPHP Worker Mode: Menekan TTFB Laravel di Produksi',
                'excerpt' => 'Strategi boot-once, cache warming, dan batasan state saat menjalankan Laravel dengan Octane.',
                'category' => 'cloud',
                'tags' => ['FrankenPHP', 'Laravel', 'Performance'],
                'image' => 'https://images.unsplash.com/photo-1558494949-ef010cbdcc31?auto=format&fit=crop&w=1400&q=80',
                'image_alt' => 'Rack server dengan lampu jaringan',
            ],
            [
                'title' => 'Observability DevOps untuk Tim Kecil',
                'excerpt' => 'Metrik, log, trace, dan alert yang cukup untuk menangkap masalah nyata tanpa membangun platform rumit.',
                'category' => 'devops',
                'tags' => ['DevOps', 'Docker', 'Redis'],
                'image' => 'https://images.unsplash.com/photo-1518770660439-4636190af475?auto=format&fit=crop&w=1400&q=80',
                'image_alt' => 'Papan sirkuit dan komponen teknologi',
            ],
            [
                'title' => 'Rust untuk Backend Startup: Kapan Layak Dipakai',
                'excerpt' => 'Membedah tradeoff ownership, compile time, performa, dan biaya onboarding untuk sistem backend.',
                'category' => 'go-rust',
                'tags' => ['Rust', 'Performance', 'Security'],
                'image' => 'https://images.unsplash.com/photo-1515879218367-8466d910aaa4?auto=format&fit=crop&w=1400&q=80',
                'image_alt' => 'Terminal dan kode di layar laptop',
            ],
            [
                'title' => 'RAG yang Bisa Dievaluasi, Bukan Sekadar Demo',
                'excerpt' => 'Checklist praktis untuk retrieval quality, grounded answer, observability, dan regresi prompt.',
                'category' => 'ai-engineering',
                'tags' => ['AI Agents', 'RAG', 'Performance'],
                'image' => 'https://images.unsplash.com/photo-1677442136019-21780ecad995?auto=format&fit=crop&w=1400&q=80',
                'image_alt' => 'Ilustrasi abstrak kecerdasan buatan',
            ],
            [
                'title' => 'Strategi DCA Bitcoin untuk Developer yang Sibuk',
                'excerpt' => 'Kerangka sederhana untuk mengelola risiko, horizon waktu, dan disiplin eksekusi tanpa trading harian.',
                'category' => 'investment',
                'tags' => ['Crypto', 'Bitcoin', 'Security'],
                'image' => 'https://images.unsplash.com/photo-1621761191319-c6fb62004040?auto=format&fit=crop&w=1400&q=80',
                'image_alt' => 'Koin Bitcoin di atas permukaan gelap',
            ],
            [
                'title' => 'MySQL Full-Text Search untuk Blog Laravel',
                'excerpt' => 'MVP search yang cepat, murah, dan cukup relevan sebelum masuk ke semantic search.',
                'category' => 'programming',
                'tags' => ['Laravel', 'MySQL', 'Performance'],
                'image' => 'https://images.unsplash.com/photo-1544383835-bda2bc66a55d?auto=format&fit=crop&w=1400&q=80',
                'image_alt' => 'Database server dan visualisasi data',
            ],
        ];
    }

    #[\NoDiscard]
    private function articleContent(string $title): string
    {
        return <<<MARKDOWN
        ## Konteks

        {$title} bukan sekadar topik tren. Untuk aplikasi produksi, keputusan teknis harus bisa dijelaskan lewat manfaat operasional, risiko yang dikurangi, dan biaya jangka panjang.

        ## Pendekatan Praktis

        Mulai dari kebutuhan pengguna, ukur baseline, lalu pilih implementasi yang paling sederhana. Di Laravel, pola yang kuat biasanya datang dari kombinasi Eloquent scope, queued jobs, cache yang punya strategi invalidasi, dan tampilan Blade yang fokus pada aksesibilitas.

        ```php
        final class ProductionChecklist
        {
            public function passes(array \$signals): bool
            {
                return collect(\$signals)
                    ->every(fn (bool \$signal): bool => \$signal);
            }
        }
        ```

        ## Hal yang Perlu Dijaga

        Pastikan setiap fitur punya test minimal, query penting sudah eager-loaded, dan pengalaman mobile tidak menjadi versi kedua dari desktop. Dokumentasikan tradeoff di commit atau PR agar keputusan bisa dilacak.

        ## Kesimpulan

        Solusi yang production-ready bukan berarti paling kompleks. Ia stabil, mudah diobservasi, dapat dipulihkan, dan memberi ruang bagi iterasi berikutnya tanpa membuat sistem rapuh.
        MARKDOWN;
    }
}
