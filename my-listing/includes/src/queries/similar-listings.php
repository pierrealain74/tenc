<?php

namespace MyListing\Src\Queries;

class Similar_Listings extends Query {
    use \MyListing\Src\Traits\Instantiatable;

    public $action = null;

    public function run( $listing_id ) {
        global $wpdb;

        if ( ! ( $listing = \MyListing\Src\Listing::get( $listing_id ) ) || ! $listing->type ) {
            return null;
        }

        $settings = $listing->type->get_layout()['similar_listings'];

        $query_args = [
            'orderby'        => 'date',
            'order'          => 'DESC',
            'posts_per_page' => $settings['listing_count'],
            'tax_query'      => [],
            'meta_query'     => [],
            'post__not_in' => [ $listing->get_id() ], // exclude current listing.
            'fields' => 'ids',
        ];

        // If set to match by type, similar listings must belong to the same listing type.
        if ( (bool) $settings['match_by_type'] === true ) {
            $query_args['meta_query']['listing_type_query'] = [
                'key'     => '_case27_listing_type',
                'value'   =>  $listing->type->get_slug(),
                'compare' => '=',
            ];
        }

        // Filter by 'category' taxonomy.
        if ( (bool) $settings['match_by_category'] === true && ( $categories = (array) $listing->get_field( 'category' ) ) ) {
            $query_args['tax_query'][] = [
                'taxonomy' => 'job_listing_category',
                'field' => 'term_id',
                'terms' => array_filter( array_map( function( $term ) {
                    return $term instanceof \WP_Term ? $term->term_id : null;
                }, $categories ) ),
            ];
        }

        // Filter by 'region' taxonomy.
        if ( (bool) $settings['match_by_region'] === true && ( $regions = (array) $listing->get_field( 'region' ) ) ) {
            $query_args['tax_query'][] = [
                'taxonomy' => 'region',
                'field' => 'term_id',
                'terms' => array_filter( array_map( function( $term ) {
                    return $term instanceof \WP_Term ? $term->term_id : null;
                }, $regions ) ),
            ];
        }

        // Filter by 'tags' taxonomy.
        if ( (bool) $settings['match_by_tags'] === true && ( $tags = (array) $listing->get_field( 'tags' ) ) ) {
            $query_args['tax_query'][] = [
                'taxonomy' => 'case27_job_listing_tags',
                'field' => 'term_id',
                'terms' => array_filter( array_map( function( $term ) {
                    return $term instanceof \WP_Term ? $term->term_id : null;
                }, $tags ) ),
            ];
        }

        // Handle orderby proximity.
        if ( $settings['orderby'] === 'proximity' && ( $latitude = $listing->get_data( 'geolocation_lat' ) ) && ( $longitude = $listing->get_data( 'geolocation_long' ) ) ) {
            $earth_radius = 6371; // km

            // Get listing id's in proximity.
            $sql = $wpdb->prepare( \MyListing\Helpers::get_proximity_sql(), $earth_radius, $latitude, $longitude, $latitude, $settings['max_proximity'] );
            $post_ids = (array) $wpdb->get_results( $sql, OBJECT_K );

            // Exclude the current listing id
            if ( isset( $post_ids[ $listing->get_id() ] ) ) {
                unset( $post_ids[ $listing->get_id()] );
            }

            if ( empty( $post_ids ) ) {
                $post_ids = ['none'];
            }

            $query_args['post__in'] = array_keys( (array) $post_ids );
            $query_args['orderby'] = 'post__in';

            // Ignore priority for this ordering option.
            $query_args['mylisting_ignore_priority'] = true;
        }

        // Handle orderby rating.
        if ( $settings['orderby'] === 'rating' ) {
            // Add rating order.
            add_filter( 'posts_join', [ $this, 'rating_field_join' ], 35, 2 );
            add_filter( 'posts_orderby', [ $this, 'rating_field_orderby' ], 35, 2 );
            $query_args['mylisting_orderby_rating'] = true; // Note the custom order to $query_args, so it's cached properly.

            // Ignore priority for this ordering option.
            $query_args['mylisting_ignore_priority'] = true;
        }

        // Handle random order
        if ( $settings['orderby'] === 'random' ) {
            // randomize every 3 hours
            $seed = apply_filters( 'mylisting/similar-listings/random-order/seed', floor( time() / 10800 ) );
            $query_args['orderby'] = sprintf( 'RAND(%d)', $seed );

            if ( apply_filters( 'mylisting/similar-listings/random-order/ignore-priority', false ) === true ) {
                $query_args['mylisting_ignore_priority'] = true;
            }
        }

        // hide priority badge if ignore_priority is enabled
        if ( isset( $query_args['mylisting_ignore_priority'] ) && $query_args['mylisting_ignore_priority'] === true ) {
            add_filter( 'mylisting/similar-listings/wrapper-class', function( $wrap ) {
                $wrap .= ' hide-priority';
                return $wrap;
            } );
        }

        $results = [];
        $result['found_jobs'] = false;

        $query_args = apply_filters( 'mylisting/similar-listings/args', $query_args );

        $listings_query = $this->query( $query_args );

        return $listings_query;
    }
}