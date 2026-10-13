<?php
/**
 * Admin View: Bookings List.
 *
 * @package Nexora_Booking_Manager
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$current_status = sanitize_key( $_GET['status'] ?? '' );
$current_type   = sanitize_key( $_GET['listing_type'] ?? '' );
$search_query   = sanitize_text_field( $_GET['s'] ?? '' );

$export_url = wp_nonce_url(
	admin_url( 'admin.php?page=nbm-bookings&nbm_action=export_csv&status=' . urlencode( $current_status ) . '&listing_type=' . urlencode( $current_type ) ),
	'nbm_export_csv'
);
?>
<div class="wrap nbm-admin-wrap">
	<div class="nbm-admin-header">
		<div>
			<h1 class="wp-heading-inline"><?php esc_html_e( 'Bookings Management', 'nexora-booking-manager' ); ?></h1>
			<a href="<?php echo esc_url( $export_url ); ?>" class="page-title-action">
				📥 <?php esc_html_e( 'Export to CSV', 'nexora-booking-manager' ); ?>
			</a>
		</div>
	</div>

	<!-- Filter Tabs / Bar -->
	<ul class="subsubsub">
		<li><a href="<?php echo esc_url( admin_url( 'admin.php?page=nbm-bookings' ) ); ?>" class="<?php echo empty( $current_status ) ? 'current' : ''; ?>"><?php esc_html_e( 'All', 'nexora-booking-manager' ); ?></a> |</li>
		<li><a href="<?php echo esc_url( admin_url( 'admin.php?page=nbm-bookings&status=pending' ) ); ?>" class="<?php echo 'pending' === $current_status ? 'current' : ''; ?>"><?php esc_html_e( 'Pending', 'nexora-booking-manager' ); ?></a> |</li>
		<li><a href="<?php echo esc_url( admin_url( 'admin.php?page=nbm-bookings&status=confirmed' ) ); ?>" class="<?php echo 'confirmed' === $current_status ? 'current' : ''; ?>"><?php esc_html_e( 'Confirmed', 'nexora-booking-manager' ); ?></a> |</li>
		<li><a href="<?php echo esc_url( admin_url( 'admin.php?page=nbm-bookings&status=rejected' ) ); ?>" class="<?php echo 'rejected' === $current_status ? 'current' : ''; ?>"><?php esc_html_e( 'Rejected', 'nexora-booking-manager' ); ?></a> |</li>
		<li><a href="<?php echo esc_url( admin_url( 'admin.php?page=nbm-bookings&status=cancelled' ) ); ?>" class="<?php echo 'cancelled' === $current_status ? 'current' : ''; ?>"><?php esc_html_e( 'Cancelled', 'nexora-booking-manager' ); ?></a> |</li>
		<li><a href="<?php echo esc_url( admin_url( 'admin.php?page=nbm-bookings&status=completed' ) ); ?>" class="<?php echo 'completed' === $current_status ? 'current' : ''; ?>"><?php esc_html_e( 'Completed', 'nexora-booking-manager' ); ?></a></li>
	</ul>

	<!-- Search & Filter Controls -->
	<form method="get" class="nbm-filters-form">
		<input type="hidden" name="page" value="nbm-bookings" />
		<?php if ( $current_status ) : ?>
			<input type="hidden" name="status" value="<?php echo esc_attr( $current_status ); ?>" />
		<?php endif; ?>

		<div class="tablenav top">
			<div class="alignleft actions">
				<select name="listing_type">
					<option value=""><?php esc_html_e( '— All Listing Types —', 'nexora-booking-manager' ); ?></option>
					<option value="hotel" <?php selected( $current_type, 'hotel' ); ?>><?php esc_html_e( 'Hotels', 'nexora-booking-manager' ); ?></option>
					<option value="bar" <?php selected( $current_type, 'bar' ); ?>><?php esc_html_e( 'Bars', 'nexora-booking-manager' ); ?></option>
				</select>
				<input type="submit" class="button" value="<?php esc_attr_e( 'Filter', 'nexora-booking-manager' ); ?>" />
			</div>

			<div class="alignright">
				<p class="search-box" style="position:relative; margin:0;">
					<label class="screen-reader-text" for="nbm-search-input"><?php esc_html_e( 'Search Bookings:', 'nexora-booking-manager' ); ?></label>
					<input type="search" id="nbm-search-input" name="s" value="<?php echo esc_attr( $search_query ); ?>" placeholder="<?php esc_attr_e( 'Search by code, guest or email...', 'nexora-booking-manager' ); ?>" />
					<input type="submit" id="search-submit" class="button" value="<?php esc_attr_e( 'Search', 'nexora-booking-manager' ); ?>" />
				</p>
			</div>
			<br class="clear" />
		</div>
	</form>

	<!-- Bookings List Table -->
	<table class="wp-list-table widefat fixed striped nbm-admin-table">
		<thead>
			<tr>
				<th style="width: 140px;"><?php esc_html_e( 'Reference', 'nexora-booking-manager' ); ?></th>
				<th><?php esc_html_e( 'Listing Venue', 'nexora-booking-manager' ); ?></th>
				<th><?php esc_html_e( 'Guest Name & Email', 'nexora-booking-manager' ); ?></th>
				<th><?php esc_html_e( 'Phone', 'nexora-booking-manager' ); ?></th>
				<th><?php esc_html_e( 'Requested Schedule', 'nexora-booking-manager' ); ?></th>
				<th style="width: 80px;"><?php esc_html_e( 'Guests', 'nexora-booking-manager' ); ?></th>
				<th style="width: 120px;"><?php esc_html_e( 'Status', 'nexora-booking-manager' ); ?></th>
				<th style="width: 130px;"><?php esc_html_e( 'Submitted', 'nexora-booking-manager' ); ?></th>
				<th style="width: 160px;"><?php esc_html_e( 'Actions', 'nexora-booking-manager' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php if ( ! empty( $results['items'] ) ) : ?>
				<?php foreach ( $results['items'] as $item ) :
					$venue_title = get_the_title( $item->listing_id );
					$sched = ( 'hotel' === $item->listing_type )
						? $item->check_in . ' &rarr; ' . $item->check_out
						: $item->booking_date . ' at ' . substr( $item->booking_time, 0, 5 );
					$badge_class = 'nbm-badge-' . sanitize_html_class( $item->status );
					$view_url = admin_url( 'admin.php?page=nbm-bookings&action=view&id=' . $item->id );
				?>
					<tr id="nbm-row-<?php echo esc_attr( $item->id ); ?>">
						<td>
							<a href="<?php echo esc_url( $view_url ); ?>" style="font-weight:700;">
								<?php echo esc_html( $item->booking_code ); ?>
							</a>
						</td>
						<td>
							<strong><?php echo esc_html( $venue_title ); ?></strong>
							<span class="nbm-sub-type">(<?php echo esc_html( ucfirst( $item->listing_type ) ); ?>)</span>
						</td>
						<td>
							<div><strong><?php echo esc_html( $item->guest_name ); ?></strong></div>
							<div class="nbm-text-dim"><a href="mailto:<?php echo esc_attr( $item->guest_email ); ?>"><?php echo esc_html( $item->guest_email ); ?></a></div>
						</td>
						<td><?php echo esc_html( $item->guest_phone ? $item->guest_phone : '—' ); ?></td>
						<td><?php echo esc_html( $sched ); ?></td>
						<td><?php echo absint( $item->guest_count ); ?></td>
						<td>
							<span class="nbm-status-pill <?php echo esc_attr( $badge_class ); ?>">
								<?php echo esc_html( ucfirst( $item->status ) ); ?>
							</span>
						</td>
						<td><?php echo esc_html( gmdate( 'Y-m-d H:i', strtotime( $item->created_at ) ) ); ?></td>
						<td>
							<a href="<?php echo esc_url( $view_url ); ?>" class="button button-small">
								<?php esc_html_e( 'View', 'nexora-booking-manager' ); ?>
							</a>
							<?php if ( 'pending' === $item->status ) : ?>
								<button type="button" class="button button-small button-primary nbm-quick-status-btn" data-id="<?php echo esc_attr( $item->id ); ?>" data-status="confirmed" title="<?php esc_attr_e( 'Confirm', 'nexora-booking-manager' ); ?>">✓</button>
								<button type="button" class="button button-small nbm-quick-status-btn" data-id="<?php echo esc_attr( $item->id ); ?>" data-status="rejected" title="<?php esc_attr_e( 'Reject', 'nexora-booking-manager' ); ?>">✕</button>
							<?php endif; ?>
							<button type="button" class="button button-small button-link-delete nbm-delete-booking-btn" data-id="<?php echo esc_attr( $item->id ); ?>" title="<?php esc_attr_e( 'Delete', 'nexora-booking-manager' ); ?>">🗑</button>
						</td>
					</tr>
				<?php endforeach; ?>
			<?php else : ?>
				<tr>
					<td colspan="9" style="text-align: center; padding: 24px;">
						<?php esc_html_e( 'No bookings found.', 'nexora-booking-manager' ); ?>
					</td>
				</tr>
			<?php endif; ?>
		</tbody>
	</table>

	<!-- Pagination -->
	<?php if ( $results['pages'] > 1 ) : ?>
		<div class="tablenav bottom">
			<div class="tablenav-pages">
				<span class="displaying-num"><?php echo sprintf( esc_html__( '%d items', 'nexora-booking-manager' ), $results['total'] ); ?></span>
				<?php
				echo paginate_links( array(
					'base'    => add_query_arg( 'paged', '%#%' ),
					'format'  => '',
					'prev_text' => '&laquo;',
					'next_text' => '&raquo;',
					'total'   => $results['pages'],
					'current' => $results['current_page'],
				) );
				?>
			</div>
		</div>
	<?php endif; ?>
</div>
