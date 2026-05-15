---
name: animatecss
description: Use for any task involving CSS animations. Activate when user mentions animations, transitions, Animate.css, entrance/exit effects, attention seekers, or when building animated UI components (page transitions, modals, notifications, scroll reveals, loading states). Covers class-based animation API, best practices for reduced motion, combining with Livewire/Alpine triggers, and Tailwind integration. Do not use for complex JS animation libraries (GSAP, Framer Motion, Lottie) — those require separate handling.
license: MIT
metadata:
  author: rafi
  version: "4.1.1"
---

# Animate.css

## Setup

Animate.css 4.1.1 is installed via npm and imported in `resources/css/app.css`:

```css
@import "animate.css";
```

No extra config needed — all animation classes are globally available.

## Basic Usage

```blade
<!-- Static animation (plays immediately on load) -->
<h1 class="animate__animated animate__fadeInDown">
    Welcome to MugiewBlog
</h1>

<!-- Hover trigger -->
<button class="hover:animate__animated hover:animate__pulse">
    Subscribe
</button>

<!-- With delay -->
<div class="animate__animated animate__fadeInUp animate__delay-1s">
    Content below fold
</div>
```

> Always prefix with `animate__animated` — required for all animations.

## Common Animation Classes

### Entrance
| Effect | Class |
|---|---|
| Fade in up | `animate__fadeInUp` |
| Fade in down | `animate__fadeInDown` |
| Fade in left | `animate__fadeInLeft` |
| Fade in right | `animate__fadeInRight` |
| Fade in | `animate__fadeIn` |
| Zoom in | `animate__zoomIn` |
| Slide in up | `animate__slideInUp` |
| Bounce in | `animate__bounceIn` |

### Attention Seekers
| Effect | Class | Use |
|---|---|---|
| Pulse | `animate__pulse` | Hover, CTA |
| Bounce | `animate__bounce` | Badge, notification |
| Shake | `animate__shakeX` | Error, invalid |
| Head shake | `animate__headShake` | Empty state |
| Heart beat | `animate__heartBeat` | Like, favorite |
| Flash | `animate__flash` | Highlight |
| Tada | `animate__tada` | Success |

### Exit
| Effect | Class |
|---|---|
| Fade out | `animate__fadeOut` |
| Fade out up | `animate__fadeOutUp` |
| Zoom out | `animate__zoomOut` |

### Modifiers
| Modifier | Class |
|---|---|
| Speed: slow | `animate__slow` (2s) |
| Speed: slower | `animate__slower` (3s) |
| Speed: fast | `animate__fast` (800ms) |
| Speed: faster | `animate__faster` (500ms) |
| Delay | `animate__delay-1s` to `animate__delay-5s` |
| Repeat | `animate__repeat-1` to `animate__repeat-3` |
| Infinite | `animate__infinite` |

## Livewire + Alpine Integration

```blade
<!-- Show/hide with animation via Alpine -->
<div x-data="{ open: false }">
    <button @click="open = !open">Toggle</button>

    <div
        x-show="open"
        x-transition:enter="animate__animated animate__fadeIn"
        x-transition:leave="animate__animated animate__fadeOut"
        class="animate__faster"
    >
        Animated content
    </div>
</div>

<!-- On Livewire event -->
<div
    x-data
    @comment-posted.window="$el.classList.add('animate__animated', 'animate__headShake');
        setTimeout(() => $el.classList.remove('animate__animated', 'animate__headShake'), 1000)"
>
    Comment section
</div>
```

## Tailwind + Animate.css

```blade
<!-- Tailwind classes work alongside Animate.css -->
<article class="animate__animated animate__fadeInUp p-6 bg-white dark:bg-gray-800 rounded-lg shadow">

<!-- Hover only animation -->
<button class="transition hover:animate__animated hover:animate__pulse hover:animate__faster">

<!-- Responsive: animate only on desktop -->
<div class="lg:animate__animated lg:animate__fadeInLeft">
```

## Accessibility (WAJIB)

```blade
<!-- Respect user's reduced-motion preference -->
<style>
@media (prefers-reduced-motion: reduce) {
    .animate__animated {
        animation-duration: 0s !important;
        transition-duration: 0s !important;
    }
}
</style>

<!-- Alternative: use Tailwind motion-safe variant -->
<div class="motion-safe:animate__animated motion-safe:animate__fadeInUp">
```

## Project Conventions

### When to Animate
| Context | Animation | Rationale |
|---|---|---|
| Page transition (SPA) | `fadeIn` + `fadeInUp` staggered | Smooth navigation |
| Article cards (homepage) | `fadeInUp` staggered delay | Progressive reveal |
| Modal/Dialog | `zoomIn` / `zoomOut` | Focus attention |
| Notification toast | `slideInRight` / `fadeOut` | Non-intrusive |
| Error state | `shakeX` | Immediate feedback |
| Empty state | `headShake` | Light humor |
| Lazy loaded content | `fadeIn` | Smooth appearance |

### When NOT to Animate
- ❌ Body text paragraphs — distracts reading
- ❌ Navigation links — slows down navigation
- ❌ Forms — interferes with input focus
- ❌ Page hero — already visible on load
- ❌ Any element that repeats (`animate__infinite`) without user control

## Rules

- **Always** `@media (prefers-reduced-motion: reduce)` — no animation for accessibility
- **Prefer `animate__faster`** (500ms) for UI — snappy, not sluggish
- **One animation at a time** — don't chain multiple on one element
- **Stagger cards** with `animate__delay-*` — not all at once
- **Exit animations** need JS/Alpine toggle — CSS can't detect removal
- **Do not** npm install Animate.css — already bundled
