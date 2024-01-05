<?php

namespace MyListing\Src\Endpoints;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Term_List_Endpoint {
	use \MyListing\Src\Traits\Instantiatable;

	public function __construct() {
		add_action( 'mylisting_ajax_mylisting_list_terms', [ $this, 'handle' ] );
		add_action( 'mylisting_ajax_nopriv_mylisting_list_terms', [ $this, 'handle' ] );
	}

	/**
	 * Retrieve a list of terms with the given args.
	 * For use in select/multiselect fields.
	 *
	 * @since 2.0
	 */
	public function handle() {
		if ( apply_filters( 'mylisting/ajax-get-request-security-check', false ) === true ) {
			check_ajax_referer( 'c27_ajax_nonce', 'security' );
		}

		try {
			// Validate taxonomy.
			if ( empty( $_REQUEST['taxonomy'] ) ) {
				throw new \Exception( _x( 'Invalid request.', 'Term dropdown list', 'my-listing' ) );
			}

			$taxonomy = get_taxonomy( $_REQUEST['taxonomy'] );
			$page = ! empty( $_REQUEST['page'] ) ? ( absint( $_REQUEST['page'] ) - 1 ) : 0;
			$search = ! empty( $_REQUEST['search'] ) ? sanitize_text_field( stripslashes( $_REQUEST['search'] ) ) : '';
			$type_id = ! empty( $_REQUEST['listing-type-id'] ) ? absint( $_REQUEST['listing-type-id'] ) : 0;
			$hide_empty = ! empty( $_REQUEST['hide_empty'] ) ? $_REQUEST['hide_empty'] === 'yes' : false;
			$return_key = ! empty( $_REQUEST['term-value'] ) && $_REQUEST['term-value'] === 'slug' ? 'slug' : 'term_id';

			$orderby = ! empty( $_REQUEST['orderby'] ) ? $_REQUEST['orderby'] : 'name';
			$order = ! empty( $_REQUEST['order'] ) ? $_REQUEST['order'] : 'ASC';

			// validate taxonomy
			if ( ! ( $taxonomy && $taxonomy->publicly_queryable ) ) {
				throw new \Exception( _x( 'There was an error.', 'Term dropdown list', 'my-listing' ) );
			}

			// `parent` request param is used to get child terms based on the parent slug
			// `parent_id` request param is used to get child terms based on the id
			$parent_slug = ! empty( $_REQUEST['parent'] ) ? $_REQUEST['parent'] : false;
			$parent_id = ! empty( $_REQUEST['parent_id'] ) ? absint( $_REQUEST['parent_id'] ) : 0;

			// determine parent term
			if ( $parent_id && ( $parent = get_term( $parent_id ) ) && ! is_wp_error( $parent ) ) {
				$parent_term = absint( $parent->term_id );
			} elseif ( $parent_slug && ( $parent = get_term_by( 'slug', $parent_slug, $taxonomy->name ) ) && ! is_wp_error( $parent ) ) {
				$parent_term = absint( $parent->term_id );
			} elseif ( isset( $_REQUEST['parent'] ) && is_numeric( $_REQUEST['parent'] ) && (int) $_REQUEST['parent'] === 0 ) {
				$parent_term = 0;
			} else {
				$parent_term = null;
			}

			$per_page = apply_filters( 'mylisting/queries/term-list/items-per-page', 25, $taxonomy );

			$args = [
				'taxonomy' => $taxonomy->name,
				'hide_empty' => $hide_empty,
				'number' => $per_page,
				'offset' => $page * $per_page,
				'search' => $search,
				'orderby' => $orderby,
				'order' => $order,
				'listing_type' => $type_id,
				'return_key' => $return_key,
				'return_value' => 'name',
				'parent' => $parent_term,

				/**
				 * Hierarchical order is disabled by default since v2.4.4 as it's very slow
				 * with thousands of terms. Can be enabled by adding the following snippets
				 * in a child theme:
				 *
				 * `add_filter( 'mylisting/terms-dropdown/order-hierarchically', '__return_true' );`
				 * `add_filter( 'mylisting/queries/term-list/items-per-page', '__return_zero' );`
				 *
				 * @since 2.4.4
				 */
				'hierarchical' => apply_filters( 'mylisting/terms-dropdown/order-hierarchically', false )
			];

			$terms = \MyListing\get_terms( $args );

			if ( empty( $terms ) || is_wp_error( $terms ) ) {
				throw new \Exception( _x( 'No terms found.', 'Term dropdown list', 'my-listing' ) );
			}

			$results = [];
			foreach ( $terms as $term_id => $term_name ) {
				$results[] = [
					'id' => $term_id,
					'text' => $term_name,
				];
			}

			wp_send_json( [
				'success' => true,
				'results' => $results,
				'more' => count( $results ) === $per_page,
				'info' => \MyListing\is_dev_mode() ? $args : [],
			] );
		} catch ( \Exception $e ) {
			wp_send_json( [
				'success' => false,
				'message' => $e->getMessage(),
			] );
		}
	}
}
