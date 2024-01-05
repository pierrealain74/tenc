<?php

namespace MyListing\Src\Geocoder;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Google_Maps_Geocoder extends Geocoder {

	protected function client_geocode( $location ) {
		$language = mylisting()->get( 'maps.gmaps_lang', 'default' );
		$params = [
			'key' => mylisting()->get( 'maps.gmaps_api_key' ),
			'language' => $language !== 'default' ? $language : 'en',
		];

		if ( is_array( $location ) ) {
			$params['latlng'] = join( ',', array_map( 'floatval', $location ) );
		} else {
			$params['address'] = (string) $location;
		}

		$request = wp_remote_get( sprintf( 'https://maps.googleapis.com/maps/api/geocode/json?%s', http_build_query( $params ) ), [
			'httpversion' => '1.1',
			'sslverify' => false,
		] );

		if ( is_wp_error( $request ) ) {
			throw new \Exception( 'Could not perform geocoding request.' );
		}

		$response = json_decode( wp_remote_retrieve_body( $request ) );
		if ( ! is_object( $response ) || $response->status !== 'OK' || empty( $response->results ) ) {
			throw new \Exception( sprintf(
				'(%s) %s',
				$response->status ?? 'REQUEST_FAILED',
				$response->error_message ?? 'Geocoding request failed.'
			) );
		}

		return $response->results[0];
	}

	protected function transform_response( $response ) {
		$feature = [
			'latitude'  => $response->geometry->location->lat,
			'longitude' => $response->geometry->location->lng,
			'address'   => $response->formatted_address,
			'provider'  => 'google-maps',
			'meta'      => [],
		];

		if ( ! empty( $response->address_components ) ) {
			foreach ( $response->address_components as $component ) {
				if ( empty( $component->types ) ) {
					continue;
				}

				foreach ( $component->types as $component_type ) {
					$feature['meta'][ $component_type ] = $component->long_name;
				}
			}
		}

		return $feature;
	}
}
