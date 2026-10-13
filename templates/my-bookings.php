<?php
/**
 * Template: Guest Booking Lookup / My Bookings shortcode.
 *
 * @package Nexora_Booking_Manager
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$instance_id = isset( $instance_id ) ? $instance_id : uniqid();
$is_logged_in = is_user_logged_in();
$user_bookings = array();

if ( $is_logged_in ) {
	$current_user = wp_get_current_user();
	$user_email   = $current_user->user_email;

	global $wpdb;
	$table         = NBM_Database::get_bookings_table();
	$user_bookings = $wpdb->get_results(
		$wpdb->prepare( "SELECT * FROM {$table} WHERE guest_email = %s ORDER BY created_at DESC LIMIT 20", $user_email )
	);
}
?>
<div class="nbm-wrapper nbm-my-bookings-wrap" id="nbm-tracker-<?php echo esc_attr( $instance_id ); ?>">
	
	<!-- Fast Tracker Form -->
	<div class="nbm-lookup-box">
		<div class="nbm-form-header">
			<h3 class="nbm-form-title"><?php esc_html_e( 'Check Booking Status', 'nexora-booking-manager' ); ?></h3>
			<p class="nbm-form-subtitle"><?php esc_html_e( 'Enter your Booking Reference and email address to view reservation details and live status.', 'nexora-booking-manager' ); ?></p>
		</div>

		<form class="nbm-lookup-form" id="nbm-lookup-form-<?php echo esc_attr( $instance_id ); ?>">
			<div class="nbm-form-row">
				<div class="nbm-form-field">
					<label for="nbm_track_code_<?php echo esc_attr( $instance_id ); ?>" class="nbm-label"><?php esc_html_e( 'Booking Reference Code', 'nexora-booking-manager' ); ?></label>
					<input type="text" id="nbm_track_code_<?php echo esc_attr( $instance_id ); ?>" name="booking_code" class="nbm-input" placeholder="e.g. NBM-HTL-XXXXXXXX" required />
				</div>
				<div class="nbm-form-field">
					<label for="nbm_track_email_<?php echo esc_attr( $instance_id ); ?>" class="nbm-label"><?php esc_html_e( 'Guest Email Address', 'nexora-booking-manager' ); ?></label>
					<input type="email" id="nbm_track_email_<?php echo esc_attr( $instance_id ); ?>" name="guest_email" class="nbm-input" placeholder="e.g. yourname@example.com" required />
				</div>
			</div>

			<div class="nbm-form-actions">
				<button type="submit" class="nbm-btn nbm-btn-primary nbm-btn-track">
					<span class="nbm-btn-text"><?php esc_html_e( 'Lookup Booking', 'nexora-booking-manager' ); ?></span>
					<span class="nbm-spinner" style="display:none;"></span>
				</button>
			</div>
		</form>

		<!-- Lookup Result Card (Dynamically populated via AJAX) -->
		<div class="nbm-lookup-result" style="display:none; margin-top:20px;"></div>
	</div>

	<!-- Logged-in User History Table -->
	<?php if ( $is_logged_in && ! empty( $user_bookings ) ) : ?>
		<div class="nbm-user-history-section" style="margin-top: 40px;">
			<h3 style="font-size: 20px; font-weight: 700; margin-bottom: 16px;"><?php esc_html_e( 'Your Recent Bookings', 'nexora-booking-manager' ); ?></h3>
			<div class="nbm-table-responsive">
				<table class="nbm-table">
					<thead>
						<tr>
							<th><?php esc_html_e( 'Reference', 'nexora-booking-manager' ); ?></th>
							<th><?php esc_html_e( 'Venue', 'nexora-booking-manager' ); ?></th>
							<th><?php esc_html_e( 'Schedule', 'nexora-booking-manager' ); ?></th>
							<th><?php esc_html_e( 'Guests', 'nexora-booking-manager' ); ?></th>
							<th><?php esc_html_e( 'Status', 'nexora-booking-manager' ); ?></th>
							<th><?php esc_html_e( 'Date Sent', 'nexora-booking-manager' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $user_bookings as $ub ) :
							$venue_title = get_the_title( $ub->listing_id );
							$sched = ( 'hotel' === $ub->listing_type )
								? $ub->check_in . ' to ' . $ub->check_out
								: $ub->booking_date . ' at ' . $ub->booking_time;
							$badge_class = 'nbm-status-' . sanitize_html_class( $ub->status );
						?>
							<tr>
								<td><strong><?php echo esc_html( $ub->booking_code ); ?></strong></td>
								<td><?php echo esc_html( $venue_title ); ?> (<?php echo esc_html( ucfirst( $ub->listing_type ) ); ?>)</td>
								<td><?php echo esc_html( $sched ); ?></td>
								<td><?php echo absint( $ub->guest_count ); ?></td>
								<td><span class="nbm-status-badge <?php echo esc_attr( $badge_class ); ?>"><?php echo esc_html( ucfirst( $ub->status ) ); ?></span></td>
								<td><?php echo esc_html( gmdate( 'M j, Y', strtotime( $ub->created_at ) ) ); ?></td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		</div>
	<?php endif; ?>

</div>
