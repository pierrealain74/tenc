<?php

namespace MyListing\Src\Endpoints;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Post_Details_Endpoint {

	public function __construct() {
		add_action( 'mylisting_ajax_post_details', [ $this, 'handle' ] );
	}

	public function handle() {
		mylisting_check_ajax_referrer();

		// validate key
		$key = isset( $_REQUEST['item_key'] ) ? $_REQUEST['item_key'] : null;
		if ( empty( $key ) || ! in_array( $key, ['ID', 'post_name'], true ) ) {
			return;
		}

		// validate post type
		$post_type = isset( $_REQUEST['post_type'] ) ? $_REQUEST['post_type'] : null;
		$allowed_post_types = [ 'job_listing', 'post', 'page', 'case27_listing_type' ];
		if ( empty( $post_type ) || ! in_array( $post_type, $allowed_post_types, true ) ) {
			return;
		}

		// validate requested posts
		$posts = isset( $_REQUEST['posts'] ) ? (array) $_REQUEST['posts'] : [];
		if ( $key === 'ID' ) {
			$posts = array_filter( array_map( 'absint', $posts ) );
		} else {
			$posts = array_filter( array_map( 'sanitize_title_for_query', $posts ) );
		}

		if ( empty( $posts ) ) {
			return;
		}

		global $wpdb;

		$imploded_keys = "'" . join( "','", $posts ) . "'";
		$results = $wpdb->get_results( $wpdb->prepare( "
			SELECT {$key}, post_title FROM {$wpdb->posts}
			WHERE post_type = %s AND post_status = 'publish'
			AND {$key} IN ({$imploded_keys})
			ORDER BY post_title ASC
		", $post_type ) );

		$output = [];
		if ( is_array( $results ) && ! empty( $results ) ) {
			foreach ( $results as $result ) {
				$output[ $result->$key ] = $result->post_title;
			}
		}

		return wp_send_json( [
			'success' => true,
			'results' => $output,
		] );
	}
}