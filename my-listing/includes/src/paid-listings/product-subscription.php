<?php

namespace MyListing\Src\Paid_Listings;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Create a custom subscription based WooCommerce product type
 * based on Simple Subscription.
 *
 * @since 1.6
 */
class Product_Subscription extends \WC_Product_Subscription {

	/**
	 * @since 1.6
	 * @param int|WC_Product|object $product Product ID, post object, or product object
	 */
	public function __construct( $product ) {
		parent::__construct( $product );
		$this->product_type = 'job_package_subscription';
	}

	/**
	 * Compatibility function for `get_id()` method
	 *
	 * @since 1.6
	 */
	public function get_id() {
		return parent::get_id();
	}

	/**
	 * Get product id
	 *
	 * @since 1.6
	 */
	public function get_product_id() {
		return $this->get_id();
	}

	/**
	 * Compatibility function to retrieve product meta.
	 *
	 * @since 1.6
	 */
	public function get_product_meta( $key ) {
		return $this->get_meta( '_' . $key );
	}

	/**
	 * Get product type.
	 *
	 * @since 1.6
	 */
	public function get_type() {
		return 'job_package_subscription';
	}

	/**
	 * Checks the product type.
	 *
	 * Backwards compat with downloadable/virtual.
	 *
	 * @since 1.6
	 * @param mixed $type Array or string of types
	 * @return bool
	 */
	public function is_type( $type ) {
		return ( 'job_package_subscription' === $type || ( is_array( $type ) && in_array( 'job_package_subscription', $type, true ) ) ) ? true : parent::is_type( $type );
	}

	/**
	 * We want to sell listings one at a time
	 *
	 * @since 1.6
	 */
	public function is_sold_individually() {
		return true;
	}

	/**
	 * Get the add to url used mainly in loops.
	 *
	 * @since 1.6
	 */
	public function add_to_cart_url() {
		$url = $this->is_in_stock() ? remove_query_arg( 'added-to-cart', add_query_arg( 'add-to-cart', $this->id ) ) : get_permalink( $this->id );
		return apply_filters( 'woocommerce_product_add_to_cart_url', $url, $this );
	}

	/**
	 * Listings are always virtual
	 *
	 * @since 1.6
	 */
	public function is_virtual() {
		return true;
	}

	/**
	 * Get listing duration configured for this package.
	 *
	 * @since 1.6
	 */
	public function get_duration() {
		$duration = $this->get_product_meta( 'job_listing_duration' );
		if ( 'listing' === $this->get_package_subscription_type() ) {
			return false;
		} elseif ( $duration ) {
			return $duration;
		} else {
			return mylisting_get_setting( 'submission_default_duration' );
		}
	}

	/**
	 * Return listing limit.
	 *
	 * @since 1.6
	 */
	public function get_limit() {
		// 0 = unlimited
		$job_listing_limit = $this->get_product_meta( 'job_listing_limit' );
		if ( $job_listing_limit ) {
			return $job_listing_limit;
		} else {
			return 0;
		}
	}

	/**
	 * Get package subscription type.
	 *
	 * @since 1.6
	 */
	public function get_package_subscription_type() {
		return $this->get_product_meta( 'package_subscription_type' );
	}

	/**
	 * Should listings with this package be marked as featured.
	 *
	 * @since 1.6
	 */
	public function is_listing_featured() {
		return $this->get_product_meta( 'job_listing_featured' ) === 'yes';
	}

	/**
	 * Should listings with this package be marked as verified.
	 *
	 * @since 2.1.6
	 */
	public function mark_verified() {
		return $this->get_product_meta( 'listing_mark_verified' ) === 'yes';
	}

	/**
	 * Is this package usable for claims.
	 *
	 * @since 1.6
	 */
	public function use_for_claims() {
		return $this->get_product_meta( 'use_for_claims' ) === 'yes';
	}
}
