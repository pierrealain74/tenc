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

//do_action( 'woocommerce_before_edit_account_form' ); ?>

<div class="container ac-details-container">
	<div class="col-md-6 col-md-offset-3">
		<div class="element">
			<div class="pf-head round-icon">
				<div class="title-style-1">
					<i class="mi person user-area-icon"></i>
					<h5><?php _e( 'Password Change', 'my-listing' ) ?></h5>
				</div>
			</div>
			<div class="pf-body">
				<form class="woocommerce-EditAccountForm edit-account sign-in-form" action=""
			method="post" <?php do_action( 'woocommerce_edit_account_form_tag' ) ?>>

					<?php do_action( 'woocommerce_edit_account_form_start' ) ?>

					<?php
					$password_field = null;
					foreach ( User_Roles\get_used_fields( User_Roles\get_current_user_role() ) as $field ) {
						$field->form = $field::FORM_ACCOUNT_DETAILS;
						$field->user = wp_get_current_user();
						if ( ! $field->get_prop('show_in_account_details') ) {
							continue;
						}

						// show password always last in account details form
						if ( $field->get_key() === 'password' ) {
							$password_field = $field;
							continue;
						}
					}

					if ( $password_field ) {
						$password_field->get_form_markup();
					}
					?>

					<?php do_action( 'woocommerce_edit_account_form' ) ?>

					<div class="">
						<?php wp_nonce_field( 'save_account_password_details', 'save-account-password-details-nonce' ) ?>
						<button type="submit" class="buttons button-2 full-width" name="save_account_password_details"
							value="<?php esc_attr_e( 'Save changes', 'my-listing' ) ?>">
							<?php esc_html_e( 'Save changes', 'my-listing' ) ?>
						</button>
						<input type="hidden" name="action" value="save_account_password_details" />
					</div>

					<?php do_action( 'woocommerce_edit_account_form_end' ) ?>
				</form>
			</div>
		</div>
	</div>
</div>

<?php do_action( 'woocommerce_after_edit_account_form' ) ?>
