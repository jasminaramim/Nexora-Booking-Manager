<?php
/**
 * Loop Card Preset 6: Vintage Speakeasy & Historic Heritage Hotel.
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
$location   = trim( implode( ', ', array_filter( array( $city, $country ) ) ) );
$permalink  = get_permalink( $hotel_id );
$btn_label  = ! empty( $btn_text ) ? $btn_text : __( 'Reserve Chamber', 'nexora-booking-manager' );
?>
<div class="nbm-card nbm-hotel-card nbm-preset-vintage">
	<div class="nbm-vintage-seal">
		<span class="nbm-vintage-seal-ring">★</span>
		<span class="nbm-vintage-seal-tier"><?php echo esc_html( '' !== $price ? NBM_Settings::format_price( $price ) : 'EST.' ); ?></span>
	</div>
	<div class="nbm-card-media nbm-vintage-media">
		<a href="<?php echo esc_url( $permalink ); ?>" class="nbm-media-link">
			<?php if ( has_post_thumbnail() ) : ?>
				<?php the_post_thumbnail( 'medium_large', array( 'class' => 'nbm-card-img', 'alt' => get_the_title() ) ); ?>
			<?php else : ?>
				<div class="nbm-card-placeholder nbm-vintage-placeholder">
					<span class="dashicons dashicons-building"></span>
				</div>
			<?php endif; ?>
		</a>
	</div>

	<div class="nbm-card-body nbm-vintage-body">
		<div class="nbm-card-location nbm-vintage-location">
			<span>✦ <?php echo esc_html( $location ? strtoupper( $location ) : 'HISTORIC QUARTER' ); ?> ✦</span>
		</div>

		<h3 class="nbm-card-title nbm-vintage-title">
			<a href="<?php echo esc_url( $permalink ); ?>"><?php the_title(); ?></a>
		</h3>

		<div class="nbm-vintage-divider">
			<span class="nbm-vintage-line"></span>
			<span class="nbm-vintage-diamond">♦</span>
			<span class="nbm-vintage-line"></span>
		</div>

		<div class="nbm-card-excerpt nbm-vintage-excerpt">
			<?php echo esc_html( wp_trim_words( get_the_excerpt(), 14 ) ); ?>
		</div>

		<div class="nbm-card-footer nbm-vintage-footer">
			<div class="nbm-meta-stats nbm-vintage-stats">
				<span>🗝 <?php echo sprintf( esc_html__( 'Up to %d guests', 'nexora-booking-manager' ), absint( $max_guests ) ); ?></span>
				<span>🏛 Heritage</span>
			</div>
			<a href="<?php echo esc_url( $permalink ); ?>" class="nbm-btn nbm-btn-vintage">
				<?php echo esc_html( $btn_label ); ?>
			</a>
		</div>
	</div>
</div>
