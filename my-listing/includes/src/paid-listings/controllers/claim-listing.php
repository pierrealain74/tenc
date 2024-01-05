<?php
/**
 * Claim Listing form payments handler.
 *
 * @since 1.6
 */

namespace MyListing\Src\Paid_Listings\Controllers;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Claim_Listing {
	use \MyListing\Src\Traits\Instantiatable;

	public function __construct() {
		// fires after the subscription has been activated, and the payment package has been created
		add_action( 'mylisting/subscriptions/claim/order-processed', [ $this, 'subscription_processed' ], 10, 2 );

		// fires after the order has been paid and processed and the payment package has been created
		add_action( 'mylisting/payments/claim/order-processed', [ $this, 'order_processed' ], 10, 2 );

		// fires after a premium product has been chosen to claim a listing
		add_action( 'mylisting/payments/claim/product-selected', [ $this, 'product_selected' ], 10, 2 );

		// user is claiming a listing with a pre-owned pacakge
		add_action( 'mylisting/payments/claim/use-available-package', [ $this, 'use_available_package' ], 10, 2 );

		// user is claiming a listing with a free package, configured to skip checkout
		add_action( 'mylisting/payments/claim/use-free-package', [ $this, 'use_free_package' ], 10, 2 );
	}

	/**
	 * Fires after the subscription has been activated, and the payment package
	 * has been created. Create the claim and submit it for approval.
	 *
	 * @since 2.1.6
	 */
	public function subscription_processed( $listing, $package ) {
		$claim_id = \MyListing\Src\Claims\Claims::create( [
			'listing_id' => absint( $listing->get_id() ),
			'user_package_id' => absint( $package->get_id() ),
			'user_id' => absint( $package->get_user_id() ),
		] );
	}

	/**
	 * After the order has been paid and processed and the payment package is
	 * created, create the claim and submit it for approval.
	 *
	 * @since 2.1.6
	 */
	public function order_processed( $listing, $package ) {
		\MyListing\Src\Claims\Claims::create( [
			'listing_id' => absint( $listing->get_id() ),
			'user_package_id' => absint( $package->get_id() ),
			'user_id' => absint( $package->get_user_id() ),
		] );
	}

	/**
	 * Fired after a premium product has been chosen by the user. Redirect to
	 * checkout without modifying listing data in any way until claim approval.
	 *
	 * @since 2.1.6
	 */
	public function product_selected( $listing, $product ) {
		$data = [
			'job_id' => $listing->get_id(),
			'assignment_type' => 'claim',
		];

		WC()->cart->add_to_cart( $product->get_id(), 1, '', '', $data );

		// clear cookie
		wc_setcookie( 'chosen_package_id', '', time() - HOUR_IN_SECONDS );

		// if the user has other items in their cart, redirect to cart page instead
		// to avoid any accidental purchases
		$redirect_url = WC()->cart->get_cart_contents_count() > 1
			? wc_get_cart_url()
			: wc_get_checkout_url();

		// redirect to checkout page
		wp_redirect( $redirect_url );
		exit;
	}

	/**
	 * User is claiming a listing using a pre-owned package. Create the claim,
	 * without modifying any listing data until the claim is approved.
	 *
	 * @since 2.1.6
	 */
	public function use_available_package( $listing, $package ) {
		if ( ! $package->belongs_to_current_user() ) {
			throw new \Exception( _x( 'Couldn\'t process package.', 'Listing submission', 'my-listing' ) );
		}

		if ( ! ( $package->get_status() === 'publish' && $package->use_for_claims() ) ) {
			throw new \Exception( _x( 'Invalid package selected.', 'Claim listing form', 'my-listing' ) );
		}

		// create claim
		$claim_id = \MyListing\Src\Claims\Claims::create( [
			'listing_id'      => $listing->get_id(),
			'user_package_id' => $package->get_id(),
		] );

		if ( ! $claim_id ) {
			throw new \Exception( _x( 'Something went wrong. Please try again.', 'Claim listing form', 'my-listing' ) );
		}

		// success, redirect to claim info page
		$claim_url = add_query_arg( 'listing_id', $listing->get_id(), get_permalink() );
		wp_safe_redirect( esc_url_raw( add_query_arg( '_claim_id', absint( $claim_id ), $claim_url ) ) );
		exit;
	}

	/**
	 * User is claiming a listing using a free package configured to skip checkout.
	 * Create the payment package right away and submit the claim for approval.
	 *
	 * @since 2.1.6
	 */
	public function use_free_package( $listing, $product ) {
		$package = \MyListing\Src\Package::create( [
			'user_id'        => get_current_user_id(),
			'product_id'     => $product->get_id(),
			'duration'       => $product->get_duration(),
			'limit'          => $product->get_limit(),
			'featured'       => $product->is_listing_featured(),
			'mark_verified'  => $product->mark_verified(),
			'use_for_claims' => $product->use_for_claims(),
			'order_id'       => false,
		] );

		// Use it.
		if ( $package ) {
			$claim_id = \MyListing\Src\Claims\Claims::create( [
				'listing_id'      => $listing->get_id(),
				'user_package_id' => $package->get_id(),
			] );

			// refresh, use claim ID in URL.
			$claim_url = add_query_arg( 'listing_id', $listing->get_id(), get_permalink() );
			wp_safe_redirect( esc_url_raw( add_query_arg( '_claim_id', absint( $claim_id ), $claim_url ) ) );
			exit;
		}
	}
}