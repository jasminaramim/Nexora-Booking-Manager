<?php
/**
 * Loop Card Preset 1: Modern Sophisticate (Hotel).
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
$btn_label  = ! empty( $btn_text ) ? $btn_text : __( 'Book Stay', 'nexora-booking-manager' );
$amenities  = get_the_terms( $hotel_id, 'nbm_amenity' );
?>
<div class="nbm-card nbm-hotel-card nbm-preset-modern">
	<div class="nbm-card-media">
		<a href="<?php echo esc_url( $permalink ); ?>" class="nbm-media-link">
			<?php if ( has_post_thumbnail() ) : ?>
				<?php the_post_thumbnail( 'medium_large', array( 'class' => 'nbm-card-img', 'alt' => get_the_title() ) ); ?>
			<?php else : ?>
				<div class="nbm-card-placeholder">
					<span class="dashicons dashicons-building"></span>
				</div>
			<?php endif; ?>
		</a>
		<?php if ( '' !== $price ) : ?>
			<div class="nbm-price-badge">
				<?php if ( $price_pfx ) : ?><span class="nbm-price-prefix"><?php echo esc_html( $price_pfx ); ?></span> <?php endif; ?>
				<strong class="nbm-price-amount"><?php echo esc_html( NBM_Settings::format_price( $price ) ); ?></strong>
				<span class="nbm-price-period">/<?php esc_html_e( 'night', 'nexora-booking-manager' ); ?></span>
			</div>
		<?php endif; ?>
	</div>

	<div class="nbm-card-body">
		<?php if ( $location ) : ?>
			<div class="nbm-card-location">
				<span class="nbm-icon-pin">📍</span>
				<span><?php echo esc_html( $location ); ?></span>
			</div>
		<?php endif; ?>

		<h3 class="nbm-card-title">
			<a href="<?php echo esc_url( $permalink ); ?>"><?php the_title(); ?></a>
		</h3>

		<div class="nbm-card-excerpt">
			<?php echo esc_html( wp_trim_words( get_the_excerpt(), 14 ) ); ?>
		</div>

		<?php if ( ! empty( $amenities ) && ! is_wp_error( $amenities ) ) : ?>
			<div class="nbm-card-amenities">
				<?php
				$shown = array_slice( $amenities, 0, 3 );
				foreach ( $shown as $am ) :
				?>
					<span class="nbm-amenity-tag"><?php echo esc_html( $am->name ); ?></span>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<div class="nbm-card-footer">
			<div class="nbm-capacity-info">
				<span class="nbm-icon-guests">👥</span>
				<span><?php echo sprintf( esc_html__( 'Up to %d guests', 'nexora-booking-manager' ), absint( $max_guests ) ); ?></span>
			</div>
			<a href="<?php echo esc_url( $permalink ); ?>" class="nbm-btn nbm-btn-primary">
				<span><?php echo esc_html( $btn_label ); ?></span>
				<span class="nbm-btn-arrow">&rarr;</span>
			</a>
		</div>
	</div>
</div>
