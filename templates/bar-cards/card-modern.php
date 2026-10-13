<?php
/**
 * Loop Card Preset 1: Modern Sophisticate.
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
$btn_label    = ! empty( $btn_text ) ? $btn_text : __( 'Reserve Table', 'nexora-booking-manager' );
$amenities    = get_the_terms( $bar_id, 'nbm_amenity' );

// Check opening status today
$day_name     = strtolower( gmdate( 'l', current_time( 'timestamp' ) ) );
$hours_config = get_post_meta( $bar_id, '_nbm_opening_hours', true );
$today_hours  = is_array( $hours_config ) ? ( $hours_config[ $day_name ] ?? array() ) : array();
$is_closed    = ! empty( $today_hours['closed'] );
?>
<div class="nbm-card nbm-bar-card nbm-preset-modern">
	<div class="nbm-card-media">
		<a href="<?php echo esc_url( $permalink ); ?>" class="nbm-media-link">
			<?php if ( has_post_thumbnail() ) : ?>
				<?php the_post_thumbnail( 'medium_large', array( 'class' => 'nbm-card-img', 'alt' => get_the_title() ) ); ?>
			<?php else : ?>
				<div class="nbm-card-placeholder">
					<span class="dashicons dashicons-beer"></span>
				</div>
			<?php endif; ?>
		</a>
		<div class="nbm-card-badges-top">
			<span class="nbm-badge-pill nbm-badge-price"><?php echo esc_html( $price_range ); ?></span>
			<?php if ( $is_closed ) : ?>
				<span class="nbm-badge-pill nbm-badge-status-closed"><?php esc_html_e( 'Closed Today', 'nexora-booking-manager' ); ?></span>
			<?php elseif ( ! empty( $today_hours['open'] ) ) : ?>
				<span class="nbm-badge-pill nbm-badge-status-open"><?php echo esc_html( sprintf( __( 'Open %s', 'nexora-booking-manager' ), $today_hours['open'] ) ); ?></span>
			<?php endif; ?>
		</div>
	</div>

	<div class="nbm-card-body">
		<?php if ( $location ) : ?>
			<div class="nbm-card-location">
				<span class="nbm-pin-icon">📍</span>
				<span><?php echo esc_html( $location ); ?></span>
			</div>
		<?php endif; ?>

		<h3 class="nbm-card-title">
			<a href="<?php echo esc_url( $permalink ); ?>"><?php the_title(); ?></a>
		</h3>

		<div class="nbm-card-excerpt">
			<?php echo esc_html( wp_trim_words( get_the_excerpt(), 13 ) ); ?>
		</div>

		<?php if ( ! empty( $amenities ) && ! is_wp_error( $amenities ) ) : ?>
			<div class="nbm-card-tags">
				<?php foreach ( array_slice( $amenities, 0, 3 ) as $am ) : ?>
					<span class="nbm-tag-pill"><?php echo esc_html( $am->name ); ?></span>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<div class="nbm-card-footer">
			<div class="nbm-meta-stats">
				<span title="<?php esc_attr_e( 'Slot Duration', 'nexora-booking-manager' ); ?>">⏱ <?php echo absint( $duration ); ?>m</span>
				<span title="<?php esc_attr_e( 'Seating Capacity', 'nexora-booking-manager' ); ?>">🪑 <?php echo absint( $table_cap ); ?> seats</span>
			</div>
			<a href="<?php echo esc_url( $permalink ); ?>" class="nbm-btn nbm-btn-primary">
				<span><?php echo esc_html( $btn_label ); ?></span>
				<span class="nbm-btn-arrow">&rarr;</span>
			</a>
		</div>
	</div>
</div>
