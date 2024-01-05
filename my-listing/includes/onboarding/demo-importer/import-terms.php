<?php

namespace MyListing\Onboarding\Demo_Importer;

if ( ! defined('ABSPATH') ) {
	exit;
}

function import_terms() {
	$config = get_demo_file('terms.json');
	foreach ( $config as $termdata ) {
		if ( term_exists( $termdata['slug'] ) ) {
			continue;
		}

		$term_ids = wp_insert_term( $termdata['name'], $termdata['taxonomy'], [
			'description' => $termdata['description'],
			'slug' => $termdata['slug'],
		] );

		if ( ! is_wp_error( $term_ids ) ) {
			$term_id = $term_ids['term_id'];
			update_term_meta( $term_id, '__demo_import_termid', $termdata['id'] );

			if ( ! empty( $termdata['icon'] ) ) {
				update_term_meta( $term_id, 'icon', $termdata['icon'] );
			}

			if ( ! empty( $termdata['icon-type'] ) ) {
				update_term_meta( $term_id, 'icon_type', $termdata['icon-type'] );
			}

			if ( ! empty( $termdata['color'] ) ) {
				update_term_meta( $term_id, 'color', $termdata['color'] );
			}

			if ( ! empty( $termdata['listing-types'] ) ) {
				update_term_meta( $term_id, '__demo_import_types', (array) $termdata['listing-types'] );
			}

			if ( ! empty( $termdata['image'] ) && ( $attachment_id = get_imported_post_id( $termdata['image'] ) ) ) {
				update_term_meta( $term_id, 'image', $attachment_id );
			}
		}
	}
}