/**
 * Admin JavaScript for Nexora Booking Manager
 */

(function($) {
	'use strict';

	$(document).ready(function() {
		
		// Initialize WordPress Color Pickers if available
		if ($.fn.wpColorPicker) {
			$('.nbm-color-field').wpColorPicker();
		}

		// Quick status change from List or Dashboard
		$('.nbm-quick-status-btn').on('click', function(e) {
			e.preventDefault();
			var $btn = $(this);
			var bookingId = $btn.data('id');
			var newStatus = $btn.data('status');

			if (!confirm(nbm_admin_vars.i18n.confirm_status)) {
				return;
			}

			$btn.prop('disabled', true).text('...');

			$.ajax({
				url: nbm_admin_vars.ajax_url,
				type: 'POST',
				data: {
					action: 'nbm_admin_update_status',
					nonce: nbm_admin_vars.nonce,
					booking_id: bookingId,
					new_status: newStatus
				},
				success: function(res) {
					if (res.success) {
						location.reload();
					} else {
						alert(res.data.message || 'Error updating booking');
						$btn.prop('disabled', false).text(newStatus === 'confirmed' ? '✓' : '✕');
					}
				},
				error: function() {
					alert('Server error occurred.');
					$btn.prop('disabled', false).text(newStatus === 'confirmed' ? '✓' : '✕');
				}
			});
		});

		// Detailed Status Change Form in booking-details.php
		$('#nbm-update-status-submit').on('click', function(e) {
			e.preventDefault();
			var $btn = $(this);
			var $form = $('#nbm-status-form');
			var $feedback = $('#nbm-status-update-feedback');

			var bookingId = $form.find('input[name="booking_id"]').val();
			var newStatus = $('#nbm-status-select').val();
			var note = $('#nbm-status-reason').val();

			if (!confirm(nbm_admin_vars.i18n.confirm_status)) {
				return;
			}

			$btn.prop('disabled', true).text(nbm_admin_vars.i18n.updating);
			$feedback.hide().empty();

			$.ajax({
				url: nbm_admin_vars.ajax_url,
				type: 'POST',
				data: {
					action: 'nbm_admin_update_status',
					nonce: nbm_admin_vars.nonce,
					booking_id: bookingId,
					new_status: newStatus,
					note: note
				},
				success: function(res) {
					$btn.prop('disabled', false).text('Update Status & Notify Guest');
					if (res.success) {
						$feedback.html('<div class="notice notice-success inline" style="margin:0; padding:8px 12px;"><p>' + res.data.message + '</p></div>').fadeIn();
						
						// Update badge
						var statusLabel = newStatus.charAt(0).toUpperCase() + newStatus.slice(1);
						$('#nbm-current-status-badge')
							.attr('class', 'nbm-status-pill nbm-badge-' + newStatus)
							.text(statusLabel);

						setTimeout(function() {
							location.reload();
						}, 1200);
					} else {
						$feedback.html('<div class="notice notice-error inline" style="margin:0; padding:8px 12px;"><p>' + res.data.message + '</p></div>').fadeIn();
					}
				},
				error: function() {
					$btn.prop('disabled', false).text('Update Status & Notify Guest');
					$feedback.html('<div class="notice notice-error inline" style="margin:0; padding:8px 12px;"><p>Server communication error.</p></div>').fadeIn();
				}
			});
		});

		// Add Private Note Form
		$('#nbm-submit-note-btn').on('click', function(e) {
			e.preventDefault();
			var $btn = $(this);
			var bookingId = $btn.data('id');
			var noteText = $('#nbm-new-admin-note').val().trim();

			if (!noteText) {
				alert('Please enter a note before saving.');
				return;
			}

			$btn.prop('disabled', true).text('Saving...');

			$.ajax({
				url: nbm_admin_vars.ajax_url,
				type: 'POST',
				data: {
					action: 'nbm_admin_add_note',
					nonce: nbm_admin_vars.nonce,
					booking_id: bookingId,
					note: noteText
				},
				success: function(res) {
					$btn.prop('disabled', false).text('Save Private Note');
					if (res.success) {
						location.reload();
					} else {
						alert(res.data.message || 'Error saving note.');
					}
				},
				error: function() {
					$btn.prop('disabled', false).text('Save Private Note');
					alert('Server error saving note.');
				}
			});
		});

		// Delete Booking
		$('.nbm-delete-booking-btn').on('click', function(e) {
			e.preventDefault();
			var $btn = $(this);
			var bookingId = $btn.data('id');

			if (!confirm(nbm_admin_vars.i18n.confirm_delete)) {
				return;
			}

			var $row = $('#nbm-row-' + bookingId);
			$row.css('opacity', '0.4');

			$.ajax({
				url: nbm_admin_vars.ajax_url,
				type: 'POST',
				data: {
					action: 'nbm_admin_delete_booking',
					nonce: nbm_admin_vars.nonce,
					booking_id: bookingId
				},
				success: function(res) {
					if (res.success) {
						$row.fadeOut(300, function() {
							$(this).remove();
						});
					} else {
						alert(res.data.message || 'Error deleting booking.');
						$row.css('opacity', '1');
					}
				},
				error: function() {
					alert('Server communication error.');
					$row.css('opacity', '1');
				}
			});
		});

		// Send Test Email diagnostic in Settings
		$('#nbm-send-test-email-btn').on('click', function(e) {
			e.preventDefault();
			var $btn = $(this);
			var email = $('#nbm-test-email-input').val().trim();
			var $feedback = $('#nbm-test-email-feedback');

			if (!email) {
				alert('Please enter an email address to test.');
				return;
			}

			var originalText = $btn.text();
			$btn.prop('disabled', true).text('Sending...');
			$feedback.hide().empty();

			$.ajax({
				url: nbm_admin_vars.ajax_url,
				type: 'POST',
				data: {
					action: 'nbm_admin_send_test_email',
					nonce: nbm_admin_vars.nonce,
					test_email: email
				},
				success: function(res) {
					$btn.prop('disabled', false).text(originalText);
					if (res.success) {
						$feedback.html('<div class="notice notice-success inline" style="margin:0; padding:8px 12px; border-left-color:#10b981;"><p><strong>✓ Success:</strong> ' + res.data.message + '</p></div>').fadeIn();
					} else {
						$feedback.html('<div class="notice notice-error inline" style="margin:0; padding:8px 12px; border-left-color:#ef4444;"><p><strong>✕ Delivery Error:</strong> ' + res.data.message + '</p></div>').fadeIn();
					}
				},
				error: function() {
					$btn.prop('disabled', false).text(originalText);
					$feedback.html('<div class="notice notice-error inline" style="margin:0; padding:8px 12px; border-left-color:#ef4444;"><p><strong>✕ Server Error:</strong> Could not connect to the server to send test email.</p></div>').fadeIn();
				}
			});
		});

		// =========================================================================
		// SHORTCODE GENERATOR & LIVE PREVIEW STUDIO
		// =========================================================================
		if ($('#nbm-preview-stage').length) {
			var currentViewMode = 'grid'; // 'grid' or 'single'

			// Preset templates generator
			function renderSampleCard(type, preset, accent, cardBg, customBtn) {
				var bgStyle = cardBg ? 'background-color:' + cardBg + ';' : '';
				var customBtnStyle = accent ? 'background-color:' + accent + ' !important; border-color:' + accent + ' !important;' : '';

				if (type === 'hotels') {
					var hotelBtn = customBtn || 'Book Stay';
					return '<div class="nbm-card nbm-hotel-card" style="' + bgStyle + '">' +
						'<div class="nbm-card-media">' +
							'<a href="#" class="nbm-media-link" onclick="return false;">' +
								'<div class="nbm-card-placeholder"><span class="dashicons dashicons-building"></span></div>' +
							'</a>' +
							'<div class="nbm-price-badge"><span class="nbm-price-prefix">From</span> <strong class="nbm-price-amount">$220.00</strong> <span class="nbm-price-period">/night</span></div>' +
						'</div>' +
						'<div class="nbm-card-body">' +
							'<div class="nbm-card-location"><span class="nbm-icon-pin">📍</span><span>Miami Beach, Florida</span></div>' +
							'<h3 class="nbm-card-title"><a href="#" onclick="return false;">The Grand Azure Luxury Suite</a></h3>' +
							'<div class="nbm-card-excerpt">Oceanfront balcony with heated private pool, king bed, and 24/7 concierge.</div>' +
							'<div class="nbm-card-amenities"><span class="nbm-amenity-tag">Infinity Pool</span><span class="nbm-amenity-tag">Free WiFi</span><span class="nbm-amenity-tag">Spa</span></div>' +
							'<div class="nbm-card-footer">' +
								'<div class="nbm-capacity-info"><span>👥 Up to 4 guests</span></div>' +
								'<a href="#" class="nbm-btn nbm-btn-primary" style="' + customBtnStyle + '" onclick="return false;">' + hotelBtn + '</a>' +
							'</div>' +
						'</div>' +
					'</div>';
				}

				// Bars presets
				var btnText = customBtn || (preset === 'luxury' ? 'Book VIP Table' : (preset === 'neon' ? 'Reserve Neon Lounge' : (preset === 'vintage' ? 'Request Table' : 'Reserve Table')));

				switch (preset) {
					case 'luxury':
						return '<div class="nbm-card nbm-bar-card nbm-preset-luxury" style="' + bgStyle + '">' +
							'<div class="nbm-luxury-glow"></div>' +
							'<div class="nbm-card-media">' +
								'<a href="#" class="nbm-media-link" onclick="return false;"><div class="nbm-card-placeholder nbm-luxury-placeholder"><span class="dashicons dashicons-beer"></span></div></a>' +
								'<div class="nbm-luxury-badge"><span class="nbm-luxury-crown">👑</span><span class="nbm-luxury-tier">$$$ VIP</span></div>' +
							'</div>' +
							'<div class="nbm-card-body">' +
								'<div class="nbm-card-location nbm-luxury-location"><span class="nbm-sparkle">✦</span><span>Rooftop Penthouse, Manhattan</span></div>' +
								'<h3 class="nbm-card-title nbm-luxury-title"><a href="#" onclick="return false;">Onyx Horizon Champagne Bar</a></h3>' +
								'<div class="nbm-card-excerpt nbm-luxury-excerpt">Elevated panoramic skyline lounge featuring rare vintage spirits and private booth service.</div>' +
								'<div class="nbm-luxury-instruction-peek"><span class="nbm-info-icon">👔</span><span>Dress code: Sophisticated cocktail attire</span></div>' +
								'<div class="nbm-card-footer nbm-luxury-footer">' +
									'<div class="nbm-meta-stats nbm-luxury-stats"><span>🍾 Up to 8 guests</span><span>⏱ 120m</span></div>' +
									'<a href="#" class="nbm-btn nbm-btn-luxury" style="' + (accent ? 'background:' + accent + ' !important;' : '') + '" onclick="return false;"><span>' + btnText + '</span><span class="nbm-luxury-arrow">✦</span></a>' +
								'</div>' +
							'</div>' +
						'</div>';

					case 'glassmorphism':
						return '<div class="nbm-card nbm-bar-card nbm-preset-glassmorphism" style="min-height:380px;">' +
							'<div class="nbm-glass-bg-wrapper">' +
								'<div class="nbm-glass-placeholder"><span class="dashicons dashicons-beer"></span></div>' +
								'<div class="nbm-glass-top-pill"><span class="nbm-glass-range">$$$</span><span class="nbm-glass-dot">•</span><span class="nbm-glass-city">Bayside Deck</span></div>' +
							'</div>' +
							'<div class="nbm-glass-frosted-panel" style="' + bgStyle + '">' +
								'<h3 class="nbm-card-title nbm-glass-title"><a href="#" onclick="return false;">Solarium Sunset Botanical Bar</a></h3>' +
								'<div class="nbm-glass-meta-row"><span>⏱ 90m slot</span><span>🪑 35 capacity</span></div>' +
								'<div class="nbm-glass-footer">' +
									'<a href="#" class="nbm-btn nbm-btn-glass" style="' + customBtnStyle + '" onclick="return false;"><span>' + btnText + '</span><span class="nbm-glass-arrow">&nearr;</span></a>' +
								'</div>' +
							'</div>' +
						'</div>';

					case 'minimal':
						return '<div class="nbm-card nbm-bar-card nbm-preset-minimal" style="' + bgStyle + '">' +
							'<div class="nbm-card-media nbm-minimal-media">' +
								'<div class="nbm-card-placeholder"><span class="dashicons dashicons-beer"></span></div>' +
								'<span class="nbm-minimal-tag">$$</span>' +
							'</div>' +
							'<div class="nbm-card-body nbm-minimal-body">' +
								'<div class="nbm-minimal-header">' +
									'<h3 class="nbm-card-title nbm-minimal-title"><a href="#" onclick="return false;">Atelier Craft & Taproom</a></h3>' +
									'<span class="nbm-minimal-location">Downtown Arts District</span>' +
								'</div>' +
								'<div class="nbm-minimal-specs">' +
									'<div class="nbm-spec-item"><span class="nbm-spec-label">Slot</span><span class="nbm-spec-val">90m</span></div>' +
									'<div class="nbm-spec-item"><span class="nbm-spec-label">Capacity</span><span class="nbm-spec-val">24 seats</span></div>' +
									'<div class="nbm-spec-item"><span class="nbm-spec-label">Tier</span><span class="nbm-spec-val">$$</span></div>' +
								'</div>' +
								'<div class="nbm-card-footer nbm-minimal-footer">' +
									'<a href="#" class="nbm-btn nbm-btn-outline nbm-btn-minimal" style="' + (accent ? 'border-color:' + accent + '; color:' + accent + ' !important;' : '') + '" onclick="return false;">' + btnText + '</a>' +
								'</div>' +
							'</div>' +
						'</div>';

					case 'neon':
						return '<div class="nbm-card nbm-bar-card nbm-preset-neon" style="' + bgStyle + '">' +
							'<div class="nbm-neon-glow-border"></div>' +
							'<div class="nbm-card-media nbm-neon-media">' +
								'<div class="nbm-card-placeholder nbm-neon-placeholder"><span class="dashicons dashicons-format-audio"></span></div>' +
								'<div class="nbm-neon-badge-pill"><span class="nbm-neon-pulse-dot"></span><span class="nbm-neon-tag-text">$$$ • ELECTRIC</span></div>' +
							'</div>' +
							'<div class="nbm-card-body nbm-neon-body">' +
								'<div class="nbm-card-location nbm-neon-location"><span class="nbm-neon-icon">⚡</span><span>Cyber District 4</span></div>' +
								'<h3 class="nbm-card-title nbm-neon-title"><a href="#" onclick="return false;">Hyperion Neon Cocktail Club</a></h3>' +
								'<div class="nbm-card-excerpt nbm-neon-excerpt">Immersive lighting, techno beats, signature nitrogen cocktails and VIP booths.</div>' +
								'<div class="nbm-neon-tags"><span class="nbm-neon-tag">Live DJ</span><span class="nbm-neon-tag">VIP Pods</span></div>' +
								'<div class="nbm-card-footer nbm-neon-footer">' +
									'<div class="nbm-meta-stats nbm-neon-stats"><span>⏱ 90m</span><span>🪩 20 cap</span></div>' +
									'<a href="#" class="nbm-btn nbm-btn-neon" style="' + customBtnStyle + '" onclick="return false;"><span>' + btnText + '</span><span class="nbm-neon-arrow">&rarr;</span></a>' +
								'</div>' +
							'</div>' +
						'</div>';

					case 'vintage':
						return '<div class="nbm-card nbm-bar-card nbm-preset-vintage" style="' + bgStyle + '">' +
							'<div class="nbm-vintage-seal"><span class="nbm-vintage-seal-ring">★</span><span class="nbm-vintage-seal-tier">$$$</span></div>' +
							'<div class="nbm-card-media nbm-vintage-media"><div class="nbm-card-placeholder nbm-vintage-placeholder"><span class="dashicons dashicons-products"></span></div></div>' +
							'<div class="nbm-card-body nbm-vintage-body">' +
								'<div class="nbm-card-location nbm-vintage-location"><span>✦ HISTORIC FRENCH QUARTER ✦</span></div>' +
								'<h3 class="nbm-card-title nbm-vintage-title"><a href="#" onclick="return false;">The Copper Cask Speakeasy</a></h3>' +
								'<div class="nbm-vintage-divider"><span class="nbm-vintage-line"></span><span class="nbm-vintage-diamond">♦</span><span class="nbm-vintage-line"></span></div>' +
								'<div class="nbm-card-excerpt nbm-vintage-excerpt">Hand-carved mahogany, barrel-aged bourbon, and Prohibition-era cocktail artistry.</div>' +
								'<div class="nbm-card-footer nbm-vintage-footer">' +
									'<div class="nbm-meta-stats nbm-vintage-stats"><span>🥃 120 min</span><span>🛋 16 seats</span></div>' +
									'<a href="#" class="nbm-btn nbm-btn-vintage" style="' + customBtnStyle + '" onclick="return false;">' + btnText + '</a>' +
								'</div>' +
							'</div>' +
						'</div>';

					case 'bistro_horizontal':
						return '<div class="nbm-card nbm-bar-card nbm-preset-bistro_horizontal" style="' + bgStyle + '">' +
							'<div class="nbm-card-media nbm-horizontal-media">' +
								'<div class="nbm-card-placeholder"><span class="dashicons dashicons-coffee"></span></div>' +
								'<span class="nbm-badge-pill nbm-horizontal-pill">$$$</span>' +
							'</div>' +
							'<div class="nbm-card-body nbm-horizontal-body">' +
								'<div class="nbm-horizontal-header">' +
									'<div class="nbm-card-location"><span>📍 West End Promenade</span></div>' +
									'<h3 class="nbm-card-title nbm-horizontal-title"><a href="#" onclick="return false;">L\'Aubergine Executive Bistro & Bar</a></h3>' +
								'</div>' +
								'<div class="nbm-card-excerpt nbm-horizontal-excerpt">Farm-to-table culinary delicacies, sommelier pairings, and private business meeting alcoves.</div>' +
								'<div class="nbm-horizontal-meta-grid">' +
									'<div class="nbm-h-meta-item"><span class="nbm-h-meta-lbl">Window</span><span class="nbm-h-meta-val">⏱ 90 mins</span></div>' +
									'<div class="nbm-h-meta-item"><span class="nbm-h-meta-lbl">Seating</span><span class="nbm-h-meta-val">🪑 30 seats</span></div>' +
									'<div class="nbm-h-meta-item"><span class="nbm-h-meta-lbl">Service</span><span class="nbm-h-meta-val">🍸 Fine Dining</span></div>' +
								'</div>' +
								'<div class="nbm-card-footer nbm-horizontal-footer">' +
									'<div class="nbm-card-tags"><span class="nbm-tag-pill">Sommelier</span><span class="nbm-tag-pill">Valet</span></div>' +
									'<a href="#" class="nbm-btn nbm-btn-primary nbm-horizontal-btn" style="' + customBtnStyle + '" onclick="return false;"><span>' + btnText + '</span><span class="nbm-btn-arrow">&rarr;</span></a>' +
								'</div>' +
							'</div>' +
						'</div>';

					case 'modern':
					default:
						return '<div class="nbm-card nbm-bar-card nbm-preset-modern" style="' + bgStyle + '">' +
							'<div class="nbm-card-media">' +
								'<a href="#" class="nbm-media-link" onclick="return false;"><div class="nbm-card-placeholder"><span class="dashicons dashicons-beer"></span></div></a>' +
								'<div class="nbm-card-badges-top"><span class="nbm-badge-pill nbm-badge-price">$$</span><span class="nbm-badge-pill nbm-badge-status-open">Open 5:00 PM</span></div>' +
							'</div>' +
							'<div class="nbm-card-body">' +
								'<div class="nbm-card-location"><span class="nbm-pin-icon">📍</span><span>SoHo Central, New York</span></div>' +
								'<h3 class="nbm-card-title"><a href="#" onclick="return false;">The Velvet Velvet Lounge</a></h3>' +
								'<div class="nbm-card-excerpt">Artisanal craft cocktails, curated charcuterie boards, and intimate ambient jazz lighting.</div>' +
								'<div class="nbm-card-tags"><span class="nbm-tag-pill">Craft Cocktails</span><span class="nbm-tag-pill">Live Jazz</span></div>' +
								'<div class="nbm-card-footer">' +
									'<div class="nbm-meta-stats"><span>⏱ 90m</span><span>🪑 25 seats</span></div>' +
									'<a href="#" class="nbm-btn nbm-btn-primary" style="' + customBtnStyle + '" onclick="return false;"><span>' + btnText + '</span><span class="nbm-btn-arrow">&rarr;</span></a>' +
								'</div>' +
							'</div>' +
						'</div>';
				}
			}

			function refreshLivePreview() {
				var type = $('#nbm-gen-type').val();
				var preset = $('#nbm-gen-preset').val();
				var columns = parseInt($('#nbm-gen-columns').val(), 10) || 3;
				var accent = $('#nbm-gen-accent').val();
				var cardBg = $('#nbm-gen-cardbg').val();
				var btnText = $('#nbm-gen-btntext').val().trim();

				// Update chip active state
				$('.nbm-chip').removeClass('active');
				$('.nbm-chip[data-preset="' + preset + '"]').addClass('active');

				var html = '';
				if (currentViewMode === 'single') {
					html = '<div style="max-width: ' + (preset === 'bistro_horizontal' ? '780px' : '380px') + '; margin: 0 auto;">' +
						renderSampleCard(type, preset, accent, cardBg, btnText) +
					'</div>';
				} else {
					var count = Math.max(columns, 3);
					if (columns === 1) count = 2;
					if (columns === 4) count = 4;
					if (preset === 'bistro_horizontal') count = 2;

					html = '<div class="nbm-grid nbm-grid-cols-' + columns + '">';
					for (var i = 0; i < count; i++) {
						html += renderSampleCard(type, preset, accent, cardBg, btnText);
					}
					html += '</div>';
				}

				$('#nbm-preview-stage').html(html);
				updateGeneratedShortcode();
			}

			function updateGeneratedShortcode() {
				var type = $('#nbm-gen-type').val();
				var shortcodeTag = (type === 'hotels') ? 'nbm_hotels' : 'nbm_bars';
				var preset = $('#nbm-gen-preset').val();
				var columns = $('#nbm-gen-columns').val();
				var perPage = $('#nbm-gen-perpage').val();
				var accent = $('#nbm-gen-accent').val();
				var cardBg = $('#nbm-gen-cardbg').val();
				var btnText = $('#nbm-gen-btntext').val().trim();
				var city = $('#nbm-gen-city').val().trim();
				var pagination = $('#nbm-gen-pagination').is(':checked') ? 'yes' : 'no';

				var parts = [shortcodeTag];

				if (type === 'bars' || type === 'hotels') {
					parts.push('preset="' + preset + '"');
				}
				parts.push('columns="' + columns + '"');
				parts.push('per_page="' + perPage + '"');

				if (city) {
					parts.push('city="' + city + '"');
				}
				if (btnText) {
					parts.push('btn_text="' + btnText + '"');
				}
				if (accent && accent !== '#1e40af') {
					parts.push('accent_color="' + accent + '"');
				}
				if (cardBg && cardBg !== '#ffffff') {
					parts.push('card_bg="' + cardBg + '"');
				}
				if (pagination === 'no') {
					parts.push('pagination="no"');
				}

				var fullCode = '[' + parts.join(' ') + ']';
				$('#nbm-generated-shortcode').val(fullCode);
			}

			// Watch inputs
			$('#nbm-shortcode-builder-form').on('input change', 'input, select', function() {
				refreshLivePreview();
			});

			// Chip click selector
			$('.nbm-chip').on('click', function() {
				var targetPreset = $(this).data('preset');
				$('#nbm-gen-preset').val(targetPreset).trigger('change');
			});

			// View mode switcher
			$('.nbm-view-mode-btn').on('click', function(e) {
				e.preventDefault();
				$('.nbm-view-mode-btn').removeClass('active');
				$(this).addClass('active');
				currentViewMode = $(this).data('mode');
				refreshLivePreview();
			});

			// Device switcher
			$('.nbm-device-btn').on('click', function(e) {
				e.preventDefault();
				$('.nbm-device-btn').removeClass('active');
				$(this).addClass('active');
				var width = $(this).data('width');
				$('#nbm-preview-wrapper').css('max-width', width);
			});

			// Copy shortcode
			$('#nbm-copy-shortcode-btn').on('click', function(e) {
				e.preventDefault();
				var $txt = $('#nbm-generated-shortcode');
				$txt.select();
				navigator.clipboard.writeText($txt.val()).then(function() {
					$('#nbm-copy-success-msg').fadeIn(200).delay(2000).fadeOut(300);
				});
			});

			// SAVE PRESET DEFAULTS AJAX ACTION
			$('#nbm-save-preset-defaults-btn, #nbm-save-preset-defaults-btn-side').on('click', function(e) {
				e.preventDefault();
				var $btn = $(this);
				var originalText = $btn.html();
				var $feedback = $('#nbm-preset-save-feedback');

				$btn.prop('disabled', true).text('Saving defaults...');
				$feedback.hide().empty();

				$.ajax({
					url: nbm_admin_vars.ajax_url,
					type: 'POST',
					data: {
						action: 'nbm_admin_save_preset_defaults',
						nonce: nbm_admin_vars.nonce,
						entity_type: $('#nbm-gen-type').val(),
						preset: $('#nbm-gen-preset').val(),
						columns: $('#nbm-gen-columns').val(),
						per_page: $('#nbm-gen-perpage').val(),
						accent_color: $('#nbm-gen-accent').val(),
						card_bg: $('#nbm-gen-cardbg').val(),
						btn_text: $('#nbm-gen-btntext').val().trim()
					},
					success: function(res) {
						$btn.prop('disabled', false).html(originalText);
						if (res.success) {
							$feedback.html('<div class="notice notice-success inline" style="margin:0; padding:10px 14px; border-left-color:#10b981; font-size:14px;"><p><strong>' + res.data.message + '</strong></p></div>').fadeIn();
							$('html, body').animate({ scrollTop: 0 }, 300);
						} else {
							$feedback.html('<div class="notice notice-error inline" style="margin:0; padding:10px 14px; border-left-color:#ef4444;"><p>' + res.data.message + '</p></div>').fadeIn();
						}
					},
					error: function() {
						$btn.prop('disabled', false).html(originalText);
						$feedback.html('<div class="notice notice-error inline" style="margin:0; padding:10px 14px; border-left-color:#ef4444;"><p>Server communication error while saving preset settings.</p></div>').fadeIn();
					}
				});
			});

			// Initialize preview on page load
			refreshLivePreview();
		}

		// =========================================================================
		// STUDIO TOP NAVIGATION TABS (CARDS VS SINGLE DETAILS)
		// =========================================================================
		$('.nbm-top-tab-btn').on('click', function(e) {
			e.preventDefault();
			var target = $(this).data('target');
			$('.nbm-top-tab-btn').removeClass('nav-tab-active');
			$(this).addClass('nav-tab-active');

			if (target === 'cards') {
				$('#nbm-tab-section-cards').show();
				$('#nbm-tab-section-single').hide();
			} else {
				$('#nbm-tab-section-cards').hide();
				$('#nbm-tab-section-single').show();
				if (typeof refreshSingleLivePreview === 'function') {
					refreshSingleLivePreview();
				}
			}
		});

		$('.nbm-trigger-save-cards').on('click', function(e) {
			e.preventDefault();
			$('#nbm-save-preset-defaults-btn').trigger('click');
		});

		// =========================================================================
		// SINGLE DETAILS PAGE DESIGN STUDIO (5 RESPONSIVE LAYOUTS)
		// =========================================================================
		if ($('#nbm-single-preview-stage').length) {
			var currentSingleLayout = $('input[name="nbm_single_layout_choice"]:checked').val() || 'classic_hero';

			var layoutTitles = {
				'classic_hero': 'Classic Luxury Hero',
				'split_screen': 'Modern 50/50 Split Screen',
				'fullscreen_cinematic': 'Fullscreen Cinematic (Dark Onyx)',
				'editorial_magazine': 'Editorial Magazine & Story',
				'tabbed_navigator': 'Interactive Tabbed Navigator'
			};

			function renderSingleMockup(layout) {
				var badgeTitle = layoutTitles[layout] || layout;
				$('#nbm-single-active-badge').text(badgeTitle);

				$('.nbm-layout-card').removeClass('selected');
				$('.nbm-layout-card[data-layout="' + layout + '"]').addClass('selected');

				if (layout === 'split_screen') {
					return '<div class="nbm-single-listing nbm-single-layout-split_screen">' +
						'<div class="nbm-container">' +
							'<div class="nbm-single-layout" style="display:grid; grid-template-columns:1fr 1fr; gap:36px;">' +
								'<div class="nbm-split-left" style="position:sticky; top:20px;">' +
									'<div class="nbm-main-image" style="height:440px; border-radius:14px; overflow:hidden; position:relative; background:#1e293b; display:flex; align-items:center; justify-content:center; color:#94a3b8;">' +
										'<span class="dashicons dashicons-format-image" style="font-size:64px; width:64px; height:64px;"></span>' +
										'<div style="position:absolute; bottom:16px; left:16px; background:rgba(15,23,42,0.8); backdrop-filter:blur(8px); padding:8px 16px; border-radius:8px; color:#fff;">' +
											'<span style="color:#38bdf8; font-weight:700;">$$$ VIP</span> • 📍 Manhattan Skyline' +
										'</div>' +
									'</div>' +
									'<div style="display:grid; grid-template-columns:repeat(3, 1fr); gap:10px; margin-top:10px;">' +
										'<div style="height:90px; background:#e2e8f0; border-radius:6px; display:flex; align-items:center; justify-content:center; color:#94a3b8;"><span class="dashicons dashicons-camera"></span></div>' +
										'<div style="height:90px; background:#e2e8f0; border-radius:6px; display:flex; align-items:center; justify-content:center; color:#94a3b8;"><span class="dashicons dashicons-camera"></span></div>' +
										'<div style="height:90px; background:#e2e8f0; border-radius:6px; display:flex; align-items:center; justify-content:center; color:#94a3b8;"><span class="dashicons dashicons-camera"></span></div>' +
									'</div>' +
								'</div>' +
								'<div class="nbm-split-right">' +
									'<div class="nbm-single-header">' +
										'<div class="nbm-header-meta"><span class="nbm-badge nbm-badge-bar">Bar & Lounge</span><span class="nbm-location-text">📍 5th Avenue Penthouse, New York</span></div>' +
										'<h1 class="nbm-single-title" style="font-size:30px;">The Celestial Horizon Lounge</h1>' +
									'</div>' +
									'<div class="nbm-features-ribbon" style="display:flex; justify-content:space-between; margin-bottom:20px;">' +
										'<div class="nbm-ribbon-item"><span class="nbm-r-label">Tier</span><span class="nbm-r-val">$$$ VIP</span></div>' +
										'<div class="nbm-ribbon-item"><span class="nbm-r-label">Max Party</span><span class="nbm-r-val">8 Guests</span></div>' +
										'<div class="nbm-ribbon-item"><span class="nbm-r-label">Slot Duration</span><span class="nbm-r-val">120 Mins</span></div>' +
									'</div>' +
									'<div class="nbm-content-block" style="background:#fff; border:1px solid #e2e8f0; border-radius:10px; padding:20px; margin-bottom:20px;">' +
										'<h3 style="margin:0 0 10px; font-size:18px;">About This Venue</h3>' +
										'<p style="color:#64748b; line-height:1.6; margin:0;">Experience world-class mixology above the clouds. Bespoke cocktails, vintage champagnes, and skyline booths overlooking Central Park.</p>' +
									'</div>' +
									'<div class="nbm-content-block" style="background:#fff; border:1px solid #e2e8f0; border-radius:10px; padding:20px; margin-bottom:20px;">' +
										'<h3 style="margin:0 0 10px; font-size:18px;">Operating Hours</h3>' +
										'<p style="margin:4px 0; color:#475569;"><strong>Monday – Thursday:</strong> 5:00 PM – 1:00 AM</p>' +
										'<p style="margin:4px 0; color:#10b981;"><strong>Friday – Saturday:</strong> 4:00 PM – 3:00 AM</p>' +
									'</div>' +
									'<div style="background:#fff; border:2px solid #1e40af; border-radius:12px; padding:20px; box-shadow:0 10px 25px rgba(30,64,175,0.08);">' +
										'<h3 style="margin:0 0 12px; font-size:18px; color:#1e40af;">📅 Instant Reservation</h3>' +
										'<div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-bottom:12px;">' +
											'<div><label style="font-size:12px; font-weight:700;">Date</label><input type="date" value="2026-10-20" style="width:100%; padding:6px;" readonly /></div>' +
											'<div><label style="font-size:12px; font-weight:700;">Time</label><input type="text" value="08:00 PM" style="width:100%; padding:6px;" readonly /></div>' +
										'</div>' +
										'<button type="button" class="nbm-btn nbm-btn-primary" style="width:100%; padding:10px; font-weight:700;">Submit Reservation Request &rarr;</button>' +
									'</div>' +
								'</div>' +
							'</div>' +
						'</div>' +
					'</div>';
				}

				if (layout === 'fullscreen_cinematic') {
					return '<div class="nbm-single-listing nbm-single-layout-fullscreen_cinematic" style="background:#090d16; color:#f8fafc; padding:30px 20px; border-radius:12px;">' +
						'<div class="nbm-single-header" style="text-align:left; border-bottom:1px solid rgba(245,158,11,0.2); padding-bottom:16px; margin-bottom:24px;">' +
							'<span style="background:rgba(245,158,11,0.15); border:1px solid #f59e0b; color:#fbbf24; font-size:11.5px; font-weight:800; padding:4px 12px; border-radius:20px; text-transform:uppercase; letter-spacing:1px;">👑 24K VIP CINEMATIC EXPERIENCE</span>' +
							'<h1 class="nbm-single-title" style="color:#ffffff; font-size:34px; margin:10px 0 6px;">The Obsidian Velvet Lounge</h1>' +
							'<span style="color:#fbbf24; font-size:13px;">📍 Rooftop Penthouse, Miami Beach • Dress Code: Sophisticated Cocktail</span>' +
						'</div>' +
						'<div class="nbm-gallery-showcase" style="border:1px solid rgba(245,158,11,0.3); border-radius:12px; overflow:hidden; margin-bottom:28px; box-shadow:0 0 30px rgba(245,158,11,0.15);">' +
							'<div class="nbm-main-image" style="height:360px; background:#111827; display:flex; align-items:center; justify-content:center; color:#94a3b8;"><span class="dashicons dashicons-format-image" style="font-size:64px; width:64px; height:64px;"></span></div>' +
						'</div>' +
						'<div class="nbm-single-layout" style="display:grid; grid-template-columns:1.8fr 1.2fr; gap:30px;">' +
							'<div>' +
								'<div class="nbm-features-ribbon" style="background:rgba(255,255,255,0.03); border:1px solid rgba(245,158,11,0.25); border-radius:10px; padding:16px; display:flex; justify-content:space-around; margin-bottom:24px;">' +
									'<div style="text-align:center;"><span style="color:#94a3b8; font-size:11px; text-transform:uppercase; display:block;">Tier</span><strong style="color:#fbbf24; font-size:16px;">$$$ VIP</strong></div>' +
									'<div style="text-align:center;"><span style="color:#94a3b8; font-size:11px; text-transform:uppercase; display:block;">Capacity</span><strong style="color:#fbbf24; font-size:16px;">10 Guests</strong></div>' +
									'<div style="text-align:center;"><span style="color:#94a3b8; font-size:11px; text-transform:uppercase; display:block;">Duration</span><strong style="color:#fbbf24; font-size:16px;">120 Mins</strong></div>' +
								'</div>' +
								'<div class="nbm-content-block" style="background:rgba(255,255,255,0.04); border:1px solid rgba(245,158,11,0.2); border-radius:12px; padding:24px; margin-bottom:20px;">' +
									'<h3 style="color:#fbbf24; margin:0 0 12px; font-size:18px;">About The Experience</h3>' +
									'<p style="color:#cbd5e1; line-height:1.6; margin:0;">An opulent nocturnal haven. Immersive ambient jazz lighting, gold-leaf mixology, and dedicated personal concierge service for discerning patrons.</p>' +
								'</div>' +
								'<div class="nbm-content-block" style="background:rgba(255,255,255,0.04); border:1px solid rgba(245,158,11,0.2); border-radius:12px; padding:24px;">' +
									'<h3 style="color:#fbbf24; margin:0 0 12px; font-size:18px;">VIP Privileges</h3>' +
									'<div style="display:flex; gap:10px; flex-wrap:wrap;">' +
										'<span style="background:rgba(245,158,11,0.1); border:1px solid rgba(245,158,11,0.3); color:#fde68a; padding:6px 12px; border-radius:6px; font-size:12px;">✓ Private Booth</span>' +
										'<span style="background:rgba(245,158,11,0.1); border:1px solid rgba(245,158,11,0.3); color:#fde68a; padding:6px 12px; border-radius:6px; font-size:12px;">✓ Valet Service</span>' +
									'</div>' +
								'</div>' +
							'</div>' +
							'<div>' +
								'<div style="background:#0f1523; border:1px solid rgba(245,158,11,0.35); border-radius:14px; padding:24px; box-shadow:0 10px 30px rgba(0,0,0,0.5);">' +
									'<h3 style="color:#fbbf24; margin:0 0 14px; font-size:18px;">👑 Request VIP Table</h3>' +
									'<p style="color:#94a3b8; font-size:12.5px; margin-bottom:14px;">Instant status notification sent directly to your phone & email.</p>' +
									'<button type="button" class="nbm-btn" style="width:100%; background:linear-gradient(135deg, #f59e0b 0%, #d97706 100%) !important; color:#0f172a !important; font-weight:800; padding:12px; border:none; border-radius:8px; cursor:pointer;">✦ Request VIP Reservation</button>' +
								'</div>' +
							'</div>' +
						'</div>' +
					'</div>';
				}

				if (layout === 'editorial_magazine') {
					return '<div class="nbm-single-listing nbm-single-layout-editorial_magazine" style="background:#ffffff; color:#111827; padding:20px;">' +
						'<div class="nbm-single-header" style="text-align:center; max-width:800px; margin:0 auto 30px;">' +
							'<div style="font-size:12px; text-transform:uppercase; letter-spacing:2px; color:#6b7280; margin-bottom:8px;">— The Culinary & Spirits Gazette —</div>' +
							'<h1 style="font-family:Georgia, serif; font-size:38px; font-weight:700; margin:0 0 10px; line-height:1.2;">The Botanist\'s Conservatory & Bistro</h1>' +
							'<div style="font-size:13.5px; color:#4b5563; font-style:italic;">By Jasmin • Downtown Cultural District • Established 2026</div>' +
						'</div>' +
						'<div class="nbm-gallery-showcase" style="border-radius:4px; overflow:hidden; margin-bottom:30px;">' +
							'<div class="nbm-main-image" style="height:400px; background:#f3f4f6; display:flex; align-items:center; justify-content:center; color:#9ca3af;"><span class="dashicons dashicons-format-image" style="font-size:64px; width:64px; height:64px;"></span></div>' +
						'</div>' +
						'<div class="nbm-features-ribbon" style="border-radius:4px; border-left:4px solid #111827; background:#fafafa; border-top:1px solid #e5e7eb; border-right:1px solid #e5e7eb; border-bottom:1px solid #e5e7eb; padding:16px 24px; display:flex; justify-content:space-between; margin-bottom:30px;">' +
							'<div><span style="font-size:11px; text-transform:uppercase; color:#6b7280; display:block;">Atmosphere</span><strong style="font-family:Georgia, serif; font-size:16px;">Artisanal Botanical</strong></div>' +
							'<div><span style="font-size:11px; text-transform:uppercase; color:#6b7280; display:block;">Seating Window</span><strong style="font-family:Georgia, serif; font-size:16px;">90 Minutes</strong></div>' +
							'<div><span style="font-size:11px; text-transform:uppercase; color:#6b7280; display:block;">Curated By</span><strong style="font-family:Georgia, serif; font-size:16px;">Awarded Sommelier</strong></div>' +
						'</div>' +
						'<div class="nbm-single-layout" style="display:grid; grid-template-columns:1.8fr 1.2fr; gap:36px;">' +
							'<div style="font-family:Georgia, serif; line-height:1.75; font-size:15px; color:#374151;">' +
								'<h2 style="font-size:22px; border-bottom:2px solid #111827; padding-bottom:8px; margin-top:0;">The Story & Philosophy</h2>' +
								'<p>A lush glass-enclosed sanctuary in the heart of the metropolis. Each cocktail incorporates fresh botanicals harvested daily from our rooftop greenhouse, accompanied by artisanal charcuterie.</p>' +
							'</div>' +
							'<div>' +
								'<div style="border:2px solid #111827; border-radius:4px; padding:24px; background:#ffffff; box-shadow:6px 6px 0px rgba(17,24,39,0.06);">' +
									'<h3 style="font-family:Georgia, serif; margin:0 0 10px; font-size:18px;">Reserve A Table</h3>' +
									'<p style="font-size:12.5px; color:#6b7280; margin-bottom:14px;">Select your preferred time slot and party size.</p>' +
									'<button type="button" class="nbm-btn" style="width:100%; background:#111827 !important; color:#ffffff !important; font-family:Georgia, serif; font-size:14px; text-transform:uppercase; letter-spacing:1px; padding:12px; border:none; cursor:pointer;">Request Reservation &rarr;</button>' +
								'</div>' +
							'</div>' +
						'</div>' +
					'</div>';
				}

				if (layout === 'tabbed_navigator') {
					return '<div class="nbm-single-listing nbm-single-layout-tabbed_navigator" style="background:#ffffff; padding:20px; border-radius:10px;">' +
						'<div class="nbm-single-header" style="margin-bottom:18px;">' +
							'<span class="nbm-badge nbm-badge-bar">Bar & Lounge</span>' +
							'<h1 class="nbm-single-title" style="font-size:30px; margin:6px 0;">Grand Regency Lounge & Bar</h1>' +
							'<span style="color:#64748b; font-size:13px;">📍 Bayside Boulevard • ⏱ Open 4:00 PM</span>' +
						'</div>' +
						'<div class="nbm-tab-navbar" style="margin-bottom:20px;">' +
							'<button type="button" class="nbm-tab-nav-btn nbm-preview-mock-tab active" data-mock-tab="overview">📋 Overview</button>' +
							'<button type="button" class="nbm-tab-nav-btn nbm-preview-mock-tab" data-mock-tab="gallery">📸 Photo Gallery</button>' +
							'<button type="button" class="nbm-tab-nav-btn nbm-preview-mock-tab" data-mock-tab="hours">⏱ Operating Hours</button>' +
							'<button type="button" class="nbm-tab-nav-btn nbm-preview-mock-tab" data-mock-tab="amenities">✨ Features & Rules</button>' +
							'<button type="button" class="nbm-tab-nav-btn nbm-preview-mock-tab nbm-tab-reserve-btn" data-mock-tab="reserve">📅 Reserve Table</button>' +
						'</div>' +
						'<div id="nbm-mock-pane-overview" class="nbm-mock-pane" style="display:block;">' +
							'<div class="nbm-features-ribbon" style="margin-bottom:20px;">' +
								'<div class="nbm-ribbon-item"><span class="nbm-r-label">Price Range</span><span class="nbm-r-val">$$$</span></div>' +
								'<div class="nbm-ribbon-item"><span class="nbm-r-label">Max Party</span><span class="nbm-r-val">8 Guests</span></div>' +
								'<div class="nbm-ribbon-item"><span class="nbm-r-label">Slot Duration</span><span class="nbm-r-val">90 Mins</span></div>' +
							'</div>' +
							'<div class="nbm-content-block" style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:20px;">' +
								'<h3 style="margin:0 0 10px;">About This Venue</h3>' +
								'<p style="color:#475569; line-height:1.6; margin:0;">Tabbed navigation allows mobile visitors to tap directly into photos, hours, or reservations without excessive scrolling. Perfect for mobile responsiveness!</p>' +
							'</div>' +
						'</div>' +
						'<div id="nbm-mock-pane-gallery" class="nbm-mock-pane" style="display:none;">' +
							'<div style="height:340px; background:#e2e8f0; border-radius:10px; display:flex; align-items:center; justify-content:center; color:#94a3b8; font-size:16px;">' +
								'<span class="dashicons dashicons-format-gallery" style="font-size:48px; width:48px; height:48px; margin-right:10px;"></span> 📸 High Resolution Venue Gallery Photos' +
							'</div>' +
						'</div>' +
						'<div id="nbm-mock-pane-hours" class="nbm-mock-pane" style="display:none;">' +
							'<div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:20px;">' +
								'<h3 style="margin:0 0 12px;">Operating Hours Schedule</h3>' +
								'<p><strong>Monday – Friday:</strong> 4:00 PM – 1:00 AM</p>' +
								'<p><strong>Saturday – Sunday:</strong> 2:00 PM – 2:00 AM</p>' +
							'</div>' +
						'</div>' +
						'<div id="nbm-mock-pane-amenities" class="nbm-mock-pane" style="display:none;">' +
							'<div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:20px;">' +
								'<h3 style="margin:0 0 12px;">Amenities & Policies</h3>' +
								'<div style="display:flex; gap:10px; flex-wrap:wrap;">' +
									'<span class="nbm-amenity-badge"><span class="nbm-check-icon">✓</span> Free High-speed WiFi</span>' +
									'<span class="nbm-amenity-badge"><span class="nbm-check-icon">✓</span> Outdoor Patio</span>' +
								'</div>' +
							'</div>' +
						'</div>' +
						'<div id="nbm-mock-pane-reserve" class="nbm-mock-pane" style="display:none;">' +
							'<div style="background:#f0fdf4; border:2px solid #10b981; border-radius:12px; padding:24px;">' +
								'<h3 style="margin:0 0 10px; color:#065f46;">📅 Book Your Table Now</h3>' +
								'<p style="color:#047857; margin-bottom:14px;">Instant reservation form with zero scroll friction on mobile devices.</p>' +
								'<button type="button" class="nbm-btn" style="background:#10b981 !important; color:#fff !important; width:100%; padding:12px; font-weight:700; border:none; border-radius:6px;">Confirm Reservation Request</button>' +
							'</div>' +
						'</div>' +
					'</div>';
				}

				// Default: Classic Hero
				return '<div class="nbm-single-listing nbm-single-layout-classic_hero" style="background:#ffffff; padding:20px; border-radius:10px;">' +
					'<div class="nbm-single-header" style="margin-bottom:18px;">' +
						'<span class="nbm-badge nbm-badge-bar">Bar & Lounge</span>' +
						'<h1 class="nbm-single-title" style="font-size:32px; margin:6px 0;">The Metropolitan Grand Club</h1>' +
						'<span style="color:#64748b; font-size:13px;">📍 Downtown Central Plaza • ⏱ Open Today</span>' +
					'</div>' +
					'<div class="nbm-gallery-showcase" style="display:grid; grid-template-columns:2fr 1fr; gap:12px; margin-bottom:24px;">' +
						'<div class="nbm-main-image" style="height:320px; background:#f1f5f9; border-radius:10px; display:flex; align-items:center; justify-content:center; color:#94a3b8;"><span class="dashicons dashicons-format-image" style="font-size:48px; width:48px; height:48px;"></span></div>' +
						'<div style="display:grid; grid-template-rows:1fr 1fr; gap:10px;">' +
							'<div style="background:#f1f5f9; border-radius:8px; display:flex; align-items:center; justify-content:center; color:#94a3b8;"><span class="dashicons dashicons-camera"></span></div>' +
							'<div style="background:#f1f5f9; border-radius:8px; display:flex; align-items:center; justify-content:center; color:#94a3b8;"><span class="dashicons dashicons-camera"></span></div>' +
						'</div>' +
					'</div>' +
					'<div class="nbm-single-layout" style="display:grid; grid-template-columns:1.8fr 1.2fr; gap:30px;">' +
						'<div>' +
							'<div class="nbm-features-ribbon" style="margin-bottom:20px;">' +
								'<div class="nbm-ribbon-item"><span class="nbm-r-label">Price Range</span><span class="nbm-r-val">$$</span></div>' +
								'<div class="nbm-ribbon-item"><span class="nbm-r-label">Max Party</span><span class="nbm-r-val">6 Guests</span></div>' +
								'<div class="nbm-ribbon-item"><span class="nbm-r-label">Table Duration</span><span class="nbm-r-val">90 Mins</span></div>' +
							'</div>' +
							'<div class="nbm-content-block" style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:20px; margin-bottom:20px;">' +
								'<h3 style="margin:0 0 10px;">About This Venue</h3>' +
								'<p style="color:#475569; line-height:1.6; margin:0;">Handcrafted cocktails, ambient live music, and exceptional culinary creations prepared by award-winning chefs.</p>' +
							'</div>' +
						'</div>' +
						'<div>' +
							'<div style="background:#ffffff; border:1px solid #cbd5e1; border-radius:12px; padding:20px; box-shadow:0 8px 24px rgba(0,0,0,0.06);">' +
								'<h3 style="margin:0 0 12px; font-size:17px; color:#1e40af;">📅 Reserve Table</h3>' +
								'<div style="margin-bottom:12px;"><label style="font-size:12px; font-weight:700;">Date</label><input type="date" value="2026-10-18" class="regular-text" style="width:100%; padding:6px;" readonly /></div>' +
								'<button type="button" class="nbm-btn nbm-btn-primary" style="width:100%; padding:10px; font-weight:700;">Request Reservation &rarr;</button>' +
							'</div>' +
						'</div>' +
					'</div>' +
				'</div>';
			}

			function refreshSingleLivePreview() {
				var layout = $('input[name="nbm_single_layout_choice"]:checked').val() || currentSingleLayout;
				$('#nbm-single-preview-stage').html(renderSingleMockup(layout));
			}

			// Watch Layout Card selection
			$('.nbm-layout-card').on('click', function() {
				var $card = $(this);
				var layout = $card.data('layout');
				$card.find('input[type="radio"]').prop('checked', true);
				currentSingleLayout = layout;
				refreshSingleLivePreview();
			});

			// Single Device Switcher
			$('.nbm-single-device-btn').on('click', function(e) {
				e.preventDefault();
				$('.nbm-single-device-btn').removeClass('active');
				$(this).addClass('active');
				var width = $(this).data('width');
				$('#nbm-single-preview-wrapper').css('max-width', width);
			});

			// Interactive mock tabs in Design 5 preview
			$(document).on('click', '.nbm-preview-mock-tab', function(e) {
				e.preventDefault();
				var tab = $(this).data('mock-tab');
				$('.nbm-preview-mock-tab').removeClass('active');
				$(this).addClass('active');

				$('.nbm-mock-pane').hide();
				$('#nbm-mock-pane-' + tab).fadeIn(200);
			});

			// Save Single Layout AJAX Action
			$('#nbm-save-single-layout-btn, .nbm-trigger-save-single').on('click', function(e) {
				e.preventDefault();
				var $btn = $(this);
				var originalText = $btn.html();
				var $feedback = $('#nbm-preset-save-feedback');
				var chosenLayout = $('input[name="nbm_single_layout_choice"]:checked').val() || 'classic_hero';

				$btn.prop('disabled', true).text('Saving layout...');
				$feedback.hide().empty();

				$.ajax({
					url: nbm_admin_vars.ajax_url,
					type: 'POST',
					data: {
						action: 'nbm_admin_save_single_layout',
						nonce: nbm_admin_vars.nonce,
						single_details_design: chosenLayout
					},
					success: function(res) {
						$btn.prop('disabled', false).html(originalText);
						if (res.success) {
							$feedback.html('<div class="notice notice-success inline" style="margin:0; padding:10px 14px; border-left-color:#10b981; font-size:14px;"><p><strong>' + res.data.message + '</strong></p></div>').fadeIn();
							$('html, body').animate({ scrollTop: 0 }, 300);
						} else {
							$feedback.html('<div class="notice notice-error inline" style="margin:0; padding:10px 14px; border-left-color:#ef4444;"><p>' + res.data.message + '</p></div>').fadeIn();
						}
					},
					error: function() {
						$btn.prop('disabled', false).html(originalText);
						$feedback.html('<div class="notice notice-error inline" style="margin:0; padding:10px 14px; border-left-color:#ef4444;"><p>Server communication error while saving single page layout.</p></div>').fadeIn();
					}
				});
			});

			refreshSingleLivePreview();
		}

	});

})(jQuery);
