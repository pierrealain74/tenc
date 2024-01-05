<?php
/**
 * Settings related to Listing Preview Cards.
 *
 * @since 2.2.3
 */

namespace MyListing\Src\Theme_Options;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Preview_Cards {
	use \MyListing\Src\Traits\Instantiatable;

	public function __construct() {
		// add tab in WP Admin > Theme Tools
		add_filter( 'mylisting/options-page', [ $this, 'add_settings_tab' ], 50 );
		add_filter( 'mylisting/options-page/preview-cards/subtabs', [ $this, 'add_subtabs' ], 50 );
		add_action( 'mylisting/options-page/preview-cards:cache', [ $this, 'render_cache_settings' ] );

		// general settings
		add_action( 'mylisting/options-page/preview-cards:other', [ $this, 'render_other_settings' ] );
		add_action( 'admin_post_mylisting_preview_card_settings', [ $this, 'save_settings' ] );
		add_action( 'init', [ $this, 'apply_settings' ] );

		// add endpoint to enable preview card cache
		add_action( 'admin_post_mylisting_previews_cache_enable', [ $this, 'enable_cache' ] );

		// add endpoint to (re)generate preview card cache
		if ( is_admin() ) {
			add_action( 'wp_ajax_mylisting_previews_cache_generate', [ $this, 'generate_cache' ] );
		}

		// refresh cache when a listing is created or updated
		add_action( 'mylisting/admin/save-listing-data', [ $this, 'refresh_cache_for_listing_and_relations' ], 200 );
		add_action( 'mylisting/submission/save-listing-data', [ $this, 'refresh_cache_for_listing_and_relations' ], 200 );
		add_action( 'mylisting/submission/order-placed', [ $this, 'refresh_cache_for_listing_and_relations' ], 200 );
		add_action( 'mylisting/submission/done', [ $this, 'refresh_cache_for_listing_and_relations' ], 200 );
		add_action( 'mylisting/switched-package', [ $this, 'refresh_cache_for_listing_and_relations' ], 200 );
		add_action( 'mylisting/promotion:start', [ $this, 'refresh_cache_for_listing' ], 200 );
		add_action( 'mylisting/promotion:end', [ $this, 'refresh_cache_for_listing' ], 200 );
		add_action( 'mylisting/reviews/updated-average-rating', [ $this, 'refresh_cache_for_listing' ], 200 );
	}

	public function add_settings_tab( $tabs ) {
		$tabs['preview-cards'] = '<i class="mi view_day" style="color:#69c9b4;"></i> Preview Cards';
		return $tabs;
	}

	public function add_subtabs( $subtabs ) {
		return [
			'cache' => 'Caching',
			'other' => 'Other settings',
		];
	}

	/**
	 * Render preview card cache settings.
	 *
	 * @since 2.2.3
	 */
	public function render_cache_settings() {
		global $wpdb;

		// get listing types with title and slug to display in a <select> option
		$listing_types = $wpdb->get_results( "SELECT post_title, post_name FROM {$wpdb->posts} WHERE post_type = 'case27_listing_type' AND post_status = 'publish'", ARRAY_A );

		// is caching enabled
		$enabled = (bool) get_option( 'mylisting_cache_previews' );

		// endpoint to enable/disable cache
		$enable_endpoint = admin_url( 'admin-post.php?action=mylisting_previews_cache_enable&_wpnonce='.wp_create_nonce( 'mylisting_previews_cache' ) );

		// is (re)generating of cache requested?
		$generate = ! empty( $_GET['generate'] ) ? sanitize_text_field( $_GET['generate'] ) : false;

		// endpoint to regenerate cache
		$generate_endpoint = admin_url( 'admin-ajax.php?action=mylisting_previews_cache_generate&_wpnonce='.wp_create_nonce( 'mylisting_previews_cache' ) );

		// if a specific listing type is request to regenerate cache, append to the endpoint
		if ( $generate && $generate !== 'all' ) {
			$generate_endpoint .= '&listing_type='.$generate;
		}

		if ( ! empty( $_GET['full-regen'] ) ) {
			$generate_endpoint .= '&full_regen=1';
		}

		$cache_count = $this->get_cached_file_count();

		require locate_template( 'templates/admin/theme-options/preview-cards-cache.php' );
	}

	/**
	 * Render preview card settings.
	 *
	 * @since 2.3.4
	 */
	public function render_other_settings() {
		$settings = $this->get_settings();
		require locate_template( 'templates/admin/theme-options/preview-cards-other.php' );
	}

	/**
	 * Retrieve from database and validate preview card settings.
	 *
	 * @since 2.3.4
	 */
	public function get_settings() {
		$values = (array) json_decode( get_option( 'mylisting_preview_cards', null ), ARRAY_A );
		$settings = [
			'bg_size' => 'medium_large',
			'gallery_count' => 3,
		];

		foreach ( $values as $key => $value ) {
			if ( isset( $settings[ $key ] ) ) {
				$settings[ $key ] = $value;
			}
		}

		return $settings;
	}

	/**
	 * Endpoint to handle settings form.
	 *
	 * @since 2.3.4
	 */
	public function save_settings() {
		check_admin_referer( 'mylisting_preview_card_settings' );
		if ( ! current_user_can( 'manage_options' ) ) {
			die;
		}

		$config = [
			'bg_size' => ! empty( $_POST['bg_size'] ) ? sanitize_text_field( $_POST['bg_size'] ) : 'medium_large',
			'gallery_count' => ! empty( $_POST['gallery_count'] ) ? absint( $_POST['gallery_count'] ) : 3,
		];

        update_option( 'mylisting_preview_cards', wp_json_encode( $config ) );

		return wp_safe_redirect( admin_url( 'admin.php?page=mylisting-options&active_tab=preview-cards&subtab=other' ) );
	}

	/**
	 * Apply settings to the preview card.
	 *
	 * @since 2.3.4
	 */
	public function apply_settings() {
		$settings = $this->get_settings();
		add_filter( 'mylisting/preview-card:bg-size', function() use ( $settings ) {
			return $settings['bg_size'];
		} );
		add_filter( 'mylisting/preview-card:gallery-count', function() use ( $settings ) {
			return absint( $settings['gallery_count'] );
		} );
	}

	/**
	 * Get preview card cache directory status.
	 *
	 * @since 2.2.3
	 */
	public function get_cached_file_count() {
		$count = 0;
		$dir = trailingslashit( wp_upload_dir()['basedir'] ).'preview-cards/';
		if ( ! file_exists( $dir ) ) {
			return $count;
		}

		$groups = scandir( $dir );
		foreach ( (array) $groups as $group ) {
			if ( in_array( $group, [ '.', '..' ], true ) || ! is_dir( trailingslashit( $dir ).$group ) ) {
				continue;
			}

			$count += ( count( scandir( trailingslashit( $dir ).$group ) ) - 2 );
		}

		return $count;
	}

	/**
	 * Handler for the `previews_cache_enable` endpoint.
	 *
	 * @since 2.2.3
	 */
	public function enable_cache() {
		check_admin_referer( 'mylisting_previews_cache' );
		if ( ! current_user_can( 'manage_options' ) ) {
			die;
		}

		$enabled = (bool) get_option( 'mylisting_cache_previews' );
		if ( $enabled ) {
			delete_option( 'mylisting_cache_previews' );
		} else {
			update_option( 'mylisting_cache_previews', true );
		}

		return wp_safe_redirect(
			(bool) get_option( 'mylisting_cache_previews' )
				? admin_url( 'admin.php?page=mylisting-options&active_tab=preview-cards&generate=all' )
				: admin_url( 'admin.php?page=mylisting-options&active_tab=preview-cards' )
		);
	}

	/**
	 * Handler for the `previews_cache_generate` endpoint.
	 *
	 * @since 2.2.3
	 */
	public function generate_cache() {
		check_admin_referer( 'mylisting_previews_cache' );
		if ( ! current_user_can( 'manage_options' ) ) {
			die;
		}

        global $wpdb;

        // generate only for a specific listing type
        $listing_type = ! empty( $_GET['listing_type'] ) ? sanitize_text_field( $_GET['listing_type'] ) : false;

        $full_regen = ! empty( $_GET['full_regen'] );

        // calculate total number of listings (or carry from the previous batch for better performance)
        if ( ! empty( $_GET['total'] ) ) {
        	$total_listings = absint( $_GET['total'] );
        } else {
        	$total_listings_sql = "SELECT COUNT(ID) FROM {$wpdb->posts} WHERE post_type = 'job_listing' AND post_status = 'publish'";
        	if ( $listing_type ) {
        		$total_listings_sql = $wpdb->prepare( "
        			SELECT COUNT(ID) FROM {$wpdb->posts} LEFT JOIN {$wpdb->postmeta} ON ({$wpdb->posts}.ID = {$wpdb->postmeta}.post_id)
        			WHERE post_type = 'job_listing' AND {$wpdb->postmeta}.meta_value = %s AND post_status = 'publish'
        		", $listing_type );
        	}

        	$total_listings = absint( $wpdb->get_var( $total_listings_sql ) );
        }

		// run for up to 60 seconds
		$max_execution_time = absint( ini_get('max_execution_time') );
		$time_limit = $max_execution_time === 0 ? 60 : min( 60, ( $max_execution_time - 10 ) );
        @set_time_limit( $time_limit + 30 );
        $starttime = microtime( true );

		// use up to 80% of the memory limit before moving on to the next chunk
		// if Query Monitor plugin is active, reduce that a bit to avoid overflows
		$max_memory_usage = class_exists( '\QueryMonitor' ) ? 0.7 : 0.8;
		$memory_limit = absint( \MyListing\return_bytes( ini_get( 'memory_limit' ) ) * $max_memory_usage );

		// number of processed listings
		$processed_listings = ! empty( $_GET['processed'] ) ? absint( $_GET['processed'] ) : 0;
		$last_id = ! empty( $_GET['last_id'] ) ? absint( $_GET['last_id'] ) : 0;

		// maybe delete the directory completely for full regeneration
		if ( $last_id === 0 && ! $listing_type && $full_regen ) {
			\MyListing\delete_directory( trailingslashit( wp_upload_dir()['basedir'] ).'preview-cards/' );
		}

		// get the listings
		$listings_sql = $wpdb->prepare( "
			SELECT ID FROM {$wpdb->posts}
			WHERE post_type = 'job_listing' AND post_status = 'publish' AND ID > %d
			ORDER BY ID ASC LIMIT 500
		", $last_id );

		// filter by listing type if requested
		if ( $listing_type ) {
			$listings_sql = $wpdb->prepare( "
				SELECT ID FROM {$wpdb->posts} LEFT JOIN {$wpdb->postmeta} ON ({$wpdb->posts}.ID = {$wpdb->postmeta}.post_id)
				WHERE post_type = 'job_listing' AND post_status = 'publish' AND ID > %d AND {$wpdb->postmeta}.meta_value = %s
				ORDER BY ID ASC LIMIT 500
			", $last_id, $listing_type );
		}

	    // get listing ids
		$listings = $wpdb->get_results( $listings_sql, ARRAY_A );

		foreach ( $listings as $listing ) {
			\MyListing\cache_preview_card( absint( $listing['ID'] ) );
			$processed_listings++;
			$last_id = absint( $listing['ID'] );

			$time_left = $time_limit - ( microtime(true) - $starttime );
			$memory_left = $memory_limit - memory_get_usage();
			if ( $time_left < 0 || $memory_left < 0 ) {
				break;
			}
		}

		return wp_send_json_success( [
			'total' => $total_listings,
			'processed' => $processed_listings,
			'last_id' => $last_id,
			'done' => ( $total_listings - $processed_listings ) < 1,
			'listing_type' => $listing_type,
		], 200 );
	}

	public function refresh_cache_for_listing( $listing_id, $force_get = true ) {
		$listing = $force_get ? \MyListing\Src\Listing::force_get( $listing_id ) : \MyListing\Src\Listing::get( $listing_id );
		if ( ! $listing ) {
			return;
		}

		if ( $listing->get_status() === 'publish' ) {
			\MyListing\cache_preview_card( $listing->get_id() );
			mlog()->note( 'Cached preview card for listing #'.$listing->get_id().' ('.current_action().')' );
		} else {
			\MyListing\delete_cached_preview_card( $listing->get_id() );
			mlog()->note( 'Removed preview card cache for listing #'.$listing->get_id().' ('.current_action().')' );
		}
	}

	public function refresh_cache_for_listing_and_relations( $listing_id ) {
		$listing = \MyListing\Src\Listing::force_get( $listing_id );
		if ( ! $listing ) {
			return;
		}

		$this->refresh_cache_for_listing( $listing->get_id(), false );
		$this->refresh_cache_for_relations( $listing->get_id() );
	}

	public function refresh_cache_for_relations( $listing_id ) {
		if ( ! ( $listing = \MyListing\Src\Listing::get( $listing_id ) ) ) {
			return;
		}

		global $wpdb;
		$parent_ids = $wpdb->get_col( $wpdb->prepare( "
			SELECT parent_listing_id FROM {$wpdb->prefix}mylisting_relations
			WHERE child_listing_id = %d ORDER BY item_order ASC
		", $listing->get_id() ) );

		$child_ids = $wpdb->get_col( $wpdb->prepare( "
			SELECT child_listing_id FROM {$wpdb->prefix}mylisting_relations
			WHERE parent_listing_id = %d ORDER BY item_order ASC
		", $listing->get_id() ) );

		$ids = array_map( 'absint', array_unique( array_merge( (array) $parent_ids, (array) $child_ids ) ) );
		foreach ( $ids as $id ) {
			$related_listing = \MyListing\Src\Listing::get( $id );
			if ( $related_listing && $related_listing->get_status() === 'publish' ) {
				mlog()->note( 'Cached preview card for relation #'.$related_listing->get_id().' ('.current_action().')' );
				\MyListing\cache_preview_card( $related_listing->get_id() );
			}
		}
	}
}