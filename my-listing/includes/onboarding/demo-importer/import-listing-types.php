<?php

namespace MyListing\Onboarding\Demo_Importer;

if ( ! defined('ABSPATH') ) {
	exit;
}

function import_listing_types() {
	$config = get_demo_file('types.json');
	foreach ( (array) $config as $type ) {
		// if listing type with this slug already exists, skip
		if ( \MyListing\Src\Listing_Type::get_by_name( $type['slug'] ) ) {
			continue;
		}

		$type_id = wp_insert_post( [
			'post_type' => 'case27_listing_type',
			'post_title' => $type['title'],
			'post_status' => 'publish',
			'post_name' => $type['slug'],
			'meta_input' => [
				'__demo_import_postid' => $type['id'],
			],
		], true );

		if ( ! is_wp_error( $type_id ) ) {
			// import featured image
			if ( ! empty( $type['featured-image'] ) && ( $attachment_id = get_imported_post_id( $type['featured-image'] ) ) ) {
				update_post_meta( $type_id, '_thumbnail_id', $attachment_id );
			}

			// import logo
			if ( ! empty( $type['logo'] ) && ( $attachment_id = get_imported_post_id( $type['logo'] ) ) ) {
				update_post_meta( $type_id, 'default_logo', $attachment_id );
				update_post_meta( $type_id, '_default_logo', 'field_5a7c651950f2e' );
			}

			// import cover image
			if ( ! empty( $type['cover-image'] ) && ( $attachment_id = get_imported_post_id( $type['cover-image'] ) ) ) {
				update_post_meta( $type_id, 'default_cover_image', $attachment_id );
				update_post_meta( $type_id, '_default_cover_image', 'field_5a7c657d50f2f' );
			}

			// import listing type configuration
			$config = $type['config'];
			update_post_meta( $type_id, 'case27_listing_type_fields', wp_slash( serialize( $config['fields']['used'] ) ) );
            update_post_meta( $type_id, 'case27_listing_type_single_page_options', wp_slash( serialize( $config['single'] ) ) );
            update_post_meta( $type_id, 'case27_listing_type_result_template', wp_slash( serialize( $config['result'] ) ) );
            update_post_meta( $type_id, 'case27_listing_type_search_page', wp_slash( serialize( $config['search'] ) ) );
            update_post_meta( $type_id, 'case27_listing_type_settings_page', wp_slash( serialize( $config['settings'] ) ) );
		}
	}
}
