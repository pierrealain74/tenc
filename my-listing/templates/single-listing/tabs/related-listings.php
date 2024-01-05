<div class="container c27-related-listings-wrapper">
    <div class="row listings-loading tab-loader">
        <div class="loader-bg">
            <?php c27()->get_partial( 'spinner', [
                'color' => '#777',
                'classes' => 'center-vh',
                'size' => 28,
                'width' => 3,
            ] ) ?>
        </div>
    </div>
    <div class="row section-body i-section">
        <div class="c27-related-listings tab-contents"></div>
    </div>
    <div class="row">
        <div class="c27-related-listings-pagination tab-pagination"></div>
    </div>
</div>