<?php

/**
 * upa25 functions and definitions
 *
 * @package upa25
 */

// Setup
require get_template_directory() . '/inc/setup.php';

// Enqueue files
require get_template_directory() . '/inc/enqueuing.php';

// Block  Variations
require get_template_directory() . '/inc/block-variations.php';

// Block Style Variations
require get_template_directory() . '/inc/block-styles.php';

// Patterns Setup
require get_template_directory() . '/inc/block-patterns.php';


/**
 * Woo
 */

// Woocommerce Setup
require get_template_directory() . '/inc/woo-remove-patterns.php';

// Woocommerce Disable Marketing
require get_template_directory() . '/inc/woo-disable-marketing.php';


/**
 * ProLooks tools
 */
// Remove emojis
require get_template_directory() . '/inc/prolooks/gpdr-remove-emojis.php';

// Dashboard Widget
require get_template_directory() . '/inc/prolooks/dashboard-widget.php';

// Remove default CSS variables
// require get_template_directory() . '/inc/prolooks/dev-remove-defaults.php';

// Purge theme cache
require get_template_directory() . '/inc/prolooks/dev-purge-themes-cache.php';
