<?php

namespace MyListing\Onboarding\Demo_Importer;

if ( ! defined('ABSPATH') ) {
	exit;
}

function import_media() {
	include_once ABSPATH . 'wp-admin/includes/file.php';
	include_once ABSPATH . 'wp-admin/includes/media.php';
	include_once ABSPATH . 'wp-admin/includes/image.php';

	$files_dir = uploads_dir('mylisting-demo-data/files/');
	$files = array_diff( scandir( $files_dir ), ['.', '..'] );
	$attachments = [];

	foreach ( $files as $filename ) {
		$filepath = $files_dir.$filename;
		$upload = wp_upload_bits( $filename, null, file_get_contents( $filepath ) );
		if ( ! empty( $upload['error'] ) ) {
			// @todo: log error mesage
			continue;
		}

		// create attachment
		$attachment_id = wp_insert_attachment( [
			'post_title' => pathinfo( $upload['file'], PATHINFO_FILENAME ),
			'guid' => $upload['url'],
			'post_mime_type' => $upload['type'],
			'post_status' => 'inherit',
		], $upload['file'] );

		if ( ! $attachment_id || is_wp_error( $attachment_id ) ) {
			continue;
		}

		// generate attachment details and sizes
		$attachments[ $attachment_id ] = $upload['file'];

		// set temporary postmeta to identify this file in other import steps
		update_post_meta( $attachment_id, '__demo_import_postid', $filename );
	}

	update_option( '__demo_import_generate_attachments', $attachments );
}

function generate_attachments() {
	include_once ABSPATH . 'wp-admin/includes/file.php';
	include_once ABSPATH . 'wp-admin/includes/media.php';
	include_once ABSPATH . 'wp-admin/includes/image.php';

	$attachments = (array) get_option( '__demo_import_generate_attachments' );
	$batch_size = apply_filters( 'mylisting/demo-import/media-batch-size', 3 );
	$process = array_slice( $attachments, 0, $batch_size, true );

	foreach ( $process as $attachment_id => $file_src ) {
		// generate attachment details and sizes
		wp_update_attachment_metadata(
			$attachment_id,
			wp_generate_attachment_metadata( $attachment_id, $file_src )
		);

		// remove this file from the list of attachemnts still to be processed
		unset( $attachments[ $attachment_id ] );
	}

	// store the remaining items in wp_options and repeat import step
	if ( ! empty( $attachments ) ) {
		update_option( '__demo_import_generate_attachments', $attachments );
		return wp_send_json_success( [
			'repeat' => true,
			'message' => sprintf( '%d remaining', count( $attachments ) ),
		] );
	}

	// all files are processed, clean up db and go to next step
	delete_option( '__demo_import_generate_attachments' );
	return wp_send_json_success();
}
