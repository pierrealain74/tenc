<?php

namespace MyListing\Apis\Wp_All_Import;

if ( ! defined('ABSPATH') ) {
	exit;
}

function import_products( $field, $field_value, $log, $delimiter ) {
	global $wpdb;

	$identifiers = explode( $delimiter, (string) $field_value );
	$product_ids = [];
	foreach ( (array) $identifiers as $identifier ) {
		$identifier = trim( $identifier );

		$product_id = $wpdb->get_var( $wpdb->prepare( "
			SELECT {$wpdb->posts}.ID FROM {$wpdb->posts}
			LEFT JOIN {$wpdb->postmeta} AS sku ON ( sku.post_id = {$wpdb->posts}.ID AND sku.meta_key = '_sku' )
			WHERE ( {$wpdb->posts}.post_type = 'product' OR {$wpdb->posts}.post_type = 'product_variation' )
			AND ( {$wpdb->posts}.ID = %s
				OR {$wpdb->posts}.post_name = %s
				OR {$wpdb->posts}.post_title = %s
				OR sku.meta_value = %s
			) LIMIT 0, 1
		", $identifier, $identifier, $identifier, $identifier ) );

		if ( ! empty( $product_id ) ) {
			$product_ids[] = absint( $product_id );
		}
	}

	$product_ids = array_filter( $product_ids );
	if ( $field->get_type() === 'select-product' ) {
		$product_ids = $product_ids[0] ?? '';
	}

	update_post_meta( $field->listing->get_id(), '_'.$field->get_key(), $product_ids );
}
