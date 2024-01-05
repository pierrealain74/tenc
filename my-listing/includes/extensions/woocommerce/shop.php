<?php

namespace MyListing\Ext\WooCommerce;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Shop {
	use \MyListing\Src\Traits\Instantiatable;

	public function __construct() {
        add_filter( 'loop_shop_columns', [ $this, 'set_shop_columns' ] );
        add_filter( 'mylisting/woocommerce/shop/columns', [ $this, 'get_shop_columns' ] );
        add_action( 'mylisting/woocommerce/shop/sidebar', [ $this, 'get_shop_sidebar' ] );
        add_action( 'theme_page_templates', [ $this, 'show_shop_archive_templates' ], 2, 3 );
        add_filter( 'woocommerce_output_related_products_args', [ $this, 'related_products_args' ] );

        // Ajax cart fragments.
        add_action( 'woocommerce_add_to_cart_fragments', [ $this, 'cart_fragments' ], 30 );
	}

    /**
     * Retrieve the cart item count on cart items change,
     * and update the cart item counter in the header.
     *
     * @since 1.7.0
     */
    public function cart_fragments( $fragments ) {
        if ( WC()->cart->get_cart_contents_count() < 1 ) {
            $fragments['#user-cart-menu .header-cart-counter'] = '<i class="header-cart-counter counter-hidden"></i>';
        } else {
            $fragments['#user-cart-menu .header-cart-counter'] = sprintf(
                '<i class="header-cart-counter counter-pulse" data-count="%d"><span>%s</span></i>',
                WC()->cart->get_cart_contents_count(),
                number_format_i18n( WC()->cart->get_cart_contents_count() )
            );
        }

        return $fragments;
    }

    public function set_shop_columns() {
        return apply_filters( 'mylisting/woocommerce/shop/columns', 3 );
    }

    public function get_shop_columns( $columns ) {
        $custom_columns = get_option( 'options_shop_page_product_columns' );
        if ( isset( $custom_columns ) && intval( $custom_columns ) ) {
            $columns = $custom_columns;
        }

        return $columns;
    }

    public function get_shop_sidebar( $sidebar_name ) {
        $custom_sidebar = get_option( 'options_shop_page_sidebar' );

        // remove extra classes
        add_filter( 'dynamic_sidebar_params', [ $this, 'remove_sidebar_classes' ] );

        if ( empty( $sidebar_name ) ) {
            dynamic_sidebar( 'sidebar' );
        } else if ( isset( $custom_sidebar ) && ! empty( $custom_sidebar ) ) {
            dynamic_sidebar( $custom_sidebar );
        } else {
            dynamic_sidebar( 'sidebar' );
        }

        remove_filter( 'dynamic_sidebar_params', [ $this, 'remove_sidebar_classes' ] );
    }

    public function remove_sidebar_classes( $params ) {
        if ( isset( $params[0]['before_widget'] ) ) {
            $params[0]['before_widget'] = '<div class="element c_widget woocommerce">';
        }

        return $params;
    }

    public function show_shop_archive_templates( $page_templates, $theme, $post ) {
        global $wp_filter;

        if ( $post && function_exists('wc_get_page_id' ) && wc_get_page_id( 'shop' ) === absint( $post->ID ) ) {

            // remove woocommerce default filter to show the page templates
            foreach( $wp_filter['theme_page_templates'] as $filter_id => $filters ) {

                if ( empty( $filters ) ) {
                    continue;
                }

                foreach( $filters as $filter_name => $filter ) {

                    if ( count( $filter['function'] ) < 1 || ! is_a( $filter['function'][0], 'WC_Admin_Post_Types' ) ) {
                        continue;
                    }

                    remove_filter( 'theme_page_templates', $filter['function'] );
                }
            }
        }

        return $page_templates;
    }

    public function related_products_args( $args ) {
        $args['columns'] = 3;
        $args['posts_per_page'] = 6;
        return $args;
    }
}
