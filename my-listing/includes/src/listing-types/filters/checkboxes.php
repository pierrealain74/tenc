<?php

namespace MyListing\Src\Listing_Types\Filters;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Checkboxes extends Base_Filter {
	use Traits\Postmeta_Query_Helpers;

	public function filter_props() {
		$this->props['type'] = 'checkboxes';
		$this->props['label'] = 'Checkboxes';
		$this->props['show_field'] = '';
		$this->props['form'] = 'advanced';
		$this->props['count'] = 8;
		$this->props['order_by'] = 'count';
		$this->props['order'] = 'DESC';
		$this->props['hide_empty'] = 1;
		$this->props['multiselect'] = 1;
		$this->props['behavior'] = 'any';

		// set allowed fields
		$this->allowed_fields = [
			'term-multiselect', 'term-select', 'text', 'select', 'date',
			'multiselect', 'checkbox', 'radio', 'number', 'location',
		];
	}

	public function apply_to_query( $args, $form_data ) {
		$field_key = $this->get_prop( 'show_field' );
		$facet_behavior = $this->get_prop( 'behavior' );
		$field = $this->listing_type->get_field( $field_key );

		if ( empty( $form_data[ $this->get_form_key() ] ) || ! $field ) {
			return $args;
		}

		$dropdown_values = array_map( 'urldecode', array_filter( explode( ',', esc_html(
			stripslashes( $form_data[ $this->get_form_key() ] )
		) ) ) );

		if ( empty( $dropdown_values ) ) {
			return $args;
		}

		if ( ! $this->get_prop('multiselect') ) {
			$dropdown_values = [ array_shift( $dropdown_values ) ];
		}

		// handle tax query
		if ( $field->get_type() === 'term-select' && taxonomy_exists( $field->get_prop('taxonomy') ) ) {
			$args['tax_query'][] = [
				'taxonomy' => $field->get_prop('taxonomy'),
				'field' => 'slug',
				'terms' => $dropdown_values,
				'operator' => $facet_behavior === 'all' ? 'AND' : 'IN',
				'include_children' => $facet_behavior !== 'all',
			];
		}
		// handle multiselect fields (stored in serialized format)
		elseif ( $field->get_type() === 'multiselect' || $field->get_type() === 'checkbox' ) {
			$subquery = [ 'relation' => $facet_behavior === 'all' ? 'AND' : 'OR' ];

			foreach ( $dropdown_values as $dropdown_value ) {
				$subquery[] = [
					'key'     => '_'.$field_key,
					'value'   => '"' . $dropdown_value . '"',
					'compare' => 'LIKE',
				];
			}

			$args['meta_query'][] = $subquery;
		}
		// other fields stored as plain text in wp_postmeta
		else {
			$args['meta_query'][] = [
				'key'     => '_'.$field_key,
				'value'   => $dropdown_values,
				'compare' => 'IN',
			];
		}

		return $args;
	}

	public function get_request_value() {
		if ( isset( $this->cache['request_value'] ) ) {
			return $this->cache['request_value'];
		}

		$field_key = $this->get_prop('show_field');
		$field = $this->listing_type->get_field( $field_key );
		$selected = [];

		if ( ! $field ) {
			$this->cache['request_value'] = '';
			return $this->cache['request_value'];
		}

		// remove "job_" prefix from category and tag fields when used in Explore page url
		if ( $field_key === 'job_category' ) { $field_key = 'category'; }
		if ( $field_key === 'job_tags' ) { $field_key = 'tag'; }

		if ( ! empty( $_GET[ $this->get_form_key() ] ) ) {
			$selected = array_filter( explode( ',', esc_html(
				$_GET[ $this->get_form_key() ]
			) ) );
		// in single term pages, the active term is passed as `explore_{taxonomy}`
		} elseif ( ( $selected_val = get_query_var( sprintf( 'explore_%s', $field_key ) ) ) ) {
		    $selected = (array) $selected_val;
		}

		$selected = $this->validate_selected_terms( $selected, $field );

		$this->cache['request_value'] = join( ',', $selected );
		return $this->cache['request_value'];
	}

	public function get_choices() {
		$field = $this->listing_type->get_field( $this->get_prop('show_field') );
		if ( ! $field ) {
			return [];
		}

		if ( $field->get_type() !== 'term-select' ) {
			return $this->postmeta_get_choices();
		}

		return $this->get_term_choices();
	}

	private function validate_selected_terms( $selected, $field ) {
		$choices = $this->get_choices();
		$validated = [];

		foreach ( $choices as $choice ) {
			if ( in_array( $choice['value'], (array) $selected ) ) {
				$validated[] = $choice['value'];
			}
		}

		return array_unique( $validated );
	}

	private function get_term_choices() {
		if ( isset( $this->cache['term_choices'] ) ) {
			return $this->cache['term_choices'];
		}

		$field = $this->listing_type->get_field( $this->get_prop('show_field') );
		if ( ! $field ) {
			$this->cache['term_choices'] = [];
			return $this->cache['term_choices'];
		}

		$orderby = $this->get_prop('order_by');
		$number = $this->get_prop('count');
		$hierarchical = $orderby === 'name' && absint( $number ) === 0;
		$terms = \MyListing\get_terms( [
	        'taxonomy' => $field->get_prop('taxonomy'),
	        'hide_empty' => $this->get_prop('hide_empty'),
	        'orderby' => $orderby,
	        'order' => $this->get_prop('order'),
	        'number' => $number,
	        'listing_type' => $this->listing_type->get_id(),
	        'return_key' => 'slug',
	        'return_value' => 'wp_term',
	        // order hierarchically only when all terms are set to be shown
	        'hierarchical' => $hierarchical,
	        'cache_time' => 24 * HOUR_IN_SECONDS,
		] );

	    $choices = [];
        foreach ( (array) $terms as $wp_term ) {
        	$depth = $wp_term->_depth;
            $term = \MyListing\Src\Term::get( $wp_term );
            $choices[] = [
                'value' => $term->get_slug(),
                'label' => $hierarchical
                	? str_repeat( '&mdash; ', $depth - 1 ) . $wp_term->name
                	: $term->get_full_name(),
                'selected' => false,
            ];
        }

		$this->cache['term_choices'] = $choices;
		return $this->cache['term_choices'];
	}
}
