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
	register_block_pattern_category('upa25/layout-a', [
		'label'       => __('Layout A', 'upa25'),
		'description' => __('Layouts with wide heading and content', 'upa25'),
	]);
	register_block_pattern_category('upa25/layout-b', [
		'label'       => __('Layout B (Two Colums)', 'upa25'),
		'description' => __('Two Column Layouts with heading and content aligned on top', 'upa25'),
	]);
	register_block_pattern_category('upa25/layout-c', [
		'label'       => __('Layout C (Two Colums)', 'upa25'),
		'description' => __('Two Column Layouts with heading and content center aligned', 'upa25'),
	]);
	register_block_pattern_category('upa25/components', [
		'label'       => __('Components', 'upa25'),
		'description' => __('Single components for specific use.', 'upa25'),
	]);
	register_block_pattern_category('upa25/cards', [
		'label'       => __('Cards', 'upa25'),
		'description' => __('Generic card layouts.', 'upa25'),
	]);
	register_block_pattern_category('upa25/faq', [
		'label'       => __('FAQ', 'upa25'),
		'description' => __('Layout for the FAQ Section.', 'upa25'),
	]);
	register_block_pattern_category('upa25/heros', [
		'label'       => __('Heros', 'upa25'),
		'description' => __('Layouts for the homepage above the fold.', 'upa25'),
	]);
}
