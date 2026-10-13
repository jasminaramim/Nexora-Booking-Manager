<?php
/**
 * Loop Card Preset 3: Glassmorphism Overlay (Full-Bleed Visual).
 *
 * @package Nexora_Booking_Manager
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$bar_id       = get_the_ID();
$city         = get_post_meta( $bar_id, '_nbm_city', true );
$country      = get_post_meta( $bar_id, '_nbm_country', true );
$price_range  = get_post_meta( $bar_id, '_nbm_price_range', true ) ?: '$$';
$duration     = get_post_meta( $bar_id, '_nbm_slot_duration', true ) ?: 90;
$table_cap    = get_post_meta( $bar_id, '_nbm_table_capacity', true ) ?: 25;
$location     = trim( implode( ', ', array_filter( array( $city, $country ) ) ) );
$permalink    = get_permalink( $bar_id );
$btn_label    = ! empty( $btn_text ) ? $btn_text : __( 'Book Table', 'nexora-booking-manager' );
?>
<div class="nbm-card nbm-bar-card nbm-preset-glassmorphism">
	<div class="nbm-glass-bg-wrapper">
		<a href="<?php echo esc_url( $permalink ); ?>" class="nbm-glass-media-link">
			<?php if ( has_post_thumbnail() ) : ?>
				<?php the_post_thumbnail( 'large', array( 'class' => 'nbm-glass-bg-img', 'alt' => get_the_title() ) ); ?>
			<?php else : ?>
				<div class="nbm-glass-placeholder">
					<span class="dashicons dashicons-beer"></span>
				</div>
			<?php endif; ?>
		</a>
		<div class="nbm-glass-top-pill">
			<span class="nbm-glass-range"><?php echo esc_html( $price_range ); ?></span>
			<?php if ( $location ) : ?>
				<span class="nbm-glass-dot">•</span>
				<span class="nbm-glass-city"><?php echo esc_html( $city ? $city : $location ); ?></span>
			<?php endif; ?>
		</div>
	</div>

	<!-- Frosted Glass Card Box -->
	<div class="nbm-glass-frosted-panel">
		<h3 class="nbm-card-title nbm-glass-title">
			<a href="<?php echo esc_url( $permalink ); ?>"><?php the_title(); ?></a>
		</h3>

		<div class="nbm-glass-meta-row">
			<span class="nbm-glass-stat">⏱ <?php echo absint( $duration ); ?>m</span>
			<span class="nbm-glass-stat">🪑 <?php echo absint( $table_cap ); ?> seats</span>
		</div>

		<div class="nbm-glass-footer">
			<a href="<?php echo esc_url( $permalink ); ?>" class="nbm-btn nbm-btn-glass">
				<span><?php echo esc_html( $btn_label ); ?></span>
				<span class="nbm-glass-arrow">&nearr;</span>
			</a>
		</div>
	</div>
</div>
