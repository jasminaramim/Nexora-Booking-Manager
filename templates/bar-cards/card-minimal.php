<?php
/**
 * Loop Card Preset 4: Minimalist Editorial / Bistro.
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
$btn_label    = ! empty( $btn_text ) ? $btn_text : __( 'Reserve', 'nexora-booking-manager' );
?>
<div class="nbm-card nbm-bar-card nbm-preset-minimal">
	<div class="nbm-card-media nbm-minimal-media">
		<a href="<?php echo esc_url( $permalink ); ?>" class="nbm-media-link">
			<?php if ( has_post_thumbnail() ) : ?>
				<?php the_post_thumbnail( 'medium_large', array( 'class' => 'nbm-card-img', 'alt' => get_the_title() ) ); ?>
			<?php else : ?>
				<div class="nbm-card-placeholder">
					<span class="dashicons dashicons-beer"></span>
				</div>
			<?php endif; ?>
		</a>
		<span class="nbm-minimal-tag"><?php echo esc_html( $price_range ); ?></span>
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

		<div class="nbm-minimal-specs">
			<div class="nbm-spec-item">
				<span class="nbm-spec-label"><?php esc_html_e( 'Slot', 'nexora-booking-manager' ); ?></span>
				<span class="nbm-spec-val"><?php echo absint( $duration ); ?>m</span>
			</div>
			<div class="nbm-spec-item">
				<span class="nbm-spec-label"><?php esc_html_e( 'Capacity', 'nexora-booking-manager' ); ?></span>
				<span class="nbm-spec-val"><?php echo absint( $table_cap ); ?> seats</span>
			</div>
			<div class="nbm-spec-item">
				<span class="nbm-spec-label"><?php esc_html_e( 'Tier', 'nexora-booking-manager' ); ?></span>
				<span class="nbm-spec-val"><?php echo esc_html( $price_range ); ?></span>
			</div>
		</div>

		<div class="nbm-card-footer nbm-minimal-footer">
			<a href="<?php echo esc_url( $permalink ); ?>" class="nbm-btn nbm-btn-outline nbm-btn-minimal">
				<?php echo esc_html( $btn_label ); ?>
			</a>
		</div>
	</div>
</div>
