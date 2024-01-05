<?php

namespace MyListing\Src\User_Roles\Profile_Fields;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Password_Field extends Base_Profile_Field {

	protected function field_props() {
		$this->props['type'] = 'password';
		$this->props['minlength'] = '';
		$this->props['maxlength'] = '';
	}

	protected function get_posted_value() {
		$value = isset( $_POST[ $this->get_form_key() ] ) ? $_POST[ $this->get_form_key() ] : '';
		return sanitize_text_field( stripslashes( $value ) );
	}

	protected function validate() {
		$value = $this->the_posted_value();
		$this->validate_minlength();
		$this->validate_maxlength();
	}

	protected function get_editor_options() {
		$this->get_label_option();
		$this->get_description_option();
		$this->get_required_option();
		$this->get_show_in_register_option();
		$this->get_show_in_account_details_option();
	}

	public function get_form_markup() {
		$generate_password = get_option('woocommerce_registration_generate_password');
		if ( $this->key === 'password' && $this->form === static::FORM_REGISTER && $generate_password !== 'no' ) {
			return;
		} ?>

		<?php wp_enqueue_script( 'wc-password-strength-meter' ) ?>

		<?php if ( $this->key === 'password' && $this->form === static::FORM_ACCOUNT_DETAILS ): ?>
			<fieldset>
				<legend><?php _e( 'Change password', 'my-listing' ); ?></legend>
				<div class="form-group">
					<input type="password" name="password_current" id="password_current" autocomplete="off" placeholder=" ">
					<label for="password_current">
						<?php _e( 'Current password (leave blank to leave unchanged)', 'my-listing' ) ?>
					</label>
				</div>
				<div class="form-group">
					<input type="password" name="password_1" id="password_1" autocomplete="off" placeholder=" ">
					<label for="password_1"><?php _e( 'New password (leave blank to leave unchanged)', 'my-listing' ) ?></label>
				</div>
				<div class="form-group">
					<input type="password" name="password_2" id="password_2" autocomplete="off" placeholder=" ">
					<label for="password_2"><?php _e( 'Confirm new password', 'my-listing' ) ?></label>
				</div>
			</fieldset>
		<?php else: ?>
			<div class="form-group">
				<input
					type="password"
					name="<?php echo esc_attr( $this->get_form_key() ) ?>"
					id="reg_<?php echo esc_attr( $this->get_key() ) ?>"
					placeholder=" "
				>
				<label><?php echo $this->get_label() ?></label>
				<?php if ( $desc = $this->get_description() ): ?>
					<p><?php echo $desc ?></p>
				<?php endif ?>
			</div>
		<?php endif;
	}
}
