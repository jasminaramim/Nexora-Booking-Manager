<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * Deletes database tables and options only if the administrator
 * explicitly opted in via the "Delete all data on uninstall" setting.
 *
 * @package Nexora_Booking_Manager
 */

// If uninstall not called from WordPress, exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Check opt-in setting.
$delete_data = get_option( 'nbm_delete_data_on_uninstall', '0' );

if ( '1' === (string) $delete_data || true === $delete_data ) {
	global $wpdb;

	// Drop custom tables.
	$bookings_table = $wpdb->prefix . 'nbm_bookings';
	$notes_table    = $wpdb->prefix . 'nbm_booking_notes';

	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.SchemaChange
	$wpdb->query( "DROP TABLE IF EXISTS {$bookings_table}" );
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.SchemaChange
	$wpdb->query( "DROP TABLE IF EXISTS {$notes_table}" );

	// Delete all plugin options.
	$options = array(
		'nbm_db_version',
		'nbm_flush_rewrite_rules',
		'nbm_settings',
		'nbm_delete_data_on_uninstall',
	);

	foreach ( $options as $opt ) {
		delete_option( $opt );
	}

	// Clean any transients matching nbm_*.
	$wpdb->query(
		"DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_nbm_%' OR option_name LIKE '_transient_timeout_nbm_%'"
	);
}
