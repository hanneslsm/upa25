# Front Page Preloader

An instant-loading, minimal preloader for the UPA25 theme front page, inspired by Framer Motion's refined aesthetic.

## Features

- **🚀 Instant Loading**: Inlined critical CSS/JS in `<head>` for immediate display
- **🎨 Theme Integrated**: Uses theme colors, spacing, typography, and actual site logo
- **⚡ Performance Optimized**: GPU-accelerated, zero HTTP requests for critical assets
- **✨ Minimal Design**: Clean Framer-style aesthetic with subtle animations
- **🎯 Smart Loading**: Monitors actual page load with minimum display time
- **♿ Accessible**: `aria-hidden`, `prefers-reduced-motion`, dark mode support
- **📱 Front Page Only**: Loads exclusively on the front page

## Why Inline Critical Assets?

**Traditional Approach:**
1. Browser requests HTML
2. Browser parses HTML
3. Browser discovers CSS/JS links
4. Browser requests CSS/JS files
5. Preloader appears → **Delay visible!**

**Optimized Approach (Current):**
1. Browser requests HTML
2. CSS/JS already in HTML `<head>`
3. Preloader appears **immediately**
4. Zero additional HTTP requests
5. First visual feedback in <50ms

**Performance Gains:**
- ✅ Eliminates 2 HTTP requests (CSS + JS)
- ✅ Eliminates render-blocking external stylesheets
- ✅ Preloader visible before DOM is fully parsed
- ✅ Better First Contentful Paint (FCP)
- ✅ Better perceived performance

**Trade-off:** +4.1KB in HTML (acceptable for instant UX)

## Design Philosophy

Inspired by Framer Motion's subtle, refined approach:
- Clean white background (theme base color)
- Actual site logo with gentle breathing animation
- Slim progress bar with theme brand color (#D62261)
- Minimal animations for sophisticated feel
- Theme typography and spacing throughout

## Files

- `preloader.php` - WordPress integration with inline critical CSS/JS
- `preloader.js` - Lightweight animation controller (1.72 KB)
- `style.scss` - Theme-integrated styles (2.38 KB)

## How It Works

1. **Inline CSS/JS**: Injected into `<head>` via `wp_head` hook (priority 1-2)
2. **Logo Preload**: `<link rel="preload">` for logo image (priority 3)
3. **HTML Render**: Preloader HTML via `wp_body_open` (priority 1)
4. **Instant Display**: Appears immediately, no waiting for external assets
5. **Progress Animation**: RAF-based smooth progress tracking
6. **Load Detection**: Monitors `window.load` with 600ms minimum
7. **Exit**: Simple 400ms fade using Web Animations API
8. **Cleanup**: Self-removes from DOM after completion

## Theme Integration

### Colors Used
- Background: `--wp--preset--color--base` (white #ffffff)
- Progress bar: `--wp--preset--color--brand` (pink #D62261)
- Progress track: `--wp--preset--color--base-4` (light gray #EDEDED)
- Text: `--wp--preset--color--contrast` (dark #0D0D0D)

### Spacing Used
- Container padding: `--wp--preset--spacing--30` (2.25rem)
- Element gap: `--wp--preset--spacing--40` (3rem)
- Mobile padding: `--wp--preset--spacing--20` (1rem)

### Typography
- Font family: `--wp--preset--font-family--josefin-sans`
- Logo text: 600 weight with responsive sizing

## Customization

### Adjust Timing
```javascript
// In preloader.js
this.minDisplayTime = 600; // Change display time (ms)
```

### Logo Size
```scss
// In style.scss
.upa25-preloader__logo {
  max-width: 200px; // Adjust as needed
}
```

### Progress Bar Style
```scss
// In style.scss
.upa25-preloader__progress {
  height: 2px; // Make thicker/thinner
  max-width: 320px; // Adjust width
}
```

## Performance

- **Minimal Assets**: 1.72 KB JS + 2.38 KB CSS (4.1 KB total)
- **GPU Acceleration**: `translateZ(0)` and `backface-visibility`
- **RequestAnimationFrame**: Smooth 60fps animations
- **Smart Loading**: Only on front page via conditional checks
- **Resource Hints**: Preload for faster loading

## Browser Support

- Chrome/Edge 84+
- Firefox 75+
- Safari 13.1+
- Graceful degradation for older browsers

## Events

Dispatches event when complete:
```javascript
window.addEventListener('upa25PreloaderComplete', () => {
  // Page is fully loaded and preloader removed
});
```
