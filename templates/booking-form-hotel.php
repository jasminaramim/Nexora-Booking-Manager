<?php
/**
 * Template: Hotel Booking Request Form.
 *
 * @package Nexora_Booking_Manager
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$instance_id  = isset( $instance_id ) ? $instance_id : uniqid();
$listing_id   = isset( $listing_id ) ? absint( $listing_id ) : 0;
$today        = current_time( 'Y-m-d' );
$tomorrow     = gmdate( 'Y-m-d', strtotime( '+1 day', current_time( 'timestamp' ) ) );

$selected_hotel = $listing_id ? get_post( $listing_id ) : null;
$price_per_night= $selected_hotel ? get_post_meta( $listing_id, '_nbm_price_per_night', true ) : '';
$max_guests     = $selected_hotel ? get_post_meta( $listing_id, '_nbm_max_guests', true ) : 4;
$min_stay       = $selected_hotel ? get_post_meta( $listing_id, '_nbm_min_stay', true ) : 1;
$checkin_time   = $selected_hotel ? get_post_meta( $listing_id, '_nbm_checkin_time', true ) : '14:00';
$checkout_time  = $selected_hotel ? get_post_meta( $listing_id, '_nbm_checkout_time', true ) : '11:00';

$phone_required = '1' === NBM_Settings::get( 'require_phone', '0' );
?>
<div class="nbm-booking-card nbm-hotel-form-box" id="nbm-form-container-<?php echo esc_attr( $instance_id ); ?>">
	<div class="nbm-form-header">
		<h3 class="nbm-form-title"><?php esc_html_e( 'Request a Reservation', 'nexora-booking-manager' ); ?></h3>
		<?php if ( $selected_hotel ) : ?>
			<div class="nbm-form-subtitle">
				<span class="nbm-venue-name"><?php echo esc_html( $selected_hotel->post_title ); ?></span>
				<?php if ( '' !== $price_per_night ) : ?>
					<span class="nbm-venue-rate"> — <strong><?php echo esc_html( NBM_Settings::format_price( $price_per_night ) ); ?></strong> /<?php esc_html_e( 'night', 'nexora-booking-manager' ); ?></span>
				<?php endif; ?>
			</div>
		<?php else : ?>
			<p class="nbm-form-subtitle"><?php esc_html_e( 'Select a hotel and your preferred stay dates.', 'nexora-booking-manager' ); ?></p>
		<?php endif; ?>
	</div>

	<form class="nbm-ajax-booking-form" id="nbm-hotel-form-<?php echo esc_attr( $instance_id ); ?>" method="post">
		<input type="hidden" name="action" value="nbm_submit_booking" />
		<input type="hidden" name="listing_type" value="hotel" />
		<input type="hidden" name="nonce" value="<?php echo esc_attr( wp_create_nonce( 'nbm_frontend_nonce' ) ); ?>" />
		
		<!-- Anti-spam Honeypot -->
		<div style="display:none !important;" aria-hidden="true">
			<label for="nbm_hp_website_<?php echo esc_attr( $instance_id ); ?>">Leave blank</label>
			<input type="text" id="nbm_hp_website_<?php echo esc_attr( $instance_id ); ?>" name="nbm_hp_website" tabindex="-1" autocomplete="off" />
		</div>

		<?php if ( ! $selected_hotel ) : ?>
			<div class="nbm-form-field">
				<label for="nbm_hotel_id_<?php echo esc_attr( $instance_id ); ?>" class="nbm-label">
					<?php esc_html_e( 'Select Hotel', 'nexora-booking-manager' ); ?> <span class="nbm-req">*</span>
				</label>
				<select name="listing_id" id="nbm_hotel_id_<?php echo esc_attr( $instance_id ); ?>" class="nbm-input nbm-hotel-selector" required>
					<option value=""><?php esc_html_e( '— Choose a Hotel —', 'nexora-booking-manager' ); ?></option>
					<?php
					$hotels_query = new WP_Query( array( 'post_type' => 'nbm_hotel', 'post_status' => 'publish', 'posts_per_page' => 100, 'orderby' => 'title', 'order' => 'ASC' ) );
					while ( $hotels_query->have_posts() ) :
						$hotels_query->the_post();
						$h_price = get_post_meta( get_the_ID(), '_nbm_price_per_night', true );
						$h_max_g = get_post_meta( get_the_ID(), '_nbm_max_guests', true );
						?>
						<option value="<?php echo esc_attr( get_the_ID() ); ?>" data-price="<?php echo esc_attr( $h_price ); ?>" data-max-guests="<?php echo esc_attr( $h_max_g ? $h_max_g : 4 ); ?>">
							<?php the_title(); ?> (<?php echo esc_html( NBM_Settings::format_price( $h_price ) ); ?>/night)
						</option>
					<?php endwhile; wp_reset_postdata(); ?>
				</select>
			</div>
		<?php else : ?>
			<input type="hidden" name="listing_id" class="nbm-hotel-id-input" value="<?php echo esc_attr( $listing_id ); ?>" data-price="<?php echo esc_attr( $price_per_night ); ?>" />
		<?php endif; ?>

		<div class="nbm-form-row nbm-row-dates">
			<div class="nbm-form-field">
				<label for="nbm_checkin_<?php echo esc_attr( $instance_id ); ?>" class="nbm-label">
					<?php esc_html_e( 'Check-In Date', 'nexora-booking-manager' ); ?> <span class="nbm-req">*</span>
				</label>
				<input type="date" id="nbm_checkin_<?php echo esc_attr( $instance_id ); ?>" name="check_in" class="nbm-input nbm-date-checkin" min="<?php echo esc_attr( $today ); ?>" value="<?php echo esc_attr( $today ); ?>" required />
				<small class="nbm-field-hint"><?php echo sprintf( esc_html__( 'Check-in from %s', 'nexora-booking-manager' ), esc_html( $checkin_time ) ); ?></small>
			</div>

			<div class="nbm-form-field">
				<label for="nbm_checkout_<?php echo esc_attr( $instance_id ); ?>" class="nbm-label">
					<?php esc_html_e( 'Check-Out Date', 'nexora-booking-manager' ); ?> <span class="nbm-req">*</span>
				</label>
				<input type="date" id="nbm_checkout_<?php echo esc_attr( $instance_id ); ?>" name="check_out" class="nbm-input nbm-date-checkout" min="<?php echo esc_attr( $tomorrow ); ?>" value="<?php echo esc_attr( $tomorrow ); ?>" required />
				<small class="nbm-field-hint"><?php echo sprintf( esc_html__( 'Check-out by %s', 'nexora-booking-manager' ), esc_html( $checkout_time ) ); ?></small>
			</div>
		</div>

		<div class="nbm-form-field">
			<label for="nbm_guests_<?php echo esc_attr( $instance_id ); ?>" class="nbm-label">
				<?php esc_html_e( 'Number of Guests', 'nexora-booking-manager' ); ?> <span class="nbm-req">*</span>
			</label>
			<select name="guest_count" id="nbm_guests_<?php echo esc_attr( $instance_id ); ?>" class="nbm-input nbm-guest-count" required>
				<?php for ( $i = 1; $i <= max( 1, intval( $max_guests ) ); $i++ ) : ?>
					<option value="<?php echo esc_attr( $i ); ?>" <?php selected( $i, 1 ); ?>>
						<?php echo sprintf( esc_html( _n( '%d Guest', '%d Guests', $i, 'nexora-booking-manager' ) ), $i ); ?>
					</option>
				<?php endfor; ?>
			</select>
		</div>

		<div class="nbm-form-row nbm-row-contact">
			<div class="nbm-form-field">
				<label for="nbm_name_<?php echo esc_attr( $instance_id ); ?>" class="nbm-label">
					<?php esc_html_e( 'Full Name', 'nexora-booking-manager' ); ?> <span class="nbm-req">*</span>
				</label>
				<input type="text" id="nbm_name_<?php echo esc_attr( $instance_id ); ?>" name="guest_name" class="nbm-input" placeholder="e.g. John Doe" required />
			</div>

			<div class="nbm-form-field">
				<label for="nbm_email_<?php echo esc_attr( $instance_id ); ?>" class="nbm-label">
					<?php esc_html_e( 'Email Address', 'nexora-booking-manager' ); ?> <span class="nbm-req">*</span>
				</label>
				<input type="email" id="nbm_email_<?php echo esc_attr( $instance_id ); ?>" name="guest_email" class="nbm-input" placeholder="e.g. john@example.com" required />
			</div>
		</div>

		<div class="nbm-form-field">
			<label for="nbm_phone_<?php echo esc_attr( $instance_id ); ?>" class="nbm-label">
				<?php esc_html_e( 'Phone Number', 'nexora-booking-manager' ); ?> <?php if ( $phone_required ) : ?><span class="nbm-req">*</span><?php else : ?><span class="nbm-opt">(<?php esc_html_e( 'Optional', 'nexora-booking-manager' ); ?>)</span><?php endif; ?>
			</label>
			<input type="tel" id="nbm_phone_<?php echo esc_attr( $instance_id ); ?>" name="guest_phone" class="nbm-input" placeholder="+1 (555) 000-0000" <?php echo $phone_required ? 'required' : ''; ?> />
		</div>

		<div class="nbm-form-field">
			<label for="nbm_msg_<?php echo esc_attr( $instance_id ); ?>" class="nbm-label">
				<?php esc_html_e( 'Special Requests / Notes', 'nexora-booking-manager' ); ?> <span class="nbm-opt">(<?php esc_html_e( 'Optional', 'nexora-booking-manager' ); ?>)</span>
			</label>
			<textarea id="nbm_msg_<?php echo esc_attr( $instance_id ); ?>" name="guest_message" class="nbm-textarea" rows="2" placeholder="<?php esc_attr_e( 'Any dietary, room accessibility or arrival notes...', 'nexora-booking-manager' ); ?>"></textarea>
		</div>

		<!-- Price summary calculation display -->
		<div class="nbm-pricing-summary" style="display:none;">
			<div class="nbm-summary-row">
				<span><?php esc_html_e( 'Estimated Stay:', 'nexora-booking-manager' ); ?></span>
				<strong class="nbm-calc-nights">1 <?php esc_html_e( 'night', 'nexora-booking-manager' ); ?></strong>
			</div>
			<div class="nbm-summary-row nbm-summary-total">
				<span><?php esc_html_e( 'Estimated Total:', 'nexora-booking-manager' ); ?></span>
				<strong class="nbm-calc-total">$0.00</strong>
			</div>
		</div>

		<div class="nbm-form-notice nbm-form-notice-pending">
			<span class="nbm-notice-icon">ℹ️</span>
			<span><?php esc_html_e( 'Request-Based: Your reservation is subject to manager review. No immediate payment required.', 'nexora-booking-manager' ); ?></span>
		</div>

		<div class="nbm-feedback-msg" style="display:none;"></div>

		<div class="nbm-form-actions">
			<button type="submit" class="nbm-btn nbm-btn-primary nbm-submit-btn">
				<span class="nbm-btn-text"><?php esc_html_e( 'Send Booking Request', 'nexora-booking-manager' ); ?></span>
				<span class="nbm-spinner" style="display:none;"></span>
			</button>
		</div>
	</form>
</div>
