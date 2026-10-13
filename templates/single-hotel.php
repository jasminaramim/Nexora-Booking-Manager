<?php
/**
 * Single Hotel Listing Template.
 *
 * Supports 5 distinct responsive layout designs:
 * 1. classic_hero         — Classic Luxury Hero & 65/35 Sticky Booking
 * 2. split_screen         — 50/50 Dual-Pane Split Screen (Sticky Media Left, Scrollable Content Right)
 * 3. fullscreen_cinematic — 24K VIP Fullscreen Cinematic (Dark Onyx & Gold)
 * 4. editorial_magazine   — Editorial Magazine & Gazette (Serif Typography & Storybook)
 * 5. tabbed_navigator     — Interactive Tabbed Navigator (Zero-Scroll Friction, 5 Tab Panes)
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

$listing_id  = $hotel_id;
$instance_id = 'single-hotel-' . $hotel_id;
?>

<div class="nbm-single-listing nbm-single-hotel nbm-single-layout-<?php echo esc_attr( $single_design ); ?>">
	<div class="nbm-container">

		<?php if ( 'split_screen' === $single_design ) : ?>
			<!-- ============================================================= -->
			<!-- LAYOUT 2: 50/50 DUAL-PANE SPLIT SCREEN -->
			<!-- ============================================================= -->
			<div class="nbm-single-split-wrapper">
				
				<!-- Left Pane: Sticky Media & Showcase -->
				<div class="nbm-split-media-pane">
					<div class="nbm-main-image nbm-split-main-image">
						<?php if ( has_post_thumbnail() ) : ?>
							<?php the_post_thumbnail( 'large', array( 'class' => 'nbm-hero-img' ) ); ?>
						<?php else : ?>
							<div class="nbm-hero-placeholder">
								<span class="dashicons dashicons-building"></span>
							</div>
						<?php endif; ?>
						<div class="nbm-split-image-badge">
							<?php if ( '' !== $price_per_night ) : ?>
								<span class="nbm-split-badge-tier"><?php echo esc_html( ( $price_prefix ? $price_prefix . ' ' : '' ) . NBM_Settings::format_price( $price_per_night ) ); ?>/night</span>
							<?php endif; ?>
							<?php if ( $location ) : ?>
								<span class="nbm-split-badge-dot">•</span>
								<span class="nbm-split-badge-loc">📍 <?php echo esc_html( $city ? $city : $location ); ?></span>
							<?php endif; ?>
						</div>
					</div>

					<?php if ( ! empty( $gallery_ids ) ) : ?>
						<div class="nbm-thumbnails-grid nbm-split-thumbs">
							<?php foreach ( array_slice( $gallery_ids, 0, 4 ) as $img_id ) : ?>
								<div class="nbm-thumb-item">
									<?php echo wp_get_attachment_image( $img_id, 'medium', false, array( 'class' => 'nbm-thumb-img' ) ); ?>
								</div>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>

					<?php if ( $phone || $email || $map_url ) : ?>
						<div class="nbm-split-contact-bar">
							<?php if ( $phone ) : ?>
								<a href="tel:<?php echo esc_attr( $phone ); ?>" class="nbm-split-contact-link">📞 <?php echo esc_html( $phone ); ?></a>
							<?php endif; ?>
							<?php if ( $map_url ) : ?>
								<a href="<?php echo esc_url( $map_url ); ?>" target="_blank" rel="noopener" class="nbm-split-contact-link">🗺 <?php esc_html_e( 'Directions', 'nexora-booking-manager' ); ?></a>
							<?php endif; ?>
						</div>
					<?php endif; ?>
				</div>

				<!-- Right Pane: Scrollable Content & Reservation -->
				<div class="nbm-split-content-pane">
					<div class="nbm-single-header">
						<div class="nbm-header-meta">
							<span class="nbm-badge nbm-badge-hotel"><?php esc_html_e( 'Hotel & Resort', 'nexora-booking-manager' ); ?></span>
							<?php if ( $location ) : ?>
								<span class="nbm-location-text">📍 <?php echo esc_html( $location ); ?></span>
							<?php endif; ?>
						</div>
						<h1 class="nbm-single-title"><?php the_title(); ?></h1>
					</div>

					<div class="nbm-features-ribbon">
						<div class="nbm-ribbon-item">
							<span class="nbm-r-label"><?php esc_html_e( 'Nightly Rate', 'nexora-booking-manager' ); ?></span>
							<span class="nbm-r-val"><?php echo esc_html( NBM_Settings::format_price( $price_per_night ) ); ?></span>
						</div>
						<div class="nbm-ribbon-item">
							<span class="nbm-r-label"><?php esc_html_e( 'Max Guests', 'nexora-booking-manager' ); ?></span>
							<span class="nbm-r-val"><?php echo sprintf( esc_html__( '%d Guests', 'nexora-booking-manager' ), absint( $max_guests ? $max_guests : 2 ) ); ?></span>
						</div>
						<div class="nbm-ribbon-item">
							<span class="nbm-r-label"><?php esc_html_e( 'Min Stay', 'nexora-booking-manager' ); ?></span>
							<span class="nbm-r-val"><?php echo sprintf( esc_html__( '%d Night(s)', 'nexora-booking-manager' ), absint( $min_stay ? $min_stay : 1 ) ); ?></span>
						</div>
					</div>

					<div class="nbm-content-block">
						<h2><?php esc_html_e( 'About This Property', 'nexora-booking-manager' ); ?></h2>
						<div class="nbm-post-body">
							<?php the_content(); ?>
						</div>
					</div>

					<?php if ( $checkin_time || $checkout_time ) : ?>
						<div class="nbm-content-block">
							<h2><?php esc_html_e( 'Check-in & Check-out Schedule', 'nexora-booking-manager' ); ?></h2>
							<div style="display:flex; gap:20px; flex-wrap:wrap;">
								<?php if ( $checkin_time ) : ?>
									<div style="background:#f8fafc; padding:12px 18px; border-radius:8px; border:1px solid #e2e8f0;">
										<span style="font-size:11px; text-transform:uppercase; color:#64748b; font-weight:700; display:block;"><?php esc_html_e( 'Check-in Time', 'nexora-booking-manager' ); ?></span>
										<strong style="font-size:16px; color:#1e293b;">⏱ <?php echo esc_html( $checkin_time ); ?></strong>
									</div>
								<?php endif; ?>
								<?php if ( $checkout_time ) : ?>
									<div style="background:#f8fafc; padding:12px 18px; border-radius:8px; border:1px solid #e2e8f0;">
										<span style="font-size:11px; text-transform:uppercase; color:#64748b; font-weight:700; display:block;"><?php esc_html_e( 'Check-out Time', 'nexora-booking-manager' ); ?></span>
										<strong style="font-size:16px; color:#1e293b;">⏱ <?php echo esc_html( $checkout_time ); ?></strong>
									</div>
								<?php endif; ?>
							</div>
						</div>
					<?php endif; ?>

					<?php if ( $booking_rules ) : ?>
						<div class="nbm-content-block">
							<h2><?php esc_html_e( 'Stay Policies & Rules', 'nexora-booking-manager' ); ?></h2>
							<div class="nbm-rules-text">
								<?php echo nl2br( esc_html( $booking_rules ) ); ?>
							</div>
						</div>
					<?php endif; ?>

					<?php if ( ! empty( $amenities ) && ! is_wp_error( $amenities ) ) : ?>
						<div class="nbm-content-block">
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

					<!-- Booking Form inside Right Column -->
					<div class="nbm-split-booking-box">
						<div class="nbm-sticky-form">
							<?php include NBM_PLUGIN_DIR . 'templates/booking-form-hotel.php'; ?>
						</div>
					</div>
				</div>

			</div>

		<?php elseif ( 'tabbed_navigator' === $single_design ) : ?>
			<!-- ============================================================= -->
			<!-- LAYOUT 5: INTERACTIVE TABBED NAVIGATOR -->
			<!-- ============================================================= -->
			<div class="nbm-tabbed-header">
				<div class="nbm-header-meta">
					<span class="nbm-badge nbm-badge-hotel"><?php esc_html_e( 'Hotel & Resort', 'nexora-booking-manager' ); ?></span>
					<?php if ( $location ) : ?>
						<span class="nbm-location-text">📍 <?php echo esc_html( $location ); ?></span>
					<?php endif; ?>
				</div>
				<h1 class="nbm-single-title"><?php the_title(); ?></h1>
			</div>

			<!-- Sticky Tab Navigation Bar -->
			<div class="nbm-tab-navbar">
				<button type="button" class="nbm-tab-nav-btn active" data-tab="overview">📋 <?php esc_html_e( 'Overview', 'nexora-booking-manager' ); ?></button>
				<button type="button" class="nbm-tab-nav-btn" data-tab="gallery">📸 <?php esc_html_e( 'Photo Gallery', 'nexora-booking-manager' ); ?></button>
				<button type="button" class="nbm-tab-nav-btn" data-tab="policies">⏱ <?php esc_html_e( 'Check-in & Rules', 'nexora-booking-manager' ); ?></button>
				<button type="button" class="nbm-tab-nav-btn" data-tab="amenities">✨ <?php esc_html_e( 'Amenities', 'nexora-booking-manager' ); ?></button>
				<button type="button" class="nbm-tab-nav-btn nbm-tab-reserve-btn" data-tab="reserve">📅 <?php esc_html_e( 'Book Stay', 'nexora-booking-manager' ); ?></button>
			</div>

			<!-- Tabbed Content Panes (Full Width, Zero Lopsided Empty Columns) -->
			<div class="nbm-tabbed-panes-wrapper">
				
				<!-- Pane 1: Overview -->
				<div class="nbm-tab-pane active" id="nbm-pane-overview">
					<div class="nbm-features-ribbon" style="margin-bottom:24px;">
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
							<span class="nbm-r-label"><?php esc_html_e( 'Check-in Time', 'nexora-booking-manager' ); ?></span>
							<span class="nbm-r-val"><?php echo esc_html( $checkin_time ? $checkin_time : '3:00 PM' ); ?></span>
						</div>
					</div>

					<div class="nbm-content-block">
						<h2><?php esc_html_e( 'About This Property', 'nexora-booking-manager' ); ?></h2>
						<div class="nbm-post-body">
							<?php the_content(); ?>
						</div>
					</div>

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

				<!-- Pane 2: Photo Gallery -->
				<div class="nbm-tab-pane" id="nbm-pane-gallery">
					<div class="nbm-gallery-showcase">
						<div class="nbm-main-image" style="height:440px;">
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
								<?php foreach ( $gallery_ids as $img_id ) : ?>
									<div class="nbm-thumb-item">
										<?php echo wp_get_attachment_image( $img_id, 'medium_large', false, array( 'class' => 'nbm-thumb-img' ) ); ?>
									</div>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>
					</div>
				</div>

				<!-- Pane 3: Check-in & Rules -->
				<div class="nbm-tab-pane" id="nbm-pane-policies">
					<div class="nbm-content-block">
						<h2><?php esc_html_e( 'Check-in & Check-out Schedule', 'nexora-booking-manager' ); ?></h2>
						<div style="display:flex; gap:20px; flex-wrap:wrap; margin-bottom:20px;">
							<div style="background:#f8fafc; padding:12px 18px; border-radius:8px; border:1px solid #e2e8f0;">
								<span style="font-size:11px; text-transform:uppercase; color:#64748b; font-weight:700; display:block;"><?php esc_html_e( 'Check-in', 'nexora-booking-manager' ); ?></span>
								<strong style="font-size:16px; color:#1e293b;">⏱ <?php echo esc_html( $checkin_time ? $checkin_time : '3:00 PM' ); ?></strong>
							</div>
							<div style="background:#f8fafc; padding:12px 18px; border-radius:8px; border:1px solid #e2e8f0;">
								<span style="font-size:11px; text-transform:uppercase; color:#64748b; font-weight:700; display:block;"><?php esc_html_e( 'Check-out', 'nexora-booking-manager' ); ?></span>
								<strong style="font-size:16px; color:#1e293b;">⏱ <?php echo esc_html( $checkout_time ? $checkout_time : '11:00 AM' ); ?></strong>
							</div>
						</div>

						<?php if ( $booking_rules ) : ?>
							<h3><?php esc_html_e( 'Stay Policies & Rules', 'nexora-booking-manager' ); ?></h3>
							<div class="nbm-rules-text">
								<?php echo nl2br( esc_html( $booking_rules ) ); ?>
							</div>
						<?php endif; ?>
					</div>
				</div>

				<!-- Pane 4: Amenities -->
				<div class="nbm-tab-pane" id="nbm-pane-amenities">
					<?php if ( ! empty( $amenities ) && ! is_wp_error( $amenities ) ) : ?>
						<div class="nbm-content-block">
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
				</div>

				<!-- Pane 5: Book Stay Form -->
				<div class="nbm-tab-pane" id="nbm-pane-reserve">
					<div class="nbm-pane-reserve-box">
						<div class="nbm-sticky-form">
							<?php include NBM_PLUGIN_DIR . 'templates/booking-form-hotel.php'; ?>
						</div>
					</div>
				</div>

			</div>

		<?php else : ?>
			<!-- ============================================================= -->
			<!-- LAYOUTS 1, 3, 4: CLASSIC HERO, FULLSCREEN CINEMATIC, EDITORIAL -->
			<!-- ============================================================= -->
			
			<!-- Header / Title -->
			<div class="nbm-single-header">
				<div class="nbm-header-meta">
					<?php if ( 'fullscreen_cinematic' === $single_design ) : ?>
						<span class="nbm-badge" style="background:rgba(245,158,11,0.15); border:1px solid #f59e0b; color:#fbbf24; font-weight:800;">👑 24K VIP RESORT</span>
					<?php else : ?>
						<span class="nbm-badge nbm-badge-hotel"><?php esc_html_e( 'Hotel & Resort', 'nexora-booking-manager' ); ?></span>
					<?php endif; ?>
					<?php if ( $location ) : ?>
						<span class="nbm-location-text">📍 <?php echo esc_html( $location ); ?></span>
					<?php endif; ?>
				</div>
				<h1 class="nbm-single-title"><?php the_title(); ?></h1>
			</div>

			<!-- Gallery Showcase -->
			<div class="nbm-gallery-showcase">
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

			<!-- Main 2-Column Content Layout -->
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

					<!-- Description -->
					<div class="nbm-content-block">
						<h2><?php esc_html_e( 'About This Property', 'nexora-booking-manager' ); ?></h2>
						<div class="nbm-post-body">
							<?php the_content(); ?>
						</div>
					</div>

					<!-- Policies & House Rules -->
					<?php if ( $booking_rules ) : ?>
						<div class="nbm-content-block">
							<h2><?php esc_html_e( 'Stay Policies & Rules', 'nexora-booking-manager' ); ?></h2>
							<div class="nbm-rules-text">
								<?php echo nl2br( esc_html( $booking_rules ) ); ?>
							</div>
						</div>
					<?php endif; ?>

					<!-- Amenities -->
					<?php if ( ! empty( $amenities ) && ! is_wp_error( $amenities ) ) : ?>
						<div class="nbm-content-block">
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
				<div class="nbm-form-column">
					<div class="nbm-sticky-form">
						<?php include NBM_PLUGIN_DIR . 'templates/booking-form-hotel.php'; ?>
					</div>
				</div>
			</div>

		<?php endif; ?>

	</div>
</div>

<?php
get_footer();
