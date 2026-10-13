<?php
/**
 * Loop Card Preset 2: Luxury VIP & Nightlife.
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
$duration     = get_post_meta( $bar_id, '_nbm_slot_duration', true ) ?: 90;
$max_party    = get_post_meta( $bar_id, '_nbm_max_party_size', true ) ?: 8;
$instructions = get_post_meta( $bar_id, '_nbm_reservation_instructions', true );
$location     = trim( implode( ', ', array_filter( array( $city, $country ) ) ) );
$permalink    = get_permalink( $bar_id );
$btn_label    = ! empty( $btn_text ) ? $btn_text : __( 'Book VIP Table', 'nexora-booking-manager' );
?>
<div class="nbm-card nbm-bar-card nbm-preset-luxury">
	<div class="nbm-luxury-glow"></div>
	<div class="nbm-card-media">
		<a href="<?php echo esc_url( $permalink ); ?>" class="nbm-media-link">
			<?php if ( has_post_thumbnail() ) : ?>
				<?php the_post_thumbnail( 'medium_large', array( 'class' => 'nbm-card-img', 'alt' => get_the_title() ) ); ?>
			<?php else : ?>
				<div class="nbm-card-placeholder nbm-luxury-placeholder">
					<span class="dashicons dashicons-beer"></span>
				</div>
			<?php endif; ?>
		</a>
		<div class="nbm-luxury-badge">
			<span class="nbm-luxury-crown">👑</span>
			<span class="nbm-luxury-tier"><?php echo esc_html( $price_range ); ?> VIP</span>
		</div>
	</div>

	<div class="nbm-card-body">
		<?php if ( $location ) : ?>
			<div class="nbm-card-location nbm-luxury-location">
				<span class="nbm-sparkle">✦</span>
				<span><?php echo esc_html( $location ); ?></span>
			</div>
		<?php endif; ?>

		<h3 class="nbm-card-title nbm-luxury-title">
			<a href="<?php echo esc_url( $permalink ); ?>"><?php the_title(); ?></a>
		</h3>

		<div class="nbm-card-excerpt nbm-luxury-excerpt">
			<?php echo esc_html( wp_trim_words( get_the_excerpt(), 13 ) ); ?>
		</div>

		<?php if ( $instructions ) : ?>
			<div class="nbm-luxury-instruction-peek">
				<span class="nbm-info-icon">👔</span>
				<span><?php echo esc_html( wp_trim_words( $instructions, 7 ) ); ?></span>
			</div>
		<?php endif; ?>

		<div class="nbm-card-footer nbm-luxury-footer">
			<div class="nbm-meta-stats nbm-luxury-stats">
				<span>🍾 <?php echo sprintf( esc_html__( 'Up to %d guests', 'nexora-booking-manager' ), absint( $max_party ) ); ?></span>
				<span>⏱ <?php echo absint( $duration ); ?>m</span>
			</div>
			<a href="<?php echo esc_url( $permalink ); ?>" class="nbm-btn nbm-btn-luxury">
				<span><?php echo esc_html( $btn_label ); ?></span>
				<span class="nbm-luxury-arrow">✦</span>
			</a>
		</div>
	</div>
</div>
