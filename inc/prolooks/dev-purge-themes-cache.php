<?php

/**
 * Remove default css. Use only for development!
 *
 * @package prolooks
 * @version 0.2.0
 * @link https://github.com/WordPress/gutenberg/issues/64173#issuecomment-2551782271
 */


// Purge theme cache by making this GET request: /wp-admin/?purge-theme-cache
add_action( 'init', 'prolooks_purge_themes_cache' );
add_action( 'admin_bar_menu', 'prolooks_admin_bar_purge_theme_cache_button', 200 );

function prolooks_purge_themes_cache() {
	if ( ! isset( $_GET['purge-theme-cache'] ) || ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$theme = wp_get_theme();
	$theme->delete_pattern_cache();

	if ( empty( $_SERVER['REQUEST_URI'] ) ) {
		return;
	}

	$redirect_path = remove_query_arg( 'purge-theme-cache', wp_unslash( $_SERVER['REQUEST_URI'] ) );

	wp_safe_redirect( $redirect_path );
	exit;
}

/**
 * Adds a button to the admin bar for purging the theme cache.
 *
 * @param WP_Admin_Bar $admin_bar WordPress admin bar instance.
 * @return void
 */
function prolooks_admin_bar_purge_theme_cache_button( $admin_bar ) {
	if ( ! is_admin_bar_showing() || ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '';

	if ( '' === $request_uri ) {
		$request_uri = '/';
	}

	$purge_request = remove_query_arg( 'purge-theme-cache', $request_uri );
	$purge_request = add_query_arg( 'purge-theme-cache', '1', $purge_request );

	$admin_bar->add_node(
		array(
			'id'     => 'prolooks-purge-theme-cache',
			'parent' => 'top-secondary',
			'title'  => esc_html__( 'Purge Theme Cache', 'prolooks' ),
			'href'   => esc_url_raw( $purge_request ),
			'meta'   => array(
				'title' => esc_attr__( 'Flush the pattern cache for the active theme', 'prolooks' ),
				'class' => 'prolooks-purge-theme-cache-button',
			),
		)
	);
}
