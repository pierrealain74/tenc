<?php

namespace MyListing\Src\Listing_Types\Filters;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Text extends Base_Filter {

	public function filter_props() {
		$this->props['type'] = 'text';
		$this->props['label'] = 'Text Search';
		$this->props['show_field'] = '';

		// set allowed fields
		$this->allowed_fields = [
			'text', 'texteditor', 'wp-editor', 'checkbox', 'radio',
			'select', 'multiselect', 'textarea', 'date', 'email',
			'url', 'number',
		];
	}

	public function apply_to_query( $args, $form_data ) {
		if ( empty( $form_data[ $this->get_form_key() ] ) ) {
			return $args;
		}

		$search_value = sanitize_text_field( stripslashes(
			$form_data[ $this->get_form_key() ]
		) );

		if ( $this->get_prop('show_field') === 'job_title' ) {
			$args['title_search'] = $search_value;
		} elseif ( $this->get_prop('show_field') === 'job_description' ) {
			$args['description_search'] = $search_value;
		} else {
			$args['meta_query'][] = [
				'compare' => 'LIKE',
				'key'     => '_'.$this->get_prop( 'show_field' ),
				'value'   => $search_value,
			];
		}

		return $args;
	}
}
