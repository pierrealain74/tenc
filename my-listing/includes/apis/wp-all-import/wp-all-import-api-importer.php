<?php

namespace MyListing\Apis\Wp_All_Import;

if ( ! defined('ABSPATH') ) {
	exit;
}

function importer( $import, $data ) {
	$post_id = $import['pid'];
	$post_data = $import['articleData'];
	$is_update = ! empty( $post_data['ID'] );
	$options = $import['import']['options'];
	$addon = $options['mylisting-addon'] ?? [];
	$addon['download_image'] = $addon['download_image'] ?? [];
	$log = $import['logger'];
	$type = \MyListing\Src\Listing_Type::get_by_name( $options['listing_type'] ?? null );
	$index = $import['i'];
	$data = $data[ $index ] ?? [];
	$data = apply_mapping_rules( $data, $addon['mapping'] ?? [] );
	$listing = \MyListing\Src\Listing::get( $post_id );

	if ( ! $type ) {
		return;
	}

	// set listing type
	update_post_meta( $post_id, '_case27_listing_type', $type->get_slug() );

	// import field data
	foreach ( $type->get_fields() as $field ) {
		$field->set_listing( $listing );

		if ( $is_update && ! can_update_meta( '_'.$field->get_key(), $options ) ) {
			continue;
		}

		$field_value = $data[ $field->get_key() ] ?? null;
		$delimiter = $data[ $field->get_key().'__wpai_delimiter' ] ?? ',';
		if ( is_null( $field_value ) ) {
			continue;
		}

		if ( $field->get_type() === 'recurring-date' ) {
			import_recurring_dates( $field, $field_value, $log );
			continue;
		}

		if ( $field->get_type() === 'related-listing' ) {
			import_related_listings( $field, $field_value, $log, $delimiter );
			continue;
		}

		if ( $field->get_type() === 'links' ) {
			import_links( $field, (array) $field_value, $log );
			continue;
		}

		if ( $field->get_type() === 'file' ) {
			$download_image = $addon['download_image'][ $field->get_key() ] === 'yes' ? 'yes' : false;
			if ( is_string( $field_value ) ) {
				$field_value = [ 'value' => $field_value ];
			}

			if ( ! is_array( $field_value ) ) {
				$field_value = [];
			}

			import_files( $field, $field_value, $log, $import, $download_image, $delimiter );
			continue;
		}

		if ( $field->get_type() === 'location' ) {
			import_location( $field, (array) $field_value, $log );
			continue;
		}

		if ( $field->get_type() === 'select-product' || $field->get_type() === 'select-products' ) {
			import_products( $field, $field_value, $log, $delimiter );
			continue;
		}

		if ( $field->get_type() === 'work-hours' ) {
			import_work_hours( $field, (array) $field_value, $log );
			continue;
		}

		if ( $field->get_type() === 'date' && ! empty( $field_value ) ) {
			$timestamp = strtotime( $field_value );
			if ( ! $timestamp ) {
				$log( sprintf( '<strong>WARNING:</strong> Invalid date supplied for "%s", skipping field.', $field->get_label() ) );
				continue;
			}

			$date_format = $field->get_prop('format') === 'datetime' ? 'Y-m-d H:i:s' : 'Y-m-d';
			$field_value = date( $date_format, $timestamp );
		}

		if ( $field->get_type() === 'number' && ! empty( $field_value ) ) {
			if ( ! is_numeric( $field_value ) ) {
				$log( sprintf( '<strong>WARNING:</strong> Non-numeric value supplied for "%s", skipping field.', $field->get_label() ) );
				continue;
			}
		}

		if ( ( $field->get_type() === 'multiselect' || $field->get_type() === 'checkbox' ) && ! empty( $field_value ) ) {
			$field_value = array_filter( array_map( 'trim', explode( $delimiter, (string) $field_value ) ) );
		}

		update_post_meta( $post_id, '_'.$field->get_key(), $field_value );
	}

	// set listing settings
	$settings = [
		// payment package must be the first option handled since other settings must be able
		// to override expiry date/verified status/priority that's set by the assigned package
		'_setting_payment_package' => '_user_package_id',
		'_setting_expiry' => '_job_expires',
		'_setting_claimed' => '_claimed',
		'_setting_priority' => '_featured',
	];

	foreach ( $settings as $setting_key => $meta_key ) {
		if ( $is_update && ! can_update_meta( $meta_key, $options ) ) {
			continue;
		}

		$field_value = $data[ $setting_key ] ?? null;
		if ( is_null( $field_value ) ) {
			continue;
		}

		// set expiry
		if ( $setting_key === '_setting_expiry' && ( $timestamp = strtotime( $field_value ) ) ) {
			update_post_meta( $post_id, '_job_expires', date( 'Y-m-d', $timestamp ) );
		}

		// set payment package
		if ( $setting_key === '_setting_payment_package' && ( $package = \MyListing\Src\Package::get( $field_value ) ) ) {
			$package_id = absint( $package->get_id() );
			$old_package_id = absint( $listing->get_package_id() );

			// only assign package if it has changed from the previous import, or if
			// the listing is getting created for the first time
			if ( ! $is_update || ( $package_id !== $old_package_id ) ) {
				$log( sprintf( '<strong>PAYMENT PACKAGE:</strong> Assigning package "%s" to listing.', $package->get_id() ) );
				$package->assign_to_listing( $listing->get_id() );
			}
		}

		// set verified status
		if ( $setting_key === '_setting_claimed' ) {
			$is_verified = in_array( strtolower( $field_value ), ['yes', 'claimed', 'verified', '1', 'true'] );
			update_post_meta( $post_id, '_claimed', $is_verified ? 1 : 0 );
		}

		// set priority
		if ( $setting_key === '_setting_priority' && is_numeric( $field_value ) ) {
			update_post_meta( $post_id, '_featured', absint( $field_value ) );
		}
	}

	// if listing is being created for the first time and no expiry date is provided, set the default expiry
	if ( ! $is_update && ! strtotime( $data['_setting_expiry'] ?? null ) ) {
		update_post_meta( $post_id, '_job_expires', \MyListing\Src\Listing::calculate_expiry( $post_id ) );
	}

	// dump($data, $addon, $options);
}
