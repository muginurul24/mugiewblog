# Product Requirement Document (PRD)
## MugiewBlog — Professional Tech & Investment Blog

**Version:** 1.0.0  
**Date:** 2026-05-15  
**Author:** Rafi + Claw Kun  
**Status:** Draft → Ready for Implementation

---

## 1. Visi & Tujuan Produk

### 1.1 Visi
Menjadi blog profesional rujukan developer Indonesia untuk topik coding, teknologi, infrastruktur, cloud, devops, investasi teknologi, dan software engineering — dengan performa kelas dunia, UI premium, dan aksesibilitas tinggi.

### 1.2 Tujuan Produk
1. **Edukasi berkualitas** — Artikel teknis mendalam dengan code snippet interaktif dan syntax highlighting profesional.
2. **Monetisasi jangka panjang** — Membangun audiens loyal sebagai fondasi untuk newsletter, kursus, atau sponsorship.
3. **Portfolio showcase** — Demonstrasi kemampuan teknis Rafi sebagai full-stack developer.
4. **SEO dominance** — Menargetkan long-tail keywords di niche coding/tech berbahasa Indonesia.
5. **Developer experience** — Admin panel yang powerful via Filament 5, workflow penulisan yang seamless.

### 1.3 Unique Value Proposition (UVP)
- **Performance-first:** TTFB < 200ms via FrankenPHP worker mode, Lighthouse ≥ 95.
- **Contextual search:** Semantic/vector search bawaan Laravel 13 untuk pencarian berbasis makna.
- **Anti-FOUC dark mode:** Zero flicker, class-strategy dengan inline blocking script.
- **SPA reading experience:** Navigasi tanpa reload via Livewire 4, progress bar baca, daftar isi otomatis.

---

## 2. Target Audiens & Persona

### 2.1 Primary Personas

| Persona | Deskripsi | Kebutuhan Utama |
|---|---|---|
| **Rizky — Junior Dev** | 22 tahun, fresh grad, belajar Laravel & React | Tutorial terstruktur, code snippet, best practice |
| **Dinda — Mid-Level Engineer** | 27 tahun, backend engineer, switch ke Go/Rust | Artikel performa, arsitektur, system design |
| **Bram — Tech Lead** | 34 tahun, lead engineer di startup | Cloud infra, devops, scaling, decision-making |
| **Sari — Investor Tech** | 30 tahun, investor saham teknologi & kripto | Analisis fundamental, tren startup, market insight |
| **Rafi — Author/Admin** | Pemilik blog, full-stack developer | Workflow penulisan efisien, moderasi, analytics |

### 2.2 Secondary Personas
- **Student CS** — Mencari referensi tugas akhir dan proyek.
- **CTO/VP Engineering** — Mengevaluasi stack dan tren arsitektur.
- **Freelancer** — Mencari best practice development workflow.

### 2.3 Demografi Target
- **Bahasa:** Indonesia (primer), English (opsional untuk beberapa artikel)
- **Region:** Indonesia, Southeast Asia
- **Device:** 60% desktop, 35% mobile, 5% tablet
- **Tech literacy:** Intermediate to advanced

---

## 3. Analisis Kompetitif

### 3.1 Kompetitor Langsung

| Kompetitor | Kekuatan | Kelemahan | Strategi Diferensiasi |
|---|---|---|---|
| **codepolitan.com** | Konten banyak, SEO kuat, events | UX lambat, ads invasif, admin panel tradisional | Performa superior, UX premium, zero ads |
| **medium.com** | Ekosistem besar, discoverability | Paywall agresif, kontrol terbatas, brand Medium | Full control, open access, no paywall |
| **dev.to** | Community-driven, open source | UX generik, kustomisasi minim, seragam | Personal branding premium, kustomisasi penuh |

### 3.2 Kompetitor Tidak Langsung
- **Substack** — Newsletter-first, bukan blog publik.
- **Hashnode** — Developer blog hosting, kurang kontrol.
- **YouTube/TikTok** — Konten video, melengkapi bukan menggantikan.

### 3.3 Competitive Advantage Matrix

| Fitur | MugiewBlog | CodePolitan | Medium | Dev.to |
|---|---|---|---|---|
| Performa (Lighthouse) | ≥ 95 | ~60 | ~75 | ~80 |
| Syntax Highlighting | Torchlight/Shiki | Prism.js | Built-in | Built-in |
| Dark Mode Anti-FOUC | ✅ | ❌ | ✅ (partial) | ✅ (partial) |
| Semantic Search | ✅ | ❌ | ❌ | ❌ |
| Admin Panel | Filament 5 | Custom | Medium CMS | Dev.to CMS |
| SPA | Livewire 4 | ❌ | ✅ (React) | ✅ (Preact) |
| Multi-language | Phase 3 | ❌ | ❌ | ❌ |
| Open Source | ✅ | ❌ | ❌ | ✅ |

---

## 4. Functional Requirements (MoSCoW Priority)

### 4.1 Must Have — MVP Phase 1

#### Manajemen Artikel (FR-ART)
| ID | Requirement | Detail |
|---|---|---|
| FR-ART-01 | CRUD Artikel | Create, Read, Update, Delete artikel via Filament admin |
| FR-ART-02 | Status Artikel | Draft, Review, Published, Scheduled (auto-publish via scheduler) |
| FR-ART-03 | Rich Markdown Editor | Editor dengan live preview, toolbar, shortcut keyboard |
| FR-ART-04 | Syntax Highlighting | Torchlight atau Shiki untuk code blocks |
| FR-ART-05 | Auto-save Draft | Simpan draft otomatis ke database setiap 30 detik |
| FR-ART-06 | Slug Unik | Auto-generate dari judul, bisa diedit manual, enforce uniqueness |
| FR-ART-07 | Kategori & Tag | Polymorphic relationship, multiple tags per article |
| FR-ART-08 | Upload Media | Drag-and-drop, optimasi otomatis ke WebP, random filename |
| FR-ART-09 | Featured Image | Satu gambar utama per artikel dengan alt text |
| FR-ART-10 | Reading Time Estimate | Kalkulasi otomatis berdasarkan word count |

#### Frontend Publik (FR-FE)
| ID | Requirement | Detail |
|---|---|---|
| FR-FE-01 | Homepage | Daftar artikel terbaru, paginasi, filter kategori/tag |
| FR-FE-02 | Pencarian | Full-text search via MySQL + semantic/vector opsional |
| FR-FE-03 | Detail Artikel | Layout bersih, ToC otomatis, progress bar, share buttons |
| FR-FE-04 | Related Posts | Berdasarkan kategori & tag yang sama |
| FR-FE-05 | RSS/Atom Feed | Auto-generate feed untuk RSS reader |

#### Komentar (FR-COM)
| ID | Requirement | Detail |
|---|---|---|
| FR-COM-01 | Nested Comments | Reply berjenjang maksimal 3 level |
| FR-COM-02 | Moderation Queue | Admin approve/reject komentar via Filament |
| FR-COM-03 | Spam Detection | Integrasi dengan Akismet atau rule-based detection |
| FR-COM-04 | Email Notification | Notifikasi ke author saat ada komentar baru |

#### Autentikasi & Otorisasi (FR-AUTH)
| ID | Requirement | Detail |
|---|---|---|
| FR-AUTH-01 | Role System | Admin, Editor, Author via Laravel Policy & Gates |
| FR-AUTH-02 | Registration & Login | Email/password + OAuth (GitHub, Google) |
| FR-AUTH-03 | Email Verification | Wajib verifikasi sebelum bisa komentar |
| FR-AUTH-04 | Forgot Password | Reset via email link dengan TTL 60 menit |
| FR-AUTH-05 | 2FA for Admin | Passkey authentication (WebAuthn) via Laravel 13 |
| FR-AUTH-06 | User Profile | Edit nama, bio, avatar, social links |

#### Theme & UX (FR-UX)
| ID | Requirement | Detail |
|---|---|---|
| FR-UX-01 | Dark/Light Mode | Toggle: Light / Dark / System, anti-FOUC via inline script |
| FR-UX-02 | WCAG 2.1 AA | Contrast ratio, keyboard navigation, screen reader |
| FR-UX-03 | Mobile Responsive | Mobile-first design, semua halaman responsif |
| FR-UX-04 | Progress Bar | Visual indicator seberapa jauh pembaca membaca artikel |

#### SEO (FR-SEO)
| ID | Requirement | Detail |
|---|---|---|
| FR-SEO-01 | Meta Tags | Dynamic title, description, OG, Twitter Card |
| FR-SEO-02 | Structured Data | Article schema JSON-LD |
| FR-SEO-03 | Sitemap.xml | Auto-generate, auto-update on publish |
| FR-SEO-04 | robots.txt | Auto-generate dari konfigurasi |
| FR-SEO-05 | Canonical URLs | Hindari duplicate content |
| FR-SEO-06 | Breadcrumbs | Structured breadcrumb untuk artikel & kategori |

#### Admin Panel (FR-ADM)
| ID | Requirement | Detail |
|---|---|---|
| FR-ADM-01 | Filament Dashboard | Overview: total articles, comments pending, user stats |
| FR-ADM-02 | Article Management | List, filter, bulk actions (publish, unpublish, delete) |
| FR-ADM-03 | User Management | CRUD users, assign roles, suspend accounts |
| FR-ADM-04 | Media Library | Browse, search, delete uploaded media |
| FR-ADM-05 | Comment Moderation | Queue with approve/reject/spam actions |
| FR-ADM-06 | Category/Tag Management | CRUD with slug auto-generation |

### 4.2 Should Have — Phase 2

| ID | Requirement | Detail |
|---|---|---|
| FR-P2-01 | Newsletter | Double opt-in, email broadcast via queue (MJML templates) |
| FR-P2-02 | Scheduled Publish | Auto publish/unpublish berdasarkan tanggal |
| FR-P2-03 | Article Series | Kolom seri artikel dengan navigasi next/previous |
| FR-P2-04 | Bookmarks | Simpan artikel favorit untuk user login |
| FR-P2-05 | External Embeds | GitHub Gist, CodePen, CodeSandbox, YouTube oEmbed |
| FR-P2-06 | Cross-posting | Share ke Dev.to / Medium via API |
| FR-P2-07 | Analytics Dashboard | Self-hosted Plausible/Umami atau GA4 integration |

### 4.3 Could Have — Phase 3

| ID | Requirement | Detail |
|---|---|---|
| FR-P3-01 | i18n | Multi-language UI + konten (EN/ID primer) |
| FR-P3-02 | Theme Builder | Kustomisasi warna, font, layout untuk pembaca |
| FR-P3-03 | Forum/Discussion | Integrasi forum ringan untuk Q&A |
| FR-P3-04 | A/B Testing | A/B test judul, featured image, layout |

---

## 5. User Stories & Acceptance Criteria

### 5.1 Pembaca (Reader)

**US-READ-01: Melihat daftar artikel terbaru**
> Sebagai pembaca, saya ingin melihat daftar artikel terbaru di homepage agar bisa menemukan konten yang relevan.

**Acceptance Criteria:**
- [ ] Homepage menampilkan artikel published dengan paginasi 12 artikel/halaman
- [ ] Setiap card menampilkan featured image, judul, excerpt, author, tanggal, reading time, kategori
- [ ] Filter berdasarkan kategori dan tag berfungsi (Livewire, tanpa reload)
- [ ] Placeholder muncul saat loading (skeleton state)
- [ ] Halaman kosong menampilkan "Belum ada artikel" state

**US-READ-02: Mencari artikel secara kontekstual**
> Sebagai pembaca, saya ingin mencari artikel tentang "PHP 8.5 Pipe Operator" dan mendapatkan hasil yang paling relevan.

**Acceptance Criteria:**
- [ ] Search bar di header, bisa diakses dari semua halaman
- [ ] Hasil pencarian menampilkan judul, excerpt, kategori, tanggal
- [ ] Full-text search MySQL berfungsi dengan relevansi yang baik
- [ ] Pencarian menampilkan "Tidak ditemukan" state jika kosong
- [ ] Mendukung pencarian partial-match dan stemming

**US-READ-03: Membaca artikel dengan nyaman**
> Sebagai pembaca, saya ingin membaca artikel dengan layout bersih dan fitur navigasi.

**Acceptance Criteria:**
- [ ] Halaman artikel menampilkan judul, author, tanggal, reading time, featured image
- [ ] Daftar isi (ToC) otomatis dari heading markdown
- [ ] Progress bar visual di bagian atas halaman
- [ ] Related posts di bagian bawah (3-5 artikel)
- [ ] Share buttons (Twitter/X, LinkedIn, copy link) berfungsi
- [ ] Code blocks dengan syntax highlighting dan copy button
- [ ] Mobile: readable tanpa horizontal scroll

**US-READ-04: Berkomentar pada artikel**
> Sebagai pembaca yang terdaftar, saya ingin berkomentar dan membalas komentar lain.

**Acceptance Criteria:**
- [ ] Form komentar tersedia di bawah artikel (wajib login)
- [ ] Reply button membuka form inline untuk balasan
- [ ] Nested comments maksimal 3 level
- [ ] Komentar baru masuk moderation queue untuk user baru
- [ ] Paginasi komentar jika > 20 komentar
- [ ] Markdown dasar didukung (bold, italic, code, link)

**US-READ-05: Toggle dark/light mode**
> Sebagai pembaca, saya ingin mengganti tema tanpa flicker atau reload.

**Acceptance Criteria:**
- [ ] Toggle di header: Light / Dark / System
- [ ] Mode disimpan di localStorage, persisten antar sesi
- [ ] Tidak ada flicker (FOUC) saat load halaman
- [ ] Transisi warna halus (CSS transition)
- [ ] Menghormati prefers-color-scheme di System mode

### 5.2 Penulis (Author)

**US-AUTH-01: Menulis artikel baru**
> Sebagai penulis, saya ingin menulis artikel dengan Markdown editor yang nyaman.

**Acceptance Criteria:**
- [ ] Filament form dengan fields: judul, slug, excerpt, konten (markdown), featured image, kategori, tags, status
- [ ] Markdown editor dengan toolbar (bold, italic, heading, code, link, image, list)
- [ ] Live preview toggle (split view)
- [ ] Syntax highlighting aktif di preview
- [ ] Slug auto-generated dari judul, bisa diedit
- [ ] Drag-and-drop featured image dengan preview

**US-AUTH-02: Auto-save draft**
> Sebagai penulis, saya ingin draft tersimpan otomatis tanpa takut kehilangan tulisan.

**Acceptance Criteria:**
- [ ] Auto-save trigger setiap 30 detik saat ada perubahan
- [ ] Status indicator: "Saving..." / "Saved at HH:MM"
- [ ] Draft bisa di-recover jika browser crash/tutup
- [ ] Konfirmasi sebelum meninggalkan halaman dengan unsaved changes

**US-AUTH-03: Menjadwalkan publikasi**
> Sebagai penulis, saya ingin menjadwalkan artikel untuk publish di waktu tertentu.

**Acceptance Criteria:**
- [ ] Status "Scheduled" dengan datetime picker
- [ ] Scheduler Laravel mengecek setiap menit untuk artikel yang waktunya publish
- [ ] Notifikasi email ke author saat artikel published
- [ ] Bisa cancel schedule (kembalikan ke draft)

### 5.3 Admin

**US-ADM-01: Memoderasi komentar**
> Sebagai admin, saya ingin memoderasi komentar dengan efisien.

**Acceptance Criteria:**
- [ ] Filament resource: Comment dengan filter status (pending, approved, spam)
- [ ] Bulk approve/reject/spam actions
- [ ] Preview konten komentar & konteks artikel
- [ ] Auto-mark as spam untuk kata kunci tertentu (configurable)
- [ ] Notifikasi ke user saat komentar disetujui

**US-ADM-02: Mengelola pengguna**
> Sebagai admin, saya ingin mengelola pengguna dan peran mereka.

**Acceptance Criteria:**
- [ ] Filament resource: User dengan filter role
- [ ] Assign/unassign role (Admin, Editor, Author, User)
- [ ] Suspend/activate user account
- [ ] View user activity (komentar, bookmark)

**US-ADM-03: Dashboard analytics**
> Sebagai admin, saya ingin melihat ringkasan performa blog.

**Acceptance Criteria:**
- [ ] Widget: total articles, published/draft count, total users, total comments
- [ ] Chart: articles published per month (6 bulan)
- [ ] Chart: comments per month
- [ ] Recent articles & recent comments lists

---

## 6. Non-Functional Requirements

### 6.1 Tech Stack (Mandatory)

| Layer | Technology | Version |
|---|---|---|
| Backend Framework | Laravel | 13.x |
| Language | PHP | 8.5 |
| Frontend | Livewire SPA | 4.x |
| Admin Panel | Filament | 5.x |
| Runtime | FrankenPHP (Octane) | latest |
| Database | MySQL | 8.4 LTS |
| Cache/Queue/Session | Redis | 7.x |
| CSS Framework | Tailwind CSS | 4.x |
| Package Manager (PHP) | Composer | latest |
| Package Manager (JS) | Bun | latest |
| Testing | Pest | 4.x |
| Code Style | Laravel Pint | latest |

### 6.2 Fitur Laravel 13 yang Wajib Dimanfaatkan

- [x] Native PHP Attributes untuk Model, Job, Command, dan Routes
- [x] Queue Routing via `Queue::route()` untuk job dispatching
- [x] `Cache::touch()` untuk memperpanjang TTL cache
- [x] Semantic/Vector Search untuk pencarian kontekstual
- [x] Passkey Authentication (WebAuthn) untuk admin 2FA
- [x] JSON:API Resources untuk REST API internal

### 6.3 Fitur PHP 8.5 yang Wajib Dimanfaatkan

- [x] URI Extension (`URI\parse_url()`, `URI\resolve()`) untuk URL processing
- [x] Pipe Operator (`|>`) untuk function chaining yang readable
- [x] `clone with` untuk immutable object modification
- [x] `#[\NoDiscard]` attribute untuk method yang return value-nya wajib digunakan
- [x] Typed class constants
- [x] Asymmetric visibility (`public private(set)`) untuk properti read-only

### 6.4 Performa

| Metric | Target | Notes |
|---|---|---|
| Lighthouse Score | ≥ 95 (mobile & desktop) | Production build |
| LCP (Largest Contentful Paint) | < 2s | Cached pages |
| TBT (Total Blocking Time) | < 200ms | Mobile 4G |
| TTFB (Time to First Byte) | < 200ms | Cached, FrankenPHP worker |
| Time to Interactive | < 3s | 4G connection |
| Cache Hit Ratio | > 90% | Public pages |
| Page Size | < 300KB (compressed) | Front page |

### 6.5 Keamanan

- **CSRF Protection:** Laravel default CSRF token di semua form & Livewire
- **XSS Prevention:** Blade auto-escaping, Content Security Policy headers
- **SQL Injection:** Eloquent ORM + parameterized queries, no raw SQL
- **CORS:** Configured `config/cors.php` untuk trusted origins
- **CSP Headers:** `Content-Security-Policy` via middleware
- **HSTS:** `Strict-Transport-Security` header di production
- **Rate Limiting:** Per IP & per user untuk login, register, search, comment
- **File Upload:** MIME validation, server-side resize, random filename, virus scan opsional
- **2FA:** Passkey/WebAuthn untuk admin panel
- **Password Policy:** Minimum 8 karakter, mixed case + number (configurable)

### 6.6 SEO

- URL slug deskriptif dengan kata kunci
- Meta title & description dinamis per halaman
- Open Graph & Twitter Card tags
- Structured data (JSON-LD Article schema)
- BreadcrumbList structured data
- Auto sitemap.xml (update saat publish/unpublish)
- Auto robots.txt
- RSS/Atom feed
- Canonical URLs
- hreflang tags (Phase 3, multi-language)

### 6.7 Accessibility (WCAG 2.1 AA)

- Color contrast ratio ≥ 4.5:1 untuk teks normal, ≥ 3:1 untuk large text
- Semua interactive elements keyboard-accessible
- Skip-to-content link
- ARIA labels untuk icon-only buttons
- Proper heading hierarchy (h1 → h2 → h3)
- Alt text untuk semua gambar
- Form labels & error messages screen-reader accessible

### 6.8 Docker & DevOps

- Multi-stage Docker build untuk production image
- Non-root user menjalankan aplikasi
- Healthcheck endpoint untuk container orchestration
- Docker Compose: app + MySQL + Redis + queue worker
- FrankenPHP worker mode via `FRANKENPHP_CONFIG`
- Volume mounting untuk development hot-reload

### 6.9 Observability

- Structured logging via Laravel `config/logging.php` (daily rotation)
- Horizon dashboard untuk queue monitoring
- Filament dashboard untuk application metrics
- Error tracking: Sentry atau Laravel Telescope opsional

---

## 7. Arsitektur Sistem

### 7.1 High-Level Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                     CLIENTS                                  │
│  Browser (Desktop/Mobile)  │  RSS Reader  │  Search Crawler │
└──────────────┬──────────────────────────────────────────────┘
               │  HTTPS (HTTP/2, HTTP/3)
               ▼
┌─────────────────────────────────────────────────────────────┐
│                  FRANKENPHP (Caddy)                          │
│  ┌───────────────────────────────────────────────────────┐  │
│  │  Laravel Octane (Worker Mode)                         │  │
│  │  ┌─────────────────────────────────────────────────┐  │  │
│  │  │  Laravel 13 Application (booted once, in-mem)    │  │  │
│  │  └─────────────────────────────────────────────────┘  │  │
│  └───────────────────────────────────────────────────────┘  │
└──────────────┬──────────────────────────────────────────────┘
               │
     ┌─────────┼──────────┐
     ▼         ▼          ▼
┌─────────┐ ┌──────┐ ┌──────────┐
│ MySQL   │ │ Redis│ │ Storage  │
│ 8.4 LTS │ │ 7.x  │ │ (Local)  │
└─────────┘ └──────┘ └──────────┘
```

### 7.2 Application Architecture (Laravel)

```
app/
├── Console/Commands/        # Scheduler commands (publish, sitemap)
├── Enums/                   # ArticleStatus, UserRole, CommentStatus
├── Events/                  # ArticlePublished, CommentCreated
├── Exceptions/              # Custom exceptions
├── Filament/                # Admin panel resources, pages, widgets
│   └── Resources/
│       ├── ArticleResource.php
│       ├── UserResource.php
│       ├── CategoryResource.php
│       ├── TagResource.php
│       └── CommentResource.php
├── Http/
│   ├── Controllers/         # Minimal (mostly Livewire components)
│   └── Middleware/           # CSP, HSTS, Security Headers
├── Jobs/                    # Queue jobs (email, newsletter, image optimize)
├── Listeners/               # Event listeners
├── Livewire/                # Livewire 4 components
│   ├── Layouts/             # Base layout (SPA shell)
│   ├── Pages/               # Full-page components
│   │   ├── HomePage.php
│   │   ├── ArticleShow.php
│   │   ├── SearchPage.php
│   │   └── ProfilePage.php
│   └── Components/          # Reusable components
│       ├── ArticleCard.php
│       ├── CommentSection.php
│       ├── ThemeToggle.php
│       ├── SearchBar.php
│       └── TableOfContents.php
├── Mail/                    # Mail classes
├── Models/                  # Eloquent models
│   ├── Article.php
│   ├── Category.php
│   ├── Tag.php
│   ├── Comment.php
│   ├── Media.php
│   └── User.php
├── Notifications/           # Notification classes
├── Policies/                # Authorization policies
├── Providers/               # Service providers
└── Services/                # Business logic services
    ├── ArticleService.php
    ├── SearchService.php
    ├── SeoService.php
    └── ImageOptimizer.php
```

### 7.3 Database Schema

```sql
-- Users & Authentication
users (id, name, email, username, password, avatar, bio, github_url, twitter_url, 
       website_url, email_verified_at, role [admin|editor|author|user], 
       is_active, two_factor_enabled, two_factor_secret, timestamps, soft_deletes)

-- Articles
articles (id, user_id FK, category_id FK nullable, title, slug, excerpt, 
          content_md, content_html, featured_image, status [draft|review|published|scheduled],
          published_at, scheduled_at, reading_time, meta_title, meta_description,
          view_count, is_featured, timestamps, soft_deletes)

-- Categories
categories (id, name, slug, description, parent_id FK nullable, sort_order, timestamps)

-- Tags
tags (id, name, slug, timestamps)

-- Article-Tag (Polymorphic Pivot)
taggables (id, tag_id FK, taggable_type, taggable_id, timestamps)

-- Comments
comments (id, article_id FK, user_id FK, parent_id FK nullable, content, 
          status [pending|approved|spam], ip_address, user_agent, timestamps)

-- Media
media (id, user_id FK, filename, original_name, path, mime_type, size, 
       alt_text, folder, timestamps)

-- Newsletter Subscribers
newsletter_subscribers (id, email, token, confirmed_at, unsubscribed_at, timestamps)

-- Bookmarks
bookmarks (id, user_id FK, article_id FK, timestamps)

-- Article Series
series (id, name, slug, description, timestamps)
series_articles (id, series_id FK, article_id FK, sort_order, timestamps)
```

### 7.4 Route Architecture (Livewire 4 SPA)

```php
// routes/web.php — Livewire 4 SPA routing

// Public pages (Livewire full-page components)
Route::livewire('/', 'pages::home-page')->name('home');
Route::livewire('/articles/{article:slug}', 'pages::article-show')->name('articles.show');
Route::livewire('/category/{category:slug}', 'pages::category-show')->name('categories.show');
Route::livewire('/tag/{tag:slug}', 'pages::tag-show')->name('tags.show');
Route::livewire('/search', 'pages::search-page')->name('search');
Route::livewire('/rss', 'pages::rss-feed')->name('rss');

// Auth pages
Route::livewire('/login', 'pages::auth.login')->name('login');
Route::livewire('/register', 'pages::auth.register')->name('register');
Route::livewire('/forgot-password', 'pages::auth.forgot-password')->name('password.request');
Route::livewire('/profile', 'pages::auth.profile')->name('profile')->middleware('auth');

// Filament admin
Route::prefix('admin')->group(function () {
    // Filament handles its own routing
});

// SEO routes
Route::get('/sitemap.xml', [SitemapController::class, 'index']);
Route::get('/robots.txt', [RobotsController::class, 'index']);
```

### 7.5 Caching Strategy

```
Cache Keys & TTL:
├── articles.paginated.{page}.{category?}.{tag?}  →  Redis tag: articles (30 min)
├── articles.show.{slug}                          →  Redis tag: articles (60 min)
├── articles.related.{article_id}                 →  Redis tag: articles (60 min)
├── categories.all                                →  Redis tag: categories (24h)
├── tags.all                                      →  Redis tag: tags (24h)
├── sitemap.xml                                   →  Cache::remember (24h)
├── rss.feed                                      →  Cache::remember (1h)
└── user.{id}.profile                             →  Individual key (30 min)

Invalidation:
- Article publish/update/delete → Cache::tags(['articles'])->flush()
- Category update → Cache::tags(['categories'])->flush()
- Tag update → Cache::tags(['tags'])->flush()
- TTL extension hot-content → Cache::touch() (Laravel 13)
```

### 7.6 Security Architecture

```
Middleware Stack:
├── TrustProxies (production behind load balancer)
├── HandleCors
├── PreventRequestsDuringMaintenance
├── ValidatePostSize
├── TrimStrings / ConvertEmptyStringsToNull
├── SecurityHeaders (CSP, HSTS, X-Frame-Options, X-Content-Type-Options)
├── RateLimiter (login, register, search, comment, api)
├── Authenticate (where applicable)
└── VerifiedEmail (comment, bookmark actions)

Authorization:
├── ArticlePolicy (view, create, update, delete, publish)
├── CommentPolicy (create, approve, reject, delete)
└── UserPolicy (viewAny, view, update, delete, assignRole)
```

---

## 8. Desain UI/UX Principles & Wireframe

### 8.1 Design Principles
1. **Content-first** — Tipografi bersih, whitespace generous, fokus pada konten.
2. **Minimal chrome** — Navigasi esensial saja, no visual clutter.
3. **Dark mode native** — Bukan afterthought, didesain dari awal.
4. **Progressive enhancement** — Fungsional tanpa JS, enhanced dengan Livewire.
5. **Consistent spacing** — 8px grid system via Tailwind spacing scale.

### 8.2 Layout Structure

```
┌─────────────────────────────────────────────────────────────┐
│ HEADER (sticky)                          [🔍] [🌙] [🔔] [👤] │
│ [Logo] [Articles ▾] [Categories ▾] [Series] [About]          │
├─────────────────────────────────────────────────────────────┤
│                                                               │
│  ┌─────────────────────────────────────────────────────────┐ │
│  │ HOMEPAGE                                                 │ │
│  │ ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐    │ │
│  │ │ Featured  │ │ Article  │ │ Article  │ │ Article  │    │ │
│  │ │ Article   │ │ Card     │ │ Card     │ │ Card     │    │ │
│  │ │ (2x span) │ │          │ │          │ │          │    │ │
│  │ └──────────┘ └──────────┘ └──────────┘ └──────────┘    │ │
│  │ ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐    │ │
│  │ │ Article  │ │ Article  │ │ Article  │ │ Article  │    │ │
│  │ │ Card     │ │ Card     │ │ Card     │ │ Card     │    │ │
│  │ └──────────┘ └──────────┘ └──────────┘ └──────────┘    │ │
│  │                    [← 1 2 3 ... 10 →]                    │ │
│  └─────────────────────────────────────────────────────────┘ │
│                                                               │
├─────────────────────────────────────────────────────────────┤
│ FOOTER                                                       │
│ [Categories] [Tags] [RSS] [Privacy] [Terms]  © 2026 MugiewBlog│
└─────────────────────────────────────────────────────────────┘
```

### 8.3 Article Detail Layout

```
┌─────────────────────────────────────────────────────────────┐
│ HEADER (sticky)                                              │
├─────────────────────────────────────────────────────────────┤
│ [PROGRESS BAR ──────────────────────────────── 65% ───────] │
│                                                               │
│  ┌─────────────────────────────────────────────────────────┐ │
│  │ ARTICLE CONTENT (max-width: 720px, centered)             │ │
│  │                                                           │ │
│  │ # Judul Artikel H1                                       │ │
│  │ [Author Avatar] Rafi · 15 May 2026 · 8 min read          │ │
│  │ [Category Badge] [Tag] [Tag]                              │ │
│  │                                                           │ │
│  │ ┌───────────────────────────────────────────────────┐   │ │
│  │ │ Featured Image (16:9, rounded)                     │   │ │
│  │ └───────────────────────────────────────────────────┘   │ │
│  │                                                           │ │
│  │ ## Pendahuluan                                           │ │
│  │ Paragraf pertama...                                       │ │
│  │                                                           │ │
│  │ ## Table of Contents (auto)                               │ │
│  │ 1. Pendahuluan                                           │ │
│  │ 2. Instalasi                                             │ │
│  │ 3. Implementasi                                          │ │
│  │                                                           │ │
│  │ ## Instalasi                                             │ │
│  │ Code block dengan syntax highlight + copy button         │ │
│  │                                                           │ │
│  │ ┌─────────────────────────────────────────────────────┐ │ │
│  │ │ ```php                                              │ │ │
│  │ │ public function boot(): void {                      │ │ │
│  │ │     // highlighted code                             │ │ │
│  │ │ }                                                    │ │ │
│  │ │ ```                                    [📋 Copy]    │ │ │
│  │ └─────────────────────────────────────────────────────┘ │ │
│  └─────────────────────────────────────────────────────────┘ │
│                                                               │
│  [❤️ Like] [💬 Comment] [🔗 Share] [📋 Copy Link]            │
│                                                               │
│  ┌─────────────────────────────────────────────────────────┐ │
│  │ RELATED POSTS                                            │ │
│  │ [Card] [Card] [Card]                                     │ │
│  └─────────────────────────────────────────────────────────┘ │
│                                                               │
│  ┌─────────────────────────────────────────────────────────┐ │
│  │ COMMENTS                                                 │ │
│  │ └─ User A: Great article!                                │ │
│  │    └─ Author: Thanks!                                    │ │
│  │       └─ User A: Looking forward to part 2               │ │
│  │ ─────────────────────────────────────────────             │ │
│  │ [Comment form] (if logged in)                            │ │
│  └─────────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────┘
```

### 8.4 Mobile Adaptation

- Single column layout, no sidebars
- Hamburger menu untuk navigasi
- Bottom sheet untuk filter/pencarian
- Card-based article list (vertikal stack)
- Swipe gestures opsional (article carousel)
- Floating action button untuk scroll-to-top

---

## 9. Rencana Pengujian (Testing Strategy)

### 9.1 Testing Pyramid

```
         ╱ E2E ╲          — Playwright: 5% (critical paths)
       ╱─────────╲
      ╱ Feature   ╲       — Pest Feature: 20% (workflows)
     ╱───────────────╲
    ╱    Unit         ╲    — Pest Unit: 75% (models, services, helpers)
   ╱───────────────────╲
```

### 9.2 Unit Tests (Pest)

| Area | Coverage Target | Examples |
|---|---|---|
| Models | ≥ 90% | Relationships, scopes, accessors, casts |
| Services | ≥ 85% | ArticleService, SearchService, SeoService |
| Helpers | ≥ 90% | Reading time calculator, slug generator |
| Policies | ≥ 95% | All authorization scenarios |
| Jobs | ≥ 85% | Newsletter dispatch, image optimization |

### 9.3 Feature Tests (Pest)

| Feature | Tests |
|---|---|
| Article CRUD | Create, read, update, delete, soft delete, restore |
| Article Status | Draft → Review → Published → Scheduled transitions |
| Search | Full-text, empty results, special characters, pagination |
| Comments | Create, reply, moderate, spam detection |
| Auth | Register, login, logout, reset password, email verify |
| OAuth | GitHub login, Google login |
| Dark Mode | localStorage persistence, system preference detection |
| SEO | Sitemap generation, RSS feed, meta tags, structured data |
| Newsletter | Subscribe, confirm, unsubscribe |

### 9.4 Browser Tests (Playwright — optional for MVP)

| Scenario |
|---|
| Visitor can browse homepage and read article |
| Visitor can search for article |
| User can register and login |
| User can comment on article |
| Author can write and publish article |
| Admin can moderate comment |
| Dark mode toggle works without flicker |
| Mobile responsive navigation |
| Performance: Lighthouse audit ≥ 95 |

### 9.5 Test Commands

```bash
# Run all tests
php artisan test --parallel

# Run with coverage
php artisan test --coverage --min=85

# Filter by test name
php artisan test --filter=ArticleTest

# Run with compact output
php artisan test --compact
```

### 9.6 Code Quality

```bash
# Format code
vendor/bin/pint --format agent

# Static analysis (optional, Phase 2)
vendor/bin/phpstan analyse --level=8

# Architecture testing (Pest Arch)
php artisan test --filter=arch
```

---

## 10. Rencana Deployment & DevOps

### 10.1 Docker Setup

```dockerfile
# Dockerfile (multi-stage, production)
FROM dunglas/frankenphp:latest-php8.5 AS base
# ... PHP extensions, composer install

FROM base AS build
# ... Bun install, Vite build

FROM base AS production
# ... Copy built assets, non-root user, healthcheck
```

```yaml
# docker-compose.yml
services:
  app:
    build: .
    environment:
      FRANKENPHP_CONFIG: "worker ./public/index.php"
      SERVER_NAME: mugiewblog.test
    ports:
      - "80:80"
      - "443:443"
    depends_on:
      mysql:
        condition: service_healthy
      redis:
        condition: service_healthy

  mysql:
    image: mysql:8.4
    healthcheck:
      test: ["CMD", "mysqladmin", "ping"]
    volumes:
      - mysql_data:/var/lib/mysql

  redis:
    image: redis:7-alpine
    healthcheck:
      test: ["CMD", "redis-cli", "ping"]

  queue:
    build: .
    command: php artisan queue:work --tries=3
    depends_on:
      - app

volumes:
  mysql_data:
```

### 10.2 CI/CD Pipeline (GitHub Actions)

```yaml
# .github/workflows/ci.yml
jobs:
  test:
    runs-on: ubuntu-latest
    services:
      mysql: ...
      redis: ...
    steps:
      - uses: actions/checkout@v4
      - name: Setup PHP 8.5
      - name: Install dependencies
      - name: Run Pint (style check)
      - name: Run Pest (tests)
      - name: Run Vite build

  deploy:
    needs: test
    if: github.ref == 'refs/heads/main'
    steps:
      - name: Deploy to production
      # SSH + docker compose pull + up
```

### 10.3 Environment Configuration

```env
APP_NAME="MugiewBlog"
APP_ENV=production
APP_URL=https://mugiewblog.com

DB_CONNECTION=mysql
DB_HOST=mysql
DB_DATABASE=mugiewblog
DB_USERNAME=mugiewblog
DB_PASSWORD=...

REDIS_HOST=redis
REDIS_CLIENT=phpredis

CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

FRANKENPHP_CONFIG="worker ./public/index.php"
SERVER_NAME=mugiewblog.com

OCTANE_SERVER=frankenphp
OCTANE_WORKERS=auto
OCTANE_MAX_REQUESTS=500

SCOUT_DRIVER=database  # or meilisearch

GITHUB_CLIENT_ID=...
GITHUB_CLIENT_SECRET=...
GOOGLE_CLIENT_ID=...
GOOGLE_CLIENT_SECRET=...

MAIL_MAILER=smtp
MAIL_HOST=...
```

### 10.4 Deployment Checklist

- [ ] Environment variables configured (.env)
- [ ] `php artisan key:generate` executed
- [ ] Database migrated: `php artisan migrate --force`
- [ ] Storage linked: `php artisan storage:link`
- [ ] Config cached: `php artisan config:cache`
- [ ] Routes cached: `php artisan route:cache`
- [ ] Views cached: `php artisan view:cache`
- [ ] Assets built: `bun run build`
- [ ] Scheduler active: `php artisan schedule:work`
- [ ] Queue worker active: `php artisan queue:work`
- [ ] SSL certificate (Caddy auto via FrankenPHP)
- [ ] Healthcheck endpoint responding

---

## 11. Risiko & Mitigasi

| Risiko | Probability | Impact | Mitigasi |
|---|---|---|---|
| **FrankenPHP memory leak** | Medium | High | Octane `max_requests=500`, monitoring memory usage |
| **N+1 query performance** | Medium | Medium | Eager-loading wajib, Laravel Debugbar dev only, testing coverage |
| **FOUC pada dark mode** | Low | Medium | Inline blocking script tested di semua browser |
| **Filament 5 breaking changes** | Low | Medium | Pin version di composer, baca changelog sebelum update |
| **Redis connection failure** | Low | High | Failover ke database cache, connection retry |
| **Image upload abuse** | Medium | Medium | Rate limiting, MIME check, file size limit, random filename |
| **Spam comments** | Medium | Low | Moderation queue, Akismet integration, rate limiting |
| **SEO indexing delay** | Low | Medium | Sitemap submission ke Google Search Console, Lighthouse CI |
| **Dependency vulnerabilities** | Low | Medium | Dependabot, `composer audit`, `bun audit` |
| **MySQL 9 compatibility** | Low | Low | Gunakan MySQL 8.4 LTS untuk stabilitas; 9.x opsional |

---

## 12. Rencana Rilis

### 12.1 MVP (v1.0) — "Foundation"
**Timeline:** 4-6 weeks  
**Goal:** Blog fungsional dengan semua fitur Must Have.

**Deliverables:**
- [x] Laravel 13 project scaffold (done)
- [x] Database schema & migrations
- [x] Models dengan relationships, factories, seeders
- [x] Filament admin panel (articles, categories, tags, comments, users)
- [x] Livewire SPA homepage dengan artikel list
- [x] Livewire article detail page
- [x] Search functionality (full-text MySQL)
- [x] Comment system (nested, moderation)
- [x] Auth system (register, login, OAuth, verify email)
- [x] Role system (Admin, Editor, Author, User)
- [x] Dark/light mode anti-FOUC
- [x] SEO (meta, OG, sitemap, robots, RSS)
- [x] Security (CSRF, CSP, HSTS, rate limiting)
- [x] Docker setup (dev + production)
- [x] Testing suite (Pest coverage on critical paths)
- [x] CI/CD pipeline (GitHub Actions)

### 12.2 v1.1 — "Engagement"
**Timeline:** 2-3 weeks  
**Goal:** Meningkatkan retensi & engagement pembaca.

**Deliverables:**
- [ ] Newsletter system (double opt-in, MJML templates)
- [ ] Scheduled publish/unpublish otomatis
- [ ] Article series dengan navigasi
- [ ] Bookmarks/favorit
- [ ] External embed support (GitHub, CodePen, YouTube)
- [ ] Cross-posting ke Dev.to/Medium
- [ ] Analytics dashboard (Plausible/Umami)

### 12.3 v2.0 — "Growth"
**Timeline:** 4-6 weeks  
**Goal:** Ekspansi audiens & monetisasi.

**Deliverables:**
- [ ] Multi-language support (EN/ID)
- [ ] Custom theme builder
- [ ] Forum/discussion integration
- [ ] A/B testing untuk konten
- [ ] Advanced analytics & reporting
- [ ] Monetisasi: sponsorship slots, premium content (opsional)

---

## Appendix A: Recommended Packages

| Package | Purpose | Version Constraint |
|---|---|---|
| `laravel/framework` | Backend framework | ^13.8 |
| `livewire/livewire` | Frontend SPA | ^4.3 |
| `filament/filament` | Admin panel | ^5.0 |
| `laravel/octane` | FrankenPHP worker | ^2.17 |
| `spatie/laravel-sitemap` | Sitemap generation | ^7.x |
| `spatie/laravel-feed` | RSS/Atom feed | ^4.x |
| `laravel/horizon` | Queue monitoring | ^5.x |
| `torchlight/torchlight-laravel` | Syntax highlighting | ^1.x (opsional) |
| `spatie/laravel-medialibrary` | Media management | ^11.x (opsional) |

## Appendix B: Development Commands

```bash
# Start development environment
composer run dev
# → Concurrent: Octane server + Queue worker + Log tail + Vite dev

# Start production-like environment
composer run start
# → Concurrent: Vite build + Octane server + Queue worker + Scheduler

# Run tests
composer run test

# Format code
vendor/bin/pint --format agent

# Create new model with all bells and whistles
php artisan make:model Article --all --pest

# Create Livewire component
php artisan make:livewire pages/home-page

# Create Filament resource
php artisan make:filament-resource Article
```

---

**Document Status:** ✅ Complete — Ready for Architecture Design & Epic Breakdown  
**Next Step:** Generate `architecture.md` → Epics → Implementation tasks for Codex
