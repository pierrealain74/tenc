<?php

namespace MyListing\Src\User_Roles\Profile_Fields;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Textarea_Field extends Base_Profile_Field {

	protected function field_props() {
		$this->props['type'] = 'textarea';
		$this->props['minlength'] = '';
		$this->props['maxlength'] = '';
	}

	protected function get_posted_value() {
		$value = isset( $_POST[ $this->get_form_key() ] ) ? $_POST[ $this->get_form_key() ] : '';
		return wp_kses_post( trim( stripslashes( $value ) ) );
	}

	protected function validate() {
		$value = $this->the_posted_value();
		$this->validate_minlength();
		$this->validate_maxlength();
	}

	protected function get_editor_options() {
		$this->get_label_option();
		$this->get_description_option();
		$this->get_minlength_option();
		$this->get_maxlength_option();
		$this->get_required_option();
		$this->get_show_in_register_option();
		$this->get_show_in_account_details_option();
	}

	public function get_form_markup() {
		$value = html_entity_decode( $this->the_posted_value() ?: $this->get_value() ); ?>
		<div class="form-group txtar-lbl">
        	<label><?php echo $this->get_label() ?></label>
			<textarea
				name="<?php echo esc_attr( $this->get_form_key() ) ?>"
				id="reg_<?php echo esc_attr( $this->get_key() ) ?>"
				rows="2" placeholder=" "
			><?php echo esc_textarea( $value ) ?></textarea>
			<?php if ( $desc = $this->get_description() ): ?>
				<p><?php echo $desc ?></p>
			<?php endif ?>
		</div>
	<?php }
}
