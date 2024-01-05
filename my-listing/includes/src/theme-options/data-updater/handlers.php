<?php

namespace MyListing\Src\Theme_Options\Data_Updater\Handlers;

use \DateTime as DateTime;

/**
 * Cleanup transients that were used to cache Explore page
 * query results pre version 2.2.3.
 *
 * @since 2.2.3
 */
function cleanup_transients() {
	global $wpdb;

	$count = $wpdb->get_var( "
		SELECT COUNT(option_id) FROM {$wpdb->options}
			WHERE option_name LIKE ('\_transient\_mylisting\_%')
			OR option_name LIKE ('\_transient\_timeout\_mylisting\_%')
			OR option_name LIKE ('listings\_tax\_%')
	" );

	if ( $count > 0 ) {
		$wpdb->query( "
			DELETE FROM {$wpdb->options}
				WHERE option_name LIKE ('\_transient\_mylisting\_%')
				OR option_name LIKE ('\_transient\_timeout\_mylisting\_%')
				OR option_name LIKE ('listings\_tax\_%')
		" );
	}

	return $count > 0
		? sprintf( 'Removed %d unused transients from wp_options table.', $count )
		: 'No unused transients were found.';
}

/**
 * Remove some old rows left in wp_options table when WP Job Manager was used.
 *
 * @since 2.2.3
 */
function remove_unused_options() {
	global $wpdb;

	$options = [

		/**
		 * Group by version added, so we can later remove checks for
		 * really old options that are unlikely to be present in any site anymore.
		 *
		 * @since 2.2.3
		 */
		'job_manager_admin_notices',
		'job_manager_installed_terms',
		'job_manager_email_employer_expiring_job',
		'job_manager_email_admin_expiring_job',
		'job_manager_email_admin_updated_job',
		'job_manager_email_admin_new_job',
		'job_manager_jobs_page_id',
		'job_manager_job_dashboard_page_id',
		'job_manager_submit_job_form_page_id',
		'job_manager_recaptcha_label',
		'job_manager_allowed_application_method',
		'job_manager_registration_role',
		'job_manager_use_standard_password_setup_email',
		'job_manager_generate_username_from_email',
		'job_manager_enable_registration',
		'job_manager_multi_job_type',
		'job_manager_enable_types',
		'job_manager_category_filter_type',
		'job_manager_enable_default_category_multiselect',
		'job_manager_enable_categories',
		'job_manager_hide_expired_content',
		'job_manager_hide_expired',
		'job_manager_hide_filled_positions',
		'job_manager_per_page',
		'job_manager_usage_tracking_enabled',
		'job_manager_google_maps_api_key',
		'job_manager_date_format',
		'widget_widget_featured_jobs',
		'widget_widget_recent_jobs',
		'options_single_listing_menu_font_size',
		'_options_single_listing_menu_font_size',
		'options_single_listing_menu_font_weight',
		'_options_single_listing_menu_font_weight',
		'options_single_listing_content_block_title_size',
		'_options_single_listing_content_block_title_size',
		'options_single_listing_content_block_title_weight',
		'_options_single_listing_content_block_title_weight',
		'options_single_listing_content_block_font_size',
		'_options_single_listing_content_block_font_size',

		/**
		 * @since 2.4
		 */
		'job_manager_permalinks',
		'wpjm_permalinks',

		/**
		 * @since 2.4.2
		 */
		'options_product_vendors_provider',
		'_options_product_vendors_provider',
		'product_vendors_enable',
		'_product_vendors_enable',
		'product_vendors_provider',
		'_product_vendors_provider',

		/**
		 * @since 2.4.3
		 */
		'promotions_version',
		'_promotions_version',
	];

	$inline_options = join( '\',\'', $options );
	$count = $wpdb->get_var( "SELECT COUNT(option_id) FROM {$wpdb->options} WHERE option_name IN ('{$inline_options}')" );

	if ( $count > 0 ) {
		$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name IN ('{$inline_options}')" );
	}

	return $count > 0
		? sprintf( 'Removed %d unused options from wp_options table.', $count )
		: 'No unused options were found.';
}

function update_term_counts() {
	global $wpdb;

	// get listing taxonomies
	$taxonomies = array_merge( [ 'job_listing_category', 'case27_job_listing_tags', 'region' ], mylisting_custom_taxonomies( 'slug', 'slug' ) );
	$taxonomy_string = '\''.join( '\',\'', $taxonomies ).'\'';

	// run in batches to avoid crashing large databases
	$per_page = 400;
	$offset_page = ! empty( $_GET['offset_pg'] ) ? absint( $_GET['offset_pg'] ) : 0;
	$offset = $offset_page * $per_page;

	// get list of terms
	$terms = $wpdb->get_results( $wpdb->prepare( "
		SELECT t.term_id, tt.taxonomy FROM {$wpdb->terms} AS t
		INNER JOIN {$wpdb->term_taxonomy} AS tt ON t.term_id = tt.term_id
		WHERE tt.taxonomy IN ({$taxonomy_string})
		ORDER BY t.term_id ASC LIMIT %d, %d
	", $offset, $per_page ), ARRAY_A );

	// update counts for list of terms
	foreach ( $terms as $term ) {
		\MyListing\update_term_counts( $term['term_id'], $term['taxonomy'] );
	}

	// if the term count matches the batch size, there may be more terms to process
	if ( count( $terms ) === $per_page ) {
		wp_safe_redirect( add_query_arg( [
			'offset_pg' => $offset_page + 1,
			'_wpnonce' => wp_create_nonce( 'mylisting_run_updater' ),
		], admin_url( 'admin-post.php?action=mylisting_run_updater&run=update_term_counts' ) ) );
		die;
	}

	return sprintf( 'Successfully recounted %s listing taxonomy terms.', number_format_i18n( $offset + count( $terms ) ) );
}

function remove_unused_user_meta() {
	global $wpdb;

	$count = $wpdb->get_var( "
		SELECT COUNT(umeta_id) FROM {$wpdb->usermeta} WHERE meta_key = '_mylisting_stats_cache'
	" );

	if ( $count > 0 ) {
		$wpdb->query( "
			DELETE FROM {$wpdb->usermeta} WHERE meta_key = '_mylisting_stats_cache'
		" );
	}

	return $count > 0
		? sprintf( 'Removed %d unused rows from wp_usermeta table.', $count )
		: 'No unused rows were found.';
}

function migrate_listing_location() {
	global $wpdb;

	if ( ! is_locations_table_created() ) {
		return 'Error: Could not find locations table.';
	}

	$per_page = 1000;
	$offset_page = ! empty( $_GET['offset_pg'] ) ? absint( $_GET['offset_pg'] ) : 0;
	$offset = $offset_page * $per_page;

	$sql = <<<SQL
		SELECT {$wpdb->posts}.ID AS id, mt1.meta_value AS address, mt2.meta_value AS lat, mt3.meta_value AS lng
		FROM {$wpdb->posts}
			INNER JOIN {$wpdb->postmeta} AS mt1 ON( {$wpdb->posts}.ID = mt1.post_id )
			INNER JOIN {$wpdb->postmeta} AS mt2 ON( {$wpdb->posts}.ID = mt2.post_id )
			INNER JOIN {$wpdb->postmeta} AS mt3 ON( {$wpdb->posts}.ID = mt3.post_id )
			LEFT JOIN {$wpdb->prefix}mylisting_locations AS locations ON( {$wpdb->posts}.ID = locations.listing_id )
		WHERE
			mt1.meta_key = '_job_location'
			AND mt2.meta_key = 'geolocation_lat'
			AND mt3.meta_key = 'geolocation_long'
			AND {$wpdb->posts}.post_type = 'job_listing'
			AND locations.id IS NULL
		GROUP BY {$wpdb->posts}.ID
		ORDER BY {$wpdb->posts}.post_date DESC
		LIMIT {$offset}, {$per_page}
	SQL;

	$results = $wpdb->get_results( $sql, ARRAY_A );

	$rows = [];
	foreach ( (array) $results as $result ) {
		$location = [
			'address' => ! empty( $result['address'] ) ? sanitize_text_field( $result['address'] ) : null,
			'lat' => ! empty( $result['lat'] ) ? round( floatval( $result['lat'] ), 5 ) : null,
			'lng' => ! empty( $result['lng'] ) ? round( floatval( $result['lng'] ), 5 ) : null,
		];

		if ( $location['address'] && $location['lat'] && $location['lng'] ) {
			$rows[] = sprintf(
				'(%d,"%s",%s,%s)',
				absint( $result['id'] ),
				esc_sql( $location['address'] ),
				(float) $location['lat'],
				(float) $location['lng'],
			);
		}
	}

	if ( ! empty( $rows ) ) {
		$query = "INSERT INTO {$wpdb->prefix}mylisting_locations
			(listing_id, address, lat, lng) VALUES ";
		$query .= implode( ',', $rows );
		$wpdb->query( $query );
	}

    if ( count( $results ) === $per_page ) {
    	wp_safe_redirect( add_query_arg( [
    		'offset_pg' => $offset_page + 1,
    		'_wpnonce' => wp_create_nonce( 'mylisting_run_updater' ),
    	], admin_url( 'admin-post.php?action=mylisting_run_updater&run=migrate_listing_location' ) ) );
    	die;
    }

    $total_count = $offset + count( $results );

	return $total_count > 0
		? sprintf( 'Migrated %d addresses to wp_mylisting_locations table.', $total_count )
		: 'No locations to migrate found.';
}

function is_locations_table_created(){
	global $wpdb;

	$table_name = $wpdb->prefix.'mylisting_locations';

	// Select 1 from table_name will return false if the table does not exist.
	$databasetable = new \MyListing\Src\Multiple_Locations\Multiple_Locations();

	$val = $wpdb->query( "SELECT 1 FROM $table_name LIMIT 1");

	if ( $val === FALSE ) {
		return false;
	}

	return true;
}

function migrate_work_hours() {
	global $wpdb;

	if ( ! is_work_hours_table_created() ) {
		return 'Error: Could not find work hours table.';
	}

	$per_page = 1000;
	$offset_page = ! empty( $_GET['offset_pg'] ) ? absint( $_GET['offset_pg'] ) : 0;
	$offset = $offset_page * $per_page;

	$sql = <<<SQL
		SELECT {$wpdb->posts}.ID AS id, mt1.meta_value AS work_hours
		FROM {$wpdb->posts}
			INNER JOIN {$wpdb->postmeta} AS mt1 ON( {$wpdb->posts}.ID = mt1.post_id )
			LEFT JOIN {$wpdb->prefix}mylisting_workhours AS workhours ON( {$wpdb->posts}.ID = workhours.listing_id )
		WHERE
			mt1.meta_key = '_work_hours'
			AND {$wpdb->posts}.post_type = 'job_listing'
			AND workhours.id IS NULL
		GROUP BY {$wpdb->posts}.ID
		ORDER BY {$wpdb->posts}.post_date DESC
		LIMIT {$offset}, {$per_page}
	SQL;

	$results = $wpdb->get_results( $sql, ARRAY_A );

	$rows = [];
	foreach ( (array) $results as $result ) {
		$schedule = maybe_unserialize( $result['work_hours'] );

		try {
			$timezone = new \DateTimeZone( $schedule['timezone'] ?? null );
		} catch ( \Exception $e ) {
			$timezone = wp_timezone();
		}

		$ranges = \MyListing\Helpers::get_open_ranges( $schedule );
		foreach ( $ranges as $range ) {
			$rows[] = sprintf( '(%d,%d,%d,\'%s\')', absint( $result['id'] ), $range[0], $range[1], esc_sql( $timezone->getName() ) );
		}
	}

	if ( ! empty( $rows ) ) {
		$query = "INSERT INTO {$wpdb->prefix}mylisting_workhours
			(`listing_id`, `start`, `end`, `timezone`) VALUES ";
		$query .= implode( ',', $rows );
		$wpdb->query( $query );
	}

    if ( count( $results ) === $per_page ) {
    	wp_safe_redirect( add_query_arg( [
    		'offset_pg' => $offset_page + 1,
    		'_wpnonce' => wp_create_nonce( 'mylisting_run_updater' ),
    	], admin_url( 'admin-post.php?action=mylisting_run_updater&run=migrate_work_hours' ) ) );
    	die;
    }

    $total_count = $offset + count( $results );

	return $total_count > 0
		? sprintf( 'Migrated %d work hours to wp_mylisting_workhours table.', $total_count )
		: 'No work hours to migrate found.';
}

function validate_utc_time( $weekday, $listing_timezone, $time ) {

	$listing_time = explode(':', $time );
	// @TODO: return error if count < 3

	$date = new \DateTime( null, new \DateTimeZone( $listing_timezone ) );

	// Modify the date it contains
	$date->modify('next ' . $weekday);
	$date->setTime( $listing_time[0], $listing_time[1] );

	// Convert to utc time
	$date->setTimezone( new \DateTimeZone('UTC') );

	return $date;
}

function is_work_hours_table_created(){
	global $wpdb;

	$table_name = $wpdb->prefix . 'mylisting_workhours';

	// Select 1 from table_name will return false if the table does not exist.
	$databasetable = new \MyListing\Src\Work_Hours\Work_Hours();

	$val = $wpdb->query( "select 1 from $table_name LIMIT 1");

	if ( $val === FALSE ) {
		return false;
	}

	return true;
}