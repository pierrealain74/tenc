<?php

namespace MyListing\Src\Listing_Types\Filters;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Related_Listing extends Base_Filter {

	public function filter_props() {
		$this->props['type'] = 'related-listing';
		$this->props['label'] = 'Related Listing';
		$this->props['show_field'] = 'related_listing';
		$this->props['multiselect'] = false;

		// set allowed fields
		$this->allowed_fields = ['related-listing'];
	}

	public function apply_to_query( $args, $form_data ) {
		global $wpdb;

		$field_key = $this->get_prop( 'show_field' );
		$field = $this->listing_type->get_field( $field_key );
		$relation_type = $field->get_prop('relation_type');

		if ( empty( $form_data[ $this->get_form_key() ] ) || ! $field ) {
			return $args;
		}

		$values = array_filter( array_map( 'absint',
			(array) $form_data[ $this->get_form_key() ]
		) );

		if ( empty( $values ) ) {
			return $args;
		}

		$imploded_ids = implode( ',', $values );
		if ( in_array( $relation_type, [ 'has_one', 'has_many' ], true ) ) {
			$rows = $wpdb->get_col( $wpdb->prepare( "
				SELECT parent_listing_id FROM {$wpdb->prefix}mylisting_relations
				WHERE child_listing_id IN ({$imploded_ids}) AND field_key = %s
				ORDER BY ID ASC
			", $field_key ) );
		}

		if ( in_array( $relation_type, [ 'belongs_to_one', 'belongs_to_many' ], true ) ) {
			$rows = $wpdb->get_col( $wpdb->prepare( "
				SELECT child_listing_id FROM {$wpdb->prefix}mylisting_relations
				WHERE parent_listing_id IN ({$imploded_ids}) AND field_key = %s
				ORDER BY ID ASC
			", $field_key ) );
		}

		$ids = array_map( 'absint', (array) $rows );
		if ( empty( $ids ) ) {
			$ids = [0];
		}

		/**
		 * If the `post__in` parameter has already been set, we must make sure to only include
		 * listings that are both in the original `post__in` and in our new custom list, so the
		 * filters don't conflict with each other and behave as expected.
		 *
		 * If `array_intersect` returns zero matches, then no search results should be returned,
		 * so we set `post__in` to `[0]`.
		 */
		if ( ! empty( $args['post__in'] ) ) {
			$ids = array_intersect( $args['post__in'], $ids );
			if ( empty( $ids ) ) {
				$ids = [0];
			}
		}

		$args['post__in'] = $ids;
		return $args;
	}

	public function get_request_value() {
		$ids = ! empty( $_GET[ $this->get_form_key() ] )
			? explode( ',', $_GET[ $this->get_form_key() ] )
			: [];

		return join( ',', array_map( 'absint', $ids ) );
	}

	public function get_ajax_params() {
		$field = $this->listing_type->get_field( $this->get_prop('show_field') );
		if ( ! $field ) {
			return [];
		}

		return [ 'listing-type' => (array) $field->get_prop('listing_type') ];
	}

	public function get_preselected_terms() {
		$imploded_ids = $this->get_request_value();
		$ids = explode( ',', $imploded_ids );

		if ( ! empty( $ids ) && $imploded_ids ) {
			global $wpdb;
			$listings = $wpdb->get_results( "
				SELECT ID AS value, post_title AS label FROM {$wpdb->posts}
				WHERE post_type = 'job_listing'
					AND post_status = 'publish'
					AND ID IN ({$imploded_ids})
				ORDER BY FIELD(ID,{$imploded_ids}) LIMIT 50
			", ARRAY_A );
		} else {
			$listings = [];
		}

		// if it's a single select use only the first value in the array
		if ( ! empty( $listings ) && ! $this->get_prop('multiselect') ) {
			$listings = [ array_shift( $listings ) ];
		}

		return $listings;
	}
}