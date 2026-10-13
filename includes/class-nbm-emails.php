<?php
/**
 * Email notifications system for Nexora Booking Manager.
 *
 * @package Nexora_Booking_Manager
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class NBM_Emails {

	/**
	 * Temporary holder for captured wp_mail error.
	 *
	 * @var WP_Error|null
	 */
	private static $last_error = null;

	/**
	 * Initialize email hooks.
	 */
	public static function init() {
		add_action( 'wp_mail_failed', array( __CLASS__, 'on_wp_mail_failed' ) );
	}

	/**
	 * Capture wp_mail failure details for diagnostics.
	 *
	 * @param WP_Error $wp_error WordPress error object.
	 */
	public static function on_wp_mail_failed( $wp_error ) {
		if ( is_wp_error( $wp_error ) ) {
			self::$last_error = $wp_error;
			update_option( 'nbm_last_mail_error', array(
				'timestamp' => current_time( 'mysql' ),
				'message'   => $wp_error->get_error_message(),
				'code'      => $wp_error->get_error_code(),
				'data'      => $wp_error->get_error_data(),
			) );
		}
	}

	/**
	 * Send email notification to admin upon new booking request.
	 *
	 * @param object $booking Booking object.
	 * @return bool
	 */
	public static function send_admin_new_booking( $booking ) {
		$enabled = NBM_Settings::get( 'notify_admin_new_booking', '1' );
		if ( '0' === (string) $enabled || false === $enabled ) {
			return false;
		}

		$to = NBM_Settings::get( 'admin_email' );
		if ( empty( $to ) || ! is_email( $to ) ) {
			$to = get_option( 'admin_email' );
		}

		if ( ! is_email( $to ) ) {
			return false;
		}

		$site_title    = get_bloginfo( 'name' );
		$listing_title = get_the_title( $booking->listing_id );
		$type_label    = ( 'hotel' === $booking->listing_type ) ? __( 'Hotel', 'nexora-booking-manager' ) : __( 'Bar', 'nexora-booking-manager' );

		/* translators: 1: site name, 2: type, 3: booking code, 4: listing title */
		$subject = sprintf( __( '[%1$s] New %2$s Booking Request: %3$s (%4$s)', 'nexora-booking-manager' ), $site_title, $type_label, $booking->booking_code, $listing_title );

		$admin_url = admin_url( 'admin.php?page=nbm-bookings&action=view&id=' . $booking->id );

		$dates_info = ( 'hotel' === $booking->listing_type )
			/* translators: 1: check in date, 2: check out date */
			? sprintf( __( 'Check-in: %1$s | Check-out: %2$s', 'nexora-booking-manager' ), $booking->check_in, $booking->check_out )
			/* translators: 1: booking date, 2: booking time */
			: sprintf( __( 'Date: %1$s at %2$s', 'nexora-booking-manager' ), $booking->booking_date, $booking->booking_time );

		$body = '
		<!DOCTYPE html>
		<html>
		<head><meta charset="utf-8"></head>
		<body style="font-family: -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica, Arial, sans-serif; line-height: 1.6; color: #334155; margin:0; padding:20px; background-color:#f8fafc;">
			<div style="max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 8px; border: 1px solid #e2e8f0; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
				<div style="background-color: #1e40af; color: #ffffff; padding: 24px; text-align: center;">
					<h2 style="margin: 0; font-size: 20px;">' . esc_html__( 'New Booking Request Received', 'nexora-booking-manager' ) . '</h2>
					<p style="margin: 6px 0 0; opacity: 0.9; font-weight: bold; letter-spacing: 1px;">' . esc_html( $booking->booking_code ) . '</p>
				</div>
				<div style="padding: 24px;">
					<p>' . esc_html__( 'A new reservation request has been submitted and is waiting for your review.', 'nexora-booking-manager' ) . '</p>
					<table style="width: 100%; border-collapse: collapse; margin: 16px 0;">
						<tr><td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9; font-weight: bold; width: 140px;">' . esc_html__( 'Listing Venue:', 'nexora-booking-manager' ) . '</td><td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9;">' . esc_html( $listing_title ) . ' (' . esc_html( $type_label ) . ')</td></tr>
						<tr><td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9; font-weight: bold;">' . esc_html__( 'Requested Schedule:', 'nexora-booking-manager' ) . '</td><td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9;">' . esc_html( $dates_info ) . '</td></tr>
						<tr><td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9; font-weight: bold;">' . esc_html__( 'Party Size:', 'nexora-booking-manager' ) . '</td><td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9;">' . absint( $booking->guest_count ) . ' ' . esc_html__( 'Guest(s)', 'nexora-booking-manager' ) . '</td></tr>
						<tr><td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9; font-weight: bold;">' . esc_html__( 'Guest Name:', 'nexora-booking-manager' ) . '</td><td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9;"><strong>' . esc_html( $booking->guest_name ) . '</strong></td></tr>
						<tr><td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9; font-weight: bold;">' . esc_html__( 'Guest Email:', 'nexora-booking-manager' ) . '</td><td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9;"><a href="mailto:' . esc_attr( $booking->guest_email ) . '">' . esc_html( $booking->guest_email ) . '</a></td></tr>
						<tr><td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9; font-weight: bold;">' . esc_html__( 'Guest Phone:', 'nexora-booking-manager' ) . '</td><td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9;">' . esc_html( $booking->guest_phone ? $booking->guest_phone : '—' ) . '</td></tr>
						<tr><td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9; font-weight: bold;">' . esc_html__( 'Guest Message:', 'nexora-booking-manager' ) . '</td><td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9;">' . nl2br( esc_html( $booking->guest_message ? $booking->guest_message : '—' ) ) . '</td></tr>
					</table>
					<div style="text-align: center; margin-top: 24px;">
						<a href="' . esc_url( $admin_url ) . '" style="display: inline-block; background-color: #1e40af; color: #ffffff; text-decoration: none; padding: 12px 24px; border-radius: 6px; font-weight: 600;">' . esc_html__( 'Review & Confirm in Dashboard', 'nexora-booking-manager' ) . ' &rarr;</a>
					</div>
				</div>
				<div style="background: #f1f5f9; padding: 12px; text-align: center; font-size: 12px; color: #64748b;">
					' . esc_html__( 'Nexora Booking Manager — Powered by Jasmin Dew', 'nexora-booking-manager' ) . '
				</div>
			</div>
		</body>
		</html>';

		// Set Reply-To to the guest so admin can reply directly!
		return self::send( $to, $subject, $body, $booking->guest_email );
	}

	/**
	 * Send pending acknowledgement email to guest.
	 *
	 * @param object $booking Booking object.
	 * @return bool
	 */
	public static function send_guest_pending_notice( $booking ) {
		$enabled = NBM_Settings::get( 'notify_guest_pending', '1' );
		if ( '0' === (string) $enabled || false === $enabled ) {
			return false;
		}

		if ( empty( $booking->guest_email ) || ! is_email( $booking->guest_email ) ) {
			return false;
		}

		$to           = $booking->guest_email;
		$site_title   = get_bloginfo( 'name' );
		$listing_title = get_the_title( $booking->listing_id );
		$type_label   = ( 'hotel' === $booking->listing_type ) ? __( 'Hotel', 'nexora-booking-manager' ) : __( 'Bar', 'nexora-booking-manager' );

		/* translators: 1: site name, 2: booking code */
		$subject = sprintf( __( '[%1$s] Booking Request Received — %2$s (Pending Confirmation)', 'nexora-booking-manager' ), $site_title, $booking->booking_code );

		$dates_info = ( 'hotel' === $booking->listing_type )
			/* translators: 1: check in date, 2: check out date */
			? sprintf( __( 'Check-in: %1$s | Check-out: %2$s', 'nexora-booking-manager' ), $booking->check_in, $booking->check_out )
			/* translators: 1: booking date, 2: booking time */
			: sprintf( __( 'Date: %1$s at %2$s', 'nexora-booking-manager' ), $booking->booking_date, $booking->booking_time );

		$body = '
		<!DOCTYPE html>
		<html>
		<head><meta charset="utf-8"></head>
		<body style="font-family: -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica, Arial, sans-serif; line-height: 1.6; color: #334155; margin:0; padding:20px; background-color:#f8fafc;">
			<div style="max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 8px; border: 1px solid #e2e8f0; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
				<div style="background-color: #0d9488; color: #ffffff; padding: 24px; text-align: center;">
					<h2 style="margin: 0; font-size: 20px;">' . esc_html__( 'Booking Request Received', 'nexora-booking-manager' ) . '</h2>
					<p style="margin: 6px 0 0; opacity: 0.95; font-weight: bold; letter-spacing: 1px;">' . esc_html__( 'Reference Code:', 'nexora-booking-manager' ) . ' ' . esc_html( $booking->booking_code ) . '</p>
				</div>
				<div style="padding: 24px;">
					<p>' . sprintf( esc_html__( 'Hello %s,', 'nexora-booking-manager' ), esc_html( $booking->guest_name ) ) . '</p>
					<p>' . esc_html__( 'Thank you for your reservation request! We have successfully received your details.', 'nexora-booking-manager' ) . '</p>
					
					<div style="background: #fef3c7; border-left: 4px solid #f59e0b; padding: 12px 16px; margin: 16px 0; border-radius: 4px; color: #92400e;">
						<strong>' . esc_html__( 'Status: Pending Review', 'nexora-booking-manager' ) . '</strong><br>
						' . esc_html__( 'Please note that your booking is not yet confirmed. Our staff will review your reservation request and send you a confirmation update shortly.', 'nexora-booking-manager' ) . '
					</div>

					<table style="width: 100%; border-collapse: collapse; margin: 16px 0;">
						<tr><td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9; font-weight: bold; width: 140px;">' . esc_html__( 'Venue / Listing:', 'nexora-booking-manager' ) . '</td><td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9;">' . esc_html( $listing_title ) . ' (' . esc_html( $type_label ) . ')</td></tr>
						<tr><td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9; font-weight: bold;">' . esc_html__( 'Requested Schedule:', 'nexora-booking-manager' ) . '</td><td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9;">' . esc_html( $dates_info ) . '</td></tr>
						<tr><td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9; font-weight: bold;">' . esc_html__( 'Party Size:', 'nexora-booking-manager' ) . '</td><td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9;">' . absint( $booking->guest_count ) . ' ' . esc_html__( 'Guest(s)', 'nexora-booking-manager' ) . '</td></tr>
					</table>

					<p style="color: #64748b; font-size: 13.5px; margin-top: 24px;">' . sprintf( esc_html__( 'If you have any urgent inquiries, you can reply directly to this email or contact %s.', 'nexora-booking-manager' ), esc_html( $site_title ) ) . '</p>
				</div>
				<div style="background: #f1f5f9; padding: 12px; text-align: center; font-size: 12px; color: #64748b;">
					' . esc_html( $site_title ) . '
				</div>
			</div>
		</body>
		</html>';

		return self::send( $to, $subject, $body );
	}

	/**
	 * Send status update notification to guest (Confirmed / Rejected / Cancelled).
	 *
	 * @param object $booking Booking object.
	 * @param string $old_status Previous status.
	 * @param string $reason Optional custom message/reason from admin.
	 * @return bool
	 */
	public static function send_guest_status_update( $booking, $old_status, $reason = '' ) {
		$enabled = NBM_Settings::get( 'notify_guest_status_change', '1' );
		if ( '0' === (string) $enabled || false === $enabled ) {
			return false;
		}

		if ( empty( $booking->guest_email ) || ! is_email( $booking->guest_email ) ) {
			return false;
		}

		$to           = $booking->guest_email;
		$site_title   = get_bloginfo( 'name' );
		$listing_title = get_the_title( $booking->listing_id );
		$status       = $booking->status;

		switch ( $status ) {
			case 'confirmed':
				/* translators: 1: site name, 2: booking code, 3: listing title */
				$subject = sprintf( __( '[%1$s] Booking Confirmed! Ref: %2$s (%3$s)', 'nexora-booking-manager' ), $site_title, $booking->booking_code, $listing_title );
				$badge_color = '#10b981';
				$status_text = __( 'CONFIRMED', 'nexora-booking-manager' );
				$main_msg    = __( 'Great news! Your booking request has been confirmed. We look forward to welcoming you!', 'nexora-booking-manager' );
				break;
			case 'rejected':
				/* translators: 1: site name, 2: booking code */
				$subject = sprintf( __( '[%1$s] Booking Request Update: %2$s', 'nexora-booking-manager' ), $site_title, $booking->booking_code );
				$badge_color = '#ef4444';
				$status_text = __( 'REJECTED / UNABLE TO ACCOMMODATE', 'nexora-booking-manager' );
				$main_msg    = __( 'We regret to inform you that we are unable to accept your booking request for the selected dates/time.', 'nexora-booking-manager' );
				break;
			case 'cancelled':
				/* translators: 1: site name, 2: booking code */
				$subject = sprintf( __( '[%1$s] Booking Cancelled: %2$s', 'nexora-booking-manager' ), $site_title, $booking->booking_code );
				$badge_color = '#64748b';
				$status_text = __( 'CANCELLED', 'nexora-booking-manager' );
				$main_msg    = __( 'Your booking has been cancelled as requested.', 'nexora-booking-manager' );
				break;
			default:
				return false;
		}

		$dates_info = ( 'hotel' === $booking->listing_type )
			/* translators: 1: check in date, 2: check out date */
			? sprintf( __( 'Check-in: %1$s | Check-out: %2$s', 'nexora-booking-manager' ), $booking->check_in, $booking->check_out )
			/* translators: 1: booking date, 2: booking time */
			: sprintf( __( 'Date: %1$s at %2$s', 'nexora-booking-manager' ), $booking->booking_date, $booking->booking_time );

		$custom_reason_html = '';
		if ( ! empty( $reason ) ) {
			$custom_reason_html = '<div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:6px; padding:12px; margin:16px 0;"><strong>' . esc_html__( 'Note from management:', 'nexora-booking-manager' ) . '</strong><br>' . nl2br( esc_html( $reason ) ) . '</div>';
		}

		$body = '
		<!DOCTYPE html>
		<html>
		<head><meta charset="utf-8"></head>
		<body style="font-family: -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica, Arial, sans-serif; line-height: 1.6; color: #334155; margin:0; padding:20px; background-color:#f8fafc;">
			<div style="max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 8px; border: 1px solid #e2e8f0; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
				<div style="background-color: ' . esc_attr( $badge_color ) . '; color: #ffffff; padding: 24px; text-align: center;">
					<h2 style="margin: 0; font-size: 20px;">' . esc_html( $status_text ) . '</h2>
					<p style="margin: 6px 0 0; opacity: 0.95; font-weight: bold; letter-spacing: 1px;">' . esc_html__( 'Reference Code:', 'nexora-booking-manager' ) . ' ' . esc_html( $booking->booking_code ) . '</p>
				</div>
				<div style="padding: 24px;">
					<p>' . sprintf( esc_html__( 'Hello %s,', 'nexora-booking-manager' ), esc_html( $booking->guest_name ) ) . '</p>
					<p>' . esc_html( $main_msg ) . '</p>
					' . $custom_reason_html . '
					<table style="width: 100%; border-collapse: collapse; margin: 16px 0;">
						<tr><td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9; font-weight: bold; width: 140px;">' . esc_html__( 'Venue:', 'nexora-booking-manager' ) . '</td><td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9;">' . esc_html( $listing_title ) . '</td></tr>
						<tr><td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9; font-weight: bold;">' . esc_html__( 'Schedule:', 'nexora-booking-manager' ) . '</td><td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9;">' . esc_html( $dates_info ) . '</td></tr>
						<tr><td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9; font-weight: bold;">' . esc_html__( 'Party Size:', 'nexora-booking-manager' ) . '</td><td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9;">' . absint( $booking->guest_count ) . ' ' . esc_html__( 'Guest(s)', 'nexora-booking-manager' ) . '</td></tr>
					</table>
					<p style="color: #64748b; font-size: 14px; margin-top: 24px;">' . esc_html( $site_title ) . '</p>
				</div>
			</div>
		</body>
		</html>';

		return self::send( $to, $subject, $body );
	}

	/**
	 * Send test email to verify server email capability.
	 *
	 * @param string $test_email Destination email.
	 * @return array
	 */
	public static function send_test_email( $test_email ) {
		if ( empty( $test_email ) || ! is_email( $test_email ) ) {
			return array(
				'success' => false,
				'message' => __( 'Please provide a valid test email address.', 'nexora-booking-manager' ),
			);
		}

		$site_title = get_bloginfo( 'name' );
		/* translators: %s: site name */
		$subject    = sprintf( __( '[%s] Nexora Booking Manager — Email Test', 'nexora-booking-manager' ), $site_title );
		$body       = '
		<!DOCTYPE html>
		<html>
		<body style="font-family: sans-serif; color: #334155; padding: 20px;">
			<div style="max-width: 500px; margin: 0 auto; border: 1px solid #e2e8f0; border-radius: 8px; padding: 24px;">
				<h3 style="color: #10b981; margin-top: 0;">✓ Email Delivery Test Successful!</h3>
				<p>This test email confirms that your WordPress installation and server can successfully send notifications for <strong>Nexora Booking Manager</strong>.</p>
				<p>Both admin alerts and guest booking confirmations will be dispatched through this email channel.</p>
				<hr style="border: none; border-top: 1px solid #e2e8f0; margin: 16px 0;">
				<small style="color: #64748b;">Sent at: ' . esc_html( current_time( 'mysql' ) ) . ' | Developed by Jasmin Dew</small>
			</div>
		</body>
		</html>';

		$sent = self::send( $test_email, $subject, $body );

		if ( $sent ) {
			return array(
				'success' => true,
				'message' => sprintf( __( 'Test email sent successfully to %s! Please check your inbox or spam folder.', 'nexora-booking-manager' ), $test_email ),
			);
		}

		$error_info = get_option( 'nbm_last_mail_error', false );
		$err_msg    = ! empty( $error_info['message'] ) ? $error_info['message'] : __( 'wp_mail() returned false. Your hosting server might not have PHP mail() configured.', 'nexora-booking-manager' );

		return array(
			'success' => false,
			'message' => $err_msg,
		);
	}

	/**
	 * Send HTML email with headers safely using WP filters.
	 *
	 * @param string $to Recipient email.
	 * @param string $subject Email subject.
	 * @param string $body HTML message body.
	 * @param string $reply_to Optional reply-to address.
	 * @return bool True if wp_mail succeeded, false otherwise.
	 */
	private static function send( $to, $subject, $body, $reply_to = '' ) {
		if ( empty( $to ) || ! is_email( $to ) ) {
			return false;
		}

		$sender_name  = NBM_Settings::get( 'email_sender_name', get_bloginfo( 'name' ) );
		$sender_email = NBM_Settings::get( 'email_sender_address', get_option( 'admin_email' ) );

		if ( empty( $sender_email ) || ! is_email( $sender_email ) ) {
			$sender_email = get_option( 'admin_email' );
		}

		$headers = array(
			'Content-Type: text/html; charset=UTF-8',
		);

		if ( ! empty( $reply_to ) && is_email( $reply_to ) ) {
			$headers[] = 'Reply-To: ' . sanitize_email( $reply_to );
		} elseif ( ! empty( $sender_email ) && is_email( $sender_email ) ) {
			$headers[] = 'Reply-To: ' . sanitize_email( $sender_email );
		}

		// Use filters for from & from_name to guarantee PHPMailer compatibility
		$filter_from = function( $original_from ) use ( $sender_email ) {
			return sanitize_email( $sender_email );
		};
		$filter_name = function( $original_name ) use ( $sender_name ) {
			return wp_strip_all_tags( $sender_name );
		};
		$filter_type = function() {
			return 'text/html';
		};

		add_filter( 'wp_mail_from', $filter_from, 999 );
		add_filter( 'wp_mail_from_name', $filter_name, 999 );
		add_filter( 'wp_mail_content_type', $filter_type, 999 );

		// Clear previous error state before sending
		delete_option( 'nbm_last_mail_error' );
		self::$last_error = null;

		$sent = wp_mail( $to, $subject, $body, $headers );

		remove_filter( 'wp_mail_from', $filter_from, 999 );
		remove_filter( 'wp_mail_from_name', $filter_name, 999 );
		remove_filter( 'wp_mail_content_type', $filter_type, 999 );

		if ( $sent ) {
			update_option( 'nbm_last_mail_success', array(
				'timestamp' => current_time( 'mysql' ),
				'to'        => $to,
				'subject'   => $subject,
			) );
		}

		return (bool) $sent;
	}
}
