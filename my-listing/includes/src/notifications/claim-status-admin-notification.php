<?php

namespace MyListing\Src\Notifications;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Claim_Status_Admin_Notification extends Base_Notification {

	public $listing, $claimer, $claim;

	public static function hook() {
		add_action( 'mylisting/claim:submitted', function( $claim_id ) {
			return new self( [ 'claim-id' => $claim_id ] );
		} );
	}

	public static function settings() {
		return [
			'name' => _x( 'Notify admin on new claim submissions', 'Notifications', 'my-listing' ),
			'description' => _x( 'Send an email to the admin whenever a new claim request is submitted', 'Notifications', 'my-listing' ),
		];
	}

	/**
	 * Validate and prepare notifcation arguments.
	 *
	 * @since 2.1
	 */
	public function prepare( $args ) {
		if ( empty( $args['claim-id'] ) ) {
			throw new \Exception( 'Invalid claim ID' );
		}

		$claim = get_post( $args['claim-id'] );
		if ( ! ( $claim && $claim->post_type === 'claim' && $claim->_user_id && $claim->_listing_id && $claim->_status ) ) {
			throw new \Exception( 'Invalid claim ID: #'.$args['claim-id'] );
		}

		$claimer = get_userdata( $claim->_user_id );
		$listing = \MyListing\Src\Listing::get( $claim->_listing_id );
		if ( ! ( $claimer && $listing ) ) {
			throw new \Exception( 'Invalid claim.' );
		}

		if ( ! in_array( $claim->_status, [ 'approved', 'pending', 'declined' ] ) ) {
			$claim->_status = 'pending';
		}

		$this->listing = $listing;
		$this->claimer = $claimer;
		$this->claim = $claim;
	}

	public function get_mailto() {
		return get_option('admin_email');
	}

	public function get_subject() {
		return sprintf( _x( 'New claim request for "%s".', 'Notifications', 'my-listing' ), esc_html( $this->listing->get_name() ) );
	}

	public function get_message() {
		$template = new Notification_Template;

		if ( $this->claim->_status === 'approved' ) {
			$template->add_paragraph( sprintf(
				_x( 'A new claim request has been submitted for <strong>%s</strong> by user <strong>%s</strong>. The request has been approved.', 'Notifications', 'my-listing' ),
				esc_html( $this->listing->get_name() ),
				esc_html( $this->claimer->display_name )
			) );
		}

		if ( $this->claim->_status === 'pending' ) {
			$template->add_paragraph( sprintf(
				_x( 'A new claim request has been submitted for <strong>%s</strong> by user <strong>%s</strong>. The request has been submitted for review.', 'Notifications', 'my-listing' ),
				esc_html( $this->listing->get_name() ),
				esc_html( $this->claimer->display_name )
			) );
		}

		$template->add_break()->add_primary_button(
			_x( 'View claim details', 'Notifications', 'my-listing' ),
			esc_url( c27()->get_edit_post_link( $this->claim->ID ) )
		);

		$template->add_button(
			_x( 'View Listing', 'Notifications', 'my-listing' ),
			esc_url( $this->listing->get_link() )
		);

		$template->add_button(
			_x( 'View user profile', 'Notifications', 'my-listing' ),
			esc_url( c27()->get_edit_user_link( $this->claimer->ID ) )
		);

		return $template->get_body();
	}

}