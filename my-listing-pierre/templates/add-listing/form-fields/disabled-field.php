<?php
/**
 * Shows the `select` form field on add listing forms.
 *
 * @since 1.0
 * @var   array $field
 */

$listing_id = ! empty( $_REQUEST[ 'job_id' ] ) ? absint( $_REQUEST[ 'job_id' ] ) : 0;

$class = 'hide ml-converted-price';
$convert_price = '';

if ( $listing_id > 0 ) {
    $listing = \MyListing\Src\Listing::force_get( $listing_id );
    $price = $listing->get_field('price');
    $convert = $listing->get_field('price_converter');

    if ( $price && $convert ) {
        $class = 'ml-converted-price';
        $convert_price = ml_convert_prices( $price, trim( $convert ) );
    }
}

?>

<div class="<?php echo esc_attr( $class ); ?>" style="border-bottom-color: transparent; border-bottom-width: 2px!important;padding: 15px 0;"><span><?php echo $convert_price; ?></span></div>