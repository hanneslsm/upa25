# Build and Assets

## Scripts

- `npm start` runs the dev build with watch (BrowserSync when `PROLOOKS_BS_PROXY` is set).
- `npm run build` runs the production build and updates compiled assets.

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
