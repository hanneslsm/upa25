<?php

defined( 'ABSPATH' ) || exit;

const UPA25_ASE_FORM_HONEYPOT_FIELD = 'upa25_contact_reference';
const UPA25_ASE_FORM_TIME_FIELD     = 'upa25_form_rendered_at';
const UPA25_ASE_FORM_TOKEN_FIELD    = 'upa25_form_render_token';
const UPA25_ASE_FORM_MIN_AGE        = 2;

add_filter( 'do_shortcode_tag', 'upa25_ase_formbuilder_inject_honeypot_shortcode', 20, 4 );
add_filter( 'render_block', 'upa25_ase_formbuilder_inject_honeypot_block', 20, 2 );
add_action( 'wp_ajax_formbuilder_process_entry', 'upa25_ase_formbuilder_reject_honeypot_spam', 1 );
add_action( 'wp_ajax_nopriv_formbuilder_process_entry', 'upa25_ase_formbuilder_reject_honeypot_spam', 1 );

/**
 * Inject the honeypot into ASE Form Builder shortcodes.
 *
 * @param string $output Shortcode output.
 * @param string $tag    Shortcode tag.
 * @return string
 */
function upa25_ase_formbuilder_inject_honeypot_shortcode( string $output, string $tag ): string {
	if ( 'formbuilder' !== $tag ) {
		return $output;
	}

	return upa25_ase_formbuilder_inject_honeypot_markup( $output );
}

/**
 * Inject the honeypot into the ASE Form Builder block output.
 *
 * @param string $block_content Rendered block content.
 * @param array  $block         Parsed block data.
 * @return string
 */
function upa25_ase_formbuilder_inject_honeypot_block( string $block_content, array $block ): string {
	if ( empty( $block['blockName'] ) || 'form-builder/form-selector' !== $block['blockName'] ) {
		return $block_content;
	}

	return upa25_ase_formbuilder_inject_honeypot_markup( $block_content );
}

/**
 * Insert honeypot markup just ahead of the submit button wrapper.
 *
 * @param string $markup Form markup.
 * @return string
 */
function upa25_ase_formbuilder_inject_honeypot_markup( string $markup ): string {
	if (
		false === strpos( $markup, 'formbuilder-form' ) ||
		false === strpos( $markup, 'fb-submit-wrap' ) ||
		false !== strpos( $markup, UPA25_ASE_FORM_HONEYPOT_FIELD )
	) {
		return $markup;
	}

	$honeypot = upa25_ase_formbuilder_get_honeypot_markup();
	$updated  = preg_replace( '/<div class="fb-submit-wrap\b/', $honeypot . '<div class="fb-submit-wrap', $markup, 1 );

	return is_string( $updated ) ? $updated : $markup;
}

/**
 * Honeypot field markup hidden from people but visible to simplistic bots.
 *
 * @return string
 */
function upa25_ase_formbuilder_get_honeypot_markup(): string {
	$rendered_at = (string) time();
	$token       = upa25_ase_formbuilder_get_timing_token( $rendered_at );

	return sprintf(
		'<div class="upa25-fb-honeypot" aria-hidden="true" style="position:absolute;left:-10000px;top:auto;width:1px;height:1px;overflow:hidden;"><label>%1$s<input type="text" name="%2$s" value="" tabindex="-1" autocomplete="off" data-lpignore="true"></label></div><input type="hidden" name="%3$s" value="%4$s"><input type="hidden" name="%5$s" value="%6$s">',
		esc_html__( 'Leave this field empty', 'upa25' ),
		esc_attr( UPA25_ASE_FORM_HONEYPOT_FIELD ),
		esc_attr( UPA25_ASE_FORM_TIME_FIELD ),
		esc_attr( $rendered_at ),
		esc_attr( UPA25_ASE_FORM_TOKEN_FIELD ),
		esc_attr( $token )
	);
}

/**
 * Create a signed token for the render timestamp.
 *
 * @param string $rendered_at Render timestamp.
 * @return string
 */
function upa25_ase_formbuilder_get_timing_token( string $rendered_at ): string {
	return wp_hash( $rendered_at . '|' . UPA25_ASE_FORM_TIME_FIELD );
}

/**
 * Stop spam submissions before ASE creates entries or sends email.
 *
 * Allows requests without the honeypot field so cached older markup does not
 * break valid submissions immediately after deployment.
 *
 * @return void
 */
function upa25_ase_formbuilder_reject_honeypot_spam(): void {
	if ( empty( $_POST['data'] ) ) {
		return;
	}

	$data = wp_unslash( $_POST['data'] );

	if ( ! is_string( $data ) || '' === $data ) {
		return;
	}

	parse_str( htmlspecialchars_decode( $data ), $parsed_data );

	if ( ! isset( $parsed_data[ UPA25_ASE_FORM_HONEYPOT_FIELD ] ) ) {
		return;
	}

	$honeypot_value = trim( (string) $parsed_data[ UPA25_ASE_FORM_HONEYPOT_FIELD ] );

	if ( '' === $honeypot_value ) {
		upa25_ase_formbuilder_validate_submission_timing( $parsed_data );
		return;
	}

	wp_send_json(
		array(
			'status'  => 'failed',
			'message' => esc_html__( 'Your message could not be sent. Please try again.', 'upa25' ),
		)
	);
}

/**
 * Reject forms that arrive too quickly or with an invalid timing token.
 *
 * @param array $parsed_data Parsed AJAX form payload.
 * @return void
 */
function upa25_ase_formbuilder_validate_submission_timing( array $parsed_data ): void {
	if ( empty( $parsed_data[ UPA25_ASE_FORM_TIME_FIELD ] ) || empty( $parsed_data[ UPA25_ASE_FORM_TOKEN_FIELD ] ) ) {
		wp_send_json(
			array(
				'status'  => 'failed',
				'message' => esc_html__( 'Your message could not be sent. Please refresh the page and try again.', 'upa25' ),
			)
		);
	}

	$rendered_at = absint( $parsed_data[ UPA25_ASE_FORM_TIME_FIELD ] );
	$token       = sanitize_text_field( (string) $parsed_data[ UPA25_ASE_FORM_TOKEN_FIELD ] );

	if ( $rendered_at <= 0 ) {
		wp_send_json(
			array(
				'status'  => 'failed',
				'message' => esc_html__( 'Your message could not be sent. Please refresh the page and try again.', 'upa25' ),
			)
		);
	}

	$expected_token = upa25_ase_formbuilder_get_timing_token( (string) $rendered_at );

	if ( ! hash_equals( $expected_token, $token ) ) {
		wp_send_json(
			array(
				'status'  => 'failed',
				'message' => esc_html__( 'Your message could not be sent. Please refresh the page and try again.', 'upa25' ),
			)
		);
	}

	$age = time() - $rendered_at;

	if ( $age < UPA25_ASE_FORM_MIN_AGE ) {
		wp_send_json(
			array(
				'status'  => 'failed',
				'message' => esc_html__( 'Your message could not be sent. Please refresh the page and try again.', 'upa25' ),
			)
		);
	}
}
