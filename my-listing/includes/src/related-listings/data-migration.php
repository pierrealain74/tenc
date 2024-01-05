<?php
/**
 * Related listings were previously stored in `wp_postmeta` table.
 * This will move data to the custom `wp_mylisting_relations` table.
 *
 * @since 2.2
 */

namespace MyListing\Src\Related_Listings;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Data_Migration {
	use \MyListing\Src\Traits\Instantiatable;

	public function __construct() {
		if ( ! is_admin() || get_option( 'mylisting_relations_migration_completed' ) ) {
			return;
		}

		add_action( 'init', [ $this, 'run_migration' ] );
	}

	public function run_migration() {
		global $wpdb;

		mlog()->note( 'Migrating related listings from wp_postmeta to wp_mylisting_relations.' );

		$rows = $wpdb->get_results( "SELECT post_id, meta_value FROM {$wpdb->postmeta} WHERE meta_key = '_related_listing'", ARRAY_A );

		foreach ( $rows as $row ) {
			$parent_id = absint( $row['meta_value'] );
			$child_id = absint( $row['post_id'] );
			if ( ! ( $parent_id && $child_id ) ) {
				continue;
			}

			$wpdb->insert( $wpdb->prefix.'mylisting_relations', [
				'parent_listing_id' => $parent_id,
				'child_listing_id' => $child_id,
				'field_key' => 'related_listing',
				'item_order' => 0,
			] );
		}

		update_option( 'mylisting_relations_migration_completed', 1 );
	}
}
