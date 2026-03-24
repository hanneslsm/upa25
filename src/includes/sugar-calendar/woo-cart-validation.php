<?php
/**
 * Sugar Calendar + WooCommerce cart quantity validation.
 *
 * Prevents adding more tickets to the WooCommerce cart than are actually
 * remaining for a given Sugar Calendar event.
 *
 * @package upa25
 * @version 1.0.0
 */

/**
 * Return the configured WooCommerce ticket product ID.
 *
 * @return int Product ID, or 0 if not configured/available.
 */
function upa25_sc_get_ticket_product_id(): int {
	if ( function_exists( '\\Sugar_Calendar\\AddOn\\Ticketing\\Settings\\get_setting' ) ) {
		return absint( \Sugar_Calendar\AddOn\Ticketing\Settings\get_setting( 'woocommerce_ticket_product' ) );
	}

	return 0;
}

/**
 * Check whether a WooCommerce product ID matches the configured ticket product.
 *
 * @param int $product_id WooCommerce product ID.
 * @return bool
 */
function upa25_sc_is_ticket_product( int $product_id ): bool {
	$ticket_product_id = upa25_sc_get_ticket_product_id();

	return $ticket_product_id > 0 && $ticket_product_id === $product_id;
}

/**
 * Return the number of remaining tickets for an event, or -1 for unlimited.
 *
 * @param int $event_id Sugar Calendar event ID.
 * @return int Remaining count, or -1 for unlimited capacity.
 */
function upa25_sc_get_remaining( int $event_id ): int {
	if ( function_exists( '\\Sugar_Calendar\\AddOn\\Ticketing\\Common\\Functions\\get_available_tickets' ) ) {
		return (int) \Sugar_Calendar\AddOn\Ticketing\Common\Functions\get_available_tickets( $event_id );
	}

	if ( ! function_exists( 'get_event_meta' ) ) {
		return -1;
	}

	if ( ! get_event_meta( $event_id, 'ticket_limit_capacity', true ) ) {
		return -1; // Unlimited capacity.
	}

	return max( (int) get_event_meta( $event_id, 'ticket_quantity', true ), 0 );
}

/**
 * Sum the quantity of a specific event already present in the WooCommerce cart.
 *
 * @param int    $event_id    Sugar Calendar event ID.
 * @param string $exclude_key Cart item key to skip (used during update-cart checks).
 * @return int Total quantity for that event currently in the cart.
 */
function upa25_sc_cart_qty_for_event( int $event_id, string $exclude_key = '' ): int {
	$total = 0;

	if ( ! WC()->cart ) {
		return $total;
	}

	foreach ( WC()->cart->get_cart() as $key => $item ) {
		if ( $key === $exclude_key ) {
			continue;
		}

		$product_id = ! empty( $item['product_id'] ) ? absint( $item['product_id'] ) : 0;
		if ( ! upa25_sc_is_ticket_product( $product_id ) ) {
			continue;
		}

		if ( ! empty( $item['event_id'] ) && (int) $item['event_id'] === $event_id ) {
			$total += (int) $item['quantity'];
		}
	}

	return $total;
}

/**
 * Validate ticket quantity when a product is added to the cart.
 *
 * Fires before the item is actually inserted, so $remaining reflects
 * only completed/paid tickets — cart items are not yet counted by
 * Sugar Calendar. We guard by comparing (cart qty + new qty) vs remaining.
 *
 * @param bool $passed     Whether the item passed prior validation.
 * @param int  $product_id WooCommerce product ID.
 * @param int  $quantity   Quantity being added.
 * @return bool
 */
function upa25_sc_validate_add_to_cart( bool $passed, int $product_id, int $quantity ): bool {
	if ( ! $passed ) {
		return $passed;
	}

	if ( ! upa25_sc_is_ticket_product( $product_id ) ) {
		return $passed;
	}

	// Sugar Calendar passes event_id as a query/POST variable.
	$event_id = isset( $_REQUEST['event_id'] ) ? absint( $_REQUEST['event_id'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( ! $event_id ) {
		return $passed;
	}

	if ( ! function_exists( 'get_event_meta' ) ) {
		return $passed;
	}

	if ( ! get_event_meta( $event_id, 'tickets', true ) ) {
		return $passed;
	}

	$remaining = upa25_sc_get_remaining( $event_id );
	if ( $remaining < 0 ) {
		return $passed; // Unlimited — nothing to enforce.
	}

	$in_cart = upa25_sc_cart_qty_for_event( $event_id );

	if ( $in_cart + $quantity > $remaining ) {
		$can_add = $remaining - $in_cart;

		if ( $can_add <= 0 ) {
			wc_add_notice(
				esc_html__( 'Sorry, there are no more tickets available for this event.', 'upa25' ),
				'error'
			);
		} else {
			wc_add_notice(
				sprintf(
					/* translators: 1: total remaining, 2: quantity already in cart */
					esc_html__( 'Sorry, only %1$s ticket(s) remaining for this event. You already have %2$s in your cart.', 'upa25' ),
					number_format_i18n( $remaining ),
					number_format_i18n( $in_cart )
				),
				'error'
			);
		}

		return false;
	}

	return $passed;
}
add_filter( 'woocommerce_add_to_cart_validation', 'upa25_sc_validate_add_to_cart', 10, 3 );

/**
 * Validate ticket quantity when a cart item quantity is updated.
 *
 * @param bool   $passed        Whether the update passed prior validation.
 * @param string $cart_item_key The cart item key being updated.
 * @param array  $values        Cart item data.
 * @param int    $quantity      New quantity requested.
 * @return bool
 */
function upa25_sc_validate_update_cart( bool $passed, string $cart_item_key, array $values, int $quantity ): bool {
	if ( ! $passed ) {
		return $passed;
	}

	$product_id = ! empty( $values['product_id'] ) ? absint( $values['product_id'] ) : 0;
	if ( ! upa25_sc_is_ticket_product( $product_id ) ) {
		return $passed;
	}

	if ( empty( $values['event_id'] ) ) {
		return $passed;
	}

	$event_id = absint( $values['event_id'] );

	$remaining = upa25_sc_get_remaining( $event_id );
	if ( $remaining < 0 ) {
		return $passed; // Unlimited.
	}

	// Exclude the current item so we measure the rest of the cart for this event.
	$other_in_cart = upa25_sc_cart_qty_for_event( $event_id, $cart_item_key );

	if ( $other_in_cart + $quantity > $remaining ) {
		wc_add_notice(
			sprintf(
				/* translators: %s: number of tickets remaining */
				esc_html__( 'Sorry, only %s ticket(s) remaining for this event.', 'upa25' ),
				number_format_i18n( $remaining )
			),
			'error'
		);

		return false;
	}

	return $passed;
}
add_filter( 'woocommerce_update_cart_validation', 'upa25_sc_validate_update_cart', 10, 4 );

/**
 * Enforce event ticket limits directly in the cart as a safety net.
 *
 * Some cart UIs can bypass specific validation hooks. This guard runs during
 * cart total calculation and clamps quantities so cart state never exceeds
 * the event's actual remaining ticket count.
 *
 * @param WC_Cart $cart WooCommerce cart object.
 * @return void
 */
function upa25_sc_enforce_cart_capacity( $cart ): void {
	if ( is_admin() && ! wp_doing_ajax() ) {
		return;
	}

	if ( ! $cart instanceof WC_Cart ) {
		return;
	}

	static $is_adjusting = false;
	if ( $is_adjusting ) {
		return;
	}

	$is_adjusting = true;

	$event_running_qty = array();

	foreach ( $cart->get_cart() as $cart_item_key => $item ) {
		$product_id = ! empty( $item['product_id'] ) ? absint( $item['product_id'] ) : 0;
		if ( ! upa25_sc_is_ticket_product( $product_id ) ) {
			continue;
		}

		if ( empty( $item['event_id'] ) ) {
			continue;
		}

		$event_id = absint( $item['event_id'] );
		if ( ! $event_id ) {
			continue;
		}

		$remaining = upa25_sc_get_remaining( $event_id );
		if ( $remaining < 0 ) {
			continue; // Unlimited.
		}

		$already_allocated = isset( $event_running_qty[ $event_id ] ) ? (int) $event_running_qty[ $event_id ] : 0;
		$current_qty       = (int) $item['quantity'];
		$max_for_item      = max( $remaining - $already_allocated, 0 );

		if ( $current_qty > $max_for_item ) {
			$cart->set_quantity( $cart_item_key, $max_for_item, false );

			if ( $max_for_item > 0 ) {
				$message = sprintf(
					/* translators: %s: number of tickets remaining */
					esc_html__( 'Cart quantity was adjusted. Only %s ticket(s) remain for this event.', 'upa25' ),
					number_format_i18n( $remaining )
				);

				if ( ! wc_has_notice( $message, 'notice' ) ) {
					wc_add_notice( $message, 'notice' );
				}
			} else {
				$message = esc_html__( 'This event is sold out. The ticket was removed from your cart.', 'upa25' );

				if ( ! wc_has_notice( $message, 'notice' ) ) {
					wc_add_notice( $message, 'notice' );
				}
			}

			$current_qty = $max_for_item;
		}

		$event_running_qty[ $event_id ] = $already_allocated + $current_qty;
	}

	$is_adjusting = false;
}
add_action( 'woocommerce_before_calculate_totals', 'upa25_sc_enforce_cart_capacity', 5 );
