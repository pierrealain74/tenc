<?php
/**
 * Retrieve the listing type configuration from database, validate, and format it for use.
 *
 * @since 2.2
 */

namespace MyListing\Src\Listing_Types;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Listing_Type_Config {

    /**
     * Used to cache method return values for multiple calls.
     *
     * @since 2.2
     */
    private $cache = [];

    /**
	 * The Listing Type object this configuration belongs to.
	 *
	 * @since 2.2
     */
    private $listing_type;

	/**
	 * Get the default configuration structure.
	 *
	 * @since 2.2
	 */
	private $default_config;

	/**
	 * Get the configuration for the requested listing type.
	 *
	 * @since 2.2
	 * @param $listing_type The Listing Type object this config belongs to
	 */
	public function __construct( $listing_type ) {
		$this->listing_type = $listing_type;
		$this->default_config = \MyListing\Src\Listing_Types\Default_Config::get();
	}


	/*=== FIELDS TAB ===*/

	/**
	 * Get all used fields from db, wrapped in a custom field class.
	 *
	 * @since 2.1
	 */
	public function get_fields() {
		if ( ! empty( $this->cache['fields'] ) ) {
			return $this->cache['fields'];
		}

		$this->cache['fields'] = [];

		$fields = get_post_meta( $this->listing_type->get_id(), 'case27_listing_type_fields', true );
		$fields = is_serialized( $fields ) ? @unserialize( $fields ) : [];

		foreach ( (array) $fields as $key => $fieldarr ) {

			if ( $fieldarr['type'] === 'restaurant-menu' ) {
				$fieldarr['type'] = 'general-repeater';
			}

			// check if field class exists
			$fieldclass = sprintf( '\MyListing\Src\Forms\Fields\%s_Field', c27()->file2class( $fieldarr['type'] ) );
			if ( ! class_exists( $fieldclass ) ) {
				mlog()->warn( sprintf( 'No class handler for field type %s found.', c27()->file2class( $fieldarr['type'] ) ) );
				continue;
			}

			// initiate class
			$field = new $fieldclass( $fieldarr );
			$field->set_listing_type( $this->listing_type );

			$this->cache['fields'][ $key ] = $field;
		}

		return $this->cache['fields'];
	}


	/*=== SINGLE LISTING TAB ===*/

	/**
	 * Get single listing config.
	 *
	 * @since 2.2
	 */
	public function get_single_listing() {
		if ( ! empty( $this->cache['single'] ) ) {
			return $this->cache['single'];
		}

        $data = get_post_meta( $this->listing_type->get_id(), 'case27_listing_type_single_page_options', true );
		$config = array_replace_recursive(
			$this->default_config['single'],
			is_serialized( $data ) ? @unserialize( $data ) : []
		);

		// convert content blocks to objects
		foreach ( $config['menu_items'] as $key => $menu_item ) {
			if ( ! empty( $menu_item['layout'] ) ) {
				$config['menu_items'][ $key ]['layout'] = $this->get_content_blocks_from_config( $menu_item['layout'] );
			}

			if ( ! empty( $menu_item['sidebar'] ) ) {
				$config['menu_items'][ $key ]['sidebar'] = $this->get_content_blocks_from_config( $menu_item['sidebar'] );
			}
		}

		$this->cache['single'] = $config;
		return $this->cache['single'];
	}

	/**
	 * Convert an array of content blocks to objects.
	 *
	 * @since 2.2
	 */
	private function get_content_blocks_from_config( $blockdata ) {
		$blocks = [];
		foreach ( (array) $blockdata as $blockarr ) {
			if ( ! is_array( $blockarr ) || empty( $blockarr['type'] ) ) {
				continue;
			}

			// check if block class exists
			$blockclass = sprintf( '\MyListing\Src\Listing_Types\Content_Blocks\%s_Block', c27()->file2class( $blockarr['type'] ) );
			if ( ! class_exists( $blockclass ) ) {
				mlog()->warn( sprintf( 'No class handler for content block type %s found.', c27()->file2class( $blockarr['type'] ) ) );
				continue;
			}

			/**
			 * Add bracket syntax support for Table, Accordion, Details, and Tabs content blocks.
			 * The old [[field]] placeholder is replaced with the actual field key.
			 *
			 * @since 2.6.1
			 */
			$aliases = array_flip( \MyListing\Src\Listing::$aliases );
			if ( in_array( $blockarr['type'], ['table', 'details', 'accordion', 'tabs'], true ) ) {
				foreach ( (array) $blockarr['rows'] as $key => $row ) {
					if ( ! isset( $row['show_field'] ) ) {
						continue;
					}

					if ( $row['show_field'] === '__listing_rating' ) {
						$row['show_field'] = ':reviews-stars';
					}

					if ( isset( $aliases[ $row['show_field'] ] ) ) {
						$row['show_field'] = $aliases[ $row['show_field'] ];
					}

					if ( $row['show_field'] === 'location' ) {
						$row['show_field'] = 'location.short';
					}

					$blockarr['rows'][ $key ]['content'] = str_replace(
						'[[field]]',
						sprintf( '[[%s]]', $row['show_field'] ),
						$row['content']
					);

					unset( $blockarr['rows'][ $key ]['show_field'] );
				}
			}

			// initiate class
			$blocks[] = new $blockclass( $blockarr );
		}

		return $blocks;
	}


	/*=== PREVIEW CARD TAB ===*/

	/**
	 * Get preview card config.
	 *
	 * @since 2.2
	 */
	public function get_preview_card() {
		if ( ! empty( $this->cache['preview'] ) ) {
			return $this->cache['preview'];
		}

        $data = get_post_meta( $this->listing_type->get_id(), 'case27_listing_type_result_template', true );
		$config = array_replace_recursive(
			$this->default_config['result'],
			is_serialized( $data ) ? @unserialize( $data ) : []
		);

		/**
		 * Add bracket syntax support for head buttons, info fields, and footer sections.
		 * The old [[field]] syntax is replaced with the actual field key.
		 *
		 * @since 2.4.5
		 */
		$aliases = array_flip( \MyListing\Src\Listing::$aliases );
		foreach ( $config['buttons'] as $key => $button ) {
			if ( ! isset( $button['show_field'] ) ) {
				continue;
			}

			if ( $button['show_field'] === '__listing_rating' ) {
				$button['show_field'] = ':reviews-stars';
			}

			if ( isset( $aliases[ $button['show_field'] ] ) ) {
				$button['show_field'] = $aliases[ $button['show_field'] ];
			}

			if ( $button['show_field'] === 'location' ) {
				$button['show_field'] = 'location.short';
			}

			$config['buttons'][ $key ]['label'] = str_replace(
				'[[field]]',
				sprintf( '[[%s]]', $button['show_field'] ),
				$button['label']
			);

			unset( $config['buttons'][ $key ]['show_field'] );
		}

		foreach ( $config['info_fields'] as $key => $info_field ) {
			if ( ! isset( $info_field['show_field'] ) ) {
				continue;
			}

			if ( isset( $aliases[ $info_field['show_field'] ] ) ) {
				$info_field['show_field'] = $aliases[ $info_field['show_field'] ];
			}

			if ( $info_field['show_field'] === 'location' ) {
				$info_field['show_field'] = 'location.short';
			}

			$config['info_fields'][ $key ]['label'] = str_replace(
				'[[field]]',
				sprintf( '[[%s]]', $info_field['show_field'] ),
				$info_field['label']
			);

			unset( $config['info_fields'][ $key ]['show_field'] );
		}

		foreach ( $config['footer']['sections'] as $section_key => $section ) {
			if ( $section['type'] === 'author' ) {
				$config['footer']['sections'][ $section_key ]['label'] = str_replace(
					'[[author]]', '[[:authname]]', $section['label']
				);
			}

			if ( $section['type'] === 'details' ) {
				foreach ( $section['details'] as $detail_key => $detail ) {
					if ( ! isset( $detail['show_field'] ) ) {
						continue;
					}

					if ( isset( $aliases[ $detail['show_field'] ] ) ) {
						$detail['show_field'] = $aliases[ $detail['show_field'] ];
					}

					if ( $detail['show_field'] === 'location' ) {
						$detail['show_field'] = 'location.short';
					}

					$config['footer']['sections'][ $section_key ]['details'][ $detail_key ]['label'] = str_replace(
						'[[field]]',
						sprintf( '[[%s]]', $detail['show_field'] ),
						$detail['label']
					);

					unset( $config['footer']['sections'][ $section_key ]['details'][ $detail_key ]['show_field'] );
				}
			}

			// section title isn't used or needed anywhere
			unset( $config['footer']['sections'][ $section_key ]['title'] );
		}

		$this->cache['preview'] = $config;
		return $this->cache['preview'];
	}


	/*=== SEARCH FORMS TAB ===*/

	/**
	 * Get search forms config.
	 *
	 * @since 2.2
	 */
	public function get_search_forms() {
		if ( ! empty( $this->cache['search'] ) ) {
			return $this->cache['search'];
		}

        $data = get_post_meta( $this->listing_type->get_id(), 'case27_listing_type_search_page', true );
		$config = array_replace_recursive(
			$this->default_config['search'],
			is_serialized( $data ) ? @unserialize( $data ) : []
		);

		$config['order']['options'] = $this->validate_ordering_options( $config['order']['options'] );
		$config['explore_tabs'] = $this->validate_explore_tabs( $config['explore_tabs'] );

		$config['advanced']['facets'] = $this->get_search_filters_from_config( (array) $config['advanced']['facets'], 'advanced' );
		$config['basic']['facets'] = $this->get_search_filters_from_config( (array) $config['basic']['facets'], 'basic' );

		$this->cache['search'] = $config;
		return $this->cache['search'];
	}

	/**
	 * Get Explore page ordering options.
	 * Values are parsed as following:
	 * Context: option; value: ':option' (prepend option name with colon)
	 * Context: meta_key; value: 'field_key'
	 * Context: raw_meta_key; value: '_raw_field_key' (prepend field key with underscore)
	 *
	 * @since 1.6
	 * @return array
	 */
	private function validate_ordering_options( $_options ) {
		$defaults = [
			[
				'label' => _x( 'Latest', 'Explore listings: Order by listing date', 'my-listing' ),
				'key' => 'latest',
				'ignore_priority' => false,
				'clauses' => [[
					'orderby' => 'date',
					'order' => 'DESC',
					'context' => 'option',
					'type' => 'CHAR',
					'custom_type' => false,
				]],
			],
			[
				'label' => _x( 'Top rated', 'Explore listings: Order by rating value', 'my-listing' ),
				'key' => 'top-rated',
				'ignore_priority' => true,
				'clauses' => [[
					'orderby' => 'rating',
					'order' => 'DESC',
					'context' => 'option',
					'type' => 'DECIMAL(10,2)',
					'custom_type' => false,
				]],
			],
			[
				'label' => _x( 'Random', 'Explore listings: Order randomly', 'my-listing' ),
				'key' => 'random',
				'ignore_priority' => false,
				'clauses' => [[
					'orderby' => 'rand',
					'order' => 'DESC',
					'context' => 'option',
					'type' => 'CHAR',
					'custom_type' => false,
				]],
			],
		];

		if ( $_options && is_array( $_options ) ) {
			$options = [];

			foreach ( (array) $_options as $option ) {
				if ( empty( $option['key'] ) || empty( $option['label'] ) || empty( $option['clauses'] ) ) {
					continue;
				}

				if ( empty( $option['ignore_priority'] ) ) {
					$option['ignore_priority'] = false;
				}

				foreach ( (array) $option['clauses'] as $clause ) {
					if ( empty( $clause['orderby'] ) || empty( $clause['order'] ) || empty( $clause['context'] ) ) {
						continue(2);
					}

					if ( empty( $clause['type'] ) ) {
						$clause['type'] = 'CHAR';
					}

					if ( $clause['context'] === 'option' && $clause['orderby'] === 'proximity' ) {
						if ( empty( $options['notes'] ) ) {
							$option['notes'] = [];
						}

						$option['notes'][] = 'has-proximity-clause';
					}
				}

				$options[] = $option;
			}

			if ( ! empty( $options ) ) {
				return $options;
			}
		}

		return $defaults;
	}

	/**
	 * Get Explore page sidebar tabs.
	 *
	 * @since 2.1
	 * @return array
	 */
	private function validate_explore_tabs( $_tabs ) {
		// @todo: grab defaults from list of presets instead
		$defaults = [
			'search-form' => [
				'type' => 'search-form',
				'label' => __( 'Filters', 'my-listing' ),
				'icon' => 'mi filter_list',
				'orderby' => '',
				'order' => '',
				'hide_empty' => false,
			],
			'categories' => [
				'type' => 'categories',
				'label' => __( 'Categories', 'my-listing' ),
				'icon' => 'mi bookmark_border',
				'orderby' => 'count',
				'order' => 'DESC',
				'hide_empty' => true,
			],
		];

		if ( $_tabs && is_array( $_tabs ) ) {
			$tabs = [];

			foreach ( (array) $_tabs as $tab ) {
				if ( empty( $tab['type'] ) || empty( $tab['label'] ) || ! isset( $tab['orderby'], $tab['order'], $tab['hide_empty'] ) ) {
					continue;
				}

				if ( empty( $tab['icon'] ) ) {
					$tab['icon'] = 'mi bookmark_border';
				}

				$tabs[ $tab['type'] ] = $tab;
			}

			if ( ! empty( $tabs ) ) {
				return $tabs;
			}
		}

		return $defaults;
	}

	/**
	 * Get list of search filters for this listing type.
	 *
	 * @since 1.5.1
	 */
	private function get_search_filters_from_config( $filterdata, $searchform ) {
		$filters = [];

		// `order` filter is mandatory in advanced search form
		if ( $searchform === 'advanced' && ! in_array( 'order', array_column( $filterdata, 'type' ) ) ) {
			$filterdata[] = [
				'type' => 'order',
				'label' => __( 'Order by', 'my-listing' ),
			];
		}

		foreach ( $filterdata as $key => $filterarr ) {
			if ( ! is_array( $filterarr ) || empty( $filterarr['type'] ) ) {
				continue;
			}

			// check if field class exists
			$filterclass = sprintf( '\MyListing\Src\Listing_Types\Filters\%s', c27()->file2class( $filterarr['type'] ) );
			if ( ! class_exists( $filterclass ) ) {
				mlog()->warn( sprintf( 'No class handler for filter type %s found.', c27()->file2class( $filterarr['type'] ) ) );
				continue;
			}

			// initiate class
			$filter = new $filterclass( $filterarr );
			$filter->set_listing_type( $this->listing_type );
			$filters[] = $filter;
		}

		return $filters;
	}


	/*=== SETTINGS TAB ===*/

	public function get_settings() {
		if ( ! empty( $this->cache['settings'] ) ) {
			return $this->cache['settings'];
		}

        $data = get_post_meta( $this->listing_type->get_id(), 'case27_listing_type_settings_page', true );
		$config = array_replace_recursive(
			$this->default_config['settings'],
			is_serialized( $data ) ? @unserialize( $data ) : []
		);

		$config['reviews']['ratings']['categories'] = $this->validate_review_categories( $config['reviews']['ratings']['categories'] );

		$this->cache['settings'] = $config;
		return $this->cache['settings'];
	}

	private function validate_review_categories( $_categories ) {
		$defaults = [
			'rating' => [
				'id'    => 'rating',
				'label' => esc_html__( 'Your Rating', 'my-listing' ),
			],
		];

		if ( $_categories && is_array( $_categories ) ) {
			$categories = [];

			// Sanitize: make sure all required keys available.
			foreach ( $_categories as $category ) {
				$category = wp_parse_args( $category, [
					'id'    => '',
					'label' => '',
				] );

				if ( $category['id'] ) {
					$categories[ $category['id'] ] = $category;
				}
			}

			return $categories;
		}

		return $defaults;
	}


	/*=== OTHER ===*/

	public function prepare_for_editor() {
		// the listing type editor expects review categories to be an array
		$settings = $this->get_settings();
		$settings['reviews']['ratings']['categories'] = array_values( $settings['reviews']['ratings']['categories'] );

		// the listing type editor expects explore tabs to be an array
		$search = $this->get_search_forms();
		$search['explore_tabs'] = array_values( $search['explore_tabs'] );

        $data = [
            'fields' => [
            	'used' => $this->get_fields(),
            ],
            'single' => $this->get_single_listing(),
            'result' => $this->get_preview_card(),
            'search' => $search,
            'settings' => $settings,
        ];

        // make sure all objects are converted to arrays for proper use
        return json_decode( wp_json_encode( $data ), true );
	}
}
