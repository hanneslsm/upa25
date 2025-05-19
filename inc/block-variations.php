<?php

/**
 * Block Variations Setup
 *
 * @package upa25
 * @link https://developer.wordpress.org/block-editor/reference-guides/block-api/block-variations/
 */


function upa25_spacer_default_variation( $variations, $block_type ) {
    // Only target the core/spacer block.
    if ( 'core/spacer' !== $block_type->name ) {
        return $variations;
    }

    // Define the new variation.
    $variations[] = array(
        'name'       => 'spacer-default',
        'title'      => __( 'Spacer', 'upa25' ),
        'description'=> __( 'Overwrite default spacer height', 'upa25' ),
        'scope'      => array( 'inserter' ),
        'isDefault'  => true,
        'attributes' => array(
            'height' => 'var:preset|spacing|40', // Sets default height.
        ),
    );

    return $variations;
}
add_filter( 'get_block_type_variations', 'upa25_spacer_default_variation', 10, 2 );
