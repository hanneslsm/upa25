<?php

/**
 * Setup
 *
 * @package upa25
 * @since 0.1.0
 * @link https://developer.wordpress.org/themes/block-themes/block-theme-setup/
 */



if (!function_exists('upa25_setup')) :
	function upa25_setup()
	{
		// Make theme available for translation.
		load_theme_textdomain('upa25', get_template_directory() . '/languages');

		// Enqueue editor styles.
		add_editor_style('assets/css/editor-style.css');
	}
endif; // upa25_setup
add_action('after_setup_theme', 'upa25_setup');
