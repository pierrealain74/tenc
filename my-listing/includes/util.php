<?php

// load dev packages
if ( \MyListing\is_dev_mode() && file_exists( locate_template('vendor/autoload.php') ) ) {
	require_once locate_template('vendor/autoload.php');
}

// Debugging helper
if ( ! function_exists('mlog') ) {
	function mlog( $message = null ) {
		if ( $message !== null ) {
			return MyListing\Utils\Logger\Logger::instance()->info( $message );
		}

		return MyListing\Utils\Logger\Logger::instance();
	}
}

// Debugging helper
if ( ! function_exists('dump') ) {
	function dump() {
		call_user_func_array( [ MyListing\Utils\Logger\Logger::instance(), 'dump' ], func_get_args() );
	}
}

// Debugging helper
if ( ! function_exists('dd') ) {
	function dd() {
		call_user_func_array( [ MyListing\Utils\Logger\Logger::instance(), 'dd' ], func_get_args() );
	}
}

// Helper function for accessing mylisting\app instance.
function mylisting() {
	return MyListing\App::instance();
}

// Alias for `mylisting()->helpers()`
function c27() {
	return mylisting()->helpers();
}

// locate_template wrapper, with $data parameter for
// a standard way to pass data to templates.
function mylisting_locate_template( $__tpl, $data = [] ) {
	if ( is_array( $data ) ) {
		extract( $data );
	}

	if ( $__tpl = locate_template( $__tpl ) ) {
		require $__tpl;
	}
}

function mylisting_check_ajax_referrer( $action = 'c27_ajax_nonce', $query_arg = 'security', $die = true ) {
	if ( \MyListing\is_dev_mode() ) {
		return true;
	}

	return check_ajax_referer( $action, $query_arg, $die );
}

function mylisting_custom_taxonomies( $key = 'slug', $value = 'label' ) {
	return MyListing\Ext\Custom_Taxonomies\Custom_Taxonomies::instance()->get_custom_taxonomies_list( $key, $value );
}

/**
 * Get a settings value from WP Admin > Listings > Settings.
 *
 * @since 2.0
 */
function mylisting_get_setting( $setting ) {
	return \MyListing\Src\Admin\Settings_Screen::instance()->get_setting( $setting );
}

// include helper functions
require locate_template( 'includes/utils/utils.php' );
require locate_template( 'includes/utils/term-utils.php' );
require locate_template( 'includes/utils/preview-card-utils.php' );

require locate_template( 'includes/onboarding/onboarding.php' );

// Start.
mylisting();

// helpers
mylisting()->register( 'helpers', MyListing\Utils\Helpers::instance() );
mylisting()->register( 'logger', MyListing\Utils\Logger\Logger::instance() );

class_alias( \MyListing\Utils\Helpers::class, '\MyListing\Helpers' );
class_alias( \MyListing\Src\Listing_Type::class, '\MyListing\Ext\Listing_Types\Listing_Type' );

/**
 * Deactivate unsupported plugins.
 *
 * @since 2.1
 */
// deactivate wpjm if active
if ( class_exists( '\\WP_Job_Manager' ) ) {
	mlog()->warn( 'WP Job Manager active, deactivating...' );

	include_once ABSPATH . 'wp-admin/includes/plugin.php';
	deactivate_plugins( [ 'wp-job-manager/wp-job-manager.php' ], true );
}

// deactivate mylisting-addons if active
if ( defined( '\\CASE27_PLUGIN_DIR' ) ) {
	mlog()->warn( 'MyListing Addons active, deactivating...' );

	include_once ABSPATH . 'wp-admin/includes/plugin.php';
	deactivate_plugins( [ 'my-listing-addons/my-listing-addons.php' ], true );
}
