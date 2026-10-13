<?php
/**
 * Single Bar Listing Template.
 *
 * @package Nexora_Booking_Manager
 */

get_header();

$bar_id          = get_the_ID();
$address         = get_post_meta( $bar_id, '_nbm_address', true );
$city            = get_post_meta( $bar_id, '_nbm_city', true );
$state           = get_post_meta( $bar_id, '_nbm_state', true );
$country         = get_post_meta( $bar_id, '_nbm_country', true );
$map_url         = get_post_meta( $bar_id, '_nbm_map_url', true );
$phone           = get_post_meta( $bar_id, '_nbm_phone', true );
$email           = get_post_meta( $bar_id, '_nbm_email', true );
$menu_url        = get_post_meta( $bar_id, '_nbm_menu_url', true );
$price_range     = get_post_meta( $bar_id, '_nbm_price_range', true );
$table_capacity  = get_post_meta( $bar_id, '_nbm_table_capacity', true );
$slot_duration   = get_post_meta( $bar_id, '_nbm_slot_duration', true );
$max_party_size  = get_post_meta( $bar_id, '_nbm_max_party_size', true );
$instructions    = get_post_meta( $bar_id, '_nbm_reservation_instructions', true );
$opening_hours   = get_post_meta( $bar_id, '_nbm_opening_hours', true );
$gallery_ids_raw = get_post_meta( $bar_id, '_nbm_gallery_ids', true );

$location = trim( implode( ', ', array_filter( array( $address, $city, $state, $country ) ) ) );
$amenities = get_the_terms( $bar_id, 'nbm_amenity' );

$gallery_ids = array();
if ( ! empty( $gallery_ids_raw ) ) {
	$gallery_ids = array_filter( array_map( 'absint', explode( ',', $gallery_ids_raw ) ) );
}

$days_of_week = array(
	'monday'    => __( 'Monday', 'nexora-booking-manager' ),
	'tuesday'   => __( 'Tuesday', 'nexora-booking-manager' ),
	'wednesday' => __( 'Wednesday', 'nexora-booking-manager' ),
	'thursday'  => __( 'Thursday', 'nexora-booking-manager' ),
	'friday'    => __( 'Friday', 'nexora-booking-manager' ),
	'saturday'  => __( 'Saturday', 'nexora-booking-manager' ),
	'sunday'    => __( 'Sunday', 'nexora-booking-manager' ),
);

$single_design = sanitize_key( $_GET['nbm_single_preset'] ?? NBM_Settings::get( 'single_details_design', 'classic_hero' ) );
$valid_designs = array( 'classic_hero', 'split_screen', 'fullscreen_cinematic', 'editorial_magazine', 'tabbed_navigator' );
if ( ! in_array( $single_design, $valid_designs, true ) ) {
	$single_design = 'classic_hero';
}
?>

<div class="nbm-single-listing nbm-single-bar nbm-single-layout-<?php echo esc_attr( $single_design ); ?>">
	<div class="nbm-container">
		
		<!-- Header / Title -->
		<div class="nbm-single-header">
			<div class="nbm-header-meta">
				<span class="nbm-badge nbm-badge-bar"><?php esc_html_e( 'Bar & Lounge', 'nexora-booking-manager' ); ?></span>
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
				<button type="button" class="nbm-tab-nav-btn" data-tab="hours">⏱ <?php esc_html_e( 'Operating Hours', 'nexora-booking-manager' ); ?></button>
				<button type="button" class="nbm-tab-nav-btn" data-tab="amenities">✨ <?php esc_html_e( 'Features & Rules', 'nexora-booking-manager' ); ?></button>
				<button type="button" class="nbm-tab-nav-btn nbm-tab-reserve-btn" data-tab="reserve">📅 <?php esc_html_e( 'Reserve Table', 'nexora-booking-manager' ); ?></button>
			</div>
		<?php endif; ?>

		<!-- Gallery Showcase -->
		<div class="nbm-gallery-showcase <?php echo ( 'tabbed_navigator' === $single_design ) ? 'nbm-tab-pane' : ''; ?>" id="nbm-tab-gallery">
			<div class="nbm-main-image">
				<?php if ( has_post_thumbnail() ) : ?>
					<?php the_post_thumbnail( 'large', array( 'class' => 'nbm-hero-img' ) ); ?>
				<?php else : ?>
					<div class="nbm-hero-placeholder">
						<span class="dashicons dashicons-beer"></span>
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

		<!-- Main Layout -->
		<div class="nbm-single-layout">
			<div class="nbm-details-column">
				
				<!-- Overview Badges -->
				<div class="nbm-features-ribbon <?php echo ( 'tabbed_navigator' === $single_design ) ? 'nbm-tab-pane active' : ''; ?>" id="nbm-tab-overview-ribbon">
					<div class="nbm-ribbon-item">
						<span class="nbm-r-label"><?php esc_html_e( 'Price Range', 'nexora-booking-manager' ); ?></span>
						<span class="nbm-r-val"><?php echo esc_html( $price_range ? $price_range : '$$' ); ?></span>
					</div>
					<div class="nbm-ribbon-item">
						<span class="nbm-r-label"><?php esc_html_e( 'Max Party Size', 'nexora-booking-manager' ); ?></span>
						<span class="nbm-r-val"><?php echo sprintf( esc_html__( '%d Guests', 'nexora-booking-manager' ), absint( $max_party_size ? $max_party_size : 8 ) ); ?></span>
					</div>
					<div class="nbm-ribbon-item">
						<span class="nbm-r-label"><?php esc_html_e( 'Table Duration', 'nexora-booking-manager' ); ?></span>
						<span class="nbm-r-val"><?php echo sprintf( esc_html__( '%d Mins', 'nexora-booking-manager' ), absint( $slot_duration ? $slot_duration : 90 ) ); ?></span>
					</div>
					<?php if ( $menu_url ) : ?>
						<div class="nbm-ribbon-item">
							<span class="nbm-r-label"><?php esc_html_e( 'Food & Drinks', 'nexora-booking-manager' ); ?></span>
							<span class="nbm-r-val"><a href="<?php echo esc_url( $menu_url ); ?>" target="_blank" rel="noopener">🍸 <?php esc_html_e( 'View Menu', 'nexora-booking-manager' ); ?></a></span>
						</div>
					<?php endif; ?>
				</div>

				<!-- Description -->
				<div class="nbm-content-block <?php echo ( 'tabbed_navigator' === $single_design ) ? 'nbm-tab-pane active' : ''; ?>" id="nbm-tab-overview">
					<h2><?php esc_html_e( 'About This Venue', 'nexora-booking-manager' ); ?></h2>
					<div class="nbm-post-body">
						<?php the_content(); ?>
					</div>
				</div>

				<!-- Opening Hours Table -->
				<div class="nbm-content-block <?php echo ( 'tabbed_navigator' === $single_design ) ? 'nbm-tab-pane' : ''; ?>" id="nbm-tab-hours">
					<h2><?php esc_html_e( 'Operating Hours', 'nexora-booking-manager' ); ?></h2>
					<table class="nbm-public-hours-table">
						<tbody>
							<?php foreach ( $days_of_week as $day_k => $day_name ) :
								$day_data = $opening_hours[ $day_k ] ?? array();
								$closed   = ! empty( $day_data['closed'] );
								$open     = $day_data['open'] ?? '16:00';
								$close    = $day_data['close'] ?? '23:30';
							?>
								<tr>
									<td class="nbm-day-col"><?php echo esc_html( $day_name ); ?></td>
									<td class="nbm-time-col">
										<?php if ( $closed ) : ?>
											<span class="nbm-tag-closed"><?php esc_html_e( 'Closed', 'nexora-booking-manager' ); ?></span>
										<?php else : ?>
											<span><?php echo esc_html( $open . ' — ' . $close ); ?></span>
										<?php endif; ?>
									</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>

				<!-- Dress Code & Policies -->
				<?php if ( $instructions ) : ?>
					<div class="nbm-content-block <?php echo ( 'tabbed_navigator' === $single_design ) ? 'nbm-tab-pane' : ''; ?>" id="nbm-tab-amenities-rules">
						<h2><?php esc_html_e( 'Dress Code & Reservation Notes', 'nexora-booking-manager' ); ?></h2>
						<div class="nbm-rules-text">
							<?php echo nl2br( esc_html( $instructions ) ); ?>
						</div>
					</div>
				<?php endif; ?>

				<!-- Amenities -->
				<?php if ( ! empty( $amenities ) && ! is_wp_error( $amenities ) ) : ?>
					<div class="nbm-content-block <?php echo ( 'tabbed_navigator' === $single_design ) ? 'nbm-tab-pane' : ''; ?>" id="nbm-tab-amenities">
						<h2><?php esc_html_e( 'Features & Vibes', 'nexora-booking-manager' ); ?></h2>
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

			<!-- Reservation Form Column -->
			<div class="nbm-form-column <?php echo ( 'tabbed_navigator' === $single_design ) ? 'nbm-tab-pane' : ''; ?>" id="nbm-tab-reserve">
				<div class="nbm-sticky-form">
					<?php
					$listing_id = $bar_id;
					$instance_id = 'single-bar-' . $bar_id;
					include NBM_PLUGIN_DIR . 'templates/booking-form-bar.php';
					?>
				</div>
			</div>
		</div>

	</div>
</div>

<?php
get_footer();
