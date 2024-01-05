<?php

namespace MyListing\Src\Listing_Types\Filters;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Order extends Base_Filter {

	protected $form_key = 'sort';

	public function filter_props() {
		$this->props['type'] = 'order';
		$this->props['label'] = 'Order by';
	}

	public function apply_to_query( $args, $form_data ) {
		// currently handled by explore-listings.php
		return $args;
	}

	public function get_choices() {
		return array_map( function( $a ) {
			return [
				'key' => $a['key'],
				'label' => $a['label'],
				'notes' => isset( $a['notes'] ) ? (array) $a['notes'] : [],
			];
		}, $this->listing_type->get_ordering_options() );
	}

	public function get_request_value() {
		$value = ! empty( $_GET[ $this->get_form_key() ] ) ? $_GET[ $this->get_form_key() ] : '';
		$options = $this->listing_type->get_ordering_options();

		// use first option if no valid value is provided via url
		if ( ! in_array( $value, array_column( $options, 'key' ) ) && isset( $options[0] ) && ! empty( $options[0]['key'] ) ) {
		    $value = $options[0]['key'];
		}

		return $value;
	}
}