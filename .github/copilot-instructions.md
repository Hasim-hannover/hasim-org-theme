# Copilot Instructions: Hasimuener Journal Theme

## Project Overview
**Hasimuener Journal** (Zwischenräume) is a WordPress child theme extending [GeneratePress](https://generatepress.com/). High-performance editorial theme for hasimuener.org.

- **Parent Theme**: GeneratePress (assumed active in WordPress installation)
- **Language**: PHP (hooks/actions), CSS, vanilla JS
- **Architecture**: Modular child theme with `functions.php` as bootstrap loader

## Architecture

### Module System (`inc/`)
`functions.php` contains **no business logic** — it only loads modules from `inc/`:

| File | Responsibility |
|------|---------------|
| `inc/helpers.php` | Utility functions (reading time, body classes, rewrite flush) |
| `inc/post-types.php` | CPT registration (essay, note) |
| `inc/taxonomies.php` | Taxonomy "topic" + default term seeding |
| `inc/enqueue.php` | Asset loading, font preload, script defer |
| `inc/generatepress-compat.php` | GP meta suppression (3 strategies) |
| `inc/meta-fields.php` | Social teaser meta + Gutenberg sidebar panel |
| `inc/seo-schema.php` | JSON-LD ScholarlyArticle for essays |

### Template Structure
- `front-page.php` — Editorial hero + notes + topic grid
- `single-essay.php` — Longform with hero, TOC, share buttons
- `single-note.php` — Compact format, no TOC
- `archive-essay.php` / `archive-note.php` — List views
- `page-mission.php` — About/mission page
- `footer.php` — Three-column colophon
- `template-parts/content-essay.php` / `content-note.php` — List items

## Editorial Context
This is a **journalistic publication for political and societal discourse** — not a blog, not a portfolio. Every technical decision must serve the reader's ability to engage with complex, long-form argumentation without distraction.

### Content Priorities (apply to ALL output)
1. **Lesefluss (Reading Flow)**: Visual and structural decisions optimize for distraction-free reading of essay-length texts. Typography, whitespace, and layout serve the argument — not decoration.
2. **Barrierefreiheit (a11y)**: Societal topics demand maximum accessibility. Semantic HTML (`<article>`, `<nav>`, `<aside>`, `aria-label`), WCAG AA contrast ratios, and logical heading hierarchy are mandatory — not optional.
3. **Ernsthaftigkeit (Scholarly Rigor)**: Metadata, structured data (ScholarlyArticle), and markup must reflect journalistic or academic depth. No superficial patterns — every schema field, every `<time>` element, every citation structure must be defensible.

## Key Principles

### 1. Hook-System (Strict)
- Modifications to parent theme **exclusively** via `add_action`, `add_filter`
- Never modify parent theme files; extend via child theme only

### 2. Naming Convention
- **`hp_` prefix** for ALL custom functions and variables (e.g., `hp_reading_time()`, `$hp_inc_dir`)
- CSS custom properties: `--hj-` prefix (e.g., `--hj-accent`, `--hj-serif`)
- CSS classes: `hp-` prefix for custom components (e.g., `.hp-topic-pill`, `.hp-colophon`)

### 3. Modularity
- Complex logic MUST go in separate `inc/` files
- Each module is self-contained with its own hook registrations
- New modules: create file in `inc/`, add `require_once` in `functions.php`

### 4. Performance-First
- Assets loaded conditionally (`is_singular()`, screen checks)
- Font preloading for critical woff2 files
- `defer` on render-blocking GP scripts
- Duplicate style dequeue (GP auto-enqueue)
- Only woff2 + woff font formats (no eot/svg/ttf)

### 5. CSS Design System
- Design tokens via CSS Custom Properties in `:root`
- Fonts: Merriweather (serif, self-hosted), system sans-serif stack
- `font-display: swap` on all @font-face declarations
- Measure: `72ch` for optimal reading width

## Common Workflows

### Adding a New Module
1. Create `inc/my-module.php` with `defined('ABSPATH') || exit;` guard
2. Define functions with `hp_` prefix
3. Register hooks at the bottom of the file
4. Add `require_once $hp_inc_dir . '/my-module.php';` to `functions.php`

### Adding Custom Styles
1. Add CSS to `style.css` using `--hj-` tokens
2. Use specific selectors to override GeneratePress defaults
3. Follow existing BEM-like naming (`.hp-component__element`)

### Adding a New CPT
1. Add registration to `inc/post-types.php`
2. Create `single-{cpt}.php` and `archive-{cpt}.php`
3. Create `template-parts/content-{cpt}.php` for list items
4. Add body class in `inc/helpers.php`

## Files & Their Roles
- `style.css` — Theme metadata + design tokens + all CSS
- `functions.php` — Bootstrap loader only (~45 lines)
- `inc/` — Modular PHP logic (one concern per file)
- `assets/js/journal.js` — TOC, reading progress, footnotes, share
- `fonts/` — Self-hosted Merriweather (woff2 + woff only)
- `template-parts/` — Reusable template fragments
