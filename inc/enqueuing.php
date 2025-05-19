<?php

/**
 * Enqueue frontend and editor styles.
 *
 * @package upa25
 */

/**
 * Enqueue global CSS and JavaScript for both the frontend and editor.
 */
function upa25_enqueue_scripts()
{
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
add_action('enqueue_block_assets', 'upa25_enqueue_scripts');

/**
 * Enqueue the screen CSS for the frontend.
 */
function upa25_enqueue_frontend_styles()
{
	$screen_style_path   = get_template_directory_uri() . '/build/css/screen.css';
	$screen_style_asset  = require get_template_directory() . '/build/css/screen.asset.php';

	wp_enqueue_style(
		'upa25-screen-style',
		$screen_style_path,
		$screen_style_asset['dependencies'],
		$screen_style_asset['version']
	);
}
add_action('wp_enqueue_scripts', 'upa25_enqueue_frontend_styles');







/**
 * Enqueue only the CSS files for blocks and block style variations
 * that are actually used on the current page.
 *
 * Scans rendered blocks via the render_block filter and loads:
 * - Base block styles from build/css/blocks/{block}.css
 * - Style variation CSS from build/css/block-styles/{style}/{block}.css
 */

add_filter('render_block', 'upa25_collect_used_blocks', 10, 2);
function upa25_collect_used_blocks($block_content, $block)
{
	static $collected = [];

	$block_name = $block['blockName'];
	if (! $block_name) {
		return $block_content;
	}

	// Collect Block
	$collected['blocks'][] = $block_name;

	// Collect Style
	if (! empty($block['attrs']['className'])) {
		if (preg_match('/is-style-([a-z0-9\-]+)/', $block['attrs']['className'], $matches)) {
			$collected['styles'][$block_name][] = $matches[1];
		}
	}

	$GLOBALS['upa25_used_blocks'] = $collected;
	return $block_content;
}

function upa25_enqueue_all_block_styles()
{
	if (empty($GLOBALS['upa25_used_blocks'])) {
		return;
	}

	$used = $GLOBALS['upa25_used_blocks'];

	// 1. Basis-Blockstyles
	$base_dir = trailingslashit(get_theme_file_path('build/css/blocks'));
	$base_url = trailingslashit(get_theme_file_uri('build/css/blocks'));

	foreach ($used['blocks'] as $block_name) {
		$slug  = str_replace('/', '-', $block_name); // e.g. core/cover → core-cover
		$path  = "{$base_dir}{$slug}.css";
		$url   = "{$base_url}{$slug}.css";
		$handle = "upa25-block-style-{$slug}";

		if (file_exists($path)) {
			wp_enqueue_style($handle, $url, [], filemtime($path));
		}
	}

	// 2. Block-Style-Variations
	$styles_dir = trailingslashit(get_theme_file_path('build/css/block-styles'));
	$styles_url = trailingslashit(get_theme_file_uri('build/css/block-styles'));

	foreach ($used['styles'] as $block_name => $styles) {
		$block_slug = str_replace('/', '-', $block_name);
		foreach ($styles as $style_slug) {
			$path = "{$styles_dir}{$style_slug}/{$block_slug}.css";
			$url  = "{$styles_url}{$style_slug}/{$block_slug}.css";
			$handle = "upa25-block-style-{$block_slug}-{$style_slug}";

			if (file_exists($path)) {
				wp_enqueue_style($handle, $url, [], filemtime($path));
			}
		}
	}
}
add_action('enqueue_block_assets', 'upa25_enqueue_all_block_styles');
