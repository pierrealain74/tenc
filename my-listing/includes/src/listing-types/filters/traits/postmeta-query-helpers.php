<?php
/**
 * @since 2.4
 */

namespace MyListing\Src\Listing_Types\Filters\Traits;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

trait Postmeta_Query_Helpers {

	public function postmeta_get_choices() {
		if ( isset( $this->cache['postmeta_choices'] ) ) {
			return $this->cache['postmeta_choices'];
		}

		global $wpdb;

		$orderby = $this->get_prop('order_by');
		$count = $this->get_prop('count');
		$order = $this->get_prop('order') === 'ASC' ? 'ASC' : 'DESC';

		$field = $this->listing_type->get_field( $this->get_prop('show_field') );
		if ( ! $field ) {
			return [];
		}

		// for 'include', we just get the list of options from the field settings, no query is needed
		if ( $orderby === 'include' ) {
			$list = [];
			$options = (array) $field->get_prop('options');

		    if ( $order === 'DESC' ) {
		        $options = array_reverse( $options );
		    }

	        if ( is_numeric( $count ) && $count >= 1 ) {
	            $options = array_slice( (array) $options, 0, $count );
	        }

		    foreach ( $options as $value => $label ) {
		        $list[] = [
		            'value' => urlencode( $value ),
		            'label' => $label,
		            'selected' => false,
		        ];
		    }

			$this->cache['postmeta_choices'] = $list;
			return $this->cache['postmeta_choices'];
		}

		// retrieve values from wp_postmeta
		if ( $orderby === 'count' ) {
			$order_clause = "COUNT({$wpdb->postmeta}.meta_value)";
		} elseif ( $orderby === 'meta_value' ) {
			$order_clause = "{$wpdb->postmeta}.meta_value";
		} elseif ( $orderby === 'meta_value_num' ) {
			$order_clause = "{$wpdb->postmeta}.meta_value +0";
		} else {
			// by default, order by name
			$order_clause = "{$wpdb->posts}.post_name";
		}

		$results = $wpdb->get_col( $wpdb->prepare( "
			SELECT {$wpdb->postmeta}.meta_value
			FROM {$wpdb->posts}
			INNER JOIN {$wpdb->postmeta} ON ( {$wpdb->posts}.ID = {$wpdb->postmeta}.post_id )
			INNER JOIN {$wpdb->postmeta} AS mt1 ON ( {$wpdb->posts}.ID = mt1.post_id )
			WHERE {$wpdb->postmeta}.meta_key = %s
				AND {$wpdb->postmeta}.meta_value != ''
			    AND {$wpdb->posts}.post_type = 'job_listing'
			    AND {$wpdb->posts}.post_status = 'publish'
				AND mt1.meta_key = '_case27_listing_type'
				AND mt1.meta_value = %s
			GROUP BY {$wpdb->postmeta}.meta_value
			ORDER BY {$order_clause} {$order}
		", '_'.$this->get_prop( 'show_field' ), $this->listing_type->get_slug() ) );

		$list = [];
		$options = $field->get_prop('options');
		foreach ( (array) $results as $value ) {
	        if ( is_serialized( $value ) ) {
	            foreach ( array_filter( (array) unserialize( $value ) ) as $subvalue ) {
	            	if ( ( is_array( $options ) && ! isset( $options[ $subvalue ] ) ) || isset( $list[ $subvalue ] ) ) {
	            		continue;
	            	}

	                $list[ $subvalue ] = [
	                    'value' => urlencode( $subvalue ),
	                    'label' => is_array( $options ) ? $options[ $subvalue ] : $subvalue,
	                    'selected' => false,
	                ];
	            }
	        } else {
            	if ( ( is_array( $options ) && ! isset( $options[ $value ] ) ) || isset( $list[ $value ] )  ) {
            		continue;
            	}

	        	$list[ $value ] = [
	            	'value' => urlencode( $value ),
	            	'label' => is_array( $options ) ? $options[ $value ] : $value,
	            	'selected' => false,
	        	];
	        }
		}

		$this->cache['postmeta_choices'] = array_values( $list );
		return $this->cache['postmeta_choices'];
	}
}
