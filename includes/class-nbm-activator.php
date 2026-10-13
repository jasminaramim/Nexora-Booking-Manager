<?php
/**
 * Fired during plugin activation.
 *
 * @package Nexora_Booking_Manager
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class NBM_Activator {

	/**
	 * Run activation tasks.
	 */
	public static function activate() {
		// Install custom database tables.
		NBM_Database::install_schema();
		update_option( 'nbm_db_version', NBM_DB_VERSION );

		// Register post types to prepare for rewrite rule flushing.
		NBM_Post_Types::register_post_types();
		NBM_Post_Types::register_taxonomies();

		// Schedule a one-time rewrite flush on next request.
		update_option( 'nbm_flush_rewrite_rules', 1 );

		// Initialize default settings if not already present.
		NBM_Settings::set_default_settings();
	}
}
