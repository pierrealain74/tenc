<?php

namespace MyListing\Src\Listing_Types\Filters;

if ( ! defined('ABSPATH') ) {
	exit;
}

class WP_Search extends Base_Filter {

	protected $form_key = 'search_keywords';

	public function filter_props() {
		$this->props['type'] = 'wp-search';
		$this->props['label'] = 'General Search Box';
	}

	public function apply_to_query( $args, $form_data ) {
		if ( ! empty( $form_data[ $this->get_form_key() ] ) ) {
			$args[ 'search_keywords' ] = sanitize_text_field( stripslashes(
				$form_data[ $this->get_form_key() ]
			) );
		}

		return $args;
	}
}