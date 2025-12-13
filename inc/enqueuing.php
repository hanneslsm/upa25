<?php
/**
 * Asset registration and enqueueing helpers.
 *
 * @package upa25
 * @version 4.0.0
 */

/**
 * Enqueue the shared theme styles for both the editor and the frontend.
 */
function upa25_enqueue_scripts(): void {
	upa25_enqueue_style_asset( 'upa25-global-style', 'build/theme/global-styles.css', ! is_admin() );

	// global.js is intentionally empty so we skip enqueuing it.
}
add_action( 'enqueue_block_assets', 'upa25_enqueue_scripts' );

/**
 * Enqueue the public-facing stylesheet.
 */
function upa25_enqueue_frontend_styles(): void {
	upa25_enqueue_style_asset( 'upa25-screen-style', 'build/theme/screen.css' );
}
add_action( 'wp_enqueue_scripts', 'upa25_enqueue_frontend_styles' );

/**
 * Register CSS handles for every block style emitted during the build step.
 *
 * WordPress uses these handles when register_block_style() references them,
 * and wp_enqueue_block_style() adds the base styles to matching blocks.
 */
function upa25_register_block_style_assets(): void {
	$blocks_dir = get_theme_file_path( 'build/blocks' );
	if ( ! is_dir( $blocks_dir ) ) {
		return;
	}

	foreach ( glob( $blocks_dir . '/*', GLOB_ONLYDIR ) as $block_path ) {
		$block_slug = basename( $block_path );
		$block_name = preg_replace( '/-/', '/', $block_slug, 1 );

		// Register base style (style.css) if it exists
		$base_style_file = $block_path . '/style.css';
		if ( file_exists( $base_style_file ) ) {
			$handle   = "upa25-block-style-{$block_slug}-base";
			$relative = "build/blocks/{$block_slug}/style.css";
			$asset    = upa25_read_asset_file( $base_style_file );

			wp_register_style(
				$handle,
				get_theme_file_uri( $relative ),
				$asset['dependencies'],
				$asset['version']
			);

			// Auto-enqueue base styles for the block
			wp_enqueue_block_style(
				$block_name,
				array(
					'handle' => $handle,
				)
			);
		}

		// Register style variations from styles/ subdirectory
		$styles_dir = $block_path . '/styles';
		if ( is_dir( $styles_dir ) ) {
			foreach ( glob( $styles_dir . '/*.css' ) as $css_file ) {
				if ( str_ends_with( $css_file, '-rtl.css' ) ) {
					continue;
				}

				$style_slug = basename( $css_file, '.css' );
				$handle     = "upa25-block-style-{$block_slug}-{$style_slug}";
				$relative   = "build/blocks/{$block_slug}/styles/{$style_slug}.css";
				$asset      = upa25_read_asset_file( $css_file );

				wp_register_style(
					$handle,
					get_theme_file_uri( $relative ),
					$asset['dependencies'],
					$asset['version']
				);
			}
		}
	}
}
add_action( 'init', 'upa25_register_block_style_assets', 9 );

/**
 * Always load component assets inside the block editor / site editor.
 */
function upa25_enqueue_component_assets_in_editor(): void {
	static $did_enqueue = false;

	if ( $did_enqueue || ! is_admin() ) {
		return;
	}

	$did_enqueue = true;

	// Enqueue all components from build/includes/
	upa25_enqueue_all_includes_assets();

	// Enqueue all block style variation handles for editor preview
	upa25_enqueue_all_block_style_handles();
}
add_action( 'enqueue_block_editor_assets', 'upa25_enqueue_component_assets_in_editor', 5 );
add_action( 'enqueue_block_assets', 'upa25_enqueue_component_assets_in_editor', 5 );

/**
 * Conditionally enqueue component assets on the frontend as blocks render.
 *
 * @param string $block_content Rendered markup.
 * @param array  $block         Block metadata.
 *
 * @return string
 */
function upa25_enqueue_dynamic_component_assets( string $block_content, array $block ): string {
	if ( is_admin() ) {
		return $block_content;
	}

	upa25_maybe_enqueue_include_assets( $block );
	upa25_maybe_enqueue_block_style_variations( $block );

	return $block_content;
}
add_filter( 'render_block', 'upa25_enqueue_dynamic_component_assets', 10, 2 );

/**
 * Load component assets from build/includes/ when blocks reference them.
 *
 * Components can be referenced via:
 * - Template part slug (core/template-part)
 * - CSS class (is-style-*, hxiGradient, etc.)
 *
 * @param array $block The current block data.
 */
function upa25_maybe_enqueue_include_assets( array $block ): void {
	$components = upa25_get_includes_component_map();
	if ( empty( $components ) ) {
		return;
	}

	$slugs_to_enqueue = array();

	// Check template-part slug
	if ( 'core/template-part' === ( $block['blockName'] ?? '' ) && ! empty( $block['attrs']['slug'] ) ) {
		$slug = sanitize_title( $block['attrs']['slug'] );
		// Map template-part slug to component (e.g., 'header' -> 'parts-header')
		$potential_slugs = array(
			$slug,
			'parts-' . $slug,
		);
		foreach ( $potential_slugs as $potential_slug ) {
			if ( isset( $components[ $potential_slug ] ) ) {
				$slugs_to_enqueue[] = $potential_slug;
			}
		}
	}

	// Check className for style variations and custom classes
	if ( ! empty( $block['attrs']['className'] ) ) {
		$class_name = $block['attrs']['className'];

		// Match is-style-* classes
		if ( preg_match_all( '/\bis-style-([a-z0-9\-]+)\b/', $class_name, $matches ) ) {
			foreach ( $matches[1] as $style_slug ) {
				$potential_slugs = array(
					$style_slug,
					'parts-' . $style_slug,
				);

				foreach ( $potential_slugs as $potential_slug ) {
					if ( isset( $components[ $potential_slug ] ) ) {
						$slugs_to_enqueue[] = $potential_slug;
					}
				}
			}
		}

	}
	/**
	 * Filters the include component slugs detected for the current block.
	 *
	 * Components can hook into this filter to declare their own detection
	 * logic without modifying the generic enqueue helpers.
	 *
	 * @param array $slugs_to_enqueue Slugs detected so far.
	 * @param array $block            The current block metadata.
	 * @param array $components       Available include component slugs.
	 */
	$slugs_to_enqueue = apply_filters(
		'upa25_include_component_slugs',
		$slugs_to_enqueue,
		$block,
		$components
	);

	// Enqueue unique components
	$slugs_to_enqueue = array_unique( $slugs_to_enqueue );
	foreach ( $slugs_to_enqueue as $slug ) {
		upa25_enqueue_include_component( $slug );
	}
}

/**
 * Ensure block style variation handles load whenever a matching class appears.
 *
 * @param array $block Current block metadata.
 */
function upa25_maybe_enqueue_block_style_variations( array $block ): void {
	if ( empty( $block['blockName'] ) || empty( $block['attrs']['className'] ) ) {
		return;
	}

	if ( ! preg_match_all( '/\bis-style-([a-z0-9\-]+)\b/', $block['attrs']['className'], $matches ) ) {
		return;
	}

	foreach ( $matches[1] as $style_slug ) {
		$handle = upa25_build_block_style_handle( $block['blockName'], $style_slug );
		if ( wp_style_is( $handle, 'registered' ) ) {
			wp_enqueue_style( $handle );
		}
	}
}

/**
 * Register block-editor stylesheet support.
 */
add_action(
	'after_setup_theme',
	function () {
		add_editor_style( 'build/theme/editor.css' );
	}
);

/**
 * Enqueue a style relative to the theme root and include dependencies.
 *
 * @param string $handle        Unique style handle.
 * @param string $relative_path Path relative to the theme directory.
 * @param bool   $async         Whether to load asynchronously on the frontend.
 */
function upa25_enqueue_style_asset( string $handle, string $relative_path, bool $async = false ): void {
	$file_path = get_theme_file_path( $relative_path );
	if ( ! file_exists( $file_path ) ) {
		return;
	}

	$asset = upa25_read_asset_file( $file_path );

	wp_enqueue_style(
		$handle,
		get_theme_file_uri( $relative_path ),
		$asset['dependencies'],
		$asset['version']
	);

	if ( $async && ! is_admin() ) {
		wp_style_add_data( $handle, 'media', 'print' );
		wp_style_add_data( $handle, 'onload', "this.media='all'" );
	}
}

/**
 * Enqueue a script relative to the theme directory.
 *
 * @param string $handle        Script handle.
 * @param string $relative_path Relative path.
 * @param bool   $defer         Whether to defer execution on the frontend.
 */
function upa25_enqueue_script_asset( string $handle, string $relative_path, bool $defer = true ): void {
	$file_path = get_theme_file_path( $relative_path );
	if ( ! file_exists( $file_path ) ) {
		return;
	}

	$asset = upa25_read_asset_file( $file_path );

	wp_enqueue_script(
		$handle,
		get_theme_file_uri( $relative_path ),
		$asset['dependencies'],
		$asset['version'],
		true
	);

	if ( $defer ) {
		wp_script_add_data( $handle, 'strategy', 'defer' );
	}
}

/**
 * Retrieve dependency metadata from the generated asset file.
 *
 * @param string $file_path Absolute file path.
 *
 * @return array
 */
function upa25_read_asset_file( string $file_path ): array {
	$asset_path = preg_replace( '/\.(css|js)$/', '.asset.php', $file_path );
	if ( $asset_path && file_exists( $asset_path ) ) {
		$data = require $asset_path;
		if ( is_array( $data ) ) {
			return array(
				'dependencies' => $data['dependencies'] ?? array(),
				'version'      => $data['version'] ?? filemtime( $file_path ),
			);
		}
	}

	return array(
		'dependencies' => array(),
		'version'      => filemtime( $file_path ),
	);
}

/**
 * Cache available component slugs from build/includes/ to avoid repeated filesystem scans.
 *
 * @return array<string,bool>
 */
function upa25_get_includes_component_map(): array {
	static $cache = null;
	if ( null !== $cache ) {
		return $cache;
	}

	$dir = get_theme_file_path( 'build/includes' );
	if ( ! is_dir( $dir ) ) {
		$cache = array();
		return $cache;
	}

	$components = array();
	foreach ( glob( $dir . '/*', GLOB_ONLYDIR ) as $component_dir ) {
		$slug = basename( $component_dir );
		// Check if component has at least one asset file
		$has_assets = ! empty( glob( $component_dir . '/*.{css,js}', GLOB_BRACE ) );
		if ( $has_assets ) {
			$components[ $slug ] = true;
		}
	}

	$cache = $components;
	return $components;
}

/**
 * Enqueue all assets for all components in build/includes/.
 * Used in the editor to ensure all components preview correctly.
 */
function upa25_enqueue_all_includes_assets(): void {
	$dir = get_theme_file_path( 'build/includes' );
	if ( ! is_dir( $dir ) ) {
		return;
	}

	foreach ( glob( $dir . '/*', GLOB_ONLYDIR ) as $component_dir ) {
		$slug = basename( $component_dir );
		upa25_enqueue_include_component( $slug );
	}
}

/**
 * Enqueue a single component's CSS/JS bundle from build/includes/.
 *
 * @param string $slug Component slug (directory name in build/includes/).
 */
function upa25_enqueue_include_component( string $slug ): void {
	static $enqueued = array();

	// Prevent duplicate enqueueing
	if ( isset( $enqueued[ $slug ] ) ) {
		return;
	}

	$component_dir = get_theme_file_path( "build/includes/{$slug}" );
	if ( ! is_dir( $component_dir ) ) {
		return;
	}

	$enqueued[ $slug ] = true;

	// Enqueue all CSS files in the component directory (both frontend and editor)
	foreach ( glob( $component_dir . '/*.css' ) as $css_file ) {
		if ( str_ends_with( $css_file, '-rtl.css' ) ) {
			continue;
		}

		$filename = basename( $css_file, '.css' );
		$handle   = "upa25-include-{$slug}-{$filename}";
		$relative = "build/includes/{$slug}/{$filename}.css";

		// Enqueue CSS always. Async flag only affects frontend lazy-loading (! is_admin()).
		upa25_enqueue_style_asset( $handle, $relative, ! is_admin() );
	}

	/**
	 * Allow components to opt-out of automatic JS enqueueing.
	 *
	 * Returning false lets a component handle its JS manually (e.g. editor-only
	 * scripts) while still benefiting from the shared CSS loader above.
	 *
	 * @param bool   $should_enqueue_js Whether JS should be enqueued for the slug.
	 * @param string $slug              Component slug.
	 * @param string $component_dir     Absolute path to the component assets.
	 */
	$should_enqueue_js = apply_filters(
		'upa25_should_enqueue_include_component_js',
		true,
		$slug,
		$component_dir
	);

	if ( ! $should_enqueue_js ) {
		return;
	}

	// Enqueue all JS files in the component directory
	foreach ( glob( $component_dir . '/*.js' ) as $js_file ) {
		$filename = basename( $js_file, '.js' );
		$handle   = "upa25-include-{$slug}-{$filename}";
		$relative = "build/includes/{$slug}/{$filename}.js";

		upa25_enqueue_script_asset( $handle, $relative );
	}
}

/**
 * Enqueue every registered block-style handle inside the editor so previews
 * always match the frontend output.
 */
function upa25_enqueue_all_block_style_handles(): void {
	$blocks_dir = get_theme_file_path( 'build/blocks' );
	if ( ! is_dir( $blocks_dir ) ) {
		return;
	}

	foreach ( glob( $blocks_dir . '/*', GLOB_ONLYDIR ) as $block_path ) {
		$block_slug = basename( $block_path );

		// Enqueue base style if registered
		$base_handle = "upa25-block-style-{$block_slug}-base";
		if ( wp_style_is( $base_handle, 'registered' ) ) {
			wp_enqueue_style( $base_handle );
		}

		// Enqueue all style variations
		$styles_dir = $block_path . '/styles';
		if ( is_dir( $styles_dir ) ) {
			foreach ( glob( $styles_dir . '/*.css' ) as $css_file ) {
				if ( str_ends_with( $css_file, '-rtl.css' ) ) {
					continue;
				}

				$style_slug = basename( $css_file, '.css' );
				$handle     = "upa25-block-style-{$block_slug}-{$style_slug}";

				if ( wp_style_is( $handle, 'registered' ) ) {
					wp_enqueue_style( $handle );
				}
			}
		}
	}
}

/**
 * Build a consistent handle for block style variations.
 *
 * @param string $block_name Block name such as core/paragraph.
 * @param string $style_slug Style slug (e.g. number).
 *
 * @return string
 */
function upa25_build_block_style_handle( string $block_name, string $style_slug ): string {
	return 'upa25-block-style-' . str_replace( '/', '-', $block_name ) . '-' . $style_slug;
}
