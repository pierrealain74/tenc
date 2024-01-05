<?php

namespace MyListing\Src\Listing_Types\Filters;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Location extends Base_Filter {

	protected $form_key = 'search_location';

	public function filter_props() {
		$this->props['type'] = 'location';
		$this->props['label'] = 'Location';
	}

	public function apply_to_query( $args, $form_data ) {
		if ( ! empty( $form_data[ $this->get_form_key() ] ) ) {
			$args['search_location'] = sanitize_text_field( stripslashes(
				$form_data[ $this->get_form_key() ]
			) );
		}

		return $args;
	}

	public function get_request_value() {
		$lat = ! empty( $_GET['lat'] ) ? floatval( $_GET['lat'] ) : false;
		$lng = ! empty( $_GET['lng'] ) ? floatval( $_GET['lng'] ) : false;

		return [
			$this->get_form_key() => ! empty( $_GET[ $this->get_form_key() ] )
				? sanitize_text_field( stripslashes( $_GET[ $this->get_form_key() ] ) )
				: '',
			'lat' => ! ( $lat > 90 || $lat < -90 ) ? $lat : false,
			'lng' => ! ( $lng > 180 || $lng < -180 ) ? $lng : false,
		];
	}
}