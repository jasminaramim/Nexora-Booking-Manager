<?php
/**
 * Template: Bar Table Reservation Request Form.
 *
 * @package Nexora_Booking_Manager
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$instance_id   = isset( $instance_id ) ? $instance_id : uniqid();
$listing_id    = isset( $listing_id ) ? absint( $listing_id ) : 0;
$today         = current_time( 'Y-m-d' );

$selected_bar  = $listing_id ? get_post( $listing_id ) : null;
$price_range   = $selected_bar ? get_post_meta( $listing_id, '_nbm_price_range', true ) : '$$';
$max_party     = $selected_bar ? get_post_meta( $listing_id, '_nbm_max_party_size', true ) : 8;
$duration      = $selected_bar ? get_post_meta( $listing_id, '_nbm_slot_duration', true ) : 90;
$opening_hours = $selected_bar ? get_post_meta( $listing_id, '_nbm_opening_hours', true ) : array();

$phone_required = '1' === NBM_Settings::get( 'require_phone', '0' );
?>
<div class="nbm-booking-card nbm-bar-form-box" id="nbm-form-container-<?php echo esc_attr( $instance_id ); ?>">
	<div class="nbm-form-header">
		<h3 class="nbm-form-title"><?php esc_html_e( 'Reserve a Table', 'nexora-booking-manager' ); ?></h3>
		<?php if ( $selected_bar ) : ?>
			<div class="nbm-form-subtitle">
				<span class="nbm-venue-name"><?php echo esc_html( $selected_bar->post_title ); ?></span>
				<span class="nbm-venue-rate"> — <strong><?php echo esc_html( $price_range ); ?></strong></span>
			</div>
		<?php else : ?>
			<p class="nbm-form-subtitle"><?php esc_html_e( 'Choose a venue and your desired reservation time.', 'nexora-booking-manager' ); ?></p>
		<?php endif; ?>
	</div>

	<form class="nbm-ajax-booking-form" id="nbm-bar-form-<?php echo esc_attr( $instance_id ); ?>" method="post">
		<input type="hidden" name="action" value="nbm_submit_booking" />
		<input type="hidden" name="listing_type" value="bar" />
		<input type="hidden" name="nonce" value="<?php echo esc_attr( wp_create_nonce( 'nbm_frontend_nonce' ) ); ?>" />
		
		<!-- Anti-spam Honeypot -->
		<div style="display:none !important;" aria-hidden="true">
			<label for="nbm_hp_website_bar_<?php echo esc_attr( $instance_id ); ?>">Leave blank</label>
			<input type="text" id="nbm_hp_website_bar_<?php echo esc_attr( $instance_id ); ?>" name="nbm_hp_website" tabindex="-1" autocomplete="off" />
		</div>

		<?php if ( ! $selected_bar ) : ?>
			<div class="nbm-form-field">
				<label for="nbm_bar_id_<?php echo esc_attr( $instance_id ); ?>" class="nbm-label">
					<?php esc_html_e( 'Select Venue / Bar', 'nexora-booking-manager' ); ?> <span class="nbm-req">*</span>
				</label>
				<select name="listing_id" id="nbm_bar_id_<?php echo esc_attr( $instance_id ); ?>" class="nbm-input nbm-bar-selector" required>
					<option value=""><?php esc_html_e( '— Choose a Bar / Lounge —', 'nexora-booking-manager' ); ?></option>
					<?php
					$bars_query = new WP_Query( array( 'post_type' => 'nbm_bar', 'post_status' => 'publish', 'posts_per_page' => 100, 'orderby' => 'title', 'order' => 'ASC' ) );
					while ( $bars_query->have_posts() ) :
						$bars_query->the_post();
						$b_range = get_post_meta( get_the_ID(), '_nbm_price_range', true );
						$b_party = get_post_meta( get_the_ID(), '_nbm_max_party_size', true );
						?>
						<option value="<?php echo esc_attr( get_the_ID() ); ?>" data-max-party="<?php echo esc_attr( $b_party ? $b_party : 8 ); ?>">
							<?php the_title(); ?> (<?php echo esc_html( $b_range ); ?>)
						</option>
					<?php endwhile; wp_reset_postdata(); ?>
				</select>
			</div>
		<?php else : ?>
			<input type="hidden" name="listing_id" value="<?php echo esc_attr( $listing_id ); ?>" />
		<?php endif; ?>

		<div class="nbm-form-row nbm-row-datetime">
			<div class="nbm-form-field">
				<label for="nbm_bar_date_<?php echo esc_attr( $instance_id ); ?>" class="nbm-label">
					<?php esc_html_e( 'Reservation Date', 'nexora-booking-manager' ); ?> <span class="nbm-req">*</span>
				</label>
				<input type="date" id="nbm_bar_date_<?php echo esc_attr( $instance_id ); ?>" name="booking_date" class="nbm-input nbm-date-picker" min="<?php echo esc_attr( $today ); ?>" value="<?php echo esc_attr( $today ); ?>" required />
			</div>

			<div class="nbm-form-field">
				<label for="nbm_bar_time_<?php echo esc_attr( $instance_id ); ?>" class="nbm-label">
					<?php esc_html_e( 'Time Slot', 'nexora-booking-manager' ); ?> <span class="nbm-req">*</span>
				</label>
				<input type="time" id="nbm_bar_time_<?php echo esc_attr( $instance_id ); ?>" name="booking_time" class="nbm-input nbm-time-picker" value="19:00" required />
				<small class="nbm-field-hint"><?php echo sprintf( esc_html__( 'Standard table duration: %d minutes', 'nexora-booking-manager' ), absint( $duration ? $duration : 90 ) ); ?></small>
			</div>
		</div>

		<div class="nbm-form-field">
			<label for="nbm_bar_guests_<?php echo esc_attr( $instance_id ); ?>" class="nbm-label">
				<?php esc_html_e( 'Party Size (Guests)', 'nexora-booking-manager' ); ?> <span class="nbm-req">*</span>
			</label>
			<select name="guest_count" id="nbm_bar_guests_<?php echo esc_attr( $instance_id ); ?>" class="nbm-input" required>
				<?php for ( $i = 1; $i <= max( 1, intval( $max_party ) ); $i++ ) : ?>
					<option value="<?php echo esc_attr( $i ); ?>" <?php selected( $i, 2 ); ?>>
						<?php echo sprintf( esc_html( _n( '%d Person', '%d People', $i, 'nexora-booking-manager' ) ), $i ); ?>
					</option>
				<?php endfor; ?>
			</select>
		</div>

		<div class="nbm-form-row nbm-row-contact">
			<div class="nbm-form-field">
				<label for="nbm_bar_name_<?php echo esc_attr( $instance_id ); ?>" class="nbm-label">
					<?php esc_html_e( 'Your Name', 'nexora-booking-manager' ); ?> <span class="nbm-req">*</span>
				</label>
				<input type="text" id="nbm_bar_name_<?php echo esc_attr( $instance_id ); ?>" name="guest_name" class="nbm-input" placeholder="e.g. Jane Smith" required />
			</div>

			<div class="nbm-form-field">
				<label for="nbm_bar_email_<?php echo esc_attr( $instance_id ); ?>" class="nbm-label">
					<?php esc_html_e( 'Email Address', 'nexora-booking-manager' ); ?> <span class="nbm-req">*</span>
				</label>
				<input type="email" id="nbm_bar_email_<?php echo esc_attr( $instance_id ); ?>" name="guest_email" class="nbm-input" placeholder="e.g. jane@example.com" required />
			</div>
		</div>

		<div class="nbm-form-field">
			<label for="nbm_bar_phone_<?php echo esc_attr( $instance_id ); ?>" class="nbm-label">
				<?php esc_html_e( 'Phone Number', 'nexora-booking-manager' ); ?> <?php if ( $phone_required ) : ?><span class="nbm-req">*</span><?php else : ?><span class="nbm-opt">(<?php esc_html_e( 'Optional', 'nexora-booking-manager' ); ?>)</span><?php endif; ?>
			</label>
			<input type="tel" id="nbm_bar_phone_<?php echo esc_attr( $instance_id ); ?>" name="guest_phone" class="nbm-input" placeholder="+1 (555) 000-0000" <?php echo $phone_required ? 'required' : ''; ?> />
		</div>

		<div class="nbm-form-field">
			<label for="nbm_bar_msg_<?php echo esc_attr( $instance_id ); ?>" class="nbm-label">
				<?php esc_html_e( 'Special Requests (Occasion, Seating preference, etc.)', 'nexora-booking-manager' ); ?> <span class="nbm-opt">(<?php esc_html_e( 'Optional', 'nexora-booking-manager' ); ?>)</span>
			</label>
			<textarea id="nbm_bar_msg_<?php echo esc_attr( $instance_id ); ?>" name="guest_message" class="nbm-textarea" rows="2" placeholder="<?php esc_attr_e( 'e.g. Birthday celebration, outdoor booth, or cocktail requests...', 'nexora-booking-manager' ); ?>"></textarea>
		</div>

		<div class="nbm-form-notice nbm-form-notice-pending">
			<span class="nbm-notice-icon">ℹ️</span>
			<span><?php esc_html_e( 'Requests are confirmed by venue staff according to table availability. You will receive an email upon confirmation.', 'nexora-booking-manager' ); ?></span>
		</div>

		<div class="nbm-feedback-msg" style="display:none;"></div>

		<div class="nbm-form-actions">
			<button type="submit" class="nbm-btn nbm-btn-primary nbm-submit-btn">
				<span class="nbm-btn-text"><?php esc_html_e( 'Request Table Reservation', 'nexora-booking-manager' ); ?></span>
				<span class="nbm-spinner" style="display:none;"></span>
			</button>
		</div>
	</form>
</div>
