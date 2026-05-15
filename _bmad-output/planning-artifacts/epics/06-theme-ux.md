# Epic 6: Theme & UX — Dark Mode, Accessibility, Performance
**Status:** 🔴 Not Started | **Priority:** P0 (MVP Blocker)  
**Estimated Tasks:** 12 | **Dependencies:** Epic 4

## Goal
Implement anti-FOUC dark/light mode, WCAG 2.1 AA accessibility, Tailwind CSS v4 CSS-first config, performance optimizations, and responsive design polish.

## Stories

### 6.1: Dark Mode Anti-FOUC
**Priority:** P0 | **Estimate:** 4 tasks

| # | Task | Description |
|---|---|---|
| T6.1.1 | Add inline blocking script to layout | `<script>` in `<head>` that reads localStorage and adds dark/light class to `<html>` before CSS loads |
| T6.1.2 | Configure Tailwind v4 dark mode | Use `@variant dark (&:where(.dark, .dark *))` in `resources/css/app.css` |
| T6.1.3 | Create ThemeToggle Alpine component | Button with sun/moon icons, toggle dark class on `<html>`, save to localStorage, support system preference changes |
| T6.1.4 | Add theme color transitions | Smooth CSS transitions on background and text colors (300ms) |

**Acceptance Criteria:**
- [ ] Zero flicker (FOUC) on page load in dark mode
- [ ] Toggle cycles through: Light → Dark (future: System)
- [ ] Theme persists across page visits
- [ ] System preference respected when no stored preference
- [ ] Chrome DevTools throttling test shows no white flash

---

### 6.2: Tailwind CSS v4 Setup
**Priority:** P0 | **Estimate:** 3 tasks

| # | Task | Description |
|---|---|---|
| T6.2.1 | Configure Tailwind CSS v4 CSS-first | `@import "tailwindcss"` base, `@theme {}` with custom design tokens (colors, fonts, spacing) |
| T6.2.2 | Define design tokens | Primary palette (OKLCH), Inter font family, content max-width, prose typography scale |
| T6.2.3 | Configure Vite for Tailwind v4 | Ensure `vite.config.js` processes CSS correctly with Tailwind v4 plugin |

**Acceptance Criteria:**
- [ ] No `tailwind.config.js` file (CSS-first config)
- [ ] Custom colors defined with OKLCH
- [ ] `bun run build` produces optimized CSS
- [ ] Dark mode variants work via `dark:` prefix

---

### 6.3: Accessibility (WCAG 2.1 AA)
**Priority:** P0 | **Estimate:** 3 tasks

| # | Task | Description |
|---|---|---|
| T6.3.1 | Audit and fix color contrast | Ensure all text meets 4.5:1 (normal) and 3:1 (large) contrast ratios in both themes |
| T6.3.2 | Add keyboard navigation | Focus styles, skip-to-content link, logical tab order, ARIA labels on icon buttons |
| T6.3.3 | Add screen reader support | Proper heading hierarchy, alt text for images, form labels, aria-live regions for dynamic content |

**Acceptance Criteria:**
- [ ] Lighthouse Accessibility score ≥ 95
- [ ] All interactive elements focusable via keyboard
- [ ] Skip-to-content link visible on focus
- [ ] Screen reader announces page changes in SPA

---

### 6.4: Performance Optimizations
**Priority:** P0 | **Estimate:** 2 tasks

| # | Task | Description |
|---|---|---|
| T6.4.1 | Implement caching strategy | Redis cache tags for articles, categories, tags; Cache::touch() for TTL extension |
| T6.4.2 | Optimize image loading | WebP format, lazy loading for below-fold images, responsive sizes, blur placeholder |

**Acceptance Criteria:**
- [ ] Lighthouse Performance ≥ 95 (mobile)
- [ ] TTFB < 200ms for cached pages
- [ ] Cache hit ratio > 90% for public pages
- [ ] Images load progressive with blur placeholder

---

**Epic Completion Criteria:**
- [ ] Dark mode works without FOUC on all pages
- [ ] Tailwind v4 configured with CSS-first approach
- [ ] WCAG 2.1 AA compliance
- [ ] Lighthouse ≥ 95 on mobile and desktop
