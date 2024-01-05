<?php

namespace MyListing\Src\Notifications;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Listing_Submitted_Admin_Notification extends Base_Notification {

	public $listing;

	public static function hook() {
		// listing submitted without paid listings
		add_action( 'mylisting/submission/done', function( $listing_id ) {
			return new self( [ 'listing-id' => $listing_id ] );
		} );

		// listing submitted with paid listings
		add_action( 'mylisting/submission/order-placed', function( $listing_id ) {
			return new self( [ 'listing-id' => $listing_id ] );
		} );
	}

	public static function settings() {
		return [
			'name' => _x( 'Notify admin on new listing submissions', 'Notifications', 'my-listing' ),
			'description' => _x( 'Send an email to the admin whenever a new listing is submitted.', 'Notifications', 'my-listing' ),
		];
	}

	/**
	 * Validate and prepare notifcation arguments.
	 *
	 * @since 2.1
	 */
	public function prepare( $args ) {
		if ( empty( $args['listing-id'] ) || ! ( $listing = \MyListing\Src\Listing::get( $args['listing-id'] ) ) || ! $listing->get_author() ) {
			throw new \Exception( 'Invalid listing ID: #'.$args['listing-id'] );
		}

		$this->listing = $listing;
	}

	public function get_mailto() {
		return get_option('admin_email');
	}

	public function get_subject() {
		return sprintf( _x( 'New listing submission: "%s"', 'Notifications', 'my-listing' ), esc_html( $this->listing->get_name() ) );
	}

	public function get_message() {
		$author = $this->listing->get_author();
		$template = new Notification_Template;

		$template->add_paragraph( sprintf(
			_x( 'A new listing has been submitted: <strong>%s</strong> by user <strong>%s</strong>.', 'Notifications', 'my-listing' ),
			esc_html( $this->listing->get_name() ),
			$author ? $author->display_name : _x( '(guest user)', 'Notifications', 'my-listing' )
		) );

		$template->add_break()->add_primary_button(
			_x( 'Review Listing', 'Notifications', 'my-listing' ),
			esc_url( c27()->get_edit_post_link( $this->listing->get_id() ) )
		);

		if ( $this->listing->get_status() === 'publish' ) {
			$template->add_button(
				_x( 'View Listing', 'Notifications', 'my-listing' ),
				esc_url( $this->listing->get_link() )
			);
		}

		if ( $author ) {
			$template->add_button(
				_x( 'View user profile', 'Notifications', 'my-listing' ),
				esc_url( c27()->get_edit_user_link( $this->listing->get_author_id() ) )
			);
		}

		return $template->get_body();
	}

}