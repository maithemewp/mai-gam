<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

class Mai_Publisher_Views {
	/**
	 * Construct the class.
	 *
	 * @return void
	 */
	function __construct() {
		$this->hooks();
	}

	/**
	 * Runs hooks.
	 *
	 * @since 0.4.0
	 *
	 * @return void
	 */
	function hooks() {
		// Register meta.
		add_action( 'init', [ $this, 'register_meta' ] );

		// Shortcode.
		add_shortcode( 'mai_views', [ $this, 'add_shortcode' ] );

		// Mai Post Grid filters.
		add_filter( 'acf/load_field/key=mai_grid_block_query_by',                 [ $this, 'add_trending_choice' ] );
		add_filter( 'acf/load_field/key=mai_grid_block_posts_orderby',            [ $this, 'add_views_choice' ] );
		add_filter( 'acf/load_field/key=mai_grid_block_post_taxonomies',          [ $this, 'add_show_conditional_logic' ] );
		add_filter( 'acf/load_field/key=mai_grid_block_post_taxonomies_relation', [ $this, 'add_show_conditional_logic' ] );
		add_filter( 'acf/load_field/key=mai_grid_block_posts_orderby',            [ $this, 'add_hide_conditional_logic' ] );
		add_filter( 'acf/load_field/key=mai_grid_block_posts_order',              [ $this, 'add_hide_conditional_logic' ] );

		// Mai Tax Grid filters.
		add_filter( 'acf/load_field/key=mai_grid_block_tax_query_by',             [ $this, 'add_trending_choice' ] );
		add_filter( 'acf/load_field/key=mai_grid_block_tax_orderby',              [ $this, 'add_views_choice' ] );
		add_filter( 'acf/load_field/key=mai_grid_block_tax_orderby',              [ $this, 'add_hide_conditional_logic' ] );
		add_filter( 'acf/load_field/key=mai_grid_block_tax_order',                [ $this, 'add_hide_conditional_logic' ] );

		// Mai Trending Post is priorty 20. This takes over for any legacy sites still running that plugin.
		add_filter( 'mai_post_grid_query_args', [ $this, 'edit_query' ], 30, 2 );
		add_filter( 'mai_term_grid_query_args', [ $this, 'edit_query' ], 30, 2 );

		// Update.
		add_action( 'wp_ajax_maipub_views',        [ $this, 'update_views' ] );
		add_action( 'wp_ajax_nopriv_maipub_views', [ $this, 'update_views' ] );
	}

	/**
	 * Registers the post meta keys.
	 *
	 * @since TBD
	 *
	 * @return void
	 */
	function register_meta() {
		register_meta( 'post', 'mai_trending', [
			'type'              => 'integer',
			'description'       => __( 'The number of views in the last n days.', 'mai-publisher' ),
			'sanitize_callback' => 'absint',
			'single'            => true,
			'show_in_rest'      => true,
		]);

		register_meta( 'post', 'mai_views', [
			'type'              => 'integer',
			'description'       => __( 'The total number of views', 'mai-publisher' ),
			'sanitize_callback' => 'absint',
			'single'            => true,
			'show_in_rest'      => true,
		]);

		register_meta( 'post', 'mai_views_updated', [
			'type'              => 'integer',
			'description'       => __( 'The last updated timestamp', 'mai-publisher' ),
			'sanitize_callback' => 'absint',
			'single'            => true,
			'show_in_rest'      => true,
		]);
	}

	/**
	 * Adds shortcode to display views.
	 * Bail if shouldn't run. This makes sure views do not display if Jetpack/Stats are not running.
	 *
	 * @since 0.1.0
	 *
	 * @return string
	 */
	function add_shortcode( $atts ) {
		return maipub_get_views( $atts );
	}

	/**
	 * Adds Trending as an "Get Entries By" choice.
	 *
	 * @since 0.4.0
	 *
	 * @param array $field The existing field array.
	 *
	 * @return array
	 */
	function add_trending_choice( $field ) {
		$field['choices'][ 'trending' ] = __( 'Trending', 'mai-publisher' ) . ' (Mai Publisher)';

		return $field;
	}

	/**
	 * Adds Views as an "Order By" choice.
	 *
	 * @since 0.1.0
	 *
	 * @param array $field The existing field array.
	 *
	 * @return array
	 */
	function add_views_choice( $field ) {
		$field['choices'] = array_merge( [ 'views' => __( 'Views', 'mai-publisher' ) . ' (Mai Analytics)' ], $field['choices'] );

		return $field;
	}

	/**
	 * Adds conditional logic to show if query by is trending.
	 * This duplicates existing conditions and changes query_by from 'tax_meta' to 'trending'.
	 *
	 * @since 0.4.0
	 *
	 * @param array $field The existing field array.
	 *
	 * @return array
	 */
	function add_show_conditional_logic( $field ) {
		$conditions = [];

		foreach ( $field['conditional_logic'] as $index => $values ) {
			$condition = $values;

			if ( isset( $condition['field'] ) && 'mai_grid_block_query_by' === $condition['field'] ) {
				$condition['value']    = 'trending';
				$condition['operator'] = '==';
			}

			$conditions[] = $condition;
		};

		$field['conditional_logic'] = $conditions ? [ $field['conditional_logic'], $conditions ] : $field['conditional_logic'];

		return $field;
	}

	/**
	 * Adds conditional logic to hide if query by is trending in Mai Post Grid.
	 *
	 * @since 0.4.0
	 *
	 * @param array $field The existing field array.
	 *
	 * @return array
	 */
	function add_hide_conditional_logic( $field ) {
		$key = false !== strpos( $field['key'], '_tax_' ) ? 'mai_grid_block_tax_query_by' : 'mai_grid_block_query_by';

		$field['conditional_logic'][] = [
			'field'    => $key,
			'operator' => '!=',
			'value'    => 'trending',
		];

		return $field;
	}

	/**
	 * Modify Mai Post Grid query args.
	 *
	 * @since 0.4.0
	 *
	 * @return array
	 */
	function edit_query( $query_args, $args ) {
		if ( isset( $args['query_by'] ) && $args['query_by'] && 'trending' === $args['query_by'] ) {
			$query_args['meta_key'] = 'mai_trending';
			$query_args['orderby']  = 'meta_value_num';
			$query_args['order']    = 'DESC';
		}

		if ( isset( $args['orderby'] ) && $args['orderby'] && 'views' === $args['orderby'] ) {
			$query_args['meta_key'] = 'mai_views';
			$query_args['orderby']  = 'meta_value_num';
		}

		return $query_args;
	}

	/**
	 * Update post/term trending and popular view counts via ajax.
	 *
	 * @since 0.4.0
	 *
	 * @return void
	 */
	function update_views() {
		// Bail if failed nonce check.
		if ( false === check_ajax_referer( 'maipub_views_nonce', 'nonce' ) ) {
			wp_send_json_error( __( 'Invalid nonce.', 'mai-publisher' ) );
			exit();
		}

		// Get options.
		$site_url      = maipub_get_option( 'matomo_url' );
		$site_id       = maipub_get_option( 'matomo_site_id' );
		$token         = maipub_get_option( 'matomo_token' );
		$views_days    = maipub_get_option( 'views_days', false );
		$trending_days = maipub_get_option( 'trending_days', false );
		$interval      = maipub_get_option( 'views_interval', false );

		// Bail if no API data.
		if ( ! ( $site_url && $site_id && $token ) ) {
			wp_send_json_error( __( 'Missing Matomo API data.', 'mai-publisher' ) );
			exit();
		}

		// Bail if nothing to fetch.
		if ( ! ( ( $trending_days || $views_days ) && $interval ) ) {
			wp_send_json_error( __( 'Missing views or trending days or interval.', 'mai-publisher' ) );
			exit();
		}

		// Get post data.
		$type     = isset( $_POST['type'] ) ? sanitize_key( $_POST['type'] ) : '';
		$id       = isset( $_POST['id'] ) ? absint( $_POST['id'] ) : '';
		$url      = isset( $_POST['url'] ) ? esc_url( $_POST['url'] ) : '';
		$current  = isset( $_POST['current'] ) ? absint( $_POST['current'] ) : '';

		// Bail if we don't have the post data we need.
		if ( ! ( $type && $id && $url && $current ) ) {
			wp_send_json_error( __( 'Missing type, id, url, or current.', 'mai-publisher' ) );
			exit();
		}

		// Start API data.
		$return   = [];
		$api_url  = trailingslashit( $site_url ) . 'index.php';
		$fetch    = [];

		// Add trending first incase views times out.
		if ( $trending_days ) {
			$fetch['trending'] = $trending_days;
		}

		// Add views.
		if ( $views_days ) {
			$fetch['views'] = $views_days;
		}

		// Try each API hit.
		foreach ( $fetch as $key => $days ) {
			$api_args = [
				'module'     => 'API',
				'method'     => 'Actions.getPageUrl',
				'idSite'     => $site_id,
				'token_auth' => $token,
				'pageUrl'    => $url,
				'period'     => 'range',
				'date'       => 'last' . $days,
				'format'     => 'json',
				// 'columns'    => 'nb_visits', // Not working.
			];

			// Allow filtering of args.
			$api_args = apply_filters( 'mai_publisher_views_api_args', $api_args, $key );

			// Get API url.
			$api_url = add_query_arg( $api_args, $api_url );

			// Send a GET request to the Matomo API.
			$response = wp_remote_get( $api_url );

			// Check for a successful request.
			if ( is_wp_error( $response ) ) {
				wp_send_json_error( $response->get_error_message(), $response->get_error_code() );
				exit();
			}

			// Get the response code.
			$code = wp_remote_retrieve_response_code( $response );

			// Bail if not a successful response.
			if ( 200 !== $code ) {
				wp_send_json_error( wp_remote_retrieve_response_message( $response ), $code );
				exit();
			}

			// Get the data.
			$body   = wp_remote_retrieve_body( $response );
			$data   = json_decode( $body, true );
			$data   = reset( $data ); // Get first. It was returning an array of 1 result.
			$visits = isset( $data['nb_visits'] ) ? absint( $data['nb_visits'] ) : null;

			// Bail if visits are not returned.
			if ( is_null( $visits ) ) {
				wp_send_json_error( __( 'No visits returned.', 'mai-publisher' ) );
				exit();
			}

			// Update meta. `mai_trending` or `mai_views`.
			switch ( $type ) {
				case 'post':
					update_post_meta( $id, "mai_{$key}", $visits );
				break;
				case 'term':
					update_term_meta( $id, "mai_{$key}", $visits );
				break;
			}

			$return[ $key ] = $visits;
		}

		// Update updated time.
		switch ( $type ) {
			case 'post':
				update_post_meta( $id, 'mai_views_updated', $current );
			break;
			case 'term':
				update_term_meta( $id, 'mai_views_updated', $current );
			break;
		}

		// Send it home.
		wp_send_json_success( $return );
		exit();
	}
}