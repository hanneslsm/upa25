<?php
/**
 * Harden guest checkout against simple bot submissions.
 *
 * @package upa25
 */

defined( 'ABSPATH' ) || exit;

const UPA25_WOO_CHECKOUT_HONEYPOT_FIELD = 'upa25_checkout_company_code';
const UPA25_WOO_CHECKOUT_TIME_FIELD     = 'upa25_checkout_rendered_at';
const UPA25_WOO_CHECKOUT_TOKEN_FIELD    = 'upa25_checkout_render_token';
const UPA25_WOO_CHECKOUT_MIN_AGE        = 2;

add_action( 'woocommerce_review_order_before_submit', 'upa25_render_woo_guest_checkout_honeypot', 5 );
add_action( 'woocommerce_after_checkout_validation', 'upa25_validate_woo_guest_checkout_honeypot', 5, 2 );

/**
 * Render hidden anti-spam fields for guest checkout only.
 *
 * @return void
 */
function upa25_render_woo_guest_checkout_honeypot(): void {
	if ( is_user_logged_in() || is_checkout_pay_page() ) {
		return;
	}

	$rendered_at = (string) time();
	$token       = upa25_get_woo_checkout_timing_token( $rendered_at );
	?>
	<div class="upa25-woo-checkout-honeypot" aria-hidden="true" style="position:absolute;left:-10000px;top:auto;width:1px;height:1px;overflow:hidden;">
		<label>
			<?php esc_html_e( 'Leave this field empty', 'upa25' ); ?>
			<input type="text" name="<?php echo esc_attr( UPA25_WOO_CHECKOUT_HONEYPOT_FIELD ); ?>" value="" tabindex="-1" autocomplete="off" data-lpignore="true">
		</label>
	</div>
	<input type="hidden" name="<?php echo esc_attr( UPA25_WOO_CHECKOUT_TIME_FIELD ); ?>" value="<?php echo esc_attr( $rendered_at ); ?>">
	<input type="hidden" name="<?php echo esc_attr( UPA25_WOO_CHECKOUT_TOKEN_FIELD ); ?>" value="<?php echo esc_attr( $token ); ?>">
	<?php
}

/**
 * Validate the guest checkout anti-spam fields.
 *
 * @param array    $data   Posted checkout data.
 * @param WP_Error $errors Checkout validation errors.
 * @return void
 */
function upa25_validate_woo_guest_checkout_honeypot( array $data, WP_Error $errors ): void {
	if ( is_user_logged_in() || is_checkout_pay_page() ) {
		return;
	}

	$honeypot_value = isset( $data[ UPA25_WOO_CHECKOUT_HONEYPOT_FIELD ] )
		? trim( (string) $data[ UPA25_WOO_CHECKOUT_HONEYPOT_FIELD ] )
		: '';

	if ( '' !== $honeypot_value ) {
		$errors->add( 'upa25_checkout_honeypot', esc_html__( 'We could not process your checkout. Please try again.', 'upa25' ) );
		return;
	}

	$rendered_at = isset( $data[ UPA25_WOO_CHECKOUT_TIME_FIELD ] ) ? absint( $data[ UPA25_WOO_CHECKOUT_TIME_FIELD ] ) : 0;
	$token       = isset( $data[ UPA25_WOO_CHECKOUT_TOKEN_FIELD ] ) ? sanitize_text_field( (string) $data[ UPA25_WOO_CHECKOUT_TOKEN_FIELD ] ) : '';

	if ( $rendered_at <= 0 || '' === $token ) {
		$errors->add( 'upa25_checkout_refresh', esc_html__( 'Please refresh the checkout page and try again.', 'upa25' ) );
		return;
	}

	$expected_token = upa25_get_woo_checkout_timing_token( (string) $rendered_at );

	if ( ! hash_equals( $expected_token, $token ) ) {
		$errors->add( 'upa25_checkout_refresh', esc_html__( 'Please refresh the checkout page and try again.', 'upa25' ) );
		return;
	}

	if ( ( time() - $rendered_at ) < UPA25_WOO_CHECKOUT_MIN_AGE ) {
		$errors->add( 'upa25_checkout_too_fast', esc_html__( 'Please wait a moment and try again.', 'upa25' ) );
	}
}

/**
 * Create a signed token for the checkout render timestamp.
 *
 * @param string $rendered_at Render timestamp.
 * @return string
 */
function upa25_get_woo_checkout_timing_token( string $rendered_at ): string {
	return wp_hash( $rendered_at . '|' . UPA25_WOO_CHECKOUT_TIME_FIELD );
}
