<?php
/**
 * Elementor Bar Grid Widget.
 *
 * @package Nexora_Booking_Manager
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class NBM_Widget_Bar_Grid extends \Elementor\Widget_Base {

	public function get_name() {
		return 'nbm_bar_grid';
	}

	public function get_title() {
		return __( 'NBM Bar Grid', 'nexora-booking-manager' );
	}

	public function get_icon() {
		return 'eicon-posts-grid';
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
					'modern'        => __( 'Modern Sophisticate (Preset 1)', 'nexora-booking-manager' ),
					'luxury'        => __( 'Luxury VIP & Gold (Preset 2)', 'nexora-booking-manager' ),
					'glassmorphism' => __( 'Glassmorphism Overlay (Preset 3)', 'nexora-booking-manager' ),
					'minimal'       => __( 'Minimalist Editorial (Preset 4)', 'nexora-booking-manager' ),
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
				'label'   => __( 'Number of Bars', 'nexora-booking-manager' ),
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
			'btn_text',
			array(
				'label'   => __( 'Custom Button Text', 'nexora-booking-manager' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => '',
				'placeholder' => __( 'e.g. Reserve Table, VIP Access', 'nexora-booking-manager' ),
			)
		);

		$this->add_control(
			'accent_color',
			array(
				'label'   => __( 'Card Accent Color', 'nexora-booking-manager' ),
				'type'    => \Elementor\Controls_Manager::COLOR,
				'default' => '',
			)
		);

		$this->add_control(
			'card_bg',
			array(
				'label'   => __( 'Card Background Color', 'nexora-booking-manager' ),
				'type'    => \Elementor\Controls_Manager::COLOR,
				'default' => '',
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		echo do_shortcode( sprintf(
			'[nbm_bars preset="%s" columns="%s" per_page="%s" city="%s" btn_text="%s" accent_color="%s" card_bg="%s"]',
			esc_attr( $settings['preset'] ?? 'modern' ),
			esc_attr( $settings['columns'] ),
			esc_attr( $settings['per_page'] ),
			esc_attr( $settings['city'] ),
			esc_attr( $settings['btn_text'] ?? '' ),
			esc_attr( $settings['accent_color'] ?? '' ),
			esc_attr( $settings['card_bg'] ?? '' )
		) );
	}
}
