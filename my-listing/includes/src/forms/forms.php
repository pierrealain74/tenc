<?php

namespace MyListing\Src\Forms;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Forms {

	public static function boot() {
		new self;
	}

	public function __construct() {
		// load posted form class for processing
		add_action( 'init', [ $this, 'load_posted_form' ] );
		add_filter( 'woocommerce_my_account_my_orders_query', [ $this, 'dashboard_orders_query' ] );
	}

	public function load_posted_form() {
		if ( ! empty( $_POST['job_manager_form'] ) ) {
			$form = $_POST['job_manager_form'];

			if ( $form === 'submit-listing' ) {
				Add_Listing_Form::instance();
			}

			if ( $form === 'edit-listing' ) {
				Edit_Listing_Form::instance();
			}
		}
	}

	public function dashboard_orders_query( $args ) {
		if ( ! empty( $_GET['show_pending'] ) ) {
			$args['status'] = [ 'wc-pending', 'wc-on-hold' ];
		}

		if ( ! empty( $_GET['order_in'] ) ) {
			$args['post__in'] = array_map( 'absint', (array) $_GET['order_in'] );
		}

		return $args;
	}

}
