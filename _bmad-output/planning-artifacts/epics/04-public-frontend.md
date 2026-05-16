# Epic 4: Public Frontend — Livewire SPA
**Status:** 🟢 Completed | **Priority:** P0 (MVP Blocker)  
**Estimated Tasks:** 18 | **Dependencies:** Epic 1

## Goal
Build the complete public-facing blog frontend using Livewire 4 SPA with full-page components, lazy loading, and responsive design.

## Stories

### 4.1: SPA Shell & Layout
**Priority:** P0 | **Estimate:** 4 tasks

| # | Task | Description |
|---|---|---|
| T4.1.1 | Create SPA layout component | `layouts::app` — HTML shell with `<html>`, `<head>` (meta, CSP, inline dark mode script), body structure |
| T4.1.2 | Create Header component | Logo, navigation (dynamic categories), search bar, theme toggle, user menu (login/avatar) |
| T4.1.3 | Create Footer component | Categories list, tags cloud, RSS link, copyright |
| T4.1.4 | Configure SPA routing | `Route::livewire()` for all public routes, `config/livewire.php` layout setting |

**Acceptance Criteria:**
- [x] SPA shell renders without full page reload on navigation
- [x] Header is sticky on scroll
- [x] Navigation highlights current page
- [x] Footer consistent across all pages

---

### 4.2: Homepage
**Priority:** P0 | **Estimate:** 4 tasks

| # | Task | Description |
|---|---|---|
| T4.2.1 | Create HomePage component | Fetch paginated articles, handle category/tag filter via query params |
| T4.2.2 | Create ArticleCard component | Featured image, category badge, title, excerpt, author avatar+name, date, reading time, comment count |
| T4.2.3 | Implement pagination | Page navigation with page numbers, prev/next |
| T4.2.4 | Add skeleton loading states | Skeleton placeholders while articles load (wire:loading) |

**Acceptance Criteria:**
- [x] Homepage displays 12 articles per page
- [x] First article (featured) spans wider
- [x] Category/tag filter works via query string
- [x] Pagination UI functional
- [x] Skeleton states display during loading
- [x] Empty state shows "Belum ada artikel" message

---

### 4.3: Article Detail Page
**Priority:** P0 | **Estimate:** 5 tasks

| # | Task | Description |
|---|---|---|
| T4.3.1 | Create ArticleShow page component | Fetch article by slug, return 404 if not published (unless author), increment view count |
| T4.3.2 | Create TableOfContents component | Parse content_html headings, generate anchor links, highlight current section on scroll |
| T4.3.3 | Create ReadingProgress component | Fixed progress bar at top, updates on scroll |
| T4.3.4 | Create ShareButtons component | Copy link, share to Twitter/X, LinkedIn, WhatsApp — with copy confirmation |
| T4.3.5 | Create RelatedPosts component | Fetch 3 related articles by same category/tags, exclude current |

**Acceptance Criteria:**
- [x] Article renders markdown content as HTML
- [x] Table of contents auto-generated from h2/h3
- [x] Progress bar fills as user scrolls
- [x] Share buttons work for all platforms
- [x] Related posts show at bottom
- [x] 404 page for non-existent or draft articles
- [x] OG meta tags populated dynamically

---

### 4.4: Category & Tag Pages
**Priority:** P0 | **Estimate:** 3 tasks

| # | Task | Description |
|---|---|---|
| T4.4.1 | Create CategoryShow page | Display category name, description, paginated articles, breadcrumb |
| T4.4.2 | Create TagShow page | Display tag name, paginated articles, breadcrumb |
| T4.4.3 | Add breadcrumb navigation | Category → Article breadcrumbs with structured data |

**Acceptance Criteria:**
- [x] Category page shows articles filtered by category
- [x] Tag page shows articles filtered by tag
- [x] Breadcrumbs rendered with Schema.org structure

---

### 4.5: Search Page
**Priority:** P0 | **Estimate:** 2 tasks

| # | Task | Description |
|---|---|---|
| T4.5.1 | Create SearchBar component | Live search (debounced 300ms), dropdown with top 5 results, "View all results" link |
| T4.5.2 | Create SearchPage full-page component | Full search with pagination, highlighted keywords in excerpts, empty state |

**Acceptance Criteria:**
- [x] Search bar in header works globally
- [x] Quick results dropdown shows 5 best matches
- [x] Full search page paginates results
- [x] Empty state for no results
- [x] Searches are rate-limited (30/min)

---

**Epic Completion Criteria:**
- [x] All 7 public pages render via Livewire SPA
- [x] Navigation between pages without full reload
- [x] All components have loading, empty, and error states
- [x] Responsive design works on mobile (tested)
- [x] Eager loading prevents N+1 queries
