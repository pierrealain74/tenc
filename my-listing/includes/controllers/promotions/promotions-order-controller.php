<?php

namespace MyListing\Controllers\Promotions;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Promotions_Order_Controller extends \MyListing\Controllers\Base_Controller {

	protected function hooks() {
		$this->on( 'woocommerce_promotion_package_add_to_cart', 'woocommerce_simple_add_to_cart', 30 );
		$this->on( 'woocommerce_checkout_create_order_line_item', '@add_listing_id_to_order_meta', 10, 4 );
		$this->on( 'woocommerce_thankyou', '@display_thankyou_message', 5 );
		$this->on( 'woocommerce_order_status_processing', '@order_paid' );
		$this->on( 'woocommerce_order_status_completed', '@order_paid' );
		$this->on( 'woocommerce_order_status_cancelled', '@order_cancelled' );
		$this->filter( 'woocommerce_get_item_data', '@display_listing_name_in_cart', 10, 2 );
		$this->filter( 'option_woocommerce_enable_signup_and_login_from_checkout', '@require_account_on_checkout' );
		$this->filter( 'option_woocommerce_enable_guest_checkout', '@disable_guest_checkout' );
	}

	protected function add_listing_id_to_order_meta( $order_item, $cart_item_key, $cart_item_data, $order ) {
		if ( isset( $cart_item_data['listing_id'] ) ) {
			$order_item->update_meta_data( '_listing_id', $cart_item_data['listing_id'] );
		}
	}

	protected function display_listing_name_in_cart( $data, $cart_item ) {
		if ( isset( $cart_item['listing_id'] ) ) {
			$data[] = [
				'name'  => esc_html( __( 'Listing', 'my-listing' ) ),
				'value' => get_the_title( absint( $cart_item['listing_id'] ) ),
			];
		}

		return $data;
	}

	protected function require_account_on_checkout( $value ) {
		global $woocommerce;
		if ( ! empty( $woocommerce->cart->cart_contents ) ) {
			foreach ( $woocommerce->cart->cart_contents as $cart_item ) {
				if ( $cart_item['data'] instanceof \WC_Product && $cart_item['data']->is_type('promotion_package') ) {
					return 'yes';
				}
			}
		}

		return $value;
	}

	protected function disable_guest_checkout( $value ) {
		global $woocommerce;
		if ( ! empty( $woocommerce->cart->cart_contents ) ) {
			foreach ( $woocommerce->cart->cart_contents as $cart_item ) {
				if ( $cart_item['data'] instanceof \WC_Product && $cart_item['data']->is_type('promotion_package') ) {
					return 'no';
				}
			}
		}

		return $value;
	}

	protected function display_thankyou_message( $order_id ) {
		global $wp_post_types;
		$order = wc_get_order( $order_id );
		$is_paid = in_array( $order->get_status(), ['completed', 'processing'], true );

		foreach ( $order->get_items() as $item ) {
			if ( ! isset( $item['listing_id'] ) ) {
				continue;
			}

			$listing_status = get_post_status( $item['listing_id'] );
			if ( $is_paid ) {
				echo wpautop( sprintf(
					__( '"%s" has been promoted successfully.', 'my-listing' ),
					get_the_title( $item['listing_id'] )
				) );
			} else {
				echo wpautop( sprintf(
					__( '"%s" will be promoted once the order is verified and completed.', 'my-listing' ),
					get_the_title( $item['listing_id'] )
				) );
			}
		}
	}

	protected function order_paid( $order_id ) {
		$order = wc_get_order( $order_id );
		if ( get_post_meta( $order_id, 'promotion_packages_processed', true ) ) {
			return;
		}

		foreach ( $order->get_items() as $item ) {
			$product = wc_get_product( $item['product_id'] );
			if ( ! ( $product->is_type('promotion_package') && $order->get_customer_id() ) ) {
				continue;
			}

			// create package
			$package_id = false;
			for ( $i = 0; $i < $item['qty']; $i++ ) {
				$package_id = wp_insert_post( [
					'post_type'   => 'cts_promo_package',
					'post_status' => 'publish',
					'meta_input'  => [
						'_user_id'    => $order->get_customer_id(),
						'_product_id' => $product->get_id(),
						'_order_id'   => $order_id,
						'_duration'   => $product->get_duration(),
						'_priority'   => $product->get_priority(),
					],
				] );

				if ( ! $package_id || is_wp_error( $package_id ) || empty( $item['listing_id'] ) ) {
					continue;
				}

				\MyListing\Src\Promotions\activate_package( $package_id, $item['listing_id'] );
			}
		}

		// mark this order as processed
		update_post_meta( $order_id, 'promotion_packages_processed', true );
	}

	protected function order_cancelled( $order_id ) {
		$packages = get_posts( [
			'post_type' => 'cts_promo_package',
			'post_status' => 'any',
			'posts_per_page' => -1,
			'suppress_filters' => false,
			'fields' => 'ids',
			'meta_query' => [ [
				'key'     => '_order_id',
				'value'   => $order_id,
				'compare' => 'IN',
			] ],
		] );

		if ( $packages && is_array( $packages ) ) {
			foreach ( $packages as $package_id ) {
				\MyListing\Src\Promotions\expire_package( $package_id );
			}
		}
	}
}
