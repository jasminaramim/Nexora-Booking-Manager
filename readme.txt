=== Nexora Booking Manager ===
Contributors: jasmin, nexoradev
Donate link: https://jasmin-v01.vercel.app/
Tags: hotel booking, bar reservation, table booking, elementor booking, appointment
Requires at least: 5.8
Tested up to: 6.7
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Professional Hotel and Bar reservation management system with request-based bookings, availability conflict prevention, and Elementor widgets.

== Description ==

**Nexora Booking Manager** is a complete, standalone WordPress plugin designed for hotels, boutique resorts, bars, lounges, and restaurant reservations. 

It empowers administrators to manage properties and venues just like standard WordPress posts, while providing visitors with a seamless, intuitive request-based reservation experience.

### Key Features

* **Custom Post Types:** Full support for Hotel listings (`nbm_hotel`) and Bar listings (`nbm_bar`).
* **Request-Based Booking Workflow:** Submissions start as **Pending** so administrators can verify details before confirming or rejecting.
* **Server-Side Availability Engine:** Prevents double bookings and overbooking by validating date ranges, hotel room capacity, bar operating hours, and concurrent table capacity.
* **Elementor Integration:** 4 dedicated Elementor widgets (Hotel Grid, Bar Grid, Booking Form, Search & Filter) that only load when Elementor is active. Works 100% standalone if Elementor is not installed.
* **Shortcode Support:** Display grids, single forms, search bars, and guest booking status lookups on any theme or page.
* **Robust Admin Dashboard:** Real-time statistics, pending request alerts, search, status filters, private staff audit notes, and CSV export.
* **Automatic Email Notifications:** Beautiful HTML emails sent via `wp_mail()` for new booking alerts, guest pending acknowledgements, and status change notices (Confirm / Reject / Cancel).
* **Security & Spam Protection:** CSRF nonces, strict capability checks, rate-limiting, and an invisible anti-spam honeypot.
* **Safe Uninstall Option:** Preserves your bookings and listings by default, with an explicit opt-in setting if you wish to wipe database tables upon uninstall.

== Installation ==

1. Upload `nexora-booking-manager.zip` via **WordPress Dashboard → Plugins → Add New → Upload Plugin**.
2. Click **Install Now** and then **Activate Plugin**.
3. Go to the new **Nexora Booking** menu in your WordPress admin menu.
4. Add your Hotels and Bars with photos, pricing, amenities, and capacity rules.
5. Place listings on your pages using the provided Shortcodes or Elementor widgets.

== Shortcodes ==

* `[nbm_hotels columns="3" per_page="9"]` — Displays hotel cards in a responsive grid.
* `[nbm_bars columns="3" per_page="6"]` — Displays bar/venue cards in a responsive grid.
* `[nbm_booking_form type="hotel"]` or `[nbm_booking_form type="hotel" id="123"]` — Interactive booking form.
* `[nbm_booking_search type="all"]` — Real-time search and filter bar.
* `[nbm_my_bookings]` — Guest booking reference lookup tool.

== Frequently Asked Questions ==

= Does this plugin require Elementor to work? =
No. Nexora Booking Manager is completely standalone. If Elementor is installed, 4 specialized widgets automatically become available in your Elementor editor.

= Are bookings confirmed immediately? =
No. By design, bookings start in the "Pending" state. An authorized administrator reviews the request in the WordPress dashboard and selects "Confirm" or "Reject". The guest receives an automatic email notification upon review.

= How does conflict prevention work? =
For hotels, the engine checks overlapping check-in/out dates against the property's configured total room inventory. For bars, it verifies the venue's operating hours for that day of the week, minimum notice hours, slot duration, and seating capacity. An additional recheck runs before an administrator confirms a booking.

== Changelog ==

= 1.0.0 =
* Initial public release with Hotels, Bars, request workflow, availability engine, email notifications, shortcodes, and Elementor integration.
