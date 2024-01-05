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

class Map_Services {
	use \MyListing\Src\Traits\Instantiatable;

	private $settings = [
		'provider' => 'google-maps',

		// Google Maps settings
		'gmaps_api_key' => '',
		'gmaps_lang' => 'default',
		'gmaps_types' => 'geocode',
		'gmaps_locations' => [],
		'gmaps_skins' => [],

		// Mapbox settings
		'mapbox_api_key' => '',
		'mapbox_lang' => 'default',
		'mapbox_types' => [],
		'mapbox_locations' => [],
		'mapbox_skins' => [],
	];

	private $config;

	public function __construct() {
		// add tab in WP Admin > Theme Tools
		add_action( 'admin_menu', [ $this, 'add_settings_page' ], 50 );
        add_action( 'admin_init', [ $this, 'maybe_migrate_options' ] );

        // add endpoint to update settings
		add_action( 'admin_post_mylisting_update_mapservices', [ $this, 'save_settings' ] );

		// lazy-load map options to be accessed using `mylisting()->get()`
		add_filter( 'mylisting/load-options:maps', [ $this, 'load_options' ] );
	}

	/**
	 * Lazy-load map options to be accessed using `mylisting()->get()`.
	 *
	 * @since 2.4
	 */
	public function load_options() {
		return $this->get_config();
	}

	/**
	 * Render settings page in WP Admin > Theme Tools > Map Services.
	 *
	 * @since 2.4
	 */
	public function add_settings_page() {
		add_submenu_page(
			'case27/tools.php',
			_x( 'Map Services', 'Map Services page title in WP Admin', 'my-listing' ),
			_x( 'Map Services', 'Map Services page title in WP Admin', 'my-listing' ),
			'manage_options',
			'theme-mapservice-settings',
			function() {
				$config = $this->get_config();
				require locate_template( 'templates/admin/theme-options/map-services.php' );
			}
		);
	}

	public function get_config() {
		$config = $this->settings;
		$values = (array) json_decode( get_option( 'mylisting_maps', null ), ARRAY_A );
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

        update_option( 'mylisting_maps', wp_json_encode( $config ) );
	}

	/**
	 * Handler for the `mylisting_update_mapservices` endpoint.
	 *
	 * @since 2.3.4
	 */
	public function save_settings() {
		check_admin_referer( 'mylisting_update_mapservices' );
		if ( ! current_user_can( 'manage_options' ) ) {
			die;
		}

		$config = [];
		$config['provider'] = ! empty( $_POST['provider'] ) ? sanitize_text_field( $_POST['provider'] ) : 'google-maps';

		// google maps
		$config['gmaps_api_key'] = ! empty( $_POST['gmaps_api_key'] ) ? sanitize_text_field( $_POST['gmaps_api_key'] ) : '';
		$config['gmaps_lang'] = ! empty( $_POST['gmaps_lang'] ) ? sanitize_text_field( $_POST['gmaps_lang'] ) : 'default';
		$config['gmaps_types'] = ! empty( $_POST['gmaps_types'] ) ? sanitize_text_field( $_POST['gmaps_types'] ) : 'geocode';
		$config['gmaps_locations'] = ! empty( $_POST['gmaps_locations'] )
				? array_map( 'sanitize_text_field', (array) $_POST['gmaps_locations'] )
				: [];

		// google maps skins
		$config['gmaps_skins'] = [];
		if ( ! empty( $_POST['gmaps_skins'] ) && ! empty( $_POST['gmaps_skinkeys'] ) ) {
			foreach ( (array) $_POST['gmaps_skins'] as $i => $skin ) {
				if ( ! empty( $skin ) && ! empty( $_POST['gmaps_skinkeys'][$i] ) && ( $skinval = json_decode( stripslashes( $skin ) ) ) ) {
					$config['gmaps_skins'][ sanitize_text_field( $_POST['gmaps_skinkeys'][$i] ) ] = json_encode( $skinval );
				}
			}
		}

		// mapbox
		$config['mapbox_api_key'] = ! empty( $_POST['mapbox_api_key'] ) ? sanitize_text_field( $_POST['mapbox_api_key'] ) : '';
		$config['mapbox_lang'] = ! empty( $_POST['mapbox_lang'] ) ? sanitize_text_field( $_POST['mapbox_lang'] ) : 'default';
		$config['mapbox_types'] = ! empty( $_POST['mapbox_types'] )
				? array_map( 'sanitize_text_field', (array) $_POST['mapbox_types'] )
				: [];
		$config['mapbox_locations'] = ! empty( $_POST['mapbox_locations'] )
				? array_map( 'sanitize_text_field', (array) $_POST['mapbox_locations'] )
				: [];

		// mapbox skins
		$config['mapbox_skins'] = [];
		if ( ! empty( $_POST['mapbox_skins'] ) && ! empty( $_POST['mapbox_skinkeys'] ) ) {
			foreach ( (array) $_POST['mapbox_skins'] as $i => $skin ) {
				if ( ! empty( $skin ) && ! empty( $_POST['mapbox_skinkeys'][$i] ) ) {
					if ( $skinval = json_decode( stripslashes( $skin ) ) ) {
						$config['mapbox_skins'][ sanitize_text_field( $_POST['mapbox_skinkeys'][$i] ) ] = json_encode( $skinval );
					} else {
						$config['mapbox_skins'][ sanitize_text_field( $_POST['mapbox_skinkeys'][$i] ) ] = $skin;
					}
				}
			}
		}

    	$this->set_config( $config );

		return wp_safe_redirect( admin_url( 'admin.php?page=theme-mapservice-settings&success=1' ) );
	}


    /**
	 * Migrate stat options from ACF fields to `mylisting_maps`
	 * field in `wp_options`. Cleanup old ACF fields in the process.
	 *
	 * @since 2.3.4
     */
    public function maybe_migrate_options() {
    	if ( get_option( 'mylisting_maps', null ) !== null ) {
    		return;
    	}

    	// mlog()->note( 'Migrating map service settings' );
    	$config = $this->get_config();
    	$cleanup = [];

    	$provider = get_option( 'options_general_maps_platform', null );
    	if ( ! empty( $provider ) ) {
    		$config['provider'] = $provider;
    	}

    	// Google Maps
    	$config['gmaps_api_key'] = get_option( 'options_general_google_maps_api_key', '' );
    	$config['gmaps_lang'] = get_option( 'options_general_google_maps_language', 'default' );
    	$config['gmaps_types'] = get_option( 'options_general_autocomplete_types', 'geocode' );
    	$config['gmaps_locations'] = (array) get_option( 'options_general_autocomplete_locations', [] );

    	// Google Maps skins
    	$i = 0;
    	$config['gmaps_skins'] = [];
    	while (
    		( $skinkey = get_option( 'options_general_google_maps_custom_skins_'.$i.'_name' ) ) &&
    		( $skinval = get_option( 'options_general_google_maps_custom_skins_'.$i.'_json' ) )
    	) {
    		if ( $skinval = json_decode( $skinval ) ) {
    			$config['gmaps_skins'][ $skinkey ] = json_encode( $skinval );
    		}

    		$cleanup[] = 'options_general_google_maps_custom_skins_'.$i.'_name';
    		$cleanup[] = 'options_general_google_maps_custom_skins_'.$i.'_json';
    		$i++;
    	}

    	// Mapbox
    	$config['mapbox_api_key'] = get_option( 'options_general_mapbox_api_key', '' );
    	$config['mapbox_lang'] = get_option( 'options_general_mapbox_language', 'default' );
    	$config['mapbox_types'] = (array) get_option( 'options_general_mapbox_autocomplete_types', [] );
    	$config['mapbox_locations'] = (array) get_option( 'options_general_mapbox_autocomplete_locations', [] );

    	// Mapbox skins
    	$i = 0;
    	$config['mapbox_skins'] = [];
    	while (
    		( $skinkey = get_option( 'options_general_mapbox_custom_skins_'.$i.'_name' ) ) &&
    		( $skinval = get_option( 'options_general_mapbox_custom_skins_'.$i.'_url' ) )
    	) {
    		if ( $jsonskin = json_decode( $skinval ) ) {
    			$config['mapbox_skins'][ $skinkey ] = json_encode( $jsonskin );
    		} else {
    			$config['mapbox_skins'][ $skinkey ] = $skinval;
    		}

    		$cleanup[] = 'options_general_mapbox_custom_skins_'.$i.'_name';
    		$cleanup[] = 'options_general_mapbox_custom_skins_'.$i.'_url';
    		$i++;
    	}

    	// save config
    	$this->set_config( $config );

    	// cleanup old option keys
    	$cleanup[] = 'options_general_maps_platform';
    	$cleanup[] = 'general_google_maps_api_key';
    	$cleanup[] = 'options_general_google_maps_api_key';
    	$cleanup[] = 'options_general_google_maps_language';
    	$cleanup[] = 'options_general_autocomplete_types';
    	$cleanup[] = 'options_general_autocomplete_locations';
    	$cleanup[] = 'options_general_mapbox_api_key';
    	$cleanup[] = 'options_general_mapbox_language';
    	$cleanup[] = 'options_general_mapbox_autocomplete_types';
    	$cleanup[] = 'options_general_mapbox_autocomplete_locations';
    	$cleanup[] = 'options_general_mapbox_custom_skins';
    	$cleanup[] = 'options_general_google_maps_custom_skins';

    	foreach ( $cleanup as $option_name ) {
    		delete_option( $option_name );
    		delete_option( '_'.$option_name );
    	}
    }

    public static function get_gmaps_lang_choices() {
    	return [
			'default' => 'Default (Browser Detected)',
			'ar' => 'Arabic',
			'be' => 'Belarusian',
			'bg' => 'Bulgarian',
			'bn' => 'Bengali',
			'ca' => 'Catalan',
			'cs' => 'Czech',
			'da' => 'Danish',
			'de' => 'German',
			'el' => 'Greek',
			'en' => 'English',
			'en-Au' => 'English (Australian)',
			'en-GB' => 'English (Great Britain)',
			'es' => 'Spanish',
			'eu' => 'Basque',
			'fa' => 'Farsi',
			'fi' => 'Finnish',
			'fil' => 'Filipino',
			'fr' => 'French',
			'gl' => 'Galician',
			'gu' => 'Gujarati',
			'hi' => 'Hindi',
			'hr' => 'Croatian',
			'hu' => 'Hungarian',
			'id' => 'Indonesian',
			'it' => 'Italian',
			'iw' => 'Hebrew',
			'ja' => 'Japanese',
			'kk' => 'Kazakh',
			'kn' => 'Kannada',
			'ko' => 'Korean',
			'ky' => 'Kyrgyz',
			'lt' => 'Lithuanian',
			'lv' => 'Latvian',
			'mk' => 'Macedonian',
			'ml' => 'Malayalam',
			'mr' => 'Marathi',
			'my' => 'Burmese',
			'nl' => 'Dutch',
			'no' => 'Norwegian',
			'pa' => 'Punjabi',
			'pl' => 'Polish',
			'pt' => 'Portuguese',
			'pt-BR' => 'Portuguese (Brazil)',
			'pt-PT' => 'Portuguese (Portugal)',
			'ro' => 'Romanian',
			'ru' => 'Russian',
			'sk' => 'Slovak',
			'sl' => 'Slovenian',
			'sq' => 'Albanian',
			'sr' => 'Serbian',
			'sv' => 'Swedish',
			'ta' => 'Tamil',
			'te' => 'Telugu',
			'th' => 'Thai',
			'tl' => 'Tagalog',
			'tr' => 'Turkish',
			'uk' => 'Ukrainian',
			'uz' => 'Uzbek',
			'vi' => 'Vietnamese',
			'zh-CN' => 'Chinese (Simlified)',
			'zh-TW' => 'Chinese (Traditional)',
    	];
    }
}