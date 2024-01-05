<?php
/**
 * Render the login and register forms in /my-account page.
 *
 * @since 1.0
 */
if ( ! defined('ABSPATH') ) {
	exit;
}

wp_enqueue_script('mylisting-auth');
do_action( 'woocommerce_before_customer_login_form' );
add_filter( 'mylisting/hide-footer', '__return_true' );
$active_form = \MyListing\Src\User_Roles\get_active_form();
$bg = c27()->get_setting('general_auth_bg'); ?>
<section>
	<div class="container-fluid sign-in-wrapper <?php echo ! $bg ? 'no-login-background' : '' ?>">
		<div class="login-container">
			<div class="login-content">
				<div class="auth-notices"><?php woocommerce_output_all_notices() ?></div>
				<?php if ( ! empty( $_GET['notice'] ) && $_GET['notice'] === 'login-required' ): ?>
					<div class="woocommerce-info">
						<?php _e( 'You must be logged in to perform this action.', 'my-listing' ) ?>
					</div>
				<?php endif ?>

				<ul class="login-tabs">
					<li class="<?php echo $active_form === 'login' ? 'active' : '' ?>">
						<h3>
							<a href="#" data-form="login"><?php _e( 'Sign in', 'my-listing' ) ?></a>
						</h3>
					</li>

			    	<?php if ( get_option( 'woocommerce_enable_myaccount_registration' ) === 'yes' ): ?>
						<li class="<?php echo $active_form === 'register' ? 'active' : '' ?>">
							<h3>
								<a href="#" data-form="register">
									<?php _e( 'Register', 'my-listing' ) ?>
								</a>
							</h3>
						</li>
					<?php endif ?>
				</ul>

				<div class="sign-in-box form-box login-form-wrap <?php echo $active_form !== 'login' ? 'hide' : '' ?>">
					<?php require locate_template('templates/auth/login-form.php') ?>

					<?php c27()->get_partial( 'spinner', [
						'color' => '#777',
						'classes' => 'center-vh',
						'size' => 24,
						'width' => 2.5,
					] ) ?>
				</div>

			   <?php if ( get_option( 'woocommerce_enable_myaccount_registration' ) === 'yes' ): ?>
					<div class="sign-in-box register-form-wrap <?php echo $active_form !== 'register' ? 'hide' : '' ?>">
						<?php require locate_template('templates/auth/register-form.php') ?>
						<?php c27()->get_partial( 'spinner', [
							'color' => '#777',
							'classes' => 'center-vh',
							'size' => 24,
							'width' => 2.5,
						] ); ?>
					</div>
				<?php endif ?>
			</div>
		</div>

		<?php if ( $bg ): ?>
			<div class="login-bg-container" style="background-image: url('<?php echo esc_url( $bg['url'] ) ?>')">
			</div>
		<?php endif ?>
	</div>
</section>

<?php do_action( 'woocommerce_after_customer_login_form' ) ?>
<?php do_action( 'mylisting/after-auth-forms' ) ?>
