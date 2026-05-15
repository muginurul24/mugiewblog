# Epic 5: Blog Features — Comments, Search, SEO, Notifications, Newsletter
**Status:** 🔴 Not Started | **Priority:** P0 (MVP Blocker — Comments, SEO, Notifications), P1 (Newsletter)  
**Estimated Tasks:** 23 | **Dependencies:** Epic 1, Epic 2

## Goal
Implement comment system, real-time database notifications (Filament bell + email), MySQL full-text search, SEO infrastructure (sitemap, RSS, metadata, structured data), and newsletter system (Phase 2).

## Stories

### 5.1: Comment System
**Priority:** P0 | **Estimate:** 6 tasks

| # | Task | Description |
|---|---|---|
| T5.1.1 | Create CommentSection Livewire component | Display approved comments (nested), comment form for authenticated users, pagination (20/page) |
| T5.1.2 | Create CommentItem component | Single comment with author avatar, name, date, content, reply button, nested children |
| T5.1.3 | Implement nested reply logic | Max 3 levels deep, inline reply form, parent_id tracking |
| T5.1.4 | Add comment form with validation | Markdown support (bold, italic, code, link), content validation, rate limiting (5/min) |
| T5.1.5 | Implement spam detection | Rule-based: check for common spam patterns, link count, keyword blacklist |
| T5.1.6 | Create CommentCreated event + dispatch logic | Fire event on comment creation, decouple from notification logic |

**Acceptance Criteria:**
- [ ] Logged-in users can post comments
- [ ] Comments display nested up to 3 levels
- [ ] New comments from new users go to moderation
- [ ] CommentCreated event fires on new comment
- [ ] Spam patterns detected and auto-flagged
- [ ] Rate limiting enforced

### 5.2: Database Notification System
**Priority:** P0 | **Estimate:** 5 tasks

| # | Task | Description |
|---|---|---|
| T5.2.1 | Create NewCommentNotification class | Database + mail channels, `ShouldQueue`, array shape `toArray()`, formatted `toMail()` |
| T5.2.2 | Create CommentApprovedNotification class | Database channel only, sent when admin approves user's comment |
| T5.2.3 | Create CommentNeedsModeration class | Database channel only, sent to admin/editor on new pending comment |
| T5.2.4 | Create SendCommentNotifications listener | Listen for CommentCreated → notify author + admin/editor, route to `notifications` queue |
| T5.2.5 | Configure Queue::route for notifications | Route notification listeners to dedicated `notifications` queue in `bootstrap/app.php` |

**Acceptance Criteria:**
- [ ] Notifications appear in Filament bell icon (polling every 20s)
- [ ] Unread count badge live-updates
- [ ] Author clicks notification → redirects to article
- [ ] Admin/editor sees "comment needs moderation" bell notification
- [ ] All notifications dispatched via ShouldQueue
- [ ] Comment author gets notification when comment approved
- [ ] Email sent to article author for new comment (queued)

### 5.3: Search Implementation
**Priority:** P0 | **Estimate:** 3 tasks

| # | Task | Description |
|---|---|---|
| T5.3.1 | Create SearchService | MySQL FULLTEXT search with boolean mode, relevance scoring, excerpt generation with keyword highlighting |
| T5.3.2 | Optimize full-text search | Proper MATCH AGAINST query, minimum word length (3 chars), stop words, partial match |
| T5.3.3 | Add search results caching | Cache popular search results for 1 hour |

**Acceptance Criteria:**
- [ ] Search returns relevant results for tech keywords
- [ ] Partial matches work (e.g., "pipe" finds "Pipe Operator")
- [ ] Results display highlighted keywords in excerpt
- [ ] Empty search shows helpful message

### 5.4: SEO Infrastructure
**Priority:** P0 | **Estimate:** 5 tasks

| # | Task | Description |
|---|---|---|
| T5.4.1 | Create SeoService | Methods for dynamic meta tags, OG tags, Twitter Cards generation per page |
| T5.4.2 | Add structured data to article page | JSON-LD Article schema with author, datePublished, image, publisher |
| T5.4.3 | Implement sitemap.xml | Auto-generate with all published articles and categories, auto-update on publish |
| T5.4.4 | Implement robots.txt | Allow all crawlers, point to sitemap |
| T5.4.5 | Implement RSS/Atom feed | Latest 20 published articles, proper RSS 2.0 format |

**Acceptance Criteria:**
- [ ] Every article page has complete OG + Twitter Card tags
- [ ] Article schema validates in Google Rich Results Test
- [ ] sitemap.xml is accessible and lists all published articles
- [ ] robots.txt points to sitemap
- [ ] RSS feed validates in W3C Feed Validator

### 5.5: Newsletter (Phase 2)
**Priority:** P1 | **Estimate:** 2 tasks (MVP scaffold only)

| # | Task | Description |
|---|---|---|
| T5.5.1 | Create newsletter subscribe form component | Email input, subscribe button, success/error states, rate limit |
| T5.5.2 | Create double opt-in flow | Send confirmation email with unique token, confirm route, unsubscribe route |

**Acceptance Criteria (MVP Scaffold):**
- [ ] Subscribe form visible in footer or sidebar
- [ ] Double opt-in email sent on subscribe
- [ ] Unsubscribe endpoint functional

---

**Epic Completion Criteria:**
- [ ] Comment system fully functional with moderation
- [ ] Database notifications working via Filament bell (20s polling)
- [ ] Email notifications queued for comment replies
- [ ] MySQL search returns relevant results
- [ ] SEO metadata present on all pages
- [ ] Sitemap, robots.txt, RSS feed accessible
