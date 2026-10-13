<?php
/**
 * Admin View: Settings.
 *
 * @package Nexora_Booking_Manager
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$settings = NBM_Settings::get();
?>
<div class="wrap nbm-admin-wrap">
	<div class="nbm-admin-header">
		<div>
			<h1 class="wp-heading-inline"><?php esc_html_e( 'Nexora Booking Settings', 'nexora-booking-manager' ); ?></h1>
			<p class="nbm-admin-subtitle"><?php esc_html_e( 'Configure global booking rules, currencies, email notifications, design branding and security.', 'nexora-booking-manager' ); ?></p>
		</div>
	</div>

	<?php if ( isset( $_GET['settings-updated'] ) && 'true' === $_GET['settings-updated'] ) : ?>
		<div class="notice notice-success is-dismissible">
			<p><?php esc_html_e( 'Settings saved successfully!', 'nexora-booking-manager' ); ?></p>
		</div>
	<?php endif; ?>

	<form method="post" action="options.php" class="nbm-settings-form">
		<?php
		settings_fields( 'nbm_settings_group' );
		?>

		<div class="nbm-settings-tabs-wrapper">
			
			<!-- SECTION 1: General & Currency -->
			<div class="nbm-panel">
				<h2><?php esc_html_e( 'General & Currency Settings', 'nexora-booking-manager' ); ?></h2>
				<table class="form-table">
					<tr>
						<th scope="row"><label for="currency_code"><?php esc_html_e( 'Currency Code', 'nexora-booking-manager' ); ?></label></th>
						<td>
							<input type="text" id="currency_code" name="nbm_settings[currency_code]" value="<?php echo esc_attr( $settings['currency_code'] ); ?>" class="regular-text" />
							<p class="description"><?php esc_html_e( 'e.g. USD, EUR, GBP, AUD, CAD', 'nexora-booking-manager' ); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="currency_symbol"><?php esc_html_e( 'Currency Symbol', 'nexora-booking-manager' ); ?></label></th>
						<td>
							<input type="text" id="currency_symbol" name="nbm_settings[currency_symbol]" value="<?php echo esc_attr( $settings['currency_symbol'] ); ?>" class="small-text" />
							<p class="description"><?php esc_html_e( 'e.g. $, €, £, ¥, ৳', 'nexora-booking-manager' ); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Currency Position', 'nexora-booking-manager' ); ?></th>
						<td>
							<label><input type="radio" name="nbm_settings[currency_position]" value="before" <?php checked( $settings['currency_position'], 'before' ); ?> /> <?php esc_html_e( 'Before amount ($100)', 'nexora-booking-manager' ); ?></label><br>
							<label><input type="radio" name="nbm_settings[currency_position]" value="after" <?php checked( $settings['currency_position'], 'after' ); ?> /> <?php esc_html_e( 'After amount (100 $)', 'nexora-booking-manager' ); ?></label>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="date_format"><?php esc_html_e( 'Date Format', 'nexora-booking-manager' ); ?></label></th>
						<td>
							<input type="text" id="date_format" name="nbm_settings[date_format]" value="<?php echo esc_attr( $settings['date_format'] ); ?>" class="regular-text" />
							<p class="description"><?php esc_html_e( 'e.g. Y-m-d, m/d/Y, d/m/Y', 'nexora-booking-manager' ); ?></p>
						</td>
					</tr>
				</table>
			</div>

			<!-- SECTION 2: Hotel & Bar Default Rules -->
			<div class="nbm-panel">
				<h2><?php esc_html_e( 'Booking & Stay Rules', 'nexora-booking-manager' ); ?></h2>
				<table class="form-table">
					<tr>
						<th scope="row"><label for="hotel_default_checkin"><?php esc_html_e( 'Default Check-in / Check-out', 'nexora-booking-manager' ); ?></label></th>
						<td>
							<input type="text" id="hotel_default_checkin" name="nbm_settings[hotel_default_checkin]" value="<?php echo esc_attr( $settings['hotel_default_checkin'] ); ?>" class="small-text" placeholder="14:00" />
							<span>—</span>
							<input type="text" id="hotel_default_checkout" name="nbm_settings[hotel_default_checkout]" value="<?php echo esc_attr( $settings['hotel_default_checkout'] ); ?>" class="small-text" placeholder="11:00" />
							<p class="description"><?php esc_html_e( 'Standard default check-in and check-out times for hotels.', 'nexora-booking-manager' ); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="hotel_default_min_stay"><?php esc_html_e( 'Default Min / Max Stay', 'nexora-booking-manager' ); ?></label></th>
						<td>
							<input type="number" id="hotel_default_min_stay" name="nbm_settings[hotel_default_min_stay]" value="<?php echo esc_attr( $settings['hotel_default_min_stay'] ); ?>" class="small-text" min="1" />
							<span><?php esc_html_e( 'to', 'nexora-booking-manager' ); ?></span>
							<input type="number" id="hotel_default_max_stay" name="nbm_settings[hotel_default_max_stay]" value="<?php echo esc_attr( $settings['hotel_default_max_stay'] ); ?>" class="small-text" min="1" />
							<span><?php esc_html_e( 'nights', 'nexora-booking-manager' ); ?></span>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="bar_default_duration"><?php esc_html_e( 'Default Bar Slot Duration', 'nexora-booking-manager' ); ?></label></th>
						<td>
							<input type="number" id="bar_default_duration" name="nbm_settings[bar_default_duration]" value="<?php echo esc_attr( $settings['bar_default_duration'] ); ?>" class="small-text" step="15" min="15" />
							<span><?php esc_html_e( 'minutes per table reservation', 'nexora-booking-manager' ); ?></span>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="bar_default_notice_hours"><?php esc_html_e( 'Advance Reservation Notice', 'nexora-booking-manager' ); ?></label></th>
						<td>
							<input type="number" id="bar_default_notice_hours" name="nbm_settings[bar_default_notice_hours]" value="<?php echo esc_attr( $settings['bar_default_notice_hours'] ); ?>" class="small-text" min="0" />
							<span><?php esc_html_e( 'hours notice required prior to reservation time', 'nexora-booking-manager' ); ?></span>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="bar_default_card_preset"><?php esc_html_e( 'Default Bar Card Design Preset', 'nexora-booking-manager' ); ?></label></th>
						<td>
							<select id="bar_default_card_preset" name="nbm_settings[bar_default_card_preset]">
								<option value="modern" <?php selected( $settings['bar_default_card_preset'] ?? 'modern', 'modern' ); ?>><?php esc_html_e( 'Preset 1: Modern Sophisticate (Clean & Elevated)', 'nexora-booking-manager' ); ?></option>
								<option value="luxury" <?php selected( $settings['bar_default_card_preset'] ?? 'modern', 'luxury' ); ?>><?php esc_html_e( 'Preset 2: Luxury VIP & Nightlife (Dark & Gold Glow)', 'nexora-booking-manager' ); ?></option>
								<option value="glassmorphism" <?php selected( $settings['bar_default_card_preset'] ?? 'modern', 'glassmorphism' ); ?>><?php esc_html_e( 'Preset 3: Glassmorphism Overlay (Full-Bleed Visual)', 'nexora-booking-manager' ); ?></option>
								<option value="minimal" <?php selected( $settings['bar_default_card_preset'] ?? 'modern', 'minimal' ); ?>><?php esc_html_e( 'Preset 4: Minimalist Editorial (Compact Bistro)', 'nexora-booking-manager' ); ?></option>
							</select>
							<p class="description"><?php esc_html_e( 'Default layout used by [nbm_bars] when no preset attribute is specified.', 'nexora-booking-manager' ); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Guest Requirements', 'nexora-booking-manager' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="nbm_settings[require_phone]" value="1" <?php checked( $settings['require_phone'], '1' ); ?> />
								<?php esc_html_e( 'Require guest phone number on booking forms', 'nexora-booking-manager' ); ?>
							</label><br>
							<label>
								<input type="checkbox" name="nbm_settings[enable_conflict_prevention]" value="1" <?php checked( $settings['enable_conflict_prevention'], '1' ); ?> />
								<?php esc_html_e( 'Enforce real-time server conflict checking and concurrency locking', 'nexora-booking-manager' ); ?>
							</label>
						</td>
					</tr>
				</table>
			</div>

			<!-- SECTION 3: Email Notifications -->
			<div class="nbm-panel">
				<h2><?php esc_html_e( 'Email Notification Settings', 'nexora-booking-manager' ); ?></h2>
				<table class="form-table">
					<tr>
						<th scope="row"><label for="admin_email"><?php esc_html_e( 'Admin Notification Email', 'nexora-booking-manager' ); ?></label></th>
						<td>
							<input type="email" id="admin_email" name="nbm_settings[admin_email]" value="<?php echo esc_attr( $settings['admin_email'] ); ?>" class="regular-text" />
							<p class="description"><?php esc_html_e( 'Where new pending reservation alert emails are sent.', 'nexora-booking-manager' ); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="email_sender_name"><?php esc_html_e( 'Sender Name & Email', 'nexora-booking-manager' ); ?></label></th>
						<td>
							<input type="text" id="email_sender_name" name="nbm_settings[email_sender_name]" value="<?php echo esc_attr( $settings['email_sender_name'] ); ?>" class="regular-text" placeholder="Sender Name" /><br style="margin-bottom:6px;">
							<input type="email" id="email_sender_address" name="nbm_settings[email_sender_address]" value="<?php echo esc_attr( $settings['email_sender_address'] ); ?>" class="regular-text" placeholder="sender@example.com" />
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Email Triggers', 'nexora-booking-manager' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="nbm_settings[notify_admin_new_booking]" value="1" <?php checked( $settings['notify_admin_new_booking'], '1' ); ?> />
								<?php esc_html_e( 'Send notification email to admin when a new booking is requested', 'nexora-booking-manager' ); ?>
							</label><br>
							<label>
								<input type="checkbox" name="nbm_settings[notify_guest_pending]" value="1" <?php checked( $settings['notify_guest_pending'], '1' ); ?> />
								<?php esc_html_e( 'Send pending acknowledgement email to guest upon form submission', 'nexora-booking-manager' ); ?>
							</label><br>
							<label>
								<input type="checkbox" name="nbm_settings[notify_guest_status_change]" value="1" <?php checked( $settings['notify_guest_status_change'], '1' ); ?> />
								<?php esc_html_e( 'Send status update email to guest when confirmed, rejected, or cancelled', 'nexora-booking-manager' ); ?>
							</label>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Test Email Delivery', 'nexora-booking-manager' ); ?></th>
						<td>
							<div class="nbm-test-email-box" style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:16px; max-width:580px;">
								<p style="margin:0 0 10px; font-weight:600; font-size:13.5px; color:#1e293b;">
									<?php esc_html_e( 'Verify that your WordPress site can successfully deliver notification emails to inboxes:', 'nexora-booking-manager' ); ?>
								</p>
								<div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
									<input type="email" id="nbm-test-email-input" value="<?php echo esc_attr( $settings['admin_email'] ); ?>" placeholder="your-email@example.com" class="regular-text" style="flex:1; min-width:220px;" />
									<button type="button" id="nbm-send-test-email-btn" class="button button-secondary">
										✉ <?php esc_html_e( 'Send Test Email', 'nexora-booking-manager' ); ?>
									</button>
								</div>
								<div id="nbm-test-email-feedback" style="margin-top:10px; display:none;"></div>
								<?php
								$last_err = get_option( 'nbm_last_mail_error', false );
								$last_ok  = get_option( 'nbm_last_mail_success', false );
								?>
								<div style="margin-top:12px; font-size:12px; color:#64748b; border-top:1px dashed #cbd5e1; padding-top:8px; line-height:1.5;">
									<?php if ( $last_ok ) : ?>
										<div style="color:#059669; font-weight:600; margin-bottom:2px;">
											✓ <?php echo sprintf( esc_html__( 'Last mail dispatched successfully at %s', 'nexora-booking-manager' ), esc_html( $last_ok['timestamp'] ) ); ?>
										</div>
									<?php endif; ?>
									<?php if ( $last_err ) : ?>
										<div style="color:#dc2626; font-weight:600; margin-bottom:4px;">
											⚠ <?php echo sprintf( esc_html__( 'Last mail error (%s): %s', 'nexora-booking-manager' ), esc_html( $last_err['timestamp'] ), esc_html( $last_err['message'] ) ); ?>
										</div>
									<?php endif; ?>
									<div style="color:#475569; margin-top:4px;">
										💡 <strong><?php esc_html_e( 'Server Note:', 'nexora-booking-manager' ); ?></strong> <?php esc_html_e( 'If test emails fail or go to spam, your server might not have native PHP mail() enabled. Install a free SMTP plugin like WP Mail SMTP or FluentSMTP to send reliably through Gmail, Outlook, or webmail.', 'nexora-booking-manager' ); ?>
									</div>
								</div>
							</div>
						</td>
					</tr>
				</table>
			</div>

			<!-- SECTION 4: Styling & Custom Colors -->
			<div class="nbm-panel">
				<h2><?php esc_html_e( 'Design & Color Branding', 'nexora-booking-manager' ); ?></h2>
				<table class="form-table">
					<tr>
						<th scope="row"><label for="primary_color"><?php esc_html_e( 'Primary Brand Color', 'nexora-booking-manager' ); ?></label></th>
						<td>
							<input type="text" id="primary_color" name="nbm_settings[primary_color]" value="<?php echo esc_attr( $settings['primary_color'] ); ?>" class="nbm-color-field" />
							<p class="description"><?php esc_html_e( 'Applied to buttons, badges, and primary highlights.', 'nexora-booking-manager' ); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="accent_color"><?php esc_html_e( 'Accent / Success Color', 'nexora-booking-manager' ); ?></label></th>
						<td>
							<input type="text" id="accent_color" name="nbm_settings[accent_color]" value="<?php echo esc_attr( $settings['accent_color'] ); ?>" class="nbm-color-field" />
						</td>
					</tr>
					<tr>
						<th scope="row"><label for="card_radius"><?php esc_html_e( 'Card Corner Radius (px)', 'nexora-booking-manager' ); ?></label></th>
						<td>
							<input type="number" id="card_radius" name="nbm_settings[card_radius]" value="<?php echo esc_attr( $settings['card_radius'] ); ?>" class="small-text" min="0" max="40" />
							<span>px</span>
						</td>
					</tr>
				</table>
			</div>

			<!-- SECTION 5: Security & Data Retention -->
			<div class="nbm-panel">
				<h2><?php esc_html_e( 'Security & Data Retention', 'nexora-booking-manager' ); ?></h2>
				<table class="form-table">
					<tr>
						<th scope="row"><label for="rate_limit_max"><?php esc_html_e( 'Submission Rate Limiting', 'nexora-booking-manager' ); ?></label></th>
						<td>
							<input type="number" id="rate_limit_max" name="nbm_settings[rate_limit_max]" value="<?php echo esc_attr( $settings['rate_limit_max'] ); ?>" class="small-text" min="1" />
							<span><?php esc_html_e( 'submissions allowed per IP per 5 minutes', 'nexora-booking-manager' ); ?></span>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Anti-Spam Honeypot', 'nexora-booking-manager' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="nbm_settings[enable_honeypot]" value="1" <?php checked( $settings['enable_honeypot'], '1' ); ?> />
								<?php esc_html_e( 'Enable silent honeypot anti-spam trap on all public booking forms', 'nexora-booking-manager' ); ?>
							</label>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Plugin Uninstall Data Wipe', 'nexora-booking-manager' ); ?></th>
						<td>
							<label style="color:#b91c1c;">
								<input type="checkbox" name="nbm_settings[delete_data_on_uninstall]" value="1" <?php checked( $settings['delete_data_on_uninstall'], '1' ); ?> />
								<strong><?php esc_html_e( 'Completely delete all custom tables, bookings and settings when plugin is uninstalled', 'nexora-booking-manager' ); ?></strong>
							</label>
							<p class="description"><?php esc_html_e( 'Warning: By default all your data is preserved when uninstalled. Checking this option will permanently drop the custom booking tables upon uninstallation.', 'nexora-booking-manager' ); ?></p>
						</td>
					</tr>
				</table>
			</div>

		</div>

		<?php submit_button( __( 'Save Nexora Settings', 'nexora-booking-manager' ) ); ?>
	</form>
</div>
