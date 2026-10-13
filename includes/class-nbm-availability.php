<?php
/**
 * Availability checking and conflict prevention engine.
 *
 * @package Nexora_Booking_Manager
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class NBM_Availability {

	/**
	 * Verify hotel stay availability and booking rules.
	 *
	 * @param int    $listing_id Hotel post ID.
	 * @param string $check_in Check-in date (Y-m-d).
	 * @param string $check_out Check-out date (Y-m-d).
	 * @param int    $guest_count Number of guests.
	 * @param int    $exclude_booking_id Optional booking ID to exclude (e.g. self when updating).
	 * @return array|WP_Error Array of availability stats or WP_Error on conflict.
	 */
	public static function check_hotel_availability( $listing_id, $check_in, $check_out, $guest_count, $exclude_booking_id = 0 ) {
		global $wpdb;

		// 1. Verify listing exists and is published
		$post = get_post( $listing_id );
		if ( ! $post || 'nbm_hotel' !== $post->post_type || 'publish' !== $post->post_status ) {
			return new WP_Error( 'invalid_listing', __( 'The requested hotel listing does not exist or is currently unavailable.', 'nexora-booking-manager' ) );
		}

		// 2. Validate dates format and logical sequence
		$check_in_date  = NBM_Security::sanitize_date( $check_in );
		$check_out_date = NBM_Security::sanitize_date( $check_out );

		if ( ! $check_in_date || ! $check_out_date ) {
			return new WP_Error( 'invalid_dates', __( 'Please provide valid check-in and check-out dates (YYYY-MM-DD).', 'nexora-booking-manager' ) );
		}

		$today = current_time( 'Y-m-d' );
		if ( $check_in_date < $today ) {
			return new WP_Error( 'past_date', __( 'Check-in date cannot be in the past.', 'nexora-booking-manager' ) );
		}

		$dt_in  = new DateTime( $check_in_date );
		$dt_out = new DateTime( $check_out_date );

		if ( $dt_in >= $dt_out ) {
			return new WP_Error( 'invalid_range', __( 'Check-out date must be after the check-in date.', 'nexora-booking-manager' ) );
		}

		$nights = (int) $dt_in->diff( $dt_out )->days;

		// 3. Check stay constraints (min / max stay)
		$min_stay = (int) get_post_meta( $listing_id, '_nbm_min_stay', true );
		$max_stay = (int) get_post_meta( $listing_id, '_nbm_max_stay', true );
		if ( $min_stay < 1 ) { $min_stay = (int) NBM_Settings::get( 'hotel_default_min_stay', 1 ); }
		if ( $max_stay < $min_stay ) { $max_stay = (int) NBM_Settings::get( 'hotel_default_max_stay', 30 ); }

		if ( $nights < $min_stay ) {
			/* translators: %d: minimum stay in nights */
			return new WP_Error( 'min_stay_error', sprintf( __( 'The minimum stay for this hotel is %d night(s).', 'nexora-booking-manager' ), $min_stay ) );
		}

		if ( $nights > $max_stay ) {
			/* translators: %d: maximum stay in nights */
			return new WP_Error( 'max_stay_error', sprintf( __( 'The maximum stay for this hotel is %d night(s).', 'nexora-booking-manager' ), $max_stay ) );
		}

		// 4. Check guest limit
		$max_guests = (int) get_post_meta( $listing_id, '_nbm_max_guests', true );
		if ( $max_guests < 1 ) { $max_guests = (int) NBM_Settings::get( 'hotel_default_max_guests', 4 ); }

		if ( (int) $guest_count > $max_guests ) {
			/* translators: %d: maximum guest limit */
			return new WP_Error( 'max_guests_exceeded', sprintf( __( 'This room accommodates a maximum of %d guest(s).', 'nexora-booking-manager' ), $max_guests ) );
		}

		// 5. Check Inventory / Overlapping Confirmed Bookings
		$total_rooms = (int) get_post_meta( $listing_id, '_nbm_total_rooms', true );
		if ( $total_rooms < 1 ) {
			$total_rooms = 1;
		}

		$table = NBM_Database::get_bookings_table();

		// Two hotel bookings overlap if: Existing check_in < Requested check_out AND Existing check_out > Requested check_in
		$exclude_sql = '';
		if ( $exclude_booking_id > 0 ) {
			$exclude_sql = $wpdb->prepare( ' AND id != %d ', $exclude_booking_id );
		}

		$sql = $wpdb->prepare(
			"SELECT COUNT(*) FROM {$table}
			WHERE listing_id = %d
			AND listing_type = 'hotel'
			AND status = 'confirmed'
			AND check_in < %s
			AND check_out > %s
			{$exclude_sql}",
			$listing_id,
			$check_out_date,
			$check_in_date
		);

		$overlapping_count = (int) $wpdb->get_var( $sql );

		if ( $overlapping_count >= $total_rooms ) {
			return new WP_Error(
				'hotel_fully_booked',
				__( 'Sorry, all available rooms for this hotel are already booked for the selected date range. Please select different dates.', 'nexora-booking-manager' )
			);
		}

		return array(
			'available'       => true,
			'nights'          => $nights,
			'total_rooms'     => $total_rooms,
			'booked_rooms'    => $overlapping_count,
			'remaining_rooms' => max( 0, $total_rooms - $overlapping_count ),
		);
	}

	/**
	 * Verify bar table reservation availability and opening rules.
	 *
	 * @param int    $listing_id Bar post ID.
	 * @param string $booking_date Reservation date (Y-m-d).
	 * @param string $booking_time Reservation time (H:i).
	 * @param int    $guest_count Number of guests.
	 * @param int    $exclude_booking_id Optional booking ID to exclude.
	 * @return array|WP_Error Array of availability stats or WP_Error on conflict.
	 */
	public static function check_bar_availability( $listing_id, $booking_date, $booking_time, $guest_count, $exclude_booking_id = 0 ) {
		global $wpdb;

		// 1. Verify listing exists and is published
		$post = get_post( $listing_id );
		if ( ! $post || 'nbm_bar' !== $post->post_type || 'publish' !== $post->post_status ) {
			return new WP_Error( 'invalid_listing', __( 'The requested bar listing does not exist or is currently unavailable.', 'nexora-booking-manager' ) );
		}

		// 2. Validate date and time
		$date_clean = NBM_Security::sanitize_date( $booking_date );
		$time_clean = NBM_Security::sanitize_time( $booking_time );

		if ( ! $date_clean || ! $time_clean ) {
			return new WP_Error( 'invalid_datetime', __( 'Please provide a valid reservation date (YYYY-MM-DD) and time (HH:MM).', 'nexora-booking-manager' ) );
		}

		$today = current_time( 'Y-m-d' );
		if ( $date_clean < $today ) {
			return new WP_Error( 'past_date', __( 'Reservation date cannot be in the past.', 'nexora-booking-manager' ) );
		}

		// Check advance notice
		$min_notice = (int) get_post_meta( $listing_id, '_nbm_min_notice_hours', true );
		if ( $min_notice < 1 ) {
			$min_notice = (int) NBM_Settings::get( 'bar_default_notice_hours', 2 );
		}

		$requested_ts = strtotime( "{$date_clean} {$time_clean}" );
		$current_ts   = current_time( 'timestamp' );

		if ( $requested_ts < ( $current_ts + ( $min_notice * 3600 ) ) ) {
			/* translators: %d: notice hours */
			return new WP_Error( 'advance_notice_required', sprintf( __( 'Reservations must be made at least %d hour(s) in advance.', 'nexora-booking-manager' ), $min_notice ) );
		}

		// 3. Check Opening Hours for that day of week
		$day_of_week   = strtolower( gmdate( 'l', $requested_ts ) );
		$opening_hours = get_post_meta( $listing_id, '_nbm_opening_hours', true );

		if ( is_array( $opening_hours ) && isset( $opening_hours[ $day_of_week ] ) ) {
			$day_config = $opening_hours[ $day_of_week ];

			if ( ! empty( $day_config['closed'] ) ) {
				/* translators: %s: day of week */
				return new WP_Error( 'bar_closed', sprintf( __( 'The bar is closed on %s.', 'nexora-booking-manager' ), ucfirst( $day_of_week ) ) );
			}

			$open_time  = $day_config['open'] ?? '16:00';
			$close_time = $day_config['close'] ?? '23:30';

			// Normalize to HH:MM:00 for comparison
			$req_time_fmt   = substr( $time_clean, 0, 5 );
			$open_time_fmt  = substr( $open_time, 0, 5 );
			$close_time_fmt = substr( $close_time, 0, 5 );

			// Handle late-night closing (e.g. open 16:00 close 02:00 next day)
			if ( $close_time_fmt < $open_time_fmt ) {
				// Midnight rollover: valid if >= open OR <= close
				if ( $req_time_fmt < $open_time_fmt && $req_time_fmt > $close_time_fmt ) {
					/* translators: 1: opening time, 2: closing time */
					return new WP_Error( 'outside_hours', sprintf( __( 'Selected time is outside operating hours (%1$s - %2$s).', 'nexora-booking-manager' ), $open_time_fmt, $close_time_fmt ) );
				}
			} else {
				if ( $req_time_fmt < $open_time_fmt || $req_time_fmt > $close_time_fmt ) {
					/* translators: 1: opening time, 2: closing time */
					return new WP_Error( 'outside_hours', sprintf( __( 'Selected time is outside operating hours (%1$s - %2$s).', 'nexora-booking-manager' ), $open_time_fmt, $close_time_fmt ) );
				}
			}
		}

		// 4. Check Party Size Limit
		$max_party = (int) get_post_meta( $listing_id, '_nbm_max_party_size', true );
		if ( $max_party < 1 ) {
			$max_party = (int) NBM_Settings::get( 'bar_default_max_guests', 8 );
		}

		if ( (int) $guest_count > $max_party ) {
			/* translators: %d: max party size */
			return new WP_Error( 'party_size_exceeded', sprintf( __( 'Maximum party size for an online reservation is %d guests. For larger parties, please contact the venue directly.', 'nexora-booking-manager' ), $max_party ) );
		}

		// 5. Check Table Capacity and Slot Duration Conflicts
		$table_capacity = (int) get_post_meta( $listing_id, '_nbm_table_capacity', true );
		if ( $table_capacity < 1 ) { $table_capacity = 25; }

		$slot_duration = (int) get_post_meta( $listing_id, '_nbm_slot_duration', true );
		if ( $slot_duration < 15 ) { $slot_duration = (int) NBM_Settings::get( 'bar_default_duration', 90 ); }

		$table = NBM_Database::get_bookings_table();

		$exclude_sql = '';
		if ( $exclude_booking_id > 0 ) {
			$exclude_sql = $wpdb->prepare( ' AND id != %d ', $exclude_booking_id );
		}

		// Fetch all confirmed bookings on the same date
		$query = $wpdb->prepare(
			"SELECT booking_time, guest_count FROM {$table}
			WHERE listing_id = %d
			AND listing_type = 'bar'
			AND status = 'confirmed'
			AND booking_date = %s
			{$exclude_sql}",
			$listing_id,
			$date_clean
		);

		$existing_bookings = $wpdb->get_results( $query );

		$req_start_ts = strtotime( "{$date_clean} {$time_clean}" );
		$req_end_ts   = $req_start_ts + ( $slot_duration * 60 );

		$overlapping_guests = 0;
		if ( ! empty( $existing_bookings ) ) {
			foreach ( $existing_bookings as $b ) {
				$b_start_ts = strtotime( "{$date_clean} {$b->booking_time}" );
				$b_end_ts   = $b_start_ts + ( $slot_duration * 60 );

				// Check overlap: start1 < end2 AND end1 > start2
				if ( $req_start_ts < $b_end_ts && $req_end_ts > $b_start_ts ) {
					$overlapping_guests += (int) $b->guest_count;
				}
			}
		}

		if ( ( $overlapping_guests + (int) $guest_count ) > $table_capacity ) {
			return new WP_Error(
				'bar_capacity_exceeded',
				__( 'Sorry, the venue cannot accommodate this reservation size at the selected time. Please select another time slot.', 'nexora-booking-manager' )
			);
		}

		return array(
			'available'          => true,
			'capacity'           => $table_capacity,
			'booked_guests'      => $overlapping_guests,
			'remaining_capacity' => max( 0, $table_capacity - $overlapping_guests ),
			'slot_duration'      => $slot_duration,
		);
	}

	/**
	 * Pre-confirmation recheck: Re-evaluates availability right before an administrator confirms a booking.
	 * Prevents overbooking race conditions.
	 *
	 * @param object|array $booking Booking object or array.
	 * @return true|WP_Error
	 */
	public static function recheck_before_confirm( $booking ) {
		$b = (object) $booking;

		if ( 'hotel' === $b->listing_type ) {
			$res = self::check_hotel_availability(
				$b->listing_id,
				$b->check_in,
				$b->check_out,
				$b->guest_count,
				$b->id
			);
		} else {
			$res = self::check_bar_availability(
				$b->listing_id,
				$b->booking_date,
				$b->booking_time,
				$b->guest_count,
				$b->id
			);
		}

		if ( is_wp_error( $res ) ) {
			return $res;
		}

		return true;
	}
}
