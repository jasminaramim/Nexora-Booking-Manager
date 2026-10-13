<?php
/**
 * Loop Card Preset 7: Horizontal Executive Suite & Bistro Lounge.
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
$table_cap    = get_post_meta( $bar_id, '_nbm_table_capacity', true ) ?: 30;
$location     = trim( implode( ', ', array_filter( array( $city, $country ) ) ) );
$permalink    = get_permalink( $bar_id );
$btn_label    = ! empty( $btn_text ) ? $btn_text : __( 'Reserve Table', 'nexora-booking-manager' );
$amenities    = get_the_terms( $bar_id, 'nbm_amenity' );
?>
<div class="nbm-card nbm-bar-card nbm-preset-bistro_horizontal">
	<div class="nbm-card-media nbm-horizontal-media">
		<a href="<?php echo esc_url( $permalink ); ?>" class="nbm-media-link">
			<?php if ( has_post_thumbnail() ) : ?>
				<?php the_post_thumbnail( 'medium_large', array( 'class' => 'nbm-card-img', 'alt' => get_the_title() ) ); ?>
			<?php else : ?>
				<div class="nbm-card-placeholder">
					<span class="dashicons dashicons-coffee"></span>
				</div>
			<?php endif; ?>
		</a>
		<span class="nbm-badge-pill nbm-horizontal-pill"><?php echo esc_html( $price_range ); ?></span>
	</div>

	<div class="nbm-card-body nbm-horizontal-body">
		<div class="nbm-horizontal-header">
			<?php if ( $location ) : ?>
				<div class="nbm-card-location">
					<span>📍 <?php echo esc_html( $location ); ?></span>
				</div>
			<?php endif; ?>
			<h3 class="nbm-card-title nbm-horizontal-title">
				<a href="<?php echo esc_url( $permalink ); ?>"><?php the_title(); ?></a>
			</h3>
		</div>

		<div class="nbm-card-excerpt nbm-horizontal-excerpt">
			<?php echo esc_html( wp_trim_words( get_the_excerpt(), 18 ) ); ?>
		</div>

		<div class="nbm-horizontal-meta-grid">
			<div class="nbm-h-meta-item">
				<span class="nbm-h-meta-lbl"><?php esc_html_e( 'Slot Window', 'nexora-booking-manager' ); ?></span>
				<span class="nbm-h-meta-val">⏱ <?php echo absint( $duration ); ?> mins</span>
			</div>
			<div class="nbm-h-meta-item">
				<span class="nbm-h-meta-lbl"><?php esc_html_e( 'Seating Area', 'nexora-booking-manager' ); ?></span>
				<span class="nbm-h-meta-val">🪑 <?php echo absint( $table_cap ); ?> seats</span>
			</div>
			<div class="nbm-h-meta-item">
				<span class="nbm-h-meta-lbl"><?php esc_html_e( 'Experience', 'nexora-booking-manager' ); ?></span>
				<span class="nbm-h-meta-val">🍸 <?php echo esc_html( $price_range ); ?> Dining</span>
			</div>
		</div>

		<div class="nbm-card-footer nbm-horizontal-footer">
			<?php if ( ! empty( $amenities ) && ! is_wp_error( $amenities ) ) : ?>
				<div class="nbm-card-tags">
					<?php foreach ( array_slice( $amenities, 0, 2 ) as $am ) : ?>
						<span class="nbm-tag-pill"><?php echo esc_html( $am->name ); ?></span>
					<?php endforeach; ?>
				</div>
			<?php else : ?>
				<div></div>
			<?php endif; ?>
			<a href="<?php echo esc_url( $permalink ); ?>" class="nbm-btn nbm-btn-primary nbm-horizontal-btn">
				<span><?php echo esc_html( $btn_label ); ?></span>
				<span class="nbm-btn-arrow">&rarr;</span>
			</a>
		</div>
	</div>
</div>
