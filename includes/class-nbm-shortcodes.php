<?php
/**
 * Shortcodes registration and rendering engine.
 *
 * @package Nexora_Booking_Manager
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class NBM_Shortcodes {

	/**
	 * Unique counter for multiple shortcode instances on one page.
	 *
	 * @var int
	 */
	private static $instance_count = 0;

	/**
	 * Initialize shortcode registrations.
	 */
	public static function init() {
		add_shortcode( 'nbm_hotels', array( __CLASS__, 'render_hotels' ) );
		add_shortcode( 'nbm_bars', array( __CLASS__, 'render_bars' ) );
		add_shortcode( 'nbm_booking_form', array( __CLASS__, 'render_booking_form' ) );
		add_shortcode( 'nbm_booking_search', array( __CLASS__, 'render_booking_search' ) );
		add_shortcode( 'nbm_my_bookings', array( __CLASS__, 'render_my_bookings' ) );
	}

	/**
	 * Get next unique instance ID.
	 *
	 * @return int
	 */
	public static function get_next_instance_id() {
		self::$instance_count++;
		return self::$instance_count;
	}

	/**
	 * Render [nbm_hotels] shortcode.
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string HTML output.
	 */
	public static function render_hotels( $atts ) {
		$atts = shortcode_atts(
			array(
				'preset'         => NBM_Settings::get( 'hotel_default_card_preset', 'modern' ),
				'columns'        => NBM_Settings::get( 'hotel_default_columns', 3 ),
				'per_page'       => NBM_Settings::get( 'hotel_default_per_page', 9 ),
				'city'           => '',
				'orderby'        => 'date',
				'order'          => 'DESC',
				'pagination'     => 'yes',
				'card_bg'        => '',
				'accent_color'   => '',
				'btn_text'       => '',
				'gap'            => '',
			),
			$atts,
			'nbm_hotels'
		);

		$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : ( ( get_query_var( 'page' ) ) ? get_query_var( 'page' ) : 1 );

		$query_args = array(
			'post_type'      => 'nbm_hotel',
			'post_status'    => 'publish',
			'posts_per_page' => intval( $atts['per_page'] ),
			'paged'          => $paged,
			'orderby'        => sanitize_key( $atts['orderby'] ),
			'order'          => sanitize_key( $atts['order'] ),
		);

		if ( ! empty( $atts['city'] ) ) {
			$query_args['meta_query'] = array(
				array(
					'key'     => '_nbm_city',
					'value'   => sanitize_text_field( $atts['city'] ),
					'compare' => 'LIKE',
				),
			);
		}

		$query       = new WP_Query( $query_args );
		$instance    = self::get_next_instance_id();
		$preset      = sanitize_key( $atts['preset'] );
		$btn_text    = sanitize_text_field( $atts['btn_text'] );
		$columns     = min( 4, max( 1, intval( $atts['columns'] ) ) );

		// Custom inline style overrides
		$inline_styles = '';
		$css_vars      = array();

		if ( ! empty( $atts['card_bg'] ) ) {
			$bg = sanitize_hex_color( $atts['card_bg'] );
			if ( $bg ) {
				$css_vars[] = "--nbm-bg-card: {$bg};";
			}
		}

		if ( ! empty( $atts['accent_color'] ) ) {
			$accent = sanitize_hex_color( $atts['accent_color'] );
			if ( $accent ) {
				$css_vars[] = "--nbm-primary: {$accent}; --nbm-primary-hover: {$accent}; --nbm-accent: {$accent};";
			}
		}

		if ( ! empty( $atts['gap'] ) ) {
			$gap = absint( $atts['gap'] );
			if ( $gap > 0 ) {
				$css_vars[] = "gap: {$gap}px;";
			}
		}

		if ( ! empty( $css_vars ) ) {
			$inline_styles = '<style>#nbm-hotels-' . esc_attr( $instance ) . ' .nbm-grid { ' . implode( ' ', $css_vars ) . ' }</style>';
		}

		ob_start();
		?>
		<div class="nbm-wrapper nbm-hotels-grid-wrap nbm-wrap-preset-<?php echo esc_attr( $preset ); ?>" id="nbm-hotels-<?php echo esc_attr( $instance ); ?>">
			<?php echo $inline_styles; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php if ( $query->have_posts() ) : ?>
				<div class="nbm-grid nbm-grid-cols-<?php echo esc_attr( $columns ); ?>">
					<?php
					while ( $query->have_posts() ) :
						$query->the_post();
						include NBM_PLUGIN_DIR . 'templates/hotel-card.php';
					endwhile;
					wp_reset_postdata();
					?>
				</div>

				<?php if ( 'yes' === $atts['pagination'] && $query->max_num_pages > 1 ) : ?>
					<div class="nbm-pagination">
						<?php
						echo paginate_links( array(
							'total'   => $query->max_num_pages,
							'current' => $paged,
							'type'    => 'list',
						) );
						?>
					</div>
				<?php endif; ?>
			<?php else : ?>
				<div class="nbm-empty-state">
					<p><?php esc_html_e( 'No hotels found matching your criteria.', 'nexora-booking-manager' ); ?></p>
				</div>
			<?php endif; ?>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Render [nbm_bars] shortcode.
	 *
	 * Supports loop card presets: 'modern', 'luxury', 'glassmorphism', 'minimal'.
	 * Supports custom columns, colors, button text, and filters.
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string HTML output.
	 */
	public static function render_bars( $atts ) {
		$atts = shortcode_atts(
			array(
				'preset'         => NBM_Settings::get( 'bar_default_card_preset', 'modern' ),
				'columns'        => 3,
				'per_page'       => 6,
				'city'           => '',
				'price_range'    => '',
				'orderby'        => 'date',
				'order'          => 'DESC',
				'pagination'     => 'yes',
				'card_bg'        => '',
				'accent_color'   => '',
				'btn_text'       => '',
				'gap'            => '',
			),
			$atts,
			'nbm_bars'
		);

		$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : ( ( get_query_var( 'page' ) ) ? get_query_var( 'page' ) : 1 );

		$query_args = array(
			'post_type'      => 'nbm_bar',
			'post_status'    => 'publish',
			'posts_per_page' => intval( $atts['per_page'] ),
			'paged'          => $paged,
			'orderby'        => sanitize_key( $atts['orderby'] ),
			'order'          => sanitize_key( $atts['order'] ),
		);

		$meta_queries = array();

		if ( ! empty( $atts['city'] ) ) {
			$meta_queries[] = array(
				'key'     => '_nbm_city',
				'value'   => sanitize_text_field( $atts['city'] ),
				'compare' => 'LIKE',
			);
		}

		if ( ! empty( $atts['price_range'] ) ) {
			$meta_queries[] = array(
				'key'     => '_nbm_price_range',
				'value'   => sanitize_text_field( $atts['price_range'] ),
				'compare' => '=',
			);
		}

		if ( ! empty( $meta_queries ) ) {
			if ( count( $meta_queries ) > 1 ) {
				$meta_queries['relation'] = 'AND';
			}
			$query_args['meta_query'] = $meta_queries;
		}

		$query       = new WP_Query( $query_args );
		$instance    = self::get_next_instance_id();
		$preset      = sanitize_key( $atts['preset'] );
		$btn_text    = sanitize_text_field( $atts['btn_text'] );
		$columns     = min( 4, max( 1, intval( $atts['columns'] ) ) );

		// Custom inline style overrides if admin supplied card_bg, accent_color or gap
		$inline_styles = '';
		$css_vars      = array();

		if ( ! empty( $atts['card_bg'] ) ) {
			$bg = sanitize_hex_color( $atts['card_bg'] );
			if ( $bg ) {
				$css_vars[] = "--nbm-bg-card: {$bg};";
			}
		}

		if ( ! empty( $atts['accent_color'] ) ) {
			$accent = sanitize_hex_color( $atts['accent_color'] );
			if ( $accent ) {
				$css_vars[] = "--nbm-primary: {$accent}; --nbm-primary-hover: {$accent}; --nbm-accent: {$accent};";
			}
		}

		if ( ! empty( $atts['gap'] ) ) {
			$gap = absint( $atts['gap'] );
			if ( $gap > 0 ) {
				$css_vars[] = "gap: {$gap}px;";
			}
		}

		if ( ! empty( $css_vars ) ) {
			$inline_styles = '<style>#nbm-bars-' . esc_attr( $instance ) . ' .nbm-grid { ' . implode( ' ', $css_vars ) . ' }</style>';
		}

		ob_start();
		?>
		<div class="nbm-wrapper nbm-bars-grid-wrap nbm-wrap-preset-<?php echo esc_attr( $preset ); ?>" id="nbm-bars-<?php echo esc_attr( $instance ); ?>">
			<?php echo $inline_styles; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php if ( $query->have_posts() ) : ?>
				<div class="nbm-grid nbm-grid-cols-<?php echo esc_attr( $columns ); ?>">
					<?php
					while ( $query->have_posts() ) :
						$query->the_post();
						include NBM_PLUGIN_DIR . 'templates/bar-card.php';
					endwhile;
					wp_reset_postdata();
					?>
				</div>

				<?php if ( 'yes' === $atts['pagination'] && $query->max_num_pages > 1 ) : ?>
					<div class="nbm-pagination">
						<?php
						echo paginate_links( array(
							'total'   => $query->max_num_pages,
							'current' => $paged,
							'type'    => 'list',
						) );
						?>
					</div>
				<?php endif; ?>
			<?php else : ?>
				<div class="nbm-empty-state">
					<p><?php esc_html_e( 'No bars found matching your criteria.', 'nexora-booking-manager' ); ?></p>
				</div>
			<?php endif; ?>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Render [nbm_booking_form] shortcode.
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string HTML output.
	 */
	public static function render_booking_form( $atts ) {
		$atts = shortcode_atts(
			array(
				'type' => 'hotel', // 'hotel' or 'bar'
				'id'   => 0,
			),
			$atts,
			'nbm_booking_form'
		);

		$listing_id   = absint( $atts['id'] );
		$listing_type = sanitize_key( $atts['type'] );

		// Auto-detect if on single listing page
		if ( 0 === $listing_id && is_singular( array( 'nbm_hotel', 'nbm_bar' ) ) ) {
			$listing_id   = get_the_ID();
			$listing_type = ( 'nbm_bar' === get_post_type() ) ? 'bar' : 'hotel';
		}

		$instance_id = self::get_next_instance_id();

		ob_start();
		?>
		<div class="nbm-wrapper nbm-booking-form-wrap" id="nbm-form-instance-<?php echo esc_attr( $instance_id ); ?>">
			<?php
			if ( 'bar' === $listing_type ) {
				include NBM_PLUGIN_DIR . 'templates/booking-form-bar.php';
			} else {
				include NBM_PLUGIN_DIR . 'templates/booking-form-hotel.php';
			}
			?>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Render [nbm_booking_search] shortcode.
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string HTML output.
	 */
	public static function render_booking_search( $atts ) {
		$atts = shortcode_atts(
			array(
				'type'    => 'all', // 'all', 'hotel', 'bar'
				'columns' => 3,
			),
			$atts,
			'nbm_booking_search'
		);

		$instance_id = self::get_next_instance_id();

		ob_start();
		include NBM_PLUGIN_DIR . 'templates/booking-search.php';
		return ob_get_clean();
	}

	/**
	 * Render [nbm_my_bookings] shortcode.
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string HTML output.
	 */
	public static function render_my_bookings( $atts ) {
		$instance_id = self::get_next_instance_id();

		ob_start();
		include NBM_PLUGIN_DIR . 'templates/my-bookings.php';
		return ob_get_clean();
	}
}
