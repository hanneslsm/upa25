<?php
/**
 * Disable WooCommerce order notes field during checkout
 *
 * @package upa25
 * @version 1.0.0
 */

/**
 * Disable the order notes field on the classic checkout page.
 */
add_filter( 'woocommerce_enable_order_notes_field', '__return_false' );

/**
 * Remove order notes from checkout fields (classic checkout fallback).
 *
 * @param array $fields Checkout fields.
 * @return array
 */
add_filter( 'woocommerce_checkout_fields', function ( $fields ) {
	unset( $fields['order']['order_comments'] );
	return $fields;
} );

/**
 * Hide order notes in block-based checkout via CSS.
 *
 * The block checkout doesn't use PHP filters - we hide it with CSS.
 */
add_action( 'wp_enqueue_scripts', function () {
	if ( is_checkout() ) {
		wp_add_inline_style( 'wc-blocks-style', '.wc-block-checkout__add-note { display: none !important; }' );
	}
} );
