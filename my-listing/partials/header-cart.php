<?php
/**
 * Template for displaying a cart icon and
 * contents count in site header.
 *
 * @since 1.7.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WC_Widget_Cart' ) ) {
	return false;
}

$cart_count = WC()->cart->get_cart_contents_count();
?>

<a class="view-cart-contents" href="#" type="button" id="user-cart-menu" data-toggle="modal" data-target="#wc-cart-modal" title="<?php esc_attr_e( 'View your shopping cart', 'my-listing' ); ?>">
	<span class="mi shopping_basket"></span>
	<i class="header-cart-counter <?php echo $cart_count < 1 ? 'counter-hidden' : '' ?>" data-count="<?php echo esc_attr( $cart_count ) ?>">
		<span><?php echo number_format_i18n( $cart_count ) ?></span>
	</i>
</a>