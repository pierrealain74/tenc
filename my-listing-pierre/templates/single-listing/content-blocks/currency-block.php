<?php
/**
 * Template for rendering a `code` block in single listing page.
 *
 * @since 1.0
 */
if ( ! defined('ABSPATH') ) {
	exit;
}

$price = $listing->get_field('price');
if ( empty( $price ) ) {
    $price = $listing->get_field('car-price');
}

if ( empty( $price ) ) {
    $price = $listing->get_field('prix-du-produit');
}

if ( empty( $price ) ) {
    $price = $listing->get_field('votre-prix');
}

$field = $listing->get_field('price_converter', true );
$content = $listing->get_field('price_converter');
if ( $field && empty( $content ) ) {
	$content = 'BTCEUR';
}

/*if ( empty( $price ) || empty( $content ) ) {
	return false;
}*/

$convert_price = ml_convert_prices( $price, trim( $content ) );
$prefix = ' BTC';
$image = get_stylesheet_directory_uri() . '/assets/btc.png';
if ( trim( $content ) == 'ETHEUR' ) {
    $prefix = ' ETH';
    $image = get_stylesheet_directory_uri() . '/assets/eth.png';
} else if ( trim( $content ) == 'EURUSDT' ) {
    $prefix = ' USDT';
    $image = get_stylesheet_directory_uri() . '/assets/usdt.png';
}

?>

<div class="<?php echo esc_attr( $block->get_wrapper_classes() ) ?>" id="<?php echo esc_attr( $block->get_wrapper_id() ) ?>">
	<div class="element content-block">
		<div class="pf-head">
			<div class="title-style-1">
				<i class="<?php echo esc_attr( $block->get_icon() ) ?>"></i>
				<h5><?php echo esc_html( $block->get_title() ) ?></h5>
			</div>
		</div>
		<div class="pf-body">
			<div class="crypto-thumb">
		    	<img src="<?php echo esc_url( $image ); ?>">
		    </div>
		    <div class="crypto-prices">
				<span class="bt-price"><?php echo number_format( floatval( $convert_price ), 4, '.', '' ) . $prefix; ?></span>
				<span class="cr-price"><?php echo sprintf( '(%s%s)', $price, '€' ); ?></span>
			</div>
		</div>
	</div>
</div>