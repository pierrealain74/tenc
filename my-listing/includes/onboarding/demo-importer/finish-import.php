<?php

namespace MyListing\Onboarding\Demo_Importer;

if ( ! defined('ABSPATH') ) {
	exit;
}

function finish_import() {
	set_global_settings();
	regenerate_elementor_css();
	regenerate_mylisting_styles();
	regenerate_mylisting_typography();
	cleanup_database();
	cleanup_files();
	flush_rewrite_rules(true);
}

function set_global_settings() {
	update_option( 'woocommerce_enable_myaccount_registration', 'yes' );
	update_option( 'woocommerce_enable_signup_and_login_from_checkout', 'yes' );
	update_option( 'elementor_global_image_lightbox', '' );
	update_option( 'users_can_register', '1' );
	update_option( 'avatar_default', 'mylisting_user_initials' );
}

function regenerate_elementor_css() {
	if ( ! class_exists( '\Elementor\Plugin' ) ) {
		return;
	}

	$e = \Elementor\Plugin::$instance;
	if ( is_object( $e ) && is_object( $e->files_manager ) && method_exists( $e->files_manager, 'clear_cache' ) ) {
		$e->files_manager->clear_cache();
	}
}

function regenerate_mylisting_styles() {
	\MyListing\generate_dynamic_styles();
}

function regenerate_mylisting_typography() {
	$typography = (array) json_decode( get_option( 'mylisting_typography', null ), ARRAY_A );
	$styles = \MyListing\Ext\Typography\Typography::instance()->generate_styles( $typography );
    update_option( 'mylisting_typography_style', $styles );
}

function cleanup_database() {
	global $wpdb;

    // postmeta
	$wpdb->query( "DELETE FROM {$wpdb->postmeta} WHERE meta_key IN (
		'__demo_import_products',
		'__demo_import_relations',
		'__demo_import_postid'
	)" );

    // termmeta
	$wpdb->query( "DELETE FROM {$wpdb->termmeta} WHERE meta_key IN (
		'__demo_import_termid',
		'__demo_import_types'
	)" );
}

function cleanup_files() {
	\MyListing\delete_directory( uploads_dir( 'mylisting-demo-data/' ) );
}
