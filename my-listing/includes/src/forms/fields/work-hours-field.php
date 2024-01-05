<?php

namespace MyListing\Src\Forms\Fields;

use \DateTime as DateTime;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Work_Hours_Field extends Base_Field {

	public function get_posted_value() {
		return ! empty( $_POST[ $this->key ] ) ? (array) $_POST[ $this->key ] : [];
	}

	public function validate() {
		$value = $this->get_posted_value();
		//
	}

	public function update() {
		$value = $this->get_posted_value();
		update_post_meta( $this->listing->get_id(), '_'.$this->key, $value );

		global $wpdb;
		$wpdb->delete( $wpdb->prefix.'mylisting_workhours', [
			'listing_id' => $this->listing->get_id()
		] );

		try {
			$timezone = new \DateTimeZone( $value['timezone'] ?? null );
		} catch ( \Exception $e ) {
			$timezone = wp_timezone();
		}

		$rows = [];
		$ranges = \MyListing\Helpers::get_open_ranges( $value );
		foreach ( $ranges as $range ) {
			$rows[] = sprintf( '(%d,%d,%d,\'%s\')', $this->listing->get_id(), $range[0], $range[1], esc_sql( $timezone->getName() ) );
		}

		if ( ! empty( $rows ) ) {
			$query = "INSERT INTO {$wpdb->prefix}mylisting_workhours
				(`listing_id`, `start`, `end`, `timezone`) VALUES ";
			$query .= implode( ',', $rows );
			$wpdb->query( $query );
		}
	}
 
	public function field_props() {
		$this->props['type'] = 'work-hours';
	}

	public function string_value( $modifier = null ) {
		return $this->listing->get_schedule()->get_label_for_preview_card();
	}

	public function get_editor_options() {
		$this->getLabelField();
		$this->getKeyField();
		$this->getPlaceholderField();
		$this->getDescriptionField();
		$this->getRequiredField();
		$this->getShowInSubmitFormField();
		$this->getShowInAdminField();
		$this->getShowInCompareField();
	}
}