<?php
/**
 * Admin View: Visual Shortcode Generator & Interactive Card Presets & Single Page Studio.
 *
 * @package Nexora_Booking_Manager
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$default_bar_preset    = NBM_Settings::get( 'bar_default_card_preset', 'modern' );
$default_bar_cols      = NBM_Settings::get( 'bar_default_columns', 3 );
$default_bar_accent    = NBM_Settings::get( 'bar_default_accent_color', '#1e40af' );
$default_bar_bg        = NBM_Settings::get( 'bar_default_card_bg', '#ffffff' );
$default_bar_btn       = NBM_Settings::get( 'bar_default_btn_text', '' );
$current_single_layout = NBM_Settings::get( 'single_details_design', 'classic_hero' );
?>
<div class="wrap nbm-admin-wrap">
	<div class="nbm-admin-header" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px;">
		<div class="nbm-admin-title-area">
			<h1 class="wp-heading-inline"><?php esc_html_e( 'Design & Presets Studio', 'nexora-booking-manager' ); ?></h1>
			<p class="nbm-admin-subtitle">
				<?php esc_html_e( 'By Jasmin — Customize card loop presets, configure responsive single details page layouts, and preview live in real-time.', 'nexora-booking-manager' ); ?>
			</p>
		</div>
	</div>

	<!-- Top Navigation Tabs -->
	<nav class="nav-tab-wrapper nbm-studio-nav-tabs" style="margin: 18px 0 20px;">
		<a href="#cards" class="nav-tab nav-tab-active nbm-top-tab-btn" data-target="cards">
			<span class="dashicons dashicons-grid-view" style="margin-top:3px;"></span>
			<?php esc_html_e( '1. Listing Cards Presets & Shortcodes', 'nexora-booking-manager' ); ?>
		</a>
		<a href="#single" class="nav-tab nbm-top-tab-btn" data-target="single">
			<span class="dashicons dashicons-layout" style="margin-top:3px;"></span>
			<?php esc_html_e( '2. Single Details Page Design (৫টি লেআউট)', 'nexora-booking-manager' ); ?>
		</a>
	</nav>

	<!-- Global Save Feedback Banner -->
	<div id="nbm-preset-save-feedback" style="display:none; margin: 15px 0;"></div>

	<!-- ===================================================================== -->
	<!-- TAB 1: LISTING CARDS PRESETS & SHORTCODES -->
	<!-- ===================================================================== -->
	<div id="nbm-tab-section-cards" class="nbm-tab-content-panel">
		<div style="display:flex; justify-content:flex-end; margin-bottom:12px;">
			<button type="button" id="nbm-save-preset-defaults-btn" class="button button-primary button-hero nbm-trigger-save-cards" style="font-weight:700; display:flex; align-items:center; gap:8px;">
				<span class="dashicons dashicons-saved" style="margin-top:2px;"></span>
				<span><?php esc_html_e( 'Save Cards Preset Defaults (সংরক্ষণ করুন)', 'nexora-booking-manager' ); ?></span>
			</button>
		</div>

		<div class="nbm-generator-grid" style="display: grid; grid-template-columns: 420px 1fr; gap: 24px; align-items: start;">
			
			<!-- Left Column: Controls & Shortcode Output -->
			<div class="nbm-generator-controls">
				<div class="nbm-panel" style="background:#ffffff; border-radius:10px; border:1px solid #cbd5e1; box-shadow:0 2px 8px rgba(0,0,0,0.04);">
					<h2 style="font-size:16px; margin:0 0 16px; border-bottom:1px solid #f1f5f9; padding-bottom:10px; color:#0f172a;">
						⚙ <?php esc_html_e( 'Configure Card Preset & Styles', 'nexora-booking-manager' ); ?>
					</h2>
					
					<form id="nbm-shortcode-builder-form">
						
						<!-- Listing Type -->
						<div class="nbm-field-group" style="margin-bottom: 16px;">
							<label style="font-weight:700; display:block; margin-bottom:6px; font-size:13px; color:#334155;">
								<?php esc_html_e( 'Listing Entity:', 'nexora-booking-manager' ); ?>
							</label>
							<select id="nbm-gen-type" style="width:100%; padding:8px 12px; font-size:13.5px; border-radius:6px;">
								<option value="bars" selected><?php esc_html_e( '🍸 Bars & Lounges ([nbm_bars])', 'nexora-booking-manager' ); ?></option>
								<option value="hotels"><?php esc_html_e( '🏨 Hotels & Resorts ([nbm_hotels])', 'nexora-booking-manager' ); ?></option>
							</select>
						</div>

						<!-- Card Preset -->
						<div class="nbm-field-group" style="margin-bottom: 16px;">
							<label style="font-weight:700; display:block; margin-bottom:6px; font-size:13px; color:#334155;">
								<?php esc_html_e( 'Card Design Preset (Loop Layout):', 'nexora-booking-manager' ); ?>
							</label>
							<select id="nbm-gen-preset" style="width:100%; padding:8px 12px; font-size:13.5px; border-radius:6px; font-weight:600;">
								<option value="modern" <?php selected( $default_bar_preset, 'modern' ); ?>><?php esc_html_e( '🌟 Preset 1: Modern Sophisticate (Clean & Elevated)', 'nexora-booking-manager' ); ?></option>
								<option value="luxury" <?php selected( $default_bar_preset, 'luxury' ); ?>><?php esc_html_e( '👑 Preset 2: Luxury VIP & Nightlife (Dark & Gold Glow)', 'nexora-booking-manager' ); ?></option>
								<option value="glassmorphism" <?php selected( $default_bar_preset, 'glassmorphism' ); ?>><?php esc_html_e( '🔮 Preset 3: Glassmorphism Overlay (Frosted Full-Bleed)', 'nexora-booking-manager' ); ?></option>
								<option value="minimal" <?php selected( $default_bar_preset, 'minimal' ); ?>><?php esc_html_e( '📰 Preset 4: Minimalist Editorial (Bistro & Specs)', 'nexora-booking-manager' ); ?></option>
								<option value="neon" <?php selected( $default_bar_preset, 'neon' ); ?>><?php esc_html_e( '⚡ Preset 5: Neon Cyberpunk & Electric Club (Cyan/Violet Glow)', 'nexora-booking-manager' ); ?></option>
								<option value="vintage" <?php selected( $default_bar_preset, 'vintage' ); ?>><?php esc_html_e( '🥃 Preset 6: Vintage Speakeasy & Bourbon Lounge (Antique Brass)', 'nexora-booking-manager' ); ?></option>
								<option value="bistro_horizontal" <?php selected( $default_bar_preset, 'bistro_horizontal' ); ?>><?php esc_html_e( '🏢 Preset 7: Horizontal Executive Suite (Wide Landscape Split)', 'nexora-booking-manager' ); ?></option>
							</select>
						</div>

						<!-- Columns & Per Page -->
						<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 16px;">
							<div>
								<label style="font-weight:700; display:block; margin-bottom:6px; font-size:13px; color:#334155;">
									<?php esc_html_e( 'Columns per Row:', 'nexora-booking-manager' ); ?>
								</label>
								<select id="nbm-gen-columns" style="width:100%; padding:8px 10px; font-size:13px; border-radius:6px;">
									<option value="1" <?php selected( $default_bar_cols, 1 ); ?>><?php esc_html_e( '1 Column (Full Width)', 'nexora-booking-manager' ); ?></option>
									<option value="2" <?php selected( $default_bar_cols, 2 ); ?>><?php esc_html_e( '2 Columns', 'nexora-booking-manager' ); ?></option>
									<option value="3" <?php selected( $default_bar_cols, 3 ); ?>><?php esc_html_e( '3 Columns (Standard)', 'nexora-booking-manager' ); ?></option>
									<option value="4" <?php selected( $default_bar_cols, 4 ); ?>><?php esc_html_e( '4 Columns (Compact)', 'nexora-booking-manager' ); ?></option>
								</select>
							</div>
							<div>
								<label style="font-weight:700; display:block; margin-bottom:6px; font-size:13px; color:#334155;">
									<?php esc_html_e( 'Cards per Page:', 'nexora-booking-manager' ); ?>
								</label>
								<input type="number" id="nbm-gen-perpage" value="6" min="1" max="48" style="width:100%; padding:7px 10px; font-size:13px; border-radius:6px;" />
							</div>
						</div>

						<!-- Color Customization -->
						<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 16px;">
							<div>
								<label style="font-weight:700; display:block; margin-bottom:6px; font-size:13px; color:#334155;">
									<?php esc_html_e( 'Accent / Button Color:', 'nexora-booking-manager' ); ?>
								</label>
								<input type="text" id="nbm-gen-accent" class="nbm-color-field" value="<?php echo esc_attr( $default_bar_accent ); ?>" />
							</div>
							<div>
								<label style="font-weight:700; display:block; margin-bottom:6px; font-size:13px; color:#334155;">
									<?php esc_html_e( 'Card Background:', 'nexora-booking-manager' ); ?>
								</label>
								<input type="text" id="nbm-gen-cardbg" class="nbm-color-field" value="<?php echo esc_attr( $default_bar_bg ); ?>" />
							</div>
						</div>

						<!-- Button Text & City Filter -->
						<div style="margin-bottom: 16px;">
							<label style="font-weight:700; display:block; margin-bottom:6px; font-size:13px; color:#334155;">
								<?php esc_html_e( 'Custom Button Label:', 'nexora-booking-manager' ); ?>
							</label>
							<input type="text" id="nbm-gen-btntext" value="<?php echo esc_attr( $default_bar_btn ); ?>" placeholder="<?php esc_attr_e( 'e.g. Reserve Table, VIP Access, Book Stay', 'nexora-booking-manager' ); ?>" style="width:100%; padding:8px 12px; font-size:13px; border-radius:6px;" />
						</div>

						<div style="margin-bottom: 16px;">
							<label style="font-weight:700; display:block; margin-bottom:6px; font-size:13px; color:#334155;">
								<?php esc_html_e( 'Filter by City (Optional):', 'nexora-booking-manager' ); ?>
							</label>
							<input type="text" id="nbm-gen-city" placeholder="<?php esc_attr_e( 'e.g. Miami, New York, London', 'nexora-booking-manager' ); ?>" style="width:100%; padding:8px 12px; font-size:13px; border-radius:6px;" />
						</div>

						<!-- Pagination -->
						<div style="margin-bottom: 18px;">
							<label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
								<input type="checkbox" id="nbm-gen-pagination" value="yes" checked />
								<span style="font-weight:600; font-size:13px; color:#334155;"><?php esc_html_e( 'Enable page numbers (pagination)', 'nexora-booking-manager' ); ?></span>
							</label>
						</div>

						<button type="button" id="nbm-save-preset-defaults-btn-side" class="button button-primary nbm-trigger-save-cards" style="width:100%; padding:8px 14px; font-size:14px; font-weight:700; justify-content:center; display:flex; align-items:center; gap:6px;">
							💾 <?php esc_html_e( 'Save as Site Defaults', 'nexora-booking-manager' ); ?>
						</button>

					</form>
				</div>

				<!-- Generated Shortcode Output Box -->
				<div class="nbm-panel" style="background: #0f172a; color: #ffffff; border-color: #1e293b; border-radius:10px; margin-top:20px; box-shadow:0 4px 14px rgba(0,0,0,0.2);">
					<h2 style="color:#38bdf8; border-color:#334155; margin:0 0 10px; font-size:15px;">
						📋 <?php esc_html_e( 'Generated Shortcode', 'nexora-booking-manager' ); ?>
					</h2>
					<p style="color:#94a3b8; font-size:12.5px; margin:0 0 10px; line-height:1.4;">
						<?php esc_html_e( 'Copy and paste anywhere in Elementor Shortcode widget, Gutenberg block, or page editor:', 'nexora-booking-manager' ); ?>
					</p>
					
					<div style="position: relative;">
						<textarea id="nbm-generated-shortcode" readonly rows="2" style="width:100%; background:#1e293b; color:#38bdf8; border:1px solid #475569; border-radius:6px; padding:10px; font-family:monospace; font-size:13px; font-weight:600; resize:none;"></textarea>
						
						<button type="button" id="nbm-copy-shortcode-btn" class="button button-secondary button-large" style="margin-top: 10px; width:100%; font-size:13.5px; font-weight:700; background:#334155; color:#ffffff; border-color:#475569;">
							📋 <?php esc_html_e( 'Copy Shortcode to Clipboard', 'nexora-booking-manager' ); ?>
						</button>
						<div id="nbm-copy-success-msg" style="display:none; color:#34d399; font-weight:700; margin-top:8px; text-align:center; font-size:13px;">✓ Copied to clipboard!</div>
					</div>
				</div>

			</div>

			<!-- Right Column: Interactive Live Preview Studio -->
			<div class="nbm-generator-showcase">
				<div class="nbm-panel" style="background:#ffffff; border-radius:10px; border:1px solid #cbd5e1; box-shadow:0 2px 8px rgba(0,0,0,0.04);">
					
					<!-- Live Preview Header Toolbar -->
					<div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; border-bottom:1px solid #f1f5f9; padding-bottom:14px; margin-bottom:16px;">
						<div>
							<h2 style="font-size:17px; margin:0; display:flex; align-items:center; gap:8px; color:#0f172a;">
								<span>👁 <?php esc_html_e( 'Live Card Preview (রিয়েল-টাইম লাইভ প্রিভিউ)', 'nexora-booking-manager' ); ?></span>
								<span class="nbm-live-indicator" style="background:#10b981; color:#fff; font-size:10.5px; font-weight:700; padding:2px 8px; border-radius:12px; letter-spacing:0.5px;">LIVE</span>
							</h2>
							<p style="margin:4px 0 0; font-size:12.5px; color:#64748b;">
								<?php esc_html_e( 'Preview instantly updates as you change preset, colors, columns, or buttons.', 'nexora-booking-manager' ); ?>
							</p>
						</div>

						<div style="display:flex; align-items:center; gap:8px;">
							<div class="button-group">
								<button type="button" class="button button-small nbm-view-mode-btn active" data-mode="grid"><?php esc_html_e( 'Grid View', 'nexora-booking-manager' ); ?></button>
								<button type="button" class="button button-small nbm-view-mode-btn" data-mode="single"><?php esc_html_e( 'Single Card', 'nexora-booking-manager' ); ?></button>
							</div>
							<div class="button-group">
								<button type="button" class="button button-small nbm-device-btn active" data-width="100%" title="<?php esc_attr_e( 'Desktop View', 'nexora-booking-manager' ); ?>">🖥️</button>
								<button type="button" class="button button-small nbm-device-btn" data-width="768px" title="<?php esc_attr_e( 'Tablet View (768px)', 'nexora-booking-manager' ); ?>">📱</button>
								<button type="button" class="button button-small nbm-device-btn" data-width="380px" title="<?php esc_attr_e( 'Mobile View (380px)', 'nexora-booking-manager' ); ?>">📲</button>
							</div>
						</div>
					</div>

					<!-- Quick Preset Navigation Chips -->
					<div class="nbm-preset-chips" style="display:flex; gap:6px; flex-wrap:wrap; margin-bottom:16px;">
						<span class="nbm-chip active" data-preset="modern">🌟 Modern</span>
						<span class="nbm-chip" data-preset="luxury">👑 Luxury VIP</span>
						<span class="nbm-chip" data-preset="glassmorphism">🔮 Glassmorphism</span>
						<span class="nbm-chip" data-preset="minimal">📰 Minimalist</span>
						<span class="nbm-chip" data-preset="neon">⚡ Neon Club</span>
						<span class="nbm-chip" data-preset="vintage">🥃 Vintage</span>
						<span class="nbm-chip" data-preset="bistro_horizontal">🏢 Horizontal</span>
					</div>

					<!-- Live Preview Canvas Stage -->
					<div id="nbm-preview-wrapper" style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:20px; min-height:480px; overflow-x:auto; transition:width 0.3s ease; margin:0 auto; box-sizing:border-box;">
						<div id="nbm-preview-stage" class="nbm-wrapper">
							<!-- Dynamically injected via JavaScript -->
						</div>
					</div>

				</div>
			</div>

		</div>
	</div>

	<!-- ===================================================================== -->
	<!-- TAB 2: SINGLE DETAILS PAGE DESIGN STUDIO (৫টি লেআউট) -->
	<!-- ===================================================================== -->
	<div id="nbm-tab-section-single" class="nbm-tab-content-panel" style="display:none;">
		<div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; margin-bottom:16px;">
			<div>
				<h2 style="font-size:18px; margin:0; color:#0f172a;">
					📄 <?php esc_html_e( 'Single Venue Details Page Studio (সিঙ্গেল ডিটেইলস পেজ ডিজাইন)', 'nexora-booking-manager' ); ?>
				</h2>
				<p style="margin:4px 0 0; color:#64748b; font-size:13px;">
					<?php esc_html_e( 'Select one of the 5 responsive layouts below, preview interactively, and click Save Changes to apply it to all single Hotel and Bar pages.', 'nexora-booking-manager' ); ?>
				</p>
			</div>
			<button type="button" id="nbm-save-single-layout-btn" class="button button-primary button-hero" style="font-weight:700; display:flex; align-items:center; gap:8px;">
				<span class="dashicons dashicons-saved" style="margin-top:2px;"></span>
				<span><?php esc_html_e( 'Save Single Page Design (সংরক্ষণ করুন)', 'nexora-booking-manager' ); ?></span>
			</button>
		</div>

		<div class="nbm-single-studio-grid" style="display: grid; grid-template-columns: 400px 1fr; gap: 24px; align-items: start;">
			
			<!-- Left: Single Layout Selector -->
			<div class="nbm-single-controls">
				<div class="nbm-panel" style="background:#ffffff; border-radius:10px; border:1px solid #cbd5e1; box-shadow:0 2px 8px rgba(0,0,0,0.04); padding:20px;">
					<h3 style="margin:0 0 14px; font-size:15px; color:#1e293b;">
						🎨 <?php esc_html_e( 'Choose Single Details Layout (৫টি ডিজাইন):', 'nexora-booking-manager' ); ?>
					</h3>

					<div class="nbm-layout-cards-list" style="display:flex; flex-direction:column; gap:12px;">
						
						<!-- Layout 1 -->
						<label class="nbm-layout-card <?php echo ( 'classic_hero' === $current_single_layout ) ? 'selected' : ''; ?>" data-layout="classic_hero" style="border:2px solid <?php echo ( 'classic_hero' === $current_single_layout ) ? '#1e40af' : '#e2e8f0'; ?>; border-radius:8px; padding:12px; cursor:pointer; display:block; transition:all 0.2s ease;">
							<div style="display:flex; align-items:center; gap:10px;">
								<input type="radio" name="nbm_single_layout_choice" value="classic_hero" <?php checked( $current_single_layout, 'classic_hero' ); ?> style="margin:0;" />
								<strong style="font-size:14px; color:#1e40af;">🏰 Layout 1: Classic Luxury Hero</strong>
							</div>
							<p style="margin:6px 0 0 24px; font-size:12px; color:#64748b; line-height:1.4;">
								Panoramic hero banner on top, 65/35 dual-column layout with gallery showcase, rich story, schedule, and sticky booking card on right.
							</p>
						</label>

						<!-- Layout 2 -->
						<label class="nbm-layout-card <?php echo ( 'split_screen' === $current_single_layout ) ? 'selected' : ''; ?>" data-layout="split_screen" style="border:2px solid <?php echo ( 'split_screen' === $current_single_layout ) ? '#1e40af' : '#e2e8f0'; ?>; border-radius:8px; padding:12px; cursor:pointer; display:block; transition:all 0.2s ease;">
							<div style="display:flex; align-items:center; gap:10px;">
								<input type="radio" name="nbm_single_layout_choice" value="split_screen" <?php checked( $current_single_layout, 'split_screen' ); ?> style="margin:0;" />
								<strong style="font-size:14px; color:#0d9488;">⚡ Layout 2: Modern 50/50 Split Screen</strong>
							</div>
							<p style="margin:6px 0 0 24px; font-size:12px; color:#64748b; line-height:1.4;">
								Dual split screen: sticky full-height gallery showcase on left 50%, fluid story stream, hours table, and reservation form on right 50%.
							</p>
						</label>

						<!-- Layout 3 -->
						<label class="nbm-layout-card <?php echo ( 'fullscreen_cinematic' === $current_single_layout ) ? 'selected' : ''; ?>" data-layout="fullscreen_cinematic" style="border:2px solid <?php echo ( 'fullscreen_cinematic' === $current_single_layout ) ? '#1e40af' : '#e2e8f0'; ?>; border-radius:8px; padding:12px; cursor:pointer; display:block; transition:all 0.2s ease; background:#0f172a; color:#fff;">
							<div style="display:flex; align-items:center; gap:10px;">
								<input type="radio" name="nbm_single_layout_choice" value="fullscreen_cinematic" <?php checked( $current_single_layout, 'fullscreen_cinematic' ); ?> style="margin:0;" />
								<strong style="font-size:14px; color:#f59e0b;">🎬 Layout 3: Fullscreen Cinematic Showcase</strong>
							</div>
							<p style="margin:6px 0 0 24px; font-size:12px; color:#cbd5e1; line-height:1.4;">
								Dark onyx luxury ambiance, ambient gold highlights, frosted glassmorphism content cards, and floating VIP booking form.
							</p>
						</label>

						<!-- Layout 4 -->
						<label class="nbm-layout-card <?php echo ( 'editorial_magazine' === $current_single_layout ) ? 'selected' : ''; ?>" data-layout="editorial_magazine" style="border:2px solid <?php echo ( 'editorial_magazine' === $current_single_layout ) ? '#1e40af' : '#e2e8f0'; ?>; border-radius:8px; padding:12px; cursor:pointer; display:block; transition:all 0.2s ease;">
							<div style="display:flex; align-items:center; gap:10px;">
								<input type="radio" name="nbm_single_layout_choice" value="editorial_magazine" <?php checked( $current_single_layout, 'editorial_magazine' ); ?> style="margin:0;" />
								<strong style="font-size:14px; color:#334155;">📰 Layout 4: Editorial Magazine & Story</strong>
							</div>
							<p style="margin:6px 0 0 24px; font-size:12px; color:#64748b; line-height:1.4;">
								Serif editorial typography, centered boutique headline, curated pull-quote spec strip, narrative reading flow, and boutique booking box.
							</p>
						</label>

						<!-- Layout 5 -->
						<label class="nbm-layout-card <?php echo ( 'tabbed_navigator' === $current_single_layout ) ? 'selected' : ''; ?>" data-layout="tabbed_navigator" style="border:2px solid <?php echo ( 'tabbed_navigator' === $current_single_layout ) ? '#1e40af' : '#e2e8f0'; ?>; border-radius:8px; padding:12px; cursor:pointer; display:block; transition:all 0.2s ease;">
							<div style="display:flex; align-items:center; gap:10px;">
								<input type="radio" name="nbm_single_layout_choice" value="tabbed_navigator" <?php checked( $current_single_layout, 'tabbed_navigator' ); ?> style="margin:0;" />
								<strong style="font-size:14px; color:#059669;">📑 Layout 5: Interactive Tabbed Navigator</strong>
							</div>
							<p style="margin:6px 0 0 24px; font-size:12px; color:#64748b; line-height:1.4;">
								Clean horizontal tab bar: <em>Overview, Photo Gallery, Hours & Schedule, Features & Rules, Reserve Now</em>. Switches smoothly without long scrolling!
							</p>
						</label>

					</div>

					<button type="button" class="button button-primary nbm-trigger-save-single" style="width:100%; margin-top:20px; padding:8px 14px; font-size:14px; font-weight:700; display:flex; justify-content:center; align-items:center; gap:6px;">
						💾 <?php esc_html_e( 'Save Single Page Design', 'nexora-booking-manager' ); ?>
					</button>

				</div>
			</div>

			<!-- Right: Single Details Interactive Live Preview -->
			<div class="nbm-single-preview-column">
				<div class="nbm-panel" style="background:#ffffff; border-radius:10px; border:1px solid #cbd5e1; box-shadow:0 2px 8px rgba(0,0,0,0.04); padding:20px;">
					
					<!-- Single Preview Header -->
					<div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; border-bottom:1px solid #f1f5f9; padding-bottom:12px; margin-bottom:16px;">
						<div>
							<h3 style="margin:0; font-size:16px; color:#0f172a; display:flex; align-items:center; gap:8px;">
								<span>👁 <?php esc_html_e( 'Single Page Live Layout Preview (রিয়েল-টাইম প্রিভিউ)', 'nexora-booking-manager' ); ?></span>
								<span id="nbm-single-active-badge" style="background:#1e40af; color:#fff; font-size:11px; font-weight:700; padding:2px 8px; border-radius:12px;">Classic Hero</span>
							</h3>
						</div>

						<!-- Device Switcher for Single -->
						<div class="button-group">
							<button type="button" class="button button-small nbm-single-device-btn active" data-width="100%" title="<?php esc_attr_e( 'Desktop View', 'nexora-booking-manager' ); ?>">🖥️ Desktop</button>
							<button type="button" class="button button-small nbm-single-device-btn" data-width="768px" title="<?php esc_attr_e( 'Tablet View (768px)', 'nexora-booking-manager' ); ?>">📱 Tablet</button>
							<button type="button" class="button button-small nbm-single-device-btn" data-width="380px" title="<?php esc_attr_e( 'Mobile View (380px)', 'nexora-booking-manager' ); ?>">📲 Mobile</button>
						</div>
					</div>

					<!-- Single Preview Wrapper -->
					<div id="nbm-single-preview-wrapper" style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:20px; min-height:550px; overflow-x:auto; transition:width 0.3s ease; margin:0 auto; box-sizing:border-box;">
						<div id="nbm-single-preview-stage" class="nbm-wrapper">
							<!-- Dynamically generated single layout preview -->
						</div>
					</div>

				</div>
			</div>

		</div>
	</div>

</div>

<style>
.nbm-chip {
	display: inline-block;
	padding: 5px 12px;
	background: #f1f5f9;
	color: #334155;
	border-radius: 16px;
	font-size: 12px;
	font-weight: 600;
	cursor: pointer;
	border: 1px solid #e2e8f0;
	transition: all 0.15s ease;
}
.nbm-chip:hover {
	background: #e2e8f0;
	border-color: #cbd5e1;
}
.nbm-chip.active {
	background: #1e40af;
	color: #ffffff;
	border-color: #1e40af;
}

.nbm-layout-card:hover {
	border-color: #94a3b8 !important;
	transform: translateY(-1px);
}
.nbm-layout-card.selected {
	border-color: #1e40af !important;
	background: #f0f7ff !important;
}
.nbm-layout-card[data-layout="fullscreen_cinematic"].selected {
	border-color: #f59e0b !important;
	background: #111827 !important;
}
</style>
