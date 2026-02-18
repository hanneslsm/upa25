# Source Structure

## Principles

- Organize by feature, not by file type.
- Put block-owned assets in `blocks/`.
- Keep global styles small and intentional.
- Name things for humans.

## Folders

- `blocks/` - Block styles, scripts, and variations.
- `includes/` - Theme-level components (non-block features).
- `scss/` - Global foundations (tokens, base, utilities).
- `images/`, `svg/` - Static assets copied into `build/`.
- `plugins/` - Plugin-specific overrides when used.

## Conventions

- Prefer `style.scss`, `editor.scss`, `view.js`, `editor.js`, `render.php`.
- Block variations live in `blocks/**/styles/*.scss` and use `.is-style-*`.
- Never reference `src/` assets at runtime; always use `build/`.

## Adding a Feature

1. Decide the owner: `blocks/` (block), `includes/` (theme component), or `scss/` (global).
2. Add only the entry files you need.
3. Run `npm run build` and verify the compiled assets in `build/`.

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
that are not blocks. These files are **automatically loaded** on `after_setup_theme`.

No manual `require` statements needed in `functions.php` or `inc/*`.

---

## `plugins/` — Plugin Integrations

Use `plugins/` for plugin-specific customizations. This folder has **special auto-detection behavior**:
files only load when the corresponding plugin is active.

### Folder structure

```
plugins/{plugin-slug}/
```

The `{plugin-slug}` must match the plugin's directory name in `wp-content/plugins/`.

### Examples

- `plugins/woocommerce/` → Loads when WooCommerce is active
- `plugins/sugar-calendar/` → Loads when Sugar Calendar is active
- `plugins/contact-form-7/` → Loads when Contact Form 7 is active

### Auto-loading behavior

1. **Plugin detection:** Theme checks if the plugin is loaded by detecting its main class, constant, or function
2. **Conditional loading:** All files (PHP, CSS, JS) only load when their corresponding plugin is active
3. **Timing:** PHP files load on `after_setup_theme` with late priority (99), so they can hook into `init` and other early actions

### Common use cases

- Remove unwanted plugin features (patterns, marketing, admin notices)
- Customize plugin behavior (checkout fields, form styling)
- Extend plugin functionality (custom filters, integrations)

### Supported file types

- `*.php` → Auto-required when plugin is active
- `style.scss` → Auto-enqueued globally when plugin is active
- `view.js` / `editor.js` → Auto-enqueued when plugin is active

This keeps plugin-specific customizations organized and prevents errors when plugins are deactivated.

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
- `src/includes/**/*.php` → Copied to `build/includes/...` and auto-loaded on `after_setup_theme`

Plugin entries:

- `src/plugins/{slug}/*.scss` → `build/plugins/{slug}/style.css`
- `src/plugins/{slug}/*.js` → `build/plugins/{slug}/view.js`
- `src/plugins/{slug}/**/*.php` → Copied to `build/plugins/...` and auto-loaded when plugin is active

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

**Plugin Assets:**
- All assets enqueued globally when the corresponding plugin is active
- PHP files auto-required on `after_setup_theme` (late priority)

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

### Plugin auto-loading

Plugin assets load automatically when their plugin is active:

```
src/plugins/sugar-calendar/style.scss → Loads when Sugar Calendar is active
src/plugins/woocommerce/disable-checkout-note.php → Auto-required when WooCommerce is active
```

All source files are compiled to `build/`, and the enqueuing system automatically discovers and loads them.

**PHP files:**
- Block PHP files (`src/blocks/**/*.php`) are auto-required on `after_setup_theme`
- Include PHP files (`src/includes/**/*.php`) are auto-required on `after_setup_theme`
- Plugin PHP files (`src/plugins/{slug}/**/*.php`) are auto-required on `after_setup_theme` (late priority) only when the corresponding plugin is active

**Style & Script files:**
- Automatically registered and enqueued based on presence on page
- Never manually require from `src/` — always reference built assets from `build/`
- Reference built assets via: `get_template_directory_uri() . '/build/...'`

---

## How To Add Something New (Checklist)

Follow this decision flow:

1. Is it block-specific?
   If yes, place it in `blocks/...`.
2. Is it a plugin integration?
   If yes, place it in `plugins/{plugin-slug}/...`.
3. Is it a cross-cutting feature or utility?
   If yes, place it in `includes/...`.
4. Is it truly global foundation?
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

### New plugin integration

- Path: `plugins/sugar-calendar/style.scss`
- Optional: `plugins/sugar-calendar/view.js`
- PHP customization: `plugins/woocommerce/disable-checkout-note.php`

### New cross-cutting UI feature

- Path: `includes/upa/spotlight/style.scss`
- Path: `includes/upa/spotlight/view.js`

---

## Maintenance Notes

If the build/enqueue pipeline changes, update the pipeline — not these conventions — unless the conventions themselves need to evolve.

When conventions do evolve, update this file first, then refactor code to match.
