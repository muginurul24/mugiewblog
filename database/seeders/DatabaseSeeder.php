<?php

namespace Database\Seeders;

use App\Enums\ArticleStatus;
use App\Enums\CommentStatus;
use App\Enums\UserRole;
use App\Models\Article;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Media;
use App\Models\NewsletterSubscriber;
use App\Models\Series;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = $this->seedUser([
            'name' => 'Rafi Mugiew',
            'username' => 'rafi',
            'email' => 'rafi@example.com',
            'email_verified_at' => now(),
            'password' => 'password',
            'bio' => 'Full-stack developer yang menulis tentang Laravel, cloud, DevOps, dan investasi teknologi.',
            'github_url' => 'https://github.com/muginurul24',
            'website_url' => 'https://mugiew.dev',
            'role' => UserRole::Admin,
            'is_active' => true,
        ]);

        $authors = $this->seedAuthors();
        $categories = $this->seedCategories();
        $tags = $this->seedTags();
        $series = Series::updateOrCreate([
            'slug' => 'laravel-production-notes',
        ], [
            'name' => 'Laravel Production Notes',
            'description' => 'Kumpulan catatan produksi Laravel, Livewire, Filament, dan deployment modern.',
        ]);

        foreach ($this->articleBlueprints() as $index => $blueprint) {
            $category = $categories[$blueprint['category']];
            $content = $this->articleContent($blueprint['title']);

            $article = Article::updateOrCreate([
                'slug' => Str::slug($blueprint['title']),
            ], [
                'user_id' => $index % 2 === 0 ? $admin->id : $authors->values()->get($index % $authors->count())->id,
                'category_id' => $category->id,
                'title' => $blueprint['title'],
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
                $series->articles()->syncWithoutDetaching([$article->id => ['sort_order' => $index + 1]]);
            }

            Media::updateOrCreate([
                'path' => $blueprint['image'],
            ], [
                'user_id' => $article->user_id,
                'filename' => Str::afterLast($blueprint['image'], '/'),
                'original_name' => Str::slug($blueprint['title']).'.jpg',
                'mime_type' => 'image/jpeg',
                'size' => 450_000 + ($index * 1_000),
                'alt_text' => $blueprint['image_alt'],
                'folder' => 'articles',
            ]);
        }

        foreach ($this->draftBlueprints() as $index => $blueprint) {
            $category = $categories[$blueprint['category']];
            $content = $this->articleContent($blueprint['title']);

            $article = Article::updateOrCreate([
                'slug' => Str::slug($blueprint['title']),
            ], [
                'user_id' => $index % 2 === 0 ? $admin->id : $authors->values()->get($index % $authors->count())->id,
                'category_id' => $category->id,
                'title' => $blueprint['title'],
                'excerpt' => $blueprint['excerpt'],
                'content_md' => $content,
                'featured_image' => $blueprint['image'],
                'featured_image_alt' => $blueprint['image_alt'],
                'status' => ArticleStatus::Draft,
                'published_at' => null,
                'scheduled_at' => null,
                'is_featured' => false,
                'view_count' => 0,
            ]);

            $article->tags()->sync(
                collect($blueprint['tags'])->map(fn (string $name): int => $tags[$name]->id)->all()
            );
        }

        Article::published()
            ->latest('published_at')
            ->take(8)
            ->get()
            ->each(function (Article $article, int $index) use ($authors): void {
                $comments = $this->commentBlueprints();

                for ($commentIndex = 0; $commentIndex < (($index % 3) + 1); $commentIndex++) {
                    $author = $authors->values()->get(($index + $commentIndex) % $authors->count());
                    $content = $comments[($index + $commentIndex) % count($comments)];

                    Comment::updateOrCreate([
                        'article_id' => $article->id,
                        'user_id' => $author->id,
                        'content' => $content,
                    ], [
                        'parent_id' => null,
                        'guest_name' => null,
                        'guest_email' => null,
                        'status' => CommentStatus::Approved,
                        'approved_at' => now()->subDays($commentIndex + 1),
                    ]);
                }
            });

        foreach ($this->newsletterSubscribers() as $subscriber) {
            NewsletterSubscriber::updateOrCreate([
                'email' => $subscriber['email'],
            ], $subscriber);
        }
    }

    /**
     * @param  array{name: string, username: string, email: string, email_verified_at: Carbon, password: string, bio: string, role: UserRole, is_active: bool, github_url?: string, website_url?: string}  $attributes
     */
    private function seedUser(array $attributes): User
    {
        $user = User::firstOrNew(['email' => $attributes['email']]);
        $user->forceFill($attributes)->save();

        return $user;
    }

    /**
     * @return Collection<int, User>
     */
    private function seedAuthors(): Collection
    {
        return collect([
            [
                'name' => 'Nadia Prasetya',
                'username' => 'nadia-prasetya',
                'email' => 'nadia@example.com',
                'bio' => 'Editor dan Laravel engineer yang fokus pada content workflow, data model, dan DX.',
                'website_url' => 'https://mugiew.dev/authors/nadia',
            ],
            [
                'name' => 'Bagas Wicaksono',
                'username' => 'bagas-wicaksono',
                'email' => 'bagas@example.com',
                'bio' => 'DevOps practitioner yang menulis tentang observability, container, queue, dan reliability.',
                'github_url' => 'https://github.com/mugiew',
            ],
            [
                'name' => 'Sinta Rahma',
                'username' => 'sinta-rahma',
                'email' => 'sinta@example.com',
                'bio' => 'AI engineer yang menghubungkan product thinking, retrieval quality, dan evaluasi LLM.',
                'website_url' => 'https://mugiew.dev/authors/sinta',
            ],
        ])->map(fn (array $author): User => $this->seedUser([
            ...$author,
            'email_verified_at' => now(),
            'password' => 'password',
            'role' => UserRole::Author,
            'is_active' => true,
        ]));
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
            $category[1] => Category::updateOrCreate([
                'slug' => $category[1],
            ], [
                'name' => $category[0],
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
            $name => Tag::updateOrCreate([
                'slug' => Str::slug($name),
            ], [
                'name' => $name,
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

    /**
     * @return array<int, array{title: string, excerpt: string, category: string, tags: array<int, string>, image: string, image_alt: string}>
     */
    private function draftBlueprints(): array
    {
        return [
            [
                'title' => 'Checklist Hardening Laravel Octane Sebelum Launch',
                'excerpt' => 'Catatan draft tentang konfigurasi worker, cache, queue, dan batasan state untuk server boot-once.',
                'category' => 'cloud',
                'tags' => ['Laravel', 'FrankenPHP', 'Security'],
                'image' => 'https://images.unsplash.com/photo-1451187580459-43490279c0fa?auto=format&fit=crop&w=1400&q=80',
                'image_alt' => 'Peta jaringan digital dan koneksi cloud',
            ],
            [
                'title' => 'Template Review PR untuk Fitur Filament',
                'excerpt' => 'Draft kerangka review untuk resource, policy, validation, dan pengalaman admin.',
                'category' => 'programming',
                'tags' => ['Filament', 'Laravel', 'Security'],
                'image' => 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?auto=format&fit=crop&w=1400&q=80',
                'image_alt' => 'Dashboard analitik di layar laptop',
            ],
            [
                'title' => 'Queue Redis dan Horizon untuk Newsletter',
                'excerpt' => 'Draft desain queue newsletter yang memperhatikan retry, rate limit, dan observability.',
                'category' => 'devops',
                'tags' => ['Redis', 'DevOps', 'Performance'],
                'image' => 'https://images.unsplash.com/photo-1550751827-4bd374c3f58b?auto=format&fit=crop&w=1400&q=80',
                'image_alt' => 'Ruang server dengan pencahayaan biru',
            ],
            [
                'title' => 'Evaluasi RAG untuk Knowledge Base Internal',
                'excerpt' => 'Draft rubrik evaluasi retrieval, citation quality, dan regression suite untuk aplikasi internal.',
                'category' => 'ai-engineering',
                'tags' => ['AI Agents', 'RAG', 'Performance'],
                'image' => 'https://images.unsplash.com/photo-1676299081847-824916de030a?auto=format&fit=crop&w=1400&q=80',
                'image_alt' => 'Visual abstrak model kecerdasan buatan',
            ],
        ];
    }

    /**
     * @return array<int, string>
     */
    private function commentBlueprints(): array
    {
        return [
            'Tulisan ini praktis dan langsung bisa dijadikan checklist sebelum masuk ke sprint berikutnya.',
            'Bagian tradeoff-nya membantu karena tidak hanya menjual satu teknologi sebagai solusi tunggal.',
            'Contoh implementasinya cukup ringkas, tetapi tetap memberi konteks untuk aplikasi produksi.',
            'Saya suka pendekatan mengukur baseline dulu sebelum menambah optimasi atau dependency baru.',
            'Poin observability dan rollback sebaiknya memang masuk sejak awal, bukan setelah incident.',
            'Struktur artikelnya enak dipindai untuk pembaca yang butuh keputusan cepat.',
        ];
    }

    /**
     * @return array<int, array{email: string, name: string, status: string, source: string, verified_at: Carbon, subscribed_at: Carbon, unsubscribed_at: null}>
     */
    private function newsletterSubscribers(): array
    {
        return collect([
            ['andi@example.com', 'Andi Mahendra', 'footer'],
            ['bima@example.com', 'Bima Santoso', 'article'],
            ['citra@example.com', 'Citra Lestari', 'homepage'],
            ['dewi@example.com', 'Dewi Kartika', 'footer'],
            ['eko@example.com', 'Eko Pranata', 'article'],
            ['farah@example.com', 'Farah Aulia', 'homepage'],
            ['galih@example.com', 'Galih Putra', 'footer'],
            ['hana@example.com', 'Hana Salsabila', 'article'],
            ['indra@example.com', 'Indra Wijaya', 'homepage'],
            ['julia@example.com', 'Julia Permata', 'footer'],
            ['kevin@example.com', 'Kevin Rahardjo', 'article'],
            ['laila@example.com', 'Laila Safitri', 'homepage'],
            ['mika@example.com', 'Mika Adinata', 'footer'],
            ['nina@example.com', 'Nina Oktavia', 'article'],
            ['oscar@example.com', 'Oscar Wibowo', 'homepage'],
        ])->map(fn (array $subscriber): array => [
            'email' => $subscriber[0],
            'name' => $subscriber[1],
            'status' => 'subscribed',
            'source' => $subscriber[2],
            'verified_at' => now(),
            'subscribed_at' => now(),
            'unsubscribed_at' => null,
        ])->all();
    }

    #[\NoDiscard]
    private function articleContent(string $title): string
    {
        return <<<MARKDOWN
        ## Konteks

        {$title} bukan sekadar topik tren. Untuk aplikasi produksi, keputusan teknis harus bisa dijelaskan lewat manfaat operasional, risiko yang dikurangi, dan biaya jangka panjang.

        ## Pendekatan Praktis

        Mulai dari kebutuhan pengguna, ukur baseline, lalu pilih implementasi yang paling sederhana. Di Laravel, pola yang kuat biasanya datang dari kombinasi Eloquent scope, queued jobs, cache yang punya strategi invalidasi, dan tampilan Blade yang fokus pada aksesibilitas.

        - Tentukan metrik sukses sebelum menulis kode.
        - Batasi scope agar perubahan mudah di-review dan mudah di-rollback.
        - Pastikan konten tetap nyaman dibaca di mobile, tablet, dan desktop.

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

        > Keputusan teknis yang baik meninggalkan jejak: alasan, baseline, dan sinyal kapan perlu diganti.

        ## Hal yang Perlu Dijaga

        Pastikan setiap fitur punya test minimal, query penting sudah eager-loaded, dan pengalaman mobile tidak menjadi versi kedua dari desktop. Dokumentasikan tradeoff di commit atau PR agar keputusan bisa dilacak.

        | Area | Sinyal sehat |
        | --- | --- |
        | Aksesibilitas | Heading runtut, kontras jelas, dan navigasi keyboard aman |
        | Performa | Query utama terukur dan aset visual tidak memblokir render |
        | Operasional | Log, retry, dan rollback tersedia sebelum fitur dibuka luas |

        ## Kesimpulan

        Solusi yang production-ready bukan berarti paling kompleks. Ia stabil, mudah diobservasi, dapat dipulihkan, dan memberi ruang bagi iterasi berikutnya tanpa membuat sistem rapuh.
        MARKDOWN;
    }
}
