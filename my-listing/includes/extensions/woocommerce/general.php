<?php

// Add 'case27-secondary-text' to product title tag.
if ( ! function_exists( 'woocommerce_template_loop_product_title' ) ) {
    function woocommerce_template_loop_product_title() {
        echo '<h2 class="woocommerce-loop-product__title case27-primary-text">' . get_the_title() . '</h2>';
    }
}