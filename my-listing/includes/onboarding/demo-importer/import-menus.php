<?php

namespace MyListing\Onboarding\Demo_Importer;

if ( ! defined('ABSPATH') ) {
	exit;
}

function import_menus() {
	$config = get_demo_file('menus.json');
	$locations = [];
	foreach ( $config as $location ) {
		// insert menu item
		$term_ids = wp_insert_term( $location['name'], 'nav_menu', [
			'slug' => $location['slug'],
		] );

		if ( is_wp_error( $term_ids ) ) {
			continue;
		}

		$term_id = $term_ids['term_id'];
		$locations[ $location['location'] ] = $term_id;

		// insert nav menu items in wp_posts
		foreach ( $location['items'] as $menu_item ) {
			$post_id = wp_insert_post( [
				'post_type' => 'nav_menu_item',
				'post_title' => $menu_item['title'],
				'post_status' => 'publish',
				'menu_order' => $menu_item['menu_order'],
				'meta_input' => [
					'_menu_item_menu_item_parent' => $menu_item['meta']['menu_item_menu_item_parent'],
					'_menu_item_type' => $menu_item['meta']['menu_item_type'],
					'_menu_item_object' => $menu_item['meta']['menu_item_object'],
					'_menu_item_object_id' => $menu_item['meta']['menu_item_object_id'],
					'_menu_item_url' => str_replace(
						'<<#siteurl#>>',
						untrailingslashit( site_url() ),
						$menu_item['meta']['menu_item_url']
					),
					'__demo_import_postid' => $menu_item['id'],
				],
			], true );

			// attach nav menu item post to the menu term
			if ( ! is_wp_error( $post_id ) ) {
				wp_set_object_terms( $post_id, $term_id, 'nav_menu' );
			}
		}
	}

	// configure nav menu locations
	if ( ! empty( $locations ) ) {
		set_theme_mod( 'nav_menu_locations', $locations );
	}
}
