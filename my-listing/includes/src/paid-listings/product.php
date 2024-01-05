<?php

namespace MyListing\Src\Paid_Listings;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Create a custom WooCommerce product type based on Simple Products.
 *
 * @since 1.6
 */
class Product extends \WC_Product_Simple {

	/**
	 * @since 1.6
	 * @param int|WC_Product|object $product Product ID, post object, or product object
	 */
	public function __construct( $product ) {
		$this->product_type = 'job_package';
		parent::__construct( $product );
	}

	/**
	 * Get product type.
	 *
	 * @since 1.6
	 */
	public function get_type() {
		return 'job_package';
	}

	/**
	 * Compatibility function to retrieve product meta.
	 * Simpler than using WC 3 Getter/Setter Method.
	 *
	 * @since 1.6
	 */
	public function get_product_meta( $key ) {
		return $this->get_meta( '_' . $key );
	}

	/**
	 * Listing packages are sold individually.
	 *
	 * @since 1.6
	 */
	public function is_sold_individually() {
		return true;
	}

	/**
	 * Always purchaseable.
	 *
	 * @since 1.6
	 */
	public function is_purchasable() {
		return apply_filters( 'mylisting/paid-listings/product/is-purchasable', true, $this );
	}

	/**
	 * Is a virtual product. No shipping.
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
		return absint( $duration ? $duration : mylisting_get_setting( 'submission_default_duration' ) );
	}

	/**
	 * Return listing limit.
	 *
	 * @since 1.6
	 */
	public function get_limit() {
		// 0 = unlimited
		$limit = $this->get_product_meta( 'job_listing_limit' );
		return absint( $limit ? $limit : 0 );
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
