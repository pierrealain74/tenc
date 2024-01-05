<?php

namespace MyListing\Src\Notifications;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Review_Submitted_User_Notification extends Base_Notification {

	public
		$listing,
		$author,
		$comment,
		$context = 'submit'; // review submit or update

	public static function hook() {
		add_action( 'mylisting/review-submitted', function( $comment_id, $listing_id ) {
			return new self( [
				'listing-id' => $listing_id,
				'comment-id' => $comment_id,
				'context' => 'submit',
			] );
		}, 50, 2 );

		add_action( 'mylisting/review-updated', function( $comment_id, $listing_id ) {
			return new self( [
				'listing-id' => $listing_id,
				'comment-id' => $comment_id,
				'context' => 'update',
			] );
		}, 50, 2 );

		add_action( 'comment_unapproved_to_approved', function( $comment ) {
			if ( get_post_type( $comment->comment_post_ID ) === 'job_listing' ) {
				return new self( [
					'listing-id' => $comment->comment_post_ID,
					'comment-id' => $comment->comment_ID,
					'context' => 'submit',
				] );
			}
		} );
	}

	public static function settings() {
		return [
			'name' => _x( 'Notify users on new reviews', 'Notifications', 'my-listing' ),
			'description' => _x( 'Send an email to the listing owner whenever a review gets submitted or updated.', 'Notifications', 'my-listing' ),
		];
	}

	/**
	 * Validate and prepare notifcation arguments.
	 *
	 * @since 2.1
	 */
	public function prepare( $args ) {
		$listing = \MyListing\Src\Listing::force_get( $args['listing-id'] );
		$comment = get_comment( $args['comment-id'] );
		$context = $args['context'] === 'update' ? 'update' : 'submit';

		if ( ! ( $listing && $listing->get_author() && $comment && $comment->comment_approved === '1' ) ) {
			throw new \Exception( 'Invalid arguments supplied.' );
		}

		if ( get_comment_meta( $comment->comment_ID, '_mylisting_notification_sent', true ) === 'yes' ) {
			throw new \Exception( 'Notification already sent.' );
		}

		update_comment_meta( $comment->comment_ID, '_mylisting_notification_sent', 'yes' );

		$this->listing = $listing;
		$this->author = $listing->get_author();
		$this->comment = $comment;
		$this->context = $context;
	}

	public function get_mailto() {
		return $this->author->user_email;
	}

	public function get_subject() {
		return $this->context === 'update'
			? sprintf( _x( 'A review has been updated on your listing "%s"', 'Notifications', 'my-listing' ), esc_html( $this->listing->get_name() ) )
			: sprintf( _x( 'A new review has been submitted on your listing "%s"', 'Notifications', 'my-listing' ), esc_html( $this->listing->get_name() ) );
	}

	public function get_message() {
		$template = new Notification_Template;

		$template->add_paragraph( sprintf(
			_x( 'Hi %s,', 'Notifications', 'my-listing' ),
			esc_html( $this->author->first_name )
		) );

		if ( $this->context === 'update' ) {
			$template->add_paragraph( sprintf(
				_x( '<strong>%s</strong> updated their review on <strong>%s</strong>:', 'Notifications', 'my-listing' ),
				esc_html( $this->comment->comment_author ),
				esc_html( $this->listing->get_name() )
			) );
		} else {
			$template->add_paragraph( sprintf(
				_x( '<strong>%s</strong> submitted a new review on <strong>%s</strong>:', 'Notifications', 'my-listing' ),
				esc_html( $this->comment->comment_author ),
				esc_html( $this->listing->get_name() )
			) );
		}

		$rating = get_comment_meta( $this->comment->comment_ID, '_case27_post_rating', true );
		if ( ! empty( $rating && $rating >= 0 && $rating <= 10 ) ) {
			$template->add_paragraph( str_repeat( '&#11088', round( $rating / 2 ) ) . ' ('. round( $rating / 2, 1 ) .'/5)' );
		}

		$template->add_content( sprintf(
			'<div style="font-style:italic;">%s</div>',
			wpautop( wp_kses( $this->comment->comment_content, [] ) )
		) );

		$template->add_break()->add_primary_button(
			_x( 'Open Listing', 'Notifications', 'my-listing' ),
			esc_url( $this->listing->get_link() )
		);

		return $template->get_body();
	}

}