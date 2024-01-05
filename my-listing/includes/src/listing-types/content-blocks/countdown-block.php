<?php

namespace MyListing\Src\Listing_Types\Content_Blocks;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Countdown_Block extends Base_Block {

	public function props() {
		$this->props['type'] = 'countdown';
		$this->props['title'] = 'Countdown';
		$this->props['icon'] = 'mi av_timer';
		$this->props['show_field'] = 'job_date';
		$this->allowed_fields = [ 'date', 'recurring-date', 'text' ];
	}

	public function get_editor_options() {
		$this->getLabelField();
		$this->getSourceField();
	}

	public function get_date_diff() {
		$tz = c27()->get_timezone();
		$now = new \DateTime( 'now', $tz );
		$field = $this->listing->get_field_object( $this->get_prop( 'show_field' ) );
		if ( ! $field ) {
			return false;
		}

		if ( $field->get_type() === 'date' ) {
			$date = $field->get_value();
			if ( ! empty( $date ) && strtotime( $date ) ) {
				$upcoming = new \DateTime( $date, $tz );
			}
		}

		if ( $field->get_type() === 'recurring-date' ) {
			$dates = \MyListing\Src\Recurring_Dates\get_upcoming_instances(
				$field->get_value(), 1
			);

			if ( ! empty( $dates ) ) {
				$upcoming = new \DateTime( $dates[0]['start'], $tz );
			}
		}

		if ( ! ( isset( $upcoming ) && $upcoming instanceof \DateTime ) ) {
			return false;
		}

		return $upcoming->diff( $now );
	}
}
