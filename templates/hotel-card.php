<?php
/**
 * Master Template Part: Hotel Card Router.
 *
 * Dynamically loads the selected preset ('modern', 'luxury', 'glassmorphism', 'minimal', 'neon', 'vintage', 'bistro_horizontal').
 *
 * @package Nexora_Booking_Manager
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$preset = ! empty( $preset ) ? sanitize_key( $preset ) : NBM_Settings::get( 'hotel_default_card_preset', 'modern' );

$preset_file = NBM_PLUGIN_DIR . 'templates/hotel-cards/card-' . $preset . '.php';

if ( file_exists( $preset_file ) ) {
	include $preset_file;
} else {
	include NBM_PLUGIN_DIR . 'templates/hotel-cards/card-modern.php';
}
