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
 * @return void
 */
function upa25_render_sugar_calendar_tickets_remaining( $event ): void {
	if ( ! is_object( $event ) || empty( $event->id ) ) {
		return;
	}

	if ( ! function_exists( 'get_event_meta' ) ) {
		return;
	}

	$event_id = (int) $event->id;

	// Only render for events that have ticketing enabled.
	$tickets_enabled = (bool) get_event_meta( $event_id, 'tickets', true );
	if ( ! $tickets_enabled ) {
		return;
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
	?>
	<div class="sc-frontend-single-event__details__tickets-remaining sc-frontend-single-event__details-row">
		<div class="sc-frontend-single-event__details__label">
			<?php esc_html_e( 'Tickets:', 'upa25' ); ?>
		</div>
		<div class="sc-frontend-single-event__details__val">
			<span class="upa25-tickets-value"><?php echo esc_html( $value ); ?></span>
			<?php if ( $show_bar ) : ?>
			<div
				class="upa25-tickets-bar"
				role="progressbar"
				aria-label="<?php esc_attr_e( 'Ticket availability', 'upa25' ); ?>"
				aria-valuenow="<?php echo esc_attr( 100 - $pct_sold ); ?>"
				aria-valuemin="0"
				aria-valuemax="100"
			>
				<div class="upa25-tickets-bar__fill" style="--upa25-tickets-pct: <?php echo esc_attr( $pct_sold ); ?>%"></div>
			</div>
			<?php endif; ?>
		</div>
	</div>
	<?php
}
add_action( 'sugar_calendar_frontend_event_details', 'upa25_render_sugar_calendar_tickets_remaining', 55 );
