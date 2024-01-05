<?php

namespace MyListing\Onboarding\Demo_Importer;

if ( ! defined('ABSPATH') ) {
	exit;
}

function import_pages() {
	$config = get_demo_file('pages.json');
	foreach ( (array) $config as $page ) {
		$page_id = wp_insert_post( [
			'post_type' => 'page',
			'post_title' => $page['title'],
			'post_content' => import_post_content( $page['content'] ),
			'post_status' => 'publish',
			'post_name' => $page['slug'],
			'meta_input' => [
				'_wp_page_template' => $page['template'],
				'__demo_import_postid' => $page['id'],
			],
		], true );

		if ( ! is_wp_error( $page_id ) ) {
			// import elementor page config
			if ( $page['is-elementor'] ) {
				update_post_meta( $page_id, '_elementor_edit_mode', 'builder' );
				update_post_meta( $page_id, '_elementor_page_settings', $page['elementor-page-settings'] );
				update_post_meta( $page_id, '_elementor_data', import_elementor_data( $page['elementor-data'] ) );
			}

			// import featured image
			if ( ! empty( $page['featured-image'] ) && ( $attachment_id = get_imported_post_id( $page['featured-image'] ) ) ) {
				update_post_meta( $page_id, '_thumbnail_id', $attachment_id );
			}
		}
	}
}
