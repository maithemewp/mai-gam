<?php

// Prevent direct file access.
defined( 'ABSPATH' ) || die;

/**
 * Adds settings page.
 *
 * Original code generated by the WordPress Option Page generator:
 * @link http://jeremyhixon.com/wp-tools/option-page/
 */
class Mai_Publisher_Settings_Categories {
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
		add_action( 'admin_menu', [ $this, 'add_menu_item' ], 12 );
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
			'', // No parent page, so no menu item.
			__( 'IAB Tech Lab Taxonomy Mapping', 'mai-publisher' ), // page_title
			'', // No menu title.
			'manage_options', // capability
			'categories', // menu_slug
			[ $this, 'add_content' ], // callback
		);
	}

	/**
	 * Adds setting page content.
	 *
	 * 1. We need to build JSON from CSV so we can easily copy and paste into the categories.json file.
	 *    This is so we can update easier the next time IAB updates the tsv file.
	 *
	 * 2. We need to show id => label with visual hierarchy for the search picker. Direct from categories.json. Simple.
	 *
	 * 3. We need to show a hierarchical ist of all the categories visually to help make a choice.
	 *    Don't need IDs, just build hierarchy from categories.json on the fly, only for that setting page.
	 *
	 * @since 0.1.0
	 *
	 * @return void
	 */
	function add_content() {
		echo '<div class="wrap">';
			printf( '<h2>%s</h2>', __( 'Categories', 'mai-publisher' ) );

			$doc = 'https://docs.google.com/spreadsheets/d/1uh3CcEW3N1d1hL5Mshi2EYk3lUeq6dcgn2QdkNF27IE/';
			$iab = 'https://github.com/InteractiveAdvertisingBureau/Taxonomies/blob/main/Content%20Taxonomies/Content%20Taxonomy%203.0.tsv';

			echo '<p>';
				echo __( 'This page is for reference only, for easier copy & paste into the plugin\'s config.php.', 'mai-publisher' );
				printf( '<br><a href="%s">%s</a>', $doc, __( 'View taxonomy spreadsheet here.', 'mai-publisher' ) );
				printf( '<br><a href="%s">%s</a>', $iab, __( 'View original spreadsheet from IAB Tech Lab.', 'mai-publisher' ) );
			echo '</p>';

			// Get the CSV file as an array.
			$file  = MAI_PUBLISHER_DIR . 'assets/csv/content-taxonomies.csv';
			$array = array_map( 'str_getcsv', file( $file ) );

			// Remove two header rows.
			array_shift( $array );
			array_shift( $array );

			// Setup variables.
			$json = $lists = [];

			// Loop through each row.
			foreach ( $array as $index => $data ) {
				list( $id, $parent, $name, $t1, $t2, $t3, $t4, $ext ) = $data;

				/**********************
				 * Start building JSON.
				 */

				$structure = [];
				$tiers     = array_filter( [ $t1, $t2, $t3, $t4 ] );

				// If multiple, remove the last then build data.
				if ( count( $tiers ) > 1 ) {
					array_pop( $tiers );

					// Loop through tiers.
					foreach ( $tiers as $index => $value ) {
						$structure[] = '&ndash;';
					}
				}

				// Add name as last value.
				$structure[] = $name;

				// Add to taxonomies array with key of id and parent/child/grandchild/name as value, removing empty values.
				$json[ $id ] = implode( '&nbsp;', $structure );

				/**********************
				 * Start building lists.
				 */

				$current = &$lists;
				$tiers   = [ $t1, $t2, $t3, $t4 ];

				// Loop through tiers.
				foreach ( $tiers as $value) {
					$value = trim( $value );

					if ( $value ) {
						// Check if the level already exists in the array.
						if ( ! isset( $current[ $value ] ) ) {
							$current[ $value ] = [];
						}
						// Update the reference to the current level.
						$current = &$current[ $value ];
					} else {
						// If the level is empty, break out of the loop.
						break;
					}
				}
			}

			// Render in a details and summary markup.
			echo '<details>';
				printf( '<summary class="button button-secondary" style="margin-bottom:8px;"><strong>%s</strong></summary>', __( 'Show categories for categories.json', 'mai-publisher' ) );
				echo '<textarea rows="10" cols="1" style="width:100%;min-height:50vh;background:white;" readonly>';
					echo json_encode( $json, JSON_PRETTY_PRINT );
				echo '</textarea>';
			echo '</details>';

			// Inline styles for the nested lists.
			?>
			<style>
				ul.maipub-categories {
					margin-left: 0;
					margin-bottom: 24px;
				}
				ul.maipub-categories ul {
					--margin-left: 8px;
					margin-top: 8px;
					margin-left: var(--margin-left);
				}
				ul.maipub-categories ul ul {
					--margin-left: 16px;
				}
				ul.maipub-categories ul ul ul {
					--margin-left: 24px;
				}
			</style>
			<?php

			// Display the $lists array as nest unordered lists.
			foreach ( $lists as $name => $children ) {
				echo '<ul class="maipub-categories">';
					printf( '<li>%s%s</li>', $name, $this->get_list( $children ) );
				echo '</ul>';
			}
		echo '</div>';
	}

	/**
	 * Function to recursively loop through associative array data to build nested unordered lists.
	 *
	 * @since 0.1.0
	 *
	 * @return string
	 */
	function get_list( $array, $indent = "&ndash;&nbsp;" ) {
		$html = $array ? '<ul>' : '';

		foreach ( $array as $key => $value ) {
			$html .= sprintf( '<li>%s%s</li>', $indent, $key );

			if ( $value ) {
				$html .= $this->get_list( $value, $indent . "&ndash;&nbsp;" );
			}
		}

		$html .= $array ? '</ul>' : '';

		return $html;
	}
}
