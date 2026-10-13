<?php
/**
 * Loop Card Preset 6: Vintage Speakeasy & Prohibition Craft Lounge.
 *
 * @package Nexora_Booking_Manager
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$bar_id       = get_the_ID();
$city         = get_post_meta( $bar_id, '_nbm_city', true );
$country      = get_post_meta( $bar_id, '_nbm_country', true );
$price_range  = get_post_meta( $bar_id, '_nbm_price_range', true ) ?: '$$$';
$duration     = get_post_meta( $bar_id, '_nbm_slot_duration', true ) ?: 120;
$table_cap    = get_post_meta( $bar_id, '_nbm_table_capacity', true ) ?: 16;
$location     = trim( implode( ', ', array_filter( array( $city, $country ) ) ) );
$permalink    = get_permalink( $bar_id );
$btn_label    = ! empty( $btn_text ) ? $btn_text : __( 'Request Table', 'nexora-booking-manager' );
$amenities    = get_the_terms( $bar_id, 'nbm_amenity' );
?>
<div class="nbm-card nbm-bar-card nbm-preset-vintage">
	<div class="nbm-vintage-frame-corner top-left"></div>
	<div class="nbm-vintage-frame-corner top-right"></div>
	<div class="nbm-vintage-frame-corner bottom-left"></div>
	<div class="nbm-vintage-frame-corner bottom-right"></div>

	<div class="nbm-card-media nbm-vintage-media">
		<a href="<?php echo esc_url( $permalink ); ?>" class="nbm-media-link">
			<?php if ( has_post_thumbnail() ) : ?>
				<?php the_post_thumbnail( 'medium_large', array( 'class' => 'nbm-card-img nbm-vintage-img', 'alt' => get_the_title() ) ); ?>
			<?php else : ?>
				<div class="nbm-card-placeholder nbm-vintage-placeholder">
					<span class="dashicons dashicons-products"></span>
				</div>
			<?php endif; ?>
		</a>
		<div class="nbm-vintage-seal">
			<span class="nbm-vintage-seal-ring">★</span>
			<span class="nbm-vintage-seal-tier"><?php echo esc_html( $price_range ); ?></span>
		</div>
	</div>

	<div class="nbm-card-body nbm-vintage-body">
		<?php if ( $location ) : ?>
			<div class="nbm-card-location nbm-vintage-location">
				<span>✦ <?php echo esc_html( strtoupper( $location ) ); ?> ✦</span>
			</div>
		<?php endif; ?>

		<h3 class="nbm-card-title nbm-vintage-title">
			<a href="<?php echo esc_url( $permalink ); ?>"><?php the_title(); ?></a>
		</h3>

		<div class="nbm-vintage-divider">
			<span class="nbm-vintage-line"></span>
			<span class="nbm-vintage-diamond">♦</span>
			<span class="nbm-vintage-line"></span>
		</div>

		<div class="nbm-card-excerpt nbm-vintage-excerpt">
			<?php echo esc_html( wp_trim_words( get_the_excerpt(), 12 ) ); ?>
		</div>

		<div class="nbm-card-footer nbm-vintage-footer">
			<div class="nbm-meta-stats nbm-vintage-stats">
				<span>🥃 <?php echo absint( $duration ); ?> min</span>
				<span>🛋 <?php echo absint( $table_cap ); ?> seats</span>
			</div>
			<a href="<?php echo esc_url( $permalink ); ?>" class="nbm-btn nbm-btn-vintage">
				<?php echo esc_html( $btn_label ); ?>
			</a>
		</div>
	</div>
</div>
