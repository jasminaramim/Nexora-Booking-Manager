<?php
/**
 * Elementor Hotel Grid Widget.
 *
 * @package Nexora_Booking_Manager
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class NBM_Widget_Hotel_Grid extends \Elementor\Widget_Base {

	public function get_name() {
		return 'nbm_hotel_grid';
	}

	public function get_title() {
		return __( 'NBM Hotel Grid', 'nexora-booking-manager' );
	}

	public function get_icon() {
		return 'eicon-gallery-grid';
	}

	public function get_categories() {
		return array( 'nexora-booking' );
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_content',
			array( 'label' => __( 'Query & Layout', 'nexora-booking-manager' ) )
		);

		$this->add_control(
			'preset',
			array(
				'label'   => __( 'Card Design Preset', 'nexora-booking-manager' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'modern',
				'options' => array(
					'modern'            => __( 'Modern Sophisticate (Preset 1)', 'nexora-booking-manager' ),
					'luxury'            => __( 'Luxury VIP & Gold (Preset 2)', 'nexora-booking-manager' ),
					'glassmorphism'     => __( 'Glassmorphism Overlay (Preset 3)', 'nexora-booking-manager' ),
					'minimal'           => __( 'Minimalist Editorial (Preset 4)', 'nexora-booking-manager' ),
					'neon'              => __( 'Neon Cyberpunk (Preset 5)', 'nexora-booking-manager' ),
					'vintage'           => __( 'Vintage Speakeasy (Preset 6)', 'nexora-booking-manager' ),
					'bistro_horizontal' => __( 'Horizontal Executive (Preset 7)', 'nexora-booking-manager' ),
				),
			)
		);

		$this->add_control(
			'columns',
			array(
				'label'   => __( 'Columns', 'nexora-booking-manager' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => '3',
				'options' => array(
					'1' => '1 Column',
					'2' => '2 Columns',
					'3' => '3 Columns',
					'4' => '4 Columns',
				),
			)
		);

		$this->add_control(
			'per_page',
			array(
				'label'   => __( 'Number of Hotels', 'nexora-booking-manager' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'default' => 6,
				'min'     => 1,
				'max'     => 48,
			)
		);

		$this->add_control(
			'city',
			array(
				'label'   => __( 'Filter by City', 'nexora-booking-manager' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => '',
			)
		);

		$this->add_control(
			'order',
			array(
				'label'   => __( 'Sort Order', 'nexora-booking-manager' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'DESC',
				'options' => array(
					'DESC' => 'Newest First',
					'ASC'  => 'Oldest First',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		echo do_shortcode( sprintf(
			'[nbm_hotels preset="%s" columns="%s" per_page="%s" city="%s" order="%s"]',
			esc_attr( $settings['preset'] ?? 'modern' ),
			esc_attr( $settings['columns'] ),
			esc_attr( $settings['per_page'] ),
			esc_attr( $settings['city'] ),
			esc_attr( $settings['order'] )
		) );
	}
}
