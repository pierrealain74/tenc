<?php

namespace MyListing\Src\Promotions;

if ( ! defined('ABSPATH') ) {
	exit;
}

function activate_package( $package_id, $listing_id = false ) {
	$package = get_post( $package_id );
	if ( ! ( $package && $package->post_type === 'cts_promo_package' ) ) {
		return false;
	}

	// if no listing id has been provided, see if there's one present in the package meta.
	if ( ! $listing_id ) {
		$listing_id = get_post_meta( $package_id, '_listing_id', true );
	}

	$listing = \MyListing\Src\Listing::get( $listing_id );
	if ( ! $listing ) {
		return false;
	}

	// add package info to listing
	update_post_meta( $listing->get_id(), '_promo_package_id', $package_id );

	// add listing info to package
	update_post_meta( $package_id, '_listing_id', $listing->get_id() );

	// save current listing priority, for when the promotion package expires
	if ( $current_priority = get_post_meta( $listing->get_id(), '_featured', true ) ) {
		update_post_meta( $listing->get_id(), '_promo_package_old_priority', absint( $current_priority ) );
	}

	// get package priority, with a default value in case it's missing
	if ( ! ( $priority = absint( get_post_meta( $package_id, '_priority', true ) ) ) ) {
		$priority = 2;
	}

	// set new listing priority
	update_post_meta( $listing->get_id(), '_featured', $priority );

	// calculate promotion expiry date
	$expires  = '';
	$duration = absint( get_post_meta( $package_id, '_duration', true ) );

	if ( $duration ) {
		$expires = date( 'Y-m-d H:i:s', strtotime(
			sprintf( '+%s days', $duration ), current_time( 'timestamp' )
		) );
	}

	// update package status to active (published), and set it's expiry date.
	wp_update_post( [
		'ID' => $package->ID,
		'post_status' => 'publish',
		'meta_input' => [
			'_expires' => $expires,
		],
	] );

	do_action( 'mylisting/promotion:start', $listing->get_id(), $package_id );
	return true;
}

function expire_package( $package_id ) {
	$listing_id = get_post_meta( $package_id, '_listing_id', true );
	if ( $listing_id ) {
		// re-assign old listing priority, the one it had before promotion
		if ( $listing_old_priority = get_post_meta( $listing_id, '_promo_package_old_priority', true ) ) {
			update_post_meta( $listing_id, '_featured', absint( $listing_old_priority ) );
		} else {
			delete_post_meta( $listing_id, '_featured' );
		}

		// delete other promotion data from listing meta
		delete_post_meta( $listing_id, '_promo_package_id' );
		delete_post_meta( $listing_id, '_promo_package_old_priority' );
	}

	// delete package
	wp_trash_post( $package_id );
	do_action( 'mylisting/promotion:end', $listing_id, $package_id );
}

/**
 * Get all "Promotion Package" products.
 *
 * @since 1.7
 */
function get_products() {
	static $packages;
	if ( ! is_null( $packages ) ) {
		return $packages;
	}

	$packages = wc_get_products( [
		'post_type'        => 'product',
		'posts_per_page'   => -1,
		'order'            => 'ASC',
		'orderby'          => 'meta_value_num',
		'meta_key'         => '_price',
		'suppress_filters' => false,
		'tax_query'        => [
			'relation' => 'AND',
			[
				'taxonomy' => 'product_type',
				'field'    => 'slug',
				'terms'    => [ 'promotion_package' ],
				'operator' => 'IN',
			],
		],
	] );

	return $packages;
}

/**
 * Get promotion packages belonging to current user.
 *
 * @since 1.7
 */
function get_available_packages_for_current_user() {
	$packages = get_posts( [
		'post_type' => 'cts_promo_package',
		'post_status' => 'publish',
		'posts_per_page' => -1,
		'meta_query' => [
			[
				'key' => '_user_id',
				'value' => get_current_user_id(),
			],
			[
		        'relation' => 'OR',
		        [ 'key' => '_listing_id', 'value' => '' ],
		        [ 'key' => '_listing_id', 'compare' => 'NOT EXISTS' ],
			]
		],
	] );

	if ( is_wp_error( $packages ) ) {
		return [];
	}

	return $packages;
}

/**
 * Get a promotion package by its ID.
 *
 * @since 1.7
 */
function get_package( $package_id ) {
	$packages = get_posts( [
		'post_type' => 'cts_promo_package',
		'post_status' => 'publish',
		'posts_per_page' => 1,
		'post__in' => [ absint( $package_id ) ],
	] );

	return ! empty( $packages ) ? reset( $packages ) : false;
}

/**
 * Check whether the given listing is promoted, and
 * return the its promotion package if found.
 *
 * @since  1.7
 */
function get_listing_package( $listing_id ) {
	if ( ! ( $listing = \MyListing\Src\Listing::get( $listing_id ) ) ) {
		return false;
	}

	if ( ! ( $package_id = $listing->get_data( '_promo_package_id' ) ) ) {
		return false;
	}

	$packages = get_posts( [
		'post_type' => 'cts_promo_package',
		'post_status' => 'publish',
		'posts_per_page' => 1,
		'post__in' => [ absint( $package_id ) ],
		'meta_query' => [ [
			'key' => '_listing_id',
			'value' => $listing_id,
		] ]
	] );

	return ! empty( $packages ) ? reset( $packages ) : false;
}

function buy_product( $product_id, $listing_id ) {
	$product = wc_get_product( absint( $product_id ) );

	// validate product
	if ( ! ( $product && $product->is_type( 'promotion_package' ) && $product->is_purchasable() ) ) {
    	throw new \Exception( __( 'Could not process request.', 'my-listing' ) );
	}

	// remove old promotion packages for this listing from the cart, if any
	if ( is_array( WC()->cart->cart_contents ) ) {
		foreach ( WC()->cart->cart_contents as $cart_item_key => $cart_item ) {
			if ( empty( $cart_item['listing_id'] ) || empty( $cart_item['data'] ) ) {
				continue;
			}

			if ( $cart_item['data']->get_type() !== 'promotion_package' ) {
				continue;
			}

			// Remove promotion package if it belongs to the listing currently being promoted.
			if ( absint( $cart_item['listing_id'] ) === absint( $listing_id ) ) {
				WC()->cart->remove_cart_item( $cart_item_key );
			}
		}
	}

	// add product to cart with listing_id provided in the cart item data
	WC()->cart->add_to_cart( $product->get_id(), 1, '', '', [
		'listing_id' => $listing_id,
	] );
}

function get_package_edit_link( $package_id ) {
	$base_url = wc_get_account_endpoint_url( \MyListing\promotions_endpoint_slug() );
	return add_query_arg( 'package', absint( $package_id ), $base_url );
}
