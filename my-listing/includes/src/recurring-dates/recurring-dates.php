<?php
/**
 * Adds support for recurring date fields through a custom table.
 *
 * @since 2.4
 */

namespace MyListing\Src\Recurring_Dates;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Recurring_Dates {

	private static $table_version;
	private static $current_version;

	public static function boot() {
		self::$table_version = '0.24';
		self::$current_version = get_option( 'mylisting_events_table_version' );
		self::create_table();

		require locate_template( 'includes/src/recurring-dates/recurring-dates-api.php' );
	}

	private static function create_table() {
		if ( self::$table_version === self::$current_version ) {
			return;
		}

		global $wpdb;
		$table_name = $wpdb->prefix . 'mylisting_events';
		$sql = "CREATE TABLE $table_name (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			listing_id bigint(20) unsigned NOT NULL,
			start_date datetime NOT NULL,
			end_date datetime NOT NULL,
			frequency int(11) UNSIGNED NOT NULL,
			repeat_unit enum('NONE','DAY','MONTH') NOT NULL DEFAULT 'NONE',
			repeat_end datetime NOT NULL,
			field_key varchar(96) NOT NULL,

			PRIMARY KEY (id),
				KEY (listing_id),
				KEY (start_date),
				KEY (end_date),
				KEY (repeat_end),
				KEY (field_key),

			FOREIGN KEY (listing_id)
				REFERENCES {$wpdb->posts}(ID) ON DELETE CASCADE
		);";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );

		update_option( 'mylisting_events_table_version', self::$table_version );
	}
}