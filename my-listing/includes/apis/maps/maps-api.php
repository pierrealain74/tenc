<?php

namespace MyListing\Apis\Maps;

if ( ! defined('ABSPATH') ) {
	exit;
}

function save_location_data( $listing_id, $location ) {
	delete_post_meta( $listing_id, 'geolocation_lat' );
	delete_post_meta( $listing_id, 'geolocation_long' );
	delete_post_meta( $listing_id, 'geolocation_formatted_address' );
	delete_post_meta( $listing_id, 'geolocation_meta' );

	$geocoder = \MyListing\Src\Geocoder\Geocoder::get();
	if ( is_null( $geocoder ) ) {
		return;
	}

	try {
		$feature = $geocoder->geocode( $location );
		update_post_meta( $listing_id, 'geolocation_lat', $feature['latitude'] );
		update_post_meta( $listing_id, 'geolocation_long', $feature['longitude'] );
		update_post_meta( $listing_id, 'geolocation_formatted_address', $feature['address'] );
		update_post_meta( $listing_id, 'geolocation_meta', $feature );
	} catch ( \Exception $e ) {
		// $e->getMessage();
	}
}

function get_skins() {
	return (array) apply_filters( 'mylisting/helpers/get_map_skins', [] );
}
