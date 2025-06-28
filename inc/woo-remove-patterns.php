<?php
/**
 * Remove all WooCommerce patterns
 *
 * @package upa25
 * @version 1.0.0
 * @link inspired by https://mariecomet.fr/
 */

add_action( 'after_setup_theme', function() {
    if (
        class_exists('Automattic\\WooCommerce\\Blocks\\Package') &&
        class_exists('Automattic\\WooCommerce\\Blocks\\BlockPatterns')
    ) {
        remove_action(
            'init',
            [
                Automattic\WooCommerce\Blocks\Package::container()->get( Automattic\WooCommerce\Blocks\BlockPatterns::class ),
                'register_block_patterns'
            ]
        );
    }
}, 1 );
