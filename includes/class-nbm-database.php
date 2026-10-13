<?php
/**
 * Database schema installation and migration manager.
 *
 * @package Nexora_Booking_Manager
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class NBM_Database {

	/**
	 * Get the booking table name with WP prefix.
	 *
	 * @return string
	 */
	public static function get_bookings_table() {
		global $wpdb;
		return $wpdb->prefix . 'nbm_bookings';
	}

	/**
	 * Get the booking notes / audit history table name with WP prefix.
	 *
	 * @return string
	 */
	public static function get_notes_table() {
		global $wpdb;
		return $wpdb->prefix . 'nbm_booking_notes';
	}

	/**
	 * Check if database needs an upgrade and run dbDelta if so.
	 */
	public static function maybe_update_database() {
		$installed_version = get_option( 'nbm_db_version', '0' );

		if ( version_compare( $installed_version, NBM_DB_VERSION, '<' ) ) {
			self::install_schema();
			update_option( 'nbm_db_version', NBM_DB_VERSION );
		}
	}

	/**
	 * Create or upgrade custom database tables using dbDelta.
	 */
	public static function install_schema() {
		global $wpdb;

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$charset_collate = $wpdb->get_charset_collate();
		$bookings_table  = self::get_bookings_table();
		$notes_table     = self::get_notes_table();

		// Schema for bookings table.
		// Note: dbDelta requires two spaces after PRIMARY KEY, lowercase SQL keywords, etc.
		$sql_bookings = "CREATE TABLE {$bookings_table} (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			booking_code varchar(32) NOT NULL,
			listing_id bigint(20) unsigned NOT NULL,
			listing_type varchar(20) NOT NULL DEFAULT 'hotel',
			guest_name varchar(190) NOT NULL,
			guest_email varchar(190) NOT NULL,
			guest_phone varchar(50) DEFAULT NULL,
			check_in date DEFAULT NULL,
			check_out date DEFAULT NULL,
			booking_date date DEFAULT NULL,
			booking_time time DEFAULT NULL,
			guest_count smallint(5) unsigned NOT NULL DEFAULT 1,
			status varchar(20) NOT NULL DEFAULT 'pending',
			guest_message text DEFAULT NULL,
			admin_note text DEFAULT NULL,
			created_at datetime NOT NULL,
			updated_at datetime NOT NULL,
			PRIMARY KEY  (id),
			UNIQUE KEY booking_code (booking_code),
			KEY listing_dates (listing_id, check_in, check_out),
			KEY listing_bar (listing_id, booking_date, booking_time),
			KEY booking_status (status),
			KEY guest_email (guest_email),
			KEY created_at (created_at)
		) {$charset_collate};";

		// Schema for internal audit notes and status history.
		$sql_notes = "CREATE TABLE {$notes_table} (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			booking_id bigint(20) unsigned NOT NULL,
			author_id bigint(20) unsigned NOT NULL DEFAULT 0,
			note_type varchar(50) NOT NULL DEFAULT 'system',
			note text NOT NULL,
			created_at datetime NOT NULL,
			PRIMARY KEY  (id),
			KEY booking_id (booking_id),
			KEY created_at (created_at)
		) {$charset_collate};";

		dbDelta( $sql_bookings );
		dbDelta( $sql_notes );
	}
}
