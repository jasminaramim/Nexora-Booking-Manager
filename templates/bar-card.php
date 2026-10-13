<?php
/**
 * Master Template Part: Bar Card Router.
 *
 * Dynamically loads the selected preset ('modern', 'luxury', 'glassmorphism', 'minimal').
 *
 * @package Nexora_Booking_Manager
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$preset = ! empty( $preset ) ? sanitize_key( $preset ) : NBM_Settings::get( 'bar_default_card_preset', 'modern' );

$preset_file = NBM_PLUGIN_DIR . 'templates/bar-cards/card-' . $preset . '.php';

if ( file_exists( $preset_file ) ) {
	include $preset_file;
} else {
	include NBM_PLUGIN_DIR . 'templates/bar-cards/card-modern.php';
}
