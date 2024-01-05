<?php
/**
 * Dashboard `My Account` page template for users that don't have
 * permission to add or manage listings.
 *
 * @since 2.5.0
 */
if ( ! defined('ABSPATH') ) {
	exit;
} ?>

<div class="row">
	<div class="col-md-9 mlduo-welcome-message">
		<h1>
			<?php printf( _x( 'Hello, %s!', 'Dashboard welcome message', 'my-listing' ), apply_filters(
				'mylisting/dashboard/greeting/username',
				trim( $current_user->user_firstname )
					? $current_user->user_firstname
					: $current_user->user_login,
				$current_user
			) ) ?>
		</h1>
	</div>
</div>

<div class="row">
	<div class="col-md-12">
		<div class="element">
		<div class="pf-head round-icon">
			<div class="title-style-1">
				<i class="mi person user-area-icon"></i>
				<h5><?php echo __( 'Welcome', 'my-listing' ) ?></h5>
			</div>
		</div>
		<?php printf(
			__( 'From your account dashboard you can view your <a href="%1$s">recent orders</a>, manage your <a href="%2$s">shipping and billing addresses</a>, and <a href="%3$s">edit your password and account details</a>.', 'my-listing' ),
			esc_url( wc_get_endpoint_url( 'orders' ) ),
			esc_url( wc_get_endpoint_url( 'edit-address' ) ),
			esc_url( wc_get_endpoint_url( 'edit-account' ) )
		) ?>
		</div>
	</div>
</div>

<?php
// Support WooCommerce dashboard hooks.
do_action( 'woocommerce_account_dashboard' );
do_action( 'woocommerce_before_my_account' );
do_action( 'woocommerce_after_my_account' );