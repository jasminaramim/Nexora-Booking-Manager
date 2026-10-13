<?php
/**
 * Elementor Booking Form Widget.
 *
 * @package Nexora_Booking_Manager
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class NBM_Widget_Booking_Form extends \Elementor\Widget_Base {

	public function get_name() {
		return 'nbm_booking_form';
	}

	public function get_title() {
		return __( 'NBM Booking Form', 'nexora-booking-manager' );
	}

	public function get_icon() {
		return 'eicon-form-horizontal';
	}

	public function get_categories() {
		return array( 'nexora-booking' );
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_form',
			array( 'label' => __( 'Form Settings', 'nexora-booking-manager' ) )
		);

		$this->add_control(
			'type',
			array(
				'label'   => __( 'Listing Type', 'nexora-booking-manager' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'hotel',
				'options' => array(
					'hotel' => __( 'Hotel Booking', 'nexora-booking-manager' ),
					'bar'   => __( 'Bar Reservation', 'nexora-booking-manager' ),
				),
			)
		);

		$this->add_control(
			'listing_id',
			array(
				'label'       => __( 'Specific Listing ID', 'nexora-booking-manager' ),
				'type'        => \Elementor\Controls_Manager::NUMBER,
				'default'     => 0,
				'description' => __( 'Leave 0 to allow visitor to pick from dropdown, or auto-detect on single listing pages.', 'nexora-booking-manager' ),
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		echo do_shortcode( sprintf(
			'[nbm_booking_form type="%s" id="%s"]',
			esc_attr( $settings['type'] ),
			esc_attr( $settings['listing_id'] )
		) );
	}
}
