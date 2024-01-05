<?php

namespace MyListing;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Update listings counts for the given term.
 *
 * @since 2.2.3
 */
function update_term_counts( $term_id, $taxonomy ) {
	global $wpdb;

	// get all child terms
	$ids = get_term_children( $term_id, $taxonomy );
	if ( is_wp_error( $ids ) ) {
		return;
	}

	// append the term id to it's child terms, and concatenate to use in db query
	$ids[] = $term_id;
	$ids = join( ',', $ids );

	$results = $wpdb->get_results( "
        SELECT mt1.meta_value AS listing_type, COUNT(DISTINCT {$wpdb->posts}.ID) AS count FROM {$wpdb->posts}
        LEFT JOIN {$wpdb->postmeta} AS mt1 ON ( mt1.meta_key = '_case27_listing_type' AND {$wpdb->posts}.ID = mt1.post_id )
        LEFT JOIN {$wpdb->term_relationships} AS tr ON ( {$wpdb->posts}.ID = tr.object_id )
        LEFT JOIN {$wpdb->term_taxonomy} AS tt ON ( tr.term_taxonomy_id = tt.term_taxonomy_id )
        WHERE post_type = 'job_listing'
            AND post_status = 'publish'
            AND tt.term_id IN({$ids})
        GROUP BY mt1.meta_value
	", ARRAY_A );

	$counts = [];
	foreach ( $results as $group ) {
		if ( ! is_numeric( $group['count'] ) || $group['count'] < 1 ) {
			continue;
		}

		if ( ! isset( $counts[ $group['listing_type'] ] ) ) {
			$counts[ $group['listing_type'] ] = 0;
		}

		$counts[ $group['listing_type'] ] += $group['count'];
	}

	if ( ! empty( $counts ) ) {
		update_term_meta( $term_id, 'listings_full_count', wp_json_encode( $counts ) );
	} else {
		delete_term_meta( $term_id, 'listings_full_count' );
	}
}

/**
 * Wrapper function around WordPress' `get_terms`, to make filtering by
 * listing types, setting the return key and value, hierarchical
 * order, caching, etc. easier.
 *
 * @since 2.4.5
 */
function get_terms( $args ) {
	$args = array_replace_recursive( $defaults = [
		'taxonomy' => '',
		'orderby' => 'name',
		'order' => 'ASC',
		'hide_empty' => false,
		'hierarchical' => false,
		'number' => 0,
		'offset' => 0,
		'search' => '',
		'meta_query' => [],
		'parent' => null,
		// listing type id
		'listing_type' => null,
		// slug or term_id (to be used as the return key)
		'return_key' => 'term_id',
		// name or wp_term object
		'return_value' => 'name',
		// amount of time (in seconds) to cache results for; 0 = no cache
		'cache_time' => 0,
	], $args );

	$args = apply_filters( 'mylisting/get-terms/args', $args );

	// validate taxonomy
	if ( empty( $args['taxonomy'] || ! taxonomy_exists( $args['taxonomy'] ) ) ) {
		return [];
	}

	if ( ! in_array( $args['return_key'], [ 'slug', 'term_id' ], true ) ) {
		$args['return_key'] = 'term_id';
	}

	if ( ! in_array( $args['return_value'], [ 'name', 'wp_term' ], true ) ) {
		$args['return_value'] = 'name';
	}

	$query_args = [
		'taxonomy' => $args['taxonomy'],
		'orderby' => $args['orderby'],
		'order' => $args['order'],
		'hide_empty' => $args['hide_empty'],
		'number' => $args['number'],
		'offset' => $args['offset'],
		'meta_query' => $args['meta_query'],
	];

	// filter by keyword
	if ( ! empty( $args['search'] ) ) {
		$query_args['search'] = $args['search'];
	}

	// limit to a parent term
	if ( $args['parent'] !== null ) {
		$query_args['parent'] = $args['parent'];
	}

	// filter by listing type
	if ( ! empty( $args['listing_type'] ) ) {
		$type_matcher = '"'.$args['listing_type'].'"';
		$query_args['meta_query'][] = [
			'relation' => 'OR',
			[ 'key' => 'listing_type', 'value' => $type_matcher, 'compare' => 'LIKE' ],
			[ 'key' => 'listing_type', 'value' => '' ],
			[ 'key' => 'listing_type', 'compare' => 'NOT EXISTS' ],
		];
	}

	$query_args = apply_filters( 'mylisting/get-terms/query-args', $query_args, $args );

	// try to retrieve from cache
	if ( $args['cache_time'] > 0 ) {
		$cache_version = \MyListing\get_taxonomy_versions( $args['taxonomy'] );
		$cache_key = sprintf( 'mylisting_termcache_%s', md5( json_encode( $query_args ) ) );

		$cached_terms = get_transient( $cache_key );
		$cached_terms_version = isset( $cached_terms['_v'] ) ? absint( $cached_terms['_v'] ) : null;
		if ( ! empty( $cached_terms ) && $cache_version === $cached_terms_version ) {
			unset( $cached_terms['_v'] );
			return $cached_terms;
		}
	}

	// query terms
    $terms = \get_terms( $query_args );
    if ( is_wp_error( $terms ) ) {
    	return [];
    }

    // order items hierarchically if requested; this will only work if args.number is
    // set to 0, as all terms are required for hierarchical ordering to be possible
	if ( $args['hierarchical'] === true ) {
		$items = [];
		$term_tree = \MyListing\get_term_tree( $terms );
		\MyListing\iterate_terms_recursively( function( $term, $depth ) use ( &$items, $args ) {
	    	$term->_depth = $depth;
	    	$key = $term->{$args['return_key']};
	    	$value = $args['return_value'] === 'wp_term'
	    		? $term
	    		: str_repeat( '&mdash; ', $depth - 1 ) . $term->name;

	       	$items[ $key ] = $value;
	    }, ! empty( $term_tree ) ? $term_tree : $terms );

	// otherwise, return in matched order
	} else {
		$items = [];
		foreach ( $terms as $term ) {
	    	$key = $term->{$args['return_key']};
	    	$value = $args['return_value'] === 'wp_term' ? $term : $term->name;
			$items[ $key ] = $value;
		}
	}

	// cache results if requested
	if ( $args['cache_time'] > 0 ) {
		$items['_v'] = $cache_version;
		set_transient( $cache_key, $items, absint( $args['cache_time'] ) );
		unset( $items['_v'] );
	}

	return $items;
}

/**
 * Construct a hierarchical term tree from a flat terms array.
 *
 * @since 1.0
 */
function get_term_tree( $terms = [], $parent = 0 ) {
	$result = [];
	foreach ( $terms as $term ) {
		if ( $parent == $term->parent ) {
			$term->children = \MyListing\get_term_tree( $terms, $term->term_id );
			$result[] = $term;
		}
	}

	return $result;
}

/**
 * Iterate a term tree recursively, passing the term object and
 * depth as callback params.
 *
 * @since 1.0
 */
function iterate_terms_recursively( $callback, $terms, $depth = 0 ) {
	$depth++;
	foreach ( $terms as $term ) {
		$callback( $term, $depth );
		if ( ! empty( $term->children ) && is_array( $term->children ) ) {
			\MyListing\iterate_terms_recursively( $callback, $term->children, $depth );
		}
	}
}
