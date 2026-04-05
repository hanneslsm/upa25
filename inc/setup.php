<?php

/**
 * Setup
 *
 * @package upa25
 * @link https://developer.wordpress.org/themes/block-themes/block-theme-setup/
 */



if (!function_exists('upa25_setup')) :
	function upa25_setup()
	{
		// Make theme available for translation.
		load_theme_textdomain('upa25', get_template_directory() . '/languages');
	}
endif; // upa25_setup
add_action('after_setup_theme', 'upa25_setup');



/**
 * Remove the default password change notification email.
 *
 * Disables the email notification that WordPress sends to users when their password is changed.
 * This is typically used to reduce email notifications or when custom password change
 * notifications are handled by other means.
 *
 * @link https://developer.wordpress.org/plugins/hooks/actions/
 * @since 0.4.4
 */
if ( !function_exists( 'wp_password_change_notification' ) ) {
    function wp_password_change_notification() {}
}
