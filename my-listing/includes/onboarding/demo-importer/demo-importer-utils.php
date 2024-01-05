<?php

namespace MyListing\Onboarding\Demo_Importer;

if ( ! defined('ABSPATH') ) {
	exit;
}

function download_package( $package ) {
	require_once ABSPATH . 'wp-admin/includes/file.php';

	$download_to = uploads_dir('mylisting-demo-import.zip');

	// if another import file has been downloaded previously, remove it
	\MyListing\delete_directory( uploads_dir('mylisting-demo-data/') );
	@unlink( $download_to );

	// download package
	$download_file = download_url( $package, $timeout = 600 );
	if ( is_wp_error( $download_file ) ) {
		throw new \Exception( 'Download failed: '.$download_file->get_error_message() );
	}

	@copy( $download_file, $download_to );
	unlink( $download_file );
}

function unzip_package() {
	require_once ABSPATH . 'wp-admin/includes/file.php';
	WP_Filesystem();

	$package = uploads_dir('mylisting-demo-import.zip');
	$unzip_to = uploads_dir('/');

	$result = unzip_file( $package, $unzip_to );
	if ( is_wp_error( $result ) ) {
		throw new \Exception( 'Unpacking failed: '.$result->get_error_message() );
	}

	// zip file is no longer needed
	@unlink( $package );
}

/**
 * Helper; Return "uploads/" full directory path.
 *
 * @since 2.5.0
 */
function uploads_dir( $path = '' ) {
	return trailingslashit( wp_upload_dir()['basedir'] ).$path;
}

/**
 * Helper; Validate, json_decode, and return a file
 * in the demo import data.
 *
 * @since 2.5.0
 */
function get_demo_file( $file ) {
	$raw_contents = file_get_contents( uploads_dir( 'mylisting-demo-data/'.$file ) );
	$file_contents = json_decode( $raw_contents, ARRAY_A );
	if ( json_last_error() !== JSON_ERROR_NONE ) {
		throw new \Exception( 'Could not parse "'.$file.'", invalid file format.' );
	}

	return $file_contents;
}

/**
 * Helper; The original item ID is stored in `__demo_import_postid` meta on
 * import. Use this to identify imported items and retrieve their new ID
 * assigned to them. This meta key is removed once the import is finished.
 *
 * @since 2.5.0
 */
function get_imported_post_id( $import_id ) {
	global $wpdb;

	$result = $wpdb->get_col( $wpdb->prepare( "
		SELECT post_id FROM {$wpdb->postmeta}
		WHERE meta_key = '__demo_import_postid'
		AND meta_value = %s
		LIMIT 1
	", $import_id ) );

	return array_shift( $result );
}

function get_term_ids_from_slugs( $term_slugs ) {
	$terms = get_terms( [
		'orderby' => 'slug__in',
		'hide_empty' => false,
		'fields' => 'ids',
		'slug' => (array) $term_slugs,
	] );

	return ! is_wp_error( $terms ) ? (array) $terms : [];
}

function import_post_content( $content ) {
	// replace <<#filesrc:(file_id)#>> with link to file (or large size if it's an image)
	// file_id is the post id in the website the demo was exported from, or the filename
	// for attachments
	$content = preg_replace_callback( '/<<#filesrc:(?P<file_id>.*?)#>>/', function( $matches ) {
		if ( $attachment_id = get_imported_post_id( $matches['file_id'] ) ) {
			return wp_attachment_is_image( $attachment_id )
				? wp_get_attachment_image_url( $attachment_id, 'large' )
				: wp_get_attachment_url( $attachment_id );
		}

		// in case the file was not imported (unlikely), use "#" as the link href
		return '#';
	}, $content );

	// replace <<#siteurl#>> with untrailingslashit( site_url() )
	$content = str_replace( '<<#siteurl#>>', untrailingslashit( site_url() ), $content );

	return $content;
}

function import_elementor_data( $data ) {
	$data = wp_json_encode( $data );

	// replace <<#filesrc:(file_id)#>> with link to file (or full size if it's an image)
	$data = preg_replace_callback( '/<<#filesrc:(?P<file_id>.*?)#>>/', function( $matches ) {
		if ( $attachment_id = get_imported_post_id( $matches['file_id'] ) ) {
			return wp_attachment_is_image( $attachment_id )
				? wp_get_attachment_image_url( $attachment_id, 'full' )
				: wp_get_attachment_url( $attachment_id );
		}

		return '';
	}, $data );

	// replace <<#fileid:(file_id)#>> with imported attachment id
	$data = preg_replace_callback( '/<<#fileid:(?P<file_id>.*?)#>>/', function( $matches ) {
		return ( $attachment_id = get_imported_post_id( $matches['file_id'] ) )
			? $attachment_id
			: '';
	}, $data );

	// replace <<#siteurl#>> with untrailingslashit( site_url() )
	$data = str_replace( '<<#siteurl#>>', untrailingslashit( site_url() ), $data );

	return wp_slash( $data );
}
