<?php
/**
 * Loop Card Preset 4: Minimalist Editorial (Hotel).
 *
 * @package Nexora_Booking_Manager
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$hotel_id   = get_the_ID();
$city       = get_post_meta( $hotel_id, '_nbm_city', true );
$country    = get_post_meta( $hotel_id, '_nbm_country', true );
$price      = get_post_meta( $hotel_id, '_nbm_price_per_night', true );
$max_guests = get_post_meta( $hotel_id, '_nbm_max_guests', true ) ?: 2;
$checkin    = get_post_meta( $hotel_id, '_nbm_checkin_time', true ) ?: '3:00 PM';
$location   = trim( implode( ', ', array_filter( array( $city, $country ) ) ) );
$permalink  = get_permalink( $hotel_id );
$btn_label  = ! empty( $btn_text ) ? $btn_text : __( 'View Property', 'nexora-booking-manager' );
?>
<div class="nbm-card nbm-hotel-card nbm-preset-minimal">
	<div class="nbm-card-media nbm-minimal-media">
		<a href="<?php echo esc_url( $permalink ); ?>" class="nbm-media-link">
			<?php if ( has_post_thumbnail() ) : ?>
				<?php the_post_thumbnail( 'medium_large', array( 'class' => 'nbm-card-img', 'alt' => get_the_title() ) ); ?>
			<?php else : ?>
				<div class="nbm-card-placeholder">
					<span class="dashicons dashicons-building"></span>
				</div>
			<?php endif; ?>
		</a>
		<?php if ( '' !== $price ) : ?>
			<span class="nbm-minimal-tag"><?php echo esc_html( NBM_Settings::format_price( $price ) ); ?>/nt</span>
		<?php endif; ?>
	</div>

	<div class="nbm-card-body nbm-minimal-body">
		<div class="nbm-minimal-header">
			<h3 class="nbm-card-title nbm-minimal-title">
				<a href="<?php echo esc_url( $permalink ); ?>"><?php the_title(); ?></a>
			</h3>
			<?php if ( $location ) : ?>
				<span class="nbm-minimal-location"><?php echo esc_html( $location ); ?></span>
			<?php endif; ?>
		</div>

		<div class="nbm-card-excerpt">
			<?php echo esc_html( wp_trim_words( get_the_excerpt(), 12 ) ); ?>
		</div>

		<div class="nbm-minimal-specs">
			<div class="nbm-spec-item">
				<span class="nbm-spec-label"><?php esc_html_e( 'Rate', 'nexora-booking-manager' ); ?></span>
				<span class="nbm-spec-val"><?php echo esc_html( NBM_Settings::format_price( $price ) ); ?></span>
			</div>
			<div class="nbm-spec-item">
				<span class="nbm-spec-label"><?php esc_html_e( 'Capacity', 'nexora-booking-manager' ); ?></span>
				<span class="nbm-spec-val"><?php echo sprintf( esc_html__( '%d guests', 'nexora-booking-manager' ), absint( $max_guests ) ); ?></span>
			</div>
			<div class="nbm-spec-item">
				<span class="nbm-spec-label"><?php esc_html_e( 'Check-in', 'nexora-booking-manager' ); ?></span>
				<span class="nbm-spec-val"><?php echo esc_html( $checkin ); ?></span>
			</div>
		</div>

		<div class="nbm-card-footer nbm-minimal-footer">
			<a href="<?php echo esc_url( $permalink ); ?>" class="nbm-btn nbm-btn-outline nbm-btn-minimal">
				<?php echo esc_html( $btn_label ); ?>
			</a>
		</div>
	</div>
</div>
