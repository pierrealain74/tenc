<?php

namespace MyListing\Src\Listing_Types\Filters;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Recurring_Date extends Base_Filter {

	public function filter_props() {
		$this->props['type'] = 'recurring-date';
		$this->props['label'] = 'Recurring Date';
		$this->props['show_field'] = 'event_date';
		$this->props['datepicker'] = true;
		$this->props['timepicker'] = false;
		$this->props['ranges'] = [
			[ 'key' => 'all', 'label' => 'Any day' ],
			[ 'key' => 'today', 'label' => 'Today' ],
			[ 'key' => 'this-week', 'label' => 'This week' ],
			[ 'key' => 'this-weekend', 'label' => 'This weekend' ],
			[ 'key' => 'next-week', 'label' => 'Next week' ],
		];

		// set allowed fields
		$this->allowed_fields = ['recurring-date'];
	}

	public function apply_to_query( $args, $form_data ) {
		$default_value = ! empty( $this->get_prop('ranges') )
			? $this->get_prop('ranges')[0]['key']
			: date('Y-m-d H:i:s', current_time('timestamp')).'..';

		// if no value is provided, the default preset range should be used,
		// or if no presets are available, then only show future events
		if ( empty( $form_data[ $this->get_form_key() ] ) ) {
			$form_data[ $this->get_form_key() ] = $default_value;
		}

		$field_key = $this->get_prop( 'show_field' );
		$values = \MyListing\Src\Recurring_Dates\parse_value(
			$form_data[ $this->get_form_key() ],
			$this->get_prop('timepicker')
		);

		if ( ! ( is_array( $values ) && isset( $values['start'], $values['end'] ) ) ) {
			return $args;
		}

		// start date is required
		if ( empty( $values['start'] ) ) {
			return $args;
		}

		if ( ! isset( $args['recurring_dates'][ $field_key ] ) ) {
			$args['recurring_dates'][ $field_key ] = [];
		}

		$args['recurring_dates'][ $field_key ]['start'] = $values['start'];
		$args['recurring_dates'][ $field_key ]['end'] = $values['end'];
		$args['recurring_dates'][ $field_key ]['where_clause'] = true;

		$reference_filter = sprintf( '_mylisting/recurring-dates/%s:reference', $field_key );
		add_filter( $reference_filter, function() use ( $values ) {
			return $values['start'];
		} );

		return $args;
	}
}
