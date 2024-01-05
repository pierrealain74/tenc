<?php

namespace MyListing\Src\Endpoints;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Post_Duplicate_Endpoint {

	public function __construct() {
		add_action( 'mylisting_ajax_post_duplicate', [ $this, 'handle' ] );
	}

	public function handle() {
		mylisting_check_ajax_referrer();

		$listing_id = $_POST['listing_id'] ?? null;
		$listing = \MyListing\Src\Listing::get( $listing_id );

		if ( ! ( $listing && $listing->get_author_id() === get_current_user_id() ) ) {
			return;
		}

		$new_listing_id = \MyListing\duplicate_listing( $listing_id );
		if ( ! is_null( $new_listing_id ) ) {
			wp_update_post( [
				'ID' => $new_listing_id,
				'post_status' => apply_filters( 'mylisting/frontend/duplicate-listing-status', 'preview', $new_listing_id, $listing->get_id() ),
			] );

			$add_listing_page = c27()->get_setting( 'general_add_listing_page' );
			$resume_url = add_query_arg( [
				'listing_type' => $listing->type->get_slug(),
				'job_id' => $new_listing_id,
			], $add_listing_page );

			return wp_send_json( [
				'success' => true,
				'redirect' => $resume_url,
			] );
		}
	}
}