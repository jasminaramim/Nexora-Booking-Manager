/**
 * Nexora Booking Manager — Frontend Interactive Script
 */

(function($) {
	'use strict';

	$(document).ready(function() {

		// Calculate Hotel Nights & Estimated Price
		function updateHotelPriceEstimate($form) {
			var $container = $form.closest('.nbm-hotel-form-box');
			var $summaryBox = $container.find('.nbm-pricing-summary');
			var $nightsLabel = $container.find('.nbm-calc-nights');
			var $totalLabel = $container.find('.nbm-calc-total');

			var checkInVal = $form.find('.nbm-date-checkin').val();
			var checkOutVal = $form.find('.nbm-date-checkout').val();

			var pricePerNight = 0;
			var $selector = $form.find('.nbm-hotel-selector');
			if ($selector.length && $selector.val()) {
				pricePerNight = parseFloat($selector.find(':selected').data('price')) || 0;
			} else {
				var $hiddenId = $form.find('.nbm-hotel-id-input');
				if ($hiddenId.length) {
					pricePerNight = parseFloat($hiddenId.data('price')) || 0;
				}
			}

			if (checkInVal && checkOutVal) {
				var dtIn = new Date(checkInVal);
				var dtOut = new Date(checkOutVal);
				var diffTime = dtOut - dtIn;
				var nights = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

				if (nights > 0 && pricePerNight > 0) {
					var total = (nights * pricePerNight).toFixed(2);
					$nightsLabel.text(nights + ' ' + (nights === 1 ? 'night' : 'nights'));
					$totalLabel.text((nbm_vars.currency_symbol || '$') + total);
					$summaryBox.slideDown(200);
				} else {
					$summaryBox.slideUp(200);
				}
			} else {
				$summaryBox.slideUp(200);
			}
		}

		// Hotel selection change updates max guests & estimates
		$(document).on('change', '.nbm-hotel-selector', function() {
			var $form = $(this).closest('form');
			var maxGuests = parseInt($(this).find(':selected').data('max-guests')) || 4;
			var $guestSelect = $form.find('.nbm-guest-count');

			$guestSelect.empty();
			for (var i = 1; i <= maxGuests; i++) {
				$guestSelect.append('<option value="' + i + '">' + i + (i === 1 ? ' Guest' : ' Guests') + '</option>');
			}

			updateHotelPriceEstimate($form);
		});

		// Date input change
		$(document).on('change', '.nbm-date-checkin, .nbm-date-checkout', function() {
			var $form = $(this).closest('form');
			updateHotelPriceEstimate($form);
		});

		// Trigger initial calculation if values present
		$('.nbm-ajax-booking-form').each(function() {
			updateHotelPriceEstimate($(this));
		});

		// AJAX Booking Submission Handler
		$(document).on('submit', '.nbm-ajax-booking-form', function(e) {
			e.preventDefault();

			var $form = $(this);
			var $btn = $form.find('.nbm-submit-btn');
			var $btnText = $btn.find('.nbm-btn-text');
			var $spinner = $btn.find('.nbm-spinner');
			var $feedback = $form.find('.nbm-feedback-msg');

			var originalText = $btnText.text();
			$btn.prop('disabled', true);
			$spinner.show();
			$btnText.text(nbm_vars.i18n.processing || 'Processing...');
			$feedback.removeClass('nbm-alert-success nbm-alert-error').hide().empty();

			var formData = $form.serialize();

			$.ajax({
				url: nbm_vars.ajax_url,
				type: 'POST',
				data: formData,
				success: function(res) {
					$btn.prop('disabled', false);
					$spinner.hide();
					$btnText.text(originalText);

					if (res.success) {
						$feedback.addClass('nbm-alert-success')
							.html('<strong>✓ ' + (nbm_vars.i18n.success_msg || 'Success!') + '</strong><br>' + res.data.message)
							.fadeIn(300);

						$form[0].reset();
						$form.find('.nbm-pricing-summary').slideUp(200);

						// Scroll smoothly to feedback message
						$('html, body').animate({
							scrollTop: $feedback.offset().top - 80
						}, 400);
					} else {
						$feedback.addClass('nbm-alert-error')
							.html('<strong>✕ ' + (nbm_vars.i18n.error_msg || 'Error') + ':</strong> ' + (res.data.message || 'Please check your inputs and try again.'))
							.fadeIn(300);
					}
				},
				error: function() {
					$btn.prop('disabled', false);
					$spinner.hide();
					$btnText.text(originalText);

					$feedback.addClass('nbm-alert-error')
						.html('<strong>✕ Server Error:</strong> Could not connect to the booking service. Please try again.')
						.fadeIn(300);
				}
			});
		});

		// Guest Booking Status Lookup Form
		$(document).on('submit', '.nbm-lookup-form', function(e) {
			e.preventDefault();

			var $form = $(this);
			var $btn = $form.find('.nbm-btn-track');
			var $spinner = $btn.find('.nbm-spinner');
			var $result = $form.siblings('.nbm-lookup-result');

			$btn.prop('disabled', true);
			$spinner.show();
			$result.hide().empty();

			$.ajax({
				url: nbm_vars.ajax_url,
				type: 'POST',
				data: {
					action: 'nbm_lookup_booking',
					nonce: nbm_vars.nonce,
					booking_code: $form.find('input[name="booking_code"]').val(),
					guest_email: $form.find('input[name="guest_email"]').val()
				},
				success: function(res) {
					$btn.prop('disabled', false);
					$spinner.hide();

					if (res.success) {
						var d = res.data;
						var statusClass = 'nbm-status-' + d.status_raw;
						var html = '<div class="nbm-panel" style="border: 2px solid #e2e8f0; border-radius: 10px; padding: 20px;">' +
							'<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">' +
								'<h4 style="margin:0; font-size:17px; font-weight:700;">' + d.listing_title + ' (' + d.listing_type + ')</h4>' +
								'<span class="nbm-status-badge ' + statusClass + '">' + d.status + '</span>' +
							'</div>' +
							'<p style="margin:4px 0; font-size:13.5px;"><strong>Reference:</strong> ' + d.booking_code + '</p>' +
							'<p style="margin:4px 0; font-size:13.5px;"><strong>Schedule:</strong> ' + d.schedule + '</p>' +
							'<p style="margin:4px 0; font-size:13.5px;"><strong>Party Size:</strong> ' + d.guest_count + ' Guest(s)</p>' +
							'<p style="margin:4px 0; font-size:12px; color:#64748b;"><strong>Submitted:</strong> ' + d.created_at + '</p>' +
						'</div>';

						$result.html(html).fadeIn(300);
					} else {
						$result.html('<div class="nbm-feedback-msg nbm-alert-error">' + res.data.message + '</div>').fadeIn(300);
					}
				},
				error: function() {
					$btn.prop('disabled', false);
					$spinner.hide();
					$result.html('<div class="nbm-feedback-msg nbm-alert-error">Server communication error.</div>').fadeIn(300);
				}
			});
		});

		// Single Listing Tab Navigator (Design 5)
		$(document).on('click', '.nbm-tab-nav-btn', function(e) {
			e.preventDefault();
			var $btn = $(this);
			var targetTab = $btn.data('tab');
			var $wrap = $btn.closest('.nbm-single-layout-tabbed_navigator');

			$wrap.find('.nbm-tab-nav-btn').removeClass('active');
			$btn.addClass('active');

			// Hide all tab panes in this container
			$wrap.find('.nbm-tab-pane').removeClass('active').hide();

			// Show matching tab pane
			var $targetPane = $wrap.find('#nbm-pane-' + targetTab);
			if ($targetPane.length) {
				$targetPane.addClass('active').fadeIn(200);
			} else {
				// Fallback for alternate ID mappings
				if (targetTab === 'overview') {
					$wrap.find('#nbm-tab-overview, #nbm-tab-overview-ribbon').addClass('active').fadeIn(200);
				} else if (targetTab === 'gallery') {
					$wrap.find('#nbm-tab-gallery').addClass('active').fadeIn(200);
				} else if (targetTab === 'hours' || targetTab === 'policies') {
					$wrap.find('#nbm-tab-hours, #nbm-tab-policies').addClass('active').fadeIn(200);
				} else if (targetTab === 'amenities') {
					$wrap.find('#nbm-tab-amenities, #nbm-tab-amenities-rules').addClass('active').fadeIn(200);
				} else if (targetTab === 'reserve') {
					$wrap.find('#nbm-tab-reserve').addClass('active').fadeIn(200);
				}
			}
		});

	});

})(jQuery);
