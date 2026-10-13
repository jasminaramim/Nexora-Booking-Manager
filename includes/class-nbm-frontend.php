<?php
/**
 * Frontend asset management, template routing, and public AJAX handlers.
 *
 * @package Nexora_Booking_Manager
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class NBM_Frontend {

	/**
	 * Initialize frontend hooks.
	 */
	public static function init() {
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );
		add_filter( 'template_include', array( __CLASS__, 'template_loader' ) );

		// AJAX Submission handlers
		add_action( 'wp_ajax_nbm_submit_booking', array( __CLASS__, 'handle_booking_submission' ) );
		add_action( 'wp_ajax_nopriv_nbm_submit_booking', array( __CLASS__, 'handle_booking_submission' ) );

		// AJAX Availability recheck
		add_action( 'wp_ajax_nbm_check_availability', array( __CLASS__, 'handle_check_availability' ) );
		add_action( 'wp_ajax_nopriv_nbm_check_availability', array( __CLASS__, 'handle_check_availability' ) );

		// AJAX Booking Lookup
		add_action( 'wp_ajax_nbm_lookup_booking', array( __CLASS__, 'handle_lookup_booking' ) );
		add_action( 'wp_ajax_nopriv_nbm_lookup_booking', array( __CLASS__, 'handle_lookup_booking' ) );
	}

	/**
	 * Enqueue frontend CSS and JS.
	 */
	public static function enqueue_assets() {
		wp_enqueue_style(
			'nbm-frontend-css',
			NBM_PLUGIN_URL . 'assets/css/frontend.css',
			array(),
			NBM_VERSION
		);

		// Dynamic color variables
		$primary = sanitize_hex_color( NBM_Settings::get( 'primary_color', '#1e40af' ) );
		$accent  = sanitize_hex_color( NBM_Settings::get( 'accent_color', '#0d9488' ) );
		$radius  = absint( NBM_Settings::get( 'card_radius', 12 ) ) . 'px';

		$inline_css = ":root {
			--nbm-primary: {$primary};
			--nbm-accent: {$accent};
			--nbm-radius: {$radius};
		}";
		wp_add_inline_style( 'nbm-frontend-css', $inline_css );

		wp_enqueue_script(
			'nbm-frontend-js',
			NBM_PLUGIN_URL . 'assets/js/frontend.js',
			array( 'jquery' ),
			NBM_VERSION,
			true
		);

		wp_localize_script(
			'nbm-frontend-js',
			'nbm_vars',
			array(
				'ajax_url'        => admin_url( 'admin-ajax.php' ),
				'nonce'           => wp_create_nonce( 'nbm_frontend_nonce' ),
				'currency_symbol' => NBM_Settings::get( 'currency_symbol', '$' ),
				'i18n'            => array(
					'processing'   => __( 'Processing request...', 'nexora-booking-manager' ),
					'checking'     => __( 'Checking availability...', 'nexora-booking-manager' ),
					'success_msg'  => __( 'Request submitted successfully!', 'nexora-booking-manager' ),
					'error_msg'    => __( 'An error occurred. Please try again.', 'nexora-booking-manager' ),
					'rate_limited' => __( 'Too many requests. Please wait a few moments before trying again.', 'nexora-booking-manager' ),
				),
			)
		);
	}

	/**
	 * Template loader for single hotel and single bar listings.
	 *
	 * @param string $template Current template file.
	 * @return string
	 */
	public static function template_loader( $template ) {
		if ( is_singular( 'nbm_hotel' ) ) {
			// Look in theme first, then fallback to plugin template
			$theme_template = locate_template( array( 'single-nbm_hotel.php', 'single-hotel.php' ) );
			if ( ! empty( $theme_template ) ) {
				return $theme_template;
			}
			$plugin_template = NBM_PLUGIN_DIR . 'templates/single-hotel.php';
			if ( file_exists( $plugin_template ) ) {
				return $plugin_template;
			}
		}

		if ( is_singular( 'nbm_bar' ) ) {
			$theme_template = locate_template( array( 'single-nbm_bar.php', 'single-bar.php' ) );
			if ( ! empty( $theme_template ) ) {
				return $theme_template;
			}
			$plugin_template = NBM_PLUGIN_DIR . 'templates/single-bar.php';
			if ( file_exists( $plugin_template ) ) {
				return $plugin_template;
			}
		}

		return $template;
	}

	/**
	 * Public AJAX booking submission handler.
	 */
	public static function handle_booking_submission() {
		// Nonce check
		if ( ! check_ajax_referer( 'nbm_frontend_nonce', 'nonce', false ) ) {
			wp_send_json_error( array( 'message' => __( 'Security check failed. Please refresh the page and try again.', 'nexora-booking-manager' ) ) );
		}

		// Anti-spam Honeypot check
		if ( ! NBM_Security::verify_honeypot( $_POST ) ) {
			// Silently fail for bots
			wp_send_json_error( array( 'message' => __( 'Submission flagged by spam protection.', 'nexora-booking-manager' ) ) );
		}

		// Rate limiting check
		$max_attempts = (int) NBM_Settings::get( 'rate_limit_max', 10 );
		$window       = (int) NBM_Settings::get( 'rate_limit_window', 300 );

		if ( ! NBM_Security::check_rate_limit( 'booking_submit', $max_attempts, $window ) ) {
			wp_send_json_error( array( 'message' => __( 'Too many submissions from your connection. Please wait a few minutes.', 'nexora-booking-manager' ) ) );
		}

		NBM_Security::record_rate_limit_attempt( 'booking_submit', $window );

		// Process booking creation
		$booking = NBM_Bookings::create_booking( $_POST );

		if ( is_wp_error( $booking ) ) {
			wp_send_json_error( array( 'message' => $booking->get_error_message() ) );
		}

		$success_msg = sprintf(
			/* translators: 1: booking reference code */
			__( 'Thank you! Your booking request has been submitted under reference %s. We have sent confirmation details to your email and our staff will review your reservation shortly.', 'nexora-booking-manager' ),
			$booking->booking_code
		);

		wp_send_json_success( array(
			'message'      => $success_msg,
			'booking_code' => $booking->booking_code,
			'status'       => $booking->status,
		) );
	}

	/**
	 * Real-time AJAX availability check handler.
	 */
	public static function handle_check_availability() {
		check_ajax_referer( 'nbm_frontend_nonce', 'nonce' );

		$listing_id   = absint( $_POST['listing_id'] ?? 0 );
		$listing_type = sanitize_key( $_POST['listing_type'] ?? 'hotel' );
		$guest_count  = max( 1, absint( $_POST['guest_count'] ?? 1 ) );

		if ( 'hotel' === $listing_type ) {
			$check_in  = sanitize_text_field( $_POST['check_in'] ?? '' );
			$check_out = sanitize_text_field( $_POST['check_out'] ?? '' );
			$res       = NBM_Availability::check_hotel_availability( $listing_id, $check_in, $check_out, $guest_count );
		} else {
			$date = sanitize_text_field( $_POST['booking_date'] ?? '' );
			$time = sanitize_text_field( $_POST['booking_time'] ?? '' );
			$res  = NBM_Availability::check_bar_availability( $listing_id, $date, $time, $guest_count );
		}

		if ( is_wp_error( $res ) ) {
			wp_send_json_error( array( 'message' => $res->get_error_message() ) );
		}

		wp_send_json_success( array(
			'message' => __( 'Selected dates/times are available!', 'nexora-booking-manager' ),
			'details' => $res,
		) );
	}

	/**
	 * Public AJAX booking lookup for [nbm_my_bookings].
	 */
	public static function handle_lookup_booking() {
		check_ajax_referer( 'nbm_frontend_nonce', 'nonce' );

		$code  = sanitize_text_field( $_POST['booking_code'] ?? '' );
		$email = sanitize_email( $_POST['guest_email'] ?? '' );

		if ( empty( $code ) || empty( $email ) ) {
			wp_send_json_error( array( 'message' => __( 'Please enter both your Booking Reference and Email address.', 'nexora-booking-manager' ) ) );
		}

		$booking = NBM_Bookings::get_booking_by_code( $code );

		if ( ! $booking || strtolower( trim( $booking->guest_email ) ) !== strtolower( trim( $email ) ) ) {
			wp_send_json_error( array( 'message' => __( 'No booking found matching that reference code and email combination.', 'nexora-booking-manager' ) ) );
		}

		$listing_title = get_the_title( $booking->listing_id );

		$dates_info = ( 'hotel' === $booking->listing_type )
			/* translators: 1: check in date, 2: check out date */
			? sprintf( __( 'Check-in: %1$s | Check-out: %2$s', 'nexora-booking-manager' ), $booking->check_in, $booking->check_out )
			/* translators: 1: booking date, 2: booking time */
			: sprintf( __( 'Date: %1$s at %2$s', 'nexora-booking-manager' ), $booking->booking_date, $booking->booking_time );

		wp_send_json_success( array(
			'booking_code'  => esc_html( $booking->booking_code ),
			'listing_title' => esc_html( $listing_title ),
			'listing_type'  => esc_html( ucfirst( $booking->listing_type ) ),
			'guest_name'    => esc_html( $booking->guest_name ),
			'schedule'      => esc_html( $dates_info ),
			'guest_count'   => absint( $booking->guest_count ),
			'status'        => esc_html( ucfirst( $booking->status ) ),
			'status_raw'    => esc_html( $booking->status ),
			'created_at'    => esc_html( $booking->created_at ),
		) );
	}
}
