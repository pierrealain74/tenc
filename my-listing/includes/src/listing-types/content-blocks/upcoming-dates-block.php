<?php

namespace MyListing\Src\Listing_Types\Content_Blocks;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Upcoming_Dates_Block extends Base_Block {

	public function props() {
		$this->props['type'] = 'upcoming_dates';
		$this->props['title'] = 'Upcoming Dates';
		$this->props['icon'] = 'fa fa-calendar-alt';
		$this->props['show_field'] = 'event_date';
		$this->props['count'] = 5;
		$this->props['past_count'] = 0;
		$this->props['show_add_to_gcal'] = true;
		$this->props['show_add_to_ical'] = true;
		$this->allowed_fields = [ 'recurring-date', 'date' ];
	}

	public function get_editor_options() {
		$this->getLabelField();
		$this->getSourceField();
		?>
		<div class="form-group">
			<label>Number of upcoming/ongoing dates to show:</label>
			<input type="number" v-model="block.count" min="1">
		</div>
		<div class="form-group">
			<label>Number of past dates to show (Default: 0)</label>
			<input type="number" v-model="block.past_count" min="0">
		</div>
		<div class="form-group">
			<div class="mb5"></div>
			<label>
				<input type="checkbox" class="form-checkbox" v-model="block.show_add_to_gcal">
				Show "Add to Google Calendar" button?
			</label>
		</div>
		<div class="form-group">
			<div class="mb5"></div>
			<label>
				<input type="checkbox" class="form-checkbox" v-model="block.show_add_to_ical">
				Show "Add to iCalendar" button?
			</label>
		</div>
		<?php
	}

	public function get_dates() {
		$dates = [];
		$now = date_create('now');
		$field = $this->listing->get_field_object( $this->get_prop( 'show_field' ) );
		if ( ! $field ) {
			return $dates;
		}

		if ( $field->get_type() === 'date' ) {
			$date = $field->get_value();
			if ( ! empty( $date ) && strtotime( $date ) ) {
				$dates[] = [
					'start' => $date,
					'end' => '',
					'gcal_link' => $this->get_google_calendar_link( $date ),
					'is_over' => $now->getTimestamp() > strtotime( $date, $now->getTimestamp() ),
				];
			}
		}

		if ( $field->get_type() === 'recurring-date' ) {
			$dates = array_merge(
				\MyListing\Src\Recurring_Dates\get_previous_instances( $field->get_value(), $this->get_prop('past_count') ),
				\MyListing\Src\Recurring_Dates\get_upcoming_instances( $field->get_value(), $this->get_prop('count') )
			);

			foreach ( $dates as $key => $date ) {
				$dates[$key]['gcal_link'] = $this->get_google_calendar_link( $date['start'], $date['end'] );
				$dates[$key]['is_over'] = $now->getTimestamp() > strtotime( $date['end'], $now->getTimestamp() );
			}
		}

		return $dates;
	}

	public function get_google_calendar_link( $start_date, $end_date = '' ) {
		// &dates=20170101T180000Z/20170101T190000Z
		$template = 'https://calendar.google.com/calendar/render?action=TEMPLATE&';
		$template .= 'text={title}&dates={dates}&details={description}&location={location}&trp=true&ctz={timezone}';

		// generate a description
		if ( $tagline = $this->listing->get_field( 'tagline' ) ) {
			$description = wp_kses( $tagline, [] );
		} else {
			$description = wp_kses( $this->listing->get_field( 'description' ), [] );
			$description = mb_strimwidth( $description, 0, 150, '...' );
		}

		if ( ! empty( $description ) ) {
			$description .= ' ';
		}

		// append listing link to the description
		$description .= $this->listing->get_link();

		// generate date string
		$dates = date( 'Ymd\THis', strtotime( $start_date ) );
		if ( ! empty( $end_date ) ) {
			$dates .= date( '/Ymd\THis', strtotime( $end_date ) );
		} else {
			// if no end date, just duplicate the start date as the link
			// doesn't work with just a start date
			$dates .= date( '/Ymd\THis', strtotime( $start_date ) );
		}

		$location = $this->listing->get_field('location', true)
			? $this->listing->get_field('location', true)->string_value('address')
			: null;

		$values = [
			'{title}' => $this->listing->get_title(),
			'{description}' => $description,
			'{location}' => $location,
			'{dates}' => $dates,
			'{timezone}' => c27()->get_timezone_string(),
		];

		return str_replace( array_keys( $values ), array_values( $values ), $template );
	}

	public function get_date_modifier() {
		$field = $this->listing->get_field_object( $this->get_prop( 'show_field' ) );
		if ( $field && $field->get_type() === 'recurring-date' && ! $field->get_prop('enable_timepicker') ) {
			return 'date';
		}

		return 'datetime';
	}
}