<?php

namespace MyListing;

if ( ! defined('ABSPATH') ) {
	exit;
}

function is_debug_mode() {
	return defined('WP_DEBUG') && WP_DEBUG;
}

function is_dev_mode() {
	return defined('MYLISTING_DEV_MODE') && MYLISTING_DEV_MODE;
}

function is_running_tests() {
	return defined('MYLISTING_RUNNING_TESTS') && MYLISTING_RUNNING_TESTS;
}

// Load textdomain early to include strings that are localized before
// the 'after_setup_theme' is called.
load_theme_textdomain( 'my-listing', trailingslashit( get_template_directory() ) . 'languages' );

// Load classes.
require_once trailingslashit( get_template_directory() ) . 'includes/autoload.php';
