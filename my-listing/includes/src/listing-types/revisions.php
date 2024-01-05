<?php

namespace MyListing\Src\Listing_Types;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Revisions {
	use \MyListing\Src\Traits\Instantiatable;

	private $max_revisioun_count;

	public function __construct() {
		if ( ! is_admin() ) {
			return;
		}

		// once this number of revisions is reached, old items are removed
		$this->max_revisioun_count = apply_filters( 'mylisting/types/max-revision-count', 15 );

		// add meta box
		add_action( 'add_meta_boxes', [ $this, 'revisions_metabox' ] );

		// add revision on listing type save
		add_action( 'mylisting/admin/types/after-update', [ $this, 'add_revision' ] );

		// register request handlers
		add_action( 'admin_post_mylisting_revisions_rollback', [ $this, 'rollback_revision' ] );
		add_action( 'wp_ajax_mylisting_revisions_import', [ $this, 'import_revision' ] );
		add_action( 'admin_post_mylisting_revisions_export', [ $this, 'export_revision' ] );
		add_action( 'admin_post_mylisting_revisions_remove', [ $this, 'remove_revision' ] );
	}

	public function add_revision( $type_id ) {
		$type = \MyListing\Src\Listing_Type::get( $type_id );
		if ( ! $type ) {
			return;
		}

		$revisions = (array) get_post_meta( $type->get_id(), '_mylisting_item_revisions', true );
		if ( count( $revisions ) >= $this->max_revisioun_count ) {
			array_shift( $revisions );
		}

		$revisions[] = [
			'time' => current_time( 'timestamp' ),
			'author' => get_current_user_id(),
			'config' => $type->get_config()->prepare_for_editor(),
		];

		update_post_meta( $type->get_id(), '_mylisting_item_revisions', $revisions );
	}

	public function rollback_revision() {
		check_admin_referer( 'mylisting_type_revisions' );

		try {
			if ( empty( $_GET['listing_type'] ) || empty( $_GET['revision_id'] ) || ! current_user_can( 'edit_post', $_GET['listing_type'] ) ) {
				throw new \Exception( __( 'Invalid request.', 'my-listing' ) );
			}

			$type = \MyListing\Src\Listing_Type::get( $_GET['listing_type'] );
			if ( ! $type ) {
				throw new \Exception( __( 'Invalid request.', 'my-listing' ) );
			}

			$revision = false;

			// Find the requested revision.
			$revisions = (array) get_post_meta( $type->get_id(), '_mylisting_item_revisions', true );
			foreach ( $revisions as $rev ) {
				if ( ! is_array( $rev ) ||  empty( $rev['config'] ) || empty( $rev['time'] ) ) {
					continue;
				}

				if ( (string) $rev['time'] === (string) $_GET['revision_id'] ) {
					$revision = $rev;
				}
			}

			// Make sure revision exists.
			if (
				! $revision || empty( $revision['config'] )
				|| empty( $revision['config']['fields'] )
				|| empty( $revision['config']['fields']['used'] )
				|| empty( $revision['config']['settings'] )
				|| empty( $revision['config']['single'] )
				|| empty( $revision['config']['result'] )
				|| empty( $revision['config']['search'] )
			) {
				throw new \Exception( __( 'Invalid request.', 'my-listing' ) );
			}

			// rollback
            update_post_meta( $type->get_id(), 'case27_listing_type_fields', wp_slash( serialize( $revision['config']['fields']['used'] ) ) );
            update_post_meta( $type->get_id(), 'case27_listing_type_single_page_options', wp_slash( serialize( $revision['config']['single'] ) ) );
            update_post_meta( $type->get_id(), 'case27_listing_type_result_template', wp_slash( serialize( $revision['config']['result'] ) ) );
            update_post_meta( $type->get_id(), 'case27_listing_type_search_page', wp_slash( serialize( $revision['config']['search'] ) ) );
            update_post_meta( $type->get_id(), 'case27_listing_type_settings_page', wp_slash( serialize( $revision['config']['settings'] ) ) );

			// redirect back to listing type editor
			wp_safe_redirect( get_edit_post_link( $type->get_id(), '' ) );
			exit;
		} catch ( \Exception $e ) {
			wp_die( $e->getMessage() );
		}
	}

	public function import_revision() {
		check_ajax_referer( 'mylisting_type_revisions' );

		try {
			if ( empty( $_GET['listing_type'] ) || empty( $_POST['config'] ) || ! current_user_can( 'edit_post', $_GET['listing_type'] ) ) {
				throw new \Exception( __( 'Invalid request.', 'my-listing' ) );
			}

			$type = \MyListing\Src\Listing_Type::get( $_GET['listing_type'] );
			if ( ! $type ) {
				throw new \Exception( __( 'Invalid request.', 'my-listing' ) );
			}

			$config = json_decode( stripslashes( $_POST['config'] ), true );
			if ( json_last_error() !== JSON_ERROR_NONE ) {
				throw new \Exception( __( 'Invalid request.', 'my-listing' ) );
			}

			// Validate config.
			if (
				empty( $config ) || empty( $config['fields'] ) || empty( $config['fields']['used'] ) || empty( $config['settings'] )
				|| empty( $config['single'] ) || empty( $config['result'] ) || empty( $config['search'] )
			) {
				throw new \Exception( __( 'Invalid request.', 'my-listing' ) );
			}

			// update settings
            update_post_meta( $type->get_id(), 'case27_listing_type_fields', wp_slash( serialize( $config['fields']['used'] ) ) );
            update_post_meta( $type->get_id(), 'case27_listing_type_single_page_options', wp_slash( serialize( $config['single'] ) ) );
            update_post_meta( $type->get_id(), 'case27_listing_type_result_template', wp_slash( serialize( $config['result'] ) ) );
            update_post_meta( $type->get_id(), 'case27_listing_type_search_page', wp_slash( serialize( $config['search'] ) ) );
            update_post_meta( $type->get_id(), 'case27_listing_type_settings_page', wp_slash( serialize( $config['settings'] ) ) );

			wp_send_json( [
				'success' => true,
				'message' => '',
			] );
			exit;
		} catch ( \Exception $e ) {
			wp_send_json( [
				'success' => false,
				'message' => $e->getMessage(),
			] );
			exit;
		}
	}

	public function export_revision() {
		check_admin_referer( 'mylisting_type_revisions' );

		try {
			if ( empty( $_GET['listing_type'] ) || empty( $_GET['revision_id'] ) || ! current_user_can( 'edit_post', $_GET['listing_type'] ) ) {
				throw new \Exception( __( 'Invalid request.', 'my-listing' ) );
			}

			$type = \MyListing\Src\Listing_Type::get( $_GET['listing_type'] );
			if ( ! $type ) {
				throw new \Exception( __( 'Invalid request.', 'my-listing' ) );
			}

			// Check if we're exporting the current listing type settings.
			if ( $_GET['revision_id'] === 'current' ) {
				$filename = sprintf( '%s.%s.config.json', $type->get_slug(), date( 'd-m', current_time( 'timestamp' ) ) );
				$this->_force_download_json( $filename, $type->get_config()->prepare_for_editor() );
				exit;
			}

			// Find the requested revision.
			$revision = false;
			$revisions = (array) get_post_meta( $type->get_id(), '_mylisting_item_revisions', true );
			foreach ( $revisions as $rev ) {
				if ( ! is_array( $rev ) ||  empty( $rev['config'] ) || empty( $rev['time'] ) ) {
					continue;
				}

				if ( (string) $rev['time'] === (string) $_GET['revision_id'] ) {
					$revision = $rev;
				}
			}

			// Make sure revision exists.
			if ( ! $revision ) {
				throw new \Exception( __( 'Invalid request.', 'my-listing' ) );
			}

			// Export json file.
			$filename = sprintf( '%s.%s.config.json', $type->get_slug(), date( 'd-m', $revision['time'] ) );
			$this->_force_download_json( $filename, $revision['config'] );
			exit;
		} catch ( \Exception $e ) {
			wp_die( $e->getMessage() );
		}
	}

	private function _force_download_json( $filename, $config ) {
		header( 'Content-disposition: attachment; filename='.$filename );
		header( 'Content-type: application/json' );
		echo wp_json_encode( $config );
	}

	public function remove_revision() {
		check_admin_referer( 'mylisting_type_revisions' );

		try {
			if ( empty( $_GET['listing_type'] ) || empty( $_GET['revision_id'] ) || ! current_user_can( 'edit_post', $_GET['listing_type'] ) ) {
				throw new \Exception( __( 'Invalid request.', 'my-listing' ) );
			}

			$type = \MyListing\Src\Listing_Type::get( $_GET['listing_type'] );
			if ( ! $type ) {
				throw new \Exception( __( 'Invalid request.', 'my-listing' ) );
			}

			// Find the requested revision and remove it.
			$revisions = (array) get_post_meta( $type->get_id(), '_mylisting_item_revisions', true );
			foreach ( $revisions as $key => $revision ) {
				if ( ! is_array( $revision ) || empty( $revision['time'] ) || empty( $revision['config'] )) {
					unset( $revisions[ $key ] );
					continue;
				}

				if ( (string) $revision['time'] === (string) $_GET['revision_id'] ) {
					unset( $revisions[ $key ] );
				}
			}

			// update revisions in database
			update_post_meta( $type->get_id(), '_mylisting_item_revisions', $revisions );

			// redirect back to listing type editor
			wp_safe_redirect( get_edit_post_link( $type->get_id(), '' ) );
			exit;
		} catch ( \Exception $e ) {
			wp_die( $e->getMessage() );
		}
	}

	private function _get_action_url( $action, $listing_type_id, $revision_id ) {
		return add_query_arg( [
			'action' => $action,
			'listing_type' => $listing_type_id,
			'revision_id' => $revision_id,
		], wp_nonce_url( admin_url( 'admin-post.php' ), 'mylisting_type_revisions' ) );
	}

	/**
	 * Add a sidebar metabox in listing type edit pag,
	 * showcasing the listing type revisions.
	 *
	 * @since 1.7.0
	 */
	public function revisions_metabox() {
		// Metabox settings.
		$id       = 'cts_listing_type_revisions';
		$title    = _x( 'Config', 'Listing type editor', 'my-listing' );
		$callback = [ $this, 'revisions_metabox_content' ];
		$screen   = 'case27_listing_type';
		$context  = 'side';
		$priority = 'default';

		// Add metabox.
		add_meta_box( $id, $title, $callback, $screen, $context, $priority );
	}

	/**
	 * The sidebar settings box contents.
	 *
	 * @since 1.7.0
	 */
	public function revisions_metabox_content( $post ) {
		$type = \MyListing\Src\Listing_Type::get( $post );
		$revisions = (array) get_post_meta( $type->get_id(), '_mylisting_item_revisions', true );

		require locate_template( 'templates/admin/listing-types/revisions.php' );
	}
}