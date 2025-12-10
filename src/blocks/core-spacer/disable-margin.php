<?php
/**
 * Disable margin controls for the core/spacer block.
 *
 * @package upa25
 * @version 0.1.0
 * @since hxi25 0.1.0
 */

/**
 * Disable margin controls for the core/spacer block.
 *
 * @param array|WP_Block_Type $args Array or object of arguments for registering a block type.
 * @param string              $name Block type name including namespace.
 * @return array|WP_Block_Type
 */
function hxi25_disable_spacer_margin( $args, $name ) {
    if ( 'core/spacer' !== $name ) {
        return $args;
    }

    // Disable margin support for spacer blocks.
    if ( isset( $args['supports']['spacing']['margin'] ) ) {
        $args['supports']['spacing']['margin'] = false;
    } elseif ( is_array( $args ) ) {
        $args['supports']['spacing']['margin'] = false;
    }

    return $args;
}
add_filter( 'register_block_type_args', 'hxi25_disable_spacer_margin', 10, 2 );
