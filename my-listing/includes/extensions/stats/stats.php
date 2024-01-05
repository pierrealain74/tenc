<?php

namespace MyListing\Ext\Stats;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Stats {
	use \MyListing\Src\Traits\Instantiatable;

	private
		$user_stats = [],
		$listing_stats = [],
		$admin_stats,
		$cache_validity;

	public function __construct() {
		// General stats
		\MyListing\Ext\Stats\General::instance();

		// Set cache validity.
		$this->cache_validity = apply_filters( 'mylisting/stats/cache-validity', ( absint( mylisting()->get( 'stats.cache_time' ) ?: 60 ) * MINUTE_IN_SECONDS ) );

		add_filter( 'mylisting/user-listings/actions', [ $this, 'display_stats_action' ], 30 );

		// Display stats in admin dashboard.
		add_action( 'wp_dashboard_setup', [ $this, 'admin_dashboard_stats' ] );

		add_action( 'transition_post_status', [ $this, 'refresh_cache_on_listing_status_update' ], 40, 3 );
	}

	public function get_user_stats( $user_id ) {
		if ( empty( $this->user_stats[ $user_id ] ) ) {
			$cache = get_user_option( '_mylisting_stats_cache', $user_id );
			$last_update = is_array( $cache ) && ! empty( $cache['updated_on'] ) ? absint( $cache['updated_on'] ) : 0;

			// Check if the last cache update is within the cache validity.
			if ( ( time() - $last_update ) <= $this->cache_validity ) {
				mlog('Retrieved user stats from cache.');

				$this->user_stats[ $user_id ] = new Stat_Group( $cache );
				return $this->user_stats[ $user_id ];
			}

			// Otherwise, fetch stats from database, and save them in cache.
			mlog()->warn('Queried user stats from database');
			$stats = apply_filters( 'mylisting/stats/user', [], $user_id );
			$stats['updated_on'] = time();

			// Cache results in user meta.
			update_user_option( $user_id, '_mylisting_stats_cache', $stats );

			$this->user_stats[ $user_id ] = new Stat_Group( $stats );
		}

		return $this->user_stats[ $user_id ];
	}

	public function get_listing_stats( $listing_id ) {
		if ( empty( $this->listing_stats[ $listing_id ] ) ) {
			$cache = get_post_meta( $listing_id, '_mylisting_stats_cache', true );
			$last_update = is_array( $cache ) && ! empty( $cache['updated_on'] ) ? absint( $cache['updated_on'] ) : 0;

			// Check if the last cache update is within the cache validity.
			if ( ( time() - $last_update ) <= $this->cache_validity ) {
				mlog('Retrieved listing stats from cache.');

				$this->listing_stats[ $listing_id ] = new Stat_Group( $cache );
				return $this->listing_stats[ $listing_id ];
			}

			// Otherwise, fetch stats from database, and save them in cache.
			mlog()->warn('Queried listing stats from database');
			$stats = apply_filters( 'mylisting/stats/listing', [], $listing_id );
			$stats['updated_on'] = time();

			// Cache results in listing meta.
			update_post_meta( $listing_id, '_mylisting_stats_cache', $stats );

			$this->listing_stats[ $listing_id ] = new Stat_Group( $stats );
		}

		return $this->listing_stats[ $listing_id ];
	}

	public function get_admin_stats() {
		if ( empty( $this->admin_stats ) ) {
			$cache = get_option( '_mylisting_stats_cache' );
			$last_update = is_array( $cache ) && ! empty( $cache['updated_on'] ) ? absint( $cache['updated_on'] ) : 0;

			// Check if the last cache update is within the cache validity.
			if ( ( time() - $last_update ) <= $this->cache_validity ) {
				mlog('Retrieved admin stats from cache.');

				$this->admin_stats = new Stat_Group( $cache );
				return $this->admin_stats;
			}

			// Otherwise, fetch stats from database, and save them in cache.
			mlog()->warn('Queried admin stats from database');
			$stats = apply_filters( 'mylisting/stats/admin', [] );
			$stats['updated_on'] = time();

			// Cache results in wp_options.
			update_option( '_mylisting_stats_cache', $stats );

			$this->admin_stats = new Stat_Group( $stats );
		}

		return $this->admin_stats;
	}

	/**
	 * Adds a `Stats` button under listing actions
	 * in User Dashboard > My Listings.
	 *
	 * @since 2.0
	 */
	public function display_stats_action( $listing ) {
		if ( $listing->get_status() !== 'publish' ) {
			return;
		}

		printf(
			'<li class="cts-listing-action-stats">
				<a href="%s" class="listing-action-stats">%s</a>
			</li>',
			esc_url( add_query_arg( [ 'listing' => $listing->get_id() ], wc_get_account_endpoint_url( 'dashboard' ) ) ),
				_x( 'Stats', 'User listings dashboard', 'my-listing' )
		);
	}

	/**
	 * Display stats in admin dashboard.
	 *
	 * @since 2.0
	 */
	public function admin_dashboard_stats() {
		// Get site stats.
		$stats = mylisting()->stats()->get_admin_stats();

		// Visits chart.
		wp_add_dashboard_widget(
			'mylisting_stats_visits_chart',
			_x( 'Listing visit stats', 'WP Admin > Dashboard stats', 'my-listing' ),
			function() use( $stats ) {
				require locate_template( 'templates/dashboard/stats/widgets/visits-chart.php' );
			}
		);

		// Views widget.
		wp_add_dashboard_widget(
			'mylisting_stats_views',
			_x( 'Listing visit stats', 'WP Admin > Dashboard stats', 'my-listing' ),
			function() use( $stats ) {
				require locate_template( 'templates/dashboard/stats/widgets/views.php' );
				require locate_template( 'templates/dashboard/stats/widgets/unique-views.php' );
			}
		);

		// Countries widget.
		wp_add_dashboard_widget(
			'mylisting_stats_countries',
			_x( 'Countries', 'WP Admin > Dashboard stats', 'my-listing' ),
			function() use( $stats ) {
				require locate_template( 'templates/dashboard/stats/widgets/countries.php' );
			}
		);

		// Platforms widget.
		wp_add_dashboard_widget(
			'mylisting_stats_platforms',
			_x( 'Platforms', 'WP Admin > Dashboard stats', 'my-listing' ),
			function() use( $stats ) {
				require locate_template( 'templates/dashboard/stats/widgets/platforms.php' );
				require locate_template( 'templates/dashboard/stats/widgets/devices.php' );
			}
		);

		// Browsers widget.
		wp_add_dashboard_widget(
			'mylisting_stats_browsers',
			_x( 'Browsers', 'WP Admin > Dashboard stats', 'my-listing' ),
			function() use( $stats ) {
				require locate_template( 'templates/dashboard/stats/widgets/browsers.php' );
			}
		);

		// Referrers widget.
		wp_add_dashboard_widget(
			'mylisting_stats_referrers',
			_x( 'Referrers', 'WP Admin > Dashboard stats', 'my-listing' ),
			function() use( $stats ) {
				require locate_template( 'templates/dashboard/stats/widgets/referrers.php' );
			}
		);

		// Dashboard scripts and styles.
		add_action( 'admin_enqueue_scripts', function() {
			wp_enqueue_style( 'mylisting-dashboard' );
			wp_enqueue_script( 'mylisting-dashboard' );
			wp_enqueue_style( 'mylisting-admin-dashboard' );
		} );
	}

	public function refresh_cache_on_listing_status_update( $new_status, $old_status, $post ) {
		if ( $post && $post->post_type === 'job_listing' && $post->post_author ) {
			delete_user_option( $post->post_author, '_mylisting_stats_cache' );
		}
	}
}