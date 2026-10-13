<?php
/**
 * Elementor Search & Filter Widget.
 *
 * @package Nexora_Booking_Manager
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class NBM_Widget_Search extends \Elementor\Widget_Base {

	public function get_name() {
		return 'nbm_search_filter';
	}

	public function get_title() {
		return __( 'NBM Search & Filter', 'nexora-booking-manager' );
	}

	public function get_icon() {
		return 'eicon-search';
	}

	public function get_categories() {
		return array( 'nexora-booking' );
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_search',
			array( 'label' => __( 'Search Options', 'nexora-booking-manager' ) )
		);

		$this->add_control(
			'type',
			array(
				'label'   => __( 'Search Type', 'nexora-booking-manager' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'all',
				'options' => array(
					'all'   => __( 'All Listings (Hotels & Bars)', 'nexora-booking-manager' ),
					'hotel' => __( 'Hotels Only', 'nexora-booking-manager' ),
					'bar'   => __( 'Bars Only', 'nexora-booking-manager' ),
				),
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		echo do_shortcode( sprintf(
			'[nbm_booking_search type="%s"]',
			esc_attr( $settings['type'] )
		) );
	}
}
