<?php
/**
 * Security and validation utilities.
 *
 * @package Nexora_Booking_Manager
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class NBM_Security {

	/**
	 * Get client IP safely for rate limiting.
	 *
	 * @return string
	 */
	public static function get_client_ip() {
		$ip = '127.0.0.1';
		if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
			$ip = sanitize_text_field( wp_unslash( $_SERVER['HTTP_CLIENT_IP'] ) );
		} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			// First IP in forward list is client.
			$parts = explode( ',', sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) );
			$ip    = trim( $parts[0] );
		} elseif ( ! empty( $_SERVER['REMOTE_ADDR'] ) ) {
			$ip = sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) );
		}
		return filter_var( $ip, FILTER_VALIDATE_IP ) ? $ip : '127.0.0.1';
	}

	/**
	 * Check rate limit for an action.
	 *
	 * @param string $action Action key.
	 * @param int    $max_attempts Max allowed requests in time window.
	 * @param int    $window_seconds Window in seconds (default 300 = 5 mins).
	 * @return bool True if allowed, false if rate limited.
	 */
	public static function check_rate_limit( $action = 'booking_submit', $max_attempts = 10, $window_seconds = 300 ) {
		$ip       = self::get_client_ip();
		$key      = 'nbm_rate_' . md5( $action . '_' . $ip );
		$attempts = (int) get_transient( $key );

		if ( $attempts >= $max_attempts ) {
			return false;
		}

		return true;
	}

	/**
	 * Increment rate limit count for the client IP.
	 *
	 * @param string $action Action key.
	 * @param int    $window_seconds Window in seconds.
	 */
	public static function record_rate_limit_attempt( $action = 'booking_submit', $window_seconds = 300 ) {
		$ip       = self::get_client_ip();
		$key      = 'nbm_rate_' . md5( $action . '_' . $ip );
		$attempts = (int) get_transient( $key );

		if ( 0 === $attempts ) {
			set_transient( $key, 1, $window_seconds );
		} else {
			set_transient( $key, $attempts + 1, $window_seconds );
		}
	}

	/**
	 * Verify honeypot anti-spam field.
	 *
	 * @param array $data Submitted data.
	 * @return bool True if clean, false if bot filled honeypot.
	 */
	public static function verify_honeypot( $data ) {
		$honeypot_field = 'nbm_hp_website';
		if ( ! empty( $data[ $honeypot_field ] ) ) {
			return false; // Spam bot trapped!
		}
		return true;
	}

	/**
	 * Generate an unpredictable and human-readable unique booking reference code.
	 * Format: NBM-H-XXXXXX or NBM-B-XXXXXX
	 *
	 * @param string $type 'hotel' or 'bar'.
	 * @return string
	 */
	public static function generate_booking_code( $type = 'hotel' ) {
		global $wpdb;

		$prefix = ( 'bar' === $type ) ? 'NBM-BAR-' : 'NBM-HTL-';
		$table  = NBM_Database::get_bookings_table();

		// Generate random high-entropy token and ensure uniqueness.
		for ( $i = 0; $i < 5; $i++ ) {
			$random = strtoupper( substr( bin2hex( random_bytes( 5 ) ), 0, 8 ) );
			$code   = $prefix . $random;

			$exists = $wpdb->get_var(
				$wpdb->prepare( "SELECT id FROM {$table} WHERE booking_code = %s", $code )
			);

			if ( ! $exists ) {
				return $code;
			}
		}

		// Fallback unique token
		return $prefix . strtoupper( wp_generate_password( 10, false, false ) );
	}

	/**
	 * Sanitize and validate date string in YYYY-MM-DD format.
	 *
	 * @param string $date Raw date.
	 * @return string|false Validated date or false.
	 */
	public static function sanitize_date( $date ) {
		if ( empty( $date ) ) {
			return false;
		}
		$date = sanitize_text_field( $date );
		$dt   = DateTime::createFromFormat( 'Y-m-d', $date );
		if ( $dt && $dt->format( 'Y-m-d' ) === $date ) {
			return $date;
		}
		return false;
	}

	/**
	 * Sanitize and validate time string in HH:MM or HH:MM:SS format.
	 *
	 * @param string $time Raw time.
	 * @return string|false Validated time or false.
	 */
	public static function sanitize_time( $time ) {
		if ( empty( $time ) ) {
			return false;
		}
		$time = sanitize_text_field( $time );
		// Match HH:MM or HH:MM:SS
		if ( preg_match( '/^([01][0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/', $time ) ) {
			return strlen( $time ) === 5 ? $time . ':00' : $time;
		}
		return false;
	}

	/**
	 * Check if current user has administrative capabilities.
	 *
	 * @return bool
	 */
	public static function current_user_can_manage() {
		return current_user_can( 'manage_options' );
	}
}
