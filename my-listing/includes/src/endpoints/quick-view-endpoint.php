<?php

namespace MyListing\Src\Endpoints;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Quick_View_Endpoint {

	public function __construct() {
		add_action( 'mylisting_ajax_get_listing_quick_view', [ $this, 'get_quick_view' ] );
		add_action( 'mylisting_ajax_nopriv_get_listing_quick_view', [ $this, 'get_quick_view' ] );
	}

	/**
	 * Retrieve the quick view template for the given listing.
	 *
	 * @since 1.0
	 */
	public function get_quick_view() {
		if ( empty( $_REQUEST['listing_id'] ) ) {
			return;
		}

		$listing = \MyListing\Src\Listing::get( absint( $_REQUEST['listing_id'] ) );
		if ( ! $listing ) {
			return;
		}

		ob_start();

		// Get quick view template.
		mylisting_locate_template( 'templates/single-listing/previews/quick-view.php', compact('listing') );

		// Send response object.
		wp_send_json( [ 'html' => ob_get_clean() ] );
	}
}
