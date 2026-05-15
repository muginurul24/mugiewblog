---
name: fontawesome
description: Use for any task involving icons. Activate when user mentions icons, FontAwesome, fas/fa-regular/fa-brands, or when building UI components that need icons (buttons, nav, headers, modals, etc.). Covers icon selection, proper HTML usage, accessibility (aria-hidden, aria-label), sizing, animation, and stacking. Do not use for SVG-based icon libraries (Heroicons, Lucide, Phosphor) — those are separate icon systems.
license: MIT
metadata:
  author: rafi
  version: "7.2.0"
---

# FontAwesome Icons

## Setup

FontAwesome Free 7.2.0 is bundled locally — **no npm install needed**. Already imported in `resources/css/app.css`:

```css
@import "./../fontawesome/css/all.min.css";
```

Includes all three styles: **Solid** (`fas`), **Regular** (`far`), **Brands** (`fab`).

## Basic Usage

```blade
<!-- Solid (default) -->
<i class="fas fa-moon"></i>
<i class="fa-solid fa-moon"></i>

<!-- Regular -->
<i class="far fa-comment"></i>
<i class="fa-regular fa-comment"></i>

<!-- Brands -->
<i class="fab fa-github"></i>
<i class="fa-brands fa-github"></i>
```

> Prefer the **short prefix** (`fas`/`far`/`fab`) — consistent with FA 7.2.

## Tailwind Integration

FontAwesome works with Tailwind utilities for color, size, and spacing:

```blade
<!-- Sized with Tailwind -->
<i class="fas fa-search h-5 w-5 text-gray-500"></i>

<!-- Inline with text -->
<button class="flex items-center gap-2">
    <i class="fas fa-pen-to-square h-4 w-4"></i>
    <span>Edit</span>
</button>

<!-- Dark mode color -->
<i class="fas fa-sun h-5 w-5 text-gray-600 dark:text-gray-300"></i>
```

## Accessibility (WAJIB)

```blade
<!-- Decorative icons (no meaning) → aria-hidden -->
<i class="fas fa-chevron-right h-4 w-4" aria-hidden="true"></i>

<!-- Meaningful standalone icons → aria-label on parent -->
<button aria-label="Toggle dark mode">
    <i class="fas fa-moon h-5 w-5" aria-hidden="true"></i>
</button>

<!-- Icons with visible text → no extra label needed -->
<a href="/profile" class="flex items-center gap-1">
    <i class="fas fa-user h-4 w-4" aria-hidden="true"></i>
    Profile
</a>
```

## Common Icon Reference

### Navigation & UI
| Icon | Class | Use |
|---|---|---|
| Sun | `fas fa-sun` | Light mode |
| Moon | `fas fa-moon` | Dark mode |
| Search | `fas fa-search` | Search bar |
| User | `fas fa-user` | Profile/login |
| Bars | `fas fa-bars` | Hamburger menu |
| Times / X | `fas fa-times` | Close button |
| Chevron down | `fas fa-chevron-down` | Dropdown indicator |
| Arrow right | `fas fa-arrow-right` | Read more, next |
| Arrow left | `fas fa-arrow-left` | Back, previous |
| House | `fas fa-house` | Home link |
| Gear | `fas fa-gear` | Settings |
| Bell | `fas fa-bell` | Notifications |

### Content & Social
| Icon | Class | Use |
|---|---|---|
| Comment | `fas fa-comment` / `far fa-comment` | Comments |
| Heart | `fas fa-heart` / `far fa-heart` | Like/bookmark |
| Share | `fas fa-share-nodes` | Share button |
| Copy | `fas fa-copy` / `far fa-copy` | Copy link |
| Clock | `far fa-clock` | Reading time |
| Calendar | `far fa-calendar` | Published date |
| Tag | `fas fa-tag` | Tags |
| Folder | `fas fa-folder` / `far fa-folder` | Categories |
| RSS | `fas fa-rss` | RSS feed |
| Envelope | `fas fa-envelope` / `far fa-envelope` | Email/newsletter |
| GitHub | `fab fa-github` | GitHub link |
| Twitter/X | `fab fa-x-twitter` | Twitter link |
| LinkedIn | `fab fa-linkedin` | LinkedIn link |

### Admin (Filament fallback)
| Icon | Class | Use |
|---|---|---|
| Pen | `fas fa-pen-to-square` | Edit |
| Trash | `fas fa-trash` | Delete |
| Plus | `fas fa-plus` | Create/add |
| Check | `fas fa-check` | Approve, publish |
| Ban | `fas fa-ban` | Reject, suspend |
| Eye | `fas fa-eye` / `far fa-eye` | View |
| Spinner | `fas fa-spinner` | Loading |

## Sizing Convention

| Context | Tailwind Class |
|---|---|
| Inline with text | `h-4 w-4` |
| Standalone button | `h-5 w-5` |
| Large feature icon | `h-6 w-6` |
| Hero/heading icon | `h-8 w-8` |

## Animation

```blade
<!-- Spin (loading, refresh) -->
<i class="fas fa-spinner fa-spin h-5 w-5"></i>

<!-- Pulse (live indicator) -->
<i class="fas fa-circle fa-beat text-green-500 h-3 w-3"></i>
```

## Rules

- **Always** `aria-hidden="true"` on `<i>` elements — screen readers ignore them
- **Use `aria-label`** on parent interactive element for standalone icons
- **Do not install** FontAwesome via npm — already bundled locally
- **Prefer Tailwind classes** over FA size classes (`h-5 w-5` > `fa-lg`)
- **Use FontAwesome for generic UI icons** — for app-specific icons, use Heroicons (Blade components) instead
