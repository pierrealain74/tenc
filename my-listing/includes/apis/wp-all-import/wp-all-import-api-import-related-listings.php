<?php

namespace MyListing\Apis\Wp_All_Import;

if ( ! defined('ABSPATH') ) {
	exit;
}

function import_related_listings( $field, $field_value, $log, $delimiter ) {
	global $wpdb;

	$identifiers = explode( $delimiter, (string) $field_value );
	$listing_ids = [];
	foreach ( $identifiers as $identifier ) {
		$identifier = trim( $identifier );
		$listing_id = $wpdb->get_var( $wpdb->prepare( "
			SELECT {$wpdb->posts}.ID FROM {$wpdb->posts}
			WHERE {$wpdb->posts}.post_type = 'job_listing' AND (
				{$wpdb->posts}.ID = %s
				OR {$wpdb->posts}.post_name = %s
				OR {$wpdb->posts}.post_title = %s
			) LIMIT 0, 1
		", $identifier, $identifier, $identifier ) );

		if ( ! empty( $listing_id ) ) {
			$listing_ids[] = absint( $listing_id );
		}
	}

	if ( in_array( $field->get_prop('relation_type'), [ 'has_one', 'belongs_to_one' ], true ) ) {
		$listing_ids = array_shift( $listing_ids );
	}

	// workaround to manually update field with a new value
	// @todo: separate the `update` method into an api function
	$_POST[ $field->get_key() ] = $listing_ids;
	$field->update();
	unset( $_POST[ $field->get_key() ] );
}
