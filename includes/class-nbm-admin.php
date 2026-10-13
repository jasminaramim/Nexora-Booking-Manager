<?php
/**
 * Admin menus, dashboard, booking views, and administrative actions.
 *
 * @package Nexora_Booking_Manager
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class NBM_Admin {

	/**
	 * Initialize admin hooks and handlers.
	 */
	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'register_admin_menus' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_admin_assets' ) );

		// AJAX actions for booking management
		add_action( 'wp_ajax_nbm_admin_update_status', array( __CLASS__, 'handle_update_status' ) );
		add_action( 'wp_ajax_nbm_admin_add_note', array( __CLASS__, 'handle_add_note' ) );
		add_action( 'wp_ajax_nbm_admin_delete_booking', array( __CLASS__, 'handle_delete_booking' ) );
		add_action( 'wp_ajax_nbm_admin_send_test_email', array( __CLASS__, 'handle_send_test_email' ) );
		add_action( 'wp_ajax_nbm_admin_save_preset_defaults', array( __CLASS__, 'handle_save_preset_defaults' ) );
		add_action( 'wp_ajax_nbm_admin_save_single_layout', array( __CLASS__, 'handle_save_single_layout' ) );

		// Admin notices
		add_action( 'admin_notices', array( __CLASS__, 'render_admin_notices' ) );

		// CSV Export action
		add_action( 'admin_init', array( __CLASS__, 'maybe_export_csv' ) );

		// Custom admin footer branding
		add_filter( 'admin_footer_text', array( __CLASS__, 'filter_admin_footer' ) );
	}

	/**
	 * Register Admin Menus & Submenus.
	 */
	public static function register_admin_menus() {
		$pending_count = (int) NBM_Bookings::get_statistics()['pending'];
		$menu_title    = __( 'Nexora Booking', 'nexora-booking-manager' );

		if ( $pending_count > 0 ) {
			/* translators: %d: count of pending bookings */
			$menu_title .= sprintf( ' <span class="update-plugins count-%1$d"><span class="plugin-count">%1$d</span></span>', $pending_count );
		}

		// Top Level Menu
		add_menu_page(
			__( 'Nexora Booking Manager', 'nexora-booking-manager' ),
			$menu_title,
			'manage_options',
			'nbm-dashboard',
			array( __CLASS__, 'render_dashboard_page' ),
			'dashicons-calendar-alt',
			26
		);

		// Submenu: Dashboard
		add_submenu_page(
			'nbm-dashboard',
			__( 'Dashboard — Nexora Booking', 'nexora-booking-manager' ),
			__( 'Dashboard', 'nexora-booking-manager' ),
			'manage_options',
			'nbm-dashboard',
			array( __CLASS__, 'render_dashboard_page' )
		);

		// Submenu: All Hotels (CPT)
		add_submenu_page(
			'nbm-dashboard',
			__( 'Hotels', 'nexora-booking-manager' ),
			__( 'All Hotels', 'nexora-booking-manager' ),
			'manage_options',
			'edit.php?post_type=nbm_hotel'
		);

		// Submenu: Add New Hotel
		add_submenu_page(
			'nbm-dashboard',
			__( 'Add New Hotel', 'nexora-booking-manager' ),
			__( 'Add New Hotel', 'nexora-booking-manager' ),
			'manage_options',
			'post-new.php?post_type=nbm_hotel'
		);

		// Submenu: All Bars (CPT)
		add_submenu_page(
			'nbm-dashboard',
			__( 'Bars', 'nexora-booking-manager' ),
			__( 'All Bars', 'nexora-booking-manager' ),
			'manage_options',
			'edit.php?post_type=nbm_bar'
		);

		// Submenu: Add New Bar
		add_submenu_page(
			'nbm-dashboard',
			__( 'Add New Bar', 'nexora-booking-manager' ),
			__( 'Add New Bar', 'nexora-booking-manager' ),
			'manage_options',
			'post-new.php?post_type=nbm_bar'
		);

		// Submenu: All Bookings
		$bookings_title = __( 'All Bookings', 'nexora-booking-manager' );
		if ( $pending_count > 0 ) {
			$bookings_title .= sprintf( ' <span class="awaiting-mod count-%1$d"><span class="pending-count">%1$d</span></span>', $pending_count );
		}

		add_submenu_page(
			'nbm-dashboard',
			__( 'Bookings Management — Nexora Booking', 'nexora-booking-manager' ),
			$bookings_title,
			'manage_options',
			'nbm-bookings',
			array( __CLASS__, 'render_bookings_page' )
		);

		// Submenu: Shortcode Generator
		add_submenu_page(
			'nbm-dashboard',
			__( 'Shortcode Generator & Card Presets', 'nexora-booking-manager' ),
			__( 'Shortcode Generator', 'nexora-booking-manager' ),
			'manage_options',
			'nbm-shortcodes',
			array( __CLASS__, 'render_shortcodes_page' )
		);

		// Submenu: Settings
		add_submenu_page(
			'nbm-dashboard',
			__( 'Settings — Nexora Booking', 'nexora-booking-manager' ),
			__( 'Settings', 'nexora-booking-manager' ),
			'manage_options',
			'nbm-settings',
			array( __CLASS__, 'render_settings_page' )
		);
	}

	/**
	 * Render Shortcode Generator Page.
	 */
	public static function render_shortcodes_page() {
		if ( ! NBM_Security::current_user_can_manage() ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'nexora-booking-manager' ) );
		}

		include NBM_PLUGIN_DIR . 'admin/views/shortcodes-generator.php';
	}

	/**
	 * Enqueue admin CSS & JS.
	 *
	 * @param string $hook_suffix Current admin page.
	 */
	public static function enqueue_admin_assets( $hook_suffix ) {
		// Only load on plugin admin pages or CPT edit pages
		$is_nbm_page = (
			strpos( $hook_suffix, 'nbm-' ) !== false ||
			'nbm_hotel' === get_post_type() ||
			'nbm_bar' === get_post_type()
		);

		if ( ! $is_nbm_page ) {
			return;
		}

		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_style(
			'nbm-admin-css',
			NBM_PLUGIN_URL . 'assets/css/admin.css',
			array(),
			NBM_VERSION
		);

		if ( strpos( $hook_suffix, 'nbm-shortcodes' ) !== false ) {
			wp_enqueue_style(
				'nbm-frontend-css',
				NBM_PLUGIN_URL . 'assets/css/frontend.css',
				array(),
				NBM_VERSION
			);
		}

		wp_enqueue_script( 'wp-color-picker' );
		wp_enqueue_script(
			'nbm-admin-js',
			NBM_PLUGIN_URL . 'assets/js/admin.js',
			array( 'jquery', 'wp-color-picker' ),
			NBM_VERSION,
			true
		);

		wp_localize_script(
			'nbm-admin-js',
			'nbm_admin_vars',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( 'nbm_admin_nonce' ),
				'i18n'     => array(
					'confirm_status' => __( 'Are you sure you want to change the status of this booking?', 'nexora-booking-manager' ),
					'confirm_delete' => __( 'Are you sure you want to permanently delete this booking? This action cannot be undone.', 'nexora-booking-manager' ),
					'updating'       => __( 'Updating status...', 'nexora-booking-manager' ),
					'deleting'       => __( 'Deleting...', 'nexora-booking-manager' ),
					'success'        => __( 'Status updated successfully.', 'nexora-booking-manager' ),
				),
			)
		);
	}

	/**
	 * Render Dashboard Page.
	 */
	public static function render_dashboard_page() {
		if ( ! NBM_Security::current_user_can_manage() ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'nexora-booking-manager' ) );
		}

		$stats = NBM_Bookings::get_statistics();
		$recent_bookings = NBM_Bookings::query_bookings( array( 'per_page' => 8, 'paged' => 1 ) )['items'];

		include NBM_PLUGIN_DIR . 'admin/views/dashboard.php';
	}

	/**
	 * Render Bookings List or Details Page.
	 */
	public static function render_bookings_page() {
		if ( ! NBM_Security::current_user_can_manage() ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'nexora-booking-manager' ) );
		}

		$action     = sanitize_key( $_GET['action'] ?? 'list' );
		$booking_id = absint( $_GET['id'] ?? 0 );

		if ( 'view' === $action && $booking_id > 0 ) {
			$booking = NBM_Bookings::get_booking( $booking_id );
			if ( ! $booking ) {
				echo '<div class="notice notice-error"><p>' . esc_html__( 'Booking not found.', 'nexora-booking-manager' ) . '</p></div>';
				return;
			}
			$notes = NBM_Bookings::get_booking_notes( $booking_id );
			include NBM_PLUGIN_DIR . 'admin/views/booking-details.php';
		} else {
			$status       = sanitize_key( $_GET['status'] ?? '' );
			$listing_type = sanitize_key( $_GET['listing_type'] ?? '' );
			$search       = sanitize_text_field( $_GET['s'] ?? '' );
			$paged        = max( 1, absint( $_GET['paged'] ?? 1 ) );

			$results = NBM_Bookings::query_bookings( array(
				'status'       => $status,
				'listing_type' => $listing_type,
				'search'       => $search,
				'paged'        => $paged,
				'per_page'     => 15,
			) );

			include NBM_PLUGIN_DIR . 'admin/views/bookings-list.php';
		}
	}

	/**
	 * Render Settings Page.
	 */
	public static function render_settings_page() {
		if ( ! NBM_Security::current_user_can_manage() ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'nexora-booking-manager' ) );
		}

		include NBM_PLUGIN_DIR . 'admin/views/settings.php';
	}

	/**
	 * AJAX: Update booking status with availability recheck.
	 */
	public static function handle_update_status() {
		if ( ! NBM_Security::current_user_can_manage() ) {
			wp_send_json_error( array( 'message' => __( 'Permission denied.', 'nexora-booking-manager' ) ) );
		}

		check_ajax_referer( 'nbm_admin_nonce', 'nonce' );

		$id         = absint( $_POST['booking_id'] ?? 0 );
		$new_status = sanitize_key( $_POST['new_status'] ?? '' );
		$note       = sanitize_textarea_field( $_POST['note'] ?? '' );

		$updated = NBM_Bookings::update_status( $id, $new_status, $note, true );

		if ( is_wp_error( $updated ) ) {
			wp_send_json_error( array( 'message' => $updated->get_error_message() ) );
		}

		wp_send_json_success( array(
			'message'    => __( 'Booking status updated successfully.', 'nexora-booking-manager' ),
			'new_status' => $updated->status,
		) );
	}

	/**
	 * AJAX: Add internal admin note to booking.
	 */
	public static function handle_add_note() {
		if ( ! NBM_Security::current_user_can_manage() ) {
			wp_send_json_error( array( 'message' => __( 'Permission denied.', 'nexora-booking-manager' ) ) );
		}

		check_ajax_referer( 'nbm_admin_nonce', 'nonce' );

		$id   = absint( $_POST['booking_id'] ?? 0 );
		$note = sanitize_textarea_field( $_POST['note'] ?? '' );

		if ( empty( $note ) ) {
			wp_send_json_error( array( 'message' => __( 'Note cannot be empty.', 'nexora-booking-manager' ) ) );
		}

		$note_id = NBM_Bookings::add_booking_note( $id, $note, 'admin', get_current_user_id() );

		if ( ! $note_id ) {
			wp_send_json_error( array( 'message' => __( 'Could not save note.', 'nexora-booking-manager' ) ) );
		}

		wp_send_json_success( array(
			'message' => __( 'Note added successfully.', 'nexora-booking-manager' ),
		) );
	}

	/**
	 * AJAX: Delete booking.
	 */
	public static function handle_delete_booking() {
		if ( ! NBM_Security::current_user_can_manage() ) {
			wp_send_json_error( array( 'message' => __( 'Permission denied.', 'nexora-booking-manager' ) ) );
		}

		check_ajax_referer( 'nbm_admin_nonce', 'nonce' );

		$id = absint( $_POST['booking_id'] ?? 0 );

		if ( ! NBM_Bookings::delete_booking( $id ) ) {
			wp_send_json_error( array( 'message' => __( 'Failed to delete booking.', 'nexora-booking-manager' ) ) );
		}

		wp_send_json_success( array( 'message' => __( 'Booking deleted successfully.', 'nexora-booking-manager' ) ) );
	}

	/**
	 * AJAX: Send test email diagnostic.
	 */
	public static function handle_send_test_email() {
		if ( ! NBM_Security::current_user_can_manage() ) {
			wp_send_json_error( array( 'message' => __( 'Permission denied.', 'nexora-booking-manager' ) ) );
		}

		check_ajax_referer( 'nbm_admin_nonce', 'nonce' );

		$email = sanitize_email( $_POST['test_email'] ?? '' );
		if ( empty( $email ) || ! is_email( $email ) ) {
			wp_send_json_error( array( 'message' => __( 'Please enter a valid email address.', 'nexora-booking-manager' ) ) );
		}

		$result = NBM_Emails::send_test_email( $email );

		if ( ! empty( $result['success'] ) ) {
			wp_send_json_success( array( 'message' => $result['message'] ) );
		} else {
			wp_send_json_error( array( 'message' => $result['message'] ) );
		}
	}

	/**
	 * AJAX: Save Card Preset and Customization Defaults.
	 */
	public static function handle_save_preset_defaults() {
		if ( ! NBM_Security::current_user_can_manage() ) {
			wp_send_json_error( array( 'message' => __( 'Permission denied.', 'nexora-booking-manager' ) ) );
		}

		check_ajax_referer( 'nbm_admin_nonce', 'nonce' );

		$type    = sanitize_key( $_POST['entity_type'] ?? 'bars' );
		$preset  = sanitize_key( $_POST['preset'] ?? 'modern' );
		$cols    = min( 4, max( 1, absint( $_POST['columns'] ?? 3 ) ) );
		$perpage = min( 48, max( 1, absint( $_POST['per_page'] ?? 6 ) ) );
		$accent  = sanitize_hex_color( $_POST['accent_color'] ?? '' );
		$cardbg  = sanitize_hex_color( $_POST['card_bg'] ?? '' );
		$btn_lbl = sanitize_text_field( $_POST['btn_text'] ?? '' );

		$to_save = array();
		if ( 'bars' === $type ) {
			$to_save['bar_default_card_preset']  = $preset;
			$to_save['bar_default_columns']      = $cols;
			$to_save['bar_default_per_page']     = $perpage;
			if ( $accent ) {
				$to_save['bar_default_accent_color'] = $accent;
			}
			if ( $cardbg ) {
				$to_save['bar_default_card_bg'] = $cardbg;
			}
			$to_save['bar_default_btn_text'] = $btn_lbl;
		} else {
			$to_save['hotel_default_card_preset']  = $preset;
			$to_save['hotel_default_columns']      = $cols;
			$to_save['hotel_default_per_page']     = $perpage;
			if ( $accent ) {
				$to_save['hotel_default_accent_color'] = $accent;
			}
			if ( $cardbg ) {
				$to_save['hotel_default_card_bg'] = $cardbg;
			}
			$to_save['hotel_default_btn_text'] = $btn_lbl;
		}

		NBM_Settings::update_settings( $to_save );

		$preset_names = array(
			'modern'            => __( 'Modern Sophisticate', 'nexora-booking-manager' ),
			'luxury'            => __( 'Luxury VIP & Nightlife', 'nexora-booking-manager' ),
			'glassmorphism'     => __( 'Glassmorphism Overlay', 'nexora-booking-manager' ),
			'minimal'           => __( 'Minimalist Editorial', 'nexora-booking-manager' ),
			'neon'              => __( 'Neon Cyberpunk & Electric Lounge', 'nexora-booking-manager' ),
			'vintage'           => __( 'Vintage Speakeasy & Bourbon Lounge', 'nexora-booking-manager' ),
			'bistro_horizontal' => __( 'Horizontal Executive Suite', 'nexora-booking-manager' ),
		);
		$preset_title = $preset_names[ $preset ] ?? ucfirst( $preset );

		wp_send_json_success( array(
			'message' => sprintf(
				/* translators: 1: entity name, 2: preset title */
				__( '✓ Successfully saved %1$s default design preset "%2$s" and styling settings to database!', 'nexora-booking-manager' ),
				( 'bars' === $type ? __( 'Bar & Lounge', 'nexora-booking-manager' ) : __( 'Hotel & Resort', 'nexora-booking-manager' ) ),
				$preset_title
			),
		) );
	}

	/**
	 * AJAX: Save Single Details Page Layout Design.
	 */
	public static function handle_save_single_layout() {
		if ( ! NBM_Security::current_user_can_manage() ) {
			wp_send_json_error( array( 'message' => __( 'Permission denied.', 'nexora-booking-manager' ) ) );
		}

		check_ajax_referer( 'nbm_admin_nonce', 'nonce' );

		$design = sanitize_key( $_POST['single_details_design'] ?? 'classic_hero' );
		$valid_designs = array( 'classic_hero', 'split_screen', 'fullscreen_cinematic', 'editorial_magazine', 'tabbed_navigator' );
		if ( ! in_array( $design, $valid_designs, true ) ) {
			$design = 'classic_hero';
		}

		NBM_Settings::update_settings( array(
			'single_details_design' => $design,
		) );

		$names = array(
			'classic_hero'         => __( 'Design 1: Classic Luxury Hero', 'nexora-booking-manager' ),
			'split_screen'         => __( 'Design 2: Modern 50/50 Split Screen', 'nexora-booking-manager' ),
			'fullscreen_cinematic' => __( 'Design 3: Fullscreen Cinematic Showcase', 'nexora-booking-manager' ),
			'editorial_magazine'   => __( 'Design 4: Editorial Magazine & Story', 'nexora-booking-manager' ),
			'tabbed_navigator'     => __( 'Design 5: Interactive Tabbed Navigator', 'nexora-booking-manager' ),
		);

		wp_send_json_success( array(
			'message' => sprintf(
				/* translators: %s: layout name */
				__( '✓ Successfully saved single page layout: "%s"! Your live venue pages will now render with this design.', 'nexora-booking-manager' ),
				$names[ $design ] ?? $design
			),
		) );
	}

	/**
	 * Export bookings as CSV if requested.
	 */
	public static function maybe_export_csv() {
		if ( ! isset( $_GET['nbm_action'] ) || 'export_csv' !== $_GET['nbm_action'] ) {
			return;
		}

		if ( ! NBM_Security::current_user_can_manage() ) {
			wp_die( esc_html__( 'Unauthorized.', 'nexora-booking-manager' ) );
		}

		check_admin_referer( 'nbm_export_csv' );

		$status       = sanitize_key( $_GET['status'] ?? '' );
		$listing_type = sanitize_key( $_GET['listing_type'] ?? '' );

		$results = NBM_Bookings::query_bookings( array(
			'status'       => $status,
			'listing_type' => $listing_type,
			'per_page'     => 1000,
			'paged'        => 1,
		) );

		$filename = 'nexora-bookings-' . gmdate( 'Y-m-d-His' ) . '.csv';

		header( 'Content-Type: text/csv; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=' . $filename );

		$output = fopen( 'php://output', 'w' );

		// Header row
		fputcsv( $output, array(
			'Booking Code',
			'Listing Name',
			'Listing Type',
			'Guest Name',
			'Guest Email',
			'Guest Phone',
			'Check In / Date',
			'Check Out / Time',
			'Guests',
			'Status',
			'Guest Message',
			'Admin Note',
			'Submitted Date',
		) );

		foreach ( $results['items'] as $item ) {
			$listing_name = get_the_title( $item->listing_id );
			$date_1       = ( 'hotel' === $item->listing_type ) ? $item->check_in : $item->booking_date;
			$date_2       = ( 'hotel' === $item->listing_type ) ? $item->check_out : $item->booking_time;

			fputcsv( $output, array(
				$item->booking_code,
				$listing_name,
				ucfirst( $item->listing_type ),
				$item->guest_name,
				$item->guest_email,
				$item->guest_phone,
				$date_1,
				$date_2,
				$item->guest_count,
				ucfirst( $item->status ),
				$item->guest_message,
				$item->admin_note,
				$item->created_at,
			) );
		}

		fclose( $output );
		exit;
	}

	/**
	 * Render Admin notices if needed.
	 */
	public static function render_admin_notices() {
		// Can show welcome or configuration hints
	}

	/**
	 * Custom footer text on Nexora Booking Manager admin pages.
	 *
	 * @param string $text Existing footer text.
	 * @return string
	 */
	public static function filter_admin_footer( $text ) {
		$screen = get_current_screen();
		if ( $screen && ( false !== strpos( $screen->id, 'nbm' ) || false !== strpos( $screen->post_type, 'nbm_' ) ) ) {
			return sprintf(
				/* translators: 1: plugin name, 2: developer branding */
				esc_html__( 'Thank you for creating with %1$s — Developed by %2$s', 'nexora-booking-manager' ),
				'<strong>' . esc_html__( 'Nexora Booking Manager', 'nexora-booking-manager' ) . '</strong>',
				'<strong>[Nexora Development Team] by Jasmin</strong>'
			);
		}
		return $text;
	}
}
