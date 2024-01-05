<?php

namespace MyListing\Src\Endpoints;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Products_List_Endpoint {

	public function __construct() {
		add_action( 'mylisting_ajax_mylisting_list_products', [ $this, 'handle' ] );
		add_action( 'mylisting_ajax_nopriv_mylisting_list_products', [ $this, 'handle' ] );
	}

	/**
	 * Retrieve a list of products with the given args.
	 * For use in select/multiselect fields.
	 *
	 * @since 2.0
	 */
	public function handle() {
		mylisting_check_ajax_referrer();

		try {
			$page = ! empty( $_REQUEST['page'] ) ? ( absint( $_REQUEST['page'] ) - 1 ) : 0;
			$author = ! empty( $_REQUEST['cts_author'] ) ? ( absint( $_REQUEST['cts_author'] ) ) : 0;
			$search = ! empty( $_REQUEST['search'] ) ? sanitize_text_field( $_REQUEST['search'] ) : '';
			$product_type = ! empty( $_REQUEST['product-type'] ) ? $_REQUEST['product-type'] : false;
			$per_page = apply_filters( 'mylisting/queries/posts-list/items-per-page', 25 );

			$args = [
				'post_type' => 'product',
				'post_status' => 'publish',
				'posts_per_page' => $per_page,
				'offset' => $page * $per_page,
				'tax_query' => [],
				'orderby' => 'name',
				'order' => 'ASC',
			];

			if ( is_array( $product_type ) && ! empty( $product_type ) ) {
				$args['tax_query'][] = [
		           'taxonomy' => 'product_type',
		           'field' => 'slug',
		           'terms' => $product_type,
		           'operator' => 'IN',
			    ];
			}

			if ( ! empty( $author ) ) {
				$args['author'] = $author;
			}

			if ( ! empty( trim( $search ) ) ) {
				$args['s'] = trim( $search );
			}

			$posts = get_posts( $args );
			if ( empty( $posts ) || is_wp_error( $posts ) ) {
				throw new \Exception( _x( 'No products found.', 'Products dropdown list', 'my-listing' ) );
			}

			$results = [];
			foreach ( $posts as $post ) {
				$results[] = [
					'id' => $post->ID,
					'text' => $post->post_title,
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