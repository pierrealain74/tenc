<?php
/**
 * Custom AJAX handler for better performance compared to admin-ajax.php
 *
 * @link  https://woocommerce.wordpress.com/2015/07/30/custom-ajax-endpoints-in-2-4/
 * @since 2.1.7
 */

namespace MyListing;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Ajax {

	public static function boot() {
		add_action( 'init', [ __CLASS__, 'define_ajax' ], 0 );
		add_action( 'template_redirect', [ __CLASS__, 'do_ajax' ], 0 );
	}

	/**
	 * Retrieve the full Ajax endpoint url for the given action.
	 *
	 * @since 2.1.7
	 */
	public static function get_endpoint( $action = '' ) {
		$url = add_query_arg( 'mylisting-ajax', 1, home_url( '/', 'relative' ) );
		if ( ! empty( $action ) ) {
			$url = add_query_arg( 'action', $action, $url );
		}

		return $url;
	}

	/**
	 * Define Ajax related constants.
	 *
	 * @since 2.1.7
	 */
	public static function define_ajax() {
		if ( empty( $_GET['mylisting-ajax'] ) ) {
			return;
		}

		if ( ! defined( 'DOING_AJAX' ) ) {
			define( 'DOING_AJAX', true );
		}

		if ( ! defined( 'MYLISTING_AJAX_HIDE_ERRORS' ) ) {
			define( 'MYLISTING_AJAX_HIDE_ERRORS', true );
		}

		// prevent malformed JSON
		if ( MYLISTING_AJAX_HIDE_ERRORS ) {
			@ini_set( 'display_errors', 0 );
			$GLOBALS['wpdb']->hide_errors();
		}

        /**
         * Prevent this request from blocking subsequent AJAX calls. Especially necessary
         * to avoid long delays because of the messages long-polling time (~20 seconds).
         *
         * @link  https://codingexplained.com/coding/php/solving-concurrent-request-blocking-in-php
         * @since 2.1.7
         */
        session_write_close();
	}

	/**
	 * Ajax request handler.
	 *
	 * @since 2.1.7
	 */
	public static function do_ajax() {
		if ( empty( $_GET['mylisting-ajax'] ) ) {
			return;
		}

		// send headers
		if ( ! headers_sent() ) {
			send_origin_headers();
			@header( 'Content-Type: text/html; charset=' . get_option( 'blog_charset' ) );
			@header( 'X-Robots-Tag: noindex' );
			send_nosniff_header();
			nocache_headers();
		} elseif ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			headers_sent( $file, $line );
			trigger_error( "Cannot set headers - headers already sent by {$file} on line {$line}", E_USER_NOTICE );
		}

		// `action` parameter is required
		if ( empty( $_REQUEST['action'] ) ) {
			wp_die();
		}

		global $wp_query;
		$wp_query->set( 'mylisting-ajax-action', sanitize_text_field( wp_unslash( $_REQUEST['action'] ) ) );
		$action = $wp_query->get( 'mylisting-ajax-action' );

		if ( is_user_logged_in() ) {
			// an action must be registered
			if ( ! has_action( "mylisting_ajax_{$action}" ) ) {
				wp_die();
			}

			status_header( 200 );
			do_action( "mylisting_ajax_{$action}" );
		} else {
			// an action must be registered
			if ( ! has_action( "mylisting_ajax_nopriv_{$action}" ) ) {
				wp_die();
			}

			status_header( 200 );
			do_action( "mylisting_ajax_nopriv_{$action}" );
		}

		wp_die();
	}
}
