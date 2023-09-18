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
	 * Adds setting page content.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	function add_content() {
		echo '<div class="wrap">';
			printf( '<h2>%s</h2>', __( 'Mai Publisher', 'mai-publisher' ) );

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

		add_settings_section(
			'maipub_settings', // id
			'', // title
			[ $this, 'maipub_section_info' ], // callback
			'mai-publisher-section' // page
		);

		add_settings_field(
			'label', // id
			__( 'Ad Label', 'mai-publisher' ), // title
			[ $this, 'label_callback' ], // callback
			'mai-publisher-section', // page
			'maipub_settings' // section
		);

		add_settings_field(
			'gam_domain', // id
			__( 'GAM Domain', 'mai-publisher' ), // title
			[ $this, 'domain_callback' ], // callback
			'mai-publisher-section', // page
			'maipub_settings' // section
		);

		add_settings_field(
			'matomo_token', // id
			__( 'Matomo Token', 'mai-publisher' ), // title
			[ $this, 'token_callback' ], // callback
			'mai-publisher-section', // page
			'maipub_settings' // section
		);

		add_settings_field(
			'header_scripts', // id
			__( 'Header Scripts', 'mai-publisher' ), // title
			[ $this, 'header_callback' ], // callback
			'mai-publisher-section', // page
			'maipub_settings' // section
		);

		add_settings_field(
			'footer', // id
			__( 'Footer Scripts', 'mai-publisher' ), // title
			[ $this, 'footer_callback' ], // callback
			'mai-publisher-section', // page
			'maipub_settings' // section
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
		// Sanitize.
		$input['gam_domain'] = isset( $input['gam_domain'] ) ? maipub_get_gam_domain_sanitized( $input['gam_domain'] ) : '';
		$input['label']      = isset( $input['label'] ) ? esc_html( $input['label'] ) : '';
		$input['header']     = current_user_can( 'unfiltered_html' ) ? trim( $input['header'] ) : wp_kses_post( trim( $input['header'] ) );
		$input['footer']     = current_user_can( 'unfiltered_html' ) ? trim( $input['footer'] ) : wp_kses_post( trim( $input['footer'] ) );

		return $input;
	}

	/**
	 * Displays HTML before settings.
	 *
	 * @since 0.1.0
	 *
	 * @return string
	 */
	function maipub_section_info() {}

	/**
	 * Setting callback.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	function label_callback() {
		printf( '<input class="regular-text" type="text" name="mai_publisher[label]" id="label" value="%s">', maipub_get_option( 'label', false ) );
	}

	/**
	 * Setting callback.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	function domain_callback() {
		printf( '<input class="regular-text" type="text" name="mai_publisher[domain]" id="domain" value="%s">', maipub_get_default_option( 'gam_domain' ), maipub_get_gam_domain( false ) );
	}

	/**
	 * Setting callback.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function token_callback() {
		printf( '<input class="regular-text" type="password" name="mai_publisher[matomo_token]" id="matomo_token" value="%s">', maipub_get_option( 'matomo_token', false ) );
	}

	/**
	 * Setting callback.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	function header_callback() {
		printf( '<textarea name="mai_publisher[header]" id="header" rows="8" style="width:100%%;max-width:600px">%s</textarea>', maipub_get_option( 'header' ) );
	}

	/**
	 * Setting callback.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	function footer_callback() {
		printf( '<textarea name="mai_publisher[footer]" id="footer" rows="8" style="width:100%%;max-width:600px">%s</textarea>', maipub_get_option( 'footer' ) );
	}

	/**
	 * Return the plugin action links.  This will only be called if the plugin is active.
	 *
	 * @since 0.1.0
	 *
	 * @param array  $actions     Associative array of action names to anchor tags
	 * @param string $plugin_file Plugin file name, ie my-plugin/my-plugin.php
	 * @param array  $plugin_data Associative array of plugin data from the plugin file headers
	 * @param string $context     Plugin status context, ie 'all', 'active', 'inactive', 'recently_active'
	 *
	 * @return array associative array of plugin action links
	 */
	function add_plugin_links( $actions, $plugin_file, $plugin_data, $context ) {
		$actions['ads']      = sprintf( '<a href="%s">%s</a>', esc_url( admin_url( 'edit.php?post_type=mai_ad' ) ), __( 'Ads', 'mai-publisher' ) );
		$actions['settings'] = sprintf( '<a href="%s">%s</a>', esc_url( admin_url( 'edit.php?post_type=mai_ad&page=settings' ) ), __( 'Settings', 'mai-publisher' ) );

		return $actions;
	}
}