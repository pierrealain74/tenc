<?php
/**
 * Account details form template.
 *
 * @since 2.4.4
 */

use \MyListing\Src\User_Roles;

if ( ! defined('ABSPATH') ) {
	exit;
} 

?>

<div class="container ac-details-container">
	<div class="col-md-6 col-md-offset-3">
		<div class="element">
			<div class="pf-head round-icon">
				<div class="title-style-1">
					<i class="mi person user-area-icon"></i>
					<h5><?php _e( 'Add your Cryptocurrency public address for each:', 'my-listing' ) ?></h5>
				</div>
			</div>
			<div class="pf-body">
				<form class="woocommerce-EditAccountForm edit-account sign-in-form" action="" method="post">
					<fieldset>
						<?php
						foreach ( User_Roles\get_custom_fields() as $field ) {
							$field->form = $field::FORM_ACCOUNT_DETAILS;
							$field->user = wp_get_current_user();

							$field->get_form_markup();
						}
						?>
					</fieldset>

					<div class="">
						<?php wp_nonce_field( 'save_crypto_details', 'save-crypto-details-nonce' ) ?>
						<button type="submit" class="buttons button-2 full-width" name="save_crypto_details"
							value="<?php esc_attr_e( 'Save changes', 'my-listing' ) ?>">
							<?php esc_html_e( 'Save changes', 'my-listing' ) ?>
						</button>
						<input type="hidden" name="action" value="save_crypto_details" />
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
