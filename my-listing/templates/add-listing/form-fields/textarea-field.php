<?php
/**
 * Shows the `textarea` form field on listing forms.
 *
 * @since 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<textarea
	cols="20" rows="3" class="input-text"
	name="<?php echo esc_attr( isset( $field['name'] ) ? $field['name'] : $key ) ?>"
	id="<?php echo esc_attr( $key ) ?>"
	placeholder="<?php echo empty( $field['placeholder'] ) ? '' : esc_attr( $field['placeholder'] ) ?>"
	maxlength="<?php echo esc_attr( ! empty( $field['maxlength'] ) ? $field['maxlength'] : '' ) ?>"
	<?php if ( ! empty( $field['required'] ) ) echo 'required'; ?>
><?php echo isset( $field['value'] ) ? esc_textarea( html_entity_decode( $field['value'] ) ) : ''; ?></textarea>
