<?php
/**
 * Checkout form.
 *
 * @since 2.5.3
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'woocommerce_before_checkout_form', $checkout ) ?>
<?php if ( $checkout->is_registration_required() && ! $checkout->is_registration_enabled() && ! is_user_logged_in() ): ?>
	<div class="woocommerce-info">
		<?php _e( 'An account is required to proceed.', 'my-listing' ) ?>

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

	<style type="text/css">
		/* hide other notices so the account-required notice is the main focus */
		.returning-customer-notice, .c27-form-coupon-wrapper {
			display: none;
		}
	</style>
<?php else: ?>
	<form name="checkout" method="post" class="checkout woocommerce-checkout"
		action="<?php echo esc_url( wc_get_checkout_url() ) ?>" enctype="multipart/form-data">

		<?php if ( $checkout->get_checkout_fields() ): ?>
			<?php do_action( 'woocommerce_checkout_before_customer_details' ) ?>

			<div class="col2-set" id="customer_details">
				<div class="col-1">
					<?php do_action( 'woocommerce_checkout_billing' ) ?>
				</div>

				<div class="col-2">
					<?php do_action( 'woocommerce_checkout_shipping' ) ?>
				</div>
			</div>

			<?php do_action( 'woocommerce_checkout_after_customer_details' ) ?>
		<?php endif ?>

		<?php do_action( 'woocommerce_checkout_before_order_review_heading' ) ?>

		<h3 id="order_review_heading"><?php esc_html_e( 'Your order', 'my-listing' ) ?></h3>

		<?php do_action( 'woocommerce_checkout_before_order_review' ) ?>

		<div id="order_review" class="woocommerce-checkout-review-order">
			<?php do_action( 'woocommerce_checkout_order_review' ) ?>
		</div>

		<?php do_action( 'woocommerce_checkout_after_order_review' ) ?>
	</form>

<?php endif ?>

<?php do_action( 'woocommerce_after_checkout_form', $checkout ) ?>
