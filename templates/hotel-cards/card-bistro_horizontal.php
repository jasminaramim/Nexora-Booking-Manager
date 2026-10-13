<?php
/**
 * Loop Card Preset 7: Horizontal Executive Suite (Hotel).
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
$min_stay   = get_post_meta( $hotel_id, '_nbm_min_stay', true ) ?: 1;
$checkin    = get_post_meta( $hotel_id, '_nbm_checkin_time', true ) ?: '3:00 PM';
$location   = trim( implode( ', ', array_filter( array( $city, $country ) ) ) );
$permalink  = get_permalink( $hotel_id );
$btn_label  = ! empty( $btn_text ) ? $btn_text : __( 'Book Suite', 'nexora-booking-manager' );
$amenities  = get_the_terms( $hotel_id, 'nbm_amenity' );
?>
<div class="nbm-card nbm-hotel-card nbm-preset-bistro_horizontal">
	<div class="nbm-card-media nbm-horizontal-media">
		<a href="<?php echo esc_url( $permalink ); ?>" class="nbm-media-link">
			<?php if ( has_post_thumbnail() ) : ?>
				<?php the_post_thumbnail( 'large', array( 'class' => 'nbm-card-img', 'alt' => get_the_title() ) ); ?>
			<?php else : ?>
				<div class="nbm-card-placeholder">
					<span class="dashicons dashicons-building"></span>
				</div>
			<?php endif; ?>
		</a>
		<?php if ( '' !== $price ) : ?>
			<span class="nbm-badge-pill nbm-horizontal-pill"><?php echo esc_html( NBM_Settings::format_price( $price ) ); ?>/night</span>
		<?php endif; ?>
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
			<?php echo esc_html( wp_trim_words( get_the_excerpt(), 15 ) ); ?>
		</div>

		<div class="nbm-horizontal-meta-grid">
			<div class="nbm-h-meta-item">
				<span class="nbm-h-meta-lbl"><?php esc_html_e( 'Capacity', 'nexora-booking-manager' ); ?></span>
				<span class="nbm-h-meta-val">👥 <?php echo sprintf( esc_html__( '%d Guests', 'nexora-booking-manager' ), absint( $max_guests ) ); ?></span>
			</div>
			<div class="nbm-h-meta-item">
				<span class="nbm-h-meta-lbl"><?php esc_html_e( 'Min Stay', 'nexora-booking-manager' ); ?></span>
				<span class="nbm-h-meta-val">🌙 <?php echo sprintf( esc_html__( '%d Night(s)', 'nexora-booking-manager' ), absint( $min_stay ) ); ?></span>
			</div>
			<div class="nbm-h-meta-item">
				<span class="nbm-h-meta-lbl"><?php esc_html_e( 'Check-in', 'nexora-booking-manager' ); ?></span>
				<span class="nbm-h-meta-val">⏱ <?php echo esc_html( $checkin ); ?></span>
			</div>
		</div>

		<div class="nbm-card-footer nbm-horizontal-footer">
			<div class="nbm-card-tags">
				<?php if ( ! empty( $amenities ) && ! is_wp_error( $amenities ) ) : ?>
					<?php foreach ( array_slice( $amenities, 0, 2 ) as $am ) : ?>
						<span class="nbm-tag-pill"><?php echo esc_html( $am->name ); ?></span>
					<?php endforeach; ?>
				<?php else : ?>
					<span class="nbm-tag-pill"><?php esc_html_e( 'Luxury Suite', 'nexora-booking-manager' ); ?></span>
				<?php endif; ?>
			</div>
			<a href="<?php echo esc_url( $permalink ); ?>" class="nbm-btn nbm-btn-primary nbm-horizontal-btn">
				<span><?php echo esc_html( $btn_label ); ?></span>
				<span class="nbm-btn-arrow">&rarr;</span>
			</a>
		</div>
	</div>
</div>
