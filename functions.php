<?php

/**
 * upa25 functions and definitions
 *
 * @package upa25
 */


// Setup
require get_template_directory() . '/inc/setup.php';

// Woocommerce Setup
require get_template_directory() . '/inc/woo-remove-patterns.php';

// Enqueue files
require get_template_directory() . '/inc/enqueuing.php';

// Block  Variations
require get_template_directory() . '/inc/block-variations.php';

// Block Style Variations
require get_template_directory() . '/inc/block-styles.php';

// Patterns Setup
require get_template_directory() . '/inc/block-patterns.php';

// Dashboard Widget
require get_template_directory() . '/inc/dashboard-widget.php';







/**
 * Development tools
 */

// Remove default CSS variables
// require get_template_directory() . '/inc/dev_remove-defaults.php';

// Purge theme cache
require get_template_directory() . '/inc/dev_purge-themes-cache.php';

// Helpers
require get_template_directory() . '/inc/dev_helpers.php';
