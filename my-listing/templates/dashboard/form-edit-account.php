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

do_action( 'woocommerce_before_edit_account_form' ); ?>

<div class="container ac-details-container">
	<div class="<?php echo User_Roles\user_can_switch_role() || apply_filters( 'mylisting/social-login-enabled', false ) ? 'col-md-5 col-md-offset-2' : 'col-md-6 col-md-offset-3' ?>">
		<div class="element">
			<div class="pf-head round-icon">
				<div class="title-style-1">
					<i class="mi person user-area-icon"></i>
					<h5><?php _e( 'Account details', 'my-listing' ) ?></h5>
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

						$field->get_form_markup();
					}

					if ( $password_field ) {
						$password_field->get_form_markup();
					}
					?>

					<?php do_action( 'woocommerce_edit_account_form' ) ?>

					<div class="">
						<?php wp_nonce_field( 'save_account_details', 'save-account-details-nonce' ) ?>
						<button type="submit" class="buttons button-2 full-width" name="save_account_details"
							value="<?php esc_attr_e( 'Save changes', 'my-listing' ) ?>">
							<?php esc_html_e( 'Save changes', 'my-listing' ) ?>
						</button>
						<input type="hidden" name="action" value="save_account_details" />
					</div>

					<?php do_action( 'woocommerce_edit_account_form_end' ) ?>
				</form>
			</div>
		</div>
	</div>
	<div class="col-md-3">

		<?php if ( apply_filters( 'mylisting/social-login-enabled', false ) ): ?>
			<div class="element">
				<div class="pf-head round-icon">
					<div class="title-style-1">
						<i class="mi supervisor_account"></i>
						<h5><?php _e( 'Connected Accounts', 'my-listing' ) ?></h5>
					</div>
				</div>
				<?php do_action( 'mylisting/connected-accounts-section' ) ?>
			</div>
		<?php endif ?>

		<?php if ( User_Roles\user_can_switch_role() ):
			$role = User_Roles\get_current_user_role();
			$other_role = $role === 'primary' ? 'secondary' : 'primary';
			$other_role_label = mylisting()->get( 'roles.'.$other_role.'.label' );
			$can_switch_back = mylisting()->get( 'roles.'.$other_role.'.can_switch_role' );

			if ( $can_switch_back ) {
				$switch_confirm = sprintf( __(
					'Are you sure you want to switch to a %s account?',
					'my-listing'
				), $other_role_label );
			} else {
				$switch_confirm = sprintf( __(
					'Are you sure you want to switch to a %s account? This is an irreversible action.',
					'my-listing'
				), $other_role_label );
			}

			?>
			<div class="element">
				<div class="pf-head round-icon">
					<div class="title-style-1">
						<i class="mi layers"></i>
						<h5><?php _e( 'Account type', 'my-listing' ) ?></h5>
					</div>
				</div>

				<p>
					<?php printf(
						__( 'Your current account type is <strong>%s</strong>.', 'my-listing' ),
						mylisting()->get( sprintf( 'roles.%s.label', $role ) )
					) ?>
				</p>

				<form class="switch-role-form" action="" method="post">
					<?php wp_nonce_field( 'mylisting_switch_role', 'mylisting-switch-role-nonce' ) ?>
					<button type="submit" class="buttons button-5 full-width" name="mylisting_switch_role" value="switch"
						onclick="return confirm('<?php echo esc_attr( $switch_confirm ) ?>')">
						<?php printf( __( 'Switch to %s', 'my-listing' ), $other_role_label ) ?>
					</button>
					<input type="hidden" name="action" value="mylisting_switch_role" />
			    </form>
			</div>
		<?php endif ?>
	</div>
</div>

<?php do_action( 'woocommerce_after_edit_account_form' ) ?>
