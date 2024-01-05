<?php

namespace MyListing\Src\Endpoints;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Restaurant_Menu_Endpoint {

	public function __construct() {
		add_action( 'wp_ajax_mylisting_repeater_upload_file', [ $this, 'handle_upload' ] );

		// only enable the endpoint for logged-out users if listing submission doesn't require an account
		if ( ! mylisting_get_setting( 'submission_requires_account' ) ) {
			add_action( 'wp_ajax_nopriv_mylisting_repeater_upload_file', [ $this, 'handle_upload' ] );
		}
	}

	/**
	 * Uploads a file on an Ajax request.
	 *
	 * @since 2.1
	 */
	public function handle_upload() {
		mylisting_check_ajax_referrer();

		$files = [];
		$file_uploader = \MyListing\Utils\File_Uploader::instance();

		if ( ! empty( $_FILES ) ) {
			foreach ( $_FILES as $file_key => $file ) {

				$files_to_upload = $this->prepare( $file_key );

				foreach ( $files_to_upload as $file_to_upload ) {
					$fieldkey = array_search( $file_to_upload['filekey'], \MyListing\Src\Listing::$aliases ) ?: $file_to_upload['filekey'];

					try {
						$uploaded_file = $file_uploader->upload( $file_to_upload, [
							'upload_dir' => 'listing-uploads/'.substr( sanitize_key( $fieldkey ), 0, 32 ),
						] );

						$attachment_id = $this->create_attachment( $uploaded_file );
						if ( ! $attachment_id ) {
							continue;
						}

						$uploaded_file->attachment_url = wp_get_attachment_url( $attachment_id );
						$uploaded_file->guid = get_the_guid( $attachment_id );
						$uploaded_file->encoded_guid = $uploaded_file->guid;
						$files[] = $uploaded_file;
					} catch ( \Exception $e ) {
						$files[] = [ 'error' => $e->getMessage() ];
					}
				}
			}
		}

		wp_send_json( [ 'files' => $files ] );
	}

	public function prepare( $filekey ) {
		$prepared = [];

		if ( empty( $_FILES[ $filekey ] ) || ! is_array( $_FILES[ $filekey ] ) ) {
			return $prepared;
		}

		$files = $_FILES[ $filekey ];

		$file_array = [];

		// single file upload
		if ( is_string( $files['name'] ) ) {
			$files['type'] = wp_check_filetype( $files['name'] )['type'];
			$files['filekey'] = $filekey;
			$prepared[] = $files;
		}

		// multiple file upload
		if ( is_array( $files['name'] ) ) {
			foreach ( $files['name'] as $key => $name ) {

				if ( ! isset( $name['mylisting_accordion_photo'] ) ) {
					continue;
				}

				$file_array['name'] = $name['mylisting_accordion_photo'];
			}

			foreach ( $files['type'] as $key => $name ) {

				if ( ! isset( $name['mylisting_accordion_photo'] ) ) {
					continue;
				}

				$file_array['type'] = $name['mylisting_accordion_photo'];
			}

			foreach ( $files['tmp_name'] as $key => $name ) {

				if ( ! isset( $name['mylisting_accordion_photo'] ) ) {
					continue;
				}

				$file_array['tmp_name'] = $name['mylisting_accordion_photo'];
			}

			foreach ( $files['error'] as $key => $name ) {

				if ( ! isset( $name['mylisting_accordion_photo'] ) ) {
					continue;
				}

				$file_array['error'] = $name['mylisting_accordion_photo'];
			}

			foreach ( $files['size'] as $key => $name ) {

				if ( ! isset( $name['mylisting_accordion_photo'] ) ) {
					continue;
				}

				$file_array['size'] = $name['mylisting_accordion_photo'];
			}

			if ( is_array( $file_array ) && ! empty( $file_array ) ) {
				$prepared[] = [
					'name' => $file_array['name'],
					'type' => wp_check_filetype( $file_array['name'] )['type'],
					'tmp_name' => $file_array['tmp_name'],
					'error' => $file_array['error'],
					'size' => $file_array['size'],
					'filekey' => $filekey,
				];
			}
		}

		return $prepared;
	}

	/**
	 * Creates a file attachment.
	 *
	 * @since 2.1
	 */
	private function create_attachment( $uploaded_file ) {
		global $wpdb;

		// @todo: cron job to check for old preview attachments, delete them
		$attachment_id = wp_insert_attachment( [
			'post_title' => $uploaded_file->name,
			'post_content' => '',
			'post_mime_type' => $uploaded_file->type,
			'guid' => $uploaded_file->url,
		], $uploaded_file->file );

		if ( ! $attachment_id || is_wp_error( $attachment_id ) ) {
			return false;
		}

		// generate attachment
		wp_update_attachment_metadata(
			$attachment_id,
			wp_generate_attachment_metadata( $attachment_id, $uploaded_file->file )
		);

		// update attachment status to `preview` (not supported by WordPress through wp_insert_attachment)
		$wpdb->update( $wpdb->posts, [ 'post_status' => 'preview' ], $where = [ 'ID' => $attachment_id ] );

		return $attachment_id;
	}
}