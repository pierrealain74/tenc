<?php

namespace MyListing\Onboarding\Demo_Importer;

if ( ! defined('ABSPATH') ) {
	exit;
}

function import_listings() {
	$config = get_demo_file('listings.json');
	foreach ( (array) $config as $listingdata ) {
		$listing_id = wp_insert_post( [
			'post_type' => 'job_listing',
			'post_title' => $listingdata['title'],
			'post_content' => $listingdata['description'],
			'post_status' => 'publish',
			'post_name' => $listingdata['slug'],
			'post_date' => $listingdata['post_date'],
			'meta_input' => [
				'__demo_import_postid' => $listingdata['id'],
				'_case27_listing_type' => $listingdata['type'],
				'_job_expires' => $listingdata['expires'],
				'_job_duration' => $listingdata['duration'],
				'_claimed' => $listingdata['verified'] ? '1' : '0',
				'_featured' => $listingdata['priority'],
				'geolocation_lat' => $listingdata['latitude'],
				'geolocation_long' => $listingdata['longitude'],
			],
		], true );

		if ( ! is_wp_error( $listing_id ) && ( $listing = \MyListing\Src\Listing::get( $listing_id ) ) ) {

			// import listing field values
			foreach ( $listingdata['fields'] as $field_key => $field_value ) {
				$field = $listing->get_field_object( $field_key );

				// skip ui fields
				if ( ! $field || $field->get_prop('is_ui') ) {
					continue;
				}

				// skip title and description as those are included with the other basic listing data
				if ( $field->get_key() === 'job_title' || $field->get_key() === 'job_description' ) {
					continue;
				}

				// file fields
				if ( $field->get_type() === 'file' ) {
					$file_guids = [];
					foreach ( (array) $field_value as $file_id ) {
						if ( $attachment_id = get_imported_post_id( $file_id ) ) {
							$file_guids[] = get_the_guid( $attachment_id );
						}
					}

					$file_guids = array_filter( $file_guids );
					if ( ! empty( $file_guids ) ) {
						update_post_meta( $listing->get_id(), '_'.$field->get_key(), $file_guids );
					}
					continue;
				}

				// term fields
				if ( $field->get_type() === 'term-select' ) {
					wp_set_object_terms( $listing->get_id(), (array) $field_value, $field->get_prop('taxonomy') );
					continue;
				}

				// related listing fields
				if ( $field->get_type() === 'related-listing' ) {
					$relations = get_post_meta( $listing->get_id(), '__demo_import_relations', true );
					if ( ! is_array( $relations ) ) {
						$relations = [];
					}

					$relations[] = [
						'key' => $field->get_key(),
						'type' => $field->get_prop('relation_type'),
						'values' => (array) $field_value,
					];

					update_post_meta( $listing->get_id(), '__demo_import_relations', $relations );
					continue;
				}

				// event/recurring-date fields
				if ( $field->get_type() === 'recurring-date' ) {
					\MyListing\Src\Recurring_Dates\update_field( $field, $field_value );
					continue;
				}

				// select product/products fields
				if ( $field->get_type() === 'select-product' || $field->get_type() === 'select-products' ) {
					$products = get_post_meta( $listing->get_id(), '__demo_import_products', true );
					if ( ! is_array( $products ) ) {
						$products = [];
					}

					$products[] = [
						'key' => $field->get_key(),
						'type' => $field->get_type(),
						'values' => (array) $field_value,
					];

					update_post_meta( $listing->get_id(), '__demo_import_products', $products );
					continue;
				}

				// other field values are stored in postmeta
				update_post_meta( $listing->get_id(), '_'.$field->get_key(), $field_value );
			}
		}
	}
}
