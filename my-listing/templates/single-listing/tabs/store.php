<div class="container c27-products-wrapper woocommerce">
    <div class="row listings-loading store-loader">
        <div class="loader-bg">
            <?php c27()->get_partial( 'spinner', [
                'color' => '#777',
                'classes' => 'center-vh',
                'size' => 28,
                'width' => 3,
            ] ) ?>
        </div>
    </div>
    <div class="section-body">
        <ul class="c27-products products columns-3 store-contents"></ul>
    </div>
    <div class="row">
        <div class="c27-products-pagination store-pagination"></div>
    </div>
</div>