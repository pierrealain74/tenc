<?php

namespace MyListing\Src\Endpoints;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Package_List_Endpoint {

	public function __construct() {
		add_action( 'mylisting_ajax_mylisting_list_packages', [ $this, 'handle' ] );
	}

	/**
	 * Retrieve a list of user payment packages with the given args.
	 * For use in select/multiselect fields.
	 *
	 * @since 2.1.6
	 */
	public function handle() {
		mylisting_check_ajax_referrer();

		try {
			if ( ! current_user_can( 'edit_pages' ) ) {
				throw new \Exception( _x( 'Invalid request', 'Payment packages dropdown list', 'my-listing' ) );
			}

			$page = ! empty( $_REQUEST['page'] ) ? ( absint( $_REQUEST['page'] ) - 1 ) : 0;
			$author = ! empty( $_REQUEST['cts_author'] ) ? ( absint( $_REQUEST['cts_author'] ) ) : 0;
			$search = ! empty( $_REQUEST['search'] ) ? absint( ltrim( trim( $_REQUEST['search'] ), '#' ) ) : '';
			$per_page = 25;

			$args = [
				'post_type' => 'case27_user_package',
				'post_status' => 'publish',
				'posts_per_page' => $per_page,
				'offset' => $page * $per_page,
				'tax_query' => [],
				'orderby' => 'date',
				'order' => 'DESC',
			];

			if ( ! empty( $author ) ) {
				$args['author'] = $author;
			}

			// search by package id
			if ( ! empty( $search ) ) {
				global $wpdb;
				$post__in = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE ID LIKE %s", '%'.absint( $search ).'%' ) );
				if ( is_array( $post__in ) && ! empty( $post__in ) ) {
					$args['post__in'] = $post__in;
				}
			}


			$posts = get_posts( $args );
			if ( empty( $posts ) || is_wp_error( $posts ) ) {
				throw new \Exception( _x( 'No packages found.', 'Payment packages dropdown list', 'my-listing' ) );
			}

			$results = [];
			foreach ( $posts as $post ) {
				if ( ! ( $package = \MyListing\Src\Package::get( $post ) ) ) {
					continue;
				}

				$text = '<strong>#'.$package->get_id().'</strong>';
				if ( $user = $package->get_user() ) {
					$text .= ' - '.$user->display_name;
				}
				$text .= sprintf( ' &middot; %d/%d', $package->get_count(), $package->get_limit() );
				$results[] = [
					'id' => $post->ID,
					'text' => $text,
				];
			}

			wp_send_json( [
				'success' => true,
				'results' => $results,
				'more' => count( $results ) === $per_page,
				'args' => \MyListing\is_dev_mode() ? $args : [],
			] );
		} catch ( \Exception $e ) {
			wp_send_json( [
				'success' => false,
				'message' => $e->getMessage(),
			] );
		}
	}
}