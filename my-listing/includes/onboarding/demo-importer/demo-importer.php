<?php

namespace MyListing\Onboarding\Demo_Importer;

if ( ! defined('ABSPATH') ) {
	exit;
}

function get_demos() {
	return [
		'main-demo' => [
			'name' => 'Main Demo',
			'preview_url' => 'https://main.mylistingtheme.com/',
			'zip_url' => 'https://mylisting-cdn.sfo2.cdn.digitaloceanspaces.com/demo-import/main-demo-v2.4.6.zip',
			'demo_image' => 'https://mylisting-cdn.sfo2.cdn.digitaloceanspaces.com/demo-import/main-demo-v2.4.6.jpg',
		],

		'property-demo' => [
			'name' => 'Property Demo',
			'preview_url' => 'https://property.mylistingtheme.com/',
			'zip_url' => 'https://mylisting-cdn.sfo2.cdn.digitaloceanspaces.com/demo-import/property-demo-v2.4.6.zip',
			'demo_image' => 'https://mylisting-cdn.sfo2.cdn.digitaloceanspaces.com/demo-import/property-demo-v2.4.6.jpg',
		],

		'car-demo' => [
			'name' => 'Car Demo',
			'preview_url' => 'https://car.mylistingtheme.com/',
			'zip_url' => 'https://mylisting-cdn.sfo2.cdn.digitaloceanspaces.com/demo-import/car-demo-v2.4.6.zip',
			'demo_image' => 'https://mylisting-cdn.sfo2.cdn.digitaloceanspaces.com/demo-import/car-demo-v2.4.6.jpg',
		],
	];
}

function get_steps() {
	return [
		'download-package' => _x( 'Downloading package...', 'demo-import', 'my-listing' ),
		'unzip-package' => _x( 'Unpacking...', 'demo-import', 'my-listing' ),
		'import-media' => _x( 'Importing media...', 'demo-import', 'my-listing' ),
		'generate-attachments' => _x( 'Generating attachments...', 'demo-import', 'my-listing' ),
		'import-config' => _x( 'Importing theme options...', 'demo-import', 'my-listing' ),
		'import-pages' => _x( 'Importing pages...', 'demo-import', 'my-listing' ),
		'import-types' => _x( 'Importing listing types...', 'demo-import', 'my-listing' ),
		'import-terms' => _x( 'Importing terms...', 'demo-import', 'my-listing' ),
		'import-listings' => _x( 'Importing listings...', 'demo-import', 'my-listing' ),
		'import-products' => _x( 'Importing products...', 'demo-import', 'my-listing' ),
		'import-posts' => _x( 'Importing posts...', 'demo-import', 'my-listing' ),
		'import-menus' => _x( 'Importing menus...', 'demo-import', 'my-listing' ),
		'import-widgets' => _x( 'Importing widgets...', 'demo-import', 'my-listing' ),
		'map-data' => _x( 'Mapping data...', 'demo-import', 'my-listing' ),
		'finish-up' => _x( 'Finishing up...', 'demo-import', 'my-listing' ),
	];
}

add_action( 'mylisting_ajax_import_demo', function() {
	mylisting_check_ajax_referrer();
	if ( ! current_user_can('administrator') ) {
		die;
	}

	require_once locate_template('includes/onboarding/demo-importer/demo-importer-utils.php');
	require_once locate_template('includes/onboarding/demo-importer/import-listing-types.php');
	require_once locate_template('includes/onboarding/demo-importer/import-listings.php');
	require_once locate_template('includes/onboarding/demo-importer/import-media.php');
	require_once locate_template('includes/onboarding/demo-importer/import-menus.php');
	require_once locate_template('includes/onboarding/demo-importer/import-pages.php');
	require_once locate_template('includes/onboarding/demo-importer/import-posts.php');
	require_once locate_template('includes/onboarding/demo-importer/import-products.php');
	require_once locate_template('includes/onboarding/demo-importer/import-site-config.php');
	require_once locate_template('includes/onboarding/demo-importer/import-terms.php');
	require_once locate_template('includes/onboarding/demo-importer/import-widgets.php');
	require_once locate_template('includes/onboarding/demo-importer/map-data.php');
	require_once locate_template('includes/onboarding/demo-importer/finish-import.php');

	try {
		$steps = get_steps();
		$demos = get_demos();

		$wp_upload_dir = wp_upload_dir();
		$upload_basedir = $wp_upload_dir['basedir'];
		$upload_baseurl = $wp_upload_dir['baseurl'];

		$step = ! empty( $_REQUEST['step'] ) ? $_REQUEST['step'] : null;
		$demo_key = ! empty( $_REQUEST['demo'] ) ? $_REQUEST['demo'] : null;

		if ( ! ( $step && isset( $steps[ $step ] ) ) || ! current_user_can( 'manage_options' ) ) {
			throw new \Exception( 'Couldn\'t process request.' );
		}

		if ( ! isset( $demos[ $demo_key ] ) ) {
			throw new \Exception( 'Couldn\'t import specified demo.' );
		}

		@ini_set( 'display_errors', 0 );
		wp_raise_memory_limit('admin');
		set_time_limit(120);
		update_option( 'cts-demo-import', $demo_key );

		if ( $step === 'download-package' ) {
			download_package( $demos[ $demo_key ]['zip_url'] );
			update_option( 'cts-demo-import-step', 'download-package' );
		}

		if ( $step === 'unzip-package' ) {
			unzip_package();
			update_option( 'cts-demo-import-step', 'unzip-package' );
		}

		if ( $step === 'import-media' ) {
			import_media();
			update_option( 'cts-demo-import-step', 'import-media' );
		}

		if ( $step === 'generate-attachments' ) {
			generate_attachments();
			update_option( 'cts-demo-import-step', 'generate-attachments' );
		}

		if ( $step === 'import-config' ) {
			import_site_config();
			update_option( 'cts-demo-import-step', 'import-config' );
		}

		if ( $step === 'import-pages' ) {
			import_pages();
			update_option( 'cts-demo-import-step', 'import-pages' );
		}

		if ( $step === 'import-types' ) {
			import_listing_types();
			update_option( 'cts-demo-import-step', 'import-types' );
		}

		if ( $step === 'import-terms' ) {
			import_terms();
			update_option( 'cts-demo-import-step', 'import-terms' );
		}

		if ( $step === 'import-listings' ) {
			import_listings();
			update_option( 'cts-demo-import-step', 'import-listings' );
		}

		if ( $step === 'import-products' ) {
			import_products();
			update_option( 'cts-demo-import-step', 'import-products' );
		}

		if ( $step === 'import-posts' ) {
			import_posts();
			update_option( 'cts-demo-import-step', 'import-posts' );
		}

		if ( $step === 'import-menus' ) {
			import_menus();
			update_option( 'cts-demo-import-step', 'import-menus' );
		}

		if ( $step === 'import-widgets' ) {
			import_widgets();
			update_option( 'cts-demo-import-step', 'import-widgets' );
		}

		if ( $step === 'map-data' ) {
			map_data();
			update_option( 'cts-demo-import-step', 'map-data' );
		}

		if ( $step === 'finish-up' ) {
			finish_import();
			delete_option( 'cts-demo-import-step' );
			delete_option( 'cts-demo-import' );
		}

		wp_send_json_success();
	} catch ( \Exception $e ) {
		wp_send_json_error( [
			'message' => $e->getMessage(),
		] );
	}
} );
