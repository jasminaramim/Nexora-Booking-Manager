<?php
/**
 * Loop Card Preset 3: Glassmorphism Overlay (Hotel).
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
$price_pfx  = get_post_meta( $hotel_id, '_nbm_price_prefix', true );
$max_guests = get_post_meta( $hotel_id, '_nbm_max_guests', true ) ?: 2;
$location   = trim( implode( ', ', array_filter( array( $city, $country ) ) ) );
$permalink  = get_permalink( $hotel_id );
$btn_label  = ! empty( $btn_text ) ? $btn_text : __( 'Book Suite', 'nexora-booking-manager' );
?>
<div class="nbm-card nbm-hotel-card nbm-preset-glassmorphism">
	<div class="nbm-glass-bg-wrapper">
		<a href="<?php echo esc_url( $permalink ); ?>" class="nbm-glass-media-link">
			<?php if ( has_post_thumbnail() ) : ?>
				<?php the_post_thumbnail( 'large', array( 'class' => 'nbm-glass-bg-img', 'alt' => get_the_title() ) ); ?>
			<?php else : ?>
				<div class="nbm-glass-placeholder">
					<span class="dashicons dashicons-building"></span>
				</div>
			<?php endif; ?>
		</a>
		<div class="nbm-glass-top-pill">
			<?php if ( '' !== $price ) : ?>
				<span class="nbm-glass-range"><?php echo esc_html( NBM_Settings::format_price( $price ) ); ?>/nt</span>
			<?php endif; ?>
			<?php if ( $location ) : ?>
				<span class="nbm-glass-dot">•</span>
				<span class="nbm-glass-city"><?php echo esc_html( $location ); ?></span>
			<?php endif; ?>
		</div>
	</div>

	<div class="nbm-glass-frosted-panel">
		<h3 class="nbm-card-title nbm-glass-title">
			<a href="<?php echo esc_url( $permalink ); ?>"><?php the_title(); ?></a>
		</h3>

		<div class="nbm-glass-meta-row">
			<span>👥 <?php echo sprintf( esc_html__( 'Up to %d guests', 'nexora-booking-manager' ), absint( $max_guests ) ); ?></span>
			<span>✨ Concierge Included</span>
		</div>

		<div class="nbm-glass-footer">
			<a href="<?php echo esc_url( $permalink ); ?>" class="nbm-btn nbm-btn-glass">
				<span><?php echo esc_html( $btn_label ); ?></span>
				<span class="nbm-glass-arrow">&nearr;</span>
			</a>
		</div>
	</div>
</div>
