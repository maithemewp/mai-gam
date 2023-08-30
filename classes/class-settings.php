<?php

// Prevent direct file access.
defined( 'ABSPATH' ) || die;

/**
 * Adds settings page.
 *
 * Original code generated by the WordPress Option Page generator:
 * @link http://jeremyhixon.com/wp-tools/option-page/
 */
class Mai_GAM_Settings {
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
	public function hooks() {
		add_action( 'admin_menu',                              [ $this, 'add_menu_item' ], 12 );
		add_action( 'admin_init',                              [ $this, 'init' ] );
		add_filter( 'plugin_action_links_mai-gam/mai-gam.php', [ $this, 'add_plugin_links' ], 10, 4 );
	}

	/**
	 * Adds menu item for settings page.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function add_menu_item() {
		add_submenu_page(
			'edit.php?post_type=mai_ad',
			__( 'Mai GAM', 'mai-gam' ), // page_title
			__( 'Settings', 'mai-gam' ), // menu_title
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
	public function add_content() {
		echo '<div class="wrap">';
			printf( '<h2>%s</h2>', __( 'Mai GAM', 'mai-gam' ) );
			// printf( '<p class="description">%s</p>', __( '', 'mai-gam' ) );

			echo '<form method="post" action="options.php">';
				settings_fields( 'maigam_group' );
				do_settings_sections( 'mai-gam-section' );
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
	public function init() {
		register_setting(
			'maigam_group', // option_group
			'mai_gam', // option_name
			[ $this, 'maigam_sanitize' ] // sanitize_callback
		);

		add_settings_section(
			'maigam_settings', // id
			'', // title
			[ $this, 'maigam_section_info' ], // callback
			'mai-gam-section' // page
		);

		add_settings_field(
			'domain', // id
			__( 'GAM Domain', 'mai-gam' ), // title
			[ $this, 'domain_callback' ], // callback
			'mai-gam-section', // page
			'maigam_settings' // section
		);

		add_settings_field(
			'label', // id
			__( 'Ad Label', 'mai-gam' ), // title
			[ $this, 'label_callback' ], // callback
			'mai-gam-section', // page
			'maigam_settings' // section
		);
	}

	/**
	 * Sanitized saved values.
	 *
	 * @param array $input
	 *
	 * @return array
	 */
	public function maigam_sanitize( $input ) {
		// Sanitize.
		$input['domain'] = maigam_sanitize_domain( $input['domain'] );
		$input['label']  = esc_html( $input['label'] );

		return $input;
	}

	/**
	 * Displays HTML before settings.
	 *
	 * @since 0.1.0
	 *
	 * @return string
	 */
	public function maigam_section_info() {}

	/**
	 * Setting callback.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function domain_callback() {
		printf( '<input class="regular-text" type="text" name="mai_gam[domain]" id="domain" value="%s">', maigam_get_domain() );
	}

	/**
	 * Setting callback.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	public function label_callback() {
		printf( '<input class="regular-text" type="text" name="mai_gam[label]" id="label" value="%s">', maigam_get_option( 'label', __( 'Advertisement', 'gam' ) ) );
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
	public function add_plugin_links( $actions, $plugin_file, $plugin_data, $context ) {
		$actions['ads']      = sprintf( '<a href="%s">%s</a>', esc_url( admin_url( 'edit.php?post_type=mai_ad' ) ), __( 'Ads', 'mai-gam' ) );
		$actions['settings'] = sprintf( '<a href="%s">%s</a>', esc_url( admin_url( 'edit.php?post_type=mai_ad&page=settings' ) ), __( 'Settings', 'mai-gam' ) );

		return $actions;
	}
}