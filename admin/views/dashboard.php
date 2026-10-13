<?php
/**
 * Admin View: Dashboard.
 *
 * @package Nexora_Booking_Manager
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="wrap nbm-admin-wrap">
	<div class="nbm-admin-header">
		<div class="nbm-admin-title-area">
			<h1 class="wp-heading-inline"><?php esc_html_e( 'Nexora Booking — Overview', 'nexora-booking-manager' ); ?></h1>
			<p class="nbm-admin-subtitle"><?php esc_html_e( 'Manage hotel and bar reservation requests, live availability and venue listings.', 'nexora-booking-manager' ); ?></p>
		</div>
		<div class="nbm-admin-actions">
			<a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=nbm_hotel' ) ); ?>" class="button button-primary">
				+ <?php esc_html_e( 'Add Hotel', 'nexora-booking-manager' ); ?>
			</a>
			<a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=nbm_bar' ) ); ?>" class="button button-secondary">
				+ <?php esc_html_e( 'Add Bar', 'nexora-booking-manager' ); ?>
			</a>
		</div>
	</div>

	<?php if ( $stats['pending'] > 0 ) : ?>
		<div class="nbm-alert-banner nbm-alert-warning">
			<span class="dashicons dashicons-bell"></span>
			<div>
				<strong><?php echo sprintf( esc_html__( 'Attention: You have %d pending booking request(s) awaiting review!', 'nexora-booking-manager' ), $stats['pending'] ); ?></strong>
				<p><?php esc_html_e( 'Review guest requests promptly to prevent inventory conflicts and keep visitors updated.', 'nexora-booking-manager' ); ?></p>
			</div>
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=nbm-bookings&status=pending' ) ); ?>" class="button button-small">
				<?php esc_html_e( 'Review Pending Requests', 'nexora-booking-manager' ); ?> &rarr;
			</a>
		</div>
	<?php endif; ?>

	<!-- Stats Grid -->
	<div class="nbm-stats-grid">
		<div class="nbm-stat-card nbm-card-pending">
			<div class="nbm-stat-icon dashicons dashicons-clock"></div>
			<div class="nbm-stat-info">
				<span class="nbm-stat-label"><?php esc_html_e( 'Pending Requests', 'nexora-booking-manager' ); ?></span>
				<strong class="nbm-stat-val"><?php echo absint( $stats['pending'] ); ?></strong>
			</div>
		</div>

		<div class="nbm-stat-card nbm-card-confirmed">
			<div class="nbm-stat-icon dashicons dashicons-yes-alt"></div>
			<div class="nbm-stat-info">
				<span class="nbm-stat-label"><?php esc_html_e( 'Confirmed Bookings', 'nexora-booking-manager' ); ?></span>
				<strong class="nbm-stat-val"><?php echo absint( $stats['confirmed'] ); ?></strong>
			</div>
		</div>

		<div class="nbm-stat-card nbm-card-total">
			<div class="nbm-stat-icon dashicons dashicons-calendar-alt"></div>
			<div class="nbm-stat-info">
				<span class="nbm-stat-label"><?php esc_html_e( 'Total Bookings', 'nexora-booking-manager' ); ?></span>
				<strong class="nbm-stat-val"><?php echo absint( $stats['total_bookings'] ); ?></strong>
			</div>
		</div>

		<div class="nbm-stat-card nbm-card-hotels">
			<div class="nbm-stat-icon dashicons dashicons-building"></div>
			<div class="nbm-stat-info">
				<span class="nbm-stat-label"><?php esc_html_e( 'Active Hotels', 'nexora-booking-manager' ); ?></span>
				<strong class="nbm-stat-val"><?php echo absint( $stats['total_hotels'] ); ?></strong>
			</div>
		</div>

		<div class="nbm-stat-card nbm-card-bars">
			<div class="nbm-stat-icon dashicons dashicons-beer"></div>
			<div class="nbm-stat-info">
				<span class="nbm-stat-label"><?php esc_html_e( 'Active Bars', 'nexora-booking-manager' ); ?></span>
				<strong class="nbm-stat-val"><?php echo absint( $stats['total_bars'] ); ?></strong>
			</div>
		</div>
	</div>

	<!-- Recent Bookings Table -->
	<div class="nbm-panel">
		<div class="nbm-panel-header">
			<h2><?php esc_html_e( 'Recent Booking Requests', 'nexora-booking-manager' ); ?></h2>
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=nbm-bookings' ) ); ?>" class="button button-secondary">
				<?php esc_html_e( 'View All Bookings', 'nexora-booking-manager' ); ?> &rarr;
			</a>
		</div>

		<?php if ( ! empty( $recent_bookings ) ) : ?>
			<table class="wp-list-table widefat fixed striped nbm-admin-table">
				<thead>
					<tr>
						<th style="width: 140px;"><?php esc_html_e( 'Reference', 'nexora-booking-manager' ); ?></th>
						<th><?php esc_html_e( 'Listing Venue', 'nexora-booking-manager' ); ?></th>
						<th><?php esc_html_e( 'Guest Details', 'nexora-booking-manager' ); ?></th>
						<th><?php esc_html_e( 'Requested Schedule', 'nexora-booking-manager' ); ?></th>
						<th style="width: 90px;"><?php esc_html_e( 'Guests', 'nexora-booking-manager' ); ?></th>
						<th style="width: 120px;"><?php esc_html_e( 'Status', 'nexora-booking-manager' ); ?></th>
						<th style="width: 160px;"><?php esc_html_e( 'Actions', 'nexora-booking-manager' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $recent_bookings as $booking ) :
						$listing_name = get_the_title( $booking->listing_id );
						$sched = ( 'hotel' === $booking->listing_type )
							? $booking->check_in . ' &rarr; ' . $booking->check_out
							: $booking->booking_date . ' at ' . substr( $booking->booking_time, 0, 5 );
						$badge_class = 'nbm-badge-' . sanitize_html_class( $booking->status );
					?>
						<tr id="nbm-row-<?php echo esc_attr( $booking->id ); ?>">
							<td>
								<a href="<?php echo esc_url( admin_url( 'admin.php?page=nbm-bookings&action=view&id=' . $booking->id ) ); ?>" style="font-weight:700;">
									<?php echo esc_html( $booking->booking_code ); ?>
								</a>
							</td>
							<td>
								<strong><?php echo esc_html( $listing_name ); ?></strong>
								<span class="nbm-sub-type">(<?php echo esc_html( ucfirst( $booking->listing_type ) ); ?>)</span>
							</td>
							<td>
								<div><strong><?php echo esc_html( $booking->guest_name ); ?></strong></div>
								<div class="nbm-text-dim"><?php echo esc_html( $booking->guest_email ); ?></div>
							</td>
							<td><?php echo esc_html( $sched ); ?></td>
							<td><?php echo absint( $booking->guest_count ); ?></td>
							<td>
								<span class="nbm-status-pill <?php echo esc_attr( $badge_class ); ?>">
									<?php echo esc_html( ucfirst( $booking->status ) ); ?>
								</span>
							</td>
							<td>
								<a href="<?php echo esc_url( admin_url( 'admin.php?page=nbm-bookings&action=view&id=' . $booking->id ) ); ?>" class="button button-small">
									<?php esc_html_e( 'Inspect', 'nexora-booking-manager' ); ?>
								</a>
								<?php if ( 'pending' === $booking->status ) : ?>
									<button type="button" class="button button-small button-primary nbm-quick-status-btn" data-id="<?php echo esc_attr( $booking->id ); ?>" data-status="confirmed" title="<?php esc_attr_e( 'Confirm Booking', 'nexora-booking-manager' ); ?>">✓</button>
									<button type="button" class="button button-small nbm-quick-status-btn" data-id="<?php echo esc_attr( $booking->id ); ?>" data-status="rejected" title="<?php esc_attr_e( 'Reject Booking', 'nexora-booking-manager' ); ?>">✕</button>
								<?php endif; ?>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		<?php else : ?>
			<div class="nbm-empty-panel">
				<p><?php esc_html_e( 'No booking requests have been received yet. Once visitors submit the frontend booking form, requests will appear here.', 'nexora-booking-manager' ); ?></p>
			</div>
		<?php endif; ?>
	</div>
</div>
