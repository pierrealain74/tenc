<?php
/**
 * Get the default configuration structure for listing types.
 *
 * @since 2.2
 */

namespace MyListing\Src\Listing_Types;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Default_Config {
	public static function get() {
		return [
			// settings tab
			'settings' => [
				'icon_type'	=> 'icon',
			    'icon' => '',
			    'image'	=> '',
			    'singular_name' => '',
			    'plural_name' => '',
			    'permalink' => '',
			    'global' => false,
			    'packages' => [
			    	'enabled' => true,
			        'used' => [],
			    ],
				'reviews' => [
					'multiple' => false,
					'ratings' => [
						'enabled' => true,
						'categories' => [],
						'mode' => 10, // 10 stars or 5 stars
					],
					'gallery' => [
						'enabled' => false,
					],
				],
				'seo' => [
					'markup' => new \stdClass,
				],
				'expiry_rules' => [],
			],

			// fields tab
			'fields' => [
				'used' => [],
			],

			// single listing tab
			'single' => [
			    'menu_items' => [],
			    'quick_actions' => [],
			    'cover_details' => [],
			    'cover_actions' => [],
			    'cover' => [
			    	'type' => 'image', // image or gallery
			    ],
			    'similar_listings' => [
			    	'enabled' => true,
			    	'match_by_type' => true,
			    	'match_by_category' => true,
			    	'match_by_tags' => false,
			    	'match_by_region' => false,
			    	'listing_count' => 3,
			    	'orderby' => 'priority', // priority|rating|proximity
			    	'max_proximity' => 100, // km
			    ],
			],

			// preview card tab
			'result' => [
				'template' => 'default', // default or alternate
				'buttons' => [],
				'info_fields' => [],
				'background' => [
					'type' => 'image',
				],
				'footer' => [
					'sections' => [],
				],
				'quick_view' => [
					'template' => 'default',
					'map_skin' => 'skin1',
					'taxonomy' => 'job_listing_category',
					'taxonomy_label' => 'Categories',
				],
				'map_marker' => [
					'taxonomy' => 'job_listing_category',
				],
			],

			// search forms tab
			'search' => [
			    'advanced' => [
			    	'facets' => [],
			    ],
			    'basic' => [
			    	'facets' => [],
			    ],
			    'order' => [

			    	/**
					 * List of options by which listing can be ordered.
					 * Each option can contain one or more ordering clauses.
			         * string options[][label]
			         * array options[][clauses]
			    	 */
			    	'options' => [],
			    	'default' => 'date',
			    ],

			    /**
			     * List of tabs to be shown in Explore page sidebar.
			     *
			     * @since 2.1
			     * @type array
			     *     string tab[label]
			     *     string tab[icon]
			     *     string tab[type]
			     *     string tab[orderby]
			     *     string tab[order]
			     *     bool   tab[hierarchical]
			     *     bool   tab[hide_empty]
			     */
			    'explore_tabs' => [],
			],
		];
	}
}