<?php

namespace MyListing\Src\Forms\Fields;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Recurring_Date_Field extends Base_Field {

	public $modifiers = [
		'date' => '%s Date',
		'time' => '%s Time',
		'status' => '%s Status',
		'start' => '%s Start',
		'end' => '%s End',
	];

	private $cached_value;

	public function get_posted_value() {
		if ( empty( $_POST[ $this->key ] ) || ! is_array( $_POST[ $this->key ] ) ) {
			return [];
		}

		$dates = [];
		$posted = $_POST[ $this->key ];
		$units = ['days', 'weeks', 'months', 'years'];

		foreach ( $posted as $date ) {
			if ( ! empty( $date['start'] ) || ! empty( $date['end'] ) ) {
				$dates[] = [
					'start' => sanitize_text_field( $date['start'] ),
					'end' => sanitize_text_field( $date['end'] ),
					'repeat' => isset( $date['repeat'] ),
					'frequency' => ! empty( $date['frequency'] ) ? absint( $date['frequency'] ) : '',
					'until' => isset( $date['until'] ) ? sanitize_text_field( $date['until'] ) : false,
					'unit' => isset( $date['unit'] ) && in_array( $date['unit'], $units, true ) ? $date['unit'] : 'days',
				];
			}
		}

		return $dates;
	}

	public function validate() {
		$dates = $this->get_posted_value();

		// if multiple dates aren't enabled, make sure only one date is present
		if ( ! $this->props['allow_multiple'] && count( $dates ) > 1 ) {
			throw new \Exception( sprintf(
				_x( 'You can\'t add more than one date for "%s"', 'Add listing form', 'my-listing' ),
				$this->props['label']
			) );
		}

		// if multiple dates are enabled, make sure the date count is not higher than the `max_date_count` setting.
		if ( $this->props['allow_multiple'] && count( $dates ) > $this->props['max_date_count'] ) {
			throw new \Exception( sprintf(
				_x( 'You can\'t add more than %d dates for "%s"', 'Add listing form', 'my-listing' ),
				$this->props['max_date_count'],
				$this->props['label']
			) );
		}

		$timepicker_enabled = $this->get_prop( 'enable_timepicker' );
		foreach ( $dates as $date ) {
			$start = date_create_from_format( $timepicker_enabled ? 'Y-m-d H:i:s' : 'Y-m-d', $date['start'] );
			$end = date_create_from_format( $timepicker_enabled ? 'Y-m-d H:i:s' : 'Y-m-d', $date['end'] );
			$until = date_create_from_format( 'Y-m-d', $date['until'] );

			// start+end are always required; end must always be higher than start
			if ( ! ( $start && $end && $end >= $start ) ) {
				throw new \Exception( sprintf(
					_x( 'The date provided for "%s" is not valid.', 'Add listing form', 'my-listing' ),
					$this->props['label']
				) );
			}

			// make sure no recurrence data is submitted if recurrence is disabled
			if ( ! $this->props['allow_recurrence'] && $date['repeat'] ) {
				throw new \Exception( sprintf(
					_x( 'The date provided for "%s" is not valid.', 'Add listing form', 'my-listing' ),
					$this->props['label']
				) );
			}

			// if it's a recurring date, repeat_end is required, and it must be higher than the start date
			if ( $date['repeat'] && ! ( $until && $until >= $start ) ) {
				throw new \Exception( sprintf(
					_x( 'You must provide a repeat end date for "%s".', 'Add listing form', 'my-listing' ),
					$this->props['label']
				) );
			}

			// if it's a recurring date, frequency must be at least 1
			if ( $date['repeat'] && ( ! is_numeric( $date['frequency'] ) || $date['frequency'] < 1 ) ) {
				throw new \Exception( sprintf(
					_x( 'You must provide a valid repeat interval for "%s".', 'Add listing form', 'my-listing' ),
					$this->props['label']
				) );
			}
		}
	}

	public function admin_validate() {
		$this->validate();
	}

	public function update() {
		$dates = $this->get_posted_value();
		\MyListing\Src\Recurring_Dates\update_field( $this, $dates );
	}

	public function get_value() {
		if ( ! is_null( $this->cached_value ) ) {
			return $this->cached_value;
		}

		$this->cached_value = $this->get_dates();
		return $this->cached_value;
	}

	/**
	 * Get all date rows attached to this listing on this field.
	 *
	 * @since 2.4
	 */
	public function get_dates() {
		global $wpdb;

		$dates = [];
		$rows = $wpdb->get_results( $wpdb->prepare( "
			SELECT start_date, end_date, frequency, repeat_unit, repeat_end
				FROM {$wpdb->prefix}mylisting_events
			WHERE listing_id = %d AND field_key = %s
			ORDER BY id ASC
		", $this->listing->get_id(), $this->key ), ARRAY_A );

		foreach ( $rows as $row ) {
			$date = [
				'start' => $row['start_date'],
				'end' => $row['end_date'],
				'repeat' => $row['repeat_unit'] !== 'NONE',
			];

			// if the repeat unit is set to `DAY` then convert the frequency and
			// units to weeks if divisible by 7; otherwise, leave it at days
			if ( $row['repeat_unit'] === 'DAY' ) {
				if ( absint( $row['frequency'] ) % 7 === 0 ) {
					$date['frequency'] = absint( $row['frequency'] ) / 7;
					$date['unit'] = 'weeks';
				} else {
					$date['frequency'] = absint( $row['frequency'] );
					$date['unit'] = 'days';
				}

				$date['until'] = date_create_from_format( 'Y-m-d H:i:s', $row['repeat_end'] )->format( 'Y-m-d' );
			}

			// if the repeat unit is set to `MONTH` then convert the frequency and
			// units to years if divisible by 12; otherwise, leave it at months
			if ( $row['repeat_unit'] === 'MONTH' ) {
				if ( absint( $row['frequency'] ) % 12 === 0 ) {
					$date['frequency'] = absint( $row['frequency'] ) / 12;
					$date['unit'] = 'years';
				} else {
					$date['frequency'] = absint( $row['frequency'] );
					$date['unit'] = 'months';
				}

				$date['until'] = date_create_from_format( 'Y-m-d H:i:s', $row['repeat_end'] )->format( 'Y-m-d' );
			}

			$dates[] = $date;
		}

		return $dates;
	}

	public function field_props() {
		$this->props['type'] = 'recurring-date';
		$this->props['allow_recurrence'] = true;
		$this->props['allow_multiple'] = true;
		$this->props['enable_timepicker'] = true;
		$this->props['max_date_count'] = 3;
	}

	public function get_editor_options() {
		$this->getLabelField();
		$this->getKeyField();
		$this->getDescriptionField();
		$this->getRecurringDateSettings();
		$this->getRequiredField();
		$this->getShowInSubmitFormField();
		$this->getShowInAdminField();
		$this->getShowInCompareField();
	}

	protected function getRecurringDateSettings() { ?>
		<div class="form-group">
			<label>Enable recurrence</label>
			<label class="form-switch mb0">
				<input type="checkbox" v-model="field.allow_recurrence">
				<span class="switch-slider"></span>
			</label>
			<p>Allow users to repeat the event date at regular intervals (e.g. every 2 weeks, every 6 months, etc.)</p>
		</div>

		<div class="form-group">
			<label>Enable multiple dates</label>
			<label class="form-switch mb0">
				<input type="checkbox" v-model="field.allow_multiple">
				<span class="switch-slider"></span>
			</label>
			<p>Allow users to add multiple dates for events that repeat at irregular intervals.</p>

			<div class="mt10" v-show="field.allow_multiple">
				<label>Maximum date count</label>
				<input type="number" v-model="field.max_date_count" min="0" style="max-width:90px;">
				<p>Set the maximum amount of irregular dates that the user can add.</p>
			</div>
		</div>

		<div class="form-group">
			<label>Enable timepicker</label>
			<label class="form-switch mb0">
				<input type="checkbox" v-model="field.enable_timepicker">
				<span class="switch-slider"></span>
			</label>
			<p>Set whether users can also select the time of day when adding an event date.</p>
		</div>
	<?php }

	public function string_value( $modifier = null ) {
		// private filter; set the reference date for upcoming events, used in Explore
		// page to display results relevant to the search timeframe
		$reference = apply_filters( sprintf(
			'_mylisting/recurring-dates/%s:reference', $this->get_key()
		), 'now' );

		$dates = \MyListing\Src\Recurring_Dates\get_upcoming_instances( $this->get_value(), 1, $reference );
		if ( empty( $dates ) ) {
			$dates = \MyListing\Src\Recurring_Dates\get_previous_instances( $this->get_value(), 1, $reference );
		}

		$date = ! empty( $dates ) ? array_shift( $dates ) : false;
		$modifier = ! empty( $modifier ) ? $modifier : 'datetime';
		if ( empty( $date ) || ! in_array( $modifier, ['datetime', 'date', 'time', 'status', 'start', 'end'], true ) ) {
			return '';
		}

		return \MyListing\Src\Recurring_Dates\display_instance( $date, $modifier, $reference );
	}
}
