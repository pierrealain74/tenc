<?php

namespace MyListing\Onboarding;

if ( ! defined('ABSPATH') ) {
	exit;
}

add_action( 'init', function() {
	if ( ! current_user_can('administrator') ) {
		return;
	}

	require_once locate_template('includes/onboarding/demo-importer/demo-importer.php');
} );

add_action( 'admin_menu', function() {
	add_submenu_page(
		'case27/tools.php',
		'Demo Import',
		'Demo Import',
		'administrator',
		'mylisting-onboarding',
		function() {
			render_template();
		}
	);
}, 50 );

function render_template() {
	$config = [
		'demos' => \MyListing\Onboarding\Demo_Importer\get_demos(),
		'steps' => \MyListing\Onboarding\Demo_Importer\get_steps(),
		'lastDemo' => get_option('cts-demo-import'),
		'lastStep' => get_option('cts-demo-import-step'),
	];

	wp_enqueue_script('mylisting-admin-onboarding');
	printf(
		'<script type="text/javascript">var MyListing_Demo_Import_Config = %s;</script>',
		wp_json_encode( (object) $config )
	);

	require locate_template('templates/admin/onboarding/onboarding.php');
}
