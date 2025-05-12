<?php

/**
 * upa25 functions and definitions
 *
 * @package upa25
 * @since 0.1
 */


// Setup
require get_template_directory() . '/inc/setup.php';

// Patterns Setup
require get_template_directory() . '/inc/block-patterns.php';

// Enqueue files
require get_template_directory() . '/inc/enqueuing.php';

// Dashboard Widget
require get_template_directory() . '/inc/dashboard-widget.php';

/**
 * Development only!
 */
// Remove default CSS variables
// require get_template_directory() . '/inc/dev_remove-defaults.php';
