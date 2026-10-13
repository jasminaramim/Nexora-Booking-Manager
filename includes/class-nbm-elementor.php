<?php
/**
 * Elementor integration and widget registrations.
 *
 * @package Nexora_Booking_Manager
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class NBM_Elementor {

	/**
	 * Initialize Elementor integration if Elementor is loaded.
	 */
	public static function init() {
		// Elementor check
		if ( ! did_action( 'elementor/loaded' ) && ! defined( 'ELEMENTOR_VERSION' ) ) {
			return;
		}

		add_action( 'elementor/init', array( __CLASS__, 'on_elementor_init' ) );
	}

	/**
	 * Check if Elementor is active and register category & widgets.
	 */
	public static function on_elementor_init() {
		// Category registration
		if ( version_compare( ELEMENTOR_VERSION, '2.0.0', '>=' ) ) {
			add_action( 'elementor/elements/categories_registered', array( __CLASS__, 'register_category' ) );
		}

		// Widgets registration (Elementor 3.5.0+)
		if ( version_compare( ELEMENTOR_VERSION, '3.5.0', '>=' ) ) {
			add_action( 'elementor/widgets/register', array( __CLASS__, 'register_widgets_new' ) );
		} else {
			add_action( 'elementor/widgets/widgets_registered', array( __CLASS__, 'register_widgets_legacy' ) );
		}

		// Enqueue elementor editor assets if needed
		add_action( 'elementor/frontend/after_enqueue_styles', array( __CLASS__, 'enqueue_widget_styles' ) );
	}

	/**
	 * Register Nexora Booking widget category.
	 *
	 * @param \Elementor\Elements_Manager $elements_manager
	 */
	public static function register_category( $elements_manager ) {
		$elements_manager->add_category(
			'nexora-booking',
			array(
				'title' => __( 'Nexora Booking', 'nexora-booking-manager' ),
				'icon'  => 'fa fa-calendar',
			)
		);
	}

	/**
	 * Load widget classes.
	 */
	private static function load_widget_files() {
		require_once NBM_PLUGIN_DIR . 'includes/elementor/class-nbm-widget-hotel-grid.php';
		require_once NBM_PLUGIN_DIR . 'includes/elementor/class-nbm-widget-bar-grid.php';
		require_once NBM_PLUGIN_DIR . 'includes/elementor/class-nbm-widget-booking-form.php';
		require_once NBM_PLUGIN_DIR . 'includes/elementor/class-nbm-widget-search.php';
	}

	/**
	 * Register widgets with new Elementor Widgets Manager API (3.5+).
	 *
	 * @param \Elementor\Widgets_Manager $widgets_manager
	 */
	public static function register_widgets_new( $widgets_manager ) {
		self::load_widget_files();

		$widgets_manager->register( new NBM_Widget_Hotel_Grid() );
		$widgets_manager->register( new NBM_Widget_Bar_Grid() );
		$widgets_manager->register( new NBM_Widget_Booking_Form() );
		$widgets_manager->register( new NBM_Widget_Search() );
	}

	/**
	 * Register widgets with legacy Elementor API.
	 *
	 * @param \Elementor\Widgets_Manager $widgets_manager
	 */
	public static function register_widgets_legacy( $widgets_manager ) {
		self::load_widget_files();

		$widgets_manager->register_widget_type( new NBM_Widget_Hotel_Grid() );
		$widgets_manager->register_widget_type( new NBM_Widget_Bar_Grid() );
		$widgets_manager->register_widget_type( new NBM_Widget_Booking_Form() );
		$widgets_manager->register_widget_type( new NBM_Widget_Search() );
	}

	/**
	 * Enqueue Elementor widget frontend stylesheet.
	 */
	public static function enqueue_widget_styles() {
		wp_enqueue_style(
			'nbm-elementor-css',
			NBM_PLUGIN_URL . 'assets/css/elementor.css',
			array( 'nbm-frontend-css' ),
			NBM_VERSION
		);
	}
}
