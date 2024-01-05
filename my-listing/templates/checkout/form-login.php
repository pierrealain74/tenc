<?php
/**
 * Checkout login form
 *
 * @since 2.5.3
 */
if ( ! defined('ABSPATH') ) {
	exit;
}

if ( is_user_logged_in() || get_option('woocommerce_enable_checkout_login_reminder') === 'no' ) {
	return;
} ?>

<div class="woocommerce-info returning-customer-notice">
	<?php _e( 'Returning customer?', 'my-listing' ) ?>

	<?php if ( get_option( 'woocommerce_enable_myaccount_registration' ) === 'yes' ): ?>
		<?php printf(
			__( '<a href="%s">Login</a> or <a href="%s">create an account</a>.', 'my-listing' ),
			esc_url( \MyListing\get_login_url() ),
			esc_url( \MyListing\get_register_url() )
		) ?>
	<?php else: ?>
		<a href="<?php echo esc_url( \MyListing\get_login_url() ) ?>">
			<?php _e( 'Click here to login.', 'my-listing' ) ?>
		</a>
	<?php endif ?>
</div>
