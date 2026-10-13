<?php
/**
 * Fired during plugin deactivation.
 *
 * @package Nexora_Booking_Manager
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class NBM_Deactivator {

	/**
	 * Run deactivation tasks.
	 * Preserves all database tables, listings, and configurations.
	 */
	public static function deactivate() {
		flush_rewrite_rules();
	}
}
