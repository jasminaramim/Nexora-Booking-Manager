<?php
/**
 * Template: Booking Search & Filter shortcode.
 *
 * @package Nexora_Booking_Manager
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$instance_id = isset( $instance_id ) ? $instance_id : uniqid();
$search_type = isset( $atts['type'] ) ? sanitize_key( $atts['type'] ) : 'all';

// Process GET filters if form submitted
$keyword = sanitize_text_field( $_GET['nbm_q'] ?? '' );
$city    = sanitize_text_field( $_GET['nbm_city'] ?? '' );
$type    = sanitize_key( $_GET['nbm_type'] ?? $search_type );
if ( ! in_array( $type, array( 'all', 'hotel', 'bar' ), true ) ) {
	$type = 'all';
}

$post_types = ( 'all' === $type ) ? array( 'nbm_hotel', 'nbm_bar' ) : ( ( 'bar' === $type ) ? array( 'nbm_bar' ) : array( 'nbm_hotel' ) );

$query_args = array(
	'post_type'      => $post_types,
	'post_status'    => 'publish',
	'posts_per_page' => 12,
);

if ( ! empty( $keyword ) ) {
	$query_args['s'] = $keyword;
}

if ( ! empty( $city ) ) {
	$query_args['meta_query'] = array(
		array(
			'key'     => '_nbm_city',
			'value'   => $city,
			'compare' => 'LIKE',
		),
	);
}

$search_query = new WP_Query( $query_args );
?>
<div class="nbm-search-wrapper" id="nbm-search-<?php echo esc_attr( $instance_id ); ?>">
	<div class="nbm-search-bar-box">
		<form method="get" class="nbm-search-form" action="<?php echo esc_url( get_permalink() ); ?>">
			<div class="nbm-search-fields">
				<div class="nbm-s-field">
					<label for="nbm_q_<?php echo esc_attr( $instance_id ); ?>"><?php esc_html_e( 'Keyword / Name', 'nexora-booking-manager' ); ?></label>
					<input type="text" id="nbm_q_<?php echo esc_attr( $instance_id ); ?>" name="nbm_q" value="<?php echo esc_attr( $keyword ); ?>" placeholder="<?php esc_attr_e( 'Search property name...', 'nexora-booking-manager' ); ?>" />
				</div>

				<div class="nbm-s-field">
					<label for="nbm_city_<?php echo esc_attr( $instance_id ); ?>"><?php esc_html_e( 'Destination / City', 'nexora-booking-manager' ); ?></label>
					<input type="text" id="nbm_city_<?php echo esc_attr( $instance_id ); ?>" name="nbm_city" value="<?php echo esc_attr( $city ); ?>" placeholder="<?php esc_attr_e( 'e.g. Miami, New York', 'nexora-booking-manager' ); ?>" />
				</div>

				<div class="nbm-s-field">
					<label for="nbm_type_<?php echo esc_attr( $instance_id ); ?>"><?php esc_html_e( 'Type', 'nexora-booking-manager' ); ?></label>
					<select name="nbm_type" id="nbm_type_<?php echo esc_attr( $instance_id ); ?>">
						<option value="all" <?php selected( $type, 'all' ); ?>><?php esc_html_e( 'All Accommodations & Bars', 'nexora-booking-manager' ); ?></option>
						<option value="hotel" <?php selected( $type, 'hotel' ); ?>><?php esc_html_e( 'Hotels Only', 'nexora-booking-manager' ); ?></option>
						<option value="bar" <?php selected( $type, 'bar' ); ?>><?php esc_html_e( 'Bars Only', 'nexora-booking-manager' ); ?></option>
					</select>
				</div>

				<div class="nbm-s-field nbm-s-action">
					<button type="submit" class="nbm-btn nbm-btn-primary nbm-btn-search">
						🔍 <?php esc_html_e( 'Find Listings', 'nexora-booking-manager' ); ?>
					</button>
				</div>
			</div>
		</form>
	</div>

	<!-- Results List -->
	<div class="nbm-search-results-section" style="margin-top: 30px;">
		<?php if ( $search_query->have_posts() ) : ?>
			<div class="nbm-grid nbm-grid-cols-3">
				<?php
				while ( $search_query->have_posts() ) :
					$search_query->the_post();
					if ( 'nbm_bar' === get_post_type() ) {
						include NBM_PLUGIN_DIR . 'templates/bar-card.php';
					} else {
						include NBM_PLUGIN_DIR . 'templates/hotel-card.php';
					}
				endwhile;
				wp_reset_postdata();
				?>
			</div>
		<?php else : ?>
			<div class="nbm-empty-state">
				<p><?php esc_html_e( 'No listings matched your search criteria. Try modifying your filters.', 'nexora-booking-manager' ); ?></p>
			</div>
		<?php endif; ?>
	</div>
</div>
