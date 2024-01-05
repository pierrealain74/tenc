<?php
/**
 * My Account page base layout.
 *
 * @since 1.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>

<div class="mlduo-account-menu">
	<?php do_action( 'woocommerce_account_navigation' ) ?>
	<div class="cts-prev"></div>
	<div class="cts-next"></div>
</div>

<section class="i-section">
	<div class="container section-body">
		<div class="col-md-12">
			<div class="row">
				<div class="col-md-12">
					<?php wc_print_notices() ?>
				</div>
			</div>
		</div>
		<div class="col-md-12">
			<div class="woocommerce-MyAccount-content">
				<?php do_action( 'woocommerce_account_content' ) ?>
			</div>
		</div>
	</div>
</section>