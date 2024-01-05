<?php

namespace MyListing\Src\Paid_Listings;

class Util {

	/**
	 * @param string $context add-listing|switch-package|claim-listing
	 */
	public static function get_package_tree_for_listing_type( $type, $context = 'add-listing' ) {
		$package_ids = array_column( $type->get_packages(), 'package' );
		$tree = [];

		// Get the products that are allowed for this listing type.
		$products = self::get_products( [
			'post__in' => $package_ids,
			'product_objects' => true,
		] );

		// Get user bought packages that are allowed for this listing type.
		$packages = self::get_current_user_packages( $package_ids );

		foreach ( (array) $products as $product ) {
			// Skip if not the right product type or not purchaseable.
			if ( ! $product->is_type( [ 'job_package', 'job_package_subscription' ] ) ) {
				continue;
			}

			// Item data.
			$item = [];
			$item['product'] = $product;
			$item['packages'] = [];

			// Get owned packages of this product.
			foreach ( (array) $packages as $key => $package ) {
				if ( absint( $package->get_product_id() ) !== absint( $product->get_id() ) ) {
					continue;
				}

				$item['packages'][] = $package;
				unset( $packages[ $key ] );
			}

			$item['title'] = $product->get_name();
			$item['description'] = $product->get_description();
			$item['featured'] = false;
			$item['image'] = false;

			// If a custom title, description, or other options are set on this product
			// for this specific listing type, then replace the default ones with the custom one.
			if ( $type && ( $_package = $type->get_package( $product->get_id() ) ) ) {
				$item['title'] = $_package['label'] ?: $item['title'];
				$item['featured'] = $_package['featured'] ?: $item['featured'];

				// Split the description textarea into new lines,
				// so it can later be reconstructed to an html list.
				$item['description'] = $_package['description'] ? preg_split( '/\r\n|[\r\n]/', $_package['description'] ) : $item['description'];
			}

			// Get product image.
			$_product_image = get_field( 'pricing_plan_image', $product->get_id() );
			if ( is_array( $_product_image ) && ! empty( $_product_image['sizes'] ) && ! empty( $_product_image['sizes']['large'] ) ) {
				$item['image'] = $_product_image['sizes']['large'];
			}

			$tree[ $product->get_id() ] = $item;
		}

		return apply_filters( 'mylisting/get_package_tree_for_listing_type', $tree, $type, $context );
	}

	public static function get_current_user_packages( $product_ids = [], $format = 'object', $status = 'publish' ) {
		// Get packages.
		$package_ids = get_posts( [
			'post_type'        => 'case27_user_package',
			'post_status'      => $status,
			'posts_per_page'   => -1,
			'suppress_filters' => false,
			'fields'           => 'ids',
			'meta_query' => [
				'relation' => 'AND',
				[
					'key'     => '_user_id',
					'value'   => get_current_user_id(),
					'compare' => 'IN',
				],
				[
					'key'     => '_product_id',
					'value'   => $product_ids,
					'compare' => 'IN',
				],
			],
		] );

		if ( $format === 'ids' ) {
			return $package_ids;
		}

		// Set package object.
		$packages = [];
		foreach ( $package_ids as $package_id ) {
			if ( ! ( $package = \MyListing\Src\Package::get( $package_id ) ) ) {
				continue;
			}

			$packages[ $package_id ] = $package;
		}

		return $packages;
	}

	/**
	 * Check if a package can be
	 * used in the given listing type.
	 *
	 * @since 2.0
	 * @param (int) $package_id   ID of the user package or wc-product.
	 * @param (str) $listing_type Listing type slug.
	 *
	 * @return bool $valid
	 */
	public static function validate_package( $package_id, $listing_type ) {
		if ( ! ( $type_obj = ( get_page_by_path( $listing_type, OBJECT, 'case27_listing_type' ) ) ) ) {
			return false;
		}

		$package = get_post( $package_id );
		$type = \MyListing\Src\Listing_Type::get( $type_obj );
		$allowed_product_ids = array_column( $type->get_packages(), 'package' );

		// Paid packages disabled for listing type.
		if ( $type->settings['packages']['enabled'] === false ) {
			return false;
		}

		// Couldn't retrieve package post object.
		if ( ! $package ) {
			return false;
		}

		// Package is a wc-product.
		if ( $package->post_type === 'product' ) {
			$product = wc_get_product( $package->ID );
			if ( ! $product || ! ( $product->is_type( [ 'job_package', 'job_package_subscription' ] ) ) ) {
				return false;
			}

			// Make sure this product type is allowed in the listing type.
			// If no products have been set in the listing type, then allow all.
			if ( ! empty( $allowed_product_ids ) && ! in_array( $product->get_id(), $allowed_product_ids ) ) {
				return false;
			}

			return true;
		}

		// Package is a case27_user_package
		if ( $package->post_type === 'case27_user_package' && is_user_logged_in() ) {
			$allowed_package_ids = self::get_current_user_packages( $allowed_product_ids, 'ids' );

			// If no products have been set in the listing type, then allow all.
			if ( ! empty( $allowed_product_ids ) && ! in_array( $package->ID, $allowed_package_ids ) ) {
				return false;
			}

			return true;
		}

		// Package post-type is not a product or user package, invalidate.
		return false;
	}

	/**
	 * Get Paid Listing Products
	 *
	 * @since 1.6
	 *
	 * @param array $args Query Args.
	 * @return array
	 */
	public static function get_products( $args = [] ) {
		$terms = [ 'job_package' ];
		if ( class_exists( '\WC_Subscriptions' ) ) {
			$terms[] = 'job_package_subscription';
		}
		$defaults = [
			'post_type'        => 'product',
			'posts_per_page'   => -1,
			'post__in'         => [],
			'order'            => 'asc',
			'orderby'          => 'post__in',
			'suppress_filters' => false,
			'fields'           => 'ids',
			'product_objects'  => false,
			'tax_query'        => [
				'relation' => 'AND',
				[
					'taxonomy' => 'product_type',
					'field'    => 'slug',
					'terms'    => $terms,
					'operator' => 'IN',
				],
			],
		];

		$args = wp_parse_args( $args, $defaults );

		$items = get_posts( $args );
		if ( $args['product_objects'] !== true || $args['fields'] !== 'ids' ) {
			return $items;
		}

		// Get WC Products.
		$products = [];
		foreach ( (array) $items as $product_id ) {
			$products[ $product_id ] = wc_get_product( $product_id );
		}

		return $products;
	}
}