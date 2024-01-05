<?php

namespace MyListing\Apis\Wp_All_Import;

if ( ! defined('ABSPATH') ) {
	exit;
}

require_once locate_template('includes/apis/wp-all-import/wp-all-import-api-fields.php');
require_once locate_template('includes/apis/wp-all-import/wp-all-import-api-parser.php');
require_once locate_template('includes/apis/wp-all-import/wp-all-import-api-importer.php');
require_once locate_template('includes/apis/wp-all-import/wp-all-import-api-import-links.php');
require_once locate_template('includes/apis/wp-all-import/wp-all-import-api-import-location.php');
require_once locate_template('includes/apis/wp-all-import/wp-all-import-api-import-recurring-dates.php');
require_once locate_template('includes/apis/wp-all-import/wp-all-import-api-import-related-listings.php');
require_once locate_template('includes/apis/wp-all-import/wp-all-import-api-import-work-hours.php');
require_once locate_template('includes/apis/wp-all-import/wp-all-import-api-import-files.php');
require_once locate_template('includes/apis/wp-all-import/wp-all-import-api-import-products.php');

function get_current_import_listing_type() {
	global $wpdb;

    if ( isset( $_GET['import_id'] ) ) {
        $import_id = $_GET['import_id'];
    } elseif ( isset( $_GET['id'] ) ) {
        $import_id = $_GET['id'];
    } else {
        $import_id = 'new';
    }

	// existing import
    $result = $wpdb->get_var( $wpdb->prepare( "SELECT options FROM {$wpdb->prefix}pmxi_imports WHERE id = %d", $import_id ) );
	$options = ! empty( $result ) ? unserialize( $result ) : [];
	if ( ! empty( $options ) ) {
	    $slug = ! empty( $options['listing_type'] ) ? $options['listing_type'] : false;
		if ( $slug && ( $listing_type = \MyListing\Src\Listing_Type::get_by_name( $slug ) ) ) {
			return $listing_type;
		}

		return false;
	}

	// new import
	$result = $wpdb->get_var( $wpdb->prepare(
		"SELECT option_value FROM $wpdb->options WHERE option_name = %s",
		'_wpallimport_session_'.$import_id.'_'
	) );

	$options = ! empty( $result ) ? unserialize( $result ) : [];

	if ( ! \MyListing\str_contains( $options['custom_type'], '_listing_type_' ) ) {
		return false;
	}

	$slug = str_replace( '_listing_type_', '', $options['custom_type'] );
	if ( $slug && ( $type = \MyListing\Src\Listing_Type::get_by_name( $slug ) ) ) {
		return $type;
	}

	return false;
}

function is_import_screen() {
	$screen = get_current_screen();
	$screen_ids = ['all-import_page_pmxi-admin-import', 'all-import_page_pmxi-admin-manage'];
	return $screen && in_array( $screen->id, $screen_ids, true );
}

function apply_mapping_rules( $data, $mapping_rules ) {
	foreach ( $mapping_rules as $field_key => $rule ) {
		$rule = json_decode( $rule, true );
		if ( ! is_array( $rule ) ) {
			continue;
		}

		if ( ! isset( $data[ $field_key ] ) ) {
			continue;
		}

		$map_from = trim( $data[ $field_key ] );
		foreach ( $rule as $map_to ) {
			if ( isset( $map_to[ $map_from ] ) ) {
				$data[ $field_key ] = trim( $map_to[ $map_from ] );
			}
		}
	}

	return $data;
}

function can_update_meta( $meta_key, $options ) {
	if ( $options['update_all_data'] === 'yes' ) {
		return true;
	}

	if ( ! $options['is_update_custom_fields'] ) {
		return false;
	}

	if ( $options['update_custom_fields_logic'] === 'full_update' ) {
		return true;
	}

	if (
		$options['update_custom_fields_logic'] === 'only'
		&& ! empty( $options['custom_fields_list'] )
		&& is_array( $options['custom_fields_list'] )
		&& in_array( $meta_key, $options['custom_fields_list'], true )
	) {
		return true;
	}

	if (
		$options['update_custom_fields_logic'] === 'all_except'
		&& ( empty( $options['custom_fields_list'] ) || ! in_array( $meta_key, $options['custom_fields_list'], true ) )
	) {
		return true;
	}

	return false;
}
