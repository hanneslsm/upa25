<!---
 * @package hxi25
 * @version 4.0.0
-->

## Webpack Build

This theme reuses the default `@wordpress/scripts` webpack configuration and layers project-specific behavior on top. The sections below describe how to work with the build as well as the history that used to live in `webpack.config.js`.

### npm scripts
* `npm run start` – development build with file watching, BrowserSync (when enabled), and readable output.
* `npm run build` – optimized production build and image processing.

### Entry detection
The custom entry factory in `webpack.config.js` keeps the configuration succinct by auto-detecting assets:

#### Theme assets
* `src/scss/global.scss` → `build/theme/global-styles.css`
* `src/scss/screen.scss` → `build/theme/screen.css`
* `src/scss/editor.scss` → `build/theme/editor.css`
* `src/js/global.js` → `build/theme/global.js`

#### Block enhancements and custom blocks
All block-related files (custom blocks and core block enhancements) output to `build/blocks/`:
* Custom blocks (directories with `block.json`) are handled by `@wordpress/scripts` default entry detection
* Core block enhancements (JS/SCSS files from `src/blocks/{block-name}/`) are consolidated into the same folder:
  * `.js` files → `build/blocks/{block-name}/{filename}.js` (editor controls)
  * `.scss` files (except `style.scss`) → `build/blocks/{block-name}/{filename}-styles.css` (editor styles)
  * `style.scss` → `build/styles/{block-name}/base.css` (block style variations)
  * `styles/*.scss` → `build/styles/{block-name}/{style-name}.css` (block style variants)

### Source File Organization

```
src/
├── blocks/                    # Block customizations and custom blocks
│   ├── core-button/           # Core block customizations
│   │   ├── arrow-toggle.js    # Editor control (JS)
│   │   ├── arrow-toggle.php   # PHP registration
│   │   ├── style.scss         # Block base styles
│   │   └── styles/            # Style variations
│   │       └── fill.scss
│   ├── core-group/
│   │   ├── hxi-gradient.js    # Editor control
│   │   ├── hxi-gradient.php   # PHP registration
│   │   ├── hxi-gradient.scss  # Associated styles
│   │   ├── link-control.js
│   │   └── link-control.php
│   └── hxi-ticker/            # Custom block (has block.json)
│       ├── block.json
│       ├── edit.js
│       ├── index.js
│       ├── render.php
│       ├── style.scss
│       └── view.js
├── parts/                     # Template part assets
│   └── header/
│       ├── header.scss
│       └── header-fixed.js
├── sections/                  # Section-specific assets
│   └── brand/
│       ├── brand.scss
│       └── hxi-bg-randomizer.js
├── scss/
│   ├── base/                  # SCSS variables, mixins
│   ├── elements/              # Element styles
│   ├── global.scss            # Global styles
│   ├── screen.scss            # Frontend-only styles
│   └── editor.scss            # Editor-only styles
└── js/
    └── global.js              # Main theme JavaScript
```

### Custom Blocks (Interactivity API)
The theme supports creating custom blocks with the WordPress Interactivity API. Custom blocks live in `src/blocks/` and are automatically detected by the presence of a `block.json` file.

#### Creating a new custom block
Run this command inside the `src/blocks/` directory:

```bash
npx @wordpress/create-block@latest your-block-name --textdomain hxi25 --template @wordpress/create-block-interactive-template --no-plugin
```

For a non-interactive (static or dynamic) block:
```bash
# Static block
npx @wordpress/create-block@latest your-block-name --textdomain hxi25 --no-plugin

# Dynamic block
npx @wordpress/create-block@latest your-block-name --textdomain hxi25 --no-plugin --variant dynamic
```

After creating the block, run `npm run build` – the block will be automatically registered.

#### Block registration
Custom blocks are registered via `inc/custom-blocks.php` which automatically discovers all blocks from `build/blocks/`. The registration uses WordPress 6.7+/6.8+ APIs (`wp_register_block_types_from_metadata_collection`) for optimal performance, with fallbacks for older versions.

#### Build flags
The build scripts include these flags for custom block support:
* `--experimental-modules` – Required for Interactivity API blocks using `viewScriptModule`
* `--blocks-manifest` – Generates a manifest file for efficient block registration

### Asset handling
* `RemoveEmptyScriptsPlugin` prevents empty JS files whenever a SCSS-only entry is compiled.
* `UpdateThemeVersionPlugin` bumps the `Version:` header in `style.css` so WordPress cache-busts the theme whenever the package version changes.
* SVG imports (outside of the static copy step) emit through webpack's asset module pipeline and land in `build/images`.

### Images
Production builds copy assets from `src/images` and `src/svg` when `PROLOOKS_COPY_IMAGES_IN_PROD` is true (default). [Sharp](https://sharp.pixelplumbing.com/) handles resizing and recompressing JPEG, PNG, AVIF, and WebP sources before writing both original-format and `.webp` conversions. SVGs are minified with SVGO by stripping metadata and preserving the optimized view box.

### Development server
BrowserSync support is optional. Install it first with `npm install --save-dev browser-sync browser-sync-webpack-plugin`. When `PROLOOKS_BS_PROXY` is set (usually to the Local WP domain), BrowserSync proxies the site on port `3000`, injects CSS/JS changes, and reloads PHP templates on change. Omitting the variable keeps BrowserSync from loading, allowing `npm run start` to succeed even in environments where the plugin is unavailable.

### Caching
Webpack's filesystem cache is stored in `.webpack-cache`. Touching `webpack.config.js` invalidates the cache automatically because the file is part of the cache key.

### Environment overrides
| Variable | Default | Purpose |
| --- | --- | --- |
| `PROLOOKS_IMG_MAX_WIDTH` | `2560` | Maximum width (px) for raster transforms. |
| `PROLOOKS_QUALITY_JPEG` / `PROLOOKS_QUALITY_PNG` / `PROLOOKS_QUALITY_AVIF` | `50` | Per-format quality level when recompressing source images. |
| `PROLOOKS_QUALITY_WEBP` | `70` | Quality for original `.webp` files. |
| `PROLOOKS_QUALITY_WEBP_CONVERT` | `60` | Quality for `.webp` conversions generated from other raster inputs. |
| `PROLOOKS_BS_PROXY` | _(unset)_ | Local domain (e.g. `https://example.local`) to proxy in BrowserSync dev builds. |
| `PROLOOKS_BS_HOST` | `localhost` | Hostname for BrowserSync server. |
| `PROLOOKS_BS_PORT` | `3000` | Port for BrowserSync server. |
| `PROLOOKS_COPY_IMAGES_IN_PROD` | `true` | Toggle for the Sharp/SVGO copy routine in production builds. |

### Changelog
* 4.0.0 – Rebuild webpack overrides on top of `@wordpress/scripts`: auto-discover global + nested block JS/SCSS/style entries, remove redundant `style-` prefixes, prune empty artifacts, keep `style.css` version synced, and run Sharp/SVGO-powered image processing guarded by environment toggles.
* Enqueuing 0.3.0 – Replace parts/sections loaders with a render-block collector that defers global CSS, conditionally enqueues `build/styles` assets & gradient utilities on the frontend, and feeds every block/style variation into all block editors via `add_editor_style` + `enqueue_block_editor_assets`.
* 3.5.3 - Add watching `theme.json`
* 3.5.2 – Allow missing sections directory with `noErrorOnMissing: true` in CopyWebpackPlugin.
* 3.5.1 – Fix core-group gradient enqueuing path from `build/editor/` to `build/blocks/` and add editor-specific gradient styles for proper display in block editor.
* 3.5.0 – Consolidate all block-related files into `build/blocks/` folder: custom blocks and core block enhancements now output to the same directory for cleaner organization and better scalability.
* 3.4.0 – Simplify webpack configuration (27% reduction): consolidate duplicate entry discovery functions, remove unused helpers, optimize plugin configuration, and fix directory handle garbage collection warning.
* 3.3.1 – Add `PROLOOKS_BS_HOST` and `PROLOOKS_BS_PORT` environment variables to configure BrowserSync host and port.
* 3.3.0 – Reorganize build output by purpose: `theme/` for global assets, `editor/` for block controls, `styles/` for block style variations, `parts/` and `sections/` for component assets. Remove separate `css/` and `js/` folders. Move block control SCSS to `src/blocks/{block-name}/` alongside JS and PHP.
* 3.2.0 – Add support for custom blocks with Interactivity API (`--experimental-modules`, `--blocks-manifest`), automatic block registration via `inc/custom-blocks.php`.
* 3.1.0 – Add support for processing all JS files from the `/blocks/` directory with nested structure preservation.
* 3.0.0 – Rename all environment variables to the `PROLOOKS_` prefix and require manual installation of BrowserSync packages before enabling proxying.
* 2.4.4 – Add control classes.
* 2.4.3 – Make BrowserSync optional so builds succeed without the plugin.
* 2.4.2 – Refactor for clarity & speed (helpers, resolved paths, plugin builders, filesystem cache).
* 2.4.1 – Remove sections SCSS pipeline; simplify BrowserSync config to proxy only; rename `QUALITY_WEBP_SECONDARY` → `QUALITY_WEBP_CONVERT`.
* 2.4.0 – Add top-level config variables (image qualities/max width, BrowserSync proxy/port, toggles).
* 2.3.0 – Merge BrowserSync proxy (2.1.3) with auto-detect block JS & `style-index` (2.2.0).
* 2.2.0 – Auto-detect block JS (`index`/`view`) and `style.scss` (style-index); cleanup.
* 2.1.3 – Add BrowserSync with proxy support for Local by Flywheel.
* 2.1.2 – Clean & copy SVGs to build.
* 2.1.1 – Disable performance hints.
* 2.1.0 – Automatic block-style entries & recursive block SCSS.
* 2.0.0 – Add WebP images.
* 1.0.0 – Initial version.
