<?php

namespace MyListing\Onboarding\Demo_Importer;

if ( ! defined('ABSPATH') ) {
	exit;
}

function import_products() {
	$config = get_demo_file('products.json');
	foreach ( $config as $item ) {
		$product_id = wp_insert_post( [
			'post_type' => 'product',
			'post_title' => $item['name'],
			'post_name' => $item['slug'],
			'post_content' => import_post_content( $item['description'] ),
			'post_excerpt' => import_post_content( $item['short_description'] ),
			'post_status' => 'publish',
			'meta_input' => [
				'__demo_import_postid' => $item['id'],
			],
		], true );

		if ( ! is_wp_error( $product_id ) ) {
			if ( ! empty( $item['product_type'] ) ) {
				wp_set_object_terms( $product_id, $item['product_type'], 'product_type' );
			}

			foreach ( $item['meta'] as $meta_key => $meta_value ) {
				if ( $meta_key === 'pricing_plan_image' || $meta_key === '_thumbnail_id' ) {
					if ( $attachment_id = get_imported_post_id( $meta_value ) ) {
						update_post_meta( $product_id, $meta_key, $attachment_id );
					}
					continue;
				}

				update_post_meta( $product_id, $meta_key, $meta_value );
			}
		}
	}
}
