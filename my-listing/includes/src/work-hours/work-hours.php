<?php

namespace MyListing\Src\Work_Hours;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Work_Hours {

	private static $table_version;
	private static $current_version;

	public static function boot() {
		static::$table_version = '0.3';
		static::$current_version = get_option( 'mylisting_work_hours_table_version' );
		static::setup_tables();
	}

	private static function setup_tables() {
		if ( static::$table_version === static::$current_version ) {
			return;
		}

		global $wpdb;

		$table_name = $wpdb->prefix . 'mylisting_workhours';
		$sql = "CREATE TABLE IF NOT EXISTS $table_name (
			`id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			`listing_id` BIGINT(20) UNSIGNED NOT NULL,
			`start` SMALLINT(5) UNSIGNED NOT NULL,
			`end` SMALLINT(5) UNSIGNED NOT NULL,
			`timezone` VARCHAR(64) NOT NULL,

			PRIMARY KEY (`id`),
				KEY (`listing_id`),
				KEY (`start`),
				KEY (`end`),

			FOREIGN KEY (`listing_id`)
				REFERENCES {$wpdb->posts}(ID) ON DELETE CASCADE
		);";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );

		update_option( 'mylisting_work_hours_table_version', static::$table_version );
	}
}