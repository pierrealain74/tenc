<?php

$is_sidebar = false;
$shop_page_id = wc_get_page_id( 'shop' );
$featured_image = c27()->featured_image($shop_page_id, 'full');

if ( is_post_type_archive( 'product' ) ) {
    $page_template = get_page_template_slug( $shop_page_id );

    $is_sidebar = in_array($page_template, ['templates/content-sidebar.php', 'templates/sidebar-content.php' ] ) || false;
}

get_header();
?>

<section class="i-section">
    <div class="container c1 wcc">
        <div class="content-area row the-page-content">

            <?php if ( $is_sidebar && $page_template == 'templates/sidebar-content.php' ): ?>
                <div class="col-md-3 page-sidebar sidebar-widgets">
                    <?php do_action( 'mylisting/woocommerce/shop/sidebar', 'sidebar' ); ?>
                </div>
            <?php endif; ?>

            <div class="<?php echo $is_sidebar ? 'col-md-9' : 'col-md-12'; ?>">
                <?php woocommerce_content() ?>
            </div>

            <?php if ( $is_sidebar && $page_template == 'templates/content-sidebar.php' ): ?>
                <div class="col-md-3 page-sidebar sidebar-widgets">
                    <?php do_action( 'mylisting/woocommerce/shop/sidebar', 'shop-page' ); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php get_footer(); ?>