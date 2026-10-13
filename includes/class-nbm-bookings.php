<?php
/**
 * Bookings data layer, CRUD operations and status lifecycle management.
 *
 * @package Nexora_Booking_Manager
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class NBM_Bookings {

	/**
	 * Allowed booking statuses.
	 */
	const STATUSES = array( 'pending', 'confirmed', 'rejected', 'cancelled', 'completed' );

	/**
	 * Create a new booking request.
	 *
	 * @param array $args Submitted booking data.
	 * @return object|WP_Error Created booking object or WP_Error.
	 */
	public static function create_booking( $args ) {
		global $wpdb;

		$table = NBM_Database::get_bookings_table();

		// Basic validation
		$listing_id   = absint( $args['listing_id'] ?? 0 );
		$listing_type = sanitize_key( $args['listing_type'] ?? 'hotel' );
		$guest_name   = sanitize_text_field( $args['guest_name'] ?? '' );
		$guest_email  = sanitize_email( $args['guest_email'] ?? '' );
		$guest_phone  = sanitize_text_field( $args['guest_phone'] ?? '' );
		$guest_count  = max( 1, absint( $args['guest_count'] ?? 1 ) );
		$guest_message= sanitize_textarea_field( $args['guest_message'] ?? '' );

		if ( empty( $listing_id ) || ! in_array( $listing_type, array( 'hotel', 'bar' ), true ) ) {
			return new WP_Error( 'invalid_listing', __( 'Invalid listing specified.', 'nexora-booking-manager' ) );
		}

		if ( empty( $guest_name ) ) {
			return new WP_Error( 'empty_name', __( 'Please provide your full name.', 'nexora-booking-manager' ) );
		}

		if ( empty( $guest_email ) || ! is_email( $guest_email ) ) {
			return new WP_Error( 'invalid_email', __( 'Please provide a valid email address.', 'nexora-booking-manager' ) );
		}

		if ( '1' === NBM_Settings::get( 'require_phone', '0' ) && empty( $guest_phone ) ) {
			return new WP_Error( 'phone_required', __( 'Please provide a contact phone number.', 'nexora-booking-manager' ) );
		}

		$check_in     = null;
		$check_out    = null;
		$booking_date = null;
		$booking_time = null;

		// Listing-specific verification & availability check
		if ( 'hotel' === $listing_type ) {
			$check_in  = NBM_Security::sanitize_date( $args['check_in'] ?? '' );
			$check_out = NBM_Security::sanitize_date( $args['check_out'] ?? '' );

			$avail_check = NBM_Availability::check_hotel_availability( $listing_id, $check_in, $check_out, $guest_count );
			if ( is_wp_error( $avail_check ) ) {
				return $avail_check;
			}
		} else {
			$booking_date = NBM_Security::sanitize_date( $args['booking_date'] ?? '' );
			$booking_time = NBM_Security::sanitize_time( $args['booking_time'] ?? '' );

			$avail_check = NBM_Availability::check_bar_availability( $listing_id, $booking_date, $booking_time, $guest_count );
			if ( is_wp_error( $avail_check ) ) {
				return $avail_check;
			}
		}

		// Generate unique reference code
		$booking_code = NBM_Security::generate_booking_code( $listing_type );
		$now          = current_time( 'mysql' );

		// All new requests are strictly PENDING
		$status = 'pending';

		$inserted = $wpdb->insert(
			$table,
			array(
				'booking_code'  => $booking_code,
				'listing_id'    => $listing_id,
				'listing_type'  => $listing_type,
				'guest_name'    => $guest_name,
				'guest_email'   => $guest_email,
				'guest_phone'   => $guest_phone,
				'check_in'      => $check_in,
				'check_out'     => $check_out,
				'booking_date'  => $booking_date,
				'booking_time'  => $booking_time,
				'guest_count'   => $guest_count,
				'status'        => $status,
				'guest_message' => $guest_message,
				'admin_note'    => null,
				'created_at'    => $now,
				'updated_at'    => $now,
			),
			array(
				'%s', '%d', '%s', '%s', '%s', '%s',
				'%s', '%s', '%s', '%s', '%d', '%s',
				'%s', '%s', '%s', '%s'
			)
		);

		if ( ! $inserted ) {
			return new WP_Error( 'db_insert_error', __( 'Failed to save booking request in database. Please try again.', 'nexora-booking-manager' ) );
		}

		$booking_id = $wpdb->insert_id;
		$booking    = self::get_booking( $booking_id );

		// Log system audit note
		self::add_booking_note(
			$booking_id,
			__( 'Booking request created via website form with status: Pending.', 'nexora-booking-manager' ),
			'system'
		);

		// Send email notifications
		$admin_sent = NBM_Emails::send_admin_new_booking( $booking );
		$guest_sent = NBM_Emails::send_guest_pending_notice( $booking );

		$mail_log = sprintf(
			__( 'Notifications: Admin alert [%1$s], Guest pending notice to %2$s [%3$s].', 'nexora-booking-manager' ),
			$admin_sent ? __( 'Sent', 'nexora-booking-manager' ) : __( 'Failed / Check Server Mail', 'nexora-booking-manager' ),
			$booking->guest_email,
			$guest_sent ? __( 'Sent', 'nexora-booking-manager' ) : __( 'Failed / Check Server Mail', 'nexora-booking-manager' )
		);
		self::add_booking_note( $booking_id, $mail_log, 'system' );

		return $booking;
	}

	/**
	 * Get single booking by ID.
	 *
	 * @param int $id Booking ID.
	 * @return object|null
	 */
	public static function get_booking( $id ) {
		global $wpdb;
		$table = NBM_Database::get_bookings_table();
		return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE id = %d", absint( $id ) ) );
	}

	/**
	 * Get booking by unique booking reference code.
	 *
	 * @param string $code Booking code.
	 * @return object|null
	 */
	public static function get_booking_by_code( $code ) {
		global $wpdb;
		$table = NBM_Database::get_bookings_table();
		return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE booking_code = %s", sanitize_text_field( $code ) ) );
	}

	/**
	 * Update booking status and record audit log.
	 *
	 * @param int    $id Booking ID.
	 * @param string $new_status Target status.
	 * @param string $admin_note Optional note from administrator.
	 * @param bool   $notify_guest Whether to send status email.
	 * @return object|WP_Error
	 */
	public static function update_status( $id, $new_status, $admin_note = '', $notify_guest = true ) {
		global $wpdb;

		$booking = self::get_booking( $id );
		if ( ! $booking ) {
			return new WP_Error( 'not_found', __( 'Booking not found.', 'nexora-booking-manager' ) );
		}

		$new_status = sanitize_key( $new_status );
		if ( ! in_array( $new_status, self::STATUSES, true ) ) {
			return new WP_Error( 'invalid_status', __( 'Invalid booking status.', 'nexora-booking-manager' ) );
		}

		$old_status = $booking->status;

		// Availability recheck before Confirming!
		if ( 'confirmed' === $new_status ) {
			$recheck = NBM_Availability::recheck_before_confirm( $booking );
			if ( is_wp_error( $recheck ) ) {
				return $recheck;
			}
		}

		$table = NBM_Database::get_bookings_table();
		$now   = current_time( 'mysql' );

		$update_data = array(
			'status'     => $new_status,
			'updated_at' => $now,
		);
		$update_formats = array( '%s', '%s' );

		if ( ! empty( $admin_note ) ) {
			$update_data['admin_note'] = sanitize_textarea_field( $admin_note );
			$update_formats[]          = '%s';
		}

		$updated = $wpdb->update(
			$table,
			$update_data,
			array( 'id' => $booking->id ),
			$update_formats,
			array( '%d' )
		);

		if ( false === $updated ) {
			return new WP_Error( 'db_update_failed', __( 'Could not update booking in database.', 'nexora-booking-manager' ) );
		}

		// Log audit history note
		$current_user_id = get_current_user_id();
		$user_info       = get_userdata( $current_user_id );
		$user_name       = $user_info ? $user_info->display_name : __( 'Administrator', 'nexora-booking-manager' );

		/* translators: 1: user name, 2: old status, 3: new status */
		$note_text = sprintf( __( 'Status changed by %1$s from %2$s to %3$s.', 'nexora-booking-manager' ), $user_name, ucfirst( $old_status ), ucfirst( $new_status ) );
		if ( ! empty( $admin_note ) ) {
			$note_text .= ' ' . sprintf( __( 'Note: %s', 'nexora-booking-manager' ), $admin_note );
		}

		self::add_booking_note( $booking->id, $note_text, 'status_change', $current_user_id );

		// Fetch refreshed booking
		$refreshed = self::get_booking( $booking->id );

		// Send email update to guest if status changed
		if ( $notify_guest && $old_status !== $new_status ) {
			$status_sent = NBM_Emails::send_guest_status_update( $refreshed, $old_status, $admin_note );
			$status_mail_log = sprintf(
				__( 'Status email notification (%1$s) to guest %2$s: [%3$s].', 'nexora-booking-manager' ),
				ucfirst( $new_status ),
				$refreshed->guest_email,
				$status_sent ? __( 'Sent', 'nexora-booking-manager' ) : __( 'Failed / Check Server Mail', 'nexora-booking-manager' )
			);
			self::add_booking_note( $booking->id, $status_mail_log, 'system' );
		}

		return $refreshed;
	}

	/**
	 * Add an internal note / audit entry.
	 *
	 * @param int    $booking_id Booking ID.
	 * @param string $note Content.
	 * @param string $note_type 'system', 'admin', 'status_change'.
	 * @param int    $author_id User ID.
	 * @return int|false
	 */
	public static function add_booking_note( $booking_id, $note, $note_type = 'admin', $author_id = 0 ) {
		global $wpdb;

		$table = NBM_Database::get_notes_table();
		$res   = $wpdb->insert(
			$table,
			array(
				'booking_id' => absint( $booking_id ),
				'author_id'  => absint( $author_id ),
				'note_type'  => sanitize_key( $note_type ),
				'note'       => sanitize_textarea_field( $note ),
				'created_at' => current_time( 'mysql' ),
			),
			array( '%d', '%d', '%s', '%s', '%s' )
		);

		return $res ? $wpdb->insert_id : false;
	}

	/**
	 * Retrieve all notes for a booking.
	 *
	 * @param int $booking_id Booking ID.
	 * @return array
	 */
	public static function get_booking_notes( $booking_id ) {
		global $wpdb;
		$table = NBM_Database::get_notes_table();
		return $wpdb->get_results(
			$wpdb->prepare( "SELECT * FROM {$table} WHERE booking_id = %d ORDER BY created_at DESC", absint( $booking_id ) )
		);
	}

	/**
	 * Query bookings with filter, search, sorting and pagination.
	 *
	 * @param array $args Query parameters.
	 * @return array Array with 'items', 'total', 'pages', 'current_page'.
	 */
	public static function query_bookings( $args = array() ) {
		global $wpdb;

		$table = NBM_Database::get_bookings_table();

		$defaults = array(
			'search'       => '',
			'status'       => '',
			'listing_type' => '',
			'listing_id'   => 0,
			'per_page'     => 20,
			'paged'        => 1,
			'orderby'      => 'created_at',
			'order'        => 'DESC',
		);

		$params = wp_parse_args( $args, $defaults );

		$where   = array( '1=1' );
		$values  = array();

		// Filter status
		if ( ! empty( $params['status'] ) && in_array( $params['status'], self::STATUSES, true ) ) {
			$where[]  = 'status = %s';
			$values[] = $params['status'];
		}

		// Filter listing type
		if ( ! empty( $params['listing_type'] ) && in_array( $params['listing_type'], array( 'hotel', 'bar' ), true ) ) {
			$where[]  = 'listing_type = %s';
			$values[] = $params['listing_type'];
		}

		// Filter listing ID
		if ( ! empty( $params['listing_id'] ) ) {
			$where[]  = 'listing_id = %d';
			$values[] = absint( $params['listing_id'] );
		}

		// Search keyword across code, name, email, phone
		if ( ! empty( $params['search'] ) ) {
			$search_like = '%' . $wpdb->esc_like( sanitize_text_field( $params['search'] ) ) . '%';
			$where[]     = '(booking_code LIKE %s OR guest_name LIKE %s OR guest_email LIKE %s OR guest_phone LIKE %s)';
			$values[]    = $search_like;
			$values[]    = $search_like;
			$values[]    = $search_like;
			$values[]    = $search_like;
		}

		$where_clause = implode( ' AND ', $where );

		// Count total records
		if ( ! empty( $values ) ) {
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$count_sql = $wpdb->prepare( "SELECT COUNT(*) FROM {$table} WHERE {$where_clause}", $values );
		} else {
			$count_sql = "SELECT COUNT(*) FROM {$table} WHERE {$where_clause}";
		}
		$total_items = (int) $wpdb->get_var( $count_sql );

		// Pagination & Sorting
		$per_page = max( 1, absint( $params['per_page'] ) );
		$paged    = max( 1, absint( $params['paged'] ) );
		$offset   = ( $paged - 1 ) * $per_page;

		$allowed_orders = array( 'ASC', 'DESC' );
		$order          = in_array( strtoupper( $params['order'] ), $allowed_orders, true ) ? strtoupper( $params['order'] ) : 'DESC';

		$allowed_columns = array( 'id', 'created_at', 'check_in', 'booking_date', 'status', 'guest_name' );
		$orderby         = in_array( $params['orderby'], $allowed_columns, true ) ? $params['orderby'] : 'created_at';

		if ( ! empty( $values ) ) {
			// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$query_sql = $wpdb->prepare(
				"SELECT * FROM {$table} WHERE {$where_clause} ORDER BY {$orderby} {$order} LIMIT %d OFFSET %d",
				array_merge( $values, array( $per_page, $offset ) )
			);
		} else {
			$query_sql = $wpdb->prepare(
				"SELECT * FROM {$table} WHERE {$where_clause} ORDER BY {$orderby} {$order} LIMIT %d OFFSET %d",
				$per_page,
				$offset
			);
		}

		$items = $wpdb->get_results( $query_sql );

		return array(
			'items'        => $items ? $items : array(),
			'total'        => $total_items,
			'pages'        => ceil( $total_items / $per_page ),
			'current_page' => $paged,
		);
	}

	/**
	 * Delete single booking and associated notes.
	 *
	 * @param int $id Booking ID.
	 * @return bool
	 */
	public static function delete_booking( $id ) {
		global $wpdb;

		$table_bookings = NBM_Database::get_bookings_table();
		$table_notes    = NBM_Database::get_notes_table();

		$wpdb->delete( $table_notes, array( 'booking_id' => absint( $id ) ), array( '%d' ) );
		$deleted = $wpdb->delete( $table_bookings, array( 'id' => absint( $id ) ), array( '%d' ) );

		return (bool) $deleted;
	}

	/**
	 * Get aggregated statistics for the admin dashboard.
	 *
	 * @return array
	 */
	public static function get_statistics() {
		global $wpdb;

		$table = NBM_Database::get_bookings_table();

		$stats = array(
			'total_hotels'    => (int) wp_count_posts( 'nbm_hotel' )->publish,
			'total_bars'      => (int) wp_count_posts( 'nbm_bar' )->publish,
			'total_bookings'  => (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table}" ),
			'pending'         => (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table} WHERE status = 'pending'" ),
			'confirmed'       => (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table} WHERE status = 'confirmed'" ),
			'rejected'        => (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table} WHERE status = 'rejected'" ),
			'cancelled'       => (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table} WHERE status = 'cancelled'" ),
			'completed'       => (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table} WHERE status = 'completed'" ),
		);

		return $stats;
	}
}
