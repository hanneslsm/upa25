<?php
/**
 * Sugar Calendar public tickets remaining details row.
 *
 * @package upa25
 * @version 1.1.0
 */

/**
 * Render a tickets-remaining row inside the Sugar Calendar event details area.
 *
 * For limited-capacity events a progress bar is shown behind the count.
 * For unlimited-capacity events the number of registered attendees is shown.
 *
 * @param object $event Sugar Calendar event object.
 * @return array<string,mixed>|null
 */
function upa25_sc_get_tickets_display_data( $event ): ?array {
	if ( ! is_object( $event ) || empty( $event->id ) ) {
		return null;
	}

	if ( ! function_exists( 'get_event_meta' ) ) {
		return null;
	}

	$event_id = (int) $event->id;

	// Only render for events that have ticketing enabled.
	$tickets_enabled = (bool) get_event_meta( $event_id, 'tickets', true );
	if ( ! $tickets_enabled ) {
		return null;
	}

	$total_capacity = (int) get_event_meta( $event_id, 'ticket_quantity', true );
	$remaining      = -1; // Default: unlimited.

	if ( function_exists( '\\Sugar_Calendar\\AddOn\\Ticketing\\Common\\Functions\\get_available_tickets' ) ) {
		$remaining = (int) \Sugar_Calendar\AddOn\Ticketing\Common\Functions\get_available_tickets( $event_id );
	} elseif ( get_event_meta( $event_id, 'ticket_limit_capacity', true ) ) {
		$remaining = max( $total_capacity, 0 );
	}

	// Build display string and progress bar data.
	$show_bar = false;
	$pct_sold = 0;

	if ( 0 === $remaining ) {
		$value    = __( 'Sold Out', 'upa25' );
		$show_bar = $total_capacity > 0;
		$pct_sold = 100;
	} elseif ( $remaining > 0 && $total_capacity > 0 ) {
		/* translators: %s: Number of tickets remaining. */
		$value    = sprintf( __( '%s remaining', 'upa25' ), number_format_i18n( $remaining ) );
		$show_bar = true;
		$purchased = $total_capacity - $remaining;
		$pct_sold  = (int) round( ( $purchased / $total_capacity ) * 100 );
	} else {
		// Unlimited capacity — show number of attendees registered.
		$purchased = 0;
		if ( function_exists( '\\Sugar_Calendar\\AddOn\\Ticketing\\Common\\Functions\\count_tickets' ) ) {
			$purchased = (int) \Sugar_Calendar\AddOn\Ticketing\Common\Functions\count_tickets( [ 'event_id' => $event_id ] );
		}
		$value = $purchased > 0
			/* translators: %s: Number of attendees registered. */
			? sprintf( __( '%s registered', 'upa25' ), number_format_i18n( $purchased ) )
			: __( 'Tickets Available', 'upa25' );
	}

	return array(
		'value'     => $value,
		'show_bar'  => $show_bar,
		'pct_sold'  => $pct_sold,
		'remaining' => $remaining,
	);
}

/**
 * Render a tickets-remaining row inside the Sugar Calendar event details area.
 *
 * @param object $event Sugar Calendar event object.
 * @return void
 */
function upa25_render_sugar_calendar_tickets_remaining( $event ): void {
	$data = upa25_sc_get_tickets_display_data( $event );

	if ( empty( $data ) ) {
		return;
	}
	?>
	<div class="sc-frontend-single-event__details__tickets-remaining sc-frontend-single-event__details-row">
		<div class="sc-frontend-single-event__details__label">
			<?php esc_html_e( 'Tickets:', 'upa25' ); ?>
		</div>
		<div class="sc-frontend-single-event__details__val">
			<span class="upa25-tickets-value"><?php echo esc_html( $data['value'] ); ?></span>
			<?php if ( ! empty( $data['show_bar'] ) ) : ?>
			<div
				class="upa25-tickets-bar"
				role="progressbar"
				aria-label="<?php esc_attr_e( 'Ticket availability', 'upa25' ); ?>"
				aria-valuenow="<?php echo esc_attr( 100 - (int) $data['pct_sold'] ); ?>"
				aria-valuemin="0"
				aria-valuemax="100"
			>
				<div class="upa25-tickets-bar__fill" style="--upa25-tickets-pct: <?php echo esc_attr( (int) $data['pct_sold'] ); ?>%"></div>
			</div>
			<?php endif; ?>
		</div>
	</div>
	<?php
}
add_action( 'sugar_calendar_frontend_event_details', 'upa25_render_sugar_calendar_tickets_remaining', 55 );

/**
 * Add tickets remaining info to Sugar Calendar Event List block list view.
 *
 * @param string $output      Current date/time output.
 * @param object $event       Sugar Calendar event object.
 * @param string $event_date  Event date string.
 * @param string $time_format Time format.
 * @return string
 */
function upa25_add_tickets_to_sugar_calendar_list_view( string $output, $event, string $event_date, string $time_format ): string { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed
	$data = upa25_sc_get_tickets_display_data( $event );

	if ( empty( $data ) ) {
		return $output;
	}

	$list_markup = '<span class="upa25-sc-list-tickets">';
	$list_markup .= '<span class="upa25-sc-list-tickets__label">' . esc_html__( 'Tickets:', 'upa25' ) . '</span>';
	$list_markup .= '<span class="upa25-tickets-value">' . esc_html( $data['value'] ) . '</span>';

	if ( ! empty( $data['show_bar'] ) ) {
		$pct_bucket = (int) ( round( (int) $data['pct_sold'] / 5 ) * 5 );
		$pct_bucket = min( 100, max( 0, $pct_bucket ) );

		$list_markup .= sprintf(
			'<span class="upa25-tickets-bar upa25-tickets-bar--list upa25-tickets-bar--pct-%1$d" aria-hidden="true"><span class="upa25-tickets-bar__fill"></span></span>',
			$pct_bucket
		);
	}

	$list_markup .= '</span>';

	return trim( $output . ' ' . $list_markup );
}
add_filter( 'sugar_calendar_block_event_list_event_list_view_event_view_dt_display', 'upa25_add_tickets_to_sugar_calendar_list_view', 10, 4 );
