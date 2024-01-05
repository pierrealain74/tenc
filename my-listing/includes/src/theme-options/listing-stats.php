<?php
/**
 * Settings related to Listing Stats.
 *
 * @since 2.3.4
 */

namespace MyListing\Src\Theme_Options;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Listing_Stats {
	use \MyListing\Src\Traits\Instantiatable;

	private $settings = [
		// amount of time (minutes) to cache user stats for
		'cache_time' => 60,

		// amount of time (days) to wait before the visit is removed from db
		'db_time' => 30,

		// palette colors
		'color1' => '#6c1cff',
		'color2' => '#911cff',
		'color3' => '#6c1cff',
		'color4' => '#0079e0',

		// visits chart
		'enable_chart' => true,
		'chart_categories' => [ 'lastday', 'lastweek', 'lastmonth', 'lasthalfyear', 'lastyear' ],
		'views_color' => '#0079e0',
		'uviews_color' => '#911cff',

		// stat boxes
		'show_referrers' => true,
		'show_browsers' => true,
		'show_platforms' => true,
		'show_countries' => true,
		'show_devices' => true,
		'show_views' => true,
		'show_uviews' => true,
		'show_tracks' => true,
	];

	private $config;

	public function __construct() {
		// add tab in WP Admin > Theme Tools
		add_action( 'admin_menu', [ $this, 'add_settings_page' ], 50 );
        add_action( 'admin_init', [ $this, 'maybe_migrate_options' ] );

        // add endpoint to update settings
		add_action( 'admin_post_mylisting_update_userdash', [ $this, 'save_settings' ] );

		// lazy-load options to be accessed using `mylisting()->get()`
		add_filter( 'mylisting/load-options:stats', [ $this, 'load_options' ] );
	}

	public function add_settings_page() {
		add_submenu_page(
			'case27/tools.php',
			_x( 'Listing Stats', 'Listing Stats page title in WP Admin', 'my-listing' ),
			_x( 'Listing Stats', 'Listing Stats page title in WP Admin', 'my-listing' ),
			'manage_options',
			'theme-stats-settings',
			[ $this, 'render_settings' ]
		);
	}

	/**
	 * Lazy-load map options to be accessed using `mylisting()->get()`.
	 *
	 * @since 2.4
	 */
	public function load_options() {
		return $this->get_config();
	}

	public function render_settings() {
		$config = $this->get_config();

		wp_enqueue_style( 'wp-color-picker');
		wp_enqueue_script( 'wp-color-picker');
		require locate_template( 'templates/admin/theme-options/listing-stats.php' );
	}

	public function get_config() {
		$config = $this->settings;
		$values = (array) json_decode( get_option( 'mylisting_userdash', null ), ARRAY_A );
		foreach ( $values as $key => $value ) {
			if ( isset( $config[ $key ] ) ) {
				$config[ $key ] = $value;
			}
		}

		return $config;
	}

	public function set_config( $new_config ) {
		$config = $this->get_config();
		foreach ( $new_config as $key => $value ) {
			if ( isset( $config[ $key ] ) ) {
				$config[ $key ] = $value;
			}
		}

        update_option( 'mylisting_userdash', wp_json_encode( $config ) );
	}

	/**
	 * Handler for the `mylisting_update_userdash` endpoint.
	 *
	 * @since 2.3.4
	 */
	public function save_settings() {
		check_admin_referer( 'mylisting_update_userdash' );
		if ( ! current_user_can( 'manage_options' ) ) {
			die;
		}

    	$config = [];
    	$config['cache_time'] = ! empty( $_POST['cache_time'] ) ? absint( $_POST['cache_time'] ) : '';
    	$config['db_time'] = ! empty( $_POST['db_time'] ) ? absint( $_POST['db_time'] ) : '';

    	$config['color1'] = ! empty( $_POST['color1'] ) ? sanitize_text_field( $_POST['color1'] ) : '';
    	$config['color2'] = ! empty( $_POST['color2'] ) ? sanitize_text_field( $_POST['color2'] ) : '';
    	$config['color3'] = ! empty( $_POST['color3'] ) ? sanitize_text_field( $_POST['color3'] ) : '';
    	$config['color4'] = ! empty( $_POST['color4'] ) ? sanitize_text_field( $_POST['color4'] ) : '';
    	$config['views_color'] = ! empty( $_POST['views_color'] ) ? sanitize_text_field( $_POST['views_color'] ) : '';
    	$config['uviews_color'] = ! empty( $_POST['uviews_color'] ) ? sanitize_text_field( $_POST['uviews_color'] ) : '';

    	$config['enable_chart'] = ! empty( $_POST['enable_chart'] ) ? true : false;
    	$config['show_referrers'] = ! empty( $_POST['show_referrers'] ) ? true : false;
    	$config['show_browsers'] = ! empty( $_POST['show_browsers'] ) ? true : false;
    	$config['show_platforms'] = ! empty( $_POST['show_platforms'] ) ? true : false;
    	$config['show_countries'] = ! empty( $_POST['show_countries'] ) ? true : false;
    	$config['show_devices'] = ! empty( $_POST['show_devices'] ) ? true : false;
    	$config['show_views'] = ! empty( $_POST['show_views'] ) ? true : false;
    	$config['show_uviews'] = ! empty( $_POST['show_uviews'] ) ? true : false;
    	$config['show_tracks'] = ! empty( $_POST['show_tracks'] ) ? true : false;

    	$chart_categories = ! empty( $_POST['chart_categories'] ) ? (array) $_POST['chart_categories'] : [];
    	$config['chart_categories'] = array_map( 'sanitize_text_field', $chart_categories );

    	$this->set_config( $config );

		return wp_safe_redirect( admin_url( 'admin.php?page=theme-stats-settings&success=1' ) );
	}


    /**
	 * Migrate stat options from ACF fields to `mylisting_userdash`
	 * field in `wp_options`. Cleanup old ACF fields in the process.
	 *
	 * @since 2.3.4
     */
    public function maybe_migrate_options() {
    	if ( get_option( 'mylisting_userdash', null ) !== null ) {
    		return;
    	}

    	mlog()->note( 'Migrating user dashboard settings' );
    	$config = $this->get_config();

    	$delete_after = get_option( 'options_stats_general_delete_after', null );
    	if ( is_numeric( $delete_after ) ) {
    		$config['db_time'] = absint( $delete_after );
    	}

    	if ( $color1 = get_option( 'options_stats_general_colors_one' ) ) { $config['color1'] = $color1; }
    	if ( $color2 = get_option( 'options_stats_general_colors_two' ) ) { $config['color2'] = $color2; }
    	if ( $color3 = get_option( 'options_stats_general_colors_three' ) ) { $config['color3'] = $color3; }
    	if ( $color4 = get_option( 'options_stats_general_colors_four' ) ) { $config['color4'] = $color4; }
    	if ( $views_color = get_option( 'options_stats_visits_chart_views_color' ) ) { $config['views_color'] = $views_color; }
    	if ( $uviews_color = get_option( 'options_stats_visits_chart_unique_views_color' ) ) { $config['uviews_color'] = $uviews_color; }

    	$config['enable_chart'] = get_option( 'options_stats_visits_chart_enabled' ) === '0' ? false : true;
    	$config['show_referrers'] = get_option( 'options_stats_referrers_enabled' ) === '0' ? false : true;
    	$config['show_browsers'] = get_option( 'options_stats_browsers_enabled' ) === '0' ? false : true;
    	$config['show_platforms'] = get_option( 'options_stats_platforms_enabled' ) === '0' ? false : true;
    	$config['show_countries'] = get_option( 'options_stats_countries_enabled' ) === '0' ? false : true;
    	$config['show_devices'] = get_option( 'options_stats_devices_enabled' ) === '0' ? false : true;
    	$config['show_views'] = get_option( 'options_stats_views_section_enabled' ) === '0' ? false : true;
    	$config['show_uviews'] = get_option( 'options_stats_unique_views_section_enabled' ) === '0' ? false : true;

    	$chart_categories = get_option( 'options_stats_visits_chart_categories' );
    	if ( is_array( $chart_categories ) && ! empty( $chart_categories ) ) {
    		$config['chart_categories'] = $chart_categories;
    	}

    	$this->set_config( $config );

    	$cleanup = [
    		'stats_unique_views_section_enabled',
    		'stats_views_section_enabled',
    		'stats_devices_enabled',
    		'stats_countries_enabled',
    		'stats_platforms_enabled',
    		'stats_browsers_enabled',
    		'stats_referrers_default_state',
    		'stats_referrers_enabled',
    		'stats_visits_chart_unique_views_color',
    		'stats_visits_chart_views_color',
    		'stats_visits_chart_categories',
    		'stats_visits_chart_enabled',
    		'stats_general_colors',
    		'stats_general_colors_one',
    		'stats_general_colors_two',
    		'stats_general_colors_three',
    		'stats_general_colors_four',
    		'stats_general_delete_after',
    	];

    	foreach ( $cleanup as $option_name ) {
    		delete_option( $option_name );
    		delete_option( '_'.$option_name );
    		delete_option( 'options_'.$option_name );
    		delete_option( '_options_'.$option_name );
    	}
    }
}