<?php
/**
 * User registration form template.
 *
 * @since 1.0
 */
if ( ! defined('ABSPATH') ) {
	exit;
} ?>
<form class="sign-in-form register mylisting-register" method="POST"
	action="<?php echo esc_url( wc_get_page_permalink('myaccount') ) ?>" enctype="multipart/form-data">

	<?php if ( mylisting()->get('roles.secondary.enabled') ):
		$choices = mylisting()->get('roles.default_form') === 'secondary' ? ['secondary', 'primary'] : ['primary', 'secondary'] ?>
		<p class="choose-role-text"><?php _e( 'Choose role', 'my-listing' ) ?></p>
		<div class="role-tabs">
			<?php foreach ( $choices as $role_key ): ?>
				<div class="md-checkbox">
					<input
						type="radio"
						name="mylisting_user_role"
						id="mylisting_user_role-<?php echo esc_attr( $role_key ) ?>"
						value="<?php echo esc_attr( $role_key ) ?>"
						<?php checked( \MyListing\Src\User_Roles\get_posted_role(), $role_key ) ?>
					>
					<label for="mylisting_user_role-<?php echo esc_attr( $role_key ) ?>">
						<?php echo mylisting()->get('roles.'.$role_key.'.label') ?>
					</label>
				</div>
			<?php endforeach ?>
		</div>
	<?php endif ?>

	<?php do_action( 'woocommerce_register_form_start' ); ?>

	<div class="primary-role-fields">
		<?php foreach ( \MyListing\Src\User_Roles\get_used_fields('primary') as $field ):
			if ( ! $field->get_prop('show_in_register_form') ) {
				continue;
			} ?>
			<?php $field->form = $field::FORM_REGISTER ?>
				<div class="fields-wrapper">
					<?php $field->get_form_markup() ?>
				</div>
		<?php endforeach ?>
	</div>

	<?php if ( mylisting()->get('roles.secondary.enabled') ): ?>
		<div class="secondary-role-fields">
			<?php foreach ( \MyListing\Src\User_Roles\get_used_fields('secondary') as $field ):
				if ( ! $field->get_prop('show_in_register_form') ) {
					continue;
				} ?>
				<?php $field->form = $field::FORM_REGISTER ?>
					<div class="fields-wrapper">
						<?php $field->get_form_markup() ?>
					</div>
			<?php endforeach ?>
		</div>
	<?php endif ?>

	<?php do_action( 'woocommerce_register_form' ); ?>

	<?php if ( mylisting()->get('roles.register_captcha') ): ?>
		<?php \MyListing\display_recaptcha() ?>
	<?php endif ?>

	<div class="form-group">
		<?php wp_nonce_field( 'woocommerce-register', 'woocommerce-register-nonce' ); ?>
		<button type="submit" class="buttons button-2 full-width" name="register" value="Register">
			<i class="mi person user-area-icon"></i>
			<?php _e( 'Sign Up', 'my-listing' ) ?>
		</button>
	</div>

	<?php do_action( 'woocommerce_register_form_end' ); ?>
</form>
