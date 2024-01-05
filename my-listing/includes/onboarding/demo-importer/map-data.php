<?php

namespace MyListing\Onboarding\Demo_Importer;

if ( ! defined('ABSPATH') ) {
	exit;
}

function map_data() {
	map_page_ids();
	map_featured_categories();
	map_listing_type_product_ids();
	map_related_listings();
	map_listing_products();
	map_term_types();
	map_menu_items();
}

function map_page_ids() {
	// these options have their values set to "map:<id>" where <id> is the old page id where the
	// demo was exported from; replace this with the new page id assigned on import
	$options = [
		'options_header_call_to_action_links_to',
		'options_general_explore_listings_page',
		'options_general_add_listing_page',
		'job_manager_claim_listing_page_id',
		'__page_on_front',
		'__page_for_posts',
		'woocommerce_cart_page_id',
		'woocommerce_checkout_page_id',
		'woocommerce_myaccount_page_id',
		'woocommerce_terms_page_id',
		'woocommerce_shop_page_id',
	];

	foreach ( $options as $option_key ) {
		$old_page_id = str_replace( 'map:', '', get_option( $option_key, '' ) );
		if ( ! empty( $old_page_id ) && ( $new_page_id = get_imported_post_id( $old_page_id ) ) ) {
			update_option( $option_key, $new_page_id );
		} else {
			delete_option( $option_key );
		}
	}

	// setting a value for these options doesn't work as pages have not been imported
	// yet during config import, and WP makes additional checks to make sure these pages
	// exist; so we use a temporary key to store the ID to map.
	update_option( 'page_on_front', get_option( '__page_on_front' ) );
	update_option( 'page_for_posts', get_option( '__page_for_posts' ) );
	delete_option( '__page_on_front' );
	delete_option( '__page_for_posts' );
}

function map_featured_categories() {
	// map options_header_search_form_featured_categories term slugs to imported term ids (serialized)
	$featured_category_slugs = (array) get_option( 'options_header_search_form_featured_categories', [] );
	if ( ! empty( $featured_category_slugs ) ) {
		$featured_categories = get_terms( [
			'orderby' => 'slug__in',
			'hide_empty' => false,
			'fields' => 'ids',
			'slug' => $featured_category_slugs,
		] );

		if ( ! is_wp_error( $featured_categories ) && ! empty( $featured_categories ) ) {
			update_option( 'options_header_search_form_featured_categories', $featured_categories );
		} else {
			delete_option( 'options_header_search_form_featured_categories' );
		}
	}
}

function map_listing_type_product_ids() {
	$type_ids = get_posts( [
		'post_type' => 'case27_listing_type',
		'post_status' => 'publish',
		'fields' => 'ids',
		'posts_per_page' => -1,
		'meta_query' => [ [
			'key' => '__demo_import_postid',
			'compare' => 'EXISTS',
		] ],
	] );

	if ( is_wp_error( $type_ids ) || empty( $type_ids ) ) {
		return;
	}

	foreach ( (array) $type_ids as $type_id ) {
		if ( ! ( $type = \MyListing\Src\Listing_Type::get( $type_id ) ) ) {
			continue;
		}

		$settings = $type->settings;

		foreach ( (array) $settings['packages']['used'] as $key => $package ) {

			// map listing type product ids to the imported product ids
			if ( $imported_product_id = get_imported_post_id( $package['package'] ) ) {
				$settings['packages']['used'][ $key ]['package'] = $imported_product_id;
			} else {
				unset( $settings['packages']['used'][ $key ] );
			}
		}

        update_post_meta( $type->get_id(), 'case27_listing_type_settings_page', wp_slash( serialize( $settings ) ) );
	}
}

function map_related_listings() {
	global $wpdb;

	$values = $wpdb->get_results( "
		SELECT post_id, meta_value FROM {$wpdb->postmeta}
		WHERE meta_key = '__demo_import_relations'",
	ARRAY_A );

	if ( empty( $values ) || ! is_array( $values ) ) {
		return;
	}

	// map __demo_import_relations to the imported listing ids and move to wp_mylisting_relations table
	foreach ( $values as $value ) {
		$listing_id = absint( $value['post_id'] );
		$relations = unserialize( $value['meta_value'] );

		foreach ( (array) $relations as $relation ) {
			// delete existing relations
			$delete_column = in_array( $relation['type'], [ 'has_one', 'has_many' ], true ) ? 'parent_listing_id' : 'child_listing_id';
			$wpdb->delete( $wpdb->prefix.'mylisting_relations', [
				$delete_column => $listing_id,
				'field_key' => $relation['key'],
			] );

			// create query with updated values
			$query_rows = [];
			foreach ( (array) $relation['values'] as $item_order => $related_listing_id ) {
				if ( ! ( $related_listing_id = get_imported_post_id( $related_listing_id ) ) ) {
					continue;
				}

				$parent_id = in_array( $relation['type'], [ 'has_one', 'has_many' ], true ) ? $listing_id : $related_listing_id;
				$child_id = in_array( $relation['type'], [ 'has_one', 'has_many' ], true ) ? $related_listing_id : $listing_id;
				$query_rows[] = $wpdb->prepare( '(%d,%d,%s,%d)', $parent_id, $child_id, $relation['key'], $item_order );
			}

			$query = "INSERT INTO {$wpdb->prefix}mylisting_relations (parent_listing_id, child_listing_id, field_key, item_order) VALUES ";
			$query .= implode( ',', $query_rows );

			// update database with new values
			$wpdb->query( $query );
		}
	}
}

function map_listing_products() {
	global $wpdb;

	$values = $wpdb->get_results( "
		SELECT post_id, meta_value FROM {$wpdb->postmeta}
		WHERE meta_key = '__demo_import_products'",
	ARRAY_A );

	if ( empty( $values ) || ! is_array( $values ) ) {
		return;
	}

	// map __demo_import_products to the imported product ids
	foreach ( $values as $value ) {
		$listing_id = absint( $value['post_id'] );
		$fields = unserialize( $value['meta_value'] );

		foreach ( (array) $fields as $field ) {
			$product_ids = [];
			foreach ( (array) $field['values'] as $product_id ) {
				if ( $product_id = get_imported_post_id( $product_id ) ) {
					$product_ids[] = $product_id;
				}
			}

			if ( ! empty( $product_ids ) ) {
				update_post_meta(
					$listing_id,
					$field['key'],
					$field['type'] === 'select-products' ? $product_ids : array_shift( $product_ids )
				);
			}
		}
	}
}

// map term meta __demo_import_types to listing_type (from slugs to serialized ids, casted as string)
function map_term_types() {
	global $wpdb;

	$values = $wpdb->get_results( "
		SELECT term_id, meta_value FROM {$wpdb->termmeta}
		WHERE meta_key = '__demo_import_types'
	", ARRAY_A );

	if ( empty( $values ) || ! is_array( $values ) ) {
		return;
	}

	foreach ( $values as $value ) {
		$term_id = absint( $value['term_id'] );
		$type_slugs = unserialize( $value['meta_value'] );
		$type_ids = [];

		foreach ( (array) $type_slugs as $slug ) {
			if ( $type = \MyListing\Src\Listing_Type::get_by_name( $slug ) ) {
				$type_ids[] = (string) $type->get_id();
			}
		}

		if ( ! empty( $type_ids ) ) {
			update_term_meta( $term_id, 'listing_type', $type_ids );
		}
	}
}

function map_menu_items() {
	$nav_menu_ids = get_posts( [
		'post_type' => 'nav_menu_item',
		'post_status' => 'publish',
		'fields' => 'ids',
		'posts_per_page' => -1,
		'meta_query' => [ [
			'key' => '__demo_import_postid',
			'compare' => 'EXISTS',
		] ],
	] );

	foreach ( $nav_menu_ids as $nav_menu_id ) {
		// when "_menu_item_type" is set to "post_type", map "_menu_item_object_id" to the imported post id
		// @todo: handle the case when "_menu_item_type" is "taxonomy"
		$object_type = get_post_meta( $nav_menu_id, '_menu_item_type', true );
		$object_id = get_post_meta( $nav_menu_id, '_menu_item_object_id', true );
		if ( $object_type === 'post_type' && $object_id && ( $post_id = get_imported_post_id( $object_id ) ) ) {
			update_post_meta( $nav_menu_id, '_menu_item_object_id', $post_id );
		}

		// map "_menu_item_menu_item_parent" to the imported nav_menu_item id
		$parent_id = get_post_meta( $nav_menu_id, '_menu_item_menu_item_parent', true );
		if ( $parent_id && ( $post_id = get_imported_post_id( $parent_id ) ) ) {
			update_post_meta( $nav_menu_id, '_menu_item_menu_item_parent', $post_id );
		}
	}
}
