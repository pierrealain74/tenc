<?php

namespace MyListing\Src\Multiple_Locations;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Multiple_Locations {
	
	private static $table_version;
	private static $current_version;

	public static function boot() {
		static::$table_version = '0.3';
		static::$current_version = get_option( 'mylisting_locations_table_version' );
		static::setup_tables();
	}

	private static function setup_tables() {
		if ( static::$table_version === static::$current_version ) {
			return;
		}

		global $wpdb;

		$table_name = $wpdb->prefix.'mylisting_locations';
		$sql = "CREATE TABLE IF NOT EXISTS $table_name (
			id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			listing_id BIGINT(20) UNSIGNED NOT NULL,
			address VARCHAR(300) NOT NULL,
			lat DECIMAL(8,5) NOT NULL,
			lng DECIMAL(8,5) NOT NULL,

			PRIMARY KEY (id),
				KEY (listing_id),
				KEY (lat),
				KEY (lng),

			FOREIGN KEY (listing_id)
				REFERENCES {$wpdb->posts}(ID) ON DELETE CASCADE
		);";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );

		update_option( 'mylisting_locations_table_version', static::$table_version );
	}
}
