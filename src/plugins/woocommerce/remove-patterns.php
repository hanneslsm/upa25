<?php
/**
 * Remove all WooCommerce patterns
 *
 * @package upa25
 * @version 1.0.0
 * @link inspired by https://mariecomet.fr/
 */

/**
 * Unregister WooCommerce block patterns.
 *
 * Runs on init with early priority (before WooCommerce registers patterns at priority 10).
 */
add_action( 'init', function () {
	if (
		class_exists( 'Automattic\\WooCommerce\\Blocks\\Package' ) &&
		class_exists( 'Automattic\\WooCommerce\\Blocks\\BlockPatterns' )
	) {
		remove_action(
			'init',
			array(
				Automattic\WooCommerce\Blocks\Package::container()->get( Automattic\WooCommerce\Blocks\BlockPatterns::class ),
				'register_block_patterns',
			)
		);
	}
}, 1 );
