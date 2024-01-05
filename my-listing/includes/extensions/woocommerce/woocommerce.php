<?php

namespace MyListing\Ext\WooCommerce;

if ( ! defined('ABSPATH') ) {
	exit;
}

class WooCommerce {
	use \MyListing\Src\Traits\Instantiatable;

	public $endpoints;

	public function __construct() {
		if ( ! class_exists( '\WooCommerce' ) ) {
			return;
		}

		$this->endpoints = Endpoints::instance();
		do_action( 'mylisting/dashboard/endpoints-init', $this->endpoints );

		$this->templates = Templates::instance();
		$this->shop = Shop::instance();
		require_once locate_template( 'includes/extensions/woocommerce/general.php' );

		// Init request handlers.
		Requests\Get_Products::instance();

        // WooCommerce scripts.
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ], 30 );

        add_filter( 'woocommerce_show_page_title', '__return_false' );

        remove_action( 'template_redirect', 'wc_disable_author_archives_for_customers' );
        remove_action( 'woocommerce_before_customer_login_form', 'woocommerce_output_all_notices', 10 );

        add_action( 'woocommerce_product_thumbnails', function() { ?>
        	<div class="wc-gallery-thumbs">
        <?php }, 1 );

        add_action( 'woocommerce_product_thumbnails', function() { ?>
        	</div>
        <?php }, 10e5 );

        $this->add_product_gallery_classes();
	}

	// Wrapper.
	public function add_dashboard_page( $page ) {
		if ( ! $this->endpoints ) {
			return false;
		}

		$this->endpoints->add_page( $page );
	}

    /**
     * Register/deregister WooCommerce scripts.
     *
     * @since 1.7.0
     */
    public function enqueue_scripts() {
        if ( ! is_user_logged_in() && get_option( 'woocommerce_enable_myaccount_registration' ) === 'yes' ) {
            wp_enqueue_script( 'wc-password-strength-meter' );
        }

        if ( is_account_page() && is_user_logged_in() ) {
            // Include charting library.
            wp_enqueue_script( 'chartist', c27()->template_uri( 'assets/vendor/chartist/chartist.js' ), [], \MyListing\get_assets_version(), true );
            wp_enqueue_style( 'chartist', c27()->template_uri( 'assets/vendor/chartist/chartist.css' ), [], \MyListing\get_assets_version() );

            // Dashboard scripts and styles.
            wp_enqueue_style( 'mylisting-dashboard' );
            wp_enqueue_script( 'mylisting-dashboard' );
        }
    }

	// Wrapper.
	public function wrap_page_in_block( $page ) {
		$this->templates->wrap_page_in_block( $page );
	}

	/**
	 * Add PhotoSwipe support in the product gallery images.
	 *
	 * @since 2.5.0
	 */
	private function add_product_gallery_classes() {
		add_filter( 'woocommerce_single_product_image_gallery_classes', function( $classes ) {
        	$classes[] = 'photoswipe-gallery';
        	return $classes;
        } );

        add_filter( 'woocommerce_gallery_image_html_attachment_image_params', function( $attrs ) {
        	$attrs['class'] .= ' photoswipe-item ';
        	return $attrs;
        } );
	}
}
