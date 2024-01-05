<?php

namespace MyListing\Src\Listing_Types\Filters;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Date extends Base_Filter {

	public function filter_props() {
		$this->props['type'] = 'date';
		$this->props['label'] = 'Date';
		$this->props['show_field'] = '';
		$this->props['option_type'] = 'exact';
		$this->props['format'] = 'ymd';

		// set allowed fields
		$this->allowed_fields = ['date'];
	}

	public function apply_to_query( $args, $form_data ) {
		$field_key = $this->get_prop( 'show_field' );
		$date_type = $this->get_prop('option_type');
		$format = $this->get_prop('format');

		if ( empty( $form_data[ $this->get_form_key() ] ) ) {
			return $args;
		}

		$values = explode( '..', $form_data[ $this->get_form_key() ] );
		$prepend = $format === 'year' ? '01-01-' : '';
		$start_stamp = ! empty( $values[0] ) ? strtotime( $prepend . $values[0] ) : false;
		$end_stamp = ! empty( $values[1] ) ? strtotime( $prepend . $values[1] ) : false;

		// single date search
		if ( $date_type === 'exact' && $start_stamp ) {
			// date is in Y-m-d format (2.g. 2020-01-23)
			if ( $format === 'ymd' ) {
				$args['meta_query'][] = [
					'key'     => '_'.$field_key,
					'value'   => date( 'Y-m-d', $start_stamp ),
					'compare' => '=',
					'type' => 'DATE',
				];
			}

			// date is in year format (e.g. 2020)
			if ( $format === 'year' ) {
				$args['meta_query'][] = [
					'key'     => '_'.$field_key,
					'value'   => [
						date( 'Y-01-01', $start_stamp ),
						date( 'Y-12-31', $start_stamp ),
					],
					'compare' => 'BETWEEN',
					'type' => 'DATE',
				];
			}
		}

		// date range search
		if ( $date_type === 'range' ) {
			// start date is provided but end date is not
			if ( $start_stamp && ! $end_stamp ) {
				$range_start = ( $format === 'ymd' )
					? date( 'Y-m-d', $start_stamp )
					: date( 'Y-01-01', $start_stamp );

				$args['meta_query'][] = [
					'key'     => '_'.$field_key,
					'value'   => $range_start,
					'compare' => '>=',
					'type' => 'DATE',
				];
			}

			// end date is provided but start date is not
			if ( $end_stamp && ! $start_stamp ) {
				$range_end = ( $format === 'ymd' )
					? date( 'Y-m-d', $end_stamp )
					: date( 'Y-12-31', $end_stamp );

				$args['meta_query'][] = [
					'key'     => '_'.$field_key,
					'value'   => $range_end,
					'compare' => '<=',
					'type' => 'DATE',
				];
			}

			// both start and end date have been provided
			if ( $start_stamp && $end_stamp ) {
				$range_start = ( $format === 'ymd' )
					? date( 'Y-m-d', $start_stamp )
					: date( 'Y-01-01', $start_stamp );

				$range_end = ( $format === 'ymd' )
					? date( 'Y-m-d', $end_stamp )
					: date( 'Y-12-31', $end_stamp );

				$args['meta_query'][] = [
					'key'     => '_'.$field_key,
					'value'   => [ $range_start, $range_end ],
					'compare' => 'BETWEEN',
					'type' => 'DATE',
				];
			}
		}

		return $args;
	}

	public function get_request_value() {
		if ( empty( $_GET[ $this->get_form_key() ] ) ) {
			return '';
		}

		$values = explode( '..', $_GET[ $this->get_form_key() ] );
		$prepend = $this->get_prop( 'format' ) === 'year' ? '01-01-' : '';
		$start_stamp = ! empty( $values[0] ) ? strtotime( $prepend . $values[0] ) : false;
		$end_stamp = ! empty( $values[1] ) ? strtotime( $prepend . $values[1] ) : false;
		$date_format = $this->get_prop( 'format' ) === 'ymd' ? 'Y-m-d' : 'Y';

		if ( $this->get_prop( 'option_type' ) === 'exact' && $start_stamp ) {
			return date( $date_format, $start_stamp );
		}

		if ( $this->get_prop( 'option_type' ) === 'range' && ( $start_stamp || $end_stamp ) ) {
			return sprintf( '%s..%s',
				$start_stamp ? date( $date_format, $start_stamp ) : '',
				$end_stamp ? date( $date_format, $end_stamp ) : ''
			);
		}

		return '';
	}

	public function get_postmeta_choices() {
		if ( isset( $this->cache['postmeta_choices'] ) ) {
			return $this->cache['postmeta_choices'];
		}

		global $wpdb;

		$results = $wpdb->get_col( $wpdb->prepare( "
			SELECT YEAR({$wpdb->postmeta}.meta_value) as item_year
			FROM {$wpdb->posts}
			INNER JOIN {$wpdb->postmeta} ON ( {$wpdb->posts}.ID = {$wpdb->postmeta}.post_id )
			WHERE {$wpdb->postmeta}.meta_key = %s
				AND {$wpdb->postmeta}.meta_value != ''
			    AND {$wpdb->posts}.post_type = 'job_listing'
			    AND {$wpdb->posts}.post_status = 'publish'
			GROUP BY item_year
			ORDER BY item_year DESC
		", '_'.$this->get_prop( 'show_field' ) ) );

		$this->cache['postmeta_choices'] = (array) $results;
		return $this->cache['postmeta_choices'];
	}
}
