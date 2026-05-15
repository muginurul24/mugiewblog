# Architecture Document
## MugiewBlog — Technical Architecture & Design Decisions

**Version:** 1.0.0  
**Date:** 2026-05-15  
**Author:** Claw Kun (Architect)  
**PRD Reference:** [PRD.md](./PRD.md)

---

## Table of Contents
1. [Architecture Overview](#1-architecture-overview)
2. [Technology Decisions](#2-technology-decisions)
3. [Database Design](#3-database-design)
4. [Model Design](#4-model-design)
5. [Livewire Component Architecture](#5-livewire-component-architecture)
6. [Filament Admin Architecture](#6-filament-admin-architecture)
7. [Authentication & Authorization](#7-authentication--authorization)
8. [Caching Architecture](#8-caching-architecture)
9. [Search Architecture](#9-search-architecture)
10. [SEO Architecture](#10-seo-architecture)
11. [Security Architecture](#11-security-architecture)
12. [Queue & Job Architecture](#12-queue--job-architecture)
13. [Dark Mode Architecture](#13-dark-mode-architecture)
14. [Docker & Deployment Architecture](#14-docker--deployment-architecture)
15. [Testing Architecture](#15-testing-architecture)

---

## 1. Architecture Overview

### 1.1 Architectural Style
**Monolith with service layer** — Single Laravel application with clear separation:
- **Livewire SPA** for public-facing frontend
- **Filament 5** for admin panel
- **Service classes** for business logic (not fat controllers/models)
- **Event-driven** internal communication

### 1.2 Request Lifecycle

```
Request → FrankenPHP (Caddy) → Laravel Octane Worker (in-memory)
  ├── Public Routes → Livewire Components (SPA)
  │   ├── Dehydrate → Send HTML fragments
  │   └── Subsequent → AJAX Wire calls (no full reload)
  └── Admin Routes → Filament Panel
      └── Standard Filament lifecycle
```

### 1.3 Directory Convention (Key Decisions)

| Directory | Purpose |
|---|---|
| `app/Services/` | Business logic (ArticleService, SearchService, etc.) |
| `app/Livewire/Pages/` | Full-page Livewire components (SPA routes) |
| `app/Livewire/Layouts/` | SPA layout shell |
| `app/Livewire/Components/` | Reusable UI components |
| `app/Filament/Resources/` | Admin CRUD resources |
| `app/Enums/` | PHP 8.5 typed enums |
| `app/Events/` | Domain events |
| `app/Jobs/` | Queue jobs |
| `app/Policies/` | Laravel authorization policies |

---

## 2. Technology Decisions

### 2.1 Why Laravel 13 + PHP 8.5

| Decision | Rationale |
|---|---|
| **PHP 8.5 Pipe Operator** | `|>` enables readable data transformation pipelines |
| **`clone with`** | Immutable updates for DTOs and value objects |
| **`#[\NoDiscard]`** | Compile-time enforcement for methods whose return values must be consumed |
| **Asymmetric visibility** | `public private(set)` for read-only from outside, writable internally |
| **Typed class constants** | Type-safe constants on Enums and config classes |

### 2.2 Why Livewire 4 SPA (not Inertia.js)

| Livewire 4 | Inertia.js |
|---|---|
| No JS framework dependency (no Vue/React) | Requires Vue/React/Svelte |
| Server-rendered, SEO-friendly by default | Needs SSR setup for SEO |
| Native Laravel ecosystem | Sits between Laravel and JS |
| View-based components (`.blade.php`) | JS component files |
| Islands architecture for partial updates | Full page hydration |

**Decision:** Livewire 4 SPA dipilih karena:
1. Semua state di server — lebih aman
2. SEO out-of-the-box tanpa SSR setup
3. Satu bahasa (PHP) untuk full stack
4. View-based components lebih simple untuk blog
5. Filament native Livewire support

### 2.3 Why Filament 5 (not Nova or custom)

| Filament 5 | Laravel Nova |
|---|---|
| Free & open source | Paid ($199/site) |
| Livewire-native | Vue-based |
| Extensible plugin ecosystem | Limited plugins |
| Filament Blueprint for AI generation | None |
| Community-driven, aktif | Laravel team maintained |

### 2.4 Why FrankenPHP Worker Mode

- **3-4x lebih cepat** dari PHP-FPM (boot sekali, reuse in-memory)
- **No Nginx/Apache** — FrankenPHP includes Caddy as HTTP server
- **HTTP/2 & HTTP/3** built-in via Caddy
- **Auto HTTPS** via Caddy (Let's Encrypt)
- **Mercure** support untuk real-time (future use)

### 2.5 Why MySQL 8.4 LTS (not PostgreSQL)

- **Familiarity** — Rafi lebih nyaman dengan MySQL
- **Laravel first-class support** — Semua fitur Laravel di-test dengan MySQL
- **Full-text search** — MySQL native FULLTEXT untuk search MVP
- **8.4 LTS** — Long term support hingga 2032

### 2.7 Why Tailwind CSS v4 (CSS-first config)

- **No JS config file** — `@import "tailwindcss"` + `@theme {}` in CSS
- **Smaller bundle** — CSS-first config eliminates JS parsing
- **Native cascade layers** — Better specificity control
- **Container queries** — Built-in support
- **OKLCH colors** — Modern color space, better dark mode

### 2.8 Icon Library: FontAwesome Free 7.2

- **Bundled locally** di `resources/fontawesome/` — no npm dependency
- **Three styles:** Solid (`fas`), Regular (`far`), Brands (`fab`)
- **Imported via** `@import "./../fontawesome/css/all.min.css"` di `app.css`
- **Convention:** Tailwind sizing (`h-5 w-5`) > FA size classes (`fa-lg`)
- **A11y:** `aria-hidden="true"` on `<i>`, `aria-label` on interactive parent
- **Skill:** `.agents/skills/fontawesome/SKILL.md`

### 2.9 Animation Library: Animate.css 4.1

- **Installed via npm**, imported via `@import "animate.css"` di `app.css`
- **Class-based API:** `animate__animated` + effect class (e.g., `animate__fadeInUp`)
- **Modifiers:** speed (`animate__faster`), delay (`animate__delay-1s`), repeat
- **Livewire/Alpine triggers:** `x-transition:enter` + animate classes
- **A11y:** `prefers-reduced-motion` WAJIB — disable all animations
- **Convention:** Exit animations need JS toggle; prefer `animate__faster` (500ms); stagger cards with delay
- **Skill:** `.agents/skills/animatecss/SKILL.md`

---

## 3. Database Design

### 3.1 Entity Relationship Diagram (Text)

```
User (1) ────────< Article (N)   [author]
User (1) ────────< Comment (N)   [commenter]
User (1) ────────< Bookmark (N)
User (1) ────────< Media (N)     [uploader]

Article (1) ─────< Comment (N)
Article (1) ─────< Bookmark (N)
Article (N) >───── Category (1)  [belongs to]
Article (N) >───── Tag (N)       [polymorphic via taggables]

Category (1) ────< Category (N)  [self-referencing, parent/child]

Comment (1) ─────< Comment (N)   [self-referencing, nested replies]

Series (1) ──────< SeriesArticle (N) >── Article (1)
```

### 3.2 Complete Migration Schema

#### 3.2.1 users
```php
Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('email')->unique();
    $table->string('username')->unique();
    $table->string('password');
    $table->string('avatar')->nullable();
    $table->text('bio')->nullable();
    $table->string('github_url')->nullable();
    $table->string('twitter_url')->nullable();
    $table->string('website_url')->nullable();
    $table->timestamp('email_verified_at')->nullable();
    $table->string('role')->default('user'); // admin, editor, author, user
    $table->boolean('is_active')->default(true);
    $table->boolean('two_factor_enabled')->default(false);
    $table->text('two_factor_secret')->nullable();
    $table->rememberToken();
    $table->timestamps();
    $table->softDeletes();
    
    $table->index('role');
    $table->index('is_active');
});
```

#### 3.2.2 categories
```php
Schema::create('categories', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('slug')->unique();
    $table->text('description')->nullable();
    $table->foreignId('parent_id')->nullable()->constrained('categories')->nullOnDelete();
    $table->integer('sort_order')->default(0);
    $table->timestamps();
    
    $table->index('parent_id');
    $table->index('sort_order');
});
```

#### 3.2.3 tags
```php
Schema::create('tags', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('slug')->unique();
    $table->timestamps();
});
```

#### 3.2.4 articles
```php
Schema::create('articles', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
    $table->string('title');
    $table->string('slug')->unique();
    $table->text('excerpt')->nullable();
    $table->longText('content_md');     // Raw markdown
    $table->longText('content_html');   // Rendered HTML
    $table->string('featured_image')->nullable();
    $table->string('status')->default('draft'); // draft, review, published, scheduled
    $table->timestamp('published_at')->nullable();
    $table->timestamp('scheduled_at')->nullable();
    $table->integer('reading_time')->nullable(); // in minutes
    $table->string('meta_title')->nullable();
    $table->text('meta_description')->nullable();
    $table->unsignedBigInteger('view_count')->default(0);
    $table->boolean('is_featured')->default(false);
    $table->timestamps();
    $table->softDeletes();
    
    $table->index('status');
    $table->index('published_at');
    $table->index('category_id');
    $table->index('is_featured');
    $table->fullText(['title', 'excerpt', 'content_md'], 'articles_fulltext');
});
```

#### 3.2.5 taggables (polymorphic)
```php
Schema::create('taggables', function (Blueprint $table) {
    $table->id();
    $table->foreignId('tag_id')->constrained()->cascadeOnDelete();
    $table->morphs('taggable'); // taggable_type, taggable_id
    $table->timestamps();
});
```

#### 3.2.6 comments
```php
Schema::create('comments', function (Blueprint $table) {
    $table->id();
    $table->foreignId('article_id')->constrained()->cascadeOnDelete();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->foreignId('parent_id')->nullable()->constrained('comments')->cascadeOnDelete();
    $table->text('content');
    $table->string('status')->default('pending'); // pending, approved, spam
    $table->string('ip_address', 45)->nullable();
    $table->text('user_agent')->nullable();
    $table->timestamps();
    
    $table->index('status');
    $table->index('parent_id');
    $table->index(['article_id', 'status']);
});
```

#### 3.2.7 media
```php
Schema::create('media', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->string('filename');
    $table->string('original_name');
    $table->string('path');
    $table->string('mime_type');
    $table->unsignedBigInteger('size');
    $table->string('alt_text')->nullable();
    $table->string('folder')->default('general');
    $table->timestamps();
});
```

#### 3.2.8 bookmarks
```php
Schema::create('bookmarks', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->foreignId('article_id')->constrained()->cascadeOnDelete();
    $table->timestamps();
    
    $table->unique(['user_id', 'article_id']);
});
```

#### 3.2.9 series
```php
Schema::create('series', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('slug')->unique();
    $table->text('description')->nullable();
    $table->timestamps();
});

Schema::create('series_articles', function (Blueprint $table) {
    $table->id();
    $table->foreignId('series_id')->constrained()->cascadeOnDelete();
    $table->foreignId('article_id')->constrained()->cascadeOnDelete();
    $table->integer('sort_order')->default(0);
    $table->timestamps();
    
    $table->unique(['series_id', 'article_id']);
});
```

#### 3.2.10 newsletter_subscribers
```php
Schema::create('newsletter_subscribers', function (Blueprint $table) {
    $table->id();
    $table->string('email')->unique();
    $table->string('token', 64)->unique();
    $table->timestamp('confirmed_at')->nullable();
    $table->timestamp('unsubscribed_at')->nullable();
    $table->timestamps();
});
```

---

## 4. Model Design

### 4.1 Article Model (Core)

```php
#[ObservedBy(ArticleObserver::class)]
final class Article extends Model
{
    use HasFactory, SoftDeletes, HasTags;

    protected $fillable = [
        'user_id', 'category_id', 'title', 'slug', 'excerpt',
        'content_md', 'content_html', 'featured_image', 'status',
        'published_at', 'scheduled_at', 'reading_time',
        'meta_title', 'meta_description', 'view_count', 'is_featured',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'scheduled_at' => 'datetime',
            'is_featured' => 'boolean',
        ];
    }

    #[BelongsTo]
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    #[BelongsTo]
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    #[HasMany]
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class)->whereNull('parent_id');
    }

    #[HasMany]
    public function bookmarks(): HasMany
    {
        return $this->hasMany(Bookmark::class);
    }

    #[Scope]
    public function scopePublished(Builder $query): void
    {
        $query->where('status', 'published')
              ->where('published_at', '<=', now());
    }

    #[Scope]
    public function scopeFeatured(Builder $query): void
    {
        $query->where('is_featured', true);
    }

    // Reading time calculator
    #[\NoDiscard]
    public function calculateReadingTime(): int
    {
        $wordCount = str_word_count(strip_tags($this->content_html));
        return (int) ceil($wordCount / 200); // 200 WPM average
    }

    // Resolve URL for sharing
    #[\NoDiscard]
    public function url(): string
    {
        return route('articles.show', $this->slug);
    }
}
```

### 4.2 Enum Design (PHP 8.5)

```php
enum ArticleStatus: string
{
    case Draft = 'draft';
    case Review = 'review';
    case Published = 'published';
    case Scheduled = 'scheduled';

    #[\NoDiscard]
    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Review => 'In Review',
            self::Published => 'Published',
            self::Scheduled => 'Scheduled',
        };
    }

    #[\NoDiscard]
    public function color(): string
    {
        return match ($this) {
            self::Draft => 'gray',
            self::Review => 'warning',
            self::Published => 'success',
            self::Scheduled => 'info',
        };
    }
}

enum UserRole: string
{
    case Admin = 'admin';
    case Editor = 'editor';
    case Author = 'author';
    case User = 'user';
}

enum CommentStatus: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Spam = 'spam';
}
```

### 4.3 Relationships Required (Eager Loading Map)

```
Article List:
  → author (user), category, tags, comments_count

Article Detail:
  → author (user), category, tags, 
  → comments (nested: user, replies.user),
  → relatedPosts (limit 3, published)

Category Page:
  → articles.published (paginated) → author, tags, comments_count

User Profile:
  → articles.published (paginated), bookmarks.article
```

---

## 5. Livewire Component Architecture

### 5.1 Component Tree

```
Layout (layouts::app.blade.php)
├── Header (components::header)
│   ├── Logo
│   ├── Navigation (dynamic: categories, series)
│   ├── SearchBar (components::search-bar)
│   ├── ThemeToggle (components::theme-toggle)
│   └── UserMenu (components::user-menu)
├── Main Content Slot
│   ├── HomePage (pages::home-page)
│   │   └── ArticleCard[] (components::article-card)
│   ├── ArticleShow (pages::article-show)
│   │   ├── ProgressBar (components::reading-progress)
│   │   ├── TableOfContents (components::table-of-contents)
│   │   ├── ArticleContent
│   │   ├── ShareButtons (components::share-buttons)
│   │   ├── RelatedPosts (components::related-posts)
│   │   └── CommentSection (components::comment-section)
│   │       └── CommentItem[] (components::comment-item)
│   ├── CategoryShow (pages::category-show)
│   ├── TagShow (pages::tag-show)
│   ├── SearchPage (pages::search-page)
│   ├── Login (pages::auth.login)
│   ├── Register (pages::auth.register)
│   ├── ForgotPassword (pages::auth.forgot-password)
│   └── Profile (pages::auth.profile)
└── Footer (components::footer)
```

### 5.2 SPA Routing (Livewire 4)

```php
// routes/web.php
use App\Livewire\Pages;

// Public pages
Route::livewire('/', Pages\HomePage::class)->name('home');
Route::livewire('/articles/{article:slug}', Pages\ArticleShow::class)->name('articles.show');
Route::livewire('/category/{category:slug}', Pages\CategoryShow::class)->name('categories.show');
Route::livewire('/tag/{tag:slug}', Pages\TagShow::class)->name('tags.show');
Route::livewire('/search', Pages\SearchPage::class)->name('search');

// Auth pages
Route::livewire('/login', Pages\Auth\Login::class)
    ->name('login')
    ->middleware('guest');
Route::livewire('/register', Pages\Auth\Register::class)
    ->name('register')
    ->middleware('guest');
Route::livewire('/forgot-password', Pages\Auth\ForgotPassword::class)
    ->name('password.request')
    ->middleware('guest');
Route::livewire('/profile', Pages\Auth\Profile::class)
    ->name('profile')
    ->middleware('auth');

// SEO routes
Route::get('/sitemap.xml', [App\Http\Controllers\SeoController::class, 'sitemap']);
Route::get('/robots.txt', [App\Http\Controllers\SeoController::class, 'robots']);
Route::get('/feed', [App\Http\Controllers\SeoController::class, 'feed']);
```

### 5.3 Key Livewire Patterns

#### Lazy Loading (Performance)
```php
// Components that can load after initial render
#[Lazy]
final class CommentSection extends Component
{
    // Loads after page is interactive
}
```

#### Optimistic UI (Bookmarks)
```php
final class BookmarkButton extends Component
{
    public Article $article;

    public function toggle(): void
    {
        // Optimistic update first
        $this->dispatch('bookmark-toggled');
        
        // Then persist
        auth()->user()->bookmarks()->toggle($this->article);
    }
}
```

#### Islands Pattern (Independent Updates)
```php
// Components that update independently without re-rendering parent
#[Island]
final class ViewCounter extends Component
{
    // Updates view count without affecting article content
}
```

---

## 6. Filament Admin Architecture

### 6.1 Panel Configuration

```php
// app/Providers/Filament/AdminPanelProvider.php
final class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([...])
            ->font('Inter')
            ->brandName('MugiewBlog Admin')
            ->resources([
                Resources\ArticleResource::class,
                Resources\CategoryResource::class,
                Resources\TagResource::class,
                Resources\CommentResource::class,
                Resources\UserResource::class,
                Resources\MediaResource::class,
                Resources\SeriesResource::class,
            ])
            ->pages([
                Pages\Dashboard::class,
            ])
            ->widgets([
                Widgets\StatsOverview::class,
                Widgets\ArticlesChart::class,
                Widgets\CommentsChart::class,
                Widgets\RecentArticles::class,
                Widgets\PendingComments::class,
            ])
            ->middleware([
                'auth',
                'verified',
                'role:admin,editor',
            ]);
    }
}
```

### 6.2 Article Resource Design

```php
final class ArticleResource extends Resource
{
    protected static ?string $model = Article::class;

    // Form schema — used for Create & Edit
    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Content')->schema([
                TextInput::make('title')->required()->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, $set) => 
                        $set('slug', Str::slug($state))),
                TextInput::make('slug')->required()->unique(ignoreRecord: true),
                Textarea::make('excerpt')->rows(3)->autosize(),
                RichEditor::make('content_md')  // Markdown editor
                    ->required()
                    ->fileAttachmentsDirectory('articles/attachments'),
                Select::make('category_id')
                    ->relationship('category', 'name')
                    ->createOptionForm([...]),
                Select::make('tags')
                    ->relationship('tags', 'name')
                    ->multiple()
                    ->createOptionForm([...]),
            ])->columns(1),
            
            Section::make('Media')->schema([
                FileUpload::make('featured_image')
                    ->image()
                    ->directory('articles/featured')
                    ->imageEditor()
                    ->optimize('webp'),
            ]),
            
            Section::make('Publishing')->schema([
                Select::make('status')
                    ->options(ArticleStatus::class)
                    ->default('draft')
                    ->required(),
                DateTimePicker::make('scheduled_at')
                    ->visible(fn ($get) => $get('status') === 'scheduled'),
                Toggle::make('is_featured'),
            ]),
            
            Section::make('SEO')->schema([
                TextInput::make('meta_title')
                    ->maxLength(60)
                    ->hint(fn ($state) => strlen($state ?? '') . '/60'),
                Textarea::make('meta_description')
                    ->maxLength(160)
                    ->hint(fn ($state) => strlen($state ?? '') . '/160'),
            ]),
        ]);
    }

    // Table columns
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('featured_image')->square(),
                TextColumn::make('title')->searchable()->sortable()->limit(50),
                TextColumn::make('author.name')->sortable(),
                TextColumn::make('category.name')->badge(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (ArticleStatus $state): string => $state->color()),
                TextColumn::make('published_at')->dateTime()->sortable(),
                TextColumn::make('view_count')->sortable()->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')->options(ArticleStatus::class),
                SelectFilter::make('category')->relationship('category', 'name'),
                Filter::make('is_featured')->toggle(),
            ])
            ->actions([
                EditAction::make(),
                Action::make('publish')
                    ->action(fn (Article $record) => $record->publish())
                    ->visible(fn (Article $record) => $record->canBePublished()),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    // Bulk publish, bulk change category, etc.
                ]),
            ]);
    }
}
```

### 6.3 Dashboard Widgets

| Widget | Type | Data Source |
|---|---|---|
| StatsOverview | StatsWidget | Total articles, published, pending comments, users |
| ArticlesChart | ChartWidget | Articles published per month (6 months) |
| CommentsChart | ChartWidget | Comments per month |
| RecentArticles | TableWidget | Last 5 articles |
| PendingComments | TableWidget | Comments awaiting moderation |

---

## 7. Authentication & Authorization

### 7.1 Guard & Policy Architecture

```
Authentication Guards:
├── web (default) — Standard session-based auth
└── (future) api — Sanctum token-based for cross-posting API

Policies:
├── ArticlePolicy
│   ├── viewAny — always true (public)
│   ├── view — true for published, or if author/editor/admin
│   ├── create — authenticated user
│   ├── update — author or editor/admin
│   ├── delete — author or admin
│   ├── publish — editor or admin
│   └── restore — admin only
├── CommentPolicy
│   ├── create — authenticated & verified email
│   ├── update — comment author (within 30 min)
│   ├── delete — comment author or admin
│   ├── approve — editor or admin
│   └── reject — editor or admin
└── UserPolicy
    ├── viewAny — admin only
    ├── view — own profile or admin
    ├── update — own profile or admin
    ├── delete — admin only
    └── assignRole — admin only
```

### 7.2 OAuth Flow (GitHub & Google)

```
1. User clicks "Login with GitHub/Google"
2. Redirect to OAuth provider
3. Callback → SocialiteController@handleCallback
4. Find or create user by email
5. Login user
6. Redirect to intended URL or home

Providers configured in config/services.php:
- github (client_id, client_secret, redirect)
- google (client_id, client_secret, redirect)
```

### 7.3 Passkey/WebAuthn 2FA Flow

```
1. Admin enables 2FA in profile settings
2. Server generates WebAuthn registration options
3. Browser creates credential (fingerprint, face, security key)
4. Server stores credential public key
5. On login: challenge → verify signature → grant access

Laravel 13 provides native Passkey support via:
- webauthn_register() helper
- webauthn_authenticate() helper
```

---

## 8. Caching Architecture

### 8.1 Cache Strategy Matrix

| Data | Cache Key | TTL | Tag | Invalidation Trigger |
|---|---|---|---|---|
| Homepage (paginated) | `articles.paginated.{page}.{category?}` | 30 min | `articles` | Article CRUD, category change |
| Article detail | `articles.show.{slug}` | 60 min | `articles` | Article update/delete |
| Related posts | `articles.related.{id}` | 60 min | `articles` | Article update/delete |
| Categories (all) | `categories.all` | 24 hr | `categories` | Category CRUD |
| Tags (all) | `tags.all` | 24 hr | `tags` | Tag CRUD |
| Sitemap | `sitemap.xml` | 24 hr | — | Article publish/unpublish |
| RSS Feed | `rss.feed` | 1 hr | `articles` | Article publish |
| User profile | `user.{id}.profile` | 30 min | — | Profile update |
| Trending articles | `articles.trending` | 1 hr | `articles` | View count update |

### 8.2 Cache Implementation

```php
final class ArticleService
{
    public function getPaginated(int $page = 1, ?string $category = null): LengthAwarePaginator
    {
        $cacheKey = "articles.paginated.{$page}." . ($category ?? 'all');
        
        return Cache::tags(['articles'])->remember(
            $cacheKey,
            now()->addMinutes(30),
            fn () => Article::published()
                ->when($category, fn ($q) => $q->whereHas('category', 
                    fn ($q) => $q->where('slug', $category)))
                ->with(['author', 'category', 'tags'])
                ->withCount('comments')
                ->latest('published_at')
                ->paginate(12)
        );
    }

    public function getBySlug(string $slug): Article
    {
        return Cache::tags(['articles'])->remember(
            "articles.show.{$slug}",
            now()->addHour(),
            fn () => Article::published()
                ->where('slug', $slug)
                ->with(['author', 'category', 'tags'])
                ->withCount('comments')
                ->firstOrFail()
        );
    }

    // Laravel 13: extend TTL without re-reading
    public function touchCache(string $slug): void
    {
        Cache::touch("articles.show.{$slug}");
    }
}
```

### 8.3 Cache Invalidation (Observer)

```php
final class ArticleObserver
{
    public function saved(Article $article): void
    {
        Cache::tags(['articles'])->flush();
    }

    public function deleted(Article $article): void
    {
        Cache::tags(['articles'])->flush();
    }
}
```

---

## 9. Search Architecture

### 9.1 Search Strategy

**Phase 1 (MVP): MySQL FULLTEXT Search**
- Native MySQL `MATCH ... AGAINST` via Eloquent
- Index pada `title`, `excerpt`, `content_md`
- Boolean mode untuk operator AND/OR

```php
final class SearchService
{
    #[\NoDiscard]
    public function search(string $query, int $page = 1): LengthAwarePaginator
    {
        return Article::published()
            ->whereFullText(['title', 'excerpt', 'content_md'], $query, [
                'mode' => 'boolean',
            ])
            ->with(['author', 'category', 'tags'])
            ->withCount('comments')
            ->latest('published_at')
            ->paginate(12);
    }
}
```

**Phase 2 (Future): Laravel Scout + Meilisearch**
- Typo-tolerant search
- Faceted filtering (kategori, tag, author)
- Instant search (as-you-type)
- Vector/semantic search via Laravel 13 native

### 9.2 Livewire Search Component

```php
final class SearchBar extends Component
{
    #[Locked]
    public string $query = '';
    
    public array $results = [];
    
    // Debounced search (300ms after last keystroke)
    #[On('search-updated')]
    public function search(): void
    {
        if (strlen($this->query) < 2) {
            $this->results = [];
            return;
        }
        
        $this->results = app(SearchService::class)
            ->quickSearch($this->query)
            ->take(5)
            ->toArray();
    }
}
```

---

## 10. SEO Architecture

### 10.1 Dynamic Meta Tags

```php
// In SPA layout or per-page component
final class ArticleShow extends Component
{
    public Article $article;

    public function mount(Article $article): void
    {
        // Must be published or author viewing
        abort_unless($article->isPublished() || $article->user_id === auth()->id(), 404);
    }

    public function getSeoMetadata(): array
    {
        return [
            'title' => $this->article->meta_title ?? $this->article->title,
            'description' => $this->article->meta_description ?? Str::limit($this->article->excerpt, 160),
            'og_type' => 'article',
            'og_image' => asset($this->article->featured_image),
            'published_time' => $this->article->published_at?->toIso8601String(),
            'author' => $this->article->author->name,
        ];
    }
}
```

### 10.2 Structured Data (JSON-LD)

```blade
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Article",
  "headline": "{{ $article->title }}",
  "author": {
    "@type": "Person",
    "name": "{{ $article->author->name }}"
  },
  "datePublished": "{{ $article->published_at->toIso8601String() }}",
  "image": "{{ asset($article->featured_image) }}"
}
</script>
```

### 10.3 Sitemap & RSS

```php
// App\Services\SeoService
final class SeoService
{
    #[\NoDiscard]
    public function generateSitemap(): string
    {
        // Cached for 24 hours
        return Cache::remember('sitemap.xml', now()->addDay(), function () {
            $articles = Article::published()->get(['slug', 'updated_at']);
            $categories = Category::all(['slug', 'updated_at']);
            
            return view('seo.sitemap', compact('articles', 'categories'))->render();
        });
    }

    #[\NoDiscard]
    public function generateFeed(): string
    {
        return Cache::remember('rss.feed', now()->addHour(), function () {
            $articles = Article::published()
                ->latest('published_at')
                ->limit(20)
                ->get();
            
            return response()->view('seo.feed', compact('articles'))
                ->header('Content-Type', 'application/rss+xml');
        });
    }
}
```

---

## 11. Security Architecture

### 11.1 Security Headers Middleware

```php
final class SecurityHeadersMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        
        return $response->withHeaders([
            'X-Frame-Options' => 'DENY',
            'X-Content-Type-Options' => 'nosniff',
            'X-XSS-Protection' => '1; mode=block',
            'Referrer-Policy' => 'strict-origin-when-cross-origin',
            'Permissions-Policy' => 'camera=(), microphone=(), geolocation=()',
            'Strict-Transport-Security' => 'max-age=31536000; includeSubDomains',
            'Content-Security-Policy' => $this->buildCsp(),
        ]);
    }

    private function buildCsp(): string
    {
        return "default-src 'self'; "
            . "script-src 'self' 'unsafe-inline' 'unsafe-eval'; "
            . "style-src 'self' 'unsafe-inline'; "
            . "img-src 'self' data: https:; "
            . "font-src 'self'; "
            . "connect-src 'self'";
    }
}
```

### 11.2 Rate Limiting

```php
// In RouteServiceProvider or bootstrap/app.php
RateLimiter::for('login', fn (Request $request) => 
    Limit::perMinute(5)->by($request->ip() . '|' . $request->input('email'))
);

RateLimiter::for('register', fn (Request $request) => 
    Limit::perHour(3)->by($request->ip())
);

RateLimiter::for('search', fn (Request $request) => 
    Limit::perMinute(30)->by($request->ip())
);

RateLimiter::for('comment', fn (Request $request) => 
    Limit::perMinute(5)->by($request->user()?->id ?: $request->ip())
);

RateLimiter::for('api', fn (Request $request) => 
    Limit::perMinute(60)->by($request->user()?->id ?: $request->ip())
);
```

### 11.3 File Upload Security

```php
// In Filament ArticleResource form validation
FileUpload::make('featured_image')
    ->image()
    ->maxSize(5120) // 5MB
    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/gif'])
    ->directory('articles/featured')
    ->imageEditor()
    ->optimize('webp')
    ->storeFileNamesIn('original_filename')
    // Server-side validation in model observer
```

---

## 12. Queue & Job Architecture

### 12.1 Job Categories

| Queue | Jobs | Priority |
|---|---|---|
| `default` | General processing | Normal |
| `emails` | Email notifications, newsletter broadcasts | Normal |
| `images` | Image optimization, WebP conversion | Low |
| `search` | Search index updates (future) | Low |

### 12.2 Key Jobs

```php
// Auto-publish scheduled articles
final class PublishScheduledArticles extends Command
{
    protected $signature = 'articles:publish-scheduled';
    
    public function handle(): void
    {
        Article::where('status', 'scheduled')
            ->where('scheduled_at', '<=', now())
            ->each(fn (Article $article) => $article->publish());
    }
}
// Scheduled in: Kernel::schedule() → ->everyMinute()

// Send comment notification to author
final class SendCommentNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Comment $comment,
    ) {}

    public function handle(): void
    {
        $author = $this->comment->article->author;
        
        $author->notify(new NewCommentNotification($this->comment));
    }
}

// Optimize uploaded image
final class OptimizeImage implements ShouldQueue
{
    public function handle(ImageOptimizer $optimizer): void
    {
        $optimizer->toWebP($this->path);
        $optimizer->resize($this->path, 1200, 630); // OG image size
    }
}
```

### 12.3 Laravel 13 Queue Routing

```php
// app/Providers/AppServiceProvider.php
public function boot(): void
{
    Queue::route('emails', [
        SendCommentNotification::class,
        SendNewsletterBroadcast::class,
    ]);

    Queue::route('images', [
        OptimizeImage::class,
    ]);
}
```

---

## 13. Dark Mode Architecture

### 13.1 Strategy: Class-based with No FOUC

```
┌─────────────────────────────────────────────────────────────┐
│ 1. Inline blocking script in <head>                         │
│    ↓                                                        │
│ 2. Read localStorage('theme') or prefers-color-scheme       │
│    ↓                                                        │
│ 3. Add 'dark' or 'light' class to <html> BEFORE CSS loads   │
│    ↓                                                        │
│ 4. CSS loads with correct theme already applied             │
│    ↓                                                        │
│ 5. No flicker — CSS never sees the "wrong" mode             │
│    ↓                                                        │
│ 6. Alpine.js ThemeToggle component handles user switches    │
└─────────────────────────────────────────────────────────────┘
```

### 13.2 Implementation

**Inline Script (in layouts::app.blade.php `<head>`):**
```blade
<script>
(function() {
    const theme = localStorage.getItem('theme') ??
        (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
    document.documentElement.classList.add(theme);
})();
</script>
```

**Tailwind CSS v4 Configuration (app.css):**
```css
@import "tailwindcss";

@variant dark (&:where(.dark, .dark *));

@theme {
    --color-primary-50: oklch(0.97 0.01 260);
    --color-primary-500: oklch(0.55 0.2 260);
    --color-primary-900: oklch(0.21 0.05 260);
}
```

**ThemeToggle Component (Alpine.js):**
```blade
<div x-data="themeToggle">
    <button @click="toggle" aria-label="Toggle theme">
        <x-heroicon-o-sun x-show="theme === 'dark'" class="h-5 w-5" />
        <x-heroicon-o-moon x-show="theme === 'light'" class="h-5 w-5" />
    </button>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('themeToggle', () => ({
        theme: document.documentElement.classList.contains('dark') ? 'dark' : 'light',
        
        toggle() {
            this.theme = this.theme === 'dark' ? 'light' : 'dark';
            document.documentElement.classList.toggle('dark');
            localStorage.setItem('theme', this.theme);
        },
        
        // Watch system preference changes
        init() {
            window.matchMedia('(prefers-color-scheme: dark)')
                .addEventListener('change', (e) => {
                    if (!localStorage.getItem('theme')) {
                        this.theme = e.matches ? 'dark' : 'light';
                        document.documentElement.classList.toggle('dark', e.matches);
                    }
                });
        }
    }));
});
</script>
```

---

## 14. Docker & Deployment Architecture

### 14.1 Docker Image (Multi-stage)

```dockerfile
# Stage 1: Build
FROM dunglas/frankenphp:latest-php8.5 AS build
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --optimize-autoloader
COPY package.json bun.lock ./
RUN bun install --frozen-lockfile --production
COPY . .
RUN bun run build

# Stage 2: Production
FROM dunglas/frankenphp:latest-php8.5 AS production
RUN install-php-extensions \
    pdo_mysql \
    redis \
    gd \
    exif \
    intl \
    opcache

COPY --from=build /app /app
RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache

USER www-data
HEALTHCHECK --interval=30s --timeout=3s \
    CMD curl -f http://localhost/health || exit 1

ENV FRANKENPHP_CONFIG="worker ./public/index.php"
ENV SERVER_NAME=":80"
```

### 14.2 Docker Compose (Full Stack)

```yaml
version: '3.8'

services:
  app:
    build:
      context: .
      target: production
    ports:
      - "${APP_PORT:-80}:80"
      - "${APP_PORT_SSL:-443}:443"
    environment:
      FRANKENPHP_CONFIG: "worker ./public/index.php"
      SERVER_NAME: "${APP_DOMAIN:-localhost}"
    depends_on:
      mysql:
        condition: service_healthy
      redis:
        condition: service_healthy
    volumes:
      - ./storage:/app/storage

  mysql:
    image: mysql:8.4
    environment:
      MYSQL_ROOT_PASSWORD: "${DB_PASSWORD}"
      MYSQL_DATABASE: "${DB_DATABASE}"
      MYSQL_USER: "${DB_USERNAME}"
      MYSQL_PASSWORD: "${DB_PASSWORD}"
    volumes:
      - mysql_data:/var/lib/mysql
    healthcheck:
      test: ["CMD", "mysqladmin", "ping", "-h", "localhost"]
      interval: 10s
      timeout: 5s
      retries: 5

  redis:
    image: redis:7-alpine
    volumes:
      - redis_data:/data
    healthcheck:
      test: ["CMD", "redis-cli", "ping"]
      interval: 10s
      timeout: 5s
      retries: 5

  queue:
    build:
      context: .
      target: production
    command: php artisan queue:work --tries=3 --sleep=3
    depends_on:
      - app
    environment:
      FRANKENPHP_CONFIG: "worker ./public/index.php"

  scheduler:
    build:
      context: .
      target: production
    command: php artisan schedule:work
    depends_on:
      - app

volumes:
  mysql_data:
  redis_data:
```

---

## 15. Notification Architecture

### 15.1 Strategy
Database notifications via Filament bell icon + optional email fallback.

```
Events → Listeners (ShouldQueue) → Notification Classes → Database + Mail
                                              ↓
                              Filament Bell Icon (polling 20s)
```

### 15.2 Channel Configuration

| Channel | Use Case | Queue |
|---|---|---|
| `database` | Admin bell notifications, user notification center | `notifications` |
| `mail` | Email for comment replies, article published, welcome | `emails` |

### 15.3 Notification Classes

```php
// app/Notifications/NewCommentNotification.php
final class NewCommentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Comment $comment,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];  // In-app + email
    }

    #[ArrayShape(['comment_id' => 'int', ...])]
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'new_comment',
            'comment_id' => $this->comment->id,
            'commenter_name' => $this->comment->user->name,
            'article_title' => $this->comment->article->title,
            'article_slug' => $this->comment->article->slug,
            'content_preview' => Str::limit($this->comment->content, 100),
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Komentar baru di {$this->comment->article->title}")
            ->line("{$this->comment->user->name} berkomentar di artikel Anda.")
            ->action('Lihat Komentar', route('articles.show', $this->comment->article->slug))
            ->line(Str::limit($this->comment->content, 200));
    }
}

// app/Notifications/CommentApprovedNotification.php
final class CommentApprovedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['database'];  // In-app only, no email spam
    }
}
```

### 15.4 Event-Driven Dispatch

```php
// Event: app/Events/CommentCreated.php
final class CommentCreated
{
    use Dispatchable, SerializesModels;

    public function __construct(public Comment $comment) {}
}

// Listener: app/Listeners/SendCommentNotifications.php
#[On('comment.created')]
final class SendCommentNotifications implements ShouldQueue
{
    public function handle(CommentCreated $event): void
    {
        // Notify article author (if not self-comment)
        if ($event->comment->user_id !== $event->comment->article->user_id) {
            $event->comment->article->author
                ->notify(new NewCommentNotification($event->comment));
        }

        // Notify admins/editors for moderation
        User::whereIn('role', ['admin', 'editor'])->each(
            fn (User $user) => $user->notify(new CommentNeedsModeration($event->comment))
        );
    }
}
```

### 15.5 Queue Routing (Laravel 13)

```php
// bootstrap/app.php
Queue::route('notifications', [
    SendCommentNotifications::class,
]);
```

### 15.6 Filament Bell Integration

Panel provider sudah dikonfigurasi:
```php
->databaseNotifications()              // Bell icon with dropdown
->databaseNotificationsPolling('20s')  // Poll every 20 detik (Filament default: 30s)
```

Bell icon otomatis muncul di Filament top bar, menampilkan:
- Unread count badge
- Dropdown dengan 5 notifikasi terbaru
- "Mark all as read" button
- "View all" link ke halaman notifikasi

### 15.7 Notification Types & Triggers

| Trigger | Notification | Recipient | Channels |
|---|---|---|---|
| Comment created | `NewCommentNotification` | Article author | database + mail |
| Comment needs approval | `CommentNeedsModeration` | Admin, Editor | database |
| Comment approved | `CommentApprovedNotification` | Comment author | database |
| Article published (Phase 2) | `ArticlePublishedNotification` | Newsletter subs | mail |
| Welcome (Phase 2) | `WelcomeNotification` | New user | database + mail |

---

## 16. Testing Architecture

### 15.1 Test Structure

```
tests/
├── Unit/
│   ├── Models/
│   │   ├── ArticleTest.php
│   │   ├── CommentTest.php
│   │   └── UserTest.php
│   ├── Services/
│   │   ├── ArticleServiceTest.php
│   │   ├── SearchServiceTest.php
│   │   └── SeoServiceTest.php
│   └── Helpers/
│       └── ReadingTimeTest.php
├── Feature/
│   ├── Articles/
│   │   ├── ArticleListTest.php
│   │   ├── ArticleShowTest.php
│   │   └── ArticleCrudTest.php
│   ├── Comments/
│   │   └── CommentTest.php
│   ├── Auth/
│   │   ├── LoginTest.php
│   │   ├── RegisterTest.php
│   │   └── OAuthTest.php
│   ├── Search/
│   │   └── SearchTest.php
│   └── Livewire/
│       ├── HomePageTest.php
│       ├── ArticleShowTest.php
│       └── ThemeToggleTest.php
└── Browser/ (optional, Phase 2)
    └── Pages/
        └── HomePageTest.php
```

### 15.2 Key Test Patterns

```php
// Unit test — Service
test('article service returns paginated published articles', function () {
    Article::factory()->count(15)->published()->create();
    Article::factory()->count(3)->draft()->create();

    $result = app(ArticleService::class)->getPaginated(1);

    expect($result)->toHaveCount(12);
    expect($result->total())->toBe(15);
});

// Feature test — Livewire component
test('home page displays published articles', function () {
    Article::factory()->count(5)->published()->create();

    Livewire::test(Pages\HomePage::class)
        ->assertSee(Article::first()->title)
        ->assertStatus(200);
});

// Feature test — Auth
test('user can register with valid data', function () {
    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'username' => 'testuser',
        'password' => 'Str0ng!Pass',
        'password_confirmation' => 'Str0ng!Pass',
    ]);

    $response->assertRedirect('/');
    $this->assertAuthenticated();
    expect(User::where('email', 'test@example.com')->exists())->toBeTrue();
});
```

---

## Appendix: Key Architectural Decisions Log

| Decision | Date | Rationale |
|---|---|---|
| Livewire 4 SPA over Inertia | 2026-05-15 | Simpler architecture, no JS framework dependency, better SEO |
| MySQL FULLTEXT over Meilisearch (MVP) | 2026-05-15 | Fewer dependencies, sufficient for MVP, upgrade later |
| Filament 5 over custom admin | 2026-05-15 | Faster development, built-in Livewire 4 support |
| FrankenPHP worker mode over FPM | 2026-05-15 | 3-4x performance, simpler deployment |
| Redis tags for cache invalidation | 2026-05-15 | Granular cache flushing without losing all cache |
| Class-based dark mode over media query | 2026-05-15 | Better control, localStorage persistence, anti-FOUC |

---

**Document Status:** ✅ Complete — Ready for Epic Breakdown  
**Next Step:** Generate Epics → Stories → Implementation tasks for Codex
