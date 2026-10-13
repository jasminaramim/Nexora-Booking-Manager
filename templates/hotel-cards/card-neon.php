<?php
/**
 * Loop Card Preset 5: Neon Cyberpunk & Electric Boutique (Hotel).
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
$btn_label  = ! empty( $btn_text ) ? $btn_text : __( 'Reserve Neon Stay', 'nexora-booking-manager' );
$amenities  = get_the_terms( $hotel_id, 'nbm_amenity' );
?>
<div class="nbm-card nbm-hotel-card nbm-preset-neon">
	<div class="nbm-neon-glow-border"></div>
	<div class="nbm-card-media nbm-neon-media">
		<a href="<?php echo esc_url( $permalink ); ?>" class="nbm-media-link">
			<?php if ( has_post_thumbnail() ) : ?>
				<?php the_post_thumbnail( 'medium_large', array( 'class' => 'nbm-card-img', 'alt' => get_the_title() ) ); ?>
			<?php else : ?>
				<div class="nbm-card-placeholder nbm-neon-placeholder">
					<span class="dashicons dashicons-building"></span>
				</div>
			<?php endif; ?>
		</a>
		<div class="nbm-neon-badge-pill">
			<span class="nbm-neon-pulse-dot"></span>
			<span class="nbm-neon-tag-text"><?php echo esc_html( '' !== $price ? NBM_Settings::format_price( $price ) . '/nt' : '⚡ ELECTRIC BOUTIQUE' ); ?></span>
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
				<?php
				$shown = array_slice( $amenities, 0, 2 );
				foreach ( $shown as $am ) :
				?>
					<span class="nbm-neon-tag"><?php echo esc_html( $am->name ); ?></span>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<div class="nbm-card-footer nbm-neon-footer">
			<div class="nbm-meta-stats nbm-neon-stats">
				<span>👥 <?php echo sprintf( esc_html__( '%d Guests', 'nexora-booking-manager' ), absint( $max_guests ) ); ?></span>
				<span>⚡ Rooftop Pool</span>
			</div>
			<a href="<?php echo esc_url( $permalink ); ?>" class="nbm-btn nbm-btn-neon">
				<span><?php echo esc_html( $btn_label ); ?></span>
				<span class="nbm-neon-arrow">&rarr;</span>
			</a>
		</div>
	</div>
</div>
