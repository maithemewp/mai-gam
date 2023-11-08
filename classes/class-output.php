<?php

// Prevent direct file access.
defined( 'ABSPATH' ) || die;

class Mai_Publisher_Output {
	protected $ads;
	protected $grouped;
	protected $locations;
	protected $dom;
	protected $gam;

	/**
	 * Constructs the class.
	 *
	 * @since TBD
	 *
	 * @return void
	 */
	function __construct() {
		$this->hooks();
	}

	/**
	 * Runs hooks.
	 *
	 * @since TBD
	 *
	 * @return void
	 */
	function hooks() {
		// add_action( 'get_header', [ $this, 'start' ], 99998 );
		// add_action( 'wp_footer',  [ $this, 'end' ], 99998 );
		// add_action( 'template_redirect', [ $this, 'start' ], 99998 );
		// add_action( 'after_setup_theme', [ $this, 'start' ], 99998 );
		// add_action( 'shutdown',  [ $this, 'end' ], 10 );
		add_action( 'template_redirect',  [ $this, 'start' ], 99998 );
		add_action( 'shutdown',           [ $this, 'end' ], 99998 );
	}

	/**
	 * Starts output buffering.
	 *
	 * @since TBD
	 *
	 * @return void
	 */
	function start() {
		$this->locations = maipub_get_ad_locations();
		$this->ads       = maipub_get_page_ads();
		$this->grouped   = $this->get_grouped_ads( $this->ads );

		// Bail if no ads.
		if ( ! $this->ads ) {
			return;
		}

		ob_start( [ $this, 'callback' ] );
	}

	/**
	 * Ends output buffering.
	 *
	 * @since TBD
	 *
	 * @return void
	 */
	function end() {
		// Bail if no ads.
		if ( ! $this->ads ) {
			return;
		}

		/**
		 * Pretty sure this is right.
		 * @link https://stackoverflow.com/questions/7355356/whats-the-difference-between-ob-flush-and-ob-end-flush
		 */
		if ( ob_get_length() ) {
			ob_end_flush();
		}
	}

	/**
	 * Buffer callback.
	 *
	 * @since TBD
	 *
	 * @param string $buffer The full dom markup.
	 *
	 * @return string
	 */
	function callback( $buffer ) {
		// Bail if no buffer.
		if ( ! $buffer ) {
			return $buffer;
		}

		// Bail if XML.
		if ( str_starts_with( trim( $buffer ), '<?xml' ) ) {
			return $buffer;
		}

		// Check if we have a sidebar.
		// $has_sidebar = false;
		// $tags        = new WP_HTML_Tag_Processor( $buffer );

		// while ( $tags->next_tag( [ 'class_name' => 'sidebar' ] ) ) {
		// 	$has_sidebar = true;
		// 	break;
		// }

		// Setup dom.
		$this->dom = $this->dom_document( $buffer );

		// In content.
		if ( isset( $this->grouped['content'] ) && $this->grouped['content'] ) {
			$this->handle_content();
		}

		// In entries.
		if ( isset( $this->grouped['entries'] ) && $this->grouped['entries'] ) {
			$this->handle_entries();
		}

		// Recipes.
		if ( isset( $this->grouped['recipe'] ) && $this->grouped['recipe'] ) {
			$this->handle_recipes();
		}

		// Comments.
		if ( isset( $this->grouped['comments'] ) && $this->grouped['comments'] ) {
			$this->handle_comments();
		}

		// Get all ads for gam.
		$config   = maipub_get_config( 'ad_units' );
		$xpath    = new DOMXPath( $this->dom );
		$ad_units = $xpath->query( '//div[contains(concat(" ", normalize-space(@class), " "), " mai-ad-unit ")]' );

		// Loop through ad units.
		foreach ( $ad_units as $ad_unit ) {
			$id    = $ad_unit->getAttribute( 'id' );
			$id    = str_replace( 'mai-ad-', '', $id );
			$array = explode( '-', $id );
			$last  = end( $array );

			// Remove last item if numeric.
			if ( is_numeric( $last ) ) {
				array_pop( $array );
			}

			// Back to string.
			$slot = implode( '-', $array );

			// Skip if no slot.
			if ( ! $slot ) {
				continue;
			}

			// If we have a sidebar.
			// if ( $has_sidebar ) {
			// 	$in_content = false;
			// 	$current    = $ad_unit;

			// 	// Check if this ad is inside the content class.
			// 	while ( $current = $current->parentNode ) {
			// 		if ( $current instanceof DOMElement && 'main' === $current->tagName ) {
			// 			$in_content = true;
			// 			break;
			// 		}
			// 	}

			// 	// If in content.
			// 	if ( $in_content ) {
			// 		// Remove all sizes larger than 800px.
			// 		foreach ( $config[ $slot ]['sizes'] as $index => $size ) {
			// 			if ( ! is_array( $size ) ) {
			// 				continue;
			// 			}

			// 			if ( $size[0] > 800 ) {
			// 				unset( $config[ $slot ]['sizes'][ $index ] );
			// 			}
			// 		}
			// 		// Remove desktop sizes larger than 800px.
			// 		foreach ( $config[ $slot ]['sizes_desktop'] as $index => $size ) {
			// 			if ( ! is_array( $size ) ) {
			// 				continue;
			// 			}

			// 			if ( $size[0] > 800 ) {
			// 				unset( $config[ $slot ]['sizes_desktop'][ $index ] );
			// 			}
			// 		}
			// 	}
			// }

			// Add to gam array.
			$this->gam[ $slot ] = [
				'sizes'        => $config[ $slot ]['sizes'],
				'sizesDesktop' => $config[ $slot ]['sizes_desktop'],
				'sizesTablet'  => $config[ $slot ]['sizes_tablet'],
				'sizesMobile'  => $config[ $slot ]['sizes_mobile'],
				'targets'      => [],
			];

			// Get and add targets.
			$at = $ad_unit->getAttribute( 'data-at' );
			$ap = $ad_unit->getAttribute( 'data-ap' );

			if ( $at ) {
				$this->gam[ $slot ]['targets']['at'] = $at;
			}

			if ( $ap ) {
				$this->gam[ $slot ]['targets']['ap'] = $ap;
			}
		}

		// Save HTML.
		$buffer = $this->dom->saveHTML();

		return $buffer;
	}

	/**
	 * Inserts ads into entry content.
	 *
	 * @since TBD
	 *
	 * @return void
	 */
	function handle_content() {
		// Get the content node.
		$xpath    = new DOMXPath( $this->dom );
		$content  = $xpath->query( '//div[contains(concat(" ", normalize-space(@class), " "), " entry-content-single ")]' )->item(0);
		$children = $content->childNodes;
		$tags     = [];

		// Bail if no content node or child nodes.
		if ( ! ( $content && $children ) ) {
			return;
		}

		// Get last child element.
		$last = $children->item( $children->length - 1 );

		// Loop through in-content ads.
		foreach ( $this->grouped['content'] as $ad ) {
			// Skip if no location.
			if ( ! isset( $ad['content_location'] ) || ! $ad['content_location'] ) {
				continue;
			}

			// Get tags by location.
			switch ( $ad['content_location'] ) {
				case 'before':
					$tags = [ 'h2', 'h3' ];
					break;
				case 'after':
					$tags = [ 'div', 'p', 'ol', 'ul', 'blockquote', 'figure', 'iframe' ];
					break;
			}

			// Skip ad if no tags.
			if ( ! $tags ) {
				continue;
			}

			// Filter and sanitize.
			$tags = apply_filters( 'mai_publisher_content_elements', $tags, $ad );
			$tags = array_map( 'sanitize_text_field', $tags );
			$tags = array_filter( $tags );
			$tags = array_unique( $tags );

			// Skip ad if no tags.
			if ( ! $tags ) {
				continue;
			}

			// Start elements.
			$elements = [];

			// Get valid elements.
			foreach ( $children as $node ) {
				if ( ! $node->childNodes->length || ! in_array( $node->nodeName, $tags ) ) {
					continue;
				}

				$elements[] = $node;
			}

			// Skip ad if no elements.
			if ( ! $elements ) {
				continue;
			}

			// Sort counts lowest to highest.
			asort( $ad['content_count'] );

			// Setup counts.
			$item       = 0;
			$tmp_counts = array_flip( $ad['content_count'] );

			// Loop through elements.
			foreach ( $elements as $element ) {
				$item++;

				// Bail if there are no more counts to check.
				if ( ! $tmp_counts ) {
					break;
				}

				// Bail if not an element we need.
				if ( ! isset( $tmp_counts[ $item ] ) ) {
					continue;
				}

				/**
				 * Build the temporary dom.
				 * Special characters were causing issues with `appendXML()`.
				 *
				 * This needs to happen inside the loop, otherwise the slot IDs are not correctly incremented.
				 *
				 * @link https://stackoverflow.com/questions/4645738/domdocument-appendxml-with-special-characters
				 * @link https://www.py4u.net/discuss/974358
				 */
				$tmp  = maipub_get_dom_document( $ad['content'] );
				$node = $this->dom->importNode( $tmp->documentElement, true );

				// If valid node.
				if ( $node ) {
					// Before headings.
					if ( 'before' === $ad['content_location'] ) {
						$this->insert_node( $node, $element, 'before' );
					}
					// After elements.
					else {
						/**
						 * Bail if this is the last element.
						 * This avoids duplicates since this location would technically be "after entry content" at this point.
						 */
						if ( $element === $last || null === $element->nextSibling ) {
							break;
						}

						// Insert the node into the dom.
						$this->insert_node( $node, $element->nextSibling, 'after' );
					}
				}

				// Remove from temp counts.
				unset( $tmp_counts[ $item ] );
			}
		}
	}

	function handle_entries() {
		// Get the content node.
		$xpath   = new DOMXPath( $this->dom );
		$wrap    = $xpath->query( '//div[contains(concat(" ", normalize-space(@class), " "), " entries-archive ")]/div[contains(concat(" ", normalize-space(@class), " "), " entries-wrap ")]' )->item(0);
		$entries = $wrap ? count( $wrap->childNodes ) : 0;

		// Bail if no wrap and no entries.
		if ( ! ( $wrap && $entries ) ) {
			return;
		}

		// Loop through entries ads.
		foreach ( $this->grouped['entries'] as $ad ) {
			// Skip if no content.
			if ( ! $ad['content'] ) {
				continue;
			}

			// Mai Theme v2 logic for rows/columns.
			if ( class_exists( 'Mai_Engine' ) ) {
				$compare  = null;

				// If counting rows.
				if ( 'rows' === $ad['content_item'] ) {
					$columns = mai_get_breakpoint_columns( $ad );

					// If columns.
					if ( isset( $columns['lg'] ) && $columns['lg'] ) {
						// Get desktop rows and round up.
						$rows    = $entries / (int) $columns['lg'];
						$compare = absint( ceil( $rows ) );
					}

				}
				// If counting entries.
				elseif ( 'entries' === $ad['content_item'] ) {
					$compare = $entries;
				}

				// If comparing.
				if ( ! is_null( $compare ) ) {
					// Remove counts that are greater than the posts per page.
					foreach ( $ad['content_count'] as $index => $count ) {
						if ( (int) $count >= $compare ) {
							// Remove this one and any after it, and break.
							$ad['content_count'] = array_slice( $ad['content_count'], 0, $index );
							break;
						}
					}
				}

				// TODO: Handle rows order for Mai Engine.


				// // Set has rows.
				// $has_rows = false;

				// // Check if we have an ad with rows.
				// foreach ( $this->grouped['entries'] as $ad ) {
				// 	if ( 'rows' === $ad['content_item'] ) {
				// 		$has_rows = true;
				// 		break;
				// 	}
				// }

				// // Set rows.
				// if ( $has_rows ) {
				// 	$styles  = (string) $wrap->getAttribute( 'style' );
				// 	$styles  = array_filter( explode( ';' , $styles ) );
				// 	$columns = [];

				// 	foreach ( $styles as $style ) {
				// 		if ( ! str_starts_with( $style, '--columns-' ) ) {
				// 			continue;
				// 		}

				// 		$column = explode( ':', $style );

				// 		if ( 2 !== count( $column ) ) {
				// 			continue;
				// 		}

				// 		$break  = str_replace( '--columns-', '', $column[0] );
				// 		$column = explode( '/', $column[1] );

				// 		if ( 2 !== count( $column ) ) {
				// 			continue;
				// 		}

				// 		$columns[ $break ] = $column[1];
				// 	}

				// 	if ( $columns ) {
				// 		// Get existing styles as an array.
				// 		$style  = (string) $wrap->getAttribute( 'style' );
				// 		$styles = explode( ';', $style );
				// 		$styles = array_map( 'trim', $styles );

				// 		foreach ( $columns as $break => $column ) {
				// 			$styles[] = sprintf( '--maipub-row-%s:%s;', $break, $column );
				// 		}

				// 		// Set new styles.
				// 		$wrap->setAttribute( 'style', implode( ';', $styles ) . ';' );
				// 	}
				// }
			}

			// Setup vars.
			$class = '';
			$style = '';

			// Build atts.
			switch ( $ad['content_item'] ) {
				case 'rows':
					$class .= 'maipub-row';
					break;
				case 'entries':
					$class .= 'maipub-entry entry entry-archive is-column';
					break;
			}

			// Sort counts lowest to highest.
			asort( $ad['content_count'] );

			// Add attributes.
			foreach ( $ad['content_count'] as $count ) {
				$style .= 'rows' === $ad['content_item'] ? "order:calc(var(--maipub-row) * {$count});" : "order:{$count};";
				$tags   = new WP_HTML_Tag_Processor( $ad['content'] );

				// Loop through tags.
				while ( $tags->next_tag() ) {
					$class = trim( $class . ' ' . (string) $tags->get_attribute( 'class' ) );
					$style = trim( $style . ' ' . (string) $tags->get_attribute( 'style' ) );
					$tags->set_attribute( 'class', $class );
					$tags->set_attribute( 'style', $style );
					// Break after first.
					break;
				}

				// Get updated HTML.
				$html = $tags->get_updated_html();

				// Skip if no HTML.
				if ( ! $html ) {
					continue;
				}

				/**
				 * Build the temporary dom.
				 * Special characters were causing issues with `appendXML()`.
				 *
				 * This needs to happen inside the loop, otherwise the slot IDs are not correctly incremented.
				 *
				 * @link https://stackoverflow.com/questions/4645738/domdocument-appendxml-with-special-characters
				 * @link https://www.py4u.net/discuss/974358
				 */
				$tmp  = maipub_get_dom_document( $html );
				$node = $this->dom->importNode( $tmp->documentElement, true );

				// If valid node.
				if ( $node ) {
					// Insert the node into the dom.
					$this->insert_node( $node, $wrap, 'append' );
				}
			}
		}

		// Bail if no HTML.
		// if ( ! $html ) {
		// 	return;
		// }

		// ray( $html );

		// /**
		//  * Build the temporary dom.
		//  * Special characters were causing issues with `appendXML()`.
		//  *
		//  * This needs to happen inside the loop, otherwise the slot IDs are not correctly incremented.
		//  *
		//  * @link https://stackoverflow.com/questions/4645738/domdocument-appendxml-with-special-characters
		//  * @link https://www.py4u.net/discuss/974358
		//  */
		// $tmp  = maipub_get_dom_document( $html );
		// $node = $this->dom->importNode( $tmp->documentElement, true );

		// // If valid node.
		// if ( $node ) {
		// 	// Insert the node into the dom.
		// 	$this->insert_node( $node, $wrap, 'append' );
		// }
	}

	function handle_recipes() {

	}

	/**
	 * Handle after header ad.
	 *
	 * @since TBD
	 *
	 * @return void
	 */
	function handle_comments() {
		// $xpath   = new DOMXPath( $this->dom );
		// $nodes   = $xpath->query( '//*[contains(concat(" ", normalize-space(@class), " "), " site-header ")]' );
		// $element = $nodes->item( 0 );

		// if ( ! $element ) {
		// 	return;
		// }

		// foreach ( $this->ads['after_header'] as $ad ) {
		// 	/**
		// 	 * Build the temporary dom.
		// 	 * Special characters were causing issues with `appendXML()`.
		// 	 *
		// 	 * This needs to happen inside the loop, otherwise the slot IDs are not correctly incremented.
		// 	 *
		// 	 * @link https://stackoverflow.com/questions/4645738/domdocument-appendxml-with-special-characters
		// 	 * @link https://www.py4u.net/discuss/974358
		// 	 */
		// 	$tmp  = maipub_get_dom_document( $ad['content'] );
		// 	// $tmp  = maipub_get_dom_document( maipub_get_processed_ad_content( $ad['content'] ) );
		// 	// $node = $this->dom->importNode( $tmp->documentElement, true );
		// 	$node = $this->dom->importNode( $tmp->documentElement, true );

		// 	// Skip if no node.
		// 	if ( ! $node ) {
		// 		continue;
		// 	}

		// 	// ray( $this->locations );

		// 	// Insert the node into the dom.
		// 	$this->insert_node( $element, $node, $this->locations['after_header']['insert'] );
		// }
	}

	/**
	 * Gets the full DOMDocument object, including DOCTYPE and <html>.
	 *
	 * @since TBD
	 *
	 * @link https://stackoverflow.com/questions/29493678/loadhtml-libxml-html-noimplied-on-an-html-fragment-generates-incorrect-tags
	 *
	 * @param string $html Any given HTML string.
	 *
	 * @return DOMDocument
	 */
	function dom_document( $html ) {
		// Create the new document.
		$dom = new DOMDocument();

		// Modify state.
		$libxml_previous_state = libxml_use_internal_errors( true );

		// Encode.
		$html = mb_convert_encoding( $html, 'HTML-ENTITIES', 'UTF-8' );

		// Load the content in the document HTML.
		$dom->loadHTML( $html );

		// Handle errors.
		libxml_clear_errors();

		// Restore.
		libxml_use_internal_errors( $libxml_previous_state );

		return $dom;
	}

	/**
	 * Insert node based on an action value.
	 *
	 * @since TBD
	 *
	 * @param DOMNode $insert The node to insert.
	 * @param DOMNode $target The target element.
	 * @param string  $action The insertion location.
	 *
	 * @return void
	 */
	function insert_node( $insert, $target, $action ) {
		switch ( $action ) {
			case 'before':
				// Insert before this element.
				$target->parentNode->insertBefore( $insert, $target );
				break;
			case 'after':
			default:
				/**
				 * Insert after this element. There is no insertAfter() in PHP ¯\_(ツ)_/¯.
				 * @link https://gist.github.com/deathlyfrantic/cd8d7ef8ba91544cdf06
				 */
				$target->parentNode->insertBefore( $insert, $target->nextSibling );
				break;
			case 'prepend':
				// Insert before first child.
				$target->insertBefore( $insert, $target->firstChild );
				break;
			case 'append':
				// Insert after last child.
				$target->appendChild( $insert );
				break;
		}
	}

	/**
	 * Loop through ads and group by location.
	 *
	 * @since TBD
	 *
	 * @param array $page_ads The existing ads array.
	 *
	 * @return array
	 */
	function get_grouped_ads( $page_ads ) {
		$ads = [];

		foreach ( $page_ads as $ad ) {
			// Skip if not a valid location.
			if ( ! isset( $ad['location'] ) || ! $ad['location'] || ! isset( $this->locations[ $ad['location'] ] ) ) {
				continue;
			}

			// Store and unset location.
			$location = $ad['location'];
			unset( $ad['location'] );

			// Make sure location key is set.
			$ads[ $location ] = isset( $ads[ $location ] ) ? $ads[ $location ] : [];

			// Add to new array.
			$ads[ $location ][] = $ad;
		}

		return $ads;
	}

	/**
	 * Get targets.
	 * These must be exist in our GAM 360.
	 *
	 * @since TBD
	 *
	 * @return void
	 */
	function get_targets() {
		$targets = [];
		$age     = maipub_get_content_age();
		$creator = maipub_get_content_creator();
		$group   = maipub_get_content_group();
		$path    = maipub_get_current_page( 'url' );
		$type    = maipub_get_content_type();
		$iabct   = maipub_get_current_page( 'iabct' );

		// Content age.
		if ( $age ) {
			$targets['ca'] = $age[0];
		}

		// Content creator.
		if ( $creator ) {
			$targets['cc'] = $creator;
		}

		// Content group/category.
		if ( $group ) {
			$targets['cg'] = $group;
		}

		// Content path.
		if ( $path ) {
			$targets['cp'] = wp_parse_url( $path, PHP_URL_PATH );
		}

		// Content type.
		if ( $type ) {
			$targets['ct'] = $type;
		}

		// IAB Content Taxonomy.
		if ( $iabct ) {
			$targets['iabct'] = $iabct;
		}

		return $targets;
	}
}

// add_action( 'genesis_before_loop', function() {
// 	echo '<h2>Here</h2>';
// }, 5 );

// add_action( 'genesis_after_header', function() {
// 	echo '<h2>Here</h2>';
// }, 15 );