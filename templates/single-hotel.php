<?php
/**
 * Single Hotel Listing Template.
 *
 * @package Nexora_Booking_Manager
 */

get_header();

$hotel_id         = get_the_ID();
$address          = get_post_meta( $hotel_id, '_nbm_address', true );
$city             = get_post_meta( $hotel_id, '_nbm_city', true );
$state            = get_post_meta( $hotel_id, '_nbm_state', true );
$country          = get_post_meta( $hotel_id, '_nbm_country', true );
$map_url          = get_post_meta( $hotel_id, '_nbm_map_url', true );
$phone            = get_post_meta( $hotel_id, '_nbm_phone', true );
$email            = get_post_meta( $hotel_id, '_nbm_email', true );
$price_per_night  = get_post_meta( $hotel_id, '_nbm_price_per_night', true );
$price_prefix     = get_post_meta( $hotel_id, '_nbm_price_prefix', true );
$max_guests       = get_post_meta( $hotel_id, '_nbm_max_guests', true );
$min_stay         = get_post_meta( $hotel_id, '_nbm_min_stay', true );
$checkin_time     = get_post_meta( $hotel_id, '_nbm_checkin_time', true );
$checkout_time    = get_post_meta( $hotel_id, '_nbm_checkout_time', true );
$booking_rules    = get_post_meta( $hotel_id, '_nbm_booking_rules', true );
$gallery_ids_raw  = get_post_meta( $hotel_id, '_nbm_gallery_ids', true );

$location = trim( implode( ', ', array_filter( array( $address, $city, $state, $country ) ) ) );
$amenities = get_the_terms( $hotel_id, 'nbm_amenity' );

$gallery_ids = array();
if ( ! empty( $gallery_ids_raw ) ) {
	$gallery_ids = array_filter( array_map( 'absint', explode( ',', $gallery_ids_raw ) ) );
}
$single_design = sanitize_key( $_GET['nbm_single_preset'] ?? NBM_Settings::get( 'single_details_design', 'classic_hero' ) );
$valid_designs = array( 'classic_hero', 'split_screen', 'fullscreen_cinematic', 'editorial_magazine', 'tabbed_navigator' );
if ( ! in_array( $single_design, $valid_designs, true ) ) {
	$single_design = 'classic_hero';
}
?>

<div class="nbm-single-listing nbm-single-hotel nbm-single-layout-<?php echo esc_attr( $single_design ); ?>">
	<div class="nbm-container">
		
		<!-- Header / Title -->
		<div class="nbm-single-header">
			<div class="nbm-header-meta">
				<span class="nbm-badge nbm-badge-hotel"><?php esc_html_e( 'Hotel & Resort', 'nexora-booking-manager' ); ?></span>
				<?php if ( $location ) : ?>
					<span class="nbm-location-text">📍 <?php echo esc_html( $location ); ?></span>
				<?php endif; ?>
			</div>
			<h1 class="nbm-single-title"><?php the_title(); ?></h1>
		</div>

		<?php if ( 'tabbed_navigator' === $single_design ) : ?>
			<!-- Tab Navigation Bar for Design 5 -->
			<div class="nbm-tab-navbar">
				<button type="button" class="nbm-tab-nav-btn active" data-tab="overview">📋 <?php esc_html_e( 'Overview', 'nexora-booking-manager' ); ?></button>
				<button type="button" class="nbm-tab-nav-btn" data-tab="gallery">📸 <?php esc_html_e( 'Photo Gallery', 'nexora-booking-manager' ); ?></button>
				<button type="button" class="nbm-tab-nav-btn" data-tab="policies">⏱ <?php esc_html_e( 'Check-in & Rules', 'nexora-booking-manager' ); ?></button>
				<button type="button" class="nbm-tab-nav-btn" data-tab="amenities">✨ <?php esc_html_e( 'Amenities', 'nexora-booking-manager' ); ?></button>
				<button type="button" class="nbm-tab-nav-btn nbm-tab-reserve-btn" data-tab="reserve">📅 <?php esc_html_e( 'Book Stay', 'nexora-booking-manager' ); ?></button>
			</div>
		<?php endif; ?>

		<!-- Gallery Showcase -->
		<div class="nbm-gallery-showcase <?php echo ( 'tabbed_navigator' === $single_design ) ? 'nbm-tab-pane' : ''; ?>" id="nbm-tab-gallery">
			<div class="nbm-main-image">
				<?php if ( has_post_thumbnail() ) : ?>
					<?php the_post_thumbnail( 'large', array( 'class' => 'nbm-hero-img' ) ); ?>
				<?php else : ?>
					<div class="nbm-hero-placeholder">
						<span class="dashicons dashicons-building"></span>
					</div>
				<?php endif; ?>
			</div>

			<?php if ( ! empty( $gallery_ids ) ) : ?>
				<div class="nbm-thumbnails-grid">
					<?php foreach ( array_slice( $gallery_ids, 0, 4 ) as $img_id ) : ?>
						<div class="nbm-thumb-item">
							<?php echo wp_get_attachment_image( $img_id, 'medium', false, array( 'class' => 'nbm-thumb-img' ) ); ?>
						</div>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>

		<!-- Main Content Layout (Details on Left, Booking Form on Right) -->
		<div class="nbm-single-layout">
			<div class="nbm-details-column">
				
				<!-- Overview Badges -->
				<div class="nbm-features-ribbon">
					<div class="nbm-ribbon-item">
						<span class="nbm-r-label"><?php esc_html_e( 'Nightly Rate', 'nexora-booking-manager' ); ?></span>
						<span class="nbm-r-val"><?php echo esc_html( NBM_Settings::format_price( $price_per_night ) ); ?></span>
					</div>
					<div class="nbm-ribbon-item">
						<span class="nbm-r-label"><?php esc_html_e( 'Max Occupancy', 'nexora-booking-manager' ); ?></span>
						<span class="nbm-r-val"><?php echo sprintf( esc_html__( '%d Guests', 'nexora-booking-manager' ), absint( $max_guests ? $max_guests : 2 ) ); ?></span>
					</div>
					<div class="nbm-ribbon-item">
						<span class="nbm-r-label"><?php esc_html_e( 'Min Stay', 'nexora-booking-manager' ); ?></span>
						<span class="nbm-r-val"><?php echo sprintf( esc_html__( '%d Night(s)', 'nexora-booking-manager' ), absint( $min_stay ? $min_stay : 1 ) ); ?></span>
					</div>
					<div class="nbm-ribbon-item">
						<span class="nbm-r-label"><?php esc_html_e( 'Check-in / Out', 'nexora-booking-manager' ); ?></span>
						<span class="nbm-r-val"><?php echo esc_html( $checkin_time ); ?> / <?php echo esc_html( $checkout_time ); ?></span>
					</div>
				</div>

				<!-- Description / The Content -->
				<div class="nbm-content-block <?php echo ( 'tabbed_navigator' === $single_design ) ? 'nbm-tab-pane active' : ''; ?>" id="nbm-tab-overview">
					<h2><?php esc_html_e( 'About This Property', 'nexora-booking-manager' ); ?></h2>
					<div class="nbm-post-body">
						<?php the_content(); ?>
					</div>
				</div>

				<!-- Policies & House Rules -->
				<?php if ( $booking_rules ) : ?>
					<div class="nbm-content-block <?php echo ( 'tabbed_navigator' === $single_design ) ? 'nbm-tab-pane' : ''; ?>" id="nbm-tab-policies">
						<h2><?php esc_html_e( 'Stay Policies & Rules', 'nexora-booking-manager' ); ?></h2>
						<div class="nbm-rules-text">
							<?php echo nl2br( esc_html( $booking_rules ) ); ?>
						</div>
					</div>
				<?php endif; ?>

				<!-- Amenities -->
				<?php if ( ! empty( $amenities ) && ! is_wp_error( $amenities ) ) : ?>
					<div class="nbm-content-block <?php echo ( 'tabbed_navigator' === $single_design ) ? 'nbm-tab-pane' : ''; ?>" id="nbm-tab-amenities">
						<h2><?php esc_html_e( 'Amenities & Facilities', 'nexora-booking-manager' ); ?></h2>
						<div class="nbm-amenities-list">
							<?php foreach ( $amenities as $am ) : ?>
								<div class="nbm-amenity-badge">
									<span class="nbm-check-icon">✓</span>
									<span><?php echo esc_html( $am->name ); ?></span>
								</div>
							<?php endforeach; ?>
						</div>
					</div>
				<?php endif; ?>

				<!-- Contact & Location Map -->
				<?php if ( $phone || $email || $map_url ) : ?>
					<div class="nbm-content-block">
						<h2><?php esc_html_e( 'Contact & Location', 'nexora-booking-manager' ); ?></h2>
						<div class="nbm-contact-grid">
							<?php if ( $phone ) : ?>
								<p><strong><?php esc_html_e( 'Phone:', 'nexora-booking-manager' ); ?></strong> <a href="tel:<?php echo esc_attr( $phone ); ?>"><?php echo esc_html( $phone ); ?></a></p>
							<?php endif; ?>
							<?php if ( $email ) : ?>
								<p><strong><?php esc_html_e( 'Email:', 'nexora-booking-manager' ); ?></strong> <a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a></p>
							<?php endif; ?>
							<?php if ( $map_url ) : ?>
								<p><a href="<?php echo esc_url( $map_url ); ?>" target="_blank" rel="noopener" class="nbm-btn nbm-btn-outline">🗺 <?php esc_html_e( 'View on Google Maps', 'nexora-booking-manager' ); ?></a></p>
							<?php endif; ?>
						</div>
					</div>
				<?php endif; ?>

			</div>

			<!-- Booking Form Sidebar Column -->
			<div class="nbm-form-column <?php echo ( 'tabbed_navigator' === $single_design ) ? 'nbm-tab-pane' : ''; ?>" id="nbm-tab-reserve">
				<div class="nbm-sticky-form">
					<?php
					$listing_id = $hotel_id;
					$instance_id = 'single-hotel-' . $hotel_id;
					include NBM_PLUGIN_DIR . 'templates/booking-form-hotel.php';
					?>
				</div>
			</div>
		</div>

	</div>
</div>

<?php
get_footer();
