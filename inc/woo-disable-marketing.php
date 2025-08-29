<?php
/**
 * Disable WooCommerce marketing features
 *
 * @package upa25
 * @version 1.0.0
 * @link https://purothemes.com/remove-woocommerce-marketing-hub/
 */
	add_filter( 'woocommerce_admin_features', 'disable_features' );
    function disable_features( $features ) {
        $marketing = array_search( 'marketing', $features );
        if ( $marketing !== false ) {
            unset( $features[$marketing] );
        }
        return $features;
    }
