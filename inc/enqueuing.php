<?php
/**
 * Asset loading for the upa25 theme
 *
 *  ── WHAT THIS FILE DOES ──────────────────────────────────────────────
 *  • Global CSS/JS → everywhere (editor + frontend).
 *  • Screen CSS    → frontend only.
 *  • Editor-canvas CSS (editor.css) → **editor iframe only**, never the frontend.
 *  • Per-block & style-variation CSS → loaded on the frontend *only if the
 *    corresponding block (or variation) is present in the rendered page*.
 *
 *  ── FOLDER STRUCTURE EXPECTED ────────────────────────────────────────
 *  build/
 *  ├── css
 *  │   ├── global.css
 *  │   ├── screen.css
 *  │   ├── editor.css
 *  │   ├── blocks/          ← core-cover.css, core-button.css, …
 *  │   └── block-styles/    ← outline/core-button.css, blurred/core-cover.css
 *  └── js
 *      └── global.js
 */

defined( 'ABSPATH' ) || exit;

/* ---------------------------------------------------------------------
 *  GLOBAL ASSETS  (frontend + editor)
 * ------------------------------------------------------------------ */

/**
 * Enqueue global CSS/JS (shared).
 */
function upa25_enqueue_global_assets(): void {
	$css_uri   = get_template_directory_uri() . '/build/css/global.css';
	$css_meta  = require get_template_directory() . '/build/css/global.asset.php';

	$js_uri    = get_template_directory_uri() . '/build/js/global.js';
	$js_meta   = require get_template_directory() . '/build/js/global.asset.php';

	wp_enqueue_style(
		'upa25-global-style',
		$css_uri,
		$css_meta['dependencies'],
		$css_meta['version']
	);

	wp_enqueue_script(
		'upa25-global-script',
		$js_uri,
		$js_meta['dependencies'],
		$js_meta['version'],
		true
	);
}
add_action( 'enqueue_block_assets', 'upa25_enqueue_global_assets' ); // recomm. hook :contentReference[oaicite:0]{index=0}

/* ---------------------------------------------------------------------
 *  FRONTEND-ONLY ASSETS
 * ------------------------------------------------------------------ */

/**
 * Screen-only CSS.
 */
function upa25_enqueue_screen_css(): void {
	$uri  = get_template_directory_uri() . '/build/css/screen.css';
	$meta = require get_template_directory() . '/build/css/screen.asset.php';

	wp_enqueue_style(
		'upa25-screen-style',
		$uri,
		$meta['dependencies'],
		$meta['version']
	);
}
add_action( 'wp_enqueue_scripts', 'upa25_enqueue_screen_css' );

/* ---------------------------------------------------------------------
 *  EDITOR-CANVAS-ONLY ASSETS  (editor iframe, not frontend)
 * ------------------------------------------------------------------ */

/**
 * Load editor.css *inside* the block-editor iframe with the correct hook.
 * Using `enqueue_block_assets` + `is_admin()` avoids the
 * “was added to the iframe incorrectly” warning introduced in WP 6.3 + 6.5. :contentReference[oaicite:1]{index=1}
 */
function upa25_enqueue_editor_canvas_css(): void {
	if ( ! is_admin() ) {
		return; // avoid the public site
	}

	$path = get_template_directory() . '/build/css/editor.css';
	$uri  = get_template_directory_uri() . '/build/css/editor.css';

	wp_enqueue_style(
		'upa25-editor-style',
		$uri,
		array(),
		filemtime( $path )
	);
}
add_action( 'enqueue_block_assets', 'upa25_enqueue_editor_canvas_css', 5 );

/* ---------------------------------------------------------------------
 *  PER-BLOCK & VARIATION STYLES  (conditional loading)
 * ------------------------------------------------------------------ */

/**
 * Register per-block CSS (always in editor, conditional on frontend)
 * and style-variation CSS (ditto).
 */
function upa25_register_block_styles(): void {

	/* -------- base block CSS ---------------------------------------- */
	$dir_blocks = trailingslashit( get_theme_file_path( 'build/css/blocks' ) );
	$url_blocks = trailingslashit( get_theme_file_uri( 'build/css/blocks' ) );

	foreach ( glob( $dir_blocks . '*.css' ) as $file ) {
		$slug       = basename( $file, '.css' );      // core-button
		$block_name = str_replace( '-', '/', $slug ); // core/button
		$handle     = "upa25-{$slug}-style";

		wp_register_style(
			$handle,
			$url_blocks . $slug . '.css',
			array(),
			filemtime( $file )
		);

		wp_enqueue_block_style(
			$block_name,
			array(
				'handle' => $handle,
			)
		);
	}

	/* -------- style-variation CSS ----------------------------------- */
	$dir_vars = trailingslashit( get_theme_file_path( 'build/css/block-styles' ) );
	$url_vars = trailingslashit( get_theme_file_uri( 'build/css/block-styles' ) );

	foreach ( glob( $dir_vars . '*/*.css' ) as $file ) {
		$rel          = str_replace( $dir_vars, '', $file );      // outline/core-button.css
		[ $variation, $css_file ] = explode( '/', $rel, 2 );

		$block_slug = basename( $css_file, '.css' );              // core-button
		$block_name = str_replace( '-', '/', $block_slug );
		$style_slug = sanitize_title( $variation );
		$handle     = "upa25-{$block_slug}-{$style_slug}-style";

		wp_register_style(
			$handle,
			"{$url_vars}{$variation}/{$block_slug}.css",
			array(),
			filemtime( $file )
		);

		register_block_style(
			$block_name,
			array(
				'name'         => $style_slug,
				'label'        => ucwords( str_replace( '-', ' ', $style_slug ) ),
				'style_handle' => $handle,
			)
		);
	}
}
add_action( 'init', 'upa25_register_block_styles' );
