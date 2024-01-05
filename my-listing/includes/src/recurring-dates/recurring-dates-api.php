<?php
/**
 * API functions for recurring dates feature.
 *
 * @since 2.4
 */

namespace MyListing\Src\Recurring_Dates;

if ( ! defined('ABSPATH') ) {
	exit;
}

/**
 * Get the SQL query used to join wp_posts to wp_mylisting_visits.
 *
 * @since 2.4
 */
function get_join_clause( $range_start, $range_end, $field_key ) {
	global $wpdb;

	$range_start = esc_sql( $range_start );
	$range_end = esc_sql( $range_end );
	$field_key = esc_sql( $field_key );

	return "
		LEFT JOIN( SELECT listing_id,
			MIN( CASE
             	WHEN (repeat_unit = 'NONE')
             		THEN IF(start_date >= '{$range_start}', start_date, NULL)

	            WHEN (start_date >= '{$range_start}')
	                THEN start_date

		     	WHEN (repeat_unit = 'DAY') THEN (
		     		IF(
			     		DATE_ADD( start_date, INTERVAL ( frequency * CEIL(
			     			( TIMESTAMPDIFF( DAY, start_date, '{$range_start}' ) / frequency ) + 0.00001
			     		) ) DAY ) <= repeat_end,
			     		DATE_ADD( start_date, INTERVAL ( frequency * CEIL(
			     			( TIMESTAMPDIFF( DAY, start_date, '{$range_start}' ) / frequency ) + 0.00001
			     		) ) DAY ),
			     		NULL
			     	)
			    )

		     	ELSE (
		     		IF (
			     		DATE_ADD( start_date, INTERVAL ( frequency * CEIL(
			     			( TIMESTAMPDIFF( MONTH, start_date, '{$range_start}' ) / frequency ) + 0.00001
				     	) ) MONTH ) <= repeat_end,
				     	DATE_ADD( start_date, INTERVAL ( frequency * CEIL(
			     			( TIMESTAMPDIFF( MONTH, start_date, '{$range_start}' ) / frequency ) + 0.00001
				     	) ) MONTH ),
				     	NULL
			     	)
			    )
		    END ) AS next_start,

		    MAX( CASE
                WHEN (repeat_unit = 'NONE')
             		THEN IF(end_date >= '{$range_start}' AND start_date <= '{$range_start}', end_date, NULL)

	         	WHEN (end_date >= '{$range_start}')
	                THEN IF(start_date <= '{$range_start}', end_date, NULL)

		     	WHEN (repeat_unit = 'DAY') THEN (
		     		IF (
			     		DATE_ADD( start_date, INTERVAL ( frequency * FLOOR(
			     			TIMESTAMPDIFF( DAY, start_date, '{$range_start}' ) / frequency
			     		) ) DAY ) <= repeat_end,
			     		DATE_ADD( end_date, INTERVAL ( frequency * FLOOR(
			     			TIMESTAMPDIFF( DAY, start_date, '{$range_start}' ) / frequency
			     		) ) DAY ),
			     		NULL
		     		)
		     	)

		     	ELSE (
		     		IF (
			     		DATE_ADD( start_date, INTERVAL ( frequency * FLOOR(
			     			TIMESTAMPDIFF( MONTH, start_date, '{$range_start}' ) / frequency
				     	) ) MONTH ) <= repeat_end,
			     		DATE_ADD( end_date, INTERVAL ( frequency * FLOOR(
			     			TIMESTAMPDIFF( MONTH, start_date, '{$range_start}' ) / frequency
				     	) ) MONTH ),
				     	NULL
				     )
			     )
		    END ) AS prev_end,

		    MAX( CASE
                WHEN (repeat_unit = 'NONE')
             		THEN IF(end_date >= '{$range_start}' AND start_date <= '{$range_start}', start_date, NULL)

	         	WHEN (end_date >= '{$range_start}')
	                THEN IF(start_date <= '{$range_start}', start_date, NULL)

		     	WHEN (repeat_unit = 'DAY') THEN (
		     		IF (
			     		DATE_ADD( start_date, INTERVAL ( frequency * FLOOR(
			     			TIMESTAMPDIFF( DAY, start_date, '{$range_start}' ) / frequency
			     		) ) DAY ) <= repeat_end,
			     		DATE_ADD( start_date, INTERVAL ( frequency * FLOOR(
			     			TIMESTAMPDIFF( DAY, start_date, '{$range_start}' ) / frequency
			     		) ) DAY ),
			     		NULL
		     		)
		     	)

		     	ELSE (
		     		IF (
			     		DATE_ADD( start_date, INTERVAL ( frequency * FLOOR(
			     			TIMESTAMPDIFF( MONTH, start_date, '{$range_start}' ) / frequency
				     	) ) MONTH ) <= repeat_end,
			     		DATE_ADD( start_date, INTERVAL ( frequency * FLOOR(
			     			TIMESTAMPDIFF( MONTH, start_date, '{$range_start}' ) / frequency
				     	) ) MONTH ),
				     	NULL
				     )
			     )
		    END ) AS prev_start

		    FROM {$wpdb->prefix}mylisting_events
			WHERE ( field_key = '{$field_key}' )
		    GROUP BY listing_id
		) AS `recur_{$field_key}` ON ({$wpdb->posts}.ID = `recur_{$field_key}`.listing_id) ";
}

/**
 * Get the SQL query to be used in the where clause to
 * filter by a recurring date.
 *
 * @since 2.4
 */
function get_where_clause( $range_start, $range_end, $field_key ) {
	global $wpdb;

	$range_start = esc_sql( $range_start );
	$range_end = esc_sql( $range_end );
	$field_key = esc_sql( $field_key );
	$join_key = sprintf( '`recur_%s`', $field_key );

	// clause when no end date is set
	if ( empty( $range_end ) ) {
		return " AND (
			{$join_key}.next_start >= '{$range_start}'
			OR {$join_key}.prev_end >= '{$range_start}'
		) ";
	}

	// clause with both start and end dates
	return " AND (
		( {$join_key}.next_start <= '{$range_end}' )
		OR ( {$join_key}.prev_end >= '{$range_start}' )
	) ";
}

/**
 * Get the SQL query to be used as the order clause to
 * order by a recurring date.
 *
 * @since 2.4
 */
function get_orderby_clause( $range_start, $range_end, $field_key, $order ) {
	global $wpdb;

	$range_start = esc_sql( $range_start );
	$range_end = esc_sql( $range_end );
	$field_key = esc_sql( $field_key );
	$join_key = sprintf( '`recur_%s`', $field_key );
	$order = ( $order === 'DESC' ) ? 'DESC' : 'ASC';

    return " IF(
    	{$join_key}.prev_end >= '{$range_start}',
    	{$join_key}.prev_start,
    	{$join_key}.next_start
    ) {$order} ";
}

/**
 * Convert a value passed through a form request to start/end valid date strings.
 * Value can be a date range, e.g. '2020-04-13..2020-04-18' or a preset range,
 * e.g. 'tomorrow' or 'this-weekend', which is then converted to a range based
 * on the current time.
 *
 * @since 2.4
 */
function parse_value( $value, $timepicker = false ) {
	$start_format = $timepicker ? 'Y-m-d H:i:00' : 'Y-m-d 00:00:00';
	$end_format = $timepicker ? 'Y-m-d H:i:59' : 'Y-m-d 23:59:59';
	$weekdays = [ 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday' ];
	$now = new \DateTime( 'now', c27()->get_timezone() );

	if ( strpos( $value, '..' ) !== false ) {
		$values = explode( '..', $value );
		$start_stamp = ! empty( $values[0] ) ? strtotime( $values[0] ) : false;
		$end_stamp = ! empty( $values[1] ) ? strtotime( $values[1] ) : false;

		return [
			'start' => $start_stamp ? date( $start_format, $start_stamp ) : false,
			'end' => $end_stamp ? date( $end_format, $end_stamp ) : false,
		];
	}

	if ( $value === 'all' ) {
		return [
			'start' => $now->format( 'Y-m-d H:i:s' ),
			'end' => '',
		];
	} elseif ( $value === 'any' ) {
		return [
			'start' => '',
			'end' => '',
		];
	} elseif ( $value === 'today' ) {
		return [
			'start' => $now->format( 'Y-m-d H:i:s' ),
			'end' => $now->format( 'Y-m-d 23:59:59' ),
		];
	} elseif ( $value === 'tomorrow' ) {
		$now->modify( '+1 day' );
		return [
			'start' => $now->format( 'Y-m-d 00:00:00' ),
			'end' => $now->format( 'Y-m-d 23:59:59' ),
		];
	} elseif ( $value === 'this-week' ) {
		$start_of_week = get_option( 'start_of_week' );
		$end_of_week = $start_of_week === 0 ? 6 : ( $start_of_week - 1 );

		return [
			'start' => $now->format( 'Y-m-d H:i:s' ),
			'end' => $now->modify( 'next '.$weekdays[ $end_of_week ] )->format( 'Y-m-d 23:59:59' ),
		];
	} elseif ( $value === 'this-weekend' ) {
		return [
			'start' => $now->modify('this saturday')->format( 'Y-m-d 00:00:00' ),
			'end' => $now->modify('+1 day')->format( 'Y-m-d 23:59:59' ),
		];
	} elseif ( $value === 'next-week' ) {
		$start_of_week = get_option( 'start_of_week' );

		return [
			'start' => $now->modify( 'next '.$weekdays[ $start_of_week ] )->format( 'Y-m-d 00:00:00' ),
			'end' => $now->modify('+6 days')->format( 'Y-m-d 23:59:59' ),
		];
	} elseif ( $value === 'this-month' ) {
		return [
			'start' => $now->format( 'Y-m-d H:i:s' ),
			'end' => $now->format( 'Y-m-t 23:59:59' ),
		];
	} elseif ( $value === 'next-month' ) {
		return [
			'start' => $now->modify('first day of next month')->format( 'Y-m-d 00:00:00' ),
			'end' => $now->format( 'Y-m-t 23:59:59' ),
		];
	} else {
		$value = sanitize_text_field( $value );
		return apply_filters( 'mylisting/filters/recurring-date/apply:'.$value, [], $now );
	}
}

/**
 * Get the next upcoming dates from the given recurring dates.
 *
 * @since 2.4
 */
function get_upcoming_instances( $recurring_dates, $count = 5, $reference = 'now' ) {
	$upcoming = [];
	$now = date_create( $reference );
	if ( ! $now ) {
		return [];
	}

	// $now = new \DateTime( '2020-04-14 20:20:00' );
	// dump('NOW: '.$now->format('Y-m-d H:i:s'));

	foreach ( $recurring_dates as $date ) {
		$start = date_create_from_format( 'Y-m-d H:i:s', $date['start'] );
		$end = date_create_from_format( 'Y-m-d H:i:s', $date['end'] );

		// repeat_end should have its time set to "23:59:59" for consistent behavior
		// with the MySQL query for finding recurrences
		$until = $date['repeat'] ? date_create_from_format( 'Y-m-d', $date['until'] ) : null;
		if ( ! is_null( $until ) ) {
			$until->setTime(23, 59, 59);
		}

		// invalid start/end dates
		if ( ! ( $start && $end ) || ( $end < $start ) ) {
			continue;
		}

		/**
		 * Handle non-recurring dates. It is considered an upcoming date
		 * if the end date has not been reached yet.
		 */
		if ( ! $date['repeat'] ) {
			if ( $now->getTimestamp() <= $end->getTimestamp() ) {
				$upcoming[] = [
					'start' => $date['start'],
					'end' => $date['end'],
				];
			}

			continue;
		}

		/**
		 * Handle recurring dates with daily/weekly intervals. It is considered
		 * an upcoming date if the recurring instance has not reached it's end yet.
		 *
		 * If the first end date has not been reached yet, the calculation starts
		 * the default start/end dates. Otherwise, find the current recurring instance
		 * and include previous instances that aren't over yet.
		 */
		if ( $date['unit'] === 'days' || $date['unit'] === 'weeks' ) {
			$frequency = $date['unit'] === 'weeks'
				? $date['frequency'] * 7
				: $date['frequency'];

			if ( $now->getTimestamp() <= $end->getTimestamp() ) {
				$days_to_add = 0;
			} else {
				// `$now->diff($end)->days` must increment the date diff only after
				// the same time of day has been passed; if time portions are equal in
				// $now and $end, treat it as part of the previous day.
				$mod = $now->format('H:i:s') === $end->format('H:i:s') ? 0 : 0.00001;
				$days_to_add = $frequency * ceil(
					( $now->diff( $end )->days / $frequency ) + $mod
				);
			}

			$start->modify( sprintf( '+%d days', $days_to_add ) );
			$end->modify( sprintf( '+%d days', $days_to_add ) );

			// find next n recurrences
			for ( $i=0; $i < $count; $i++ ) {
				$next_start = clone $start;
				$next_start = $next_start->modify(
					sprintf( '+%d days', $frequency * $i )
				)->format( 'Y-m-d H:i:s' );

				$next_end = clone $end;
				$next_end = $next_end->modify(
					sprintf( '+%d days', $frequency * $i )
				)->format( 'Y-m-d H:i:s' );

				// don't include dates past the repeat end date
				if ( strtotime( $next_start ) > $until->getTimestamp() ) {
					break;
				}

				$upcoming[] = [
					'start' => $next_start,
					'end' => $next_end,
				];
			}
		}

		/**
		 * Handle recurring dates with monthly/yearly intervals. Years are converted
		 * into months to simplify calculation.
		 */
		if ( $date['unit'] === 'months' || $date['unit'] === 'years' ) {
			$frequency = $date['unit'] === 'years'
				? $date['frequency'] * 12
				: $date['frequency'];

			if ( $now->getTimestamp() <= $end->getTimestamp() ) {
				$months_to_add = 0;
			} else {
				$mod = $now->format('H:i:s') === $end->format('H:i:s') ? 0 : 0.00001;
				$diff = $now->diff( $end );
				$months_to_add = $frequency * ceil(
					( ( $diff->m + ( $diff->y * 12 ) ) / $frequency ) + $mod
				);
			}

			$start_day_of_month = (int) $start->format('j');
			$end_day_of_month = (int) $end->format('j');

			$start->modify( sprintf( 'first day of +%d months', $months_to_add ) );
			$end->modify( sprintf( 'first day of +%d months', $months_to_add ) );

			for ( $i=0; $i < $count; $i++ ) {

				/**
				 * If the date is e.g. "2020-03-31", then adding a month will result
				 * in "2020-05-01", the default behavior in PHP. We want the result to
				 * be "2020-04-30" instead, so we have to correct this manually.
				 */
				$next_start = clone $start;
				$next_start = $next_start->modify( sprintf( '+%d months', $frequency * $i ) );
				$days_in_month = cal_days_in_month( CAL_GREGORIAN,
					(int) $next_start->format('n'),
					(int) $next_start->format('Y')
				);

				$next_start->modify( sprintf( '+%d days',
					( $start_day_of_month <= $days_in_month ? $start_day_of_month : $days_in_month ) - 1
				) );

				// do the same for the end date
				$next_end = clone $end;
				$next_end = $next_end->modify( sprintf( '+%d months', $frequency * $i ) );
				$days_in_month = cal_days_in_month( CAL_GREGORIAN,
					(int) $next_end->format('n'),
					(int) $next_end->format('Y')
				);

				$next_end->modify( sprintf( '+%d days',
					( $end_day_of_month <= $days_in_month ? $end_day_of_month : $days_in_month ) - 1
				) );

				if ( $next_start->getTimestamp() > $until->getTimestamp() ) {
					break;
				}

				$upcoming[] = [
					'start' => $next_start->format('Y-m-d H:i:s'),
					'end' => $next_end->format('Y-m-d H:i:s'),
				];
			}
		}
	}

	// sort by date
	usort( $upcoming, function($a, $b) {
		return strtotime( $a['start'] ) - strtotime( $b['start'] );
	} );

	return array_slice( $upcoming, 0, $count );
}

/**
 * Get the previous occurrences from the given recurring dates.
 *
 * @since 2.4
 */
function get_previous_instances( $recurring_dates, $count = 5, $reference = 'now' ) {
	$previous = [];
	$now = date_create( $reference );
	if ( ! $now ) {
		return [];
	}

	foreach ( $recurring_dates as $date ) {
		$start = date_create_from_format( 'Y-m-d H:i:s', $date['start'] );
		$end = date_create_from_format( 'Y-m-d H:i:s', $date['end'] );

		// repeat_end should have its time set to "23:59:59" for consistent behavior
		// with the MySQL query for finding recurrences
		$until = $date['repeat'] ? date_create_from_format( 'Y-m-d', $date['until'] ) : null;
		if ( ! is_null( $until ) ) {
			$until->setTime(23, 59, 59);
		}

		// invalid start/end dates
		if ( ! ( $start && $end ) || ( $end < $start ) ) {
			continue;
		}

		/**
		 * Handle non-recurring dates. It is considered an upcoming date
		 * if the end date has not been reached yet.
		 */
		if ( ! $date['repeat'] ) {
			if ( $now->getTimestamp() > $end->getTimestamp() ) {
				$previous[] = [
					'start' => $date['start'],
					'end' => $date['end'],
				];
			}

			continue;
		}

		if ( $date['unit'] === 'days' || $date['unit'] === 'weeks' ) {
			$frequency = $date['unit'] === 'weeks' ? $date['frequency'] * 7 : $date['frequency'];

			// make sure reference is between first start and repeat end
			$ref = ( $until < $now ) ? clone $until : clone $now;
			if ( $ref < $start ) {
				continue;
			}

			$days_to_add = $frequency * floor(
				( $ref->diff( $end )->days / $frequency )
			);

			$start->modify( sprintf( '+%d days', $days_to_add ) );
			$end->modify( sprintf( '+%d days', $days_to_add ) );

			// find previous n recurrences
			for ( $i=0; $i < $count; $i++ ) {
				$prev_start = clone $start;
				$prev_start = $prev_start->modify(
					sprintf( '-%d days', $frequency * $i )
				)->format( 'Y-m-d H:i:s' );

				$prev_end = clone $end;
				$prev_end = $prev_end->modify(
					sprintf( '-%d days', $frequency * $i )
				)->format( 'Y-m-d H:i:s' );

				// don't include dates before the initial start date
				if ( strtotime( $prev_start ) < strtotime( $date['start'] ) ) {
					break;
				}

				$previous[] = [
					'start' => $prev_start,
					'end' => $prev_end,
				];
			}
		}

		if ( $date['unit'] === 'months' || $date['unit'] === 'years' ) {
			$frequency = $date['unit'] === 'years' ? $date['frequency'] * 12 : $date['frequency'];

			// make sure reference is between first start and repeat end
			$ref = ( $until < $now ) ? clone $until : clone $now;
			if ( $ref < $start ) {
				continue;
			}

			$diff = $ref->diff( $end );
			$months_to_add = $frequency * floor(
				( $diff->m + ( $diff->y * 12 ) ) / $frequency
			);

			$start_day_of_month = (int) $start->format('j');
			$end_day_of_month = (int) $end->format('j');

			$start->modify( sprintf( 'first day of +%d months', $months_to_add ) );
			$end->modify( sprintf( 'first day of +%d months', $months_to_add ) );

			for ( $i=0; $i < $count; $i++ ) {
				$prev_start = clone $start;
				$prev_start = $prev_start->modify( sprintf( '-%d months', $frequency * $i ) );

				/**
				 * If the date is e.g. "2020-05-31", then subtracting a month will result
				 * in "2020-05-01", the default behavior in PHP. We want the result to
				 * be "2020-04-30" instead, so we have to correct this manually.
				 */
				$days_in_month = cal_days_in_month(
					CAL_GREGORIAN,
					(int) $prev_start->format('n'),
					(int) $prev_start->format('Y')
				);

				$prev_start->modify( sprintf( '+%d days',
					( $start_day_of_month <= $days_in_month ? $start_day_of_month : $days_in_month ) - 1
				) );

				// do the same for the end date
				$prev_end = clone $end;
				$prev_end = $prev_end->modify( sprintf( '-%d months', $frequency * $i ) );

				$days_in_month = cal_days_in_month(
					CAL_GREGORIAN,
					(int) $prev_end->format('n'),
					(int) $prev_end->format('Y')
				);

				$prev_end->modify( sprintf( '+%d days',
					( $end_day_of_month <= $days_in_month ? $end_day_of_month : $days_in_month ) - 1
				) );

				if ( $prev_start->getTimestamp() < strtotime( $date['start'] ) ) {
					break;
				}

				$previous[] = [
					'start' => $prev_start->format('Y-m-d H:i:s'),
					'end' => $prev_end->format('Y-m-d H:i:s'),
				];
			}
		}
	}

	// sort by date
	usort( $previous, function($a, $b) {
		return strtotime( $a['start'] ) - strtotime( $b['start'] );
	} );

	return array_slice( $previous, 0 - $count );
}

/**
 * Retrieve the textual representation of an event instance.
 *
 * @since 2.4
 */
function display_instance( $instance, $modifier = 'datetime', $reference = 'now' ) {
	$now = date_create( $reference );
	if ( ! $now ) {
		return '';
	}

	$start_stamp = ! empty( $instance['start'] )
		? strtotime( $instance['start'], $now->getTimestamp() )
		: false;

	$end_stamp = ! empty( $instance['end'] )
		? strtotime( $instance['end'], $now->getTimestamp() )
		: false;

	if ( ! $start_stamp ) {
		return '';
	}

	$date_format = apply_filters( 'mylisting/display-event:date-format', get_option('date_format') );
	$time_format = apply_filters( 'mylisting/display-event:time-format', get_option('time_format') );
	$datetime_format = sprintf( '%s %s', $date_format, $time_format );

	// format as date+time
	if ( $modifier === 'datetime' ) {
		$output = date_i18n( $datetime_format, $start_stamp );
		if ( $end_stamp ) {
			// if end date is within the same day, omit displaying the date part again
			$output .= ( ( $end_stamp - $start_stamp ) < DAY_IN_SECONDS )
				? sprintf( ' - %s', date_i18n( $time_format, $end_stamp ) )
				: sprintf( ' - %s', date_i18n( $datetime_format, $end_stamp ) );
		}

		return $output;
	}

	// format as date only
	if ( $modifier === 'date' ) {
		$output = date_i18n( $date_format, $start_stamp );
		// include end date only if it's in a separate day
		if ( $end_stamp && ( $end_stamp - $start_stamp ) > DAY_IN_SECONDS ) {
			$output .= sprintf( ' - %s', date_i18n( $date_format, $end_stamp ) );
		}

		return $output;
	}

	// format as time only
	if ( $modifier === 'time' ) {
		$output = date_i18n( $time_format, $start_stamp );
		if ( $end_stamp ) {
			$output .= sprintf( ' - %s', date_i18n( $time_format, $end_stamp ) );
		}

		return $output;
	}

	if ( $modifier === 'status' && $end_stamp ) {
		// display event status (always using "now" as reference time, unless we're running
		// tests, where we need to set a static reference time)
		if ( ! \MyListing\is_running_tests() ) {
			$now = date_create( 'now' );
		}

		if ( $now->getTimestamp() < $start_stamp ) {
			return _x( 'Upcoming', 'Event status', 'my-listing' );
		} elseif ( $now->getTimestamp() >= $start_stamp && $now->getTimestamp() <= $end_stamp ) {
			return _x( 'Ongoing', 'Event status', 'my-listing' );
		} else {
			return _x( 'Ended', 'Event status', 'my-listing' );
		}
	}

	if ( $modifier === 'start' ) {
		return date_i18n( $datetime_format, $start_stamp );
	}

	if ( $modifier === 'end' && $end_stamp ) {
		return date_i18n( $datetime_format, $end_stamp );
	}

	return '';
}

/**
 * Update a recurring-date field in a listing with the
 * provided set of dates.
 *
 * @since 2.5
 */
function update_field( $field, $dates ) {
	global $wpdb;

	// delete previous dates
	$wpdb->delete( $wpdb->prefix.'mylisting_events', [
		'listing_id' => $field->listing->get_id(),
		'field_key' => $field->key,
	] );

	// insert new dates
	$query_rows = [];
	$timepicker_enabled = $field->get_prop( 'enable_timepicker' );
	foreach ( $dates as $date ) {
		$start = date_create_from_format( $timepicker_enabled ? 'Y-m-d H:i:s' : 'Y-m-d', $date['start'] );
		$end = date_create_from_format( $timepicker_enabled ? 'Y-m-d H:i:s' : 'Y-m-d', $date['end'] );
		$until = $date['repeat'] ? date_create_from_format( 'Y-m-d', $date['until'] ) : null;
		$frequency = 0;
		$unit = 'NONE';

		// recurring date
		if ( $date['repeat'] ) {
			$frequency = $date['frequency'];

			// days and weeks can both use the `DAY` repeat unit
			if ( in_array( $date['unit'], [ 'days', 'weeks' ], true ) ) {
				$unit = 'DAY';
			}

			// months and years can both use the `MONTH` repeat unit
			if ( in_array( $date['unit'], [ 'months', 'years' ], true ) ) {
				$unit = 'MONTH';
			}

			// since weeks use the `DAY` repeat unit, we need to multiply the frequency by 7,
			// i.e. `every 3 weeks` is stored as `every 21 days`.
			if ( $date['unit'] === 'weeks' ) {
				$frequency *= 7;
			}

			// since years use the `MONTH` repeat unit, we need to multiply the frequency by 12,
			// i.e. `every 1 year` is stored as `every 12 months`.
			if ( $date['unit'] === 'years' ) {
				$frequency *= 12;
			}
		}

		$query_rows[] = $wpdb->prepare(
			/* listing_id, start_date, end_date, frequency, repeat_unit, repeat_end, field_key */
			'(%d,%s,%s,%d,%s,%s,%s)',

			/* values */
			$field->listing->get_id(),
			$start->format( $timepicker_enabled ? 'Y-m-d H:i:s' : 'Y-m-d 00:00:00' ),
			$end->format( $timepicker_enabled ? 'Y-m-d H:i:s' : 'Y-m-d 23:59:59' ),
			$frequency,
			$unit,

			// for non-recurring dates, end_date and repeat_end have the same value,
			// which simplifies some queries like the expiration check query
			$date['repeat'] ? $until->format( 'Y-m-d 23:59:59' ) : $end->format( 'Y-m-d H:i:s' ),
			$field->key
		);
	}

	// update database with new values
	if ( ! empty( $query_rows ) ) {
		$query = "INSERT INTO {$wpdb->prefix}mylisting_events
			(listing_id, start_date, end_date, frequency, repeat_unit, repeat_end, field_key) VALUES ";
		$query .= implode( ',', $query_rows );
		$wpdb->query( $query );
	}
}
