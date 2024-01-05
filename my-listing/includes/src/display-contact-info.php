<?php

namespace MyListing\Src;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Display_Contact_Info {

	public static function boot() {
		add_action( 'mylisting_ajax_display_contact_info', [ __CLASS__, 'handle_contact_info_request' ] );
		add_action( 'mylisting_ajax_nopriv_display_contact_info', [ __CLASS__, 'handle_contact_info_request' ] );
	}

	/**
	 * Handle `contact_info` AJAX request.
	 *
	 * @since 1.0
	 */
	public static function handle_contact_info_request() {
		$listing_id = ! empty( $_POST['listing_id'] ) ? absint( $_POST['listing_id'] ) : false;
		$field_id = ! empty( $_POST['field_id'] ) ? sanitize_text_field( $_POST['field_id'] ) : false;

		// validate request
		if ( ! ( $field_id && $listing_id ) ) {
			return;
		}

		// validate listing
		$listing = \MyListing\Src\Listing::get( $listing_id );
		if ( ! ( $listing && $listing->get_status() === 'publish' ) ) {
			return;
		}

		if ( $field_id === 'job_phone' ) {
			$field_id = 'phone';
		} else if ( $field_id === 'job_email' ) {
			$field_id = 'email';
		}

		$field_object = $listing->get_field_object( $field_id );
		if ( ! ( $field_object && ( $field_object->get_type() === 'number' || in_array( $field_id, [ 'phone', 'email' ], true ) ) )  ) {
			return;
		}

		$value = $listing->get_field( $field_id );

		// send json response
		return wp_send_json( [
			'status' => true,
			'value' => $value,
		] );
	}
}
