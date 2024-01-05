<?php

namespace MyListing\Controllers\Promotions;

use \MyListing\Src\Promotions;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Promotions_Dashboard_Controller extends \MyListing\Controllers\Base_Controller {

	protected function hooks() {
		$this->on( 'mylisting_ajax_cts_promotions', '@handle_promotion_request' );
		$this->on( 'mylisting/promotion-request/buy-package', '@buy_package' );
		$this->on( 'mylisting/promotion-request/use-available-package', '@use_available_package' );
		$this->on( 'mylisting/promotion-request/cancel-package', '@cancel_package' );
		$this->on( 'after_setup_theme', '@setup_dashboard_endpoint' );
		$this->on( 'mylisting/get-footer', '@include_promotions_modal' );
		$this->filter( 'mylisting/user-listings/actions', '@display_promote_listing_action', 10 );
	}

	protected function buy_package( $listing ) {
		if ( empty( $_POST['package_id'] ) ) {
        	throw new \Exception( __( 'Could not process request.', 'my-listing' ), 20 );
		}

		Promotions\buy_product( $_POST['package_id'], $listing->get_id() );

        return wp_send_json( [
            'status'  => 'success',
            'redirect' => add_query_arg(
            	[ 't' => time() ],
            	WC()->cart->get_cart_contents_count() > 1
            		? wc_get_cart_url()
            		: wc_get_checkout_url()
            ),
        ] );
	}

	protected function use_available_package( $listing ) {
		if ( empty( $_POST['package_id'] ) ) {
        	throw new \Exception( __( 'Could not process request.', 'my-listing' ), 30 );
		}

		$package = Promotions\get_package( $_POST['package_id'] );

		// package must be editable by current user, and must not be assigned to another listing
		$package_owner = get_post_meta( $package->ID, '_user_id', true );
		$can_assign = \MyListing\current_user_can_edit( $package->ID )
			|| absint( get_current_user_id() ) === absint( $package_owner );
		if ( ! ( $package && $can_assign && empty( $package->_listing_id ) ) ) {
        	throw new \Exception( __( 'Could not process request.', 'my-listing' ), 31 );
		}

		if ( ! ( Promotions\activate_package( $package->ID, $listing->get_id() ) ) ) {
        	throw new \Exception( __( 'Could not process request.', 'my-listing' ), 32 );
		}

		wc_add_notice( sprintf(
			__( '"%s" has been promoted.', 'my-listing' ),
			$listing->get_title()
		), 'success' );

        return wp_send_json( [
            'status'  => 'success',
            'redirect' => add_query_arg( 't', time(), Promotions\get_package_edit_link( $package->ID ) ),
        ] );
	}


	protected function cancel_package( $listing ) {
		$package = Promotions\get_listing_package( $listing->get_id() );
		if ( ! $package ) {
        	throw new \Exception( __( 'Could not process request.', 'my-listing' ), 40 );
		}

		Promotions\expire_package( $package->ID );

		wc_add_notice( sprintf(
			__( 'Promotion cancelled for "%s".', 'my-listing' ),
			$listing->get_title()
		), 'notice' );

        return wp_send_json( [
            'status'  => 'success',
            'redirect' => add_query_arg( 't', time(), wc_get_account_endpoint_url( \MyListing\promotions_endpoint_slug() ) ),
        ] );
	}

	protected function display_promote_listing_action( $listing ) {
		if ( $listing->get_status() !== 'publish' ) {
			return;
		}

		$package = Promotions\get_listing_package( $listing->get_id() );
		if ( $package ): ?>
			<li class="cts-listing-action-promote listing-promoted">
				<a href="<?php echo esc_url( Promotions\get_package_edit_link( $package->ID ) ) ?>" class="listing-action-promoted">
					<?php _ex( 'Promoted', 'Promoted listing link name', 'my-listing' ) ?>
				</a>
			</li>
		<?php else: ?>
			<li class="cts-listing-action-promote">
				<a class="listing-dashboard-action-promote" data-toggle="modal" data-target="#promo-modal"
					data-listing-id="<?php echo esc_attr( $listing->get_id() ) ?>"
					data-listing-name="<?php echo esc_attr( $listing->get_name() ) ?>"
				><?php _ex( 'Promote', 'Promote listing link name', 'my-listing' ) ?></a>
			</li>
		<?php endif;
	}

	protected function include_promotions_modal() {
		if ( ! ( function_exists( 'is_wc_endpoint_url' ) && is_wc_endpoint_url( \MyListing\my_listings_endpoint_slug() ) ) ) {
			return;
		}

		$products = Promotions\get_products();
		$packages = Promotions\get_available_packages_for_current_user();
		return require locate_template( 'templates/dashboard/promotions/choose-promotion.php' );
	}

	protected function setup_dashboard_endpoint() {
		$slug = \MyListing\promotions_endpoint_slug();
		$slug = apply_filters( 'mylisting/promotions/url-endpoint', $slug );
		\MyListing\add_dashboard_page( [
			'endpoint' => $slug,
			'title' => _x( 'Promotions', 'User Dashboard > Promotions page title', 'my-listing' ),
			'template' => locate_template( 'templates/dashboard/promotions/dashboard.php' ),
			'show_in_menu' => true,
			'order' => 3,
		] );
	}

	protected function handle_promotion_request() {
		check_ajax_referer( 'c27_ajax_nonce', 'security' );
		$process = ! empty( $_POST['process'] ) ? $_POST['process'] : 'buy-package';
		try {
			// validate request
			if ( ! is_user_logged_in() || empty( $_POST['listing_id'] ) ) {
	            throw new \Exception( __( 'Could not process request.', 'my-listing' ), 10 );
			}

			// verify it's a published listing and editable by current user
			$listing = \MyListing\Src\Listing::get( absint( $_POST['listing_id'] ) );
			if ( ! ( $listing && $listing->get_status() === 'publish' && $listing->editable_by_current_user() ) ) {
	            throw new \Exception( __( 'Could not process request.', 'my-listing' ), 11 );
			}

			if ( $process === 'buy-package' ) {
				do_action( 'mylisting/promotion-request/buy-package', $listing );
			}

			if ( $process === 'use-package' ) {
				do_action( 'mylisting/promotion-request/use-available-package', $listing );
	        }

			if ( $process === 'cancel-package' ) {
				do_action( 'mylisting/promotion-request/cancel-package', $listing );
			}
		} catch ( \Exception $e ) {
            return wp_send_json( [
                'status'  => 'error',
                'message' => $e->getMessage(),
                'code' => \MyListing\is_debug_mode() ? $e->getCode() : '',
            ] );
		}
	}
}
