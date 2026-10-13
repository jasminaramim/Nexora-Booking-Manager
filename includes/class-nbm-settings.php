<?php
/**
 * Plugin settings and configurations management.
 *
 * @package Nexora_Booking_Manager
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class NBM_Settings {

	/**
	 * Settings option name in wp_options.
	 */
	const OPTION_NAME = 'nbm_settings';

	/**
	 * Cached settings array.
	 *
	 * @var array|null
	 */
	private static $settings = null;

	/**
	 * Initialize settings hooks.
	 */
	public static function init() {
		add_action( 'admin_init', array( __CLASS__, 'register_settings' ) );
	}

	/**
	 * Default plugin settings.
	 *
	 * @return array
	 */
	public static function get_defaults() {
		return array(
			// General
			'currency_code'               => 'USD',
			'currency_symbol'             => '$',
			'currency_position'           => 'before', // 'before' or 'after'
			'date_format'                 => 'Y-m-d',
			'time_format'                 => 'H:i',

			// Hotels
			'hotel_default_checkin'       => '14:00',
			'hotel_default_checkout'      => '11:00',
			'hotel_default_min_stay'      => 1,
			'hotel_default_max_stay'      => 30,
			'hotel_default_max_guests'    => 4,

			// Bars
			'bar_default_duration'        => 90, // minutes
			'bar_default_max_guests'      => 8,
			'bar_default_notice_hours'    => 2,  // min hours before booking
			'bar_default_card_preset'     => 'modern', // 'modern', 'luxury', 'glassmorphism', 'minimal', 'neon', 'vintage', 'bistro_horizontal'
			'bar_default_columns'         => 3,
			'bar_default_per_page'        => 6,
			'bar_default_accent_color'    => '#1e40af',
			'bar_default_card_bg'         => '#ffffff',
			'bar_default_btn_text'        => '',

			// Hotels
			'hotel_default_card_preset'   => 'modern',
			'hotel_default_columns'       => 3,
			'hotel_default_per_page'      => 6,
			'hotel_default_accent_color'  => '#1e40af',
			'hotel_default_card_bg'       => '#ffffff',
			'hotel_default_btn_text'      => '',

			// Single Details Page Layout Design Presets
			'single_details_design'       => 'classic_hero', // 'classic_hero', 'split_screen', 'fullscreen_cinematic', 'editorial_magazine', 'tabbed_navigator'

			// Booking Workflow & Rules
			'pending_hold_hours'          => 24,
			'require_phone'               => '0',
			'enable_conflict_prevention'  => '1',

			// Emails
			'admin_email'                 => get_option( 'admin_email' ),
			'notify_admin_new_booking'    => '1',
			'notify_guest_pending'        => '1',
			'notify_guest_status_change'  => '1',
			'email_sender_name'           => get_bloginfo( 'name' ),
			'email_sender_address'        => get_option( 'admin_email' ),

			// Design & Styles
			'primary_color'               => '#1e40af', // Sleek Navy
			'accent_color'                => '#0d9488', // Teal
			'card_radius'                 => '12',      // px

			// Security
			'rate_limit_max'              => 10,
			'rate_limit_window'           => 300,
			'enable_honeypot'             => '1',

			// Data retention
			'delete_data_on_uninstall'    => '0',
		);
	}

	/**
	 * Set default options in database if not yet stored.
	 */
	public static function set_default_settings() {
		$existing = get_option( self::OPTION_NAME, false );
		if ( false === $existing ) {
			update_option( self::OPTION_NAME, self::get_defaults() );
		}
	}

	/**
	 * Get a specific setting value or all settings.
	 *
	 * @param string|null $key Setting key.
	 * @param mixed       $default Fallback default.
	 * @return mixed
	 */
	public static function get( $key = null, $default = null ) {
		if ( null === self::$settings ) {
			$defaults       = self::get_defaults();
			$stored         = get_option( self::OPTION_NAME, array() );
			self::$settings = wp_parse_args( is_array( $stored ) ? $stored : array(), $defaults );
		}

		if ( null === $key ) {
			return self::$settings;
		}

		return isset( self::$settings[ $key ] ) ? self::$settings[ $key ] : $default;
	}

	/**
	 * Register WordPress settings.
	 */
	public static function register_settings() {
		register_setting(
			'nbm_settings_group',
			self::OPTION_NAME,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( __CLASS__, 'sanitize_settings' ),
			)
		);
	}

	/**
	 * Sanitize all settings before saving.
	 *
	 * @param array $input Raw input.
	 * @return array Sanitized input.
	 */
	public static function sanitize_settings( $input ) {
		$defaults  = self::get_defaults();
		$sanitized = array();

		// Currency
		$sanitized['currency_code']     = sanitize_text_field( $input['currency_code'] ?? $defaults['currency_code'] );
		$sanitized['currency_symbol']   = sanitize_text_field( $input['currency_symbol'] ?? $defaults['currency_symbol'] );
		$sanitized['currency_position'] = in_array( $input['currency_position'] ?? '', array( 'before', 'after' ), true )
			? $input['currency_position']
			: $defaults['currency_position'];
		$sanitized['date_format']       = sanitize_text_field( $input['date_format'] ?? $defaults['date_format'] );
		$sanitized['time_format']       = sanitize_text_field( $input['time_format'] ?? $defaults['time_format'] );

		// Hotel rules
		$sanitized['hotel_default_checkin']    = sanitize_text_field( $input['hotel_default_checkin'] ?? $defaults['hotel_default_checkin'] );
		$sanitized['hotel_default_checkout']   = sanitize_text_field( $input['hotel_default_checkout'] ?? $defaults['hotel_default_checkout'] );
		$sanitized['hotel_default_min_stay']   = max( 1, absint( $input['hotel_default_min_stay'] ?? $defaults['hotel_default_min_stay'] ) );
		$sanitized['hotel_default_max_stay']   = max( $sanitized['hotel_default_min_stay'], absint( $input['hotel_default_max_stay'] ?? $defaults['hotel_default_max_stay'] ) );
		$sanitized['hotel_default_max_guests'] = max( 1, absint( $input['hotel_default_max_guests'] ?? $defaults['hotel_default_max_guests'] ) );

		// Bar rules
		$sanitized['bar_default_duration']     = max( 15, absint( $input['bar_default_duration'] ?? $defaults['bar_default_duration'] ) );
		$sanitized['bar_default_max_guests']   = max( 1, absint( $input['bar_default_max_guests'] ?? $defaults['bar_default_max_guests'] ) );
		$sanitized['bar_default_notice_hours'] = absint( $input['bar_default_notice_hours'] ?? $defaults['bar_default_notice_hours'] );
		$preset_input = sanitize_key( $input['bar_default_card_preset'] ?? '' );
		$sanitized['bar_default_card_preset'] = in_array( $preset_input, array( 'modern', 'luxury', 'glassmorphism', 'minimal' ), true )
			? $preset_input
			: $defaults['bar_default_card_preset'];

		// Workflow
		$sanitized['pending_hold_hours']         = absint( $input['pending_hold_hours'] ?? $defaults['pending_hold_hours'] );
		$sanitized['require_phone']              = ! empty( $input['require_phone'] ) ? '1' : '0';
		$sanitized['enable_conflict_prevention'] = ! empty( $input['enable_conflict_prevention'] ) ? '1' : '0';

		// Emails
		$sanitized['admin_email']                = sanitize_email( $input['admin_email'] ?? $defaults['admin_email'] );
		$sanitized['notify_admin_new_booking']   = ! empty( $input['notify_admin_new_booking'] ) ? '1' : '0';
		$sanitized['notify_guest_pending']       = ! empty( $input['notify_guest_pending'] ) ? '1' : '0';
		$sanitized['notify_guest_status_change'] = ! empty( $input['notify_guest_status_change'] ) ? '1' : '0';
		$sanitized['email_sender_name']          = sanitize_text_field( $input['email_sender_name'] ?? $defaults['email_sender_name'] );
		$sanitized['email_sender_address']       = sanitize_email( $input['email_sender_address'] ?? $defaults['email_sender_address'] );

		// Styles
		$primary = sanitize_hex_color( $input['primary_color'] ?? '' );
		$accent  = sanitize_hex_color( $input['accent_color'] ?? '' );
		$sanitized['primary_color'] = $primary ? $primary : $defaults['primary_color'];
		$sanitized['accent_color']  = $accent ? $accent : $defaults['accent_color'];
		$sanitized['card_radius']   = absint( $input['card_radius'] ?? $defaults['card_radius'] );

		// Security
		$sanitized['rate_limit_max']    = max( 1, absint( $input['rate_limit_max'] ?? $defaults['rate_limit_max'] ) );
		$sanitized['rate_limit_window'] = max( 60, absint( $input['rate_limit_window'] ?? $defaults['rate_limit_window'] ) );
		$sanitized['enable_honeypot']   = ! empty( $input['enable_honeypot'] ) ? '1' : '0';

		// Data retention & uninstall
		$delete_data = ! empty( $input['delete_data_on_uninstall'] ) ? '1' : '0';
		$sanitized['delete_data_on_uninstall'] = $delete_data;
		update_option( 'nbm_delete_data_on_uninstall', $delete_data );

		self::$settings = $sanitized;
		return $sanitized;
	}

	/**
	 * Helper to format price according to settings.
	 *
	 * @param float|int|string $price Numeric price.
	 * @return string Formatted price with symbol.
	 */
	public static function format_price( $price ) {
		$numeric = floatval( $price );
		$symbol  = self::get( 'currency_symbol', '$' );
		$pos     = self::get( 'currency_position', 'before' );
		$formatted = number_format_i18n( $numeric, 2 );

		if ( 'after' === $pos ) {
			return $formatted . ' ' . $symbol;
		}
		return $symbol . $formatted;
	}

	/**
	 * Update one or more settings and refresh cache.
	 *
	 * @param array $new_values Key-value pairs to update.
	 * @return bool
	 */
	public static function update_settings( $new_values ) {
		$current = self::get();
		$merged  = wp_parse_args( $new_values, $current );
		$updated = update_option( self::OPTION_NAME, $merged );
		self::$settings = $merged;
		return (bool) $updated;
	}
}
