<?php
/**
 * Payment Package object.
 *
 * @since 1.6
 */

namespace MyListing\Src;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Package {

	public static $instances = [];

	private
		$package, // case27_user_package
		$product, // wc_product
		$order,   // wc_order
		$user;

	/**
	 * Get a new package instance (Multiton pattern). When called the first time,
	 * package will be fetched from database. Otherwise, it will return the previous instance.
	 *
	 * @since 2.1.6
	 * @param $package int or \WP_Post
	 */
	public static function get( $package ) {
		if ( is_numeric( $package ) ) {
			$package = get_post( $package );
		}

		if ( ! $package instanceof \WP_Post ) {
			return false;
		}

		if ( $package->post_type !== 'case27_user_package' ) {
			return false;
		}

		if ( ! array_key_exists( $package->ID, self::$instances ) ) {
			self::$instances[ $package->ID ] = new self( $package );
		}

		return self::$instances[ $package->ID ];
	}

	/**
	 * Create a new payment package, and return the package wrapper
	 * object on success, or false on failure.
	 *
	 * @since 2.1.6
	 */
	public static function create( $args = [] ) {
		$args = wp_parse_args( $args, [
			'user_id'           => get_current_user_id(),
			'product_id'        => false,
			'order_id'          => false,
			'featured'          => false,
			'limit'             => false,
			'count'             => false,
			'duration'          => false,
			'mark_verified'     => false,
			'use_for_claims'    => false,
		] );

		$package_id = wp_insert_post( [
			'post_type'   => 'case27_user_package',
			'post_status' => 'publish',
			'meta_input'  => [
				'_user_id'        => $args['user_id'] ? absint( $args['user_id'] ) : '',
				'_product_id'     => $args['product_id'] ? absint( $args['product_id'] ) : '',
				'_order_id'       => $args['order_id'] ? absint( $args['order_id'] ) : '',
				'_featured'       => $args['featured'] ? 1 : '',
				'_mark_verified'  => $args['mark_verified'] ? 1 : '',
				'_use_for_claims' => $args['use_for_claims'] ? 1 : '',
				'_limit'          => $args['limit'] ? absint( $args['limit'] ) : '',
				'_count'          => $args['count'] ? absint( $args['limit'] ) : '',
				'_duration'       => $args['duration'] ? absint( $args['duration'] ) : '',
			],
		] );

		if ( ! $package_id || is_wp_error( $package_id ) ) {
			return false;
		}

		return static::get( $package_id );
	}

	/**
	 * Get the list of possible package statuses,
	 * and their display name.
	 *
	 * @since 2.1.6
	 */
	public static function get_statuses() {
		return [
			'publish'          => _x( 'Active', 'Payment Package Status', 'my-listing' ),
			'draft'            => _x( 'Inactive', 'Payment Package Status', 'my-listing' ),
			'case27_full'      => _x( 'Full', 'Payment Package Status', 'my-listing' ),
			'case27_cancelled' => _x( 'Order Cancelled', 'Payment Package Status', 'my-listing' ),
		];
	}

	private function __construct( $package ) {
		$this->package = $package;
	}

	/**
	 * Assign this package to the given listing.
	 *
	 * @since 2.1.6
	 */
	public function assign_to_listing( $listing_id ) {
		$listing = \MyListing\Src\Listing::get( $listing_id );
		if ( ! $listing ) {
			return false;
		}

		// give listing the package attributes
		update_post_meta( $listing->get_id(), '_package_id', $this->get_product_id() );
		update_post_meta( $listing->get_id(), '_user_package_id', $this->get_id() );
		update_post_meta( $listing->get_id(), '_claimed', $this->mark_verified() ? 1 : 0 );
		update_post_meta( $listing->get_id(), '_job_duration', $this->get_duration() );

		// Make sure any listing promotions aren't made inactive when switching plans.
		// @todo check if there's a promotion package active instead
		$priority = (int) get_post_meta( $listing->get_id(), '_featured', true );
		if ( $priority <= 1 ) {
			update_post_meta( $listing->get_id(), '_featured', $this->is_featured() ? 1 : 0 );
		}

		// set new expiry if the listing is already published
		delete_post_meta( $listing->get_id(), '_job_expires' );
		if ( get_post_status( $listing->get_id() ) === 'publish' ) {
			$expires = \MyListing\Src\Listing::calculate_expiry( $listing->get_id() );
			update_post_meta( $listing->get_id(), '_job_expires', $expires );
		}

		// update package count
		$this->increase_count();
		do_action( 'mylisting/switched-package', $listing->get_id(), $this );
		return true;
	}

	/**
	 * Get payment package id.
	 * @since 1.6
	 */
	public function get_id() {
		return $this->package->ID;
	}

	/**
	 * Get payment package status.
	 *
	 * @since 1.6
	 */
	public function get_status() {
		return $this->package->post_status;
	}

	/**
	 * Get WC Product object.
	 *
	 * @since 1.6
	 */
	public function get_product() {
		if ( $this->product === null ) {
			$this->product = wc_get_product( $this->get_product_id() );
		}

		return $this->product;
	}

	/**
	 * Get WC Product ID.
	 *
	 * @since 1.6
	 */
	public function get_product_id() {
		return $this->package->_product_id;
	}

	/**
	 * Get WC Product image used on pricing plans template.
	 *
	 * @since 2.1.6
	 */
	public function get_product_image( $size = 'thumbnail' ) {
		$image = get_field( 'pricing_plan_image', $this->get_product_id() );
		if ( is_array( $image ) && ! empty( $image['sizes'] ) && ! empty( $image['sizes'][ $size ] ) ) {
			return $image['sizes'][ $size ];
		}

		return false;
	}

	/**
	 * Is payment package featured.
	 *
	 * @since 1.6
	 */
	public function is_featured() {
		return $this->package->_featured ? true : false;
	}

	/**
	 * Is payment package a claim payment package.
	 *
	 * @since 1.6
	 */
	public function use_for_claims() {
		return $this->package->_use_for_claims ? true : false;
	}

	/**
	 * Should listings with this package be marked as verified.
	 *
	 * @since 2.1.6
	 */
	public function mark_verified() {
		return $this->package->_mark_verified ? true : false;
	}

	/**
	 * Should listings with this package be claimable by other users. This is
	 * intended for admin users, to have a way to assign a package for display
	 * to a listing, but still allow other users to claim it.
	 *
	 * @since 2.1.6
	 */
	public function is_claimable() {
		return $this->package->_is_claimable ? true : false;
	}

	/**
	 * Get payment package limit.
	 *
	 * @since 1.6
	 */
	public function get_limit() {
		return absint( $this->package->_limit );
	}

	/**
	 * Get payment package listing count.
	 *
	 * @since 1.6
	 */
	public function get_count() {
		return absint( $this->package->_count );
	}

	/**
	 * Get payment package remaining count.
	 *
	 * @since 1.6
	 */
	public function get_remaining_count() {
		return $this->get_limit() - $this->get_count();
	}

	/**
	 * Get payment package duration for listings.
	 *
	 * @since 1.6
	 */
	public function get_duration() {
		return absint( $this->package->_duration );
	}

	/**
	 * Get payment package order ID.
	 *
	 * @since 1.6
	 */
	public function get_order_id() {
		return absint( $this->package->_order_id );
	}

	/**
	 * Get WC Order object.
	 *
	 * @since 2.1.6
	 */
	public function get_order() {
		if ( $this->order === null ) {
			$this->order = wc_get_order( $this->get_order_id() );
		}

		return $this->order;
	}

	/**
	 * Get payment package owner's user ID.
	 *
	 * @since 1.6
	 */
	public function get_user_id() {
		return absint( $this->package->_user_id );
	}

	/**
	 * Get WP_User object.
	 *
	 * @since 2.1.6
	 */
	public function get_user() {
		if ( $this->user === null ) {
			$this->user = get_userdata( $this->get_user_id() );
		}

		return $this->user;
	}

	public function belongs_to_current_user() {
		if ( ! $user_id = $this->get_user_id() ) {
			return false;
		}

		return $user_id === get_current_user_id();
	}

	/**
	 * Decrease the listing count for this package.
	 *
	 * @since 2.1.6
	 */
	public function decrease_count() {
		$new_count = $this->get_count() - 1;
		update_post_meta( $this->get_id(), '_count', max( 0, $new_count ) );
		$this->maybe_update_status();
	}

	/**
	 * Increase the listing count for this package, ensuring it
	 * doesn't go over the limit.
	 *
	 * @since 2.1.6
	 */
	public function increase_count() {
		$new_count = $this->get_count() + 1;
		$new_count = $this->get_limit() ? min( $this->get_limit(), $new_count ) : $new_count;
		update_post_meta( $this->get_id(), '_count', $new_count );
		$this->maybe_update_status();
	}

	/**
	 * Reset the listing count for this package to zero.
	 *
	 * @since 2.1.6
	 */
	public function reset_count() {
		update_post_meta( $this->get_id(), '_count', 0 );
		$this->maybe_update_status();
	}

	/**
	 * After the package's listing count changes, we may have to update it's status
	 * e.g. from publish to full or vice-versa.
	 *
	 * @since 2.1.6
	 */
	public function maybe_update_status() {
		$status = $this->get_proper_status();
		if ( $status && get_post_status( $this->get_id() ) !== $status ) {
			wp_update_post( [
				'ID' => $this->get_id(),
				'post_status' => $status,
			] );
		}
	}

	/**
	 * Get the correct post status based on limit/count and order status.
	 *
	 * @since 1.6
	 */
	private function get_proper_status() {
		// Get post status.
		$status = $this->get_status();

		// if the package has been deleted, keep it so
		if ( $status === 'trash' ) {
			return $status;
		}

		// if the package has reached it's limit, set it to Full, otherwise Publish
		if ( $this->get_limit() ) {
			// listing has reached limit, set to Full
			if ( $this->get_remaining_count() <= 0 ) {
				$status = 'case27_full';
			// listing has not reached the limit, and it's previous status was Full, so set it to Publish now
			} elseif ( 'case27_full' === $status ) {
				$status = 'publish';
			}
		}

		// if the package doesn't have a limit, it's always publish
		if ( ! $this->get_limit() && $this->get_status() === 'case27_full' ) {
			$status = 'publish';
		}

		// handle cancelled orders
		if ( $order = $this->get_order() ) {
			if ( $order->get_status() === 'cancelled' ) {
				$status = 'case27_cancelled';
			} elseif ( 'case27_cancelled' === $this->get_status() ) {
				$status = 'publish';
			}
		}

		return $status;
	}
}
