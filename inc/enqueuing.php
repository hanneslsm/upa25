<?php
/**
 * Enqueue frontend and editor styles.
 *
 * @package upa25
 * @since 0.1.0
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
 * Enqueue the editor CSS for the block editor.
 */
function upa25_enqueue_editor_styles() {
	$editor_style_path   = get_template_directory_uri() . '/build/css/editor.css';
	$editor_style_asset  = require get_template_directory() . '/build/css/editor.asset.php';

	wp_enqueue_style(
		'upa25-editor-style',
		$editor_style_path,
		$editor_style_asset['dependencies'],
		$editor_style_asset['version']
	);
}
add_action( 'enqueue_block_editor_assets', 'upa25_enqueue_editor_styles' );

/**
 * Enqueue individual block styles from the build/css/blocks directory.
 */
function upa25_enqueue_block_styles() {
	$blocks_dir   = get_theme_file_path( 'build/css/blocks/' );
	$block_styles = glob( $blocks_dir . '*.css' );

	if ( $block_styles ) {
		foreach ( $block_styles as $style_path ) {
			$filename   = basename( $style_path, '.css' );
			// Replace only the first hyphen with a slash for the block name.
			$block_name = preg_replace( '/-/', '/', $filename, 1 );
			$style_uri  = get_theme_file_uri( 'build/css/blocks/' . $filename . '.css' );

			wp_enqueue_block_style(
				$block_name,
				array(
					'handle' => 'upa25-' . $filename . '-style',
					'src'    => $style_uri,
				)
			);
		}
	}
}
add_action( 'init', 'upa25_enqueue_block_styles' );
