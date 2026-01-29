# `src/` — Theme Source Conventions

This document defines **how we organize source files** in `src/` and **how to add new styles/scripts safely and predictably**.

It is intentionally **tool-agnostic**: do not treat the current file structure, bundler setup, or `enqueue.php` as the source of truth. This README is the source of truth.

---

## Core Principles

1. **Organize by feature, not by file type.**
   Keep related SCSS, JS, and (if needed) PHP together.
2. **Prefer block-aligned structure.**
   If something is tied to a block, place it under `blocks/`.
3. **Keep global styles small and intentional.**
   Global SCSS should define tokens, base rules, and shared patterns only.
4. **Name things for humans.**
   Choose clear, stable names that reflect what the code *does*.

---

## Top-Level Structure (Conceptual)

The exact build pipeline may change, but the **meaning** of each area should remain stable:

- `blocks/`
  Block-specific assets and block style variations.
- `includes/`
  Feature folders for non-block behavior (integrations, UI features, utilities).
- `scss/`
  Global styling foundations (tokens, base, elements, utilities).
- `images/`
  Static raster images that ship with the theme.
- `svg/`
  Static SVG assets that ship with the theme.

If you introduce a new top-level folder, document it here first.

---

## `images/` and `svg/` — Static Theme Assets

Use these folders for theme-owned assets (not WordPress Media Library uploads).

Conventions:

- Put raster files in `src/images/...` (e.g., `jpg`, `png`, `webp`, `avif`).
- Put standalone SVGs in `src/svg/...`.
- Keep subfolders stable and human-readable. The build output mirrors this structure.

Build behavior (current pipeline):

- Production builds copy `src/images/...` to `build/images/...`.
- Production builds also create `build/webp/...` versions of raster files.
- Production builds copy `src/svg/...` to `build/svg/...`.
- Imports from SCSS/JS emit to `build/images/...` as asset files.

Referencing assets at runtime:

- Never reference `src/...` from PHP, patterns, or templates.
- Reference built assets via `get_template_directory_uri() . '/build/images/...'` (or `/build/webp/...` and `/build/svg/...`).

---

## `blocks/` — Block-Scoped Assets

Use this when styles/scripts are tightly coupled to a block.

### Custom blocks (create-block)

Custom blocks are created with `@wordpress/create-block`. Keep the generated structure intact
(`block.json`, `index.js`, `render.php` when dynamic, etc.). We build and ship from `build/`,
never from `src/`.

### Folder layout

Use a two-level namespace:

- `blocks/core/<block-name>/...` for core blocks
- `blocks/<slug>/<block-name>/...` for custom blocks

Examples:

- `blocks/core/image/`
- `blocks/core/button/`
- `blocks/upa/hero/`

### File roles (conventions)

Inside a block folder, use these names when relevant:

- `style.scss`
  Front-end block styles.
- `editor.scss`
  Editor-only styles.
- `view.js`
  Front-end behavior.
- `editor.js`
  Editor behavior.
- `render.php`
  Server-side rendering, if needed.

Not every block needs every file. Only add what you use.

### Block style variations

Use a `styles/` subfolder for block style variations:

- `blocks/core/button/styles/fill.scss`
- `blocks/core/button/styles/outline.scss`

All block style variation selectors should start with:

- `.is-style-...`

---

## `includes/` — Feature Folders (Non-Block)

Use `includes/` for cross-cutting features, integrations, or behaviors that are **not owned by a single block**.

Examples:

- Third-party plugin styling
- UI features (e.g., modals, spotlight effects, filters)
- Shared interactive behaviors

### Folder layout

Organize by slug/owner, then by feature:

- `includes/<slug>/<feature>/...`

Examples:

- `includes/upa/spotlight/view.js`
- `includes/custom/forms/style.scss`
- `includes/plugins/sugar-calendar/style.scss`

### File roles

Use the same file naming conventions as blocks where possible:

- `style.scss`
- `editor.scss`
- `view.js`
- `editor.js`
- `render.php` (rare here, but allowed)

This keeps the mental model consistent across the codebase. Includes can use any filenames,
but **prefer these names** for predictability.

### PHP usage (non-block)

`includes/` is also the home for PHP that powers editor controls, theme utilities, or integrations
that are not blocks. These files still need to be required from your theme entry points
(e.g. `functions.php` or `inc/*`) to run.

---

## `scss/` — Global Foundations Only

Use `scss/` for theme-wide foundations, not feature work.

Good fits:

- Design tokens (variables, maps)
- Base styles (resets, typography defaults)
- Elements (buttons, forms, links)
- Utilities and helpers

Avoid placing feature-specific or plugin-specific styles here. Those belong in `includes/` (or `blocks/` if block-owned).

### Recommendation for third-party styles

Move third-party or integration-specific SCSS out of `scss/` and into `includes/vendors/<name>/style.scss`.

For example:

- Prefer: `includes/plugins/sugar-calendar/style.scss`
- Avoid: `scss/custom/sugar-calendar.scss`

---

## Naming Conventions

### Folder names

- Use lowercase and hyphens.
- Use real domain words (e.g., `sugar-calendar`, not `sc`).

### File names

Prefer these standard entry names:

- `style.scss`
- `editor.scss`
- `view.js`
- `editor.js`
- `render.php`

Use additional files only when the feature clearly benefits from splitting.

---

## Build Entries (Current Mental Model)

The build looks for **specific entry file names**. If a file is not listed here and is not
imported by an entry, it will not be bundled.

Global entries:

- `src/scss/global.scss` → `build/global-styles.css`
- `src/scss/screen.scss` → `build/screen.css`
- `src/scss/editor.scss` → `build/editor.css`

Block entries (any namespace):

- `src/blocks/**/style.scss` → `build/blocks/{namespace}/{block}/style.css`
- `src/blocks/**/editor.scss` → `build/blocks/{namespace}/{block}/editor.scss`
- `src/blocks/**/view.js` → `build/blocks/{namespace}/{block}/view.js`
- `src/blocks/**/editor.js` → `build/blocks/{namespace}/{block}/editor.js`
- `src/blocks/**/styles/*.scss` → `build/blocks/{namespace}/{block}/styles/{variation}.css`
- `src/blocks/**/block.json` → Auto-registered custom blocks
- `src/blocks/**/*.php` → Copied to `build/blocks/...` and auto-loaded

Include entries:

- `src/includes/**/*.scss` → `build/includes/{category}/{name}/style.css`
- `src/includes/**/*.js` → `build/includes/{category}/{name}/view.js`
- `src/includes/**/*.php` → Copied to `build/includes/...`

---

## Auto-Discovery & Enqueuing System

Assets are automatically discovered and loaded based on folder structure and naming conventions.
**Zero manual PHP configuration needed** when adding new features.

### How assets are detected and loaded

**Block Assets:**
- `style.scss` → Auto-enqueued when block is used (via `wp_enqueue_block_style`)
- `styles/{variation}.scss` → Registered; enqueued when `is-style-{variation}` class detected
- `block.json` → Auto-registered custom blocks
- `*.php` → Auto-required on theme setup

**Include Assets (Non-Block Features):**
- `style.scss` → Enqueued when CSS class matches component name or `is-style-{name}`
- `view.js` → Enqueued on frontend only
- `editor.js` → Enqueued in block editor only
- Plugin includes in `plugins/` category → Enqueued globally when plugin is active

**Global Styles:**
- Always loaded (frontend + editor)
- `global.scss` → Theme-wide tokens, base styles, utilities
- `screen.scss` → Frontend-only styles
- `editor.scss` → Editor-only styles

### Class-based detection for includes

Include components are detected by CSS class matching:

```html
<!-- This will auto-enqueue includes/custom/modal/style.scss -->
<div class="modal">...</div>
<div class="is-style-modal">...</div>
```

Plugin includes load automatically when their plugin is active:

```
src/includes/plugins/sugar-calendar/style.scss → Loads when Sugar Calendar is active
```

All source files are compiled to `build/`, and the enqueuing system automatically discovers and loads them.

**PHP files:**
- Block PHP files (`src/blocks/**/*.php`) are auto-required on `after_setup_theme`
- Include PHP files can be required from entry points (e.g., `functions.php`) if needed for setup

**Style & Script files:**
- Automatically registered and enqueued based on presence on page
- Never manually require from `src/` — always reference built assets from `build/`
- Reference built assets via: `get_template_directory_uri() . '/build/...'`

---

## How To Add Something New (Checklist)

Follow this decision flow:

1. Is it block-specific?
   If yes, place it in `blocks/...`.
2. Is it a cross-cutting feature or integration?
   If yes, place it in `includes/...`.
3. Is it truly global foundation?
   If yes, place it in `scss/...`.

Then:

1. Create the folder.
2. Add only the entry files you need.
3. Keep selectors scoped and predictable.
4. Update this README if you introduce a new pattern.

---

## Guardrails (Do / Don’t)

Do:

- Co-locate related assets.
- Keep features easy to find by name.
- Scope styles to the smallest reasonable surface.

Don’t:

- Depend on current bundler details in your decisions.
- Scatter one feature across multiple top-level folders.
- Put plugin/integration styles in global foundations.

---

## Examples

### New block style variation

- Path: `blocks/core/button/styles/ghost.scss`
- Selector: `.is-style-ghost`

### New third-party integration styling

- Path: `includes/plugins/sugar-calendar/style.scss`
- Optional: `includes/plugins/sugar-calendar/view.js`

### New cross-cutting UI feature

- Path: `includes/upa/spotlight/style.scss`
- Path: `includes/upa/spotlight/view.js`

---

## Maintenance Notes

If the build/enqueue pipeline changes, update the pipeline — not these conventions — unless the conventions themselves need to evolve.

When conventions do evolve, update this file first, then refactor code to match.
