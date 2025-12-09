<?php

/**
 * Enqueue frontend and editor styles.
 *
 * @package upa25
 * @version 0.3.0
 * @since 0.2.0
 */

/**
 * Enqueue global CSS and JavaScript for both the frontend and editor.
 */
function upa25_enqueue_scripts()
{
	// Enqueue the global CSS.
	$global_style_path   = get_template_directory_uri() . '/build/theme/global-styles.css';
	$global_style_asset  = require get_template_directory() . '/build/theme/global-styles.asset.php';

	wp_enqueue_style(
		'upa25-global-style',
		$global_style_path,
		$global_style_asset['dependencies'],
		$global_style_asset['version']
	);

	// Load global styles non-blocking on frontend.
	if ( ! is_admin() ) {
		wp_style_add_data( 'upa25-global-style', 'media', 'print' );
		wp_style_add_data( 'upa25-global-style', 'onload', "this.media='all'" );
	}

	// Enqueue the global JavaScript.
	$global_js_path   = get_template_directory_uri() . '/build/theme/global.js';
	$global_js_asset  = require get_template_directory() . '/build/theme/global.asset.php';

	wp_enqueue_script(
		'upa25-global-script',
		$global_js_path,
		$global_js_asset['dependencies'],
		$global_js_asset['version'],
		true // Load in footer
	);

	// Note: Parts and sections are now manually imported via global.scss and global.js
	// so we no longer need to enqueue them separately.
}
add_action('enqueue_block_assets', 'upa25_enqueue_scripts');

/**
 * Enqueue the screen CSS for the frontend.
 */
function upa25_enqueue_frontend_styles()
{
	$screen_style_path   = get_template_directory_uri() . '/build/theme/screen.css';
	$screen_style_asset  = require get_template_directory() . '/build/theme/screen.asset.php';

	wp_enqueue_style(
		'upa25-screen-style',
		$screen_style_path,
		$screen_style_asset['dependencies'],
		$screen_style_asset['version']
	);
}
add_action('wp_enqueue_scripts', 'upa25_enqueue_frontend_styles');

/**
 * Parts and sections are now manually imported via global.scss and global.js,
 * so the following functions are no longer needed and have been removed:
 * - upa25_enqueue_parts()
 * - upa25_enqueue_all_parts()
 * - upa25_enqueue_sections()
 * - upa25_enqueue_all_sections()
 */

/**
 * Remove the upa25_enqueue_editor_styles function and its add_action, and replace with add_editor_style for block editor CSS
 */
add_action( 'after_setup_theme', function() {
	add_editor_style( 'build/theme/editor.css' );
} );

/**
 * 1. Collect everything that is actually rendered.
 */
add_filter('render_block', 'upa25_collect_used_blocks', 10, 2);

function upa25_collect_used_blocks(string $block_content, array $block): string
{
	static $collected = [
		'blocks' => [],
		'styles' => [],
		'extras' => [],
	];

	if (empty($block['blockName'])) {
		return $block_content;
	}

	$block_name = $block['blockName'];

	// Collect block names.
	if (! in_array($block_name, $collected['blocks'], true)) {
		$collected['blocks'][] = $block_name;
	}

	// Collect style variations.
	if (
		! empty($block['attrs']['className'])
		&& preg_match('/\bis-style-([a-z0-9\-]+)\b/', $block['attrs']['className'], $m)
	) {
		$style_slug = $m[1];

		if (! isset($collected['styles'][$block_name])) {
			$collected['styles'][$block_name] = [];
		}

		if (! in_array($style_slug, $collected['styles'][$block_name], true)) {
			$collected['styles'][$block_name][] = $style_slug;
		}
	}

	// Collect gradient utility usage for group block.
	$has_gradient_attr = ! empty($block['attrs']['hxiGradient']);
	$has_gradient_class = (
		! empty($block['attrs']['className'])
		&& preg_match('/\bhas-hxi-gradient-[a-z0-9\-]+\b/', $block['attrs']['className'])
	);

	if ('core/group' === $block_name && ($has_gradient_attr || $has_gradient_class)) {
		$collected['extras']['group-gradients'] = true;
	}

	$GLOBALS['upa25_used_blocks'] = $collected;
	return $block_content;
}

/**
 * 2. Enqueue the collected block‐ and style‐variation CSS
 */
add_action('enqueue_block_assets', 'upa25_enqueue_block_styles', 20);

function upa25_enqueue_block_styles(): void
{
    $used = $GLOBALS['upa25_used_blocks'] ?? [];

    if (empty($used['blocks'])) {
        return;
    }

    $base_dir = trailingslashit(get_theme_file_path('build/styles'));
    $base_url = trailingslashit(get_theme_file_uri('build/styles'));

	// Base block styles (style.scss) - built to build/styles/{block-slug}/base.css
    foreach ($used['blocks'] as $block_name) {
        $slug = str_replace('/', '-', $block_name); // core/cover → core-cover
		$path = "{$base_dir}{$slug}/base.css";

        if (file_exists($path)) {
            wp_enqueue_style(
                "upa25-block-style-{$slug}",
				"{$base_url}{$slug}/base.css",
                [],
                filemtime($path)
            );
        }
    }

    // Style variations - new structure: build/css/blocks/{block-slug}/{style-name}.css
    if (! empty($used['styles'])) {
        foreach ($used['styles'] as $block_name => $variations) {
            $block_slug = str_replace('/', '-', $block_name);

            foreach ($variations as $style_slug) {
                $path = "{$base_dir}{$block_slug}/{$style_slug}.css";

                if (file_exists($path)) {
                    wp_enqueue_style(
                        "upa25-block-style-{$block_slug}-{$style_slug}",
                        "{$base_url}{$block_slug}/{$style_slug}.css",
                        [],
                        filemtime($path)
                    );
                }
            }
        }
    }
}

/**
 * Conditionally enqueue Group gradient utilities only when needed on the frontend.
 */
add_action('enqueue_block_assets', 'upa25_enqueue_group_gradient_styles', 25);

function upa25_get_group_gradient_style()
{
	$relative = 'build/blocks/core-group/hxi-gradient-styles.css';
	$path     = get_theme_file_path($relative);

	if (! file_exists($path)) {
		return null;
	}

	return [
		'relative' => $relative,
		'path'     => $path,
		'url'      => get_theme_file_uri($relative),
		'handle'   => 'upa25-group-gradients',
		'version'  => filemtime($path),
	];
}

function upa25_enqueue_group_gradient_styles(): void
{
	$asset = upa25_get_group_gradient_style();

	if (! $asset) {
		return;
	}

	if (is_admin()) {
		wp_enqueue_style(
			$asset['handle'],
			$asset['url'],
			[],
			$asset['version']
		);

		return;
	}

	$used = $GLOBALS['upa25_used_blocks']['extras']['group-gradients'] ?? false;

	if (! $used) {
		return;
	}

	wp_enqueue_style(
		$asset['handle'],
		$asset['url'],
		[],
		$asset['version']
	);
}
/**
 * Enqueue ALL block & variation styles into every block-based editor,
 * including the Site Editor.
 */
function upa25_enqueue_all_block_styles_in_editor(): void {
    // Skip the public frontend.
    if ( ! is_admin() ) {
        return;
    }

    $dir_base = get_theme_file_path( 'build/styles' );
    $url_base = get_theme_file_uri( 'build/styles' );

	// Enqueue all CSS files: build/styles/{block-slug}/*.css
	// This includes both base styles (base.css) and variations
    foreach ( glob( $dir_base . '/*/*.css' ) as $file ) {
        // Skip RTL files
        if ( str_ends_with( $file, '-rtl.css' ) ) {
            continue;
        }

        $rel               = str_replace( $dir_base, '', $file );      // "/core-button/outline.css"
        $rel               = ltrim( $rel, '/' );                       // "core-button/outline.css"
        list( $block_slug, $css_file ) = explode( '/', $rel, 2 );      // [ "core-button", "outline.css" ]
        $style_slug        = basename( $css_file, '.css' );
        wp_enqueue_style(
            "upa25-block-style-{$block_slug}-{$style_slug}",
            "{$url_base}/{$block_slug}/{$style_slug}.css",
            [],
            filemtime( $file )
        );
    }
}

// Page/post editors.
add_action( 'enqueue_block_editor_assets', 'upa25_enqueue_all_block_styles_in_editor', 5 );
// Site Editor (template & template-part editing).
add_action( 'enqueue_block_assets',       'upa25_enqueue_all_block_styles_in_editor', 5 );
