<?php
/**
 * Disable WooCommerce marketing features
 *
 * @package upa25
 * @version 1.0.0
 * @link https://purothemes.com/remove-woocommerce-marketing-hub/
 */

/**
 * Remove the marketing feature from WooCommerce admin.
 *
 * @param array $features List of enabled features.
 * @return array
 */
// add_filter( 'woocommerce_admin_features', function ( $features ) {
// 	$marketing = array_search( 'marketing', $features, true );
// 	if ( false !== $marketing ) {
// 		unset( $features[ $marketing ] );
// 	}
// 	return $features;
// } );
