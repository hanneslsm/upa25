# upa25 WordPress Theme

**upa25** is the custom block theme used on [Unique Pole Art](https://poledance-darmstadt.de/). The codebase is built as a block theme and relies on WordPress 6+ features.

This repository contains the PHP, SCSS and JavaScript sources as well as the theme templates and patterns. Assets are compiled using `@wordpress/scripts`.

## Getting Started

1. Install dependencies:
   ```bash
   npm install
   ```
2. Start the development build (watch mode):
   ```bash
   npm run start
   ```
   or build once for production:
   ```bash
   npm run build
   ```
3. Copy the theme directory into your WordPress installation under `wp-content/themes/`.
4. Activate the **upa25** theme in the WordPress admin.

The compiled assets are written to the `build/` directory. Version numbers are automatically updated in `style.css` during the build step.

## Repository Layout

```
assets/         → Fonts and images used in the theme
inc/            → PHP helpers loaded from `functions.php`
parts/          → Template parts (header, footer, …)
patterns/       → PHP files registering block patterns
src/            → Source SCSS and JavaScript (compiled by Webpack)
styles/         → Additional block and section style variations (JSON)
templates/      → Block template HTML files
```

Important files:

- `functions.php` – loads all PHP modules from `inc/`.
- `theme.json` – theme settings, colors and presets.
- `webpack.config.js` – build configuration using `@wordpress/scripts`.
- `package.json` – npm scripts and dev dependencies.

## Development Notes

- **Block style variations** are registered in `inc/block-styles.php`. Corresponding
  SCSS lives in `src/scss/blocks/` and can be nested per style name.
- **Template parts** reside in `parts/` and are referenced in patterns and templates.
  Register new parts via `theme.json`.
- **Patterns** are defined in the `patterns/` folder and categories are registered in
  `inc/block-patterns.php`.
- **Block variations** can be added in `inc/block-variations.php`.
- **Dashboard widget** in `inc/dashboard-widget.php` shows theme and server info on
  the WordPress dashboard.
- **Enqueuing helper** `inc/enqueuing.php` automatically loads block styles and
  variations from the `build/` directory.
- **Dev remove defaults** `inc/dev_remove-defaults.php` strips default palette and
  gradients when developing the theme.
- **Utility classes** live in `src/scss/utilities/` (e.g. `helpers.scss`) and can be
  used to build layouts quickly.
- **Editor outline** in `src/scss/editor.scss` adds a dotted border around nested
  blocks to simplify editing.
- **Custom JavaScript** modules go in `src/js/custom/` and are imported in
  `src/js/global.js`.
- If patterns do not appear in the editor, switch the development mode to `theme`
  or run `/wp-admin/?purge-theme-cache`.

### npm scripts

- `npm run start` – watch source files and rebuild on changes.
- `npm run build` – create production assets and bump the version in `style.css`.
- `npm run lint:css` / `npm run lint:js` – lint styles and scripts.
- `npm run format` – format project files with Prettier.
- `npm run zip` – create a distributable archive.
- `npm run packages-update` – update `@wordpress/scripts` dependencies.

## License

upa25 is released under the terms of the [GPLv2 or later](LICENSE).
