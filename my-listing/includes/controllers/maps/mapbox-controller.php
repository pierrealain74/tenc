<?php

namespace MyListing\Controllers\Maps;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Mapbox_Controller extends \MyListing\Controllers\Base_Controller {

	protected function is_active() {
		return mylisting()->get( 'maps.provider', 'google-maps' ) === 'mapbox';
	}

	protected function hooks() {
		$this->on( 'wp_enqueue_scripts', '@enqueue_scripts', 25 );
        $this->on( 'admin_enqueue_scripts', '@enqueue_scripts', 25 );
        $this->filter( 'mylisting/localize-data', '@localize_data', 25 );
        $this->filter( 'mylisting/helpers/get_map_skins', '@set_skins', 25 );
	}

	protected function enqueue_scripts() {
		wp_enqueue_script(
			'mapbox-gl',
			'https://api.tiles.mapbox.com/mapbox-gl-js/v1.9.0/mapbox-gl.js',
			[], \MyListing\get_assets_version(), true
		);

		wp_enqueue_style(
			'mapbox-gl',
			'https://api.tiles.mapbox.com/mapbox-gl-js/v1.9.0/mapbox-gl.css',
			[], \MyListing\get_assets_version()
		);

		wp_enqueue_script( 'mylisting-mapbox' );
		wp_enqueue_style( 'mylisting-mapbox' );
	}

	protected function set_skins() {
		$skins = [];
		$custom_skins = [];

		// default skin should be the first option
		$skins['skin12'] = _x( 'Standard', 'Mapbox Skin', 'my-listing' );

		// followed by custom ones (if available).
		$custom_skins = mylisting()->get( 'maps.mapbox_skins', [] );
		foreach ( (array) $custom_skins as $skin_name => $skin ) {
			if ( empty( $skin ) ) {
				continue;
			}

			$skin_key = esc_attr( sprintf( 'custom_%s', $skin_name ) );
			$skins[ $skin_key ] = esc_html( $skin_name );
			$custom_skins[ $skin_key ] = $skin;
		}

		// Append other MyListing skins.
		$skins['skin3'] = _x( 'Light', 'Mapbox Skin', 'my-listing' );
		$skins['skin4'] = _x( 'Dark', 'Mapbox Skin', 'my-listing' );
		$skins['skin2'] = _x( 'Outdoors', 'Mapbox Skin', 'my-listing' );
		$skins['skin6'] = _x( 'Satellite', 'Mapbox Skin', 'my-listing' );
		$skins['skin7'] = _x( 'Nav Day', 'Mapbox Skin', 'my-listing' );
		$skins['skin8'] = _x( 'Nav Night', 'Mapbox Skin', 'my-listing' );
		$skins['skin9'] = _x( 'Guide Day', 'Mapbox Skin', 'my-listing' );
		$skins['skin10'] = _x( 'Guide Night', 'Mapbox Skin', 'my-listing' );
		return $skins;
	}

	protected function localize_data( $data ) {
		$language = mylisting()->get( 'maps.mapbox_lang', 'default' );
		// if set to default, try to retrieve the browser language via js and use that
		if ( $language === 'default' ) {
			$language = false;
		}

		$custom_skins = mylisting()->get( 'maps.mapbox_skins', [] );
		foreach ( (array) $custom_skins as $skin_name => $skin ) {
			if ( empty( $skin ) ) {
				continue;
			}

			$skin_key = esc_attr( sprintf( 'custom_%s', $skin_name ) );
			$custom_skins[ $skin_key ] = $skin;
		}

		$data['MapConfig']['AccessToken'] = mylisting()->get( 'maps.mapbox_api_key' );
		$data['MapConfig']['Language'] = $language;
		$data['MapConfig']['TypeRestrictions'] = mylisting()->get( 'maps.mapbox_types', 'geocode' );
		$data['MapConfig']['CountryRestrictions'] = mylisting()->get( 'maps.mapbox_locations', [] );
		$data['MapConfig']['CustomSkins'] = (object) $custom_skins;
		return $data;
	}
}
