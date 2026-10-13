# Nexora Booking Manager

> **Premium Hotel & Bar Reservation Management System for WordPress**  
> Developed by **Jasmin** ([Portfolio](https://jasmin-v01.vercel.app/) • [GitHub](https://github.com/jasminaramim))

[![WordPress Version](https://img.shields.io/badge/WordPress-6.0%2B-blue.svg)](https://wordpress.org)
[![PHP Version](https://img.shields.io/badge/PHP-7.4%20|%208.0%20|%208.1%20|%208.2-purple.svg)](https://php.net)
[![Elementor Compatible](https://img.shields.io/badge/Elementor-Ready-red.svg)](https://elementor.com)
[![License](https://img.shields.io/badge/License-GPLv2%2B-green.svg)](https://www.gnu.org/licenses/gpl-2.0.html)

---

## ✨ Features Overview

**Nexora Booking Manager** is a high-performance, standalone WordPress booking plugin designed for hotels, luxury resorts, boutique lounges, and bars. It works seamlessly out of the box with or without page builders, and includes native Elementor widgets.

### 🏨 1. Dedicated Hotel & Bar Management
- Independent Custom Post Types for **Hotels** (`nbm_hotel`) and **Bars** (`nbm_bar`).
- Comprehensive metadata: Room types, table categories, guest capacity, pricing, amenities, address, and interactive opening hours.

### 📅 2. Real-Time Reservation Engine
- Instant AJAX validation preventing double-bookings.
- Automatic capacity calculation and availability verification.
- Two-way email notifications for both guests (booking receipt & status changes) and administrators.
- Status workflow: `Pending` ➔ `Confirmed` / `Rejected` / `Cancelled`.

### 🎴 3. Seven (7) Loop Card Presets
Choose and customize stunning loop cards for archive pages, grids, and shortcodes:
1. **Classic Luxe** — Timeless hotel luxury card with rating badge.
2. **Modern Glass** — Sleek dark glassmorphism aesthetic.
3. **Minimalist Clean** — Ultra-modern Scandinavian typography.
4. **Dark Velvet Club** — Rich nightlife & cocktail lounge ambience.
5. **Neon Cyberpunk** — Glowing electric accents for modern bars & nightclubs.
6. **Vintage Speakeasy** — Warm bourbon, copper, and rustic tones.
7. **Bistro Horizontal** — Executive landscape card layout.

### 📄 4. Five (5) Single Details Page Layouts
Select your preferred venue details layout in the admin dashboard with instant live preview:
1. **Classic Luxury Hero** — Wide hero banner, 3-column media showcase, content & sticky reservation card.
2. **50/50 Split Screen** — Modern boutique dual-pane layout with persistent sticky media pane.
3. **Fullscreen Cinematic VIP** — 100vh immersive backdrop with glassmorphic reservation card.
4. **Editorial Magazine** — Elegant typography, masonry photo collage, and bottom floating reservation bar.
5. **Interactive Tabbed Navigator** — Structured multi-tab experience (Overview, Amenities, Menu/Drinks, Policies, Reviews).

### 🎨 5. Admin Design Studio & Live Preview
- Switch between **Desktop**, **Tablet**, and **Mobile** viewports directly inside WordPress admin.
- Customize colors, card layouts, items per page, and columns.
- **One-Click Save**: Persist default card styles and single page layouts globally via AJAX.

### 🧩 6. Elementor Widgets & Shortcodes
- Shortcode generator included:
  - `[nbm_bars]` & `[nbm_hotels]` with preset, column, color, and pagination parameters.
  - `[nbm_booking_form post_id="..."]` for standalone booking forms.
  - `[nbm_search]` for dynamic availability searches.
- Dedicated Elementor widgets for listing grids and single reservation modules.

---

## 🚀 Installation

1. Download or clone this repository:
   ```bash
   git clone https://github.com/jasminaramim/Nexora-Booking-Manager.git
   ```
2. Upload the `nexora-booking-manager` folder to your WordPress plugins directory (`wp-content/plugins/`).
3. Activate the plugin through the **Plugins** menu in WordPress.
4. Go to **Nexora Booking -> Settings** to configure email notifications, currency, and defaults.
5. Explore **Nexora Booking -> Shortcodes & Presets** to preview cards and configure your single details page design.

---

## 💻 Tech Stack
- **Backend:** PHP 7.4 - 8.2+, WordPress Core API, Custom Database Tables (`wp_nbm_bookings`).
- **Frontend:** Vanilla JavaScript (ES6+), Modern Responsive CSS3 (Flexbox/Grid), Glassmorphism, Micro-animations.
- **Builders:** Native Elementor Addon integration.

---

## 👤 Author & Credits

- **Author:** Jasmin
- **Team:** [Nexora Development Team](https://github.com/jasminaramim)
- **Contributors:** jasmin, nexoradev
- **License:** GNU General Public License v2 or later
