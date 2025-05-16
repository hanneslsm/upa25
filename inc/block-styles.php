<?php

/**
 * Block Style Variations
 * @link https://developer.wordpress.org/themes/features/block-style-variations/
 *
 * @package upa25
 */


add_action( 'init', function() {
	register_block_style_variations();
} );

function register_block_style_variations() {
	$block_styles = array(
		array(
			'block'  => 'core/button',
			'styles' => array(
				array( 'name' => 'brand', 'label' => __( 'Brand', 'upa25' ) ),
				array( 'name' => 'base',  'label' => __( 'Base',  'upa25' ) ),
			),
		),
		array(
			'block'  => 'core/details',
			'styles' => array(
				array( 'name' => 'chevron', 'label' => __( 'Chevron', 'upa25' ) ),
			),
		),
		array(
			'block'  => 'core/gallery',
			'styles' => array(
				array( 'name' => 'scale-effect', 'label' => __( 'Scale Effect', 'upa25' ) ),
			),
		),
		array(
			'block'  => 'core/cover',
			'styles' => array(
				array( 'name' => 'blurred', 'label' => __( 'Blurred', 'upa25' ) ),
				array( 'name' => 'cards',   'label' => __( 'Cards',   'upa25' ) ),
			),
		),
		array(
			'block'  => 'core/image',
			'styles' => array(
				array( 'name' => 'picture-frame', 'label' => __( 'Picture Frame', 'upa25' ) ),
			),
		),
		array(
			'block'  => 'core/list',
			'styles' => array(
				array( 'name' => 'checkmark',     'label' => __( 'Checkmark',     'upa25' ) ),
				array( 'name' => 'crossmark',     'label' => __( 'Crossmark',     'upa25' ) ),
				array( 'name' => 'crossmark-2',   'label' => __( 'Crossmark 2 Red', 'upa25' ) ),
				array( 'name' => 'checkmark-2',   'label' => __( 'Checkmark 2 Green', 'upa25' ) ),
			),
		),
		array(
			'block'  => 'core/paragraph',
			'styles' => array(
				array( 'name' => 'indicator', 'label' => __( 'Indicator', 'upa25' ) ),
				array( 'name' => 'overline',  'label' => __( 'Overline',  'upa25' ) ),
				array( 'name' => 'checkmark', 'label' => __( 'Checkmark', 'upa25' ) ),
			),
		),
	);

	foreach ( $block_styles as $variation ) {
		$block_name = $variation['block'];
		foreach ( $variation['styles'] as $style ) {
			register_block_style( $block_name, array(
				'name'  => $style['name'],
				'label' => $style['label'],
			) );
		}
	}
}
