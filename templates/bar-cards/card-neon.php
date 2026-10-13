<?php
/**
 * Loop Card Preset 5: Neon Cyberpunk & Electric Nightclub.
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
$table_cap    = get_post_meta( $bar_id, '_nbm_table_capacity', true ) ?: 20;
$location     = trim( implode( ', ', array_filter( array( $city, $country ) ) ) );
$permalink    = get_permalink( $bar_id );
$btn_label    = ! empty( $btn_text ) ? $btn_text : __( 'Reserve Neon Lounge', 'nexora-booking-manager' );
$amenities    = get_the_terms( $bar_id, 'nbm_amenity' );
?>
<div class="nbm-card nbm-bar-card nbm-preset-neon">
	<div class="nbm-neon-glow-border"></div>
	<div class="nbm-card-media nbm-neon-media">
		<a href="<?php echo esc_url( $permalink ); ?>" class="nbm-media-link">
			<?php if ( has_post_thumbnail() ) : ?>
				<?php the_post_thumbnail( 'medium_large', array( 'class' => 'nbm-card-img', 'alt' => get_the_title() ) ); ?>
			<?php else : ?>
				<div class="nbm-card-placeholder nbm-neon-placeholder">
					<span class="dashicons dashicons-format-audio"></span>
				</div>
			<?php endif; ?>
		</a>
		<div class="nbm-neon-badge-pill">
			<span class="nbm-neon-pulse-dot"></span>
			<span class="nbm-neon-tag-text"><?php echo esc_html( $price_range ); ?> • ELECTRIC</span>
		</div>
	</div>

	<div class="nbm-card-body nbm-neon-body">
		<?php if ( $location ) : ?>
			<div class="nbm-card-location nbm-neon-location">
				<span class="nbm-neon-icon">⚡</span>
				<span><?php echo esc_html( $location ); ?></span>
			</div>
		<?php endif; ?>

		<h3 class="nbm-card-title nbm-neon-title">
			<a href="<?php echo esc_url( $permalink ); ?>"><?php the_title(); ?></a>
		</h3>

		<div class="nbm-card-excerpt nbm-neon-excerpt">
			<?php echo esc_html( wp_trim_words( get_the_excerpt(), 13 ) ); ?>
		</div>

		<?php if ( ! empty( $amenities ) && ! is_wp_error( $amenities ) ) : ?>
			<div class="nbm-neon-tags">
				<?php foreach ( array_slice( $amenities, 0, 3 ) as $am ) : ?>
					<span class="nbm-neon-tag"><?php echo esc_html( $am->name ); ?></span>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<div class="nbm-card-footer nbm-neon-footer">
			<div class="nbm-meta-stats nbm-neon-stats">
				<span>⏱ <?php echo absint( $duration ); ?>m</span>
				<span>🪩 <?php echo absint( $table_cap ); ?> cap</span>
			</div>
			<a href="<?php echo esc_url( $permalink ); ?>" class="nbm-btn nbm-btn-neon">
				<span><?php echo esc_html( $btn_label ); ?></span>
				<span class="nbm-neon-arrow">&rarr;</span>
			</a>
		</div>
	</div>
</div>
