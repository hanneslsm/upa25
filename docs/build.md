# Build and Assets

## Scripts

- `npm start` - Development build with BrowserSync proxy (`http://uniquepoleart.local`).
- `npm run build` - Production build via `@wordpress/scripts`.
- `npm run lint:css` - Stylelint for `src/**/*.scss`.
- `npm run lint:css:fix` - Auto-fix eligible Stylelint issues.
- `npm run lint:js` - ESLint for `src/**/*.{js,jsx,json,ts,tsx}`.
- `npm run format` - Prettier formatting for JS/TS/JSON/CSS/SCSS.
- `npm run format:reorder` - Reorder CSS properties using `prettier-plugin-css-order`.
- `npm run zip` - Build a distributable theme zip.
- `npm run packages-update` - Update `@wordpress` dependencies.

## Build Output

Compiled assets live in `build/`:

- `global-styles.css` and `global-styles.js` load in frontend and editor.
- `screen.css` and `screen.js` load on the frontend only.
- `editor.css` and `editor.js` load in the editor only.
- `blocks/` contains custom block bundles and block style variations.
- `includes/` contains component assets and copied PHP helpers.

## Enqueuing Rules (Summary)

- Global assets are always enqueued by the theme.
- Block base styles load when the block appears on a page.
- Block style variations load when the `is-style-*` class is present.
- Include components load when their class/slug is detected on the page.
- The editor loads all include assets for accurate previews.
- PHP files in `build/includes/` are auto-loaded on `after_setup_theme`.
