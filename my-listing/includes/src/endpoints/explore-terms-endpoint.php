<?php

namespace MyListing\Src\Endpoints;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Explore_Terms_Endpoint {

	public function __construct() {
		add_action( 'mylisting_ajax_explore_terms', [ $this, 'handle' ] );
		add_action( 'mylisting_ajax_nopriv_explore_terms', [ $this, 'handle' ] );
	}

	/**
	 * Retrieve a list of terms with the given args.
	 * For use in Explore page categories/terms tabs.
	 *
	 * @since 2.1
	 */
	public function handle() {
		if ( apply_filters( 'mylisting/ajax-get-request-security-check', false ) === true ) {
			check_ajax_referer( 'c27_ajax_nonce', 'security' );
		}

		try {
			// Validate taxonomy.
			if ( empty( $_REQUEST['taxonomy'] ) || ! isset( $_REQUEST['parent_id'] ) || ! isset( $_REQUEST['type_id'] ) ) {
				throw new \Exception( _x( 'Invalid request.', 'Explore terms', 'my-listing' ) );
			}

			$taxonomy = get_taxonomy( $_REQUEST['taxonomy'] );
			$page = ! empty( $_REQUEST['page'] ) ? ( absint( $_REQUEST['page'] ) ) : 0;
			$per_page = ! empty( $_REQUEST['per_page'] ) ? absint( $_REQUEST['per_page'] ) : 10;
			$type_id = ! empty( $_REQUEST['type_id'] ) ? absint( $_REQUEST['type_id'] ) : 0;
			$hide_empty = ! empty( $_REQUEST['hide_empty'] ) ? $_REQUEST['hide_empty'] === 'yes' : false;
			$orderby = ! empty( $_REQUEST['orderby'] ) ? sanitize_text_field( $_REQUEST['orderby'] ) : 'name';
			$order = ! empty( $_REQUEST['order'] ) ? sanitize_text_field( $_REQUEST['order'] ) : 'ASC';

			$parent_id = ! empty( $_REQUEST['parent_id'] ) ? absint( $_REQUEST['parent_id'] ) : 0;
			if ( ! empty( $_REQUEST['hierarchical'] ) && $_REQUEST['hierarchical'] === 'no' ) {
				$parent_id = null;
			}

			if ( ! ( $taxonomy && $taxonomy->publicly_queryable ) ) {
				throw new \Exception( _x( 'There was an error.', 'Explore terms', 'my-listing' ) );
			}

			$args = [
				'taxonomy' => $taxonomy->name,
				'hide_empty' => $hide_empty,
				'number' => $per_page,
				'offset' => $page * $per_page,
				'orderby' => $orderby,
				'order' => $order,
				'listing_type' => $type_id,
				'parent' => $parent_id,
			];

			$terms = $this->get_terms( $args );
			$parent = $parent_id ? \MyListing\Src\Term::get( $parent_id ) : false;
			wp_send_json( [
				'success' => true,
				'details' => $parent ? $this->format_term( $parent ) : false,
				'children' => $terms,
				'more' => count( $terms ) === $per_page,
			] );
		} catch ( \Exception $e ) {
			wp_send_json( [
				'success' => false,
				'message' => $e->getMessage(),
			] );
		}
	}

	public function get_terms( $args = [] ) {
		$args = c27()->merge_options( [
			'taxonomy' => '',
			'orderby' => 'name',
			'order' => 'ASC',
			'hide_empty' => false,
			'listing_type' => '',
			'number' => 0,
			'offset' => 0,
			'parent' => null,
		], $args );

		$query_args = [
			'taxonomy' => $args['taxonomy'],
			'orderby' => $args['orderby'],
			'order' => $args['order'],
			'hide_empty' => $args['hide_empty'],
			'number' => $args['number'],
			'offset' => $args['offset'],
		];

		if ( $args['parent'] !== null ) {
			$query_args['parent'] = $args['parent'];
		}

		// Filter by listing type.
		if ( ! empty( $args['listing_type'] ) ) {
			$query_args['meta_query'][] = [
				'relation' => 'OR',
				[ 'key' => 'listing_type', 'value' => '"' . $args['listing_type'] . '"', 'compare' => 'LIKE' ],
				[ 'key' => 'listing_type', 'value' => '' ],
				[ 'key' => 'listing_type', 'compare' => 'NOT EXISTS' ],
			];
		}

		// If available, fetch terms from cache.
		$cache_version = \MyListing\get_taxonomy_versions( $args['taxonomy'] );
		$terms_hash = sprintf( 'explore_terms_%s_v%s', md5( json_encode( $query_args ) ), $cache_version );
		$terms = get_transient( $terms_hash );

		// Otherwise, query.
		if ( empty( $terms ) ) {
		    $terms = get_terms( $query_args );
		    set_transient( $terms_hash, $terms, HOUR_IN_SECONDS * 6 );
		}

		if ( is_wp_error( $terms ) ) {
			return [];
		}

		$items = [];
		foreach ( $terms as $term ) {
			if ( ! ( $term = \MyListing\Src\Term::get( $term ) ) ) {
				continue;
			}
			$items[ 'term_'.$term->get_id() ] = $this->format_term( $term );
		}

		return $items;
	}

	private function format_term( $term ) {
		$background = $term->get_image();
		return [
			'term_id' => $term->get_id(),
			'name' => $term->get_name(),
			'description' => $term->get_description(),
			'slug' => $term->get_slug(),
			'link' => $term->get_link(),
			'parent' => $term->get_parent_id(),
			'count' => $term->get_count(),
			'single_icon' => $term->get_icon( [ 'background' => false, 'color' => true ] ),
			'color' => $term->get_color(),
			'icon' => $term->get_icon( [ 'background' => false, 'color' => false ] ),
			'background' => is_array( $background ) && ! empty( $background ) ? $background['sizes']['large'] : false,
			'listing_types' => array_filter( array_map( 'absint', (array) get_term_meta( $term->get_id(), 'listing_type', true ) ) ),
		];
	}
}