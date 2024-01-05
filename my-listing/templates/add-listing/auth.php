<?php
/**
 * In listing creation flow, this template shows above the creation form.
 *
 * @since 1.6.3
 */

if ( ! defined('ABSPATH') ) {
	exit;
}

if ( is_user_logged_in() ) {
	return;
}

$account_required = mylisting_get_setting( 'submission_requires_account' );
if ( $account_required ) {
	$message = __( 'You must be logged in to post new listings.', 'my-listing' );
} elseif ( get_option( 'woocommerce_enable_myaccount_registration' ) === 'yes' ) {
	$message = __( 'You can login to your existing account or create a new one.', 'my-listing' );
} else {
	$message = __( 'If you already have an account, you can login below.', 'my-listing' );
}
?>

<div class="form-section-wrapper active" id="form-section-auth">
	<div class="element form-section">
		<div class="pf-head round-icon">
			<div class="title-style-1">
				<i class="mi account_circle"></i>
				<h5><?php _ex( 'Account', 'Add listing form', 'my-listing' ) ?></h5>
			</div>
		</div>
		<div class="pf-body">
			<fieldset class="fieldset-login_required">
				<p><?php echo $message ?></p>
				<p>
					<a href="<?php echo esc_url( \MyListing\get_login_url() ) ?>" class="buttons button-5">
						<i class="mi person"></i>
						<?php _e( 'Sign in', 'my-listing' ) ?>
					</a>
					<?php if ( get_option( 'woocommerce_enable_myaccount_registration' ) === 'yes' ): ?>
						<span><?php _e( 'or', 'my-listing' ) ?></span>
						<a href="<?php echo esc_url( \MyListing\get_register_url() ) ?>" class="buttons button-5">
							<i class="mi person"></i>
							<?php _e( 'Register', 'my-listing' ) ?>
						</a>
					<?php endif ?>
				</p>
			</fieldset>
		</div>
	</div>
</div>
