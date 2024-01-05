<?php
/**
 * Adds ability for listings to be related to one another via a custom table,
 * supporting one-to-one, one-to-many, and many-to-many relations.
 *
 * @since 2.2
 */

namespace MyListing\Src\Related_Listings;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Related_Listings {

	private $table_version, $current_version;

    public static function boot() {
        new self;
    }

	public function __construct() {
		$this->table_version = '0.56';
		$this->current_version = get_option( 'mylisting_relations_table_version' );
		$this->setup_tables();

		// maybe run data migration
		Data_Migration::instance();
	}

	private function setup_tables() {
		if ( $this->table_version === $this->current_version ) {
			return;
		}

		global $wpdb;
		$table_name = $wpdb->prefix . 'mylisting_relations';
		$sql = "CREATE TABLE $table_name (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			parent_listing_id bigint(20) unsigned NOT NULL,
			child_listing_id bigint(20) unsigned NOT NULL,
			field_key varchar(96) NOT NULL,
			item_order bigint(20) unsigned NOT NULL,
			PRIMARY KEY (id),
			KEY (parent_listing_id),
			KEY (child_listing_id),
			KEY (field_key),
			KEY (item_order),
			FOREIGN KEY (parent_listing_id) REFERENCES {$wpdb->posts}(ID) ON DELETE CASCADE,
			FOREIGN KEY (child_listing_id) REFERENCES {$wpdb->posts}(ID) ON DELETE CASCADE
		);";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );

		update_option( 'mylisting_relations_table_version', $this->table_version );
	}
}