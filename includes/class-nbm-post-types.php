<?php
/**
 * Custom Post Types and Meta Boxes manager.
 *
 * @package Nexora_Booking_Manager
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class NBM_Post_Types {

	/**
	 * Initialize hooks for CPTs and Taxonomies.
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_post_types' ) );
		add_action( 'init', array( __CLASS__, 'register_taxonomies' ) );
		add_action( 'add_meta_boxes', array( __CLASS__, 'add_meta_boxes' ) );
		add_action( 'save_post_nbm_hotel', array( __CLASS__, 'save_hotel_meta' ) );
		add_action( 'save_post_nbm_bar', array( __CLASS__, 'save_bar_meta' ) );

		// Custom admin columns
		add_filter( 'manage_nbm_hotel_posts_columns', array( __CLASS__, 'set_hotel_columns' ) );
		add_action( 'manage_nbm_hotel_posts_custom_column', array( __CLASS__, 'render_hotel_columns' ), 10, 2 );

		add_filter( 'manage_nbm_bar_posts_columns', array( __CLASS__, 'set_bar_columns' ) );
		add_action( 'manage_nbm_bar_posts_custom_column', array( __CLASS__, 'render_bar_columns' ), 10, 2 );
	}

	/**
	 * Register Custom Post Types: nbm_hotel and nbm_bar.
	 */
	public static function register_post_types() {
		// Hotel CPT
		$hotel_labels = array(
			'name'                  => _x( 'Hotels', 'Post Type General Name', 'nexora-booking-manager' ),
			'singular_name'         => _x( 'Hotel', 'Post Type Singular Name', 'nexora-booking-manager' ),
			'menu_name'             => __( 'Hotels', 'nexora-booking-manager' ),
			'all_items'             => __( 'All Hotels', 'nexora-booking-manager' ),
			'add_new_item'          => __( 'Add New Hotel', 'nexora-booking-manager' ),
			'add_new'               => __( 'Add New Hotel', 'nexora-booking-manager' ),
			'new_item'              => __( 'New Hotel', 'nexora-booking-manager' ),
			'edit_item'             => __( 'Edit Hotel', 'nexora-booking-manager' ),
			'update_item'           => __( 'Update Hotel', 'nexora-booking-manager' ),
			'view_item'             => __( 'View Hotel', 'nexora-booking-manager' ),
			'search_items'          => __( 'Search Hotels', 'nexora-booking-manager' ),
			'not_found'             => __( 'No hotels found', 'nexora-booking-manager' ),
			'not_found_in_trash'    => __( 'No hotels found in Trash', 'nexora-booking-manager' ),
		);

		$hotel_args = array(
			'label'                 => __( 'Hotel', 'nexora-booking-manager' ),
			'labels'                => $hotel_labels,
			'supports'              => array( 'title', 'editor', 'thumbnail', 'excerpt', 'revisions' ),
			'taxonomies'            => array( 'nbm_location', 'nbm_amenity' ),
			'hierarchical'          => false,
			'public'                => true,
			'show_ui'               => true,
			'show_in_menu'          => false, // Shown under Nexora Booking menu
			'show_in_nav_menus'     => true,
			'show_in_admin_bar'     => true,
			'can_export'            => true,
			'has_archive'           => true,
			'rewrite'               => array( 'slug' => 'hotels' ),
			'capability_type'       => 'post',
			'show_in_rest'          => true,
		);

		register_post_type( 'nbm_hotel', $hotel_args );

		// Bar CPT
		$bar_labels = array(
			'name'                  => _x( 'Bars', 'Post Type General Name', 'nexora-booking-manager' ),
			'singular_name'         => _x( 'Bar', 'Post Type Singular Name', 'nexora-booking-manager' ),
			'menu_name'             => __( 'Bars', 'nexora-booking-manager' ),
			'all_items'             => __( 'All Bars', 'nexora-booking-manager' ),
			'add_new_item'          => __( 'Add New Bar', 'nexora-booking-manager' ),
			'add_new'               => __( 'Add New Bar', 'nexora-booking-manager' ),
			'new_item'              => __( 'New Bar', 'nexora-booking-manager' ),
			'edit_item'             => __( 'Edit Bar', 'nexora-booking-manager' ),
			'update_item'           => __( 'Update Bar', 'nexora-booking-manager' ),
			'view_item'             => __( 'View Bar', 'nexora-booking-manager' ),
			'search_items'          => __( 'Search Bars', 'nexora-booking-manager' ),
			'not_found'             => __( 'No bars found', 'nexora-booking-manager' ),
			'not_found_in_trash'    => __( 'No bars found in Trash', 'nexora-booking-manager' ),
		);

		$bar_args = array(
			'label'                 => __( 'Bar', 'nexora-booking-manager' ),
			'labels'                => $bar_labels,
			'supports'              => array( 'title', 'editor', 'thumbnail', 'excerpt', 'revisions' ),
			'taxonomies'            => array( 'nbm_location', 'nbm_amenity' ),
			'hierarchical'          => false,
			'public'                => true,
			'show_ui'               => true,
			'show_in_menu'          => false, // Shown under Nexora Booking menu
			'show_in_nav_menus'     => true,
			'show_in_admin_bar'     => true,
			'can_export'            => true,
			'has_archive'           => true,
			'rewrite'               => array( 'slug' => 'bars' ),
			'capability_type'       => 'post',
			'show_in_rest'          => true,
		);

		register_post_type( 'nbm_bar', $bar_args );
	}

	/**
	 * Register Taxonomies: Location & Amenities.
	 */
	public static function register_taxonomies() {
		// Location Taxonomy (City / Region)
		$loc_labels = array(
			'name'              => _x( 'Locations', 'taxonomy general name', 'nexora-booking-manager' ),
			'singular_name'     => _x( 'Location', 'taxonomy singular name', 'nexora-booking-manager' ),
			'search_items'      => __( 'Search Locations', 'nexora-booking-manager' ),
			'all_items'         => __( 'All Locations', 'nexora-booking-manager' ),
			'parent_item'       => __( 'Parent Location', 'nexora-booking-manager' ),
			'edit_item'         => __( 'Edit Location', 'nexora-booking-manager' ),
			'update_item'       => __( 'Update Location', 'nexora-booking-manager' ),
			'add_new_item'      => __( 'Add New Location', 'nexora-booking-manager' ),
			'new_item_name'     => __( 'New Location Name', 'nexora-booking-manager' ),
			'menu_name'         => __( 'Locations', 'nexora-booking-manager' ),
		);

		register_taxonomy( 'nbm_location', array( 'nbm_hotel', 'nbm_bar' ), array(
			'hierarchical'      => true,
			'labels'            => $loc_labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'destination' ),
			'show_in_rest'      => true,
		) );

		// Amenity Taxonomy
		$amenity_labels = array(
			'name'              => _x( 'Amenities', 'taxonomy general name', 'nexora-booking-manager' ),
			'singular_name'     => _x( 'Amenity', 'taxonomy singular name', 'nexora-booking-manager' ),
			'search_items'      => __( 'Search Amenities', 'nexora-booking-manager' ),
			'all_items'         => __( 'All Amenities', 'nexora-booking-manager' ),
			'edit_item'         => __( 'Edit Amenity', 'nexora-booking-manager' ),
			'update_item'       => __( 'Update Amenity', 'nexora-booking-manager' ),
			'add_new_item'      => __( 'Add New Amenity', 'nexora-booking-manager' ),
			'new_item_name'     => __( 'New Amenity Name', 'nexora-booking-manager' ),
			'menu_name'         => __( 'Amenities', 'nexora-booking-manager' ),
		);

		register_taxonomy( 'nbm_amenity', array( 'nbm_hotel', 'nbm_bar' ), array(
			'hierarchical'      => false,
			'labels'            => $amenity_labels,
			'show_ui'           => true,
			'show_admin_column' => false,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'amenity' ),
			'show_in_rest'      => true,
		) );
	}

	/**
	 * Register meta boxes for listings.
	 */
	public static function add_meta_boxes() {
		// Hotel details
		add_meta_box(
			'nbm_hotel_details_box',
			__( 'Hotel Details, Pricing & Availability Rules', 'nexora-booking-manager' ),
			array( __CLASS__, 'render_hotel_meta_box' ),
			'nbm_hotel',
			'normal',
			'high'
		);

		// Bar details
		add_meta_box(
			'nbm_bar_details_box',
			__( 'Bar Details, Opening Hours & Reservation Rules', 'nexora-booking-manager' ),
			array( __CLASS__, 'render_bar_meta_box' ),
			'nbm_bar',
			'normal',
			'high'
		);
	}

	/**
	 * Render Hotel Meta Box.
	 *
	 * @param WP_Post $post Current post.
	 */
	public static function render_hotel_meta_box( $post ) {
		wp_nonce_field( 'nbm_save_hotel_meta_action', 'nbm_hotel_nonce' );

		$address          = get_post_meta( $post->ID, '_nbm_address', true );
		$city             = get_post_meta( $post->ID, '_nbm_city', true );
		$state            = get_post_meta( $post->ID, '_nbm_state', true );
		$country          = get_post_meta( $post->ID, '_nbm_country', true );
		$postal_code      = get_post_meta( $post->ID, '_nbm_postal_code', true );
		$map_url          = get_post_meta( $post->ID, '_nbm_map_url', true );
		$phone            = get_post_meta( $post->ID, '_nbm_phone', true );
		$email            = get_post_meta( $post->ID, '_nbm_email', true );
		$price_per_night  = get_post_meta( $post->ID, '_nbm_price_per_night', true );
		$price_prefix     = get_post_meta( $post->ID, '_nbm_price_prefix', true );
		$max_guests       = get_post_meta( $post->ID, '_nbm_max_guests', true );
		$total_rooms      = get_post_meta( $post->ID, '_nbm_total_rooms', true );
		$min_stay         = get_post_meta( $post->ID, '_nbm_min_stay', true );
		$max_stay         = get_post_meta( $post->ID, '_nbm_max_stay', true );
		$checkin_time     = get_post_meta( $post->ID, '_nbm_checkin_time', true );
		$checkout_time    = get_post_meta( $post->ID, '_nbm_checkout_time', true );
		$booking_rules    = get_post_meta( $post->ID, '_nbm_booking_rules', true );
		$gallery_ids      = get_post_meta( $post->ID, '_nbm_gallery_ids', true );

		// Fallbacks from global settings
		if ( '' === $price_per_night ) { $price_per_night = '120.00'; }
		if ( empty( $max_guests ) ) { $max_guests = NBM_Settings::get( 'hotel_default_max_guests', 4 ); }
		if ( empty( $total_rooms ) ) { $total_rooms = 10; }
		if ( empty( $min_stay ) ) { $min_stay = NBM_Settings::get( 'hotel_default_min_stay', 1 ); }
		if ( empty( $max_stay ) ) { $max_stay = NBM_Settings::get( 'hotel_default_max_stay', 30 ); }
		if ( empty( $checkin_time ) ) { $checkin_time = NBM_Settings::get( 'hotel_default_checkin', '14:00' ); }
		if ( empty( $checkout_time ) ) { $checkout_time = NBM_Settings::get( 'hotel_default_checkout', '11:00' ); }
		?>
		<div class="nbm-meta-box-wrap">
			<style>
				.nbm-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
				.nbm-grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px; }
				.nbm-field-group { margin-bottom: 12px; }
				.nbm-field-group label { font-weight: 600; display: block; margin-bottom: 4px; }
				.nbm-field-group input[type="text"],
				.nbm-field-group input[type="number"],
				.nbm-field-group input[type="email"],
				.nbm-field-group textarea { width: 100%; }
				.nbm-section-header { font-size: 14px; font-weight: 700; border-bottom: 2px solid #e2e8f0; padding-bottom: 6px; margin: 20px 0 12px; color: #1e293b; }
			</style>

			<div class="nbm-section-header"><?php esc_html_e( 'Location & Contact Details', 'nexora-booking-manager' ); ?></div>
			<div class="nbm-grid-2">
				<div class="nbm-field-group">
					<label for="nbm_address"><?php esc_html_e( 'Street Address', 'nexora-booking-manager' ); ?></label>
					<input type="text" id="nbm_address" name="nbm_address" value="<?php echo esc_attr( $address ); ?>" placeholder="e.g. 104 Ocean View Road" />
				</div>
				<div class="nbm-field-group">
					<label for="nbm_city"><?php esc_html_e( 'City', 'nexora-booking-manager' ); ?></label>
					<input type="text" id="nbm_city" name="nbm_city" value="<?php echo esc_attr( $city ); ?>" placeholder="e.g. Miami" />
				</div>
			</div>
			<div class="nbm-grid-3">
				<div class="nbm-field-group">
					<label for="nbm_state"><?php esc_html_e( 'State / Region', 'nexora-booking-manager' ); ?></label>
					<input type="text" id="nbm_state" name="nbm_state" value="<?php echo esc_attr( $state ); ?>" placeholder="e.g. Florida" />
				</div>
				<div class="nbm-field-group">
					<label for="nbm_country"><?php esc_html_e( 'Country', 'nexora-booking-manager' ); ?></label>
					<input type="text" id="nbm_country" name="nbm_country" value="<?php echo esc_attr( $country ); ?>" placeholder="e.g. USA" />
				</div>
				<div class="nbm-field-group">
					<label for="nbm_postal_code"><?php esc_html_e( 'Postal Code', 'nexora-booking-manager' ); ?></label>
					<input type="text" id="nbm_postal_code" name="nbm_postal_code" value="<?php echo esc_attr( $postal_code ); ?>" placeholder="e.g. 33139" />
				</div>
			</div>
			<div class="nbm-grid-3">
				<div class="nbm-field-group">
					<label for="nbm_phone"><?php esc_html_e( 'Phone Number', 'nexora-booking-manager' ); ?></label>
					<input type="text" id="nbm_phone" name="nbm_phone" value="<?php echo esc_attr( $phone ); ?>" />
				</div>
				<div class="nbm-field-group">
					<label for="nbm_email"><?php esc_html_e( 'Inquiry Email', 'nexora-booking-manager' ); ?></label>
					<input type="email" id="nbm_email" name="nbm_email" value="<?php echo esc_attr( $email ); ?>" />
				</div>
				<div class="nbm-field-group">
					<label for="nbm_map_url"><?php esc_html_e( 'Google Maps Link or Coordinates', 'nexora-booking-manager' ); ?></label>
					<input type="text" id="nbm_map_url" name="nbm_map_url" value="<?php echo esc_attr( $map_url ); ?>" placeholder="https://maps.google.com/..." />
				</div>
			</div>

			<div class="nbm-section-header"><?php esc_html_e( 'Pricing, Inventory & Capacity', 'nexora-booking-manager' ); ?></div>
			<div class="nbm-grid-3">
				<div class="nbm-field-group">
					<label for="nbm_price_per_night"><?php esc_html_e( 'Base Price Per Night', 'nexora-booking-manager' ); ?> (<?php echo esc_html( NBM_Settings::get( 'currency_symbol', '$' ) ); ?>)</label>
					<input type="number" step="0.01" min="0" id="nbm_price_per_night" name="nbm_price_per_night" value="<?php echo esc_attr( $price_per_night ); ?>" required />
				</div>
				<div class="nbm-field-group">
					<label for="nbm_price_prefix"><?php esc_html_e( 'Price Prefix / Suffix Note', 'nexora-booking-manager' ); ?></label>
					<input type="text" id="nbm_price_prefix" name="nbm_price_prefix" value="<?php echo esc_attr( $price_prefix ); ?>" placeholder="e.g. Starting from / night" />
				</div>
				<div class="nbm-field-group">
					<label for="nbm_max_guests"><?php esc_html_e( 'Maximum Guests Per Booking', 'nexora-booking-manager' ); ?></label>
					<input type="number" min="1" id="nbm_max_guests" name="nbm_max_guests" value="<?php echo esc_attr( $max_guests ); ?>" required />
				</div>
			</div>
			<div class="nbm-grid-3">
				<div class="nbm-field-group">
					<label for="nbm_total_rooms"><?php esc_html_e( 'Total Rooms (Inventory Capacity)', 'nexora-booking-manager' ); ?></label>
					<input type="number" min="1" id="nbm_total_rooms" name="nbm_total_rooms" value="<?php echo esc_attr( $total_rooms ); ?>" required />
					<small class="description"><?php esc_html_e( 'Overlapping confirmed bookings cannot exceed this number.', 'nexora-booking-manager' ); ?></small>
				</div>
				<div class="nbm-field-group">
					<label for="nbm_min_stay"><?php esc_html_e( 'Minimum Stay (Nights)', 'nexora-booking-manager' ); ?></label>
					<input type="number" min="1" id="nbm_min_stay" name="nbm_min_stay" value="<?php echo esc_attr( $min_stay ); ?>" />
				</div>
				<div class="nbm-field-group">
					<label for="nbm_max_stay"><?php esc_html_e( 'Maximum Stay (Nights)', 'nexora-booking-manager' ); ?></label>
					<input type="number" min="1" id="nbm_max_stay" name="nbm_max_stay" value="<?php echo esc_attr( $max_stay ); ?>" />
				</div>
			</div>

			<div class="nbm-section-header"><?php esc_html_e( 'Schedule & Booking Instructions', 'nexora-booking-manager' ); ?></div>
			<div class="nbm-grid-2">
				<div class="nbm-field-group">
					<label for="nbm_checkin_time"><?php esc_html_e( 'Check-in Time', 'nexora-booking-manager' ); ?></label>
					<input type="text" id="nbm_checkin_time" name="nbm_checkin_time" value="<?php echo esc_attr( $checkin_time ); ?>" placeholder="14:00" />
				</div>
				<div class="nbm-field-group">
					<label for="nbm_checkout_time"><?php esc_html_e( 'Check-out Time', 'nexora-booking-manager' ); ?></label>
					<input type="text" id="nbm_checkout_time" name="nbm_checkout_time" value="<?php echo esc_attr( $checkout_time ); ?>" placeholder="11:00" />
				</div>
			</div>
			<div class="nbm-field-group">
				<label for="nbm_booking_rules"><?php esc_html_e( 'Booking Policies & Check-in Instructions', 'nexora-booking-manager' ); ?></label>
				<textarea id="nbm_booking_rules" name="nbm_booking_rules" rows="3"><?php echo esc_textarea( $booking_rules ); ?></textarea>
			</div>
			<div class="nbm-field-group">
				<label for="nbm_gallery_ids"><?php esc_html_e( 'Image Gallery Attachment IDs (comma-separated)', 'nexora-booking-manager' ); ?></label>
				<input type="text" id="nbm_gallery_ids" name="nbm_gallery_ids" value="<?php echo esc_attr( $gallery_ids ); ?>" placeholder="e.g. 101, 102, 103" />
				<small class="description"><?php esc_html_e( 'Upload images via Media Library and enter their IDs here for the listing gallery.', 'nexora-booking-manager' ); ?></small>
			</div>
			<div class="nbm-field-group">
				<label for="nbm_single_layout"><?php esc_html_e( 'Single Details Page Layout Design', 'nexora-booking-manager' ); ?></label>
				<?php $single_layout = get_post_meta( $post->ID, '_nbm_single_layout', true ); ?>
				<select id="nbm_single_layout" name="nbm_single_layout">
					<option value="" <?php selected( $single_layout, '' ); ?>><?php esc_html_e( '— Use Global Default Studio Design —', 'nexora-booking-manager' ); ?></option>
					<option value="classic_hero" <?php selected( $single_layout, 'classic_hero' ); ?>><?php esc_html_e( 'Layout 1: Classic Luxury Hero', 'nexora-booking-manager' ); ?></option>
					<option value="split_screen" <?php selected( $single_layout, 'split_screen' ); ?>><?php esc_html_e( 'Layout 2: Modern 50/50 Split Screen', 'nexora-booking-manager' ); ?></option>
					<option value="fullscreen_cinematic" <?php selected( $single_layout, 'fullscreen_cinematic' ); ?>><?php esc_html_e( 'Layout 3: Fullscreen Cinematic Showcase', 'nexora-booking-manager' ); ?></option>
					<option value="editorial_magazine" <?php selected( $single_layout, 'editorial_magazine' ); ?>><?php esc_html_e( 'Layout 4: Editorial Magazine & Story', 'nexora-booking-manager' ); ?></option>
					<option value="tabbed_navigator" <?php selected( $single_layout, 'tabbed_navigator' ); ?>><?php esc_html_e( 'Layout 5: Interactive Tabbed Navigator', 'nexora-booking-manager' ); ?></option>
				</select>
				<small class="description"><?php esc_html_e( 'Choose a bespoke layout for this venue or inherit the global design chosen in Shortcode Generator & Design Studio.', 'nexora-booking-manager' ); ?></small>
			</div>
		</div>
		<?php
	}

	/**
	 * Render Bar Meta Box.
	 *
	 * @param WP_Post $post Current post.
	 */
	public static function render_bar_meta_box( $post ) {
		wp_nonce_field( 'nbm_save_bar_meta_action', 'nbm_bar_nonce' );

		$address             = get_post_meta( $post->ID, '_nbm_address', true );
		$city                = get_post_meta( $post->ID, '_nbm_city', true );
		$state               = get_post_meta( $post->ID, '_nbm_state', true );
		$country             = get_post_meta( $post->ID, '_nbm_country', true );
		$postal_code         = get_post_meta( $post->ID, '_nbm_postal_code', true );
		$map_url             = get_post_meta( $post->ID, '_nbm_map_url', true );
		$phone               = get_post_meta( $post->ID, '_nbm_phone', true );
		$email               = get_post_meta( $post->ID, '_nbm_email', true );
		$menu_url            = get_post_meta( $post->ID, '_nbm_menu_url', true );
		$price_range         = get_post_meta( $post->ID, '_nbm_price_range', true );
		$table_capacity      = get_post_meta( $post->ID, '_nbm_table_capacity', true );
		$slot_duration       = get_post_meta( $post->ID, '_nbm_slot_duration', true );
		$max_party_size      = get_post_meta( $post->ID, '_nbm_max_party_size', true );
		$min_notice_hours    = get_post_meta( $post->ID, '_nbm_min_notice_hours', true );
		$reservation_notes   = get_post_meta( $post->ID, '_nbm_reservation_instructions', true );
		$gallery_ids         = get_post_meta( $post->ID, '_nbm_gallery_ids', true );
		$opening_hours       = get_post_meta( $post->ID, '_nbm_opening_hours', true );

		if ( empty( $price_range ) ) { $price_range = '$$'; }
		if ( empty( $table_capacity ) ) { $table_capacity = 25; }
		if ( empty( $slot_duration ) ) { $slot_duration = NBM_Settings::get( 'bar_default_duration', 90 ); }
		if ( empty( $max_party_size ) ) { $max_party_size = NBM_Settings::get( 'bar_default_max_guests', 8 ); }
		if ( empty( $min_notice_hours ) ) { $min_notice_hours = NBM_Settings::get( 'bar_default_notice_hours', 2 ); }

		$days_of_week = array(
			'monday'    => __( 'Monday', 'nexora-booking-manager' ),
			'tuesday'   => __( 'Tuesday', 'nexora-booking-manager' ),
			'wednesday' => __( 'Wednesday', 'nexora-booking-manager' ),
			'thursday'  => __( 'Thursday', 'nexora-booking-manager' ),
			'friday'    => __( 'Friday', 'nexora-booking-manager' ),
			'saturday'  => __( 'Saturday', 'nexora-booking-manager' ),
			'sunday'    => __( 'Sunday', 'nexora-booking-manager' ),
		);

		if ( ! is_array( $opening_hours ) ) {
			$opening_hours = array();
		}
		?>
		<div class="nbm-meta-box-wrap">
			<style>
				.nbm-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
				.nbm-grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px; }
				.nbm-field-group { margin-bottom: 12px; }
				.nbm-field-group label { font-weight: 600; display: block; margin-bottom: 4px; }
				.nbm-field-group input[type="text"],
				.nbm-field-group input[type="number"],
				.nbm-field-group input[type="email"],
				.nbm-field-group input[type="url"],
				.nbm-field-group textarea,
				.nbm-field-group select { width: 100%; }
				.nbm-hours-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
				.nbm-hours-table th, .nbm-hours-table td { padding: 8px 12px; border: 1px solid #e2e8f0; text-align: left; }
				.nbm-hours-table th { background: #f8fafc; font-weight: 600; }
				.nbm-section-header { font-size: 14px; font-weight: 700; border-bottom: 2px solid #e2e8f0; padding-bottom: 6px; margin: 20px 0 12px; color: #1e293b; }
			</style>

			<div class="nbm-section-header"><?php esc_html_e( 'Location & Bar Information', 'nexora-booking-manager' ); ?></div>
			<div class="nbm-grid-2">
				<div class="nbm-field-group">
					<label for="nbm_bar_address"><?php esc_html_e( 'Street Address', 'nexora-booking-manager' ); ?></label>
					<input type="text" id="nbm_bar_address" name="nbm_address" value="<?php echo esc_attr( $address ); ?>" placeholder="e.g. 42 Rooftop Avenue" />
				</div>
				<div class="nbm-field-group">
					<label for="nbm_bar_city"><?php esc_html_e( 'City', 'nexora-booking-manager' ); ?></label>
					<input type="text" id="nbm_bar_city" name="nbm_city" value="<?php echo esc_attr( $city ); ?>" placeholder="e.g. Chicago" />
				</div>
			</div>
			<div class="nbm-grid-3">
				<div class="nbm-field-group">
					<label for="nbm_bar_phone"><?php esc_html_e( 'Phone Number', 'nexora-booking-manager' ); ?></label>
					<input type="text" id="nbm_bar_phone" name="nbm_phone" value="<?php echo esc_attr( $phone ); ?>" />
				</div>
				<div class="nbm-field-group">
					<label for="nbm_bar_email"><?php esc_html_e( 'Contact Email', 'nexora-booking-manager' ); ?></label>
					<input type="email" id="nbm_bar_email" name="nbm_email" value="<?php echo esc_attr( $email ); ?>" />
				</div>
				<div class="nbm-field-group">
					<label for="nbm_bar_menu_url"><?php esc_html_e( 'Menu URL (Link to Food & Drinks)', 'nexora-booking-manager' ); ?></label>
					<input type="url" id="nbm_bar_menu_url" name="nbm_menu_url" value="<?php echo esc_attr( $menu_url ); ?>" placeholder="https://..." />
				</div>
			</div>

			<div class="nbm-section-header"><?php esc_html_e( 'Capacity & Reservation Rules', 'nexora-booking-manager' ); ?></div>
			<div class="nbm-grid-4" style="display:grid; grid-template-columns: repeat(4, 1fr); gap:15px;">
				<div class="nbm-field-group">
					<label for="nbm_price_range"><?php esc_html_e( 'Price Range', 'nexora-booking-manager' ); ?></label>
					<select id="nbm_price_range" name="nbm_price_range">
						<option value="$" <?php selected( $price_range, '$' ); ?>>$ (Budget-Friendly)</option>
						<option value="$$" <?php selected( $price_range, '$$' ); ?>>$$ (Moderate)</option>
						<option value="$$$" <?php selected( $price_range, '$$$' ); ?>>$$$ (Fine & Craft)</option>
						<option value="$$$$" <?php selected( $price_range, '$$$$' ); ?>>$$$$ (Luxury VIP)</option>
					</select>
				</div>
				<div class="nbm-field-group">
					<label for="nbm_table_capacity"><?php esc_html_e( 'Table / Seating Capacity', 'nexora-booking-manager' ); ?></label>
					<input type="number" min="1" id="nbm_table_capacity" name="nbm_table_capacity" value="<?php echo esc_attr( $table_capacity ); ?>" required />
					<small class="description"><?php esc_html_e( 'Total seats/tables reserved concurrently.', 'nexora-booking-manager' ); ?></small>
				</div>
				<div class="nbm-field-group">
					<label for="nbm_slot_duration"><?php esc_html_e( 'Slot Duration (Minutes)', 'nexora-booking-manager' ); ?></label>
					<input type="number" min="15" step="15" id="nbm_slot_duration" name="nbm_slot_duration" value="<?php echo esc_attr( $slot_duration ); ?>" required />
				</div>
				<div class="nbm-field-group">
					<label for="nbm_max_party_size"><?php esc_html_e( 'Max Party Size', 'nexora-booking-manager' ); ?></label>
					<input type="number" min="1" id="nbm_max_party_size" name="nbm_max_party_size" value="<?php echo esc_attr( $max_party_size ); ?>" required />
				</div>
			</div>

			<div class="nbm-section-header"><?php esc_html_e( 'Weekly Opening Hours & Availability', 'nexora-booking-manager' ); ?></div>
			<p class="description"><?php esc_html_e( 'Reservations will only be accepted within these operating hours.', 'nexora-booking-manager' ); ?></p>
			<table class="nbm-hours-table">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Day', 'nexora-booking-manager' ); ?></th>
						<th><?php esc_html_e( 'Opening Time', 'nexora-booking-manager' ); ?></th>
						<th><?php esc_html_e( 'Closing Time', 'nexora-booking-manager' ); ?></th>
						<th><?php esc_html_e( 'Closed Today?', 'nexora-booking-manager' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $days_of_week as $day_key => $day_label ) :
						$open_val   = $opening_hours[ $day_key ]['open'] ?? '16:00';
						$close_val  = $opening_hours[ $day_key ]['close'] ?? '23:30';
						$is_closed  = ! empty( $opening_hours[ $day_key ]['closed'] );
					?>
						<tr>
							<td><strong><?php echo esc_html( $day_label ); ?></strong></td>
							<td>
								<input type="time" name="nbm_opening_hours[<?php echo esc_attr( $day_key ); ?>][open]" value="<?php echo esc_attr( $open_val ); ?>" />
							</td>
							<td>
								<input type="time" name="nbm_opening_hours[<?php echo esc_attr( $day_key ); ?>][close]" value="<?php echo esc_attr( $close_val ); ?>" />
							</td>
							<td>
								<label>
									<input type="checkbox" name="nbm_opening_hours[<?php echo esc_attr( $day_key ); ?>][closed]" value="1" <?php checked( $is_closed ); ?> />
									<?php esc_html_e( 'Closed', 'nexora-booking-manager' ); ?>
								</label>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>

			<div class="nbm-field-group" style="margin-top: 15px;">
				<label for="nbm_reservation_instructions"><?php esc_html_e( 'Reservation Instructions / Dress Code', 'nexora-booking-manager' ); ?></label>
				<textarea id="nbm_reservation_instructions" name="nbm_reservation_instructions" rows="3"><?php echo esc_textarea( $reservation_notes ); ?></textarea>
			</div>
			<div class="nbm-field-group">
				<label for="nbm_bar_gallery_ids"><?php esc_html_e( 'Image Gallery Attachment IDs (comma-separated)', 'nexora-booking-manager' ); ?></label>
				<input type="text" id="nbm_bar_gallery_ids" name="nbm_gallery_ids" value="<?php echo esc_attr( $gallery_ids ); ?>" placeholder="e.g. 201, 202, 203" />
			</div>
			<div class="nbm-field-group">
				<label for="nbm_bar_single_layout"><?php esc_html_e( 'Single Details Page Layout Design', 'nexora-booking-manager' ); ?></label>
				<?php $bar_single_layout = get_post_meta( $post->ID, '_nbm_single_layout', true ); ?>
				<select id="nbm_bar_single_layout" name="nbm_single_layout">
					<option value="" <?php selected( $bar_single_layout, '' ); ?>><?php esc_html_e( '— Use Global Default Studio Design —', 'nexora-booking-manager' ); ?></option>
					<option value="classic_hero" <?php selected( $bar_single_layout, 'classic_hero' ); ?>><?php esc_html_e( 'Layout 1: Classic Luxury Hero', 'nexora-booking-manager' ); ?></option>
					<option value="split_screen" <?php selected( $bar_single_layout, 'split_screen' ); ?>><?php esc_html_e( 'Layout 2: Modern 50/50 Split Screen', 'nexora-booking-manager' ); ?></option>
					<option value="fullscreen_cinematic" <?php selected( $bar_single_layout, 'fullscreen_cinematic' ); ?>><?php esc_html_e( 'Layout 3: Fullscreen Cinematic Showcase', 'nexora-booking-manager' ); ?></option>
					<option value="editorial_magazine" <?php selected( $bar_single_layout, 'editorial_magazine' ); ?>><?php esc_html_e( 'Layout 4: Editorial Magazine & Story', 'nexora-booking-manager' ); ?></option>
					<option value="tabbed_navigator" <?php selected( $bar_single_layout, 'tabbed_navigator' ); ?>><?php esc_html_e( 'Layout 5: Interactive Tabbed Navigator', 'nexora-booking-manager' ); ?></option>
				</select>
				<small class="description"><?php esc_html_e( 'Choose a bespoke layout for this venue or inherit the global design chosen in Shortcode Generator & Design Studio.', 'nexora-booking-manager' ); ?></small>
			</div>
		</div>
		<?php
	}

	/**
	 * Save Hotel Meta Box.
	 *
	 * @param int $post_id Post ID.
	 */
	public static function save_hotel_meta( $post_id ) {
		if ( ! isset( $_POST['nbm_hotel_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['nbm_hotel_nonce'] ), 'nbm_save_hotel_meta_action' ) ) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// Text fields
		$text_fields = array(
			'nbm_address'        => '_nbm_address',
			'nbm_city'           => '_nbm_city',
			'nbm_state'          => '_nbm_state',
			'nbm_country'        => '_nbm_country',
			'nbm_postal_code'    => '_nbm_postal_code',
			'nbm_phone'          => '_nbm_phone',
			'nbm_price_prefix'   => '_nbm_price_prefix',
			'nbm_checkin_time'   => '_nbm_checkin_time',
			'nbm_checkout_time'  => '_nbm_checkout_time',
			'nbm_gallery_ids'    => '_nbm_gallery_ids',
		);

		foreach ( $text_fields as $post_key => $meta_key ) {
			if ( isset( $_POST[ $post_key ] ) ) {
				update_post_meta( $post_id, $meta_key, sanitize_text_field( wp_unslash( $_POST[ $post_key ] ) ) );
			}
		}

		// URL & Email fields
		if ( isset( $_POST['nbm_map_url'] ) ) {
			update_post_meta( $post_id, '_nbm_map_url', esc_url_raw( wp_unslash( $_POST['nbm_map_url'] ) ) );
		}
		if ( isset( $_POST['nbm_email'] ) ) {
			update_post_meta( $post_id, '_nbm_email', sanitize_email( wp_unslash( $_POST['nbm_email'] ) ) );
		}

		// Numeric fields
		if ( isset( $_POST['nbm_price_per_night'] ) ) {
			update_post_meta( $post_id, '_nbm_price_per_night', max( 0, floatval( wp_unslash( $_POST['nbm_price_per_night'] ) ) ) );
		}
		if ( isset( $_POST['nbm_max_guests'] ) ) {
			update_post_meta( $post_id, '_nbm_max_guests', max( 1, absint( wp_unslash( $_POST['nbm_max_guests'] ) ) ) );
		}
		if ( isset( $_POST['nbm_total_rooms'] ) ) {
			update_post_meta( $post_id, '_nbm_total_rooms', max( 1, absint( wp_unslash( $_POST['nbm_total_rooms'] ) ) ) );
		}
		if ( isset( $_POST['nbm_min_stay'] ) ) {
			update_post_meta( $post_id, '_nbm_min_stay', max( 1, absint( wp_unslash( $_POST['nbm_min_stay'] ) ) ) );
		}
		if ( isset( $_POST['nbm_max_stay'] ) ) {
			update_post_meta( $post_id, '_nbm_max_stay', max( 1, absint( wp_unslash( $_POST['nbm_max_stay'] ) ) ) );
		}

		// Textarea
		if ( isset( $_POST['nbm_booking_rules'] ) ) {
			update_post_meta( $post_id, '_nbm_booking_rules', sanitize_textarea_field( wp_unslash( $_POST['nbm_booking_rules'] ) ) );
		}

		// Single page layout design
		if ( isset( $_POST['nbm_single_layout'] ) ) {
			$layout_choice = sanitize_key( wp_unslash( $_POST['nbm_single_layout'] ) );
			$valid_layouts = array( '', 'classic_hero', 'split_screen', 'fullscreen_cinematic', 'editorial_magazine', 'tabbed_navigator' );
			if ( in_array( $layout_choice, $valid_layouts, true ) ) {
				update_post_meta( $post_id, '_nbm_single_layout', $layout_choice );
			}
		}
	}

	/**
	 * Save Bar Meta Box.
	 *
	 * @param int $post_id Post ID.
	 */
	public static function save_bar_meta( $post_id ) {
		if ( ! isset( $_POST['nbm_bar_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['nbm_bar_nonce'] ), 'nbm_save_bar_meta_action' ) ) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// Text & URL fields
		if ( isset( $_POST['nbm_address'] ) ) {
			update_post_meta( $post_id, '_nbm_address', sanitize_text_field( wp_unslash( $_POST['nbm_address'] ) ) );
		}
		if ( isset( $_POST['nbm_city'] ) ) {
			update_post_meta( $post_id, '_nbm_city', sanitize_text_field( wp_unslash( $_POST['nbm_city'] ) ) );
		}
		if ( isset( $_POST['nbm_phone'] ) ) {
			update_post_meta( $post_id, '_nbm_phone', sanitize_text_field( wp_unslash( $_POST['nbm_phone'] ) ) );
		}
		if ( isset( $_POST['nbm_email'] ) ) {
			update_post_meta( $post_id, '_nbm_email', sanitize_email( wp_unslash( $_POST['nbm_email'] ) ) );
		}
		if ( isset( $_POST['nbm_menu_url'] ) ) {
			update_post_meta( $post_id, '_nbm_menu_url', esc_url_raw( wp_unslash( $_POST['nbm_menu_url'] ) ) );
		}
		if ( isset( $_POST['nbm_price_range'] ) ) {
			update_post_meta( $post_id, '_nbm_price_range', sanitize_text_field( wp_unslash( $_POST['nbm_price_range'] ) ) );
		}
		if ( isset( $_POST['nbm_gallery_ids'] ) ) {
			update_post_meta( $post_id, '_nbm_gallery_ids', sanitize_text_field( wp_unslash( $_POST['nbm_gallery_ids'] ) ) );
		}

		// Numbers
		if ( isset( $_POST['nbm_table_capacity'] ) ) {
			update_post_meta( $post_id, '_nbm_table_capacity', max( 1, absint( wp_unslash( $_POST['nbm_table_capacity'] ) ) ) );
		}
		if ( isset( $_POST['nbm_slot_duration'] ) ) {
			update_post_meta( $post_id, '_nbm_slot_duration', max( 15, absint( wp_unslash( $_POST['nbm_slot_duration'] ) ) ) );
		}
		if ( isset( $_POST['nbm_max_party_size'] ) ) {
			update_post_meta( $post_id, '_nbm_max_party_size', max( 1, absint( wp_unslash( $_POST['nbm_max_party_size'] ) ) ) );
		}

		// Textarea
		if ( isset( $_POST['nbm_reservation_instructions'] ) ) {
			update_post_meta( $post_id, '_nbm_reservation_instructions', sanitize_textarea_field( wp_unslash( $_POST['nbm_reservation_instructions'] ) ) );
		}

		// Opening hours array
		if ( isset( $_POST['nbm_opening_hours'] ) && is_array( $_POST['nbm_opening_hours'] ) ) {
			$sanitized_hours = array();
			$days = array( 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday' );

			foreach ( $days as $day ) {
				$day_data = $_POST['nbm_opening_hours'][ $day ] ?? array();
				$sanitized_hours[ $day ] = array(
					'open'   => sanitize_text_field( $day_data['open'] ?? '16:00' ),
					'close'  => sanitize_text_field( $day_data['close'] ?? '23:30' ),
					'closed' => ! empty( $day_data['closed'] ) ? 1 : 0,
				);
			}
			update_post_meta( $post_id, '_nbm_opening_hours', $sanitized_hours );
		}

		// Single page layout design
		if ( isset( $_POST['nbm_single_layout'] ) ) {
			$layout_choice = sanitize_key( wp_unslash( $_POST['nbm_single_layout'] ) );
			$valid_layouts = array( '', 'classic_hero', 'split_screen', 'fullscreen_cinematic', 'editorial_magazine', 'tabbed_navigator' );
			if ( in_array( $layout_choice, $valid_layouts, true ) ) {
				update_post_meta( $post_id, '_nbm_single_layout', $layout_choice );
			}
		}
	}

	/**
	 * Custom columns for Hotels list.
	 */
	public static function set_hotel_columns( $columns ) {
		$new = array();
		foreach ( $columns as $k => $v ) {
			if ( 'title' === $k ) {
				$new['nbm_thumb'] = __( 'Photo', 'nexora-booking-manager' );
				$new[ $k ]        = $v;
				$new['nbm_city']  = __( 'City', 'nexora-booking-manager' );
				$new['nbm_price'] = __( 'Nightly Price', 'nexora-booking-manager' );
				$new['nbm_rooms'] = __( 'Capacity', 'nexora-booking-manager' );
			} else {
				$new[ $k ] = $v;
			}
		}
		return $new;
	}

	/**
	 * Render Hotel custom columns content.
	 */
	public static function render_hotel_columns( $column, $post_id ) {
		switch ( $column ) {
			case 'nbm_thumb':
				if ( has_post_thumbnail( $post_id ) ) {
					echo get_the_post_thumbnail( $post_id, array( 50, 50 ), array( 'style' => 'border-radius:4px;object-fit:cover;' ) );
				} else {
					echo '<span style="color:#94a3b8;">&mdash;</span>';
				}
				break;
			case 'nbm_city':
				$city = get_post_meta( $post_id, '_nbm_city', true );
				echo esc_html( $city ? $city : '—' );
				break;
			case 'nbm_price':
				$price = get_post_meta( $post_id, '_nbm_price_per_night', true );
				echo esc_html( NBM_Settings::format_price( $price ) );
				break;
			case 'nbm_rooms':
				$rooms = get_post_meta( $post_id, '_nbm_total_rooms', true );
				$max_g = get_post_meta( $post_id, '_nbm_max_guests', true );
				printf( esc_html__( '%d rooms (max %d guests)', 'nexora-booking-manager' ), absint( $rooms ), absint( $max_g ) );
				break;
		}
	}

	/**
	 * Custom columns for Bars list.
	 */
	public static function set_bar_columns( $columns ) {
		$new = array();
		foreach ( $columns as $k => $v ) {
			if ( 'title' === $k ) {
				$new['nbm_thumb']    = __( 'Photo', 'nexora-booking-manager' );
				$new[ $k ]           = $v;
				$new['nbm_city']     = __( 'City', 'nexora-booking-manager' );
				$new['nbm_range']    = __( 'Price Range', 'nexora-booking-manager' );
				$new['nbm_capacity'] = __( 'Seating', 'nexora-booking-manager' );
			} else {
				$new[ $k ] = $v;
			}
		}
		return $new;
	}

	/**
	 * Render Bar custom columns content.
	 */
	public static function render_bar_columns( $column, $post_id ) {
		switch ( $column ) {
			case 'nbm_thumb':
				if ( has_post_thumbnail( $post_id ) ) {
					echo get_the_post_thumbnail( $post_id, array( 50, 50 ), array( 'style' => 'border-radius:4px;object-fit:cover;' ) );
				} else {
					echo '<span style="color:#94a3b8;">&mdash;</span>';
				}
				break;
			case 'nbm_city':
				$city = get_post_meta( $post_id, '_nbm_city', true );
				echo esc_html( $city ? $city : '—' );
				break;
			case 'nbm_range':
				$range = get_post_meta( $post_id, '_nbm_price_range', true );
				echo esc_html( $range ? $range : '—' );
				break;
			case 'nbm_capacity':
				$cap  = get_post_meta( $post_id, '_nbm_table_capacity', true );
				$dur  = get_post_meta( $post_id, '_nbm_slot_duration', true );
				printf( esc_html__( '%d seats (%d min slots)', 'nexora-booking-manager' ), absint( $cap ), absint( $dur ) );
				break;
		}
	}
}
