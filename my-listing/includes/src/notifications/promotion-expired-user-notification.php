<?php

namespace MyListing\Src\Notifications;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Promotion_Expired_User_Notification extends Base_Notification {

	public $listing, $user, $package_id;

	public static function hook() {
		add_action( 'mylisting/promotion:end', function( $listing_id, $package_id ) {
			if ( $listing = \MyListing\Src\Listing::get( $listing_id ) ) {
				return new self( [ 'listing' => $listing, 'package_id' => $package_id ] );
			}
		}, 50, 2 );
	}

	public static function settings() {
		return [
			'name' => _x( 'Notify users when a promotion ends', 'Notifications', 'my-listing' ),
			'description' => _x( 'Send an email to the user when their promotion package expires', 'Notifications', 'my-listing' ),
		];
	}

	/**
	 * Validate and prepare notifcation arguments.
	 *
	 * @since 2.1
	 */
	public function prepare( $args ) {
		if ( empty( $args['listing'] ) ) {
			throw new \Exception( 'Invalid listing provided.' );
		}

		$this->listing = $args['listing'];
		$this->package_id = $args['package_id'];

		// validate author
		$this->user = get_userdata( $this->listing->get_author_id() );
		if ( ! $this->user ) {
			throw new \Exception( 'Invalid user ID.' );
		}
	}

	public function get_mailto() {
		return $this->user->user_email;
	}

	public function get_subject() {
		return sprintf(
			_x( 'Promotion for "%s" has ended.', 'Notifications', 'my-listing' ),
			esc_html( $this->listing->get_title() )
		);
	}

	public function get_message() {
		$template = new Notification_Template;

		$template->add_paragraph( sprintf(
			_x( 'Hi %s,', 'Notifications', 'my-listing' ),
			esc_html( $this->user->first_name )
		) );

		$template->add_paragraph( sprintf(
			_x( 'The promotion period for <strong>%s</strong> has ended.', 'Notifications', 'my-listing' ),
			esc_html( $this->listing->get_title() )
		) );

		$template->add_break()->add_primary_button(
			_x( 'Go to dashboard', 'Notifications', 'my-listing' ),
			esc_url( wc_get_account_endpoint_url( \MyListing\my_listings_endpoint_slug() ) )
		);

		return $template->get_body();
	}

}