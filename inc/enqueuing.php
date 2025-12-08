<?php

/**
 * Enqueue frontend and editor styles.
 *
 * @package upa25
 * @version 0.2.0
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

	// Note: global.js is empty (comments-only) so we skip enqueuing it.

	// Enqueue part styles and scripts
	upa25_enqueue_parts();

	// Enqueue section styles and scripts
	upa25_enqueue_sections();
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
 * Enqueue part styles and scripts conditionally based on usage.
 */
function upa25_enqueue_parts(): void
{
	// Skip on admin/editor - always load all parts there
	if (is_admin()) {
		upa25_enqueue_all_parts();
		return;
	}

	$used = $GLOBALS['upa25_used_blocks']['parts'] ?? [];

	// If no parts collected yet (before render), enqueue all
	if (empty($used)) {
		upa25_enqueue_all_parts();
		return;
	}

	$parts_dir = get_theme_file_path('build/parts');
	$parts_url = get_theme_file_uri('build/parts');

	// Only enqueue parts that are actually used
	foreach ($used as $part_slug) {
		// Enqueue CSS
		$css_file = "{$parts_dir}/{$part_slug}.css";
		if (file_exists($css_file)) {
			$asset_file = str_replace('.css', '.asset.php', $css_file);
			if (file_exists($asset_file)) {
				$asset = require $asset_file;
				$handle = "upa25-part-{$part_slug}";
				wp_enqueue_style(
					$handle,
					"{$parts_url}/{$part_slug}.css",
					$asset['dependencies'] ?? [],
					$asset['version'] ?? filemtime($css_file)
				);
				// Load async to prevent render blocking
				wp_style_add_data($handle, 'media', 'print');
				wp_style_add_data($handle, 'onload', "this.media='all'");
			}
		}

		// Enqueue JS
		$js_file = "{$parts_dir}/{$part_slug}.js";
		if (file_exists($js_file)) {
			$asset_file = str_replace('.js', '.asset.php', $js_file);
			if (file_exists($asset_file)) {
				$asset = require $asset_file;
				$handle = "upa25-part-js-{$part_slug}";
				wp_enqueue_script(
					$handle,
					"{$parts_url}/{$part_slug}.js",
					$asset['dependencies'] ?? [],
					$asset['version'] ?? filemtime($js_file),
					true // Load in footer
				);
				// Add defer attribute for non-blocking load
				wp_script_add_data($handle, 'strategy', 'defer');
			}
		}
	}
}

/**
 * Enqueue all parts (for editor or fallback).
 */
function upa25_enqueue_all_parts(): void
{
	$parts_dir = get_theme_file_path('build/parts');
	$parts_url = get_theme_file_uri('build/parts');

	if (!is_dir($parts_dir)) {
		return;
	}

	// Enqueue CSS files from build/parts/ (non-blocking)
	foreach (glob($parts_dir . '/*.css') as $file) {
		$part_name = basename($file, '.css');
		$asset_file = str_replace('.css', '.asset.php', $file);

		if (file_exists($asset_file)) {
			$asset = require $asset_file;
			$handle = "upa25-part-{$part_name}";
			wp_enqueue_style(
				$handle,
				"{$parts_url}/{$part_name}.css",
				$asset['dependencies'] ?? [],
				$asset['version'] ?? filemtime($file)
			);
			// Load async to prevent render blocking
			wp_style_add_data($handle, 'media', 'print');
			wp_style_add_data($handle, 'onload', "this.media='all'");
		}
	}

	// Enqueue JS files from build/parts/ (deferred)
	foreach (glob($parts_dir . '/*.js') as $file) {
		$part_name = basename($file, '.js');
		$asset_file = str_replace('.js', '.asset.php', $file);

		if (file_exists($asset_file)) {
			$asset = require $asset_file;
			$handle = "upa25-part-js-{$part_name}";
			wp_enqueue_script(
				$handle,
				"{$parts_url}/{$part_name}.js",
				$asset['dependencies'] ?? [],
				$asset['version'] ?? filemtime($file),
				true // Load in footer
			);
			// Add defer attribute for non-blocking load
			wp_script_add_data($handle, 'strategy', 'defer');
		}
	}
}


/**
 * Enqueue section styles and scripts conditionally based on usage.
 */
function upa25_enqueue_sections(): void
{
	// Skip on admin/editor - always load all sections there
	if (is_admin()) {
		upa25_enqueue_all_sections();
		return;
	}

	$used = $GLOBALS['upa25_used_blocks']['sections'] ?? [];

	// If no sections collected yet (before render), enqueue all
	if (empty($used)) {
		upa25_enqueue_all_sections();
		return;
	}

	$sections_dir = get_theme_file_path('build/sections');
	$sections_url = get_theme_file_uri('build/sections');

	// Only enqueue sections that are actually used
	foreach ($used as $section_slug) {
		// Enqueue CSS
		$css_file = "{$sections_dir}/{$section_slug}.css";
		if (file_exists($css_file)) {
			$asset_file = str_replace('.css', '.asset.php', $css_file);
			if (file_exists($asset_file)) {
				$asset = require $asset_file;
				$handle = "upa25-section-{$section_slug}";
				wp_enqueue_style(
					$handle,
					"{$sections_url}/{$section_slug}.css",
					$asset['dependencies'] ?? [],
					$asset['version'] ?? filemtime($css_file)
				);
				// Load async to prevent render blocking
				wp_style_add_data($handle, 'media', 'print');
				wp_style_add_data($handle, 'onload', "this.media='all'");
			}
		}

		// Enqueue JS
		foreach (glob($sections_dir . '/*.js') as $js_file) {
			$js_name = basename($js_file, '.js');
			$asset_file = str_replace('.js', '.asset.php', $js_file);

			if (file_exists($asset_file)) {
				$asset = require $asset_file;
				$handle = "upa25-section-js-{$js_name}";
				wp_enqueue_script(
					$handle,
					"{$sections_url}/{$js_name}.js",
					$asset['dependencies'] ?? [],
					$asset['version'] ?? filemtime($js_file),
					true // Load in footer
				);
				// Add defer attribute for non-blocking load
				wp_script_add_data($handle, 'strategy', 'defer');
			}
		}
	}
}

/**
 * Enqueue all sections (for editor or fallback).
 */
function upa25_enqueue_all_sections(): void
{
	$sections_dir = get_theme_file_path('build/sections');
	$sections_url = get_theme_file_uri('build/sections');

	if (!is_dir($sections_dir)) {
		return;
	}

	// Enqueue CSS files from build/sections/ (non-blocking)
	foreach (glob($sections_dir . '/*.css') as $file) {
		$section_name = basename($file, '.css');
		$asset_file = str_replace('.css', '.asset.php', $file);

		if (file_exists($asset_file)) {
			$asset = require $asset_file;
			$handle = "upa25-section-{$section_name}";
			wp_enqueue_style(
				$handle,
				"{$sections_url}/{$section_name}.css",
				$asset['dependencies'] ?? [],
				$asset['version'] ?? filemtime($file)
			);
			// Load async to prevent render blocking
			wp_style_add_data($handle, 'media', 'print');
			wp_style_add_data($handle, 'onload', "this.media='all'");
		}
	}

	// Enqueue JS files from build/sections/ (deferred)
	foreach (glob($sections_dir . '/*.js') as $file) {
		$section_name = basename($file, '.js');
		$asset_file = str_replace('.js', '.asset.php', $file);

		if (file_exists($asset_file)) {
			$asset = require $asset_file;
			$handle = "upa25-section-js-{$section_name}";
			wp_enqueue_script(
				$handle,
				"{$sections_url}/{$section_name}.js",
				$asset['dependencies'] ?? [],
				$asset['version'] ?? filemtime($file),
				true // Load in footer
			);
			// Add defer attribute for non-blocking load
			wp_script_add_data($handle, 'strategy', 'defer');
		}
	}
}

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
		'parts' => [],
		'sections' => [],
	];

	if (empty($block['blockName'])) {
		return $block_content;
	}

	$block_name = $block['blockName'];

	// Collect block names.
	if (! in_array($block_name, $collected['blocks'], true)) {
		$collected['blocks'][] = $block_name;
	}

	// Collect template parts (header, footer, etc.)
	if ('core/template-part' === $block_name && !empty($block['attrs']['slug'])) {
		$part_slug = $block['attrs']['slug'];
		if (!in_array($part_slug, $collected['parts'], true)) {
			$collected['parts'][] = $part_slug;
		}
	}

	// Collect sections (brand section, etc.)
	if (!empty($block['attrs']['className'])) {
		// Check for section-brand class or similar
		if (preg_match('/\bis-style-section-([a-z0-9\-]+)\b/', $block['attrs']['className'], $m)) {
			$section_slug = $m[1];
			if (!in_array($section_slug, $collected['sections'], true)) {
				$collected['sections'][] = $section_slug;
			}
		}
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
