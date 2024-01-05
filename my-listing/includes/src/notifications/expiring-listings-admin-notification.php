<?php

namespace MyListing\Src\Notifications;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Expiring_Listings_Admin_Notification extends Base_Notification {

	public $listings;

	public static function hook() {
		add_action( 'mylisting/expiring-listings/all', function( $listings ) {
			if ( ! empty( $listings ) ) {
				return new self( [ 'listings' => $listings ] );
			}
		} );
	}

	public static function settings() {
		return [
			'name' => _x( 'Notify admin on expiring listings', 'Notifications', 'my-listing' ),
			'description' => _x( 'Send an email to the site admin when one or more listings are reaching their expiry date.', 'Notifications', 'my-listing' ),
		];
	}

	/**
	 * Validate and prepare notifcation arguments.
	 *
	 * @since 2.1
	 */
	public function prepare( $args ) {
		if ( empty( $args['listings'] ) ) {
			throw new \Exception( 'Missing arguments.' );
		}

		// validate listings
		$this->listings = [];
		foreach ( $args['listings'] as $listing ) {
			if ( $listing = \MyListing\Src\Listing::get( $listing ) ) {
				$this->listings[] = $listing;
			}
		}

		if ( empty( $this->listings ) ) {
			throw new \Exception( 'Invalid listings.' );
		}
	}

	public function get_mailto() {
		return get_option('admin_email');
	}

	public function get_subject() {
		if ( count( $this->listings ) === 1 ) {
			return sprintf(
				_x( 'Admin Notice: "%s" will expire on %s.', 'Notifications', 'my-listing' ),
				esc_html( $this->listings[0]->get_name() ),
				date_i18n( get_option( 'date_format' ), strtotime( get_post_meta( $this->listings[0]->get_id(), '_job_expires', true ) ) )
			);
		}

		return sprintf(
			_x( 'Admin Notice: %s listings will expire soon.', 'Notifications', 'my-listing' ),
			number_format_i18n( count( $this->listings ) )
		);
	}

	public function get_message() {
		$template = new Notification_Template;

		if ( count( $this->listings ) > 1 ) {
			$template->add_paragraph( _x( 'The following listings are set to expire soon.', 'Notifications', 'my-listing' ) );
		}

		foreach ( $this->listings as $listing ) {
			$template
				->add_paragraph( sprintf(
					_x( '<strong>%s</strong> will expire on <strong>%s</strong>', 'Notifications', 'my-listing' ),
					esc_html( $listing->get_name() ),
					date_i18n( get_option( 'date_format' ), strtotime( get_post_meta( $listing->get_id(), '_job_expires', true ) ) )
				) )
				->add_button( _x( 'Edit listing', 'Notifications', 'my-listing' ), esc_url( c27()->get_edit_post_link( $listing->get_id() ) ) )
				->add_button( _x( 'View listing', 'Notifications', 'my-listing' ), esc_url( $listing->get_link() ) )
				->add_button( _x( 'View user profile', 'Notifications', 'my-listing' ), esc_url( c27()->get_edit_user_link( $listing->get_author_id() ) ) )
				->add_thematic_break();
		}

		$template->add_primary_button( _x( 'Go to dashboard', 'Notifications', 'my-listing' ), esc_url( admin_url( 'edit.php?post_type=job_listing' ) ) );

		return $template->get_body();
	}
}