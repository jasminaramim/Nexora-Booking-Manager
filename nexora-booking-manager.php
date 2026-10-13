<?php
/**
 * Plugin Name:       Nexora Booking Manager
 * Plugin URI:        https://nexorabooking.com
 * Description:       A modern, standalone Hotel and Bar reservation management system with request-based bookings, availability conflict prevention, and Elementor integration.
 * Version:           1.0.0
 * Author:            Jasmin
 * Author URI:        https://nexorabooking.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       nexora-booking-manager
 * Domain Path:       /languages
 * Requires at least: 5.8
 * Requires PHP:      7.4
 *
 * @package Nexora_Booking_Manager
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Current plugin version and constants.
 */
define( 'NBM_VERSION', '1.0.0' );
define( 'NBM_DB_VERSION', '1.0.0' );
define( 'NBM_PLUGIN_FILE', __FILE__ );
define( 'NBM_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'NBM_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'NBM_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

/**
 * Autoload core class files.
 */
require_once NBM_PLUGIN_DIR . 'includes/class-nbm-database.php';
require_once NBM_PLUGIN_DIR . 'includes/class-nbm-activator.php';
require_once NBM_PLUGIN_DIR . 'includes/class-nbm-deactivator.php';
require_once NBM_PLUGIN_DIR . 'includes/class-nbm-security.php';
require_once NBM_PLUGIN_DIR . 'includes/class-nbm-settings.php';
require_once NBM_PLUGIN_DIR . 'includes/class-nbm-post-types.php';
require_once NBM_PLUGIN_DIR . 'includes/class-nbm-availability.php';
require_once NBM_PLUGIN_DIR . 'includes/class-nbm-emails.php';
require_once NBM_PLUGIN_DIR . 'includes/class-nbm-bookings.php';
require_once NBM_PLUGIN_DIR . 'includes/class-nbm-shortcodes.php';
require_once NBM_PLUGIN_DIR . 'includes/class-nbm-frontend.php';
require_once NBM_PLUGIN_DIR . 'includes/class-nbm-admin.php';
require_once NBM_PLUGIN_DIR . 'includes/class-nbm-elementor.php';

/**
 * Activation Hook.
 */
function nbm_activate_plugin() {
	NBM_Activator::activate();
}
register_activation_hook( __FILE__, 'nbm_activate_plugin' );

/**
 * Deactivation Hook.
 */
function nbm_deactivate_plugin() {
	NBM_Deactivator::deactivate();
}
register_deactivation_hook( __FILE__, 'nbm_deactivate_plugin' );

/**
 * The core plugin class that coordinates all modules.
 */
final class Nexora_Booking_Manager {

	/**
	 * Single instance of the plugin.
	 *
	 * @var Nexora_Booking_Manager|null
	 */
	private static $instance = null;

	/**
	 * Main instance getter.
	 *
	 * @return Nexora_Booking_Manager
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Private constructor.
	 */
	private function __construct() {
		$this->init_hooks();
	}

	/**
	 * Initialize WordPress action hooks and filters.
	 */
	private function init_hooks() {
		add_action( 'plugins_loaded', array( $this, 'on_plugins_loaded' ) );
		add_action( 'init', array( $this, 'load_textdomain' ) );
		add_action( 'init', array( $this, 'maybe_flush_rewrites' ), 99 );
	}

	/**
	 * Fire on plugins loaded: initialize components.
	 */
	public function on_plugins_loaded() {
		// Initialize database if migration needed.
		NBM_Database::maybe_update_database();

		// Initialize components.
		NBM_Settings::init();
		NBM_Post_Types::init();
		NBM_Emails::init();
		NBM_Shortcodes::init();
		NBM_Frontend::init();

		if ( is_admin() ) {
			NBM_Admin::init();
		}

		// Initialize Elementor integration if present.
		NBM_Elementor::init();
	}

	/**
	 * Load plugin textdomain for translations.
	 */
	public function load_textdomain() {
		load_plugin_textdomain(
			'nexora-booking-manager',
			false,
			dirname( plugin_basename( __FILE__ ) ) . '/languages'
		);
	}

	/**
	 * Safely flush rewrite rules once after activation if needed.
	 */
	public function maybe_flush_rewrites() {
		if ( get_option( 'nbm_flush_rewrite_rules', false ) ) {
			delete_option( 'nbm_flush_rewrite_rules' );
			flush_rewrite_rules();
		}
	}
}

/**
 * Bootstrap the plugin instance.
 */
function nbm_run() {
	return Nexora_Booking_Manager::get_instance();
}
nbm_run();
