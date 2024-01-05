<?php

namespace MyListing\Apis\Wp_All_Import;

if ( ! defined('ABSPATH') ) {
	exit;
}

function import_recurring_dates( $field, $field_value, $log ) {
	$dates = [];
	$date_format = $field->get_prop( 'enable_timepicker' ) ? 'Y-m-d H:i:s' : 'Y-m-d';

	foreach ( (array) $field_value as $date ) {
		$start = strtotime( $date['start'] ?? null );
		$end = strtotime( $date['end'] ?? null );
		$repetition = $date['frequency'] ?? '';
		$until = strtotime( $date['until'] ?? null );
		$frequency = preg_replace( '/[^0-9]/', '', $repetition );

		if ( \MyListing\str_contains( $repetition, 'day' ) ) {
			$unit = 'days';
		} elseif ( \MyListing\str_contains( $repetition, 'week' ) ) {
			$unit = 'weeks';
		} elseif ( \MyListing\str_contains( $repetition, 'month' ) ) {
			$unit = 'months';
		} elseif ( \MyListing\str_contains( $repetition, 'year' ) ) {
			$unit = 'years';
		} else {
			$unit = null;
		}

		if ( ! ( $start && $end ) || ( ! empty( $date['until'] ) && ! $until ) ) {
			$log( sprintf(
				'<strong>WARNING:</strong> Invalid date supplied for "%s": (%s, %s, %s), skipping.',
				$field->get_label(),
				$date['start'] ?? '',
				$date['end'] ?? '',
				$date['until'] ?? ''
			) );
			continue;
		}

		$dates[] = [
			'start' => date( $date_format, $start ),
			'end' => date( $date_format, $end ),
			'repeat' => is_numeric( $frequency ) && ! is_null( $unit ),
			'frequency' => $frequency,
			'unit' => $unit,
			'until' => date( 'Y-m-d', $until ),
		];
	}

	\MyListing\Src\Recurring_Dates\update_field( $field, $dates );
}
