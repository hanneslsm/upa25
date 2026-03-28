<?php
/**
 * Asset registration and enqueueing helpers.
 *
 * Auto-discovers and conditionally loads assets from the build/ folder structure:
 * - build/blocks/{namespace}/{block}/ — Block base styles, style variations, custom blocks
 * - build/includes/{category}/{name}/ — Component assets (plugins, utilities, etc.)
 *
 * @package upa25
 * @version 5.2.1
 */

declare( strict_types=1 );

/*
|--------------------------------------------------------------------------
| Global & Screen Styles
|--------------------------------------------------------------------------
*/

/**
 * Enqueue the shared theme styles for both the editor and the frontend.
 */
function upa25_enqueue_global_styles(): void {
	upa25_enqueue_style_asset( 'upa25-global-style', 'build/global-styles.css', ! is_admin() );
}
add_action( 'enqueue_block_assets', 'upa25_enqueue_global_styles' );

/**
 * Enqueue the public-facing stylesheet.
 */
function upa25_enqueue_frontend_styles(): void {
	upa25_enqueue_style_asset( 'upa25-screen-style', 'build/screen.css' );
}
add_action( 'wp_enqueue_scripts', 'upa25_enqueue_frontend_styles' );

/**
 * Register block-editor stylesheet support.
 */
function upa25_register_editor_styles(): void {
	add_editor_style( 'build/editor.css' );
}
add_action( 'after_setup_theme', 'upa25_register_editor_styles' );

/*
|--------------------------------------------------------------------------
| Block Assets Auto-Registration
|--------------------------------------------------------------------------
|
| Scans build/blocks/{namespace}/{block}/ for:
| - style.css — Base block styles (auto-enqueued via wp_enqueue_block_style)
| - styles/*.css — Style variations (registered, enqueued when is-style-* detected)
| - block.json — Custom block registration (calls register_block_type)
| - editor.js — Editor-only scripts (enqueued via enqueue_block_editor_assets)
|
*/

/**
 * Register and auto-enqueue block assets from build/blocks/.
 */
function upa25_register_block_assets(): void {
	$blocks_dir = get_theme_file_path( 'build/blocks' );
	if ( ! is_dir( $blocks_dir ) ) {
		return;
	}

	// Scan for namespace directories (core, woocommerce, upa25, etc.)
	foreach ( glob( $blocks_dir . '/*', GLOB_ONLYDIR ) as $namespace_path ) {
		$namespace = basename( $namespace_path );

		// Scan for block directories within namespace
		foreach ( glob( $namespace_path . '/*', GLOB_ONLYDIR ) as $block_path ) {
			$block_slug = basename( $block_path );
			$block_name = "{$namespace}/{$block_slug}";

			// Register custom block if block.json exists
			$block_json = $block_path . '/block.json';
			if ( file_exists( $block_json ) ) {
				register_block_type( $block_path );
			}

			// Register base style (style.css) if it exists
			$base_style_file = $block_path . '/style.css';
			if ( file_exists( $base_style_file ) ) {
				$handle   = upa25_build_block_style_handle( $block_name, 'base' );
				$relative = "build/blocks/{$namespace}/{$block_slug}/style.css";
				$asset    = upa25_read_asset_file( $base_style_file );

				wp_register_style(
					$handle,
					get_theme_file_uri( $relative ),
					$asset['dependencies'],
					$asset['version']
				);

				// Auto-enqueue base styles when block is used
				wp_enqueue_block_style(
					$block_name,
					array( 'handle' => $handle )
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
					$handle     = upa25_build_block_style_handle( $block_name, $style_slug );
					$relative   = "build/blocks/{$namespace}/{$block_slug}/styles/{$style_slug}.css";
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
}
add_action( 'init', 'upa25_register_block_assets', 9 );

/**
 * Enqueue editor.js scripts for core block extensions.
 *
 * Scans build/blocks/{namespace}/{block}/ for editor.js files and enqueues
 * them in the block editor. Skips custom blocks (those with block.json) as
 * they handle their own editor scripts.
 */
function upa25_enqueue_block_editor_scripts(): void {
	$blocks_dir = get_theme_file_path( 'build/blocks' );
	if ( ! is_dir( $blocks_dir ) ) {
		return;
	}

	// Scan for namespace directories (core, woocommerce, etc.)
	foreach ( glob( $blocks_dir . '/*', GLOB_ONLYDIR ) as $namespace_path ) {
		$namespace = basename( $namespace_path );

		// Scan for block directories within namespace
		foreach ( glob( $namespace_path . '/*', GLOB_ONLYDIR ) as $block_path ) {
			$block_slug = basename( $block_path );

			// Skip custom blocks (they handle their own scripts via block.json)
			if ( file_exists( $block_path . '/block.json' ) ) {
				continue;
			}

			// Enqueue editor.js if it exists
			$editor_js = $block_path . '/editor.js';
			if ( file_exists( $editor_js ) ) {
				$handle   = "upa25-block-{$namespace}-{$block_slug}-editor";
				$relative = "build/blocks/{$namespace}/{$block_slug}/editor.js";

				upa25_enqueue_script_asset( $handle, $relative, false );
			}
		}
	}
}
add_action( 'enqueue_block_editor_assets', 'upa25_enqueue_block_editor_scripts' );

/**
 * Auto-load PHP files from build/blocks/ (e.g., block controls, filters).
 *
 * Skips render.php files as these are block render templates handled by WordPress.
 */
function upa25_autoload_block_php_files(): void {
	$blocks_dir = get_theme_file_path( 'build/blocks' );
	if ( ! is_dir( $blocks_dir ) ) {
		return;
	}

	// Recursively find all PHP files in build/blocks/
	$iterator = new RecursiveIteratorIterator(
		new RecursiveDirectoryIterator( $blocks_dir, RecursiveDirectoryIterator::SKIP_DOTS )
	);

	foreach ( $iterator as $file ) {
		// Skip render.php files (block render templates handled by WP block rendering).
		if ( $file->isFile() && 'php' === $file->getExtension() && 'render.php' !== $file->getFilename() ) {
			require_once $file->getPathname();
		}
	}
}
add_action( 'after_setup_theme', 'upa25_autoload_block_php_files', 5 );

/**
 * Auto-load PHP files from build/includes/ (e.g., component logic, filters).
 *
 * Skips render.php files as these are render templates handled by WordPress.
 */
function upa25_autoload_includes_php_files(): void {
	$includes_dir = get_theme_file_path( 'build/includes' );
	if ( ! is_dir( $includes_dir ) ) {
		return;
	}

	// Recursively find all PHP files in build/includes/
	$iterator = new RecursiveIteratorIterator(
		new RecursiveDirectoryIterator( $includes_dir, RecursiveDirectoryIterator::SKIP_DOTS )
	);

	foreach ( $iterator as $file ) {
		// Skip render.php files (render templates handled by the theme).
		if ( $file->isFile() && 'php' === $file->getExtension() && 'render.php' !== $file->getFilename() ) {
			require_once $file->getPathname();
		}
	}
}
add_action( 'after_setup_theme', 'upa25_autoload_includes_php_files', 5 );

/**
 * Auto-load PHP files from build/plugins/ during theme setup.
 *
 * Runs on after_setup_theme (late priority) so plugin files can hook into
 * after_setup_theme and init.
 */
function upa25_autoload_plugin_php_files(): void {
	$plugins_dir = get_theme_file_path( 'build/plugins' );
	if ( ! is_dir( $plugins_dir ) ) {
		return;
	}

	// Scan plugin directories
	foreach ( glob( $plugins_dir . '/*', GLOB_ONLYDIR ) as $plugin_path ) {
		$plugin_slug = basename( $plugin_path );

		// Only load if the plugin is active
		if ( ! upa25_is_plugin_active( $plugin_slug ) ) {
			continue;
		}

		// Recursively find all PHP files in this plugin's directory
		$iterator = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator( $plugin_path, RecursiveDirectoryIterator::SKIP_DOTS )
		);

		foreach ( $iterator as $file ) {
			if ( $file->isFile() && 'php' === $file->getExtension() ) {
				require_once $file->getPathname();
			}
		}
	}
}
add_action( 'after_setup_theme', 'upa25_autoload_plugin_php_files', 99 );

/*
|--------------------------------------------------------------------------
| Includes / Component Assets
|--------------------------------------------------------------------------
|
| Scans build/includes/{category}/{name}/ for component assets:
| - plugins/{slug}/ — Loads globally when plugin is active
| - {category}/{name}/ — Loads when CSS class matches component name
|
*/

/**
 * Build the includes component map with category awareness.
 *
 * @return array<string, array{category: string, name: string, path: string}>
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

	// Scan category directories
	foreach ( glob( $dir . '/*', GLOB_ONLYDIR ) as $category_path ) {
		$category = basename( $category_path );

		// Scan component directories within category
		foreach ( glob( $category_path . '/*', GLOB_ONLYDIR ) as $component_path ) {
			$name = basename( $component_path );

			// Check if component has at least one asset file
			$has_assets = ! empty( glob( $component_path . '/*.{css,js}', GLOB_BRACE ) );
			if ( $has_assets ) {
				$key                = "{$category}/{$name}";
				$components[ $key ] = array(
					'category' => $category,
					'name'     => $name,
					'path'     => $component_path,
				);
			}
		}
	}

	$cache = $components;
	return $components;
}

/**
 * Check if a plugin is active by slug.
 *
 * Tries common plugin file patterns.
 *
 * @param string $slug Plugin slug.
 * @return bool
 */
function upa25_is_plugin_active( string $slug ): bool {
	if ( ! function_exists( 'is_plugin_active' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}

	// First try by directory slug against active plugins.
	$active_plugins = (array) get_option( 'active_plugins', array() );
	foreach ( $active_plugins as $plugin_file ) {
		if ( $slug === dirname( (string) $plugin_file ) ) {
			return true;
		}
	}

	// Multisite network-active plugins.
	if ( is_multisite() ) {
		$network_plugins = (array) get_site_option( 'active_sitewide_plugins', array() );
		foreach ( array_keys( $network_plugins ) as $plugin_file ) {
			if ( $slug === dirname( (string) $plugin_file ) ) {
				return true;
			}
		}
	}

	// Try common patterns
	$patterns = array(
		"{$slug}/{$slug}.php",
		"{$slug}/wp-{$slug}.php",
		"{$slug}/plugin.php",
		"{$slug}/index.php",
		"{$slug}.php",
	);

	foreach ( $patterns as $pattern ) {
		if ( is_plugin_active( $pattern ) ) {
			return true;
		}
	}

	return false;
}

/**
 * Build the plugins component map from build/plugins/.
 *
 * @return array<string, array{slug: string, path: string}>
 */
function upa25_get_plugins_component_map(): array {
	static $cache = null;
	if ( null !== $cache ) {
		return $cache;
	}

	$dir = get_theme_file_path( 'build/plugins' );
	if ( ! is_dir( $dir ) ) {
		$cache = array();
		return $cache;
	}

	$components = array();

	// Scan plugin directories
	foreach ( glob( $dir . '/*', GLOB_ONLYDIR ) as $plugin_path ) {
		$slug = basename( $plugin_path );

		// Check if plugin directory has at least one asset file
		$has_assets = ! empty( glob( $plugin_path . '/*.{css,js}', GLOB_BRACE ) );
		if ( $has_assets ) {
			$components[ $slug ] = array(
				'slug' => $slug,
				'path' => $plugin_path,
			);
		}
	}

	$cache = $components;
	return $components;
}

/**
 * Enqueue plugin component assets globally when plugin is active.
 */
function upa25_enqueue_plugin_assets(): void {
	$components = upa25_get_plugins_component_map();

	foreach ( $components as $slug => $component ) {
		// Check if plugin is active
		if ( upa25_is_plugin_active( $slug ) ) {
			upa25_enqueue_plugin_component( $slug );
		}
	}
}
add_action( 'wp_enqueue_scripts', 'upa25_enqueue_plugin_assets' );

/**
 * Enqueue a single plugin component's CSS/JS.
 *
 * @param string $slug Plugin slug.
 */
function upa25_enqueue_plugin_component( string $slug ): void {
	static $enqueued = array();

	if ( isset( $enqueued[ $slug ] ) ) {
		return;
	}

	$components = upa25_get_plugins_component_map();
	if ( ! isset( $components[ $slug ] ) ) {
		return;
	}

	$enqueued[ $slug ] = true;
	$component         = $components[ $slug ];
	$component_dir     = $component['path'];
	$relative_base     = "build/plugins/{$slug}";

	// Enqueue all CSS files
	foreach ( glob( $component_dir . '/*.css' ) as $css_file ) {
		if ( str_ends_with( $css_file, '-rtl.css' ) ) {
			continue;
		}

		$filename = basename( $css_file, '.css' );
		$handle   = "upa25-plugin-{$slug}-{$filename}";
		$relative = "{$relative_base}/{$filename}.css";

		upa25_enqueue_style_asset( $handle, $relative, ! is_admin() );
	}

	// Enqueue view.js on frontend, editor.js in editor
	$view_js = $component_dir . '/view.js';
	if ( file_exists( $view_js ) && ! is_admin() ) {
		$handle   = "upa25-plugin-{$slug}-view";
		$relative = "{$relative_base}/view.js";
		upa25_enqueue_script_asset( $handle, $relative );
	}

	$editor_js = $component_dir . '/editor.js';
	if ( file_exists( $editor_js ) && is_admin() ) {
		$handle   = "upa25-plugin-{$slug}-editor";
		$relative = "{$relative_base}/editor.js";
		upa25_enqueue_script_asset( $handle, $relative, false );
	}
}

/**
 * Enqueue all include assets in the editor for preview consistency.
 */
function upa25_enqueue_includes_in_editor(): void {
	if ( ! is_admin() ) {
		return;
	}

	$components = upa25_get_includes_component_map();
	foreach ( $components as $key => $component ) {
		upa25_enqueue_include_component( $key );
	}

	// Also enqueue plugin assets in editor
	$plugins = upa25_get_plugins_component_map();
	foreach ( $plugins as $slug => $plugin ) {
		if ( upa25_is_plugin_active( $slug ) ) {
			upa25_enqueue_plugin_component( $slug );
		}
	}
}
add_action( 'enqueue_block_assets', 'upa25_enqueue_includes_in_editor' );

/**
 * Enqueue all block style variation handles in the editor.
 */
function upa25_enqueue_block_styles_in_editor(): void {
	if ( ! is_admin() ) {
		return;
	}

	$blocks_dir = get_theme_file_path( 'build/blocks' );
	if ( ! is_dir( $blocks_dir ) ) {
		return;
	}

	foreach ( glob( $blocks_dir . '/*', GLOB_ONLYDIR ) as $namespace_path ) {
		$namespace = basename( $namespace_path );

		foreach ( glob( $namespace_path . '/*', GLOB_ONLYDIR ) as $block_path ) {
			$block_slug = basename( $block_path );
			$block_name = "{$namespace}/{$block_slug}";

			// Enqueue base style if registered
			$base_handle = upa25_build_block_style_handle( $block_name, 'base' );
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
					$handle     = upa25_build_block_style_handle( $block_name, $style_slug );

					if ( wp_style_is( $handle, 'registered' ) ) {
						wp_enqueue_style( $handle );
					}
				}
			}
		}
	}
}
add_action( 'enqueue_block_assets', 'upa25_enqueue_block_styles_in_editor' );

/*
|--------------------------------------------------------------------------
| Dynamic Asset Enqueueing (Frontend)
|--------------------------------------------------------------------------
|
| On render_block, detect which assets to enqueue based on:
| - Block className (for style variations and includes)
|
*/

/**
 * Conditionally enqueue assets as blocks render on the frontend.
 *
 * @param string $block_content Rendered markup.
 * @param array  $block         Block metadata.
 * @return string
 */
function upa25_enqueue_dynamic_assets( string $block_content, array $block ): string {
	if ( is_admin() ) {
		return $block_content;
	}

	upa25_maybe_enqueue_block_style_variations( $block );
	upa25_maybe_enqueue_include_assets( $block );

	return $block_content;
}
add_filter( 'render_block', 'upa25_enqueue_dynamic_assets', 10, 2 );

/**
 * Enqueue block style variation CSS when is-style-* class detected.
 *
 * @param array $block Block metadata.
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
 * Enqueue include component assets based on block className.
 *
 * Detects components by matching className against component names.
 * Plugins are handled separately via upa25_enqueue_plugin_includes().
 *
 * @param array $block Block metadata.
 */
function upa25_maybe_enqueue_include_assets( array $block ): void {
	$components = upa25_get_includes_component_map();
	if ( empty( $components ) ) {
		return;
	}

	$class_name = $block['attrs']['className'] ?? '';
	if ( empty( $class_name ) ) {
		return;
	}

	$slugs_to_enqueue = array();

	foreach ( $components as $key => $component ) {
		$name = $component['name'];

		// Match exact class name or is-style-{name}
		$patterns = array(
			'/\\b' . preg_quote( $name, '/' ) . '\\b/',
			'/\\bis-style-' . preg_quote( $name, '/' ) . '\\b/',
		);

		foreach ( $patterns as $pattern ) {
			if ( preg_match( $pattern, $class_name ) ) {
				$slugs_to_enqueue[] = $key;
				break;
			}
		}
	}

	/**
	 * Filters the include component keys detected for the current block.
	 *
	 * @param array $slugs_to_enqueue Component keys detected so far.
	 * @param array $block            The current block metadata.
	 * @param array $components       Available include components.
	 */
	$slugs_to_enqueue = apply_filters(
		'upa25_include_component_slugs',
		$slugs_to_enqueue,
		$block,
		$components
	);

	$slugs_to_enqueue = array_unique( $slugs_to_enqueue );
	foreach ( $slugs_to_enqueue as $key ) {
		upa25_enqueue_include_component( $key );
	}
}

/**
 * Enqueue a single include component's CSS/JS.
 *
 * @param string $key Component key (category/name format).
 */
function upa25_enqueue_include_component( string $key ): void {
	static $enqueued = array();

	if ( isset( $enqueued[ $key ] ) ) {
		return;
	}

	$components = upa25_get_includes_component_map();
	if ( ! isset( $components[ $key ] ) ) {
		return;
	}

	$enqueued[ $key ] = true;
	$component        = $components[ $key ];
	$component_dir    = $component['path'];
	$relative_base    = "build/includes/{$component['category']}/{$component['name']}";

	// Enqueue all CSS files
	foreach ( glob( $component_dir . '/*.css' ) as $css_file ) {
		if ( str_ends_with( $css_file, '-rtl.css' ) ) {
			continue;
		}

		$filename = basename( $css_file, '.css' );
		$handle   = "upa25-include-{$component['category']}-{$component['name']}-{$filename}";
		$relative = "{$relative_base}/{$filename}.css";

		upa25_enqueue_style_asset( $handle, $relative, ! is_admin() );
	}

	// Enqueue view.js on frontend, editor.js in editor
	$view_js = $component_dir . '/view.js';
	if ( file_exists( $view_js ) && ! is_admin() ) {
		$handle   = "upa25-include-{$component['category']}-{$component['name']}-view";
		$relative = "{$relative_base}/view.js";
		upa25_enqueue_script_asset( $handle, $relative );
	}

	$editor_js = $component_dir . '/editor.js';
	if ( file_exists( $editor_js ) && is_admin() ) {
		$handle   = "upa25-include-{$component['category']}-{$component['name']}-editor";
		$relative = "{$relative_base}/editor.js";
		upa25_enqueue_script_asset( $handle, $relative, false );
	}
}

/*
|--------------------------------------------------------------------------
| Asset Helper Functions
|--------------------------------------------------------------------------
*/

/**
 * Enqueue a style relative to the theme root.
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
 * @param bool   $defer         Whether to defer execution.
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
 * @return array{dependencies: array, version: string}
 */
function upa25_read_asset_file( string $file_path ): array {
	$asset_path = preg_replace( '/\.(css|js)$/', '.asset.php', $file_path );
	if ( $asset_path && file_exists( $asset_path ) ) {
		$data = require $asset_path;
		if ( is_array( $data ) ) {
			return array(
				'dependencies' => $data['dependencies'] ?? array(),
				'version'      => $data['version'] ?? (string) filemtime( $file_path ),
			);
		}
	}

	return array(
		'dependencies' => array(),
		'version'      => (string) filemtime( $file_path ),
	);
}

/**
 * Build a consistent handle for block style variations.
 *
 * @param string $block_name Block name (e.g., core/paragraph).
 * @param string $style_slug Style slug (e.g., indicator).
 * @return string
 */
function upa25_build_block_style_handle( string $block_name, string $style_slug ): string {
	return 'upa25-block-style-' . str_replace( '/', '-', $block_name ) . '-' . $style_slug;
}
