# Epic 1: Database & Models Foundation
**Status:** 🔴 Not Started | **Priority:** P0 (MVP Blocker)  
**Estimated Tasks:** 18 | **Dependencies:** None

## Goal
Establish the complete database schema, Eloquent models with relationships, enums, factories, and seeders.

## Stories

### 1.1: Create All Migrations
**Priority:** P0 | **Estimate:** 3 tasks

| # | Task | Description | File |
|---|---|---|---|
| T1.1.1 | Create users migration | Full schema: name, email, username, password, avatar, bio, role, 2FA fields, soft deletes | `database/migrations/xxxx_create_users_table.php` |
| T1.1.2 | Create articles migration | Full schema with FULLTEXT index, status enum, all SEO fields, featured image | `database/migrations/xxxx_create_articles_table.php` |
| T1.1.3 | Create categories migration | Self-referencing parent_id for hierarchy | `database/migrations/xxxx_create_categories_table.php` |
| T1.1.4 | Create tags & taggables migration | Tags table + polymorphic pivot | `database/migrations/xxxx_create_tags_table.php` + taggables |
| T1.1.5 | Create comments migration | Nested replies via parent_id, moderation status | `database/migrations/xxxx_create_comments_table.php` |
| T1.1.6 | Create media migration | File metadata storage | `database/migrations/xxxx_create_media_table.php` |
| T1.1.7 | Create bookmarks migration | User-article unique pivot | `database/migrations/xxxx_create_bookmarks_table.php` |
| T1.1.8 | Create series migration | Series + series_articles pivot | `database/migrations/xxxx_create_series_table.php` |
| T1.1.9 | Create newsletter_subscribers migration | Double opt-in fields | `database/migrations/xxxx_create_newsletter_subscribers_table.php` |

**Acceptance Criteria:**
- [ ] `php artisan migrate:fresh` runs without errors
- [ ] All foreign keys correctly defined with cascade rules
- [ ] FULLTEXT index exists on articles (title, excerpt, content_md)
- [ ] Proper indexes on all foreign keys and frequently queried columns

---

### 1.2: Create Enums
**Priority:** P0 | **Estimate:** 1 task

| # | Task | Description | File |
|---|---|---|---|
| T1.2.1 | Create all enums | ArticleStatus, UserRole, CommentStatus with labels and colors | `app/Enums/ArticleStatus.php`, `UserRole.php`, `CommentStatus.php` |

**Acceptance Criteria:**
- [ ] PHP 8.5 backed enums with `string` type
- [ ] Each enum has `label()` and `color()` methods
- [ ] `#[\NoDiscard]` attribute on return-value methods

---

### 1.3: Create Eloquent Models
**Priority:** P0 | **Estimate:** 8 tasks

| # | Task | Description | File |
|---|---|---|---|
| T1.3.1 | Create User model | Role cast, relationships (articles, comments, bookmarks, media), avatar accessor | `app/Models/User.php` |
| T1.3.2 | Create Article model | Status cast, all relationships, scopes (published, featured), reading time calculator, `#[\NoDiscard]` on url() | `app/Models/Article.php` |
| T1.3.3 | Create Category model | Parent/child self-relationship, articles relationship, slug auto-generation | `app/Models/Category.php` |
| T1.3.4 | Create Tag model | Polymorphic taggable relationship, articles accessor | `app/Models/Tag.php` |
| T1.3.5 | Create Comment model | Nested relationship (parent/children/replies), article, user, scopes by status | `app/Models/Comment.php` |
| T1.3.6 | Create Media model | User relationship, URL accessor | `app/Models/Media.php` |
| T1.3.7 | Create Bookmark model | User + Article relationships | `app/Models/Bookmark.php` |
| T1.3.8 | Create Series + NewsletterSubscriber models | Basic relationships | `app/Models/Series.php`, `NewsletterSubscriber.php` |

**Acceptance Criteria:**
- [ ] All relationships return correct Eloquent relationship types
- [ ] All casts defined using `casts()` method
- [ ] Scopes defined for common query patterns
- [ ] `php artisan tinker` can create and query all relationships

---

### 1.4: Create Factories & Seeders
**Priority:** P0 | **Estimate:** 2 tasks

| # | Task | Description | File |
|---|---|---|---|
| T1.4.1 | Create all factories | ArticleFactory (with states: published, draft, scheduled, featured), CommentFactory, UserFactory (with role states), CategoryFactory, TagFactory | `database/factories/` |
| T1.4.2 | Create DatabaseSeeder | Seed 1 admin, 5 categories, 15 tags, 20 published articles, 5 draft, 30 comments | `database/seeders/DatabaseSeeder.php` |

**Acceptance Criteria:**
- [ ] `php artisan db:seed` populates all tables with realistic data
- [ ] Factory states work: `Article::factory()->published()->create()`
- [ ] All foreign key relationships satisfied in seed data

---

**Epic Completion Criteria:**
- [ ] All 9 migrations created and runnable
- [ ] All 8 models with complete relationships
- [ ] All 3 enums with labels and colors
- [ ] All factories with states
- [ ] DatabaseSeeder populates meaningful demo data
- [ ] `php artisan migrate:fresh --seed` runs successfully
