<?php

namespace MyListing\Apis\Wp_All_Import;

if ( ! defined('ABSPATH') ) {
	exit;
}

function import_work_hours( $field, $field_value, $log ) {
	$method = $field_value['method'] ?? 'default';

	if ( $method === 'default' ) {
		$work_hours = [];

		$days = [
			'mon' => 'Monday',
			'tue' => 'Tuesday',
			'wed' => 'Wednesday',
			'thu' => 'Thursday',
			'fri' => 'Friday',
			'sat' => 'Saturday',
			'sun' => 'Sunday',
		];

		foreach ( $days as $form_key => $db_key ) {
			$work_hours[ $db_key ] = [
				'status' => 'enter-hours',
			];

			$from = $field_value[ $form_key.'_from' ] ?? null;
			$to = $field_value[ $form_key.'_to' ] ?? null;

			if ( $from === 'open' ) {
				$work_hours[ $db_key ]['status'] = 'open-all-day';
				continue;
			}

			if ( $from === 'closed' ) {
				$work_hours[ $db_key ]['status'] = 'closed-all-day';
				continue;
			}

			if ( $from === 'appointment' ) {
				$work_hours[ $db_key ]['status'] = 'by-appointment-only';
				continue;
			}

			if ( is_null( $from ) || is_null( $to ) ) {
				continue;
			}

			foreach ( explode( ',', $from ) as $key => $starting_hour ) {
				$timestamp = strtotime( $starting_hour );
				if ( $timestamp ) {
					$work_hours[ $db_key ][ $key ] = [
						'from' => date( 'H:i', $timestamp ),
					];
				}
			}

			foreach ( explode( ',', $to ) as $key => $ending_hour ) {
				$timestamp = strtotime( $ending_hour );
				if ( $timestamp && isset( $work_hours[ $db_key ][ $key ] ) ) {
					$work_hours[ $db_key ][ $key ]['to'] = date( 'H:i', $timestamp );
				} else {
					unset( $work_hours[ $db_key ][ $key ] );
				}
			}
		}

		$timezone = $field_value['timezone'] ?? null;
		if ( is_null( $timezone ) || ! in_array( $timezone, timezone_identifiers_list() ) ) {
			$timezone = wp_timezone()->getName();
			$log( sprintf(
				'<strong>NOTICE:</strong> Invalid timezone provided for "%s", using site timezone.',
				$field->get_label()
			) );
		}

		$work_hours['timezone'] = $timezone;

		update_post_meta( $field->listing->get_id(), '_'.$field->get_key(), $work_hours );
	}

	if ( $method === 'serialized' ) {
		$work_hours = @unserialize( $field_value['serialized'] ?? null );
		if ( ! ( $work_hours && is_array( $work_hours ) ) ) {
			$log( sprintf(
				'<strong>WARNING:</strong> Invalid serialized value supplied for "%s", skipping.',
				$field->get_label()
			) );
			return;
		}

		update_post_meta( $field->listing->get_id(), '_'.$field->get_key(), $work_hours );
	}

	if ( ! empty( $work_hours ) ) {
		$_POST[ $field->get_key() ] = $work_hours;
		$field->update();
		unset( $_POST[ $field->get_key() ] );
	}
}
