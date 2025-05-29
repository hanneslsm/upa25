<?php

/**
 * Patterns Setup
 *
 * @package upa25
 */


/**
 * Remove core patterns.
 * @link https://developer.wordpress.org/themes/patterns/registering-patterns/#removing-core-patterns
 */
add_action('after_setup_theme', 'upa25_remove_core_patterns');

function upa25_remove_core_patterns()
{
	remove_theme_support('core-block-patterns');
}

/**
 * Disable remote patterns
 * @link https://developer.wordpress.org/themes/patterns/registering-patterns/#disabling-remote-patterns
 */
add_filter('should_load_remote_block_patterns', '__return_false');


/**
 * Register custom pattern categories
 * @link https://developer.wordpress.org/themes/patterns/registering-patterns/#registering-a-pattern-category
 */

add_action('init', 'upa25_register_pattern_categories', 1);
// add_action( 'after_setup_theme', 'upa25_register_pattern_categories', 5 );

function upa25_register_pattern_categories()
{
	register_block_pattern_category('upa25/components', [
		'label'       => __('Components', 'upa25'),
		'description' => __('Single components for specific use.', 'upa25'),
	]);
	register_block_pattern_category('upa25/content', [
		'label'       => __('Content', 'upa25'),
		'description' => __('Generic content layouts.', 'upa25'),
	]);
	register_block_pattern_category('upa25/cards', [
		'label'       => __('Cards', 'upa25'),
		'description' => __('Generic card layouts.', 'upa25'),
	]);
	register_block_pattern_category('upa25/faq', [
		'label'       => __('FAQ', 'upa25'),
		'description' => __('Layout for the FAQ Section.', 'upa25'),
	]);
		register_block_pattern_category('upa25/columns', [
		'label'       => __('Columns', 'upa25'),
		'description' => __('Layout with columns.', 'upa25'),
	]);
			register_block_pattern_category('upa25/heros', [
		'label'       => __('Heros', 'upa25'),
		'description' => __('Layouts for the homepage above the fold.', 'upa25'),
	]);
}
