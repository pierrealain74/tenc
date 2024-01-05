<?php

namespace MyListing\Ext\WooCommerce;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Endpoints {
	use \MyListing\Src\Traits\Instantiatable;

	public $pages = [];

	public function __construct() {
		// Add custom dashboard endpoints.
		add_action( 'init', [ $this, 'add_endpoints' ] );

		// Add custom WooCommerce query vars.
		add_filter( 'woocommerce_get_query_vars', array( $this, 'add_query_vars' ), 0 );

		// Update the dashboard menu with custom endpoints.
		add_filter( 'woocommerce_account_menu_items', array( $this, 'new_menu_items' ) );
	}

	/**
	 * Enqueue a custom dashboard endpoint.
	 *
	 * @since 1.0
	 */
	public function add_page( $page ) {
		$this->pages[ $page['endpoint'] ] = $page;
	}

	/**
	 * Register custom dashboard endpoints.
	 *
	 * @since 1.0
	 */
	public function add_endpoints() {
		foreach ( $this->pages as $page ) {
			add_rewrite_endpoint( $page['endpoint'], EP_ROOT | EP_PAGES );
			add_action(
				sprintf( 'woocommerce_account_%s_endpoint', $page['endpoint'] ),
				function() use ( $page ) {
					if ( is_callable( $page['template'] ) ) {
						call_user_func( $page['template'] );
					} elseif ( is_string( $page['template'] ) ) {
						require $page['template'];
					}
				}
			);
		}
	}

	/**
	 * Add custom WooCommerce query vars.
	 *
	 * @since 1.0
	 */
	public function add_query_vars( $vars ) {
		return array_merge( $vars, array_column(
			$this->pages, 'endpoint', 'endpoint'
		) );
	}

	/**
	 * Update the dashboard menu with custom endpoints.
	 *
	 * @since 1.0
	 */
	public function new_menu_items( $items ) {
		// Remove the logout menu item.
		$logout = $items['customer-logout'];
		unset( $items['customer-logout'] );

		// Insert custom endpoints.
		$items += array_column(array_filter($this->pages, function($page) {
			return $page['show_in_menu'];
		}), 'title', 'endpoint');

		// Insert back the logout item.
		$items['customer-logout'] = $logout;

		// Sort items.
		foreach ($items as $item_key => $item) {
			if ( in_array( $item_key, array_keys( $this->pages ) ) ) {
				$items[$item_key] = $this->pages[$item_key];
			}

			if ( $item_key == 'dashboard' ) {
				$items['dashboard'] = [
					'title' => __( 'My Account', 'my-listing' ),
					'order' => 1,
				];
			}
		}

		$items = $this->sort_by_prop( $items, 'order' );

		foreach ($items as $item_key => $item) {
			if ( is_array( $item ) && ! empty( $item['title'] ) ) {
				$items[$item_key] = $item['title'];
			}
		}

		return $items;
	}

	/**
	 * Custom sorting algorithm, to fix inconsistency
	 * in uasort() function between PHP5 and PHP7.
	 *
	 * @link  unknown
	 * @since 1.0
	 */
	public function sort_by_prop( $array, $propName, $reverse = false ) {
		$sorted = [];
		foreach ($array as $itemKey => $item) {
			if ( ! is_array( $item ) ) {
				$item = [ 'title' => $item, 'order' => 25, 'endpoint' => $itemKey ];
			}

			if ( ! isset( $item[ $propName ] ) ) {
				$item[ $propName ] = 25;
			}

			if ( ! isset( $item[ 'endpoint' ] ) ) {
				$item[ 'endpoint' ] = $itemKey;
			}

			$sorted[ $item[ $propName ] ][] = $item;
		}

		$reverse ? krsort( $sorted ) : ksort( $sorted );

		$result = [];
		foreach ($sorted as $subArray) foreach ($subArray as $item) {
			$result[ $item['endpoint'] ] = $item;
		}

		return $result;
	}
}
