<?php

namespace MyListing\Controllers\Maps;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Maps_Controller extends \MyListing\Controllers\Base_Controller {

	protected function dependencies() {
		require_once locate_template('includes/apis/maps/maps-api.php');
	}

	protected function hooks() {
        $this->filter( 'mylisting/localize-data', '@localize_data', 20 );
		$this->on( 'mylisting/get-footer', '@load_marker_templates', 25 );
		$this->on( 'admin_footer', '@load_marker_templates', 25 );
		$this->on( 'mylisting/submission/save-listing-data', '@frontend_listing_update', 10, 2 );
		$this->on( 'mylisting/admin/save-listing-data', '@backend_listing_update', 40, 2 );
	}

	protected function load_marker_templates() {
    	c27()->get_partial( 'marker-templates' );
	}

	protected function localize_data( $data ) {
		$data['MapConfig'] = [];

		/**
		 * Since v2.4.4, clusters can be completely disabled using
		 * `add_filter( 'mylisting/maps/cluster-size', '__return_zero' );`
		 */
		$data['MapConfig']['ClusterSize'] = apply_filters( 'mylisting/maps/cluster-size', 35 );
		return $data;
	}

	/**
	 * Geocode location on listing submit/edit through the front-end forms.
	 *
	 * @since 1.7.2
	 */
	protected function frontend_listing_update( $listing_id, $fields ) {
		if ( empty( $fields['job_location'] ) || empty( $fields['job_location']['value'] ) ) {
			return;
		}

		if ( ! empty( $_POST['job_location__latitude'] ) && ! empty( $_POST['job_location__longitude'] ) ) {
			return;
		}

		\MyListing\Apis\Maps\save_location_data( $listing_id, $fields['job_location']['value'] );
	}

	/**
	 * Geocode address when listing is created/edited through wp-admin.
	 *
	 * @since 1.7.2
	 */
	protected function backend_listing_update( $listing_id, $listing ) {
		if ( ! is_admin() || empty( $_POST['job_location'] ) ) {
			return;
		}

		if ( ! empty( $_POST['job_location__latitude'] ) && ! empty( $_POST['job_location__longitude'] ) ) {
			return;
		}

		\MyListing\Apis\Maps\save_location_data( $listing_id, sanitize_text_field( $_POST['job_location'] ) );
	}
}
