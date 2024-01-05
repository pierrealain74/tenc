<?php

namespace MyListing\Src\Listing_Types\Filters;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Dropdown extends Base_Filter {
	use Traits\Postmeta_Query_Helpers;

	public function filter_props() {
		$this->props['type'] = 'dropdown';
		$this->props['label'] = 'Dropdown';
		$this->props['show_field'] = '';
		$this->props['order_by'] = 'count';
		$this->props['order'] = 'DESC';
		$this->props['hide_empty'] = 1;
		$this->props['multiselect'] = 1;
		$this->props['behavior'] = 'any';

		// set allowed fields
		$this->allowed_fields = [
			'text', 'checkbox', 'radio', 'select',
			'multiselect', 'date', 'term-multiselect',
			'term-select', 'number', 'location',
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

		if ( $field->get_type() !== 'term-select' ) {
			$values = $this->postmeta_validate_selection( $selected, $field );
			$this->cache['request_value'] = join( ',', $values );
			return $this->cache['request_value'];
		}

		$values = $this->validate_selected_terms( $selected, $field );
		if ( ! $this->get_prop('multiselect') && count( $values ) > 1 ) {
			$values = [ array_pop( $values ) ];
		}

		$this->cache['request_value'] = join( ',', array_map( function( $term ) {
			return $term->slug;
		}, $values ) );
		return $this->cache['request_value'];
	}

	public function get_ajax_params() {
		$field = $this->listing_type->get_field( $this->get_prop('show_field') );
		if ( ! $field ) {
			return [];
		}

		$multiple = (bool) $this->get_prop( 'multiselect' );
		$params = [
		    'taxonomy' => $field['taxonomy'],
		    'listing-type-id' => $this->listing_type->get_id(),
		    'orderby' => $this->get_prop('order_by'),
		    'order' => $this->get_prop('order'),
		    'hide_empty' => $this->get_prop('hide_empty') ? 'yes' : '',
		    'term-value' => 'slug',
		];

		if ( ! $multiple ) {
		    $params['parent'] = 0;
		}

		return $params;
	}

	public function get_preselected_terms() {
		$field = $this->listing_type->get_field( $this->get_prop('show_field') );
		$values = explode( ',', $this->get_request_value() );
		if ( ! $field || empty( $values ) ) {
			return [];
		}

		if ( $field->get_type() !== 'term-select' ) {
			$values = $this->postmeta_validate_selection( $values, $field );
			return array_map( function( $value ) {
				return [ 'value' => $value, 'label' => $value ];
			}, $values );
		}

		$values = $this->validate_selected_terms( $values, $field );
		return array_map( function( $term ) {
			return [ 'value' => $term->slug, 'label' => $term->name ];
		}, $values );
	}

	private function validate_selected_terms( $selected, $field ) {
		if ( empty( $selected ) ) {
			return [];
		}

	    $selected_terms = [];
	    $_selected_terms = get_terms( [
			'taxonomy' => $field->get_prop('taxonomy'),
			'hide_empty' => false,
			'slug' => $selected,
	    ] );

	    if ( is_wp_error( $_selected_terms ) ) {
	        return [];
	    }

	    // validate selected terms
	    foreach ( $_selected_terms as $_term ) {
	        if ( ! $_term instanceof \WP_Term ) {
	            continue;
	        }

	        // ignore term if it doesn't belong to this listing type
	        $term_types = array_filter( array_map( 'absint',
	        	(array) get_term_meta( $_term->term_id, 'listing_type', true )
	        ) );

	        if ( ! empty( $term_types ) && ! in_array( $this->listing_type->get_id(), $term_types ) ) {
	            continue;
	        }

	        $selected_terms[] = $_term;
	    }

	    // for single select filters, we also need the ancestors of the selected term
	    // to construct the hierarchical list of dropdowns
		if ( ! $this->get_prop('multiselect') ) {
	        $selected_tree = [];

	        if ( ! empty( $selected_terms ) && $selected_terms[0] instanceof \WP_Term ) {
	            $term_list = array_reverse( get_ancestors(
	            	$selected_terms[0]->term_id,
	            	$field->get_prop('taxonomy'), 'taxonomy'
	            ) );

	            $term_list[] = $selected_terms[0]->term_id;
	            foreach ( $term_list as $term_id ) {
	                $term = get_term( $term_id );
	                $selected_tree[] = $term;
	            }
	        }

	        return $selected_tree;
	    }

	    return $selected_terms;
	}

	private function postmeta_validate_selection( $selected, $field ) {
		$choices = $this->postmeta_get_choices();
		$validated = [];

		foreach ( $choices as $choice ) {
			if ( in_array( $choice['value'], (array) $selected ) ) {
				$validated[] = $choice['value'];
			}
		}

		return array_unique( $validated );
	}
}
