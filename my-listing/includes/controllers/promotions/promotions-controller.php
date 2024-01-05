<?php

namespace MyListing\Controllers\Promotions;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Promotions_Controller extends \MyListing\Controllers\Base_Controller {

	protected function dependencies() {
		require_once locate_template('includes/src/promotions/promotions-api.php');
	}

	protected function hooks() {
		$this->on( 'init', '@register_post_type' );
		$this->on( 'deleted_user', '@delete_packages_with_user', 10, 2 );
		$this->on( 'mylisting/schedule:twicedaily', '@handle_expired_packages', 30 );
		$this->filter( 'product_type_selector', '@add_product_type', 40 );
		$this->filter( 'woocommerce_product_class', '@set_product_class', 10, 3 );
		$this->filter( 'the_title', '@set_package_title', 10, 2 );
	}

	protected function add_product_type( $types ) {
		$types['promotion_package'] = esc_html( __( 'Promotion Package', 'my-listing' ) );
		return $types;
	}

	protected function set_product_class( $classname, $product_type ) {
		if ( $product_type === 'promotion_package' ) {
			return 'MyListing\Src\Promotion_Product';
		}

		return $classname;
	}

	protected function delete_packages_with_user( $id, $reassign ) {
		$packages = get_posts( [
			'post_type' => 'cts_promo_package',
			'post_status'  => 'any',
			'posts_per_page' => -1,
			'suppress_filters' => false,
			'fields' => 'ids',
			'meta_query' => [ [
				'key'     => '_user_id',
				'value'   => $id,
				'compare' => 'IN',
			] ],
		] );

		foreach ( $packages as $package_id ) {
			wp_delete_post( $package_id, true );
		}
	}

	protected function set_package_title( $title, $id = null ) {
		if ( ! ( $id && get_post_type( $id ) === 'cts_promo_package' ) ) {
			return $title;
		}

		$title = sprintf( '#%s', $id );
		$listing_id = get_post_meta( $id, '_listing_id', true );

		// append listing name to promotion package title
		if ( absint( $listing_id ) && ( $listing_title = get_the_title( $listing_id ) ) ) {
			$title = sprintf( '#%s &mdash; %s', $id, $listing_title );
		}

		return $title;
	}

	protected function register_post_type() {
		register_post_type( 'cts_promo_package', [
			'description'         => '',
			'public'              => false,
			'publicly_queryable'  => false,
			'show_in_nav_menus'   => false,
			'show_in_admin_bar'   => false,
			'exclude_from_search' => true, // Need this for WP_Query.
			'show_ui'             => true,
			'show_in_menu'        => false,
			'menu_position'       => 3,
			'menu_icon'           => 'dashicons-screenoptions',
			'can_export'          => true,
			'delete_with_user'    => false,
			'hierarchical'        => false,
			'has_archive'         => false,
			'query_var'           => true,
			'rewrite'             => false,
			'capability_type'     => 'page',
			'supports'            => [''],
			'labels'              => [
				'name'               => __( 'Promotion Packages', 'my-listing' ),
				'singular_name'      => __( 'Promotion Package', 'my-listing' ),
				'add_new'            => __( 'Promote a Listing', 'my-listing' ),
				'add_new_item'       => __( 'Add New Package', 'my-listing' ),
				'edit_item'          => __( 'Edit Package', 'my-listing' ),
				'new_item'           => __( 'New Package', 'my-listing' ),
				'all_items'          => __( 'All Packages', 'my-listing' ),
				'view_item'          => __( 'View Package', 'my-listing' ),
				'search_items'       => __( 'Search Packages', 'my-listing' ),
				'not_found'          => __( 'Not Found', 'my-listing' ),
				'not_found_in_trash' => __( 'Not Found in Trash', 'my-listing' ),
				'menu_name'          => __( 'Promotion Packages', 'my-listing' ),
			],
		] );
	}

	protected function handle_expired_packages() {
		global $wpdb;

		// find expired packages
		$package_ids = $wpdb->get_col( $wpdb->prepare( "
			SELECT postmeta.post_id FROM {$wpdb->postmeta} as postmeta
			LEFT JOIN {$wpdb->posts} as posts ON postmeta.post_id = posts.ID
			WHERE postmeta.meta_key = '_expires'
			AND postmeta.meta_value > 0
			AND postmeta.meta_value < %s
			AND posts.post_status = 'publish'
			AND posts.post_type = 'cts_promo_package'
		", date( 'Y-m-d H:i:s', current_time( 'timestamp' ) ) ) );

		// expire them
		foreach ( (array) $package_ids as $package_id ) {
			\MyListing\Src\Promotions\expire_package( $package_id );
		}
	}
}
