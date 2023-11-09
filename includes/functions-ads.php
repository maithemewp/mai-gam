<?php

// Prevent direct file access.
defined( 'ABSPATH' ) || die;

/**
 * Returns array of ads for the currently viewed page.
 *
 * @since TBD
 *
 * @return array
 */
function maipub_get_page_ads() {
	static $ads = null;

	if ( ! is_null( $ads ) ) {
		return $ads;
	}

	$ads = [];

	// Get ad data from location settings.
	$data = maipub_get_page_ads_data();

	// Bail if no actual values.
	if ( ! array_filter( array_values( $data ) ) ) {
		return $ads;
	}

	// Loop through each type.
	foreach ( $data as $type => $items ) {
		// Loop through each item.
		foreach ( $items as $args ) {
			// Validate.
			$args = maipub_validate_ad_conditions( $args, $type );

			// Bail if not valid args.
			if ( ! $args ) {
				continue;
			}

			// Add to ads.
			$ads[] = $args;
		}
	}

	return $ads;
}

/**
 * Returns an array of ads.
 *
 * @since TBD
 *
 * @return array
 */
function maipub_get_page_ads_data() {
	static $ads = null;

	if ( ! is_null( $ads ) ) {
		return $ads;
	}

	// Set default ad array.
	$ads = [
		'global'  => [],
		'single'  => [],
		'archive' => [],
	];

	// Start post ID.
	$post_id = false;

	// Get post ID.
	if ( maipub_is_singular() ) {
		$post_id = get_the_ID();
	} elseif ( is_home() && ! is_front_page() ) {
		$post_id = get_option( 'page_for_posts' );
	} elseif ( maipub_is_shop_archive() ) {
		$post_id = get_option( 'woocommerce_shop_page_id' );
	}

	// Check visibility.
	$visibility = $post_id ? get_post_meta( $post_id, 'maipub_visibility', true ) : false;

	// Bail if hidding all ads.
	if ( $visibility && in_array( 'all', $visibility ) ) {
		return $ads;
	}

	$query = new WP_Query(
		[
			'post_type'              => 'mai_ad',
			'posts_per_page'         => 500,
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
			'suppress_filters'       => false, // https://github.com/10up/Engineering-Best-Practices/issues/116
			'orderby'                => 'menu_order',
			'order'                  => 'ASC',
		]
	);

	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) : $query->the_post();
			$post_id          = get_the_ID();
			$slug             = get_post()->post_name;
			$content          = get_post()->post_content;
			$global_location  = get_field( 'maipub_global_location' );
			$single_location  = get_field( 'maipub_single_location' );
			$archive_location = get_field( 'maipub_archive_location' );

			if ( $global_location ) {
				$ads['global'][] = maipub_filter_associative_array(
					[
						'id'       => $post_id,
						'slug'     => $slug,
						'location' => $global_location,
						'content'  => $content,
					]
				);
			}

			if ( $single_location ) {
				$ads['single'][] = maipub_filter_associative_array(
					[
						'id'                  => $post_id,
						'slug'                => $slug,
						'location'            => $single_location,
						'content'             => $content,
						'content_location'    => get_field( 'maipub_single_content_location' ),
						'content_count'       => get_field( 'maipub_single_content_count' ),
						'comment_count'       => get_field( 'maipub_single_comment_count' ),
						'types'               => get_field( 'maipub_single_types' ),
						'keywords'            => get_field( 'maipub_single_keywords' ),
						'taxonomies'          => get_field( 'maipub_single_taxonomies' ),
						'taxonomies_relation' => get_field( 'maipub_single_taxonomies_relation' ),
						'authors'             => get_field( 'maipub_single_authors' ),
						'include'             => get_field( 'maipub_single_entries' ),
						'exclude'             => get_field( 'maipub_single_exclude_entries' ),
					]
				);
			}

			if ( $archive_location ) {
				$ads['archive'][] = maipub_filter_associative_array(
					[
						'id'            => $post_id,
						'slug'          => $slug,
						'location'      => $archive_location,
						'content'       => $content,
						'content_count' => get_field( 'maipub_archive_content_count' ),
						'content_item'  => get_field( 'maipub_archive_content_item' ),
						'types'         => get_field( 'maipub_archive_types' ),
						'taxonomies'    => get_field( 'maipub_archive_taxonomies' ),
						'terms'         => get_field( 'maipub_archive_terms' ),
						'exclude'       => get_field( 'maipub_archive_exclude_terms' ),
						'includes'      => get_field( 'maipub_archive_includes' ),
					]
				);
			}

		endwhile;
	}
	wp_reset_postdata();

	// Now that we have data, maybe check visibility for incontent ads.
	if ( maipub_is_singular() && $visibility && in_array( 'incontent', $visibility ) ) {
		foreach ( $ads['single'] as $index => $values ) {
			if ( 'content' !== $values['location'] ) {
				continue;
			}

			unset( $ads['single'][ $index ] );
		}
	}

	return $ads;
}

/**
 * Gets valid args.
 *
 * @since TBD
 *
 * @param array  $args The ad args.
 * @param string $type The ad type. Either 'global', 'single', or 'archive'.
 *
 * @return array
 */
function maipub_validate_ad_conditions( $args, $type ) {
	$valid = [];

	// Bail if no id, content, and location.
	if ( ! ( $args['id'] && $args['location'] && $args['content'] ) ) {
		return $valid;
	}

	// Set variables.
	$locations = maipub_get_locations();

	// Bail if no location hook. Only check isset for location since 'content' has no hook.
	if ( ! isset( $locations[ $args['location'] ] ) ) {
		return $valid;
	}

	// Validate by type.
	switch ( $type ) {
		case 'global':
			$valid = maipub_validate_ad_conditions_global( $args );
			break;

		case 'single':
			$valid = maipub_validate_ad_conditions_single( $args );
			break;

		case 'archive':
			$valid = maipub_validate_ad_conditions_archive( $args );
			break;
	}

	return $valid;
}

/**
 * Validate global content args.
 *
 * @since TBD
 *
 * @param array $args The ad args.
 *
 * @return array
 */
function maipub_validate_ad_conditions_global( $args ) {
	// Parse.
	$args = wp_parse_args( $args, [
		'id'       => '',
		'slug'     => '',
		'location' => '',
		'content'  => '',
	] );

	// Sanitize.
	$args = [
		'id'       => absint( $args['id'] ),
		'slug'     => sanitize_key( $args['slug'] ),
		'location' => esc_html( $args['location'] ),
		'content'  => trim( maipub_get_processed_ad_content( $args['content'] ) ),
	];

	return $args;
}

/**
 * Validates single content args.
 * In content, recipe, etc. are checked directly in the dom.
 *
 * @since TBD
 *
 * @param array $args The ad args.
 *
 * @return array
 */
function maipub_validate_ad_conditions_single( $args ) {
	if ( ! maipub_is_singular() ) {
		return [];
	}

	// Parse.
	$args = wp_parse_args( $args, [
		'id'                  => '',
		'slug'                => '',
		'location'            => '',
		'content'             => '',
		'content_location'    => 'after',
		'content_count'       => 6,
		'content_count'       => 5,
		'types'               => [],
		'keywords'            => '',
		'taxonomies'          => [],
		'taxonomies_relation' => 'AND',
		'authors'             => [],
		'include'             => [],
		'exclude'             => [],
	] );

	// Sanitize.
	$args = [
		'id'                  => absint( $args['id'] ),
		'slug'                => sanitize_key( $args['slug'] ),
		'location'            => esc_html( $args['location'] ),
		'content'             => trim( $args['content'] ),
		'content_location'    => esc_html( $args['content_location'] ),
		'content_count'       => $args['content_count'] ? array_map( 'absint', explode( ',', (string) $args['content_count'] ) ) : [],
		'comment_count'       => absint( $args['comment_count'] ),
		'types'               => $args['types'] ? array_map( 'esc_html', (array) $args['types'] ) : [],
		'keywords'            => maipub_sanitize_keywords( $args['keywords'] ),
		'taxonomies'          => maipub_sanitize_taxonomies( $args['taxonomies'] ),
		'taxonomies_relation' => esc_html( $args['taxonomies_relation'] ),
		'authors'             => $args['authors'] ? array_map( 'absint', (array) $args['authors'] ) : [],
		'include'             => $args['include'] ? array_map( 'absint', (array) $args['include'] ) : [],
		'exclude'             => $args['exclude'] ? array_map( 'absint', (array) $args['exclude'] ) : [],
	];

	// Set variables.
	$post_id      = get_the_ID();
	$post_type    = get_post_type();
	$post_content = null;

	// Bail if excluding this entry.
	if ( $args['exclude'] && in_array( $post_id, $args['exclude'] ) ) {
		return [];
	}

	// If including this entry.
	$include = $args['include'] && in_array( $post_id, $args['include'] );

	// If not already including, check post types.
	if ( ! $include && ! ( in_array( '*', $args['types'] ) || in_array( $post_type, $args['types'] ) ) ) {
		return [];
	}

	// If not already including, and have keywords, check for them.
	if ( ! $include && $args['keywords'] ) {
		$post         = get_post( $post_id );
		$post_content = maipub_strtolower( strip_tags( do_shortcode( trim( $post->post_content ) ) ) );

		if ( ! maipub_str_contains( $post_content, $args['keywords'] ) ) {
			return [];
		}
	}

	// If not already including, check taxonomies.
	if ( ! $include && $args['taxonomies'] ) {

		if ( 'AND' === $args['taxonomies_relation'] ) {

			// Loop through all taxonomies to give a chance to bail if NOT IN.
			foreach ( $args['taxonomies'] as $data ) {
				$has_term = has_term( $data['terms'], $data['taxonomy'] );

				// Bail if we have a term and we aren't displaying here.
				if ( $has_term && 'NOT IN' === $data['operator'] ) {
					return [];
				}

				// Bail if we have don't a term and we are dislaying here.
				if ( ! $has_term && 'IN' === $data['operator'] ) {
					return [];
				}
			}

		} elseif ( 'OR' === $args['taxonomies_relation'] ) {

			$meets_any = [];

			foreach ( $args['taxonomies'] as $data ) {
				$has_term = has_term( $data['terms'], $data['taxonomy'] );

				if ( $has_term && 'IN' === $data['operator'] ) {
					$meets_any = true;
					break;
				}

				if ( ! $has_term && 'NOT IN' === $data['operator'] ) {
					$meets_any = true;
					break;
				}
			}

			if ( ! $meets_any ) {
				return [];
			}
		}
	}

	// If not already including, check authors.
	if ( ! $include && $args['authors'] ) {
		$author_id = get_post_field( 'post_author', $post_id );

		if ( ! in_array( $author_id, $args['authors'] ) ) {
			return [];
		}
	}

	// Process content.
	$args['content'] = maipub_get_processed_ad_content( $args['content'] );

	return $args;
}

/**
 * Validates content archive args.
 * In entries, etc. are checked directly in the dom.
 *
 * @since TBD
 *
 * @param array $args The ad args.
 *
 * @return array
 */
function maipub_validate_ad_conditions_archive( $args ) {
	if ( ! maipub_is_archive() ) {
		return [];
	}

	// Parse.
	$args = wp_parse_args( $args, [
		'id'            => '',
		'slug'          => '',
		'location'      => '',
		'content'       => '',
		'content_count' => '',
		'content_item'  => 'rows',
		'types'         => [],
		'taxonomies'    => [],
		'terms'         => [],
		'exclude'       => [],
		'includes'      => [],
	] );

	// Sanitize.
	$args = [
		'id'            => absint( $args['id'] ),
		'slug'          => sanitize_key( $args['slug'] ),
		'location'      => esc_html( $args['location'] ),
		'content'       => trim( $args['content'] ),
		'content_count' => $args['content_count'] ? array_map( 'absint', explode( ',', (string) $args['content_count'] ) ) : [],
		'content_item'  => $args['content_item'] ? sanitize_key( $args['content_item'] ) : 'rows',
		'types'         => $args['types'] ? array_map( 'esc_html', (array) $args['types'] ) : [],
		'taxonomies'    => $args['taxonomies'] ? array_map( 'esc_html', (array) $args['taxonomies'] ) : [],
		'terms'         => $args['terms'] ? array_map( 'absint', (array) $args['terms'] ) : [],
		'exclude'       => $args['exclude'] ? array_map( 'absint', (array) $args['exclude'] ) : [],
		'includes'      => $args['includes'] ? array_map( 'sanitize_key', (array) $args['includes'] ) : [],
	];

	// Blog.
	if ( is_home() ) {
		// Bail if not showing on post archive.
		if ( ! $args['types'] && ( ! in_array( '*', $args['types'] ) || ! in_array( 'post', $args['types'] ) ) ) {
			return [];
		}
	}
	// CPT archive. WooCommerce shop returns false for `is_post_type_archive()`.
	elseif ( is_post_type_archive() || maipub_is_shop_archive() ) {
		// Bail if shop page and not showing here.
		if ( maipub_is_shop_archive() ) {
			if ( ! $args['types'] && ( ! in_array( '*', $args['types'] ) || ! in_array( 'product', $args['types'] ) ) ) {
				return [];
			}
		}
		// Bail if not showing on this post type archive.
		else {
			global $wp_query;

			$post_type = isset( $wp_query->query['post_type'] ) ? $wp_query->query['post_type'] : '';

			if ( ! $args['types'] && ( ! in_array( '*', $args['types'] ) || ! is_post_type_archive( $post_type ) ) ) {
				return [];
			}
		}
	}
	// Term archive.
	elseif ( is_tax() || is_category() || is_tag() ) {
		$object = get_queried_object();

		// Bail if excluding this term archive.
		if ( $args['exclude'] && in_array( $object->term_id, $args['exclude'] ) ) {
			return [];
		}

		// If including this entry.
		$include = $args['terms'] && in_array( $object->term_id, $args['terms'] );

		// If not already including, check taxonomies if we're restricting to specific taxonomies.
		if ( ! $include && ! ( $args['taxonomies'] && in_array( $object->taxonomy, $args['taxonomies'] ) ) ) {
			return [];
		}
	}
	// Search results;
	elseif ( is_search() ) {
		// Bail if not set to show on search results.
		if ( ! ( $args['includes'] || in_array( 'search', $args['includes'] ) ) ) {
			return [];
		}
	}

	// Process content.
	$args['content'] = maipub_get_processed_ad_content( $args['content'] );

	return $args;
}
