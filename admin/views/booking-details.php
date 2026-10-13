<?php
/**
 * Admin View: Booking Details & Status Lifecycle Management.
 *
 * @package Nexora_Booking_Manager
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$listing_title = get_the_title( $booking->listing_id );
$edit_link     = get_edit_post_link( $booking->listing_id );
$badge_class   = 'nbm-badge-' . sanitize_html_class( $booking->status );
?>
<div class="wrap nbm-admin-wrap">
	<div class="nbm-admin-header">
		<div>
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=nbm-bookings' ) ); ?>" class="nbm-back-link">
				&larr; <?php esc_html_e( 'Back to All Bookings', 'nexora-booking-manager' ); ?>
			</a>
			<h1 class="wp-heading-inline">
				<?php echo sprintf( esc_html__( 'Booking %s', 'nexora-booking-manager' ), esc_html( $booking->booking_code ) ); ?>
			</h1>
			<span class="nbm-status-pill <?php echo esc_attr( $badge_class ); ?>" id="nbm-current-status-badge">
				<?php echo esc_html( ucfirst( $booking->status ) ); ?>
			</span>
		</div>
		<div class="nbm-admin-actions">
			<button type="button" class="button" onclick="window.print();">🖨 <?php esc_html_e( 'Print Details', 'nexora-booking-manager' ); ?></button>
		</div>
	</div>

	<div class="nbm-details-grid">
		
		<!-- Left Column: Reservation & Guest Details -->
		<div class="nbm-details-main">
			
			<div class="nbm-panel">
				<h2><?php esc_html_e( 'Reservation Information', 'nexora-booking-manager' ); ?></h2>
				<table class="nbm-info-table">
					<tbody>
						<tr>
							<th><?php esc_html_e( 'Booking Reference:', 'nexora-booking-manager' ); ?></th>
							<td><strong><?php echo esc_html( $booking->booking_code ); ?></strong></td>
						</tr>
						<tr>
							<th><?php esc_html_e( 'Listing Venue:', 'nexora-booking-manager' ); ?></th>
							<td>
								<strong><?php echo esc_html( $listing_title ); ?></strong>
								(<?php echo esc_html( ucfirst( $booking->listing_type ) ); ?>)
								<?php if ( $edit_link ) : ?>
									— <a href="<?php echo esc_url( $edit_link ); ?>" target="_blank"><?php esc_html_e( 'Edit Listing', 'nexora-booking-manager' ); ?> &rarr;</a>
								<?php endif; ?>
							</td>
						</tr>
						<tr>
							<th><?php esc_html_e( 'Schedule:', 'nexora-booking-manager' ); ?></th>
							<td>
								<?php if ( 'hotel' === $booking->listing_type ) : ?>
									<strong><?php echo esc_html( $booking->check_in ); ?></strong> <?php esc_html_e( 'to', 'nexora-booking-manager' ); ?> <strong><?php echo esc_html( $booking->check_out ); ?></strong>
									<span class="nbm-text-dim">(<?php echo sprintf( esc_html__( '%d night(s)', 'nexora-booking-manager' ), max( 1, (int) ( ( strtotime( $booking->check_out ) - strtotime( $booking->check_in ) ) / 86400 ) ) ); ?>)</span>
								<?php else : ?>
									<strong><?php echo esc_html( $booking->booking_date ); ?></strong> <?php esc_html_e( 'at', 'nexora-booking-manager' ); ?> <strong><?php echo esc_html( substr( $booking->booking_time, 0, 5 ) ); ?></strong>
								<?php endif; ?>
							</td>
						</tr>
						<tr>
							<th><?php esc_html_e( 'Party Size:', 'nexora-booking-manager' ); ?></th>
							<td><?php echo sprintf( esc_html__( '%d Guest(s)', 'nexora-booking-manager' ), absint( $booking->guest_count ) ); ?></td>
						</tr>
						<tr>
							<th><?php esc_html_e( 'Submitted On:', 'nexora-booking-manager' ); ?></th>
							<td><?php echo esc_html( $booking->created_at ); ?></td>
						</tr>
						<tr>
							<th><?php esc_html_e( 'Last Updated:', 'nexora-booking-manager' ); ?></th>
							<td><?php echo esc_html( $booking->updated_at ); ?></td>
						</tr>
					</tbody>
				</table>
			</div>

			<div class="nbm-panel">
				<h2><?php esc_html_e( 'Guest Contact Details', 'nexora-booking-manager' ); ?></h2>
				<table class="nbm-info-table">
					<tbody>
						<tr>
							<th><?php esc_html_e( 'Full Name:', 'nexora-booking-manager' ); ?></th>
							<td><strong><?php echo esc_html( $booking->guest_name ); ?></strong></td>
						</tr>
						<tr>
							<th><?php esc_html_e( 'Email Address:', 'nexora-booking-manager' ); ?></th>
							<td><a href="mailto:<?php echo esc_attr( $booking->guest_email ); ?>"><?php echo esc_html( $booking->guest_email ); ?></a></td>
						</tr>
						<tr>
							<th><?php esc_html_e( 'Phone Number:', 'nexora-booking-manager' ); ?></th>
							<td>
								<?php if ( $booking->guest_phone ) : ?>
									<a href="tel:<?php echo esc_attr( $booking->guest_phone ); ?>"><?php echo esc_html( $booking->guest_phone ); ?></a>
								<?php else : ?>
									<span class="nbm-text-dim"><?php esc_html_e( 'Not provided', 'nexora-booking-manager' ); ?></span>
								<?php endif; ?>
							</td>
						</tr>
						<tr>
							<th><?php esc_html_e( 'Guest Message:', 'nexora-booking-manager' ); ?></th>
							<td>
								<?php if ( $booking->guest_message ) : ?>
									<div class="nbm-msg-box"><?php echo nl2br( esc_html( $booking->guest_message ) ); ?></div>
								<?php else : ?>
									<span class="nbm-text-dim"><?php esc_html_e( 'No message included', 'nexora-booking-manager' ); ?></span>
								<?php endif; ?>
							</td>
						</tr>
					</tbody>
				</table>
			</div>

			<!-- Status History & Audit Notes -->
			<div class="nbm-panel">
				<h2><?php esc_html_e( 'Internal Audit Log & Notes', 'nexora-booking-manager' ); ?></h2>
				
				<div class="nbm-notes-timeline" id="nbm-notes-list">
					<?php if ( ! empty( $notes ) ) : ?>
						<?php foreach ( $notes as $note ) :
							$author = ( $note->author_id > 0 ) ? get_userdata( $note->author_id ) : null;
							$author_name = $author ? $author->display_name : __( 'System', 'nexora-booking-manager' );
						?>
							<div class="nbm-note-item nbm-note-<?php echo esc_attr( $note->note_type ); ?>">
								<div class="nbm-note-meta">
									<strong><?php echo esc_html( $author_name ); ?></strong>
									<span><?php echo esc_html( $note->created_at ); ?></span>
								</div>
								<div class="nbm-note-content"><?php echo nl2br( esc_html( $note->note ) ); ?></div>
							</div>
						<?php endforeach; ?>
					<?php else : ?>
						<p class="nbm-text-dim"><?php esc_html_e( 'No audit entries yet.', 'nexora-booking-manager' ); ?></p>
					<?php endif; ?>
				</div>

				<!-- Add Note Form -->
				<div class="nbm-add-note-box" style="margin-top: 20px; padding-top: 16px; border-top: 1px solid #e2e8f0;">
					<h4><?php esc_html_e( 'Add Private Staff Note', 'nexora-booking-manager' ); ?></h4>
					<p class="description"><?php esc_html_e( 'Internal notes are strictly confidential and will never be seen by the guest.', 'nexora-booking-manager' ); ?></p>
					<textarea id="nbm-new-admin-note" rows="3" style="width:100%; margin:8px 0;" placeholder="<?php esc_attr_e( 'Write an internal note...', 'nexora-booking-manager' ); ?>"></textarea>
					<button type="button" class="button" id="nbm-submit-note-btn" data-id="<?php echo esc_attr( $booking->id ); ?>">
						<?php esc_html_e( 'Save Private Note', 'nexora-booking-manager' ); ?>
					</button>
				</div>
			</div>

		</div>

		<!-- Right Column: Status Transition Station -->
		<div class="nbm-details-sidebar">
			<div class="nbm-panel nbm-action-box">
				<h2><?php esc_html_e( 'Booking Status Control', 'nexora-booking-manager' ); ?></h2>
				<p class="description">
					<?php esc_html_e( 'Changing status will re-check resource availability (when confirming) and dispatch email notifications to the guest.', 'nexora-booking-manager' ); ?>
				</p>

				<form id="nbm-status-form" style="margin-top: 16px;">
					<input type="hidden" name="booking_id" value="<?php echo esc_attr( $booking->id ); ?>" />
					
					<div class="nbm-field-group">
						<label for="nbm-status-select" style="font-weight:700; display:block; margin-bottom:6px;">
							<?php esc_html_e( 'Target Status:', 'nexora-booking-manager' ); ?>
						</label>
						<select id="nbm-status-select" name="new_status" style="width: 100%; font-size:15px; padding:6px 10px;">
							<option value="pending" <?php selected( $booking->status, 'pending' ); ?>><?php esc_html_e( 'Pending (Awaiting Review)', 'nexora-booking-manager' ); ?></option>
							<option value="confirmed" <?php selected( $booking->status, 'confirmed' ); ?>><?php esc_html_e( 'Confirmed (Lock Inventory)', 'nexora-booking-manager' ); ?></option>
							<option value="rejected" <?php selected( $booking->status, 'rejected' ); ?>><?php esc_html_e( 'Rejected (Decline Request)', 'nexora-booking-manager' ); ?></option>
							<option value="cancelled" <?php selected( $booking->status, 'cancelled' ); ?>><?php esc_html_e( 'Cancelled', 'nexora-booking-manager' ); ?></option>
							<option value="completed" <?php selected( $booking->status, 'completed' ); ?>><?php esc_html_e( 'Completed', 'nexora-booking-manager' ); ?></option>
						</select>
					</div>

					<div class="nbm-field-group" style="margin-top: 14px;">
						<label for="nbm-status-reason" style="font-weight:600; display:block; margin-bottom:4px;">
							<?php esc_html_e( 'Reason / Note to Guest (Optional):', 'nexora-booking-manager' ); ?>
						</label>
						<textarea id="nbm-status-reason" name="note" rows="3" style="width:100%;" placeholder="<?php esc_attr_e( 'Optional message included in guest status email...', 'nexora-booking-manager' ); ?>"></textarea>
					</div>

					<div style="margin-top: 16px;">
						<button type="button" id="nbm-update-status-submit" class="button button-primary button-large" style="width: 100%;">
							<?php esc_html_e( 'Update Status & Notify Guest', 'nexora-booking-manager' ); ?>
						</button>
					</div>

					<div id="nbm-status-update-feedback" style="margin-top:12px; display:none;"></div>
				</form>
			</div>
		</div>

	</div>
</div>
