# Epic 3: Filament Admin Panel
**Status:** 🔴 Not Started | **Priority:** P0 (MVP Blocker)  
**Estimated Tasks:** 20 | **Dependencies:** Epic 1

## Goal
Complete Filament 5 admin panel with resources for articles, categories, tags, comments, users, media, and dashboard widgets.

## Stories

### 3.1: Panel Configuration & Dashboard
**Priority:** P0 | **Estimate:** 4 tasks

| # | Task | Description |
|---|---|---|
| T3.1.1 | Configure AdminPanelProvider | Brand name, colors (match blog theme), font, path `/admin`, login page, middleware (auth + role) |
| T3.1.2 | Create Dashboard page | Custom dashboard extending Filament page |
| T3.1.3 | Create StatsOverview widget | Total articles, published, pending comments, users — with Stat components |
| T3.1.4 | Create RecentArticles & PendingComments widgets | Table widgets showing last 5 articles and comments needing moderation |

**Acceptance Criteria:**
- [ ] Admin panel accessible at `/admin`
- [ ] Dashboard shows live stats
- [ ] Only admin/editor roles can access

---

### 3.2: Article Resource
**Priority:** P0 | **Estimate:** 5 tasks

| # | Task | Description |
|---|---|---|
| T3.2.1 | Create ArticleResource with form | Title, slug (auto-gen), excerpt, content_md (RichEditor), category, tags, featured image upload, status, scheduled_at, is_featured, SEO fields |
| T3.2.2 | Create ArticleResource table | Columns: image, title, author, category badge, status badge (color-coded), published_at, view_count; Filters: status, category, featured; Actions: edit, delete, publish |
| T3.2.3 | Add bulk actions | Bulk publish, bulk delete, bulk change category |
| T3.2.4 | Implement Markdown preview | RichEditor with live preview toggle, syntax highlighting preview |
| T3.2.5 | Implement image upload with optimization | FileUpload with WebP conversion, image editor, random filename |

**Acceptance Criteria:**
- [ ] CRUD operations work for articles
- [ ] Status transitions: Draft → Review → Published
- [ ] Scheduled articles save scheduled_at datetime
- [ ] Featured image upload + preview works
- [ ] Markdown preview shows rendered HTML
- [ ] SEO fields have character counters

---

### 3.3: Category & Tag Resources
**Priority:** P0 | **Estimate:** 3 tasks

| # | Task | Description |
|---|---|---|
| T3.3.1 | Create CategoryResource | Name, slug (auto-gen), description, parent (self-referencing select), sort_order |
| T3.3.2 | Create TagResource | Name, slug (auto-gen), articles count column |
| T3.3.3 | Add create option from Article form | Inline creation of category/tag while creating article |

**Acceptance Criteria:**
- [ ] Categories support hierarchy (parent/child)
- [ ] Tags display article count
- [ ] Categories and tags creatable inline from article form

---

### 3.4: Comment Resource
**Priority:** P0 | **Estimate:** 3 tasks

| # | Task | Description |
|---|---|---|
| T3.4.1 | Create CommentResource table | Columns: content preview, article title, author, status badge, created_at; Filters: status, article |
| T3.4.2 | Add moderation actions | Approve, Reject (mark as spam), Delete — as table actions and bulk actions |
| T3.4.3 | Add comment detail view | View full comment with article context, user info, IP, user agent |

**Acceptance Criteria:**
- [ ] Pending comments appear in moderation queue
- [ ] Approve/reject/spam actions update status
- [ ] Bulk moderation works

---

### 3.5: User & Media Resources
**Priority:** P0 | **Estimate:** 3 tasks

| # | Task | Description |
|---|---|---|
| T3.5.1 | Create UserResource | Table: avatar, name, email, role badge, status, created_at; Form: edit role, suspend/activate |
| T3.5.2 | Create MediaResource | Table: thumbnail, filename, size, uploader, date; Actions: view, delete |
| T3.5.3 | Add role assignment guard | Only admin can change roles |

**Acceptance Criteria:**
- [ ] Admin can view all users
- [ ] Admin can change user roles
- [ ] Admin can suspend/activate accounts
- [ ] Media browser shows all uploaded files

---

### 3.6: Navigation & Branding
**Priority:** P0 | **Estimate:** 2 tasks

| # | Task | Description |
|---|---|---|
| T3.6.1 | Configure Filament navigation | Group resources logically: Content (Articles, Categories, Tags), Community (Comments), System (Users, Media) |
| T3.6.2 | Set up admin user seeder | Default admin account created on first setup |

**Acceptance Criteria:**
- [ ] Navigation groups display correctly
- [ ] Default admin can login immediately after setup

---

**Epic Completion Criteria:**
- [ ] All 6 resources fully functional
- [ ] Dashboard with live widgets
- [ ] Article CRUD with rich editor and image upload
- [ ] Comment moderation queue working
- [ ] Role-based access enforced in admin panel
