<?php
/**
 * upa25 Dashboard Widget
 *
 * @package upa25
 * @since 0.1.0
 * @version 1.2.0
 */

add_action('wp_dashboard_setup', 'upa25_dashboard_widgets');
function upa25_dashboard_widgets()
{
	wp_add_dashboard_widget('upa25_help_widget', 'Theme Support & Server Info', 'upa25_dashboard_help');
}

function upa25_dashboard_help()
{
	$theme = wp_get_theme();

	// Check if screenshot.png or screenshot.jpg exists in the theme folder
	$screenshot_path_png = get_template_directory() . '/screenshot.png';
	$screenshot_path_jpg = get_template_directory() . '/screenshot.jpg';

	if ( file_exists( $screenshot_path_png ) ) {
		$screenshot_url = get_template_directory_uri() . '/screenshot.png';
	} elseif ( file_exists( $screenshot_path_jpg ) ) {
		$screenshot_url = get_template_directory_uri() . '/screenshot.jpg';
	} else {
		$screenshot_url = ''; // No screenshot found
	}

	// Server Information
	$php_version      = phpversion();
	$mysql_version    = $GLOBALS['wpdb']->db_version();
	$server_software  = isset($_SERVER['SERVER_SOFTWARE']) ? $_SERVER['SERVER_SOFTWARE'] : 'Unknown';
	$host             = gethostname() ? gethostname() : 'Unknown Host';
	$max_upload_size  = size_format(wp_max_upload_size());

	// For WP memory limit, we can fall back to WP_MEMORY_LIMIT if ini_get('memory_limit') is empty.
	$memory_limit_ini = ini_get('memory_limit');
	$wp_memory_limit  = $memory_limit_ini ? $memory_limit_ini : (defined('WP_MEMORY_LIMIT') ? WP_MEMORY_LIMIT : 'Not defined');

	echo '
	<div style="margin-bottom: 10px;">
		<div style="border: 1px solid #ddd;">';

	if ( ! empty( $screenshot_url ) ) {
		echo '
			<img src="' . esc_url( $screenshot_url ) . '" alt="Theme Screenshot" style="max-width: 100%; height: auto; display: block; margin-top: 10px;">';
	}

	echo '
			<div style="padding: 10px; background: #f9f9f9; border-top: 1px solid #ddd;">
				<strong>' . esc_html( $theme->get("Name") ) . '</strong>
				<span style="font-size: 0.75em">' . esc_html( $theme->get("Version") ) . '</span>
				<br />
			</div>
		</div>
	</div>

	<h2 style="margin-bottom: 10px; font-size: 20px; border-bottom: 1px solid #ddd; padding-bottom: 5px;">Theme Information</h2>

	<div style="margin-bottom: 20px;">
		<p>' . esc_html( $theme->get("Description") ) . '</p>
		<strong>Theme Name:</strong> ' . esc_html( $theme->get("Name") ) . '<br>
		<strong>Author:</strong> <a href="' . esc_url( $theme->get("AuthorURI") ) . '">' . esc_html( $theme->get("Author") ) . '</a><br>
		<strong>Author URI:</strong> <a href="' . esc_url( $theme->get("AuthorURI") ) . '">' . esc_html( $theme->get("AuthorURI") ) . '</a><br>
		<strong>Theme URI:</strong> <a href="' . esc_url( $theme->get("ThemeURI") ) . '">' . esc_html( $theme->get("ThemeURI") ) . '</a><br>
		<strong>Version:</strong> ' . esc_html( $theme->get("Version") ) . '<br />
		<strong>Text Domain:</strong> ' . esc_html( $theme->get("TextDomain") ) . '<br />
	</div>

	<h2 style="margin-bottom: 10px; font-size: 20px; border-bottom: 1px solid #ddd; padding-bottom: 5px;">Server Information</h2>
	<p style="margin-bottom: 10px;">
		<strong>PHP Version:</strong> ' . esc_html( $php_version ) . '<br />
		<strong>MySQL Version:</strong> ' . esc_html( $mysql_version ) . '<br />
		<strong>Server Software:</strong> ' . esc_html( $server_software ) . '<br />
		<strong>Host:</strong> ' . esc_html( $host ) . '<br />
		<strong>WordPress Version:</strong> ' . esc_html( get_bloginfo("version") ) . '<br />
		<strong>WP Memory Limit:</strong> ' . esc_html( $wp_memory_limit ) . '<br />
		<strong>Max Upload Size:</strong> ' . esc_html( $max_upload_size ) . '<br />
	</p>
	';
}
