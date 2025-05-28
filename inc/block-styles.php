<?php
/**
 * Register block-style variations and link them to the handles
 * created in enqueuing.php
 */

add_action( 'init', 'upa25_register_block_style_variations', 10 );
function upa25_register_block_style_variations() {

	$block_styles = [
		'core/button' => [
			[ 'name' => 'ghost', 'label' => __( 'Ghost', 'upa25' ) ],
		],
		'core/details' => [
			[ 'name' => 'chevron', 'label' => __( 'Chevron', 'upa25' ) ],
		],
		'core/gallery' => [
			[ 'name' => 'scale-effect', 'label' => __( 'Scale Effect', 'upa25' ) ],
		],
		'core/cover' => [
			[ 'name' => 'blurred', 'label' => __( 'Blurred', 'upa25' ) ],
			[ 'name' => 'card--interactive',   'label' => __( 'Card (Interactive)',   'upa25' ) ],
		],
		'core/image' => [
			[ 'name' => 'picture-frame', 'label' => __( 'Picture Frame', 'upa25' ) ],
		],
		'core/list' => [
			[ 'name' => 'checkmark',   'label' => __( 'Checkmark',         'upa25' ) ],
			[ 'name' => 'crossmark',   'label' => __( 'Crossmark',         'upa25' ) ],
			[ 'name' => 'crossmark-2', 'label' => __( 'Crossmark 2 Red',   'upa25' ) ],
			[ 'name' => 'checkmark-2', 'label' => __( 'Checkmark 2 Green', 'upa25' ) ],
		],
		'core/paragraph' => [
			[ 'name' => 'indicator', 'label' => __( 'Indicator', 'upa25' ) ],
			[ 'name' => 'overline',  'label' => __( 'Overline',  'upa25' ) ],
			[ 'name' => 'checkmark', 'label' => __( 'Checkmark', 'upa25' ) ],
		],
	];

	foreach ( $block_styles as $block => $styles ) {
		foreach ( $styles as $style ) {
			register_block_style( $block, [
				'name'         => $style['name'],
				'label'        => $style['label'],
				'style_handle' => 'upa25-block-style-' . str_replace( '/', '-', $block ) . '-' . $style['name'],
			] );
		}
	}
}
