<?php

// Prevent direct file access.
defined( 'ABSPATH' ) || die;

/**
 * Adds settings page.
 *
 * Original code generated by the WordPress Option Page generator:
 * @link http://jeremyhixon.com/wp-tools/option-page/
 */
class Mai_Publisher_Settings {
	/**
	 * Construct the class.
	 */
	function __construct() {
		$this->hooks();
	}

	/**
	 * Add hooks.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	function hooks() {
		add_action( 'admin_menu',                                          [ $this, 'add_menu_item' ], 12 );
		add_action( 'admin_enqueue_scripts',                               [ $this, 'enqueue_script' ] );
		add_action( 'admin_init',                                          [ $this, 'init' ] );
		add_filter( 'plugin_action_links_mai-publisher/mai-publisher.php', [ $this, 'add_plugin_links' ], 10, 4 );
	}

	/**
	 * Adds menu item for settings page.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	function add_menu_item() {
		add_submenu_page(
			'edit.php?post_type=mai_ad',
			__( 'Mai Publisher', 'mai-publisher' ), // page_title
			__( 'Settings', 'mai-publisher' ), // menu_title
			'manage_options', // capability
			'settings', // menu_slug
			[ $this, 'add_content' ], // callback
		);
	}

	/**
	 * Enqueue script for select2.
	 *
	 * @since 0.7.0
	 *
	 * @return void
	 */
	function enqueue_script() {
		$screen = get_current_screen();

		if ( 'mai_ad_page_settings' !== $screen->id ) {
			return;
		}

		// Select2 from CDN.
		wp_enqueue_style( 'mai-publisher-select2', 'https://cdn.jsdelivr.net/npm/select2/dist/css/select2.min.css' );
		wp_enqueue_script( 'mai-publisher-select2', 'https://cdn.jsdelivr.net/npm/select2/dist/js/select2.min.js' );

		// Get files.
		$suffix = maipub_get_suffix();
		$css    = "assets/css/mai-publisher-settings{$suffix}.css";
		$js     = "assets/js/mai-publisher-settings{$suffix}.js";

		// Enqueue.
		wp_enqueue_style( 'mai-publisher-settings', maipub_get_file_data( $css, 'url' ), [], maipub_get_file_data( $css, 'version' ) );
		wp_enqueue_script( 'mai-publisher-settings', maipub_get_file_data( $js, 'url' ), [ 'jquery', 'mai-publisher-select2' ], maipub_get_file_data( $js, 'version' ), [ 'in_footer' => true ] );
	}

	/**
	 * Adds setting page content.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	function add_content() {
		echo '<div class="wrap">';
			printf( '<h2>%s (%s)</h2>', __( 'Mai Publisher', 'mai-publisher' ), MAI_PUBLISHER_VERSION );
			printf( '<p>%s</p>', __( 'Settings and configuration for Mai Publisher.', 'mai-publisher' ) );
			echo '<form method="post" action="options.php">';
				settings_fields( 'maipub_group' );
				do_settings_sections( 'mai-publisher-section' );
				submit_button();
			echo '</form>';
		echo '</div>';
	}

	/**
	 * Initialize the settings.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	function init() {
		register_setting(
			'maipub_group', // option_group
			'mai_publisher', // option_name
			[ $this, 'maipub_sanitize' ] // sanitize_callback
		);

		/****************
		 * Set Defaults *
		 ****************/

		if ( is_null( get_option( 'mai_publisher', null ) ) ) {
			update_option( 'mai_publisher', maipub_get_default_options() );
		}

		/************
		 * Sections *
		 ************/

		add_settings_section(
			'maipub_settings', // id
			'', // title
			[ $this, 'maipub_section_info' ], // callback
			'mai-publisher-section' // page
		);

		add_settings_section(
			'maipub_settings_debug', // id
			'', // title
			[ $this, 'maipub_section_debug' ], // callback
			'mai-publisher-section' // page
		);

		add_settings_section(
			'maipub_settings_matomo', // id
			'', // title
			[ $this, 'maipub_section_matomo' ], // callback
			'mai-publisher-section' // page
		);

		add_settings_section(
			'maipub_settings_views', // id
			'', // title
			[ $this, 'maipub_section_views' ], // callback
			'mai-publisher-section' // page
		);

		add_settings_section(
			'maipub_settings_scripts', // id
			'', // title
			[ $this, 'maipub_section_scripts' ], // callback
			'mai-publisher-section' // page
		);

		/************
		 * Fields   *
		 ************/

		/**
		 * Ads
		 */

		add_settings_field(
			'ad_mode', // id
			__( 'Ad mode', 'mai-publisher' ), // title
			[ $this, 'ad_mode_callback' ], // callback
			'mai-publisher-section', // page
			'maipub_settings' // section
		);

		add_settings_field(
			'ad_label', // id
			__( 'Ad Label', 'mai-publisher' ), // title
			[ $this, 'ad_label_callback' ], // callback
			'mai-publisher-section', // page
			'maipub_settings' // section
		);

		add_settings_field(
			'gam_domain', // id
			__( 'GAM Domain', 'mai-publisher' ), // title
			[ $this, 'gam_domain_callback' ], // callback
			'mai-publisher-section', // page
			'maipub_settings' // section
		);

		add_settings_field(
			'gam_network_code', // id
			__( 'GAM Network Code', 'mai-publisher' ), // title
			[ $this, 'gam_network_code_callback' ], // callback
			'mai-publisher-section', // page
			'maipub_settings' // section
		);

		add_settings_field(
			'gam_hashed_domain', // id
			__( 'GAM Hashed Domain', 'mai-publisher' ), // title
			[ $this, 'gam_hashed_domain_callback' ], // callback
			'mai-publisher-section', // page
			'maipub_settings' // section
		);

		add_settings_field(
			'gam_sellers_name', // id
			__( 'GAM Sellers Name', 'mai-publisher' ), // title
			[ $this, 'gam_sellers_name_callback' ], // callback
			'mai-publisher-section', // page
			'maipub_settings' // section
		);

		add_settings_field(
			'gam_sellers_id', // id
			__( 'GAM Sellers ID', 'mai-publisher' ), // title
			[ $this, 'gam_sellers_id_callback' ], // callback
			'mai-publisher-section', // page
			'maipub_settings' // section
		);

		add_settings_field(
			'gam_targets', // id
			__( 'Key/Value Pairs', 'mai-publisher' ), // title
			[ $this, 'gam_targets_callback' ], // callback
			'mai-publisher-section', // page
			'maipub_settings' // section
		);

		add_settings_field(
			'sourcepoint_property_id', // id
			__( 'Sourcepoint Property ID', 'mai-publisher' ), // title
			[ $this, 'sourcepoint_property_id_callback' ], // callback
			'mai-publisher-section', // page
			'maipub_settings' // section
		);

		add_settings_field(
			'sourcepoint_tcf_message_id', // id
			__( 'Sourcepoint TCF Message ID', 'mai-publisher' ), // title
			[ $this, 'sourcepoint_tcf_message_id_callback' ], // callback
			'mai-publisher-section', // page
			'maipub_settings' // section
		);

		add_settings_field(
			'sourcepoint_msps_message_id', // id
			__( 'Sourcepoint MSPS Message ID', 'mai-publisher' ), // title
			[ $this, 'sourcepoint_msps_message_id_callback' ], // callback
			'mai-publisher-section', // page
			'maipub_settings' // section
		);

		add_settings_field(
			'category', // id
			__( 'Sitewide Category', 'mai-publisher' ), // title
			[ $this, 'category_callback' ], // callback
			'mai-publisher-section', // page
			'maipub_settings' // section
		);

		add_settings_field(
			'magnite_enabled', // id
			__( 'Magnite Demand Manager', 'mai-publisher' ), // title
			[ $this, 'magnite_enabled_callback' ], // callback
			'mai-publisher-section', // page
			'maipub_settings' // section
		);

		add_settings_field(
			'amazon_uam_enabled', // id
			__( 'Amazon UAM', 'mai-publisher' ), // title
			[ $this, 'amazon_uam_enabled_callback' ], // callback
			'mai-publisher-section', // page
			'maipub_settings' // section
		);

		/**
		 * Debugging
		 */

		add_settings_field(
			'load_delay', // id
			__( 'Ad load delay', 'mai-publisher' ), // title
			[ $this, 'load_delay_callback' ], // callback
			'mai-publisher-section', // page
			'maipub_settings_debug' // section
		);

		add_settings_field(
			'debug_enabled', // id
			__( 'Ad Debugging', 'mai-publisher' ), // title
			[ $this, 'debug_enabled_callback' ], // callback
			'mai-publisher-section', // page
			'maipub_settings_debug' // section
		);

		/**
		 * Analytics
		 */

		add_settings_field(
			'matomo_enabled_global', // id
			__( 'Enable Globally', 'mai-publisher' ), // title
			[ $this, 'matomo_enabled_global_callback' ], // callback
			'mai-publisher-section', // page
			'maipub_settings_matomo' // section
		);

		add_settings_field(
			'matomo_enabled', // id
			__( 'Enable', 'mai-publisher' ), // title
			[ $this, 'matomo_enabled_callback' ], // callback
			'mai-publisher-section', // page
			'maipub_settings_matomo' // section
		);

		// add_settings_field(
		// 	'matomo_enabled_backend', // id
		// 	__( 'Enable back-end tracking', 'mai-publisher' ), // title
		// 	[ $this, 'matomo_enabled_backend_callback' ], // callback
		// 	'mai-publisher-section', // page
		// 	'maipub_settings_matomo' // section
		// );

		add_settings_field(
			'matomo_url', // id
			__( 'Tracker URL', 'mai-publisher' ), // title
			[ $this, 'matomo_url_callback' ], // callback
			'mai-publisher-section', // page
			'maipub_settings_matomo' // section
		);

		add_settings_field(
			'matomo_site_id', // id
			__( 'Site ID', 'mai-publisher' ), // title
			[ $this, 'matomo_site_id_callback' ], // callback
			'mai-publisher-section', // page
			'maipub_settings_matomo' // section
		);

		add_settings_field(
			'matomo_token', // id
			__( 'Token', 'mai-publisher' ), // title
			[ $this, 'matomo_token_callback' ], // callback
			'mai-publisher-section', // page
			'maipub_settings_matomo' // section
		);

		/**
		 * Views
		 */

		add_settings_field(
			'views_api', // id
			__( 'Views API', 'mai-publisher' ), // title
			[ $this, 'views_api_callback' ], // callback
			'mai-publisher-section', // page
			'maipub_settings_views' // section
		);

		add_settings_field(
			'views_years', // id
			__( 'Total Views Years', 'mai-publisher' ), // title
			[ $this, 'views_years_callback' ], // callback
			'mai-publisher-section', // page
			'maipub_settings_views' // section
		);

		add_settings_field(
			'trending_days', // id
			__( 'Trending Days', 'mai-publisher' ), // title
			[ $this, 'trending_days_callback' ], // callback
			'mai-publisher-section', // page
			'maipub_settings_views' // section
		);


		add_settings_field(
			'views_interval', // id
			__( 'Trending/Popular Interval', 'mai-publisher' ), // title
			[ $this, 'views_interval_callback' ], // callback
			'mai-publisher-section', // page
			'maipub_settings_views' // section
		);

		/**
		 * Scripts
		 */

		add_settings_field(
			'header_scripts', // id
			__( 'Header Scripts', 'mai-publisher' ), // title
			[ $this, 'header_callback' ], // callback
			'mai-publisher-section', // page
			'maipub_settings_scripts' // section
		);

		add_settings_field(
			'footer', // id
			__( 'Footer Scripts', 'mai-publisher' ), // title
			[ $this, 'footer_callback' ], // callback
			'mai-publisher-section', // page
			'maipub_settings_scripts' // section
		);
	}

	/**
	 * Sanitized saved values.
	 *
	 * @param array $input
	 *
	 * @return array
	 */
	function maipub_sanitize( $input ) {
		$allowed = [
			'ad_mode'                     => 'sanitize_text_field',
			'gam_domain'                  => 'maipub_get_url_host',
			'gam_network_code'            => 'absint',
			'gam_hashed_domain'           => 'sanitize_text_field',
			'gam_sellers_id'              => 'sanitize_text_field',
			'gam_sellers_name'            => 'sanitize_text_field',
			'gam_targets'                 => 'sanitize_text_field',
			'sourcepoint_property_id'     => 'absint',
			'sourcepoint_msps_message_id' => 'absint',
			'sourcepoint_tcf_message_id'  => 'absint',
			'load_delay'                  => 'absint',
			'debug_enabled'               => 'rest_sanitize_boolean',
			'category'                    => 'sanitize_text_field',
			'magnite_enabled'             => 'rest_sanitize_boolean',
			'amazon_uam_enabled'          => 'rest_sanitize_boolean',
			'matomo_enabled_global'       => 'rest_sanitize_boolean',
			'matomo_enabled'              => 'rest_sanitize_boolean',
			'matomo_enabled_backend'      => 'rest_sanitize_boolean',
			'matomo_url'                  => 'trailingslashit',
			'matomo_site_id'              => 'absint',
			'matomo_token'                => 'sanitize_text_field',
			'views_api'                   => 'sanitize_text_field',
			'views_years'                 => 'absint',
			'trending_days'               => 'absint',
			'views_interval'              => 'absint',
			'ad_label'                    => 'sanitize_text_field',
			'header'                      => 'trim',
			'footer'                      => 'trim',
		];

		// Get an array of matching keys from $input.
		$input = array_intersect_key( $input, $allowed );

		// Sanitize.
		foreach ( $input as $key => $value ) {
			if ( is_null( $value ) || '' === $value ) {
				$input[ $key ] = '';
			} else {
				$input[ $key ] = $allowed[ $key ]( $value );
			}
		}

		// If gam_hashed_domain is empty, and we have a gam_domain value, set gam_hashed_domain to an encoded version of gam_domain.
		if ( empty( $input['gam_hashed_domain'] ) && ! empty( $input['gam_domain'] ) ) {
			$input['gam_hashed_domain'] = maipub_encode( $input['gam_domain'], 14 );
		}

		return $input;
	}

	/**
	 * Displays HTML before settings.
	 *
	 * @since 0.1.0
	 *
	 * @return string
	 */
	function maipub_section_info() {
		printf( '<h3 class="heading-tab"><span class="heading-tab__text">%s</span></h3>', __( 'Ads', 'mai-publisher' ) );
	}

	function maipub_section_debug() {
		printf( '<h3 class="heading-tab"><span class="heading-tab__text">%s</span></h3>', __( 'Debugging', 'mai-publisher' ) );
	}

	function maipub_section_matomo() {
		printf( '<h3 class="heading-tab"><span class="heading-tab__text">%s</span></h3>', __( 'Analytics', 'mai-publisher' ) );
	}

	function maipub_section_scripts() {
		printf( '<h3 class="heading-tab"><span class="heading-tab__text">%s</span></h3>', __( 'Scripts', 'mai-publisher' ) );
		printf( '<p>%s</p>', __( 'Scripts are only output in the header and footer if ad mode is active or demo.', 'mai-publisher' ) );
	}

	function maipub_section_views() {
		printf( '<h3 class="heading-tab"><span class="heading-tab__text">%s</span></h3>', __( 'Views', 'mai-publisher' ) );
	}

	/**
	 * Setting callback.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	function ad_mode_callback() {
		$selected = maipub_get_option( 'ad_mode' );

		echo '<select name="mai_publisher[ad_mode]">';
			printf( '<option value="active">%s</option>', __( 'Active', 'mai-publisher' ) );
			printf( '<option value="disabled"%s>%s</option>', selected( $selected, 'disabled' ), __( 'Disabled', 'mai-publisher' ) );
			printf( '<option value="demo"%s>%s</option>', selected( $selected, 'demo' ), __( 'Demo', 'mai-publisher' ) );
		echo '</select>';
	}

	/**
	 * Setting callback.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	function ad_label_callback() {
		printf( '<input class="regular-text" type="text" name="mai_publisher[ad_label]" id="ad_label" value="%s">', maipub_get_option( 'ad_label', false ) );
	}

	/**
	 * Setting callback.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	function gam_domain_callback() {
		printf( '<input class="regular-text" type="text" name="mai_publisher[gam_domain]" id="gam_domain" value="%s">', maipub_get_option( 'gam_domain', true ) );
	}

	/**
	 * Setting callback.
	 *
	 * @since 0.5.1
	 *
	 * @return void
	 */
	function gam_network_code_callback() {
		printf( '<input class="regular-text" type="text" name="mai_publisher[gam_network_code]" id="gam_network_code" value="%s">', maipub_get_option( 'gam_network_code', false ) );
	}

	/**
	 * Setting callback.
	 *
	 * @since 0.11.0?
	 *
	 * @return void
	 */
	function gam_hashed_domain_callback() {
		printf( '<input class="regular-text" type="text" name="mai_publisher[gam_hashed_domain]" id="gam_hashed_domain" value="%s">', maipub_get_option( 'gam_hashed_domain', true ) );
	}

	/**
	 * Setting callback.
	 *
	 * @since 0.23.0
	 *
	 * @return void
	 */
	function gam_sellers_name_callback() {
		$sellers_name = maipub_get_option( 'gam_sellers_name', false );

		printf( '<input class="regular-text" type="text" name="mai_publisher[gam_sellers_name]" id="gam_sellers_name" value="%s">', $sellers_name );
		printf( '<p class="description">%s <a target="_blank" href="https://bizbudding.com/sellers.json">sellers.json</a></p>', __( 'Must match', 'mai-publisher' ) );
	}

	/**
	 * Setting callback.
	 *
	 * @since 0.11.0?
	 *
	 * @return void
	 */
	function gam_sellers_id_callback() {
		$network_code  = maipub_get_option( 'gam_network_code', false );
		$sellers_id    = $network_code ? maipub_encode( $network_code ) : '';

		printf( '<input class="regular-text" type="text" name="mai_publisher[gam_sellers_id]" id="gam_sellers_id" value="%s" readonly>', $sellers_id );
		printf( '<p class="description">%s <a target="_blank" href="https://bizbudding.com/sellers.json">sellers.json</a></p>', __( 'Must match', 'mai-publisher' ) );
	}

	/**
	 * Setting callback.
	 *
	 * @since 0.13.0?
	 *
	 * @return void
	 */
	function gam_targets_callback() {
		printf( '<input class="regular-text" type="text" name="mai_publisher[gam_targets]" id="gam_targets" value="%s">', maipub_get_option( 'gam_targets', false ) );
		printf( '<p class="description">%s</p>', __( 'Comma-separated key value pairs. Example: a=b, d=f', 'mai-publisher' ) );
	}

	/**
	 * Setting callback.
	 *
	 * @since 1.6.6
	 *
	 * @return void
	 */
	function sourcepoint_property_id_callback() {
		$id  = maipub_get_option( 'sourcepoint_property_id', false );

		printf( '<input class="regular-text" type="text" name="mai_publisher[sourcepoint_property_id]" id="sourcepoint_property_id" value="%s">', $id );
		printf( '<p class="description">%s</p>', __( 'Adding a property ID here will automatically load the Sourcepoint scripts on pages with ads.', 'mai-publisher' ) );
	}

	/**
	 * Setting callback.
	 *
	 * @since 1.6.6
	 *
	 * @return void
	 */
	function sourcepoint_tcf_message_id_callback() {
		$id  = maipub_get_option( 'sourcepoint_tcf_message_id', false );

		printf( '<input class="regular-text" type="text" name="mai_publisher[sourcepoint_tcf_message_id]" id="sourcepoint_tcf_message_id" value="%s">', $id );
		// printf( '<p class="description">%s</p>', __( 'Adding a property ID here will automatically load the Sourcepoint scripts on pages with ads.', 'mai-publisher' ) );
	}

	/**
	 * Setting callback.
	 *
	 * @since 1.6.6
	 *
	 * @return void
	 */
	function sourcepoint_msps_message_id_callback() {
		$id  = maipub_get_option( 'sourcepoint_msps_message_id', false );

		printf( '<input class="regular-text" type="text" name="mai_publisher[sourcepoint_msps_message_id]" id="sourcepoint_msps_message_id" value="%s">', $id );
		// printf( '<p class="description">%s</p>', __( 'Adding a property ID here will automatically load the Sourcepoint scripts on pages with ads.', 'mai-publisher' ) );
	}

	/**
	 * Setting callback.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	function category_callback() {
		$selected = maipub_get_option( 'category' );

		echo '<div style="display:flex;align-items:center;gap:1em;">';
			echo '<select id="iab-category" name="mai_publisher[category]">';
				printf( '<option value="">%s</option>', __( 'Select a sitewide category...', 'mai-publisher' ) );
				foreach ( (array) maipub_get_all_iab_categories() as $id => $label ) {
					printf( '<option value="%s"%s>%s</option>', $id, selected( $selected, $id ), $label );
				}
			echo '</select>';
			printf( '<p><a target="_blank" href="%s">%s</a></p>', admin_url( 'edit.php?post_type=mai_ad&page=categories' ), __( 'View full category list', 'mai-publisher' ) );
		echo '</div>';
	}

	/**
	 * Setting callback.
	 *
	 * @since TBD
	 *
	 * @return void
	 */
	function magnite_enabled_callback() {
		$value = (bool) maipub_get_option( 'magnite_enabled', false );

		printf(
			'<input type="checkbox" name="mai_publisher[magnite_enabled]" id="magnite_enabled" %s> <label for="magnite_enabled">%s</label>',
			$value ? ' checked' : '',
			__( 'Enable Magnite bidding for ads', 'mai-publisher' )
		);
	}

	/**
	 * Setting callback.
	 *
	 * @since 0.11.0?
	 *
	 * @return void
	 */
	function amazon_uam_enabled_callback() {
		$value = (bool) maipub_get_option( 'amazon_uam_enabled', false );

		printf(
			'<input type="checkbox" name="mai_publisher[amazon_uam_enabled]" id="amazon_uam_enabled" %s> <label for="amazon_uam_enabled">%s</label>',
			$value ? ' checked' : '',
			__( 'Enable Amazon UAM bidding for ads', 'mai-publisher' )
		);
	}

	/**
	 * Setting callback.
	 *
	 * @since 1.9.0
	 *
	 * @return void
	 */
	function load_delay_callback() {
		$delay  = maipub_get_option( 'load_delay', false );

		printf( '<input class="regular-text" type="number" name="mai_publisher[load_delay]" id="load_delay" value="%s">', absint( $delay ) );
		printf( '<p class="description">%s</p>', __( 'Add a delay in ms (1000ms = 1s). A value here will also move the event from DOMContentLoaded to window load. Leave empty for no delay.', 'mai-publisher' ) );
	}

	/**
	 * Setting callback.
	 *
	 * @since 1.9.0
	 *
	 * @return void
	 */
	function debug_enabled_callback() {
		$value = (bool) maipub_get_option( 'debug_enabled', false );

		printf(
			'<input type="checkbox" name="mai_publisher[debug_enabled]" id="debug_enabled" %s> <label for="debug_enabled">%s</label>',
			$value ? ' checked' : '',
			__( 'Enable console logging for this website.', 'mai-publisher' )
		);
	}

	/**
	 * Setting callback.
	 *
	 * @since 0.3.0
	 *
	 * @return void
	 */
	function matomo_enabled_global_callback() {
		$constant = defined( 'MAI_PUBLISHER_MATOMO_ENABLED_GLOBAL' );
		$value    = $constant ? rest_sanitize_boolean( MAI_PUBLISHER_MATOMO_ENABLED_GLOBAL ) : (bool) maipub_get_option( 'matomo_enabled_global', false );

		printf(
			'<input type="checkbox" name="mai_publisher[matomo_enabled_global]" id="matomo_enabled_global" %s%s> <label for="matomo_enabled_global">%s%s</label>',
			$value ? ' checked' : '',
			$constant ? ' disabled' : '',
			__( 'Enable global tracking for the Mai Publisher Network.', 'mai-publisher' ),
			$constant ? ' ' . $this->config_notice() : ''
		);

		// Can't get connection info because we need a token and the repo is currently public so updates work.
		// $this->connection_info( 'https://bizbudding.info/', maipub_get_option( 'matomo_token' ), '123456' );
	}

	/**
	 * Setting callback.
	 *
	 * @since 0.3.0
	 *
	 * @return void
	 */
	function matomo_enabled_callback() {
		$constant = defined( 'MAI_PUBLISHER_MATOMO_ENABLED' );
		$value    = $constant ? rest_sanitize_boolean( MAI_PUBLISHER_MATOMO_ENABLED ) : (bool) maipub_get_option( 'matomo_enabled', false );

		printf(
			'<input type="checkbox" name="mai_publisher[matomo_enabled]" id="matomo_enabled" %s%s> <label for="matomo_enabled">%s%s</label><br><br>',
			$value ? ' checked' : '',
			$constant ? ' disabled' : '',
			__( 'Enable tracking for this website.', 'mai-publisher' ),
			$constant ? ' ' . $this->config_notice() : ''
		);

		$this->connection_info( maipub_get_option( 'matomo_url' ), maipub_get_option( 'matomo_token' ), maipub_get_option( 'matomo_enabled' ) );
	}

	/**
	 * Setting callback.
	 *
	 * @since 0.3.0
	 *
	 * @return void
	 */
	// function matomo_enabled_backend_callback() {
	// 	$constant = defined( 'MAI_PUBLISHER_MATOMO_ENABLED_BACKEND' );
	// 	$value    = $constant ? rest_sanitize_boolean( MAI_PUBLISHER_MATOMO_ENABLED_BACKEND ) : maipub_get_option( 'matomo_enabled_backend', false );

	// 	printf(
	// 		'<input type="checkbox" name="mai_publisher[matomo_enabled_backend]" id="matomo_enabled_backend" value="matomo_enabled_backend"%s%s> <label for="matomo_enabled_backend">%s%s</label>',
	// 		$value ? ' checked' : '',
	// 		$constant ? ' disabled' : '',
	// 		__( 'Enable tracking in the WordPress Dashboard.', 'mai-publisher' ),
	// 		$constant ? ' ' . $this->config_notice() : ''
	// 	);
	// }

	/**
	 * Setting callback.
	 *
	 * @since 0.3.0
	 *
	 * @return void
	 */
	function matomo_url_callback() {
		$constant = defined( 'MAI_PUBLISHER_MATOMO_URL' );
		$value    = $constant ? trailingslashit( esc_url( MAI_PUBLISHER_MATOMO_URL ) ) : maipub_get_option( 'matomo_url', false );

		printf(
			'<input class="regular-text" type="text" name="mai_publisher[matomo_url]" id="matomo_url" value="%s"%s>%s',
			$value,
			$constant ? ' disabled' : '',
			$constant ? ' ' . $this->config_notice() : ''
		);
	}

	/**
	 * Setting callback.
	 *
	 * @since 0.3.0
	 *
	 * @return void
	 */
	function matomo_site_id_callback() {
		$constant = defined( 'MAI_PUBLISHER_MATOMO_SITE_ID' );
		$value    = $constant ? absint( MAI_PUBLISHER_MATOMO_SITE_ID ) : maipub_get_option( 'matomo_site_id', false );

		printf(
			'<input class="regular-text" type="number" name="mai_publisher[matomo_site_id]" id="matomo_site_id" value="%s"%s>%s',
			$value,
			$constant ? ' disabled' : '',
			$constant ? ' ' . $this->config_notice() : ''
		);
	}

	/**
	 * Setting callback.
	 *
	 * @since 0.3.0
	 *
	 * @return void
	 */
	function matomo_token_callback() {
		$constant = defined( 'MAI_PUBLISHER_MATOMO_TOKEN' );
		$value    = $constant ? sanitize_text_field( MAI_PUBLISHER_MATOMO_TOKEN ) : maipub_get_option( 'matomo_token', false );

		printf(
			'<input class="regular-text" type="password" name="mai_publisher[matomo_token]" id="matomo_token" value="%s"%s>%s',
			$value,
			$constant ? ' disabled' : '',
			$constant ? ' ' . $this->config_notice() : ''
		);
	}

	/**
	 * Setting callback.
	 *
	 * @since 0.12.0
	 *
	 * @return void
	 */
	function views_api_callback() {
		$selected = maipub_get_option( 'views_api', false );

		echo '<select name="mai_publisher[views_api]">';
		printf( '<option value="matomo"%s>%s</option>', selected( $selected, 'matomo' ), __( 'Matomo', 'mai-publisher' ) );
		printf( '<option value="jetpack"%s>%s</option>', selected( $selected, 'jetpack' ), __( 'Jetpack', 'mai-publisher' ) );
		printf( '<option value="disabled"%s>%s</option>', selected( $selected, 'disabled' ), __( 'Disabled', 'mai-publisher' ) );
		echo '</select>';

		if ( 'jetpack' === $selected ) {
			$connected = class_exists( 'Jetpack' ) && Jetpack::is_module_active( 'stats' );
			$color     = $connected ? '#00a32a' : '#d63638';
			$text      = $connected ? __( ' Jetpack Stats is running', 'mai-publisher' ) : __( 'Jetpack Stats is not running', 'mai-publisher' );

			printf( '<span style="margin-left:8px;color:%s;">%s</span>', $color, $text );
		}

		printf( '<p class="description">%s</p>', __( 'Select the API to use for fetching post views. If Matomo is used, it must be enabled and connected in the Analytics settings. If Jetpack is used, Jetpack and the Stats module must both be active.', 'mai-publisher' ) );

	}

	/**
	 * Setting callback.
	 *
	 * @since 0.12.0
	 *
	 * @return void
	 */
	function views_years_callback() {
		printf(
			'<input class="small-text" type="number" name="mai_publisher[views_years]" id="views_years" value="%s"> %s',
			maipub_get_option( 'views_years' ),
			__( 'years', 'mai-publisher' ),
		);

		printf( '<p class="description">%s %s %s</p>',
			__( 'Retrieve total post views going back this many years.', 'mai-publisher' ),
			__( 'Use 0 to disable fetching total views.', 'mai-publisher' ),
			__( 'Values are stored in the <code>mai_views</code> meta key.', 'mai-publisher' )
		);
	}

	/**
	 * Setting callback.
	 *
	 * @since 0.4.0
	 *
	 * @return void
	 */
	function trending_days_callback() {
		printf(
			'<input class="small-text" type="number" name="mai_publisher[trending_days]" id="trending_days" value="%s"> %s',
			maipub_get_option( 'trending_days' ),
			__( 'days', 'mai-publisher' ),
		);

		printf( '<p class="description">%s %s %s</p>',
			__( 'Retrieve trending post views going back this many days.', 'mai-publisher' ),
			__( 'Use 0 to disable fetching trending post views.', 'mai-publisher' ),
			__( 'Values are stored in the <code>mai_trending</code> meta key.', 'mai-publisher' )
		);
	}

	/**
	 * Setting callback.
	 *
	 * @since 0.4.0
	 *
	 * @return void
	 */
	function views_interval_callback() {
		printf(
			'<input class="small-text" type="number" name="mai_publisher[views_interval]" id="views_interval" value="%s"> %s',
			maipub_get_option( 'views_interval' ),
			__( 'minutes', 'mai-publisher' ),
		);

		printf( '<p class="description">%s %s</p>',
			__( 'Wait this long between fetching the view counts for a given post.', 'mai-publisher' ),
			__( 'Views are only fetched when a post is visited on the front end of the site.', 'mai-publisher' )
		);
	}

	/**
	 * Setting callback.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	function header_callback() {
		printf( '<textarea name="mai_publisher[header]" id="header" rows="16" style="width:100%%;max-width:600px">%s</textarea>', maipub_get_option( 'header' ) );
	}

	/**
	 * Setting callback.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	function footer_callback() {
		printf( '<textarea name="mai_publisher[footer]" id="footer" rows="16" style="width:100%%;max-width:600px">%s</textarea>', maipub_get_option( 'footer' ) );
	}

	/**
	 * Checks if connection is valid.
	 *
	 * @link https://matomo.org/faq/how-to/faq_20278/
	 *
	 * @since 0.3.0
	 *
	 * @param string $url     The Matomo URL.
	 * @param string $token   The Matomo token.
	 * @param bool   $enabled If tracking is enabled.
	 *
	 * @return void
	 */
	function connection_info( $url, $token, $enabled ) {
		if ( ! ( $url && $token ) ) {
			return;
		}

		// Start notices.
		$notices = [];

		// If enabled.
		if ( $enabled ) {
			// Build connection urls.
			$connections = [
				// 'Matomo Version' => $url . sprintf( 'index.php?module=API&method=API.getMatomoVersion&format=JSON&token_auth=%s', $token ),
				'Matomo Version' => $url . 'index.php?module=API&method=API.getMatomoVersion&format=JSON',
				'Matomo Tracker' => $url . 'matomo.php',
			];

			$first = true;

			// Check each connection.
			foreach ( $connections as $label => $url ) {
				// Handle first request.
				if ( $first ) {
					$response = wp_remote_post( $url, [ 'body' => [ 'token_auth' => $token ] ] );
					$first    = false;
				}
				// Not first.
				else {
					$response = wp_remote_get( $url );
				}

				// If error, add error message.
				if ( is_wp_error( $response ) ) {
					$type    = 'error';
					$message = $response->get_error_message();
				}
				// Success.
				else {
					$body   = wp_remote_retrieve_body( $response );
					$decode = json_decode( $body, true );

					// If we have JSON, and no errors.
					if ( ! is_null( $decode ) && json_last_error() === JSON_ERROR_NONE ) {
						$body = $decode;
					}

					// Get response code.
					$code = wp_remote_retrieve_response_code( $response );

					// If not 200, it's an error.
					if ( 200 !== $code ) {
						$type    = 'error';
						$message =  $code . ' ' . wp_remote_retrieve_response_message( $response );

						// Add Matomo message if available.
						if ( is_array( $body ) && isset( $body['result'] ) && isset( $body['message'] ) ) {
							$message .= '. ' . $body['message'];
						}
					}

					/**********************************
					 * Handle various response types. *
					 **********************************/

					// String response for matomo.php.
					elseif ( is_string( $body ) ) {
						$type    = 'success';
						$message = __( 'Connected', 'mai-publisher' );
					}
					// The getMatomoVersion response should be the version value.
					elseif ( is_array( $body ) && isset( $body['value'] ) ) {
						$type    = 'success';
						$message = $body['value'];
					}
					// We hit this at one point when things were throwing errors.
					elseif ( is_array( $body ) && isset( $body['result'] ) && isset( $body['message'] ) ) {
						$type    = 'error' === $body['result'] ? 'error' : 'success';
						$message = $body['message'];
					}

					// Add notice.
					$notices[] = [
						'type'    => $type,
						'label'   => $label,
						'message' => $message,
					];
				}

				// Stop checking if we have an error.
				if ( 'error' === $type ) {
					break;
				}
			}
		}
		// Tracking not enabled.
		else {
			$notices[] = [
				'type'    => 'warning',
				'label'   => 'Matomo',
				'message' => __( 'Tracking is disabled.', 'mai-publisher' ),
			];
		}

		// Display notices.
		foreach ( $notices as $notice ) {
			// WP default colors.
			switch ( $notice['type'] ) {
				case 'success':
					$color = '#00a32a';
				break;
				case 'warning':
					$color = '#dba617';
				break;
				case 'error':
					$color = '#d63638';
				break;
				default:
					$color = 'blue'; // This should never happen.
			}

			printf( '<div style="color:%s;">%s: %s</div>', $color, $notice['label'], wp_kses_post( $notice['message'] ) );
		}
	}

	/**
	 * Gets notice for when config values are used.
	 *
	 * @since 0.3.0
	 *
	 * @return string
	 */
	function config_notice() {
		return sprintf( '<span style="color:green;">%s</span>', __( 'Overridden in wp-config.php', 'mai-publisher' ) );
	}

	/**
	 * Return the plugin action links.  This will only be called if the plugin is active.
	 *
	 * @since 0.1.0
	 *
	 * @param array  $actions     Associative array of action names to anchor tags.
	 * @param string $plugin_file Plugin file name, ie my-plugin/my-plugin.php.
	 * @param array  $plugin_data Associative array of plugin data from the plugin file headers.
	 * @param string $context     Plugin status context, ie 'all', 'active', 'inactive', 'recently_active'.
	 *
	 * @return array associative array of plugin action links.
	 */
	function add_plugin_links( $actions, $plugin_file, $plugin_data, $context ) {
		$actions['ads']      = sprintf( '<a href="%s">%s</a>', esc_url( admin_url( 'edit.php?post_type=mai_ad' ) ), __( 'Ads', 'mai-publisher' ) );
		$actions['settings'] = sprintf( '<a href="%s">%s</a>', esc_url( admin_url( 'edit.php?post_type=mai_ad&page=settings' ) ), __( 'Settings', 'mai-publisher' ) );

		return $actions;
	}
}