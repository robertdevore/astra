<?php
/**
 * Astra Builder Loader.
 *
 * @package astra-builder
 */

if ( ! class_exists( 'Astra_Builder_Loader' ) ) {

	/**
	 * Class Astra_Builder_Loader.
	 */
	final class Astra_Builder_Loader {

		/**
		 *  No. Of. Footer Widgets.
		 *
		 * @var constant
		 */
		public static $num_of_footer_widgets = 4;

		/**
		 *  No. Of. Footer HTML.
		 *
		 * @var constant
		 */
		public static $num_of_footer_html = 2;

		/**
		 *  No. Of. Header Widgets.
		 *
		 * @var constant
		 */
		public static $num_of_header_widgets = 0;

		/**
		 *  No. Of. Header Menu.
		 *
		 * @var constant
		 */
		public static $num_of_header_menu = 2;

		/**
		 *  No. Of. Header Buttons.
		 *
		 * @var constant
		 */
		public static $num_of_header_button = 1;

		/**
		 *  No. Of. Header HTML.
		 *
		 * @var constant
		 */
		public static $num_of_header_html = 2;

		/**
		 * Member Variable
		 *
		 * @var instance
		 */
		private static $instance = null;

		/**
		 *  Initiator
		 */
		public static function get_instance() {

			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
				do_action( 'astra_builder_loaded' );
			}

			return self::$instance;
		}

		/**
		 * Constructor
		 */
		public function __construct() {

			$this->define_constants();

			add_action( 'after_setup_theme', array( $this, 'load_plugin' ) );

			add_action( 'after_setup_theme', array( $this, 'load_options_default' ), 9 );


			add_action( 'init', array( $this, 'load_core_files' ) );

			add_action( 'wp', array( $this, 'load_markup' ), 100 );

			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

			add_action( 'admin_init', array( $this, 'page_header_compatibility' ) );
		}

		/**
		 * Addon: Page Header Compatibility.
		 *
		 * @since x.x.x
		 * @return void
		 */
		public function page_header_compatibility() {
			if ( is_callable( 'Astra_Ext_Advanced_Headers_Meta::get_instance' ) && Astra_Builder_Helper::is_migrated() ) {
				remove_action( 'astra_adv_headers_tabs_site-header_action', array( Astra_Ext_Advanced_Headers_Meta::get_instance(), 'site_header_tab' ), 10, 1 );
				add_action( 'astra_adv_headers_tabs_site-header_action', array( $this, 'site_header_tab' ), 10, 1 );
			}
		}

		/**
		 * Markup for Site Header Tabs.
		 *
		 * @since x.x.x
		 *
		 * @param  array $options Post meta.
		 */
		public function site_header_tab( $options ) {
			$layout = $options['layouts'];
			$design = $options['designs'];

			?>
			<table class="ast-advanced-headers-table widefat ast-required-advanced-headers">
				<tr class="ast-advanced-headers-row ast-advanced-header-layout-merge-wrap">
					<td class="ast-advanced-headers-row-heading">
						<label><?php esc_html_e( 'Merge Page Header with Site Header', 'astra-addon', 'astra' ); ?></label>
					</td>
					<td class="ast-advanced-headers-row-content">
						<input type="checkbox" id="ast-advanced-header-layout-merged"
								name="ast-advanced-headers-layout[merged]"
								value="enabled" <?php checked( isset( $layout['merged'] ) ? $layout['merged'] : '', 'enabled' ); ?> />
					</td>
				</tr>
			</table>

			<table class="ast-advanced-headers-table require-merge-ast-advanced-header widefat">
				<!-- Site Identity  -->
				<tr class="ast-advanced-headers-row">
					<td class="ast-advanced-headers-row-heading title">
						<label><?php esc_html_e( 'Site Identity', 'astra-addon', 'astra' ); ?></label>
					</td>
				</tr>
				<tr class="ast-advanced-headers-row diff-logo-wrap">
					<td class="ast-advanced-headers-row-heading">
						<label><?php esc_html_e( 'Different Logo for Page Header?', 'astra-addon', 'astra' ); ?></label>
					</td>
					<td class="ast-advanced-headers-row-content">
						<input type="checkbox" id="ast-advanced-header-diff-header-logo"
								name="ast-advanced-headers-layout[diff-header-logo]"
								value="enabled" <?php checked( isset( $layout['diff-header-logo'] ) ? $layout['diff-header-logo'] : '', 'enabled' ); ?> />
					</td>
				</tr>
				<tr class="ast-advanced-headers-row ast-logo-settings-wrap">

					<td class="ast-advanced-headers-row-sub-heading">
						<label><?php esc_html_e( 'Logo', 'astra-addon', 'astra' ); ?></label>
					</td>
					<td class="ast-advanced-headers-row-content">
						<div id="ast-advanced-headers-preview-logo">
							<?php if ( isset( $design['logo-url'] ) && '' != $design['logo-url'] ) { ?>
								<img class="ast-advanced-headers-logo saved-image"
									src="<?php echo esc_url( $design['logo-url'] ); ?>"
									style="margin-bottom:12px;max-width:150px;"/>
							<?php } ?>
						</div>
						<input type="hidden" id="ast-advanced-headers-logo-id" class="ast-advanced-headers-logo-id"
								name="ast-advanced-headers-design[logo-id]"
								value="<?php echo esc_attr( $design['logo-id'] ); ?>"/>
						<input type="hidden" id="ast-advanced-headers-logo" class="ast-advanced-headers-logo"
								name="ast-advanced-headers-design[logo-url]"
								value="<?php echo esc_attr( $design['logo-url'] ); ?>"/>

						<a class="ast-advanced-header-logo-select button-secondary"
							href="#"><?php esc_html_e( 'Select logo', 'astra-addon', 'astra' ); ?></a>
						<?php
						// Remove button based on image is selected or not.
						$remove_logo_button = ( isset( $design['logo-url'] ) && '' != $design['logo-url'] ) ? 'display:inline-block;' : 'display:none;';
						?>
						<button class="ast-advanced-headers-logo-remove button" type="button"
								style="<?php echo esc_attr( $remove_logo_button ); ?>">
							<?php esc_html_e( 'Remove Logo', 'astra-addon', 'astra' ); ?>
						</button>
					</td>
				</tr>
				<tr class="ast-advanced-headers-row ast-diff-header-retina-logo">
					<td class="ast-advanced-headers-row-sub-heading">
						<label><?php esc_html_e( 'Different Logo for retina devices?', 'astra-addon', 'astra' ); ?></label>
					</td>
					<td class="ast-advanced-headers-row-content">
						<input type="checkbox" id="ast-advanced-header-diff-header-retina-logo"
								name="ast-advanced-headers-layout[diff-header-retina-logo]"
								value="enabled" <?php checked( isset( $layout['diff-header-retina-logo'] ) ? $layout['diff-header-retina-logo'] : '', 'enabled' ); ?> />
					</td>
				</tr>
				<tr class="ast-advanced-headers-row ast-retina-logo-settings-wrap">
					<td class="ast-advanced-headers-row-sub-heading">
						<label><?php esc_html_e( 'Retina Logo', 'astra-addon', 'astra' ); ?></label>
					</td>
					<td class="ast-advanced-headers-row-content">
						<div id="ast-advanced-headers-preview-logo">
							<?php if ( isset( $design['retina-logo-url'] ) && '' != $design['retina-logo-url'] ) { ?>
								<img class="ast-advanced-headers-logo saved-image"
									src="<?php echo esc_url( $design['retina-logo-url'] ); ?>"
									style="margin-bottom:12px;max-width:150px;"/>
							<?php } ?>
						</div>
						<input type="hidden" id="ast-advanced-headers-logo-id" class="ast-advanced-headers-logo-id"
								name="ast-advanced-headers-design[retina-logo-id]"
								value="<?php echo esc_attr( $design['retina-logo-id'] ); ?>"/>
						<input type="hidden" id="ast-advanced-headers-logo" class="ast-advanced-headers-logo"
								name="ast-advanced-headers-design[retina-logo-url]"
								value="<?php echo esc_attr( $design['retina-logo-url'] ); ?>"/>

						<a class="ast-advanced-header-logo-select button-secondary"
							href="#"><?php esc_html_e( 'Select logo', 'astra-addon', 'astra' ); ?></a>
						<?php
						// Remove button based on image is selected or not.
						$remove_logo_button = ( isset( $design['retina-logo-url'] ) && '' != $design['retina-logo-url'] ) ? 'display:inline-block;' : 'display:none;';
						?>
						<button class="ast-advanced-headers-logo-remove button" type="button"
								style="<?php echo esc_attr( $remove_logo_button ); ?>">
							<?php esc_html_e( 'Remove Logo', 'astra-addon', 'astra' ); ?>
						</button>
					</td>
				</tr>
				<tr class="ast-advanced-headers-row ast-logo-settings-wrap">
					<td class="ast-advanced-headers-row-sub-heading">
						<label><?php esc_html_e( 'Logo Width', 'astra-addon', 'astra' ); ?></label>
					</td>
					<td class="ast-advanced-headers-row-content">
						<input type="number" min="0" step="1" max="600"
								name="ast-advanced-headers-design[header-logo-width]"
								value="<?php echo esc_attr( $design['header-logo-width'] ); ?>" placeholder='0'/>
					</td>
				</tr>
				<!-- Header Colors  -->
				<tr class="ast-advanced-headers-row">
					<td class="ast-advanced-headers-row-heading title">
						<label><?php esc_html_e( 'Customize Site Header', 'astra-addon', 'astra' ); ?></label>
					</td>
				</tr>
				<tr class="ast-advanced-headers-row">
					<td class="ast-advanced-headers-row-sub-heading">
						<label><?php esc_html_e( 'Background Overlay Color', 'astra-addon', 'astra' ); ?></label>
					</td>
					<td class="ast-advanced-headers-row-content">
						<input type="text" class="ast-advanced-headers-color-picker" data-alpha="true"
								name="ast-advanced-headers-design[header-bg-color]"
								value="<?php echo esc_attr( $design['header-bg-color'] ); ?>" />
					</td>
				</tr>

				<?php
				$site_title = astra_get_option( 'display-site-title' );
				if ( $site_title ) {
					?>
					<tr class="ast-advanced-headers-row">
						<td class="ast-advanced-headers-row-sub-heading">
							<label><?php esc_html_e( 'Site Title Color', 'astra-addon', 'astra' ); ?></label>
						</td>
						<td class="ast-advanced-headers-row-content">
							<input type="text" class="ast-advanced-headers-color-picker" data-alpha="true"
									name="ast-advanced-headers-design[site-title-color]"
									value="<?php echo esc_attr( $design['site-title-color'] ); ?>" />
						</td>
					</tr>
					<tr class="ast-advanced-headers-row">
						<td class="ast-advanced-headers-row-sub-heading">
							<label><?php esc_html_e( 'Site Title Hover Color', 'astra-addon', 'astra' ); ?></label>
						</td>
						<td class="ast-advanced-headers-row-content">
							<input type="text" class="ast-advanced-headers-color-picker" data-alpha="true"
									name="ast-advanced-headers-design[site-title-h-color]"
									value="<?php echo esc_attr( $design['site-title-h-color'] ); ?>" />
						</td>
					</tr>
					<?php
				}
				$display_site_tagline = astra_get_option( 'display-site-tagline' );
				if ( $display_site_tagline ) {
					?>

					<tr class="ast-advanced-headers-row">
						<td class="ast-advanced-headers-row-sub-heading">
							<label><?php esc_html_e( 'Site Tagline Color', 'astra-addon', 'astra' ); ?></label>
						</td>
						<td class="ast-advanced-headers-row-content">
							<input type="text" class="ast-advanced-headers-color-picker" data-alpha="true"
									name="ast-advanced-headers-design[site-tagline-color]"
									value="<?php echo esc_attr( $design['site-tagline-color'] ); ?>" />
						</td>
					</tr>
					<?php
				}
				?>
				<tr class="ast-advanced-headers-row">
					<td class="ast-advanced-headers-row-sub-heading">
						<label><?php esc_html_e( 'Border Bottom Size', 'astra-addon', 'astra' ); ?></label>
					</td>
					<td class="ast-advanced-headers-row-content">
						<input type="number" min="0" step="1" max="600"
								name="ast-advanced-headers-design[header-main-sep]"
								value="<?php echo esc_attr( $design['header-main-sep'] ); ?>" placeholder='0'/>
					</td>
				</tr>
				<tr class="ast-advanced-headers-row">
					<td class="ast-advanced-headers-row-sub-heading">
						<label><?php esc_html_e( 'Bottom Border Color', 'astra-addon', 'astra' ); ?></label>
					</td>
					<td class="ast-advanced-headers-row-content">
						<input type="text" class="ast-advanced-headers-color-picker" data-alpha="true"
							name="ast-advanced-headers-design[header-main-sep-color]"
							value="<?php echo esc_attr( $design['header-main-sep-color'] ); ?>" />
					</td>
				</tr>
				</table>
				<!-- Primary menu Colors  -->
				<table class="ast-advanced-headers-table widefat">
					<tr class="ast-advanced-headers-row">
						<td class="ast-advanced-headers-row-heading title">
							<label><?php esc_html_e( 'Primary Menu', 'astra-addon', 'astra' ); ?></label>
						</td>
					</tr>
					<tr class="ast-advanced-headers-row require-merge-ast-advanced-header">
						<td class="ast-advanced-headers-row-sub-heading">
							<label><?php esc_html_e( 'Background Color', 'astra-addon', 'astra' ); ?></label>
						</td>
						<td class="ast-advanced-headers-row-content">
							<input type="text" class="ast-advanced-headers-color-picker" data-alpha="true"
									name="ast-advanced-headers-design[primary-menu-bg-color]"
									value="<?php echo esc_attr( $design['primary-menu-bg-color'] ); ?>" />
						</td>
					</tr>
					<tr class="ast-advanced-headers-row require-merge-ast-advanced-header">
						<td class="ast-advanced-headers-row-sub-heading">
							<label><?php esc_html_e( 'Link / Text Color', 'astra-addon', 'astra' ); ?></label>
						</td>
						<td class="ast-advanced-headers-row-content">
							<input type="text" class="ast-advanced-headers-color-picker" data-alpha="true"
									name="ast-advanced-headers-design[primary-menu-color]"
									value="<?php echo esc_attr( $design['primary-menu-color'] ); ?>" />
						</td>
					</tr>
					<tr class="ast-advanced-headers-row require-merge-ast-advanced-header">
						<td class="ast-advanced-headers-row-sub-heading">
							<label><?php esc_html_e( 'Link Hover Color', 'astra-addon', 'astra' ); ?></label>
						</td>
						<td class="ast-advanced-headers-row-content">
							<input type="text" class="ast-advanced-headers-color-picker" data-alpha="true"
									name="ast-advanced-headers-design[primary-menu-h-color]"
									value="<?php echo esc_attr( $design['primary-menu-h-color'] ); ?>" />
						</td>
					</tr>
					<tr class="ast-advanced-headers-row require-merge-ast-advanced-header">
						<td class="ast-advanced-headers-row-sub-heading">
							<label><?php esc_html_e( 'Link Active Color', 'astra-addon', 'astra' ); ?></label>
						</td>
						<td class="ast-advanced-headers-row-content">
							<input type="text" class="ast-advanced-headers-color-picker" data-alpha="true"
									name="ast-advanced-headers-design[primary-menu-a-color]"
									value="<?php echo ( isset( $design['primary-menu-a-color'] ) ) ? esc_attr( $design['primary-menu-a-color'] ) : ''; ?>" />
						</td>
					</tr>

					<!-- Primary menu -> submenu Colors  -->
					<tr class="ast-advanced-headers-row require-merge-ast-advanced-header">
						<td class="ast-advanced-headers-row-sub-heading">
							<label><?php esc_html_e( 'Submenu Background Color', 'astra-addon', 'astra' ); ?></label>
						</td>
						<td class="ast-advanced-headers-row-content">
							<input type="text" class="ast-advanced-headers-color-picker" data-alpha="true"
									name="ast-advanced-headers-design[primary-submenu-bg-color]"
									value="<?php echo ( isset( $design['primary-submenu-bg-color'] ) ) ? esc_attr( $design['primary-submenu-bg-color'] ) : ''; ?>" />
						</td>
					</tr>
					<tr class="ast-advanced-headers-row require-merge-ast-advanced-header">
						<td class="ast-advanced-headers-row-sub-heading">
							<label><?php esc_html_e( 'Submenu Link / Text Color', 'astra-addon', 'astra' ); ?></label>
						</td>
						<td class="ast-advanced-headers-row-content">
							<input type="text" class="ast-advanced-headers-color-picker" data-alpha="true"
									name="ast-advanced-headers-design[primary-submenu-color]"
									value="<?php echo ( isset( $design['primary-submenu-color'] ) ) ? esc_attr( $design['primary-submenu-color'] ) : ''; ?>" />
						</td>
					</tr>
					<tr class="ast-advanced-headers-row require-merge-ast-advanced-header">
						<td class="ast-advanced-headers-row-sub-heading">
							<label><?php esc_html_e( 'Submenu Link Hover Color', 'astra-addon', 'astra' ); ?></label>
						</td>
						<td class="ast-advanced-headers-row-content">
							<input type="text" class="ast-advanced-headers-color-picker" data-alpha="true"
									name="ast-advanced-headers-design[primary-submenu-h-color]"
									value="<?php echo ( isset( $design['primary-submenu-h-color'] ) ) ? esc_attr( $design['primary-submenu-h-color'] ) : ''; ?>" />
						</td>
					</tr>
					<tr class="ast-advanced-headers-row require-merge-ast-advanced-header">
						<td class="ast-advanced-headers-row-sub-heading">
							<label><?php esc_html_e( 'Submenu Link Active Color', 'astra-addon', 'astra' ); ?></label>
						</td>
						<td class="ast-advanced-headers-row-content">
							<input type="text" class="ast-advanced-headers-color-picker" data-alpha="true"
									name="ast-advanced-headers-design[primary-submenu-a-color]"
									value="<?php echo ( isset( $design['primary-submenu-a-color'] ) ) ? esc_attr( $design['primary-submenu-a-color'] ) : ''; ?>" />
						</td>
					</tr>
					<?php
					// Get all nav menus.
					$menu_locations = get_nav_menu_locations();
					?>

					<tr class="ast-advanced-headers-row">
						<td class="ast-advanced-headers-row-sub-heading">
							<label><?php esc_html_e( 'Select Primary Menu', 'astra-addon', 'astra' ); ?></label>
						</td>
						<td class="ast-advanced-headers-row-content">
							<?php
							if ( isset( $design['custom-menu'] ) ) {
								$primary_manu = isset( $menu_locations['primary'] ) ? $menu_locations['primary'] : '';
								$custom_menu  = ( ( isset( $design['custom-menu'] ) && '' == $design['custom-menu'] ) ) ? $primary_manu : $design['custom-menu'];
								$nav_menus    = wp_get_nav_menus();
							}
							?>

							<select name="ast-advanced-headers-design[custom-menu]" style="width: auto" ;>
								<option
									value="0"><?php printf( '&mdash; %s &mdash;', esc_html__( 'Default', 'astra-addon', 'astra' ) ); ?></option>
								<?php
								if ( isset( $design['custom-menu'] ) && ! empty( $nav_menus ) ) {
									foreach ( $nav_menus as $menu ) :
										?>
									<option <?php selected( $custom_menu == $menu->term_id ); ?>
										value="<?php echo esc_attr( $menu->term_id ); ?>">
										<?php echo esc_html( $menu->name ); ?>
									</option>
										<?php
								endforeach;
								}
								?>
							</select>
						</td>
					</tr>
				</table>
				<?php

				$above_header_layout = astra_get_option( 'above-header-layout' );

				if ( Astra_Ext_Extension::is_active( 'header-sections' ) && 'disabled' != $above_header_layout ) {
					?>

				<!-- Above Header Colors  -->
				<table class="ast-advanced-headers-table widefat">
					<tr class="ast-advanced-headers-row">
						<td class="ast-advanced-headers-row-heading title">
							<label><?php esc_html_e( 'Above Header', 'astra-addon', 'astra' ); ?></label>
						</td>
						<tr class="ast-advanced-headers-row">
							<td class="ast-advanced-headers-row-sub-heading">
								<label><?php esc_html_e( 'Display Above Header', 'astra-addon', 'astra' ); ?></label>
							</td>
							<td class="ast-advanced-headers-row-content">
								<input type="checkbox" id="ast-advanced-header-layout-above-header"
										name="ast-advanced-headers-layout[above-header-enabled]"
										value="enabled" <?php checked( isset( $layout['above-header-enabled'] ) ? $layout['above-header-enabled'] : '', 'enabled' ); ?> />
							</td>
						</tr>
					</tr>
					<tr class="ast-advanced-headers-row ast-above-header-required">
						<td class="ast-advanced-headers-row-sub-heading">
							<label><?php esc_html_e( 'Background Color', 'astra-addon', 'astra' ); ?></label>
						</td>
						<td class="ast-advanced-headers-row-content">
							<input type="text" class="ast-advanced-headers-color-picker" data-alpha="true"
									name="ast-advanced-headers-design[above-header-bg-color]"
									value="<?php echo esc_attr( $design['above-header-bg-color'] ); ?>" />
						</td>
					</tr>
					<tr class="ast-advanced-headers-row ast-above-header-required">
						<td class="ast-advanced-headers-row-sub-heading">
							<label><?php esc_html_e( 'Link / Text Color', 'astra-addon', 'astra' ); ?></label>
						</td>
						<td class="ast-advanced-headers-row-content">
							<input type="text" class="ast-advanced-headers-color-picker" data-alpha="true"
									name="ast-advanced-headers-design[above-header-text-link-color]"
									value="<?php echo esc_attr( $design['above-header-text-link-color'] ); ?>" />
						</td>
					</tr>
					<tr class="ast-advanced-headers-row ast-above-header-required">
						<td class="ast-advanced-headers-row-sub-heading">
							<label><?php esc_html_e( 'Link Hover Color', 'astra-addon', 'astra' ); ?></label>
						</td>
						<td class="ast-advanced-headers-row-content">
							<input type="text" class="ast-advanced-headers-color-picker" data-alpha="true"
									name="ast-advanced-headers-design[above-header-h-color]"
									value="<?php echo esc_attr( $design['above-header-h-color'] ); ?>" />
						</td>
					</tr>
					<tr class="ast-advanced-headers-row ast-above-header-required">
						<td class="ast-advanced-headers-row-sub-heading">
							<label><?php esc_html_e( 'Link Active Color', 'astra-addon', 'astra' ); ?></label>
						</td>
						<td class="ast-advanced-headers-row-content">
							<input type="text" class="ast-advanced-headers-color-picker" data-alpha="true"
									name="ast-advanced-headers-design[above-header-a-color]"
									value="<?php echo ( isset( $design['above-header-a-color'] ) ) ? esc_attr( $design['above-header-a-color'] ) : ''; ?>" />
						</td>
					</tr>
					<!-- Above menu -> submenu Colors  -->
					<tr class="ast-advanced-headers-row ast-above-header-required">
						<td class="ast-advanced-headers-row-sub-heading">
							<label><?php esc_html_e( 'Submenu Background Color', 'astra-addon', 'astra' ); ?></label>
						</td>
						<td class="ast-advanced-headers-row-content">
							<input type="text" class="ast-advanced-headers-color-picker" data-alpha="true"
									name="ast-advanced-headers-design[above-header-submenu-bg-color]"
									value="<?php echo ( isset( $design['above-header-submenu-bg-color'] ) ) ? esc_attr( $design['above-header-submenu-bg-color'] ) : ''; ?>" />
						</td>
					</tr>
					<tr class="ast-advanced-headers-row ast-above-header-required">
						<td class="ast-advanced-headers-row-sub-heading">
							<label><?php esc_html_e( 'Submenu Link / Text Color', 'astra-addon', 'astra' ); ?></label>
						</td>
						<td class="ast-advanced-headers-row-content">
							<input type="text" class="ast-advanced-headers-color-picker" data-alpha="true"
									name="ast-advanced-headers-design[above-header-submenu-link-color]"
									value="<?php echo ( isset( $design['above-header-submenu-link-color'] ) ) ? esc_attr( $design['above-header-submenu-link-color'] ) : ''; ?>" />
						</td>
					</tr>
					<tr class="ast-advanced-headers-row ast-above-header-required">
						<td class="ast-advanced-headers-row-sub-heading">
							<label><?php esc_html_e( 'Submenu Link Hover Color', 'astra-addon', 'astra' ); ?></label>
						</td>
						<td class="ast-advanced-headers-row-content">
							<input type="text" class="ast-advanced-headers-color-picker" data-alpha="true"
									name="ast-advanced-headers-design[above-header-submenu-h-color]"
									value="<?php echo ( isset( $design['above-header-submenu-h-color'] ) ) ? esc_attr( $design['above-header-submenu-h-color'] ) : ''; ?>" />
						</td>
					</tr>
					<tr class="ast-advanced-headers-row ast-above-header-required">
						<td class="ast-advanced-headers-row-sub-heading">
							<label><?php esc_html_e( 'Submenu Link Active Color', 'astra-addon', 'astra' ); ?></label>
						</td>
						<td class="ast-advanced-headers-row-content">
							<input type="text" class="ast-advanced-headers-color-picker" data-alpha="true"
									name="ast-advanced-headers-design[above-header-submenu-a-color]"
									value="<?php echo ( isset( $design['above-header-submenu-a-color'] ) ) ? esc_attr( $design['above-header-submenu-a-color'] ) : ''; ?>" />
						</td>
					</tr>
				</table>
					<?php
				}

				$below_header_layout = astra_get_option( 'below-header-layout' );

				if ( Astra_Ext_Extension::is_active( 'header-sections' ) && 'disabled' != $below_header_layout ) {
					?>

				<!-- Below Header Colors  -->
				<table class="ast-advanced-headers-table widefat">
					<tr class="ast-advanced-headers-row">
						<td class="ast-advanced-headers-row-heading title">
							<label><?php esc_html_e( 'Below Header', 'astra-addon', 'astra' ); ?></label>
						</td>
						<tr class="ast-advanced-headers-row">
							<td class="ast-advanced-headers-row-sub-heading">
								<label><?php esc_html_e( 'Display Below Header', 'astra-addon', 'astra' ); ?></label>
							</td>
							<td class="ast-advanced-headers-row-content">
								<input type="checkbox" id="ast-advanced-header-layout-below-header"
										name="ast-advanced-headers-layout[below-header-enabled]"
										value="enabled" <?php checked( isset( $layout['below-header-enabled'] ) ? $layout['below-header-enabled'] : '', 'enabled' ); ?> />
							</td>
						</tr>
					</tr>
					<tr class="ast-advanced-headers-row ast-below-header-required">
						<td class="ast-advanced-headers-row-sub-heading">
							<label><?php esc_html_e( 'Background Color', 'astra-addon', 'astra' ); ?></label>
						</td>
						<td class="ast-advanced-headers-row-content">
							<input type="text" class="ast-advanced-headers-color-picker" data-alpha="true"
									name="ast-advanced-headers-design[below-header-bg-color]"
									value="<?php echo esc_attr( $design['below-header-bg-color'] ); ?>" />
						</td>
					</tr>
					<tr class="ast-advanced-headers-row ast-below-header-required">
						<td class="ast-advanced-headers-row-sub-heading">
							<label><?php esc_html_e( 'Link / Text Color', 'astra-addon', 'astra' ); ?></label>
						</td>
						<td class="ast-advanced-headers-row-content">
							<input type="text" class="ast-advanced-headers-color-picker" data-alpha="true"
									name="ast-advanced-headers-design[below-header-text-link-color]"
									value="<?php echo esc_attr( $design['below-header-text-link-color'] ); ?>" />
						</td>
					</tr>
					<tr class="ast-advanced-headers-row ast-below-header-required">
						<td class="ast-advanced-headers-row-sub-heading">
							<label><?php esc_html_e( 'Link Hover Color', 'astra-addon', 'astra' ); ?></label>
						</td>
						<td class="ast-advanced-headers-row-content">
							<input type="text" class="ast-advanced-headers-color-picker" data-alpha="true"
									name="ast-advanced-headers-design[below-header-h-color]"
									value="<?php echo esc_attr( $design['below-header-h-color'] ); ?>" />
						</td>
					</tr>
					<tr class="ast-advanced-headers-row ast-below-header-required">
						<td class="ast-advanced-headers-row-sub-heading">
							<label><?php esc_html_e( 'Link Active Color', 'astra-addon', 'astra' ); ?></label>
						</td>
						<td class="ast-advanced-headers-row-content">
							<input type="text" class="ast-advanced-headers-color-picker" data-alpha="true"
									name="ast-advanced-headers-design[below-header-a-color]"
									value="<?php echo ( isset( $design['below-header-a-color'] ) ) ? esc_attr( $design['below-header-a-color'] ) : ''; ?>" />
						</td>
					</tr>
					<!-- Below menu -> submenu Colors  -->
					<tr class="ast-advanced-headers-row ast-above-header-required">
						<td class="ast-advanced-headers-row-sub-heading">
							<label><?php esc_html_e( 'Submenu Background Color', 'astra-addon', 'astra' ); ?></label>
						</td>
						<td class="ast-advanced-headers-row-content">
							<input type="text" class="ast-advanced-headers-color-picker" data-alpha="true"
									name="ast-advanced-headers-design[below-header-submenu-bg-color]"
									value="<?php echo ( isset( $design['below-header-submenu-bg-color'] ) ) ? esc_attr( $design['below-header-submenu-bg-color'] ) : ''; ?>" />
						</td>
					</tr>
					<tr class="ast-advanced-headers-row ast-below-header-required">
						<td class="ast-advanced-headers-row-sub-heading">
							<label><?php esc_html_e( 'Submenu Link / Text Color', 'astra-addon', 'astra' ); ?></label>
						</td>
						<td class="ast-advanced-headers-row-content">
							<input type="text" class="ast-advanced-headers-color-picker" data-alpha="true"
									name="ast-advanced-headers-design[below-header-submenu-link-color]"
									value="<?php echo ( isset( $design['below-header-submenu-link-color'] ) ) ? esc_attr( $design['below-header-submenu-link-color'] ) : ''; ?>" />
						</td>
					</tr>
					<tr class="ast-advanced-headers-row ast-below-header-required">
						<td class="ast-advanced-headers-row-sub-heading">
							<label><?php esc_html_e( 'Submenu Link Hover Color', 'astra-addon', 'astra' ); ?></label>
						</td>
						<td class="ast-advanced-headers-row-content">
							<input type="text" class="ast-advanced-headers-color-picker" data-alpha="true"
									name="ast-advanced-headers-design[below-header-submenu-h-color]"
									value="<?php echo ( isset( $design['below-header-submenu-h-color'] ) ) ? esc_attr( $design['below-header-submenu-h-color'] ) : ''; ?>" />
						</td>
					</tr>
					<tr class="ast-advanced-headers-row ast-below-header-required">
						<td class="ast-advanced-headers-row-sub-heading">
							<label><?php esc_html_e( 'Submenu Link Active Color', 'astra-addon', 'astra' ); ?></label>
						</td>
						<td class="ast-advanced-headers-row-content">
							<input type="text" class="ast-advanced-headers-color-picker" data-alpha="true"
									name="ast-advanced-headers-design[below-header-submenu-a-color]"
									value="<?php echo ( isset( $design['below-header-submenu-a-color'] ) ) ? esc_attr( $design['below-header-submenu-a-color'] ) : ''; ?>" />
						</td>
					</tr>
				</table>
					<?php
				}
		}

		/**
		 * Update components based on Pro / Free version.
		 *
		 * @since x.x.x
		 */
		public function define_constants() {

			if ( defined( 'ASTRA_EXT_FILE' ) ) {
				self::$num_of_header_widgets = 4;
				self::$num_of_header_button  = 2;
				self::$num_of_header_html    = 4;
			}

			add_filter( 'astra_quick_settings', array( $this, 'quick_settings' ) );
		}

		/**
		 * Update Quick Settings links.
		 *
		 * @param array $quick_settings Links to the Quick Settings in Astra.
		 * @since x.x.x
		 */
		public function quick_settings( $quick_settings ) {

			if ( ! Astra_Builder_Helper::is_migrated() ) {
				return $quick_settings;
			}

			$quick_settings['header']['title']     = __( 'Header Builder', 'astra-builder' );
			$quick_settings['header']['quick_url'] = admin_url( 'customize.php?autofocus[section]=section-header-builder-layout' );

			$quick_settings['footer']['title']     = __( 'Footer Builder', 'astra-builder' );
			$quick_settings['footer']['quick_url'] = admin_url( 'customize.php?autofocus[section]=section-footer-builder-layout' );

			return $quick_settings;
		}

		/**
		 * Advanced Hooks markup loader
		 *
		 * Loads appropriate template file based on the style option selected in options panel.
		 *
		 * @since x.x.x
		 */
		public function load_markup() {

			if ( ! defined( 'ASTRA_EXT_VER' ) || ! Astra_Builder_Helper::is_migrated() ) {
				return;
			}

			$option = array(
				'location'  => 'ast-advanced-hook-location',
				'exclusion' => 'ast-advanced-hook-exclusion',
				'users'     => 'ast-advanced-hook-users',
			);

			$result             = Astra_Target_Rules_Fields::get_instance()->get_posts_by_conditions( ASTRA_ADVANCED_HOOKS_POST_TYPE, $option );
			$header_counter     = 0;
			$footer_counter     = 0;
			$layout_404_counter = 0;

			foreach ( $result as $post_id => $post_data ) {
				$post_type = get_post_type();

				if ( ASTRA_ADVANCED_HOOKS_POST_TYPE != $post_type ) {

					$layout = get_post_meta( $post_id, 'ast-advanced-hook-layout', false );

					if ( isset( $layout[0] ) && '404-page' == $layout[0] && 0 == $layout_404_counter ) {

						$layout_404_settings = get_post_meta( $post_id, 'ast-404-page', true );
						if ( isset( $layout_404_settings['disable_header'] ) && 'enabled' == $layout_404_settings['disable_header'] ) {
							remove_action( 'astra_header', array( Astra_Builder_Header::get_instance(), 'header_builder_markup' ) );
						}

						if ( isset( $layout_404_settings['disable_footer'] ) && 'enabled' == $layout_404_settings['disable_footer'] ) {
							remove_action( 'astra_footer', array( Astra_Builder_Footer::get_instance(), 'footer_markup' ) );
						}

						$layout_404_counter ++;
					} elseif ( isset( $layout[0] ) && 'header' == $layout[0] && 0 == $header_counter ) {
						// Remove default site's header.
						remove_action( 'astra_header', array( Astra_Builder_Header::get_instance(), 'header_builder_markup' ) );
						$header_counter++;
					} elseif ( isset( $layout[0] ) && 'footer' == $layout[0] && 0 == $footer_counter ) {
						// Remove default site's footer.
						remove_action( 'astra_footer', array( Astra_Builder_Footer::get_instance(), 'footer_markup' ) );
						$footer_counter++;
					}
				}
			}
		}

		/**
		 * Enqueue scripts.
		 *
		 * @since x.x.x
		 */
		public function enqueue_scripts() {

			if ( ! Astra_Builder_Helper::is_migrated() ) {
				return;
			}

			wp_enqueue_script( 'astra-mobile-header-js', ASTRA_THEME_URI . 'inc/assets/js/navigation.js', array( 'jquery' ), ASTRA_THEME_VERSION, true );
		}

		/**
		 * Loads default options array.
		 *
		 * @since x.x.x
		 */
		public function load_options_default() {
			require_once ASTRA_THEME_DIR . 'inc/customizer/builder/customizer/defaults/class-astra-options-default.php';
			require_once ASTRA_THEME_DIR . 'inc/customizer/builder/class-astra-builder-widget-controller.php';
		}

		/**
		 * Loads plugin files.
		 *
		 * @since 1.0.0
		 *
		 * @return void
		 */
		public function load_plugin() {

			/**
			 * Astra Builder Init.
			 *
			 * Fires when Astra Builder is instantiated.
			 *
			 * @since 1.0.0
			 */
			do_action( 'astra_builder_init' );

			require_once ASTRA_THEME_DIR . 'inc/customizer/builder/class-astra-builder-helper.php';
		}

		/**
		 * Load core files.
		 */
		public function load_core_files() {

			require_once ASTRA_THEME_DIR . 'inc/customizer/builder/class-astra-builder-controller.php';
			require_once ASTRA_THEME_DIR . 'inc/customizer/builder/class-astra-builder-icon-controller.php';
			require_once ASTRA_THEME_DIR . 'inc/customizer/builder/class-astra-builder-ui-controller.php';

			if ( Astra_Builder_Helper::is_migrated() ) {

				require_once ASTRA_THEME_DIR . 'inc/customizer/builder/class-astra-builder-header.php';
				require_once ASTRA_THEME_DIR . 'inc/customizer/builder/class-astra-builder-footer.php';

				// Register the Secondary & Mobile menus.
				register_nav_menus(
					array(
						'secondary_menu' => __( 'Secondary Menu', 'astra-builder' ),
					)
				);
			}
			require_once ASTRA_THEME_DIR . 'inc/customizer/builder/customizer/theme-configuration/class-astra-theme-existing-configs.php';
			require_once ASTRA_THEME_DIR . 'inc/customizer/builder/class-astra-builder-admin.php';
			if ( Astra_Builder_Helper::is_migrated() ) {
				add_filter( 'astra_existing_header_footer_configs', '__return_false' );
				add_filter( 'astra_addon_existing_header_footer_configs', '__return_false' );
			}
		}

	}

	/**
	 *  Prepare if class 'Astra_Builder_Loader' exist.
	 *  Kicking this off by calling 'get_instance()' method
	 */
	Astra_Builder_Loader::get_instance();
}


if ( ! function_exists( 'astra_builder' ) ) {
	/**
	 * Get global class.
	 *
	 * @return object
	 */
	function astra_builder() {
		return Astra_Builder_Loader::get_instance();
	}
}
