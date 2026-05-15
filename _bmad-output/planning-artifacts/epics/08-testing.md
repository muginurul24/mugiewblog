# Epic 8: Testing Suite
**Status:** 🔴 Not Started | **Priority:** P0 (MVP Blocker)  
**Estimated Tasks:** 14 | **Dependencies:** Epic 1-7

## Goal
Achieve ≥ 85% test coverage with Pest unit and feature tests across all critical paths.

## Stories

### 8.1: Unit Tests — Models
**Priority:** P0 | **Estimate:** 4 tasks

| # | Task | Description |
|---|---|---|
| T8.1.1 | Create ArticleTest | Test: relationships (author, category, tags, comments), scopes (published, featured, draft), status casting, reading time calculation, URL generation |
| T8.1.2 | Create CommentTest | Test: nested relationships, article/user relationships, status scopes, reply depth |
| T8.1.3 | Create UserTest | Test: role casting, article relationship, comment relationship, bookmark relationship |
| T8.1.4 | Create CategoryTest & TagTest | Test: parent/child hierarchy, slug auto-generation, taggable polymorphic |

**Acceptance Criteria:**
- [ ] All model relationships return expected types
- [ ] All scopes filter correctly
- [ ] All accessors/mutators work
- [ ] Enum casts work in both directions

---

### 8.2: Unit Tests — Services
**Priority:** P0 | **Estimate:** 3 tasks

| # | Task | Description |
|---|---|---|
| T8.2.1 | Create ArticleServiceTest | Test: pagination, filtering by category, filtering by tag, published scope |
| T8.2.2 | Create SearchServiceTest | Test: full-text search relevance, empty results, partial match |
| T8.2.3 | Create SeoServiceTest | Test: sitemap generation, RSS feed format, meta tag generation |

**Acceptance Criteria:**
- [ ] Services return correct data structures
- [ ] Edge cases handled (empty, null, invalid input)
- [ ] Cache integration tested

---

### 8.3: Feature Tests — Auth
**Priority:** P0 | **Estimate:** 3 tasks

| # | Task | Description |
|---|---|---|
| T8.3.1 | Create LoginTest | Test: valid login, invalid credentials, rate limiting, remember me, redirect to intended |
| T8.3.2 | Create RegisterTest | Test: valid registration, duplicate email, password confirmation, email verification sent |
| T8.3.3 | Create PasswordResetTest | Test: forgot password flow, invalid token, expired token, successful reset |

**Acceptance Criteria:**
- [ ] All auth flows return correct HTTP status
- [ ] Session correctly set after login
- [ ] Rate limiting triggers at configured threshold

---

### 8.4: Feature Tests — Articles & Comments
**Priority:** P0 | **Estimate:** 2 tasks

| # | Task | Description |
|---|---|---|
| T8.4.1 | Create ArticleTest (feature) | Test: only published visible, draft hidden, author can view own draft, pagination, category filter |
| T8.4.2 | Create CommentTest (feature) | Test: authenticated can comment, guest redirected, nested replies, moderation visibility |

**Acceptance Criteria:**
- [ ] Public cannot see draft articles
- [ ] Author can see own drafts
- [ ] Comments require authentication
- [ ] Pending comments not visible to public

---

### 8.5: Feature Tests — Livewire Components
**Priority:** P0 | **Estimate:** 2 tasks

| # | Task | Description |
|---|---|---|
| T8.5.1 | Create HomePageTest | Test: renders published articles, pagination works, category filter works, skeleton state |
| T8.5.2 | Create ArticleShowTest | Test: renders article content, ToC generated, related posts shown, 404 for non-existent |

**Acceptance Criteria:**
- [ ] Livewire components render expected HTML
- [ ] Component state updates correctly
- [ ] Event dispatching works

---

**Epic Completion Criteria:**
- [ ] `php artisan test --coverage` shows ≥ 85% coverage
- [ ] All tests pass in CI
- [ ] Tests use database transactions (RefreshDatabase)
- [ ] Factories used for all test data
