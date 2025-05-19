<?php
/**
 * Enqueue frontend and editor styles.
 *
 * @package upa25
 */

/**
 * Enqueue global CSS and JavaScript for both the frontend and editor.
 */
function upa25_enqueue_scripts() {
	// Enqueue the global CSS.
	$global_style_path   = get_template_directory_uri() . '/build/css/global.css';
	$global_style_asset  = require get_template_directory() . '/build/css/global.asset.php';

	wp_enqueue_style(
		'upa25-global-style',
		$global_style_path,
		$global_style_asset['dependencies'],
		$global_style_asset['version']
	);

	// Enqueue the global JavaScript.
	$global_script_path   = get_template_directory_uri() . '/build/js/global.js';
	$global_script_asset  = require get_template_directory() . '/build/js/global.asset.php';

	wp_enqueue_script(
		'upa25-global-script',
		$global_script_path,
		$global_script_asset['dependencies'],
		$global_script_asset['version'],
		true
	);
}
add_action( 'enqueue_block_assets', 'upa25_enqueue_scripts' );

/**
 * Enqueue the screen CSS for the frontend.
 */
function upa25_enqueue_frontend_styles() {
	$screen_style_path   = get_template_directory_uri() . '/build/css/screen.css';
	$screen_style_asset  = require get_template_directory() . '/build/css/screen.asset.php';

	wp_enqueue_style(
		'upa25-screen-style',
		$screen_style_path,
		$screen_style_asset['dependencies'],
		$screen_style_asset['version']
	);
}
add_action( 'wp_enqueue_scripts', 'upa25_enqueue_frontend_styles' );




/**
 * Enqueue CSS for all block styles.
 */
add_action( 'enqueue_block_assets', 'upa25_enqueue_all_block_styles' );
function upa25_enqueue_all_block_styles() {

	$base_dir = trailingslashit( get_theme_file_path( 'build/css/blocks' ) );
	$base_url = trailingslashit( get_theme_file_uri( 'build/css/blocks' ) );

	if ( ! is_dir( $base_dir ) ) {
		return;
	}

	// 1. Base files: build/css/blocks/{block}.css
	foreach ( glob( "{$base_dir}*.css" ) as $file_path ) {
		$block_slug = basename( $file_path, '.css' );
		$handle     = "upa25-block-style-{$block_slug}";
		$src        = $base_url . "{$block_slug}.css";

		wp_enqueue_style( $handle, $src, [], filemtime( $file_path ) );
	}

	// 2. Variation files: build/css/blocks/{variation}/{block}.css
	foreach ( glob( "{$base_dir}*", GLOB_ONLYDIR ) as $variation_path ) {
		$variation_slug = basename( $variation_path );
		$css_files      = glob( "{$variation_path}/*.css" );

		if ( empty( $css_files ) ) {
			continue;
		}

		foreach ( $css_files as $file_path ) {
			$block_slug = basename( $file_path, '.css' );
			$handle     = "upa25-block-style-{$block_slug}-{$variation_slug}";
			$src        = $base_url . "{$variation_slug}/{$block_slug}.css";

			wp_enqueue_style( $handle, $src, [], filemtime( $file_path ) );
		}
	}
}

