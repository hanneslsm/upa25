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
function upa25_register_block_styles() {
    $blocks_dir = get_theme_file_path( 'build/css/blocks/' );
    if ( is_dir( $blocks_dir ) ) {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator( $blocks_dir, FilesystemIterator::SKIP_DOTS )
        );
        foreach ( $iterator as $file ) {
            if ( $file->isFile() && 'css' === $file->getExtension() ) {
                // Pfad relativ zum blocks-Ordner
                $relative = ltrim( str_replace( $blocks_dir, '', $file->getPathname() ), DIRECTORY_SEPARATOR );
                // Verzeichnis-Name (oder "."), und Dateiname ohne .css
                $subfolder = dirname( $relative );
                $name      = basename( $relative, '.css' );
                $block_name = preg_replace( '/-/', '/', $name, 1 );   // "core/cover"
                // Handle: wenn Root (.) dann ohne Ordner, sonst mit
                $handle = 'upa25-block-' . ( '.' === $subfolder ? $name : "{$subfolder}-{$name}" );
                $src    = get_theme_file_uri( "build/css/blocks/{$relative}" );
                wp_register_style( $handle, $src );
                wp_enqueue_block_style( $block_name, [ 'handle' => $handle ] );
            }
        }
    }

    $styles_root = get_theme_file_path( 'build/css/styles/blocks/' );
    if ( is_dir( $styles_root ) ) {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator( $styles_root, FilesystemIterator::SKIP_DOTS )
        );
        foreach ( $iterator as $file ) {
            if ( $file->isFile() && 'css' === $file->getExtension() ) {
                $relative   = ltrim( str_replace( $styles_root, '', $file->getPathname() ), DIRECTORY_SEPARATOR );
                // Hier ist das erste Segment der Style-Name
                $parts      = explode( DIRECTORY_SEPARATOR, $relative );
                $style_name = array_shift( $parts );
                $filename   = implode( DIRECTORY_SEPARATOR, $parts );
                $name       = basename( $filename, '.css' );
                $block_name = preg_replace( '/-/', '/', $name, 1 );
                $handle     = "upa25-{$style_name}-{$name}";
                $src        = get_theme_file_uri( "build/css/styles/blocks/{$relative}" );
                wp_register_style( $handle, $src );
                register_block_style( $block_name, [
                    'name'         => $style_name,
                    'label'        => ucfirst( $style_name ),
                    'style_handle' => $handle,
                ] );
            }
        }
    }
}
add_action( 'init', 'upa25_register_block_styles' );
