<?php

namespace MyListing\Src\Geocoder;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

abstract class Geocoder {

	private static $instance;

	public static function get() {
		if ( ! is_null( static::$instance ) ) {
			return static::$instance;
		}

		if ( mylisting()->get( 'maps.provider', 'google-maps' ) === 'google-maps' ) {
			static::$instance = new Google_Maps_Geocoder;
		}

		if ( mylisting()->get( 'maps.provider', 'google-maps' ) === 'mapbox' ) {
			static::$instance = new Mapbox_Geocoder;
		}

		return static::$instance;
	}

	/**
	 * Must be overridden by the geocoding service provider to
	 * perform the API request.
	 *
	 * @since 1.0
	 */
	abstract protected function client_geocode( $location );

	/**
	 * Must be overridden by the geocoding service provider to normalize
	 * the API response, returning an array with the following keys: latitude,
	 * longitude, address, provider, and meta.
	 *
	 * @since 1.0
	 */
	abstract protected function transform_response( $response );

	/**
	 * If $location is a string, regular address geocoding is tried;
	 * If $location is an array with two numeric values, reverse geocoding is tried.
	 *
	 * @since 1.0
	 */
	public function geocode( $location ) {
		if ( is_array( $location ) ) {
			$latitude = $location[0] ?? null;
			$longitude = $location[1] ?? null;
			if ( ! is_numeric( $latitude ) || ! is_numeric( $longitude ) ) {
				throw new \Exception( 'Invalid coordinates provided.' );
			}
		} elseif ( ! is_string( $location ) || empty( trim( $location ) ) ) {
			throw new \Exception( 'Invalid address provided.' );
		}

		$response = $this->client_geocode( $location );
		$address = $this->transform_response( $response );
		return $address;
	}

	public function get_provider_key() {
		return mylisting()->get( 'maps.provider', 'google-maps' );
	}

	public function get_provider_label() {
		$provider = $this->get_provider_key();
		if ( $provider === 'google-maps' ) {
			return 'Google Maps';
		} elseif ( $provider === 'google-maps' ) {
			return 'Google Maps';
		} else {
			return 'N/A';
		}
	}
}
