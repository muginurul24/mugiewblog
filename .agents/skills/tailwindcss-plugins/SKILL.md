---
name: tailwindcss-plugins
description: Use for any task involving rich text/article content styling (prose) or form element styling (forms plugin). Activate when user mentions typography, prose, article content, blog post styling, form design, input styling, or @tailwindcss/typography and @tailwindcss/forms. Covers prose modifiers, dark mode prose, form element base styles, and plugin customization. Do not use for basic Tailwind utilities — those are covered by the tailwindcss-development skill.
license: MIT
metadata:
  author: rafi
  versions:
    "@tailwindcss/typography": "0.5.19"
    "@tailwindcss/forms": "0.5.11"
---

# Tailwind CSS Plugins — Typography & Forms

## Setup

Both plugins are installed via npm and configured in `resources/css/app.css`:

```css
@plugin "@tailwindcss/typography";
@plugin "@tailwindcss/forms";
```

No extra config files needed — plugins auto-configure with Tailwind v4.

---

## @tailwindcss/typography

Use for **article content** and any long-form text (blog posts, comments, about page).

### Basic Usage

```blade
<!-- Article body with prose styling -->
<article class="prose dark:prose-invert max-w-none">
    {!! $article->content_html !!}
</article>

<!-- Comment content -->
<div class="prose prose-sm dark:prose-invert">
    {{ $comment->content }}
</div>
```

### Prose Modifiers

| Modifier | Effect | Use |
|---|---|---|
| `prose` | Base typography (16px, 1.75 line-height) | Article body |
| `prose-sm` | Smaller (14px) | Comments, excerpts, cards |
| `prose-lg` | Larger (18px) | Featured articles |
| `prose-xl` | Extra large (20px) | Hero sections |
| `prose-2xl` | Huge (24px) | Landing page |
| `prose-invert` | Invert colors (for dark backgrounds) | Dark sections |
| `dark:prose-invert` | Auto-invert in dark mode | **PREFERRED** |
| `max-w-none` | Remove max-width constraint | Full-width content |
| `prose-headings:...` | Style only headings | `prose-headings:font-bold` |
| `prose-a:...` | Style only links | `prose-a:text-primary-500` |
| `prose-code:...` | Style only inline code | `prose-code:bg-gray-100` |
| `prose-pre:...` | Style only code blocks | `prose-pre:bg-gray-900` |

### Article Content Convention

```blade
{{-- resources/views/components/layouts/article-content.blade.php --}}
<article class="
    prose prose-lg
    dark:prose-invert
    max-w-none
    prose-headings:scroll-mt-20
    prose-headings:font-bold
    prose-a:text-primary-600 dark:prose-a:text-primary-400
    prose-a:no-underline hover:prose-a:underline
    prose-code:before:content-none prose-code:after:content-none
    prose-code:bg-gray-100 dark:prose-code:bg-gray-800
    prose-code:px-1.5 prose-code:py-0.5 prose-code:rounded
    prose-code:text-sm prose-code:font-normal
    prose-pre:bg-gray-900 dark:prose-pre:bg-gray-950
    prose-pre:border dark:prose-pre:border-gray-800
    prose-img:rounded-xl
    prose-blockquote:border-l-primary-500
    prose-blockquote:bg-gray-50 dark:prose-blockquote:bg-gray-900/50
">
    {!! $article->content_html !!}
</article>
```

### Table of Contents Styling

```blade
<!-- Sidebar ToC -->
<nav class="prose prose-sm dark:prose-invert">
    <ul>
        @foreach ($toc as $item)
            <li class="prose-li:my-0.5">
                <a href="#{{ $item['id'] }}" class="prose-a:no-underline">
                    {{ $item['title'] }}
                </a>
            </li>
        @endforeach
    </ul>
</nav>
```

### Customizing Prose (Tailwind v4 `@theme`)

```css
/* resources/css/app.css */
@theme {
    /* Override prose defaults */
    --prose-body: var(--color-gray-700);
    --prose-headings: var(--color-gray-900);
    --prose-links: var(--color-primary-600);
    --prose-code: var(--color-primary-700);
    --prose-pre-bg: var(--color-gray-900);
    --prose-pre-border: var(--color-gray-800);
    --prose-blockquote-border: var(--color-primary-500);
}
```

---

## @tailwindcss/forms

Provides **opinionated base styles** for form elements — inputs, selects, checkboxes, radios, textareas.

### What It Does

Automatically styles form elements with:
- Consistent padding (`px-3 py-2`)
- Border color (`border-gray-300 dark:border-gray-600`)
- Focus ring (`focus:ring-2 focus:ring-primary-500 focus:border-primary-500`)
- Checkbox/radio sizing and accent color
- Select dropdown arrow
- Better iOS rendering

### Basic Usage

```blade
<!-- Text input — auto-styled by forms plugin -->
<input type="text" placeholder="Search..." class="w-full rounded-lg">

<!-- With Tailwind overrides -->
<input type="email"
    class="w-full rounded-lg border-gray-300 focus:border-primary-500 focus:ring-primary-500">

<!-- Select -->
<select class="w-full rounded-lg">
    <option>Choose category</option>
</select>

<!-- Checkbox -->
<label class="flex items-center gap-2">
    <input type="checkbox" class="rounded text-primary-600 focus:ring-primary-500">
    Remember me
</label>

<!-- Textarea -->
<textarea class="w-full rounded-lg resize-y min-h-[120px]"></textarea>
```

### Livewire Form Convention

```blade
<form wire:submit="save" class="space-y-6">
    <div>
        <label for="title" class="block text-sm font-medium mb-1">Title</label>
        <input type="text" id="title" wire:model="title"
            class="w-full rounded-lg @error('title') border-red-500 focus:ring-red-500 @enderror">
        @error('title') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="content" class="block text-sm font-medium mb-1">Content</label>
        <textarea id="content" wire:model="content" rows="8"
            class="w-full rounded-lg resize-y min-h-[200px]"></textarea>
    </div>

    <button type="submit"
        class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700
               focus:ring-2 focus:ring-primary-500 focus:ring-offset-2
               disabled:opacity-50 disabled:cursor-not-allowed">
        Save
    </button>
</form>
```

### Comment Form Example

```blade
<form wire:submit="postComment" class="space-y-4">
    <textarea wire:model="content" placeholder="Write a comment..."
        class="w-full rounded-lg resize-y min-h-[100px]"
        rows="3"></textarea>

    <div class="flex items-center gap-2">
        <input type="checkbox" id="notify" wire:model="notify" class="rounded">
        <label for="notify" class="text-sm">Notify me of replies</label>
    </div>

    <button type="submit"
        class="px-4 py-2 bg-primary-600 text-white rounded-lg text-sm font-medium
               hover:bg-primary-700 transition">
        Post Comment
    </button>
</form>
```

### Customizing Forms (Tailwind v4 `@theme`)

```css
/* Override forms plugin defaults */
@theme {
    --form-border-color: var(--color-gray-300);
    --form-border-color-dark: var(--color-gray-600);
    --form-focus-ring: var(--color-primary-500);
    --form-checkbox-radius: 0.25rem;
}
```

---

## Rules

- **Always use `prose dark:prose-invert`** for article content — never style manually
- **Always use `max-w-none`** with prose when inside a constrained container
- **Remove backtick pseudo-elements** from inline code: `prose-code:before:content-none prose-code:after:content-none`
- **Forms plugin is auto-styled** — only add Tailwind classes for overrides (size, color variants)
- **Don't disable forms plugin** globally — it provides accessibility-required focus rings
- **Use `@theme` custom properties** for global overrides — not inline styles
- **Both plugins are already configured** — do not re-install or re-configure
