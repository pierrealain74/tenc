<?php

namespace MyListing\Src\Geocoder;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Mapbox_Geocoder extends Geocoder {

	protected function client_geocode( $location ) {
		$url = 'https://api.mapbox.com/geocoding/v5/mapbox.places/%s.json?%s';
		$language = mylisting()->get( 'maps.mapbox_lang', 'default' );
		if ( empty( $language ) || $language === 'default' ) {
			$language = 'en';
		}

		$params = [
			'access_token' => mylisting()->get( 'maps.mapbox_api_key' ),
			'language' => $language,
			'limit' => 1,
		];

		if ( is_array( $location ) ) {
			$location = join( ',', array_reverse( array_map( 'floatval', $location ) ) );
		} else {
			$location = urlencode( $location );
		}

		$request = wp_remote_get( sprintf( $url, $location, http_build_query( $params ) ), [
			'httpversion' => '1.1',
			'sslverify' => false,
		] );

		if ( is_wp_error( $request ) ) {
			throw new \Exception( 'Could not perform geocoding request.' );
		}

		$response = json_decode( wp_remote_retrieve_body( $request ) );
		if ( ! is_object( $response ) || empty( $response->features ) ) {
			throw new \Exception( $response->message ?? 'Geocoding request failed.' );
		}

		return $response->features[0];
	}

	protected function transform_response( $response ) {
		$feature = [
			'latitude'  => $response->geometry->coordinates[1],
			'longitude' => $response->geometry->coordinates[0],
			'address'   => $response->place_name,
			'provider'  => 'mapbox',
			'meta'      => [],
		];

		return $feature;
	}
}
