<?php

namespace MyListing\Src\Notifications;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Listing_Reported_Admin_Notification extends Base_Notification {

	public
		$report,
		$reason,
		$listing,
		$user;

	public static function hook() {
		// new report submitted
		add_action( 'mylisting/reports:new-submission', function( $report_id ) {
			return new self( [ 'report-id' => $report_id ] );
		} );
	}

	public static function settings() {
		return [
			'name' => _x( 'Notify admin when a listing gets reported', 'Notifications', 'my-listing' ),
			'description' => _x( 'Send an email to the admin whenever a new report has been received.', 'Notifications', 'my-listing' ),
		];
	}

	public function prepare( $args ) {
		if ( empty( $args['report-id'] ) ) {
			throw new \Exception( 'Missing report ID.' );
		}

		$this->report = get_post( $args['report-id'] );
		if ( ! ( $this->report && $this->report->post_type === 'case27_report' ) ) {
			throw new \Exception( 'Invalid report.' );
		}

		$this->listing = \MyListing\Src\Listing::get( get_post_meta( $this->report->ID, '_report_listing_id', true ) );
		$this->user = get_userdata( get_post_meta( $this->report->ID, '_report_user_id', true ) ); // user who reported the listing
		$this->reason = get_post_meta( $this->report->ID, '_report_content', true );

		if ( ! ( $this->listing && $this->user && $this->reason ) ) {
			throw new \Exception( 'Invalid report email arguments.' );
		}
	}

	public function get_mailto() {
		return get_option('admin_email');
	}

	public function get_subject() {
		return sprintf( _x( 'A listing has been reported: "%s"', 'Notifications', 'my-listing' ), esc_html( $this->listing->get_name() ) );
	}

	public function get_message() {
		$template = new Notification_Template;

		$template->add_paragraph( sprintf(
			_x( 'A new report has been submitted for listing <strong>%s</strong> by user <strong>%s</strong>, with the following reason:', 'Notifications', 'my-listing' ),
			esc_html( $this->listing->get_name() ),
			$this->user->display_name
		) );

		$template->add_paragraph( '<em>'.nl2br( esc_html( $this->reason ) ).'</em>' );

		$template->add_break()->add_primary_button(
			_x( 'View Report', 'Notifications', 'my-listing' ),
			esc_url( c27()->get_edit_post_link( $this->report->ID ) )
		);

		$template->add_button(
			_x( 'Open Listing', 'Notifications', 'my-listing' ),
			esc_url( $this->listing->get_link() )
		);

		return $template->get_body();
	}

}