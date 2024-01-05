<?php

namespace MyListing\Onboarding\Demo_Importer;

if ( ! defined('ABSPATH') ) {
	exit;
}

function import_posts() {
	$config = get_demo_file('posts.json');
	foreach ( (array) $config as $post ) {
		$post_id = wp_insert_post( [
			'post_type' => 'post',
			'post_title' => $post['title'],
			'post_content' => import_post_content( $post['content'] ),
			'post_status' => 'publish',
			'post_name' => $post['slug'],
			'meta_input' => [
				'_wp_page_template' => $post['template'],
				'__demo_import_postid' => $post['id'],
			],
		], true );

		if ( ! is_wp_error( $post_id ) ) {
			// set featured image
			if ( ! empty( $post['featured-image'] ) && ( $attachment_id = get_imported_post_id( $post['featured-image'] ) ) ) {
				update_post_meta( $post_id, '_thumbnail_id', $attachment_id );
			}

			// set categories
			if ( ! empty( $post['categories'] ) ) {
				wp_set_object_terms( $post_id, (array) $post['categories'], 'category' );
			}

			// set tags
			if ( ! empty( $post['tags'] ) ) {
				wp_set_object_terms( $post_id, (array) $post['tags'], 'post_tag' );
			}
		}
	}
}
