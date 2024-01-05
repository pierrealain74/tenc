<?php

namespace MyListing\Src\Notifications;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Claim_Status_User_Notification extends Base_Notification {

	public $listing, $claimer, $claim;

	public static function hook() {
		add_action( 'mylisting/claim:submitted', function( $claim_id ) {
			return new self( [ 'claim-id' => $claim_id ] );
		} );

		add_action( 'mylisting/admin/claim:updated', function( $claim_id, $send_email ) {
			if ( (bool) $send_email === true ) {
				return new self( [ 'claim-id' => $claim_id ] );
			}
		}, 10, 2 );
	}

	public static function settings() {
		return [
			'name' => _x( 'Notify user on claim status change', 'Notifications', 'my-listing' ),
			'description' => _x( 'Send an email to the user whenever their claim is approved or declined.', 'Notifications', 'my-listing' ),
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
		return $this->claimer->user_email;
	}

	public function get_subject() {
		if ( $this->claim->_status === 'approved' ) {
			return sprintf( _x( 'Your claim request for "%s" has been approved.', 'Notifications', 'my-listing' ), esc_html( $this->listing->get_name() ) );
		}

		if ( $this->claim->_status === 'pending' ) {
			return sprintf( _x( 'Your claim request for "%s" has been submitted for review.', 'Notifications', 'my-listing' ), esc_html( $this->listing->get_name() ) );
		}

		if ( $this->claim->_status === 'declined' ) {
			return sprintf( _x( 'Your claim request for "%s" has been declined.', 'Notifications', 'my-listing' ), esc_html( $this->listing->get_name() ) );
		}
	}

	public function get_message() {
		$template = new Notification_Template;

		$template->add_paragraph( sprintf(
			_x( 'Hi %s,', 'Notifications', 'my-listing' ),
			esc_html( $this->claimer->first_name )
		) );

		if ( $this->claim->_status === 'approved' ) {
			$template->add_paragraph( sprintf(
				_x( 'Your claim request for <strong>%s</strong> has been approved. You have now been assigned as the new owner of this listing.', 'Notifications', 'my-listing' ),
				esc_html( $this->listing->get_name() )
			) );
		}

		if ( $this->claim->_status === 'pending' ) {
			$template->add_paragraph( sprintf(
				_x( 'Your claim request for <strong>%s</strong> has been submitted for review. We\'ll get back to you soon.', 'Notifications', 'my-listing' ),
				esc_html( $this->listing->get_name() )
			) );
		}

		if ( $this->claim->_status === 'declined' ) {
			$template->add_paragraph( sprintf(
				_x( 'Your claim request for <strong>%s</strong> has been declined. Please let us know if you think we\'ve made a mistake.', 'Notifications', 'my-listing' ),
				esc_html( $this->listing->get_name() )
			) );
		}

		$template->add_break()->add_primary_button(
			_x( 'View claim details', 'Notifications', 'my-listing' ),
			esc_url( add_query_arg( 'claim-id', $this->claim->ID, wc_get_account_endpoint_url( _x( 'claim-requests', 'Claims user dashboard page slug', 'my-listing' ) ) ) )
		);

		$template->add_button(
			_x( 'View Listing', 'Notifications', 'my-listing' ),
			esc_url( $this->listing->get_link() )
		);

		return $template->get_body();
	}

}