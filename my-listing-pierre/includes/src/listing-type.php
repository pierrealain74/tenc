<?php

namespace MyListing\Src;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Listing_Type {

	/**
	 * An instance of \MyListing\Src\Listing_Types\Listing_Type_Config class which
	 * is used to load listing type configuration from the database.
	 *
	 * @since 2.2
	 */
    private $config;

    /**
	 * The \WP_Post instance of the listing type, which \MyListing\Src\Listing_Type wraps.
	 *
	 * @since 1.0
     */
	private $data;

	/**
	 * The settings tab configuration.
	 *
	 * @since 1.0
	 */
	public $settings;

	/**
	 * Store listing type instances for the Multition pattern.
	 *
	 * @since 1.0
	 */
	private static $instances = [];

	/**
	 * Listing type ID.
	 *
	 * @since 2.5.0
	 */
	public $id;

	/**
	 * Listing type slug.
	 *
	 * @since 2.5.0
	 */
	public $slug;

	/**
	 * Get a new listing instance (Multiton pattern).
	 * When called the first time, listing will be fetched from database.
	 * Otherwise, it will return the previous instance.
	 *
	 * @since 1.6.0
	 * @param $listing int or \WP_Post
	 */
	public static function get( $type ) {
		if ( is_numeric( $type ) ) {
			$type = get_post( $type );
		}

		if ( ! $type instanceof \WP_Post ) {
			return false;
		}

		if ( $type->post_type !== 'case27_listing_type' ) {
			return false;
		}

		if ( ! array_key_exists( $type->ID, self::$instances ) ) {
			self::$instances[ $type->ID ] = new self( $type );
		}

		return self::$instances[ $type->ID ];
	}

	/**
	 * Retrieve a listing type object by it's post name (slug).
	 *
	 * @since 2.0
	 */
	public static function get_by_name( $postname ) {
		$type_obj = apply_filters(
			'mylisting/listing-types/get-by-name',
			get_page_by_path( $postname, OBJECT, 'case27_listing_type' ),
			$postname
		);

		if ( ! $type_obj ) {
			return false;
		}

		return self::get( $type_obj );
	}

	public function __construct( \WP_Post $post ) {
		$this->data = $post;
		$this->id = $this->data->ID;
		$this->slug = $this->data->post_name;
		$this->config = new \MyListing\Src\Listing_Types\Listing_Type_Config( $this );

		// load settings tab immediately, others will be loaded as needed
		$this->settings = $this->config->get_settings();
	}

	/**
	 * Listing type ID.
	 *
	 * @since 1.0
	 */
	public function get_id() {
		return $this->data->ID;
	}

	/**
	 * Listing type post title.
	 *
	 * @since 1.0
	 */
	public function get_name() {
		return $this->data->post_title;
	}

	/**
	 * Listing type slug.
	 *
	 * @since 1.0
	 */
	public function get_slug() {
		return $this->data->post_name;
	}

	/**
	 * Listing type permalink (%listing_type% in WP Admin > Settings > Permalinks).
	 *
	 * @since 1.0
	 */
	public function get_permalink_name() {
		return $this->settings['permalink'] ? : $this->get_slug();
	}

	/**
	 * Singular name.
	 *
	 * @since 1.0
	 */
	public function get_singular_name() {
		return $this->settings['singular_name'] ? : $this->data->post_title;
	}

	/**
	 * Plural name.
	 *
	 * @since 1.0
	 */
	public function get_plural_name() {
		return $this->settings['plural_name'] ? : $this->data->post_title;
	}

	public function get_data( $key = null ) {
		if ( $key ) {
			if ( isset( $this->data->$key ) ) {
				return $this->data->$key;
			}

			return null;
		}

		return $this->data;
	}

	public function get_fields() {
		return $this->config->get_fields();
	}

	public function get_layout() {
		return $this->config->get_single_listing();
	}

	public function get_field( $key = null ) {
		$fields = $this->config->get_fields();

		if ( $key && ! empty( $fields[ $key ] ) ) {
			return $fields[ $key ];
		}

		return false;
	}

	public function get_default_logo( $size = 'thumbnail' ) {
		if ( $image = wp_get_attachment_image_src( $this->get_data( 'default_logo' ), $size ) ) {
			return $image[0];
		}

		return false;
	}

	public function get_default_cover( $size = 'large' ) {
		if ( $image = wp_get_attachment_image_src( $this->get_data( 'default_cover_image' ), $size ) ) {
			return $image[0];
		}

		return false;
	}

	public function get_settings() {
		return $this->settings;
	}

	public function get_preview_options() {
		return $this->config->get_preview_card();
	}

	public function get_packages() {
		return $this->settings['packages']['used'];
	}

	public function is_rating_enabled() {
		return (bool) $this->settings['reviews']['ratings']['enabled'];
	}

	public function get_icon() {
		if ( 'image' === $this->get_icon_type() && $image_id = c27()->get_attachment_by_guid( $this->get_setting( 'image' ) ) ) {
			return wp_get_attachment_image( $image_id, 'full' );
		}

		// Font Icon.
		ob_start(); ?>
			<i class="<?php echo esc_attr( $this->get_setting('icon') ) ?>"></i>
		<?php return ob_get_clean();
	}

	public function get_icon_type() {
		$type = $this->get_setting( 'icon_type' );
		return 'image' === $type ? 'image' : 'icon';
	}

	/**
	 * Determine whether users are allowed to submit
	 * multiple comments on listings of this listing type.
	 *
	 * @since 2.0
	 */
	public function multiple_comments_allowed() {
		return ! $this->is_rating_enabled() && $this->settings['reviews']['multiple'];
	}

	/**
	 * Check if this is a global listing type.
	 * Global types can be used in the Explore page to query
	 * results within all other listing types.
	 *
	 * @since 1.6.0
	 */
	public function is_global() {
		return (bool) $this->settings['global'];
	}

	public function get_review_mode() {
		return $this->settings['reviews']['ratings']['mode'];
	}

	public function get_review_categories() {
		return $this->settings['reviews']['ratings']['categories'];
	}

	/**
	 * Get Explore page ordering options.
	 *
	 * @since 1.6
	 */
	public function get_ordering_options() {
		$search = $this->config->get_search_forms();
		return $search['order']['options'];
	}

	/**
	 * Get Explore page sidebar tabs.
	 *
	 * @since 2.1
	 * @return array
	 */
	public function get_explore_tabs() {
		$search = $this->config->get_search_forms();
		return $search['explore_tabs'];
	}

	public function is_gallery_enabled() {
		return (bool) $this->settings['reviews']['gallery']['enabled'];
	}

	public function get_package( $package_id ) {
		foreach ($this->settings['packages']['used'] as $package) {
			if ( $package['package'] == $package_id ) {
				return $package;
			}
		}

		return false;
	}

	public function get_advanced_filters() {
		$search = $this->config->get_search_forms();
		return $search['advanced']['facets'];
	}

	/**
	 * Get the `order` filter instance for this listing type.
	 *
	 * @since 2.4
	 */
	public function get_order_filter() {
		$filters = $this->get_advanced_filters();
		foreach ( $filters as $filter ) {
			if ( $filter->get_type() === 'order' ) {
				return $filter;
			}
		}

		return false;
	}

	public function get_selected_filter( $type ) {
		$filters = $this->get_advanced_filters();
		foreach ( $filters as $filter ) {
			if ( $filter->get_type() === $type ) {
				return $filter;
			}
		}

		return false;
	}

	/**
	 * Get the primary filter for this listing type, which is
	 * given higher visibility in some cases.
	 *
	 * @since 2.4
	 */
	public function get_primary_filter() {
		$filters = $this->get_advanced_filters();
		foreach ( $filters as $filter ) {
			if ( $filter->get_prop( 'is_primary' ) ) {
				return $filter;
			}
		}

		// if a wp-search filter exists, use that as fallback
		foreach ( $filters as $filter ) {
			if ( $filter->get_type() === 'wp-search' ) {
				return $filter;
			}
		}

		// finally, use the order filter as that's guaranteed to be present
		return $this->get_order_filter();
	}

	public function get_basic_filters() {
		$search = $this->config->get_search_forms();
		return $search['basic']['facets'];
	}

	public function get_setting( $key = null ) {
		if ( $key && ! empty( $this->settings[ $key ] ) ) {
			return $this->settings[ $key ];
		}

		return false;
	}

	public function get_schema_markup() {
		if ( empty( $this->settings['seo']['markup'] ) || ! is_array( $this->settings['seo']['markup'] ) ) {
			return $this->get_default_schema_markup();
		}

		return $this->settings['seo']['markup'];
	}

	public function get_default_schema_markup() {
		return require locate_template( 'includes/src/listing-types/schema/LocalBusiness.php' );
	}

	public function get_image( $size = 'large' ) {
		$image = wp_get_attachment_image_src( get_post_thumbnail_id( $this->data->ID ), $size );

		return $image ? array_shift( $image ) : false;
	}

	public function get_count() {
		// @todo
		return 0;
	}

    /**
     * Get listing type config.
     *
     * @since  1.7.5
     * @return array
     */
	public function get_config() {
        return $this->config;
	}
}
