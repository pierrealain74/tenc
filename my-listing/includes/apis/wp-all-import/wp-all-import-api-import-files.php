<?php

namespace MyListing\Apis\Wp_All_Import;

if ( ! defined('ABSPATH') ) {
	exit;
}

function import_files( $field, $field_value, $log, $import, $download_image, $delimiter ) {
	$files = [];
	$urls = explode( $delimiter, $field_value['value'] ?? '' );
	$titles = explode( $delimiter, $field_value['title'] ?? '' );
	$descriptions = explode( $delimiter, $field_value['description'] ?? '' );
	$captions = explode( $delimiter, $field_value['caption'] ?? '' );
	$alts = explode( $delimiter, $field_value['alt'] ?? '' );

	foreach ( $urls as $index => $url_or_path ) {
		if ( empty( $url_or_path ) || ! is_string( $url_or_path ) ) {
			continue;
		}

		$files[ $index ] = [
			'url_or_path' => trim( $url_or_path ),
			'title' => $titles[ $index ] ?? '',
			'description' => $descriptions[ $index ] ?? '',
			'caption' => $captions[ $index ] ?? '',
			'alt' => $alts[ $index ] ?? '',
		];
	}

	$uploaded = [];
	foreach ( $files as $file ) {
		$url_or_path = $file['url_or_path'];
		$extension = substr( strrchr( $url_or_path, '.' ), 1 );
		$file_type = in_array( $extension, [ 'jpg', 'gif', 'png', 'jpeg', 'jpe' ] ) ? 'images' : 'files';

		$attachment_id = \PMXI_API::upload_image(
			$field->listing->get_id(), $url_or_path, $download_image, $log, true, '', $file_type, true, $import['articleData'], $import
		);

		$file_guid = get_the_guid( $attachment_id );
		if ( $attachment_id && ! empty( $file_guid ) ) {
			$uploaded[] = $file_guid;

			$details = array_filter( [
				'post_title' => $file['title'] ?? null,
				'post_content' => $file['description'] ?? null,
				'post_excerpt' => $file['caption'] ?? null,
			] );

			if ( ! empty( $file['alt'] ) ) {
				$details['meta_input'] = [ '_wp_attachment_image_alt' => $file['alt'] ];
			}

			if ( ! empty( $details ) ) {
				$details['ID'] = $attachment_id;
				wp_update_post( $details );
			}
		}
	}

	update_post_meta( $field->listing->get_id(), '_'.$field->get_key(), array_filter( $uploaded ) );
}
