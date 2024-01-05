<?php

namespace MyListing\Src\User_Roles\Profile_Fields;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Checkbox_Field extends Base_Profile_Field {

	protected function field_props() {
		$this->props['type'] = 'checkbox';
		$this->props['options'] = new \stdClass;
	}

	protected function get_posted_value() {
		return isset( $_POST[ $this->get_form_key() ] )
			? array_map( 'sanitize_text_field', $_POST[ $this->get_form_key() ] )
			: [];
	}

	protected function get_editor_options() {
		$this->get_label_option();
		$this->get_description_option();
		$this->get_icon_field();
		$this->get_options_field();
		$this->get_required_option();
		$this->get_show_in_register_option();
		$this->get_show_in_account_details_option();
	}

	protected function validate() {
		$value = $this->get_posted_value();

		// maintain backwards compatibility (checkboxes only had valud of 1 or 0).
		if ( empty( $this->props['options'] ) && ( count( $value ) !== 1 || (string) $value[0] !== '1' ) ) {
			// translators: %s is the field label.
			throw new \Exception( sprintf( _x( 'Invalid value supplied for %s.', 'Add listing form', 'my-listing' ), $this->props['label'] ) );
		} else {
			$this->validateSelectedOption();
		}
	}

	public function validateSelectedOption() {
		$value = $this->get_posted_value();
		$has_options = is_array( $this->props['options'] ) && ! empty( $this->props['options'] );

		foreach ( (array) $value as $option ) {
			if ( $has_options && ! in_array( $option, array_keys( $this->props['options'] ) ) ) {
				// translators: %s is the field label.
				throw new \Exception( sprintf( _x( 'Invalid value supplied for %s.', 'Add listing form', 'my-listing' ), $this->props['label'] ) );
			}
		}
	}

	public function get_form_markup() {
		$icon = ''; 
			if( $this->get_prop('icon') ){
				$icon = 'show-icon';
			}

			$field_value = $this->the_posted_value() ?: $this->get_value();
		?>

		<div class="form-group <?php echo $icon; ?>">
			<?php

			if ( ! is_array( $this->props['options'] ) || empty( $this->props['options'] ) ) {
				$this->props['options'] = [ '1' => ! empty( $this->props['placeholder'] ) ? $this->props['placeholder'] : '' ];
			}
			?>

		<?php foreach ( $this->props['options'] as $option_key => $value ): $option_id = 'cbopt-'.\MyListing\Utils\Random_Id::generate(7); ?>

			<div class="md-checkbox">
				<input
					type="checkbox"
					name="<?php echo esc_attr( $this->get_form_key() ) ?>[]"
					id="reg_<?php echo esc_attr( $option_id ) ?>"
					value="<?php echo esc_attr( $option_key ); ?>"
					<?php checked( in_array( $option_key, ! empty( $field_value ) ? (array) $field_value : [] ), true ) ?>
				>
				<label for="reg_<?php echo esc_attr( $option_id ) ?>">
					<?php echo $value; ?>
				</label>
			</div>

		<?php endforeach; ?>

		</div>
		<?php
	}
}
