<?php

// Prevent direct file access.
defined( 'ABSPATH' ) || die;

/**
 * Checks if current page is an singular.
 *
 * @since 0.1.0
 *
 * @return bool
 */
function maipub_is_singular() {
	return is_singular();
}

/**
 * Checks if current page is an archive.
 *
 * @since 0.1.0
 *
 * @return bool
 */
function maipub_is_archive() {
	return is_home() || is_post_type_archive() || is_category() || is_tag() || is_tax() || is_search() || maipub_is_product_archive();
}

/**
 * Checks if current page is a WooCommerce shop.
 *
 * @since 1.2.0
 *
 * @return bool
 */
function maipub_is_shop_archive() {
	return class_exists( 'WooCommerce' ) && is_shop();
}

/**
 * Checks if current page is a WooCommerce product archive.
 *
 * @since 1.2.0
 *
 * @return bool
 */
function maipub_is_product_archive() {
	return class_exists( 'WooCommerce' ) && ( is_shop() || is_product_taxonomy() );
}

/**
 * Checks if current page is a WooCommerce single product.
 *
 * @since 1.2.0
 *
 * @return bool
 */
function maipub_is_product_singular() {
	return class_exists( 'WooCommerce' ) && is_product();
}

/**
 * Checks if current page has a sidebar.
 *
 * @since 0.12.0
 *
 * @return bool
 */
function maipub_has_sidebar() {
	static $cache = null;

	if ( ! is_null( $cache ) ) {
		return $cache;
	}

	$mai_sidebar     = function_exists( 'mai_has_sidebar' ) && mai_has_sidebar();
	$genesis_sidebar = function_exists( 'genesis_site_layout' ) && in_array( genesis_site_layout(), [ 'sidebar', 'sidebar-alt' ] );
	$cache           = $mai_sidebar || $genesis_sidebar;

	return $cache;
}

/**
 * Checks if a post has a recipe in it.
 *
 * @since 0.12.1
 *
 * @param int $post_id The post ID.
 *
 * @return bool
 */
function maipub_has_recipe( $post_id = 0 ) {
	static $cache = null;

	// Check for cache.
	if ( ! is_null( $cache ) && isset( $cache[ $post_id ] ) ) {
		return $cache[ $post_id ];
	}

	// Set array.
	if ( ! is_array( $cache ) ) {
		$cache = [];
	}

	// Maybe get post ID.
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}

	// Bail if no recipe plugin.
	if ( ! class_exists( 'WP_Recipe_Maker' ) ) {
		$cache[ $post_id ] = false;
		return $cache[ $post_id ];
	}

	// Get recipes.
	$cache[ $post_id ] = (bool) WPRM_Recipe_Manager::get_recipe_ids_from_post( $post_id );

	return $cache[ $post_id ];
}

/**
 * Check if a string contains at least one specified string.
 *
 * @since 0.1.0
 *
 * @param string       $haystack String to check in.
 * @param string|array $needle   String or array of strings to check for.
 *
 * @return string
 */
function maipub_str_contains( $haystack, $needle ) {
	if ( ! $haystack ) {
		return false;
	}

	if ( is_array( $needle ) ) {
		foreach ( $needle as $string ) {
			if ( str_contains( $haystack, $string ) ) {
				return true;
			}
		}

		return false;
	}

	return str_contains( $haystack, $needle );
}

/**
 * If on a page that we should be tracking.
 *
 * @access private
 *
 * @since 0.3.0
 *
 * @return bool
 */
function maipub_should_track() {
	static $cache = null;

	if ( ! is_null( $cache ) ) {
		return $cache;
	}

	$cache          = false;
	$enabled_global = maipub_get_option( 'matomo_enabled_global', false );
	$enabled        = maipub_get_option( 'matomo_enabled', false );

	// Bail if not enabled.
	if ( ! ( $enabled_global || $enabled ) ) {
		return $cache;
	}

	// If not enabled globally, check if we have hte data we need.
	if ( ! $enabled_global ) {
		$site_url = maipub_get_option( 'matomo_url', false );
		$site_id  = maipub_get_option( 'matomo_site_id', false );

		// Bail if we don't have the data we need.
		if ( ! ( $site_url && $site_id ) ) {
			return $cache;
		}
	}

	// Bail if contributor or above.
	if ( current_user_can( 'edit_posts' ) ) {
		return $cache;
	}

	// Bail if we are in an ajax call.
	if ( wp_doing_ajax() ) {
		return $cache;
	}

	// Bail if this is a JSON request.
	if ( wp_is_json_request() ) {
		return $cache;
	}

	// Bail if this running via a CLI command.
	if ( defined( 'WP_CLI' ) && WP_CLI ) {
		return $cache;
	}

	// Bail if admin page and we're not tracking.
	// if ( ! maipub_get_option( 'matomo_enabled_backend' ) && is_admin() ) {
	// 	return $cache;
	// }

	// We got here, set cache and let's track it.
	$cache = true;

	return $cache;
}