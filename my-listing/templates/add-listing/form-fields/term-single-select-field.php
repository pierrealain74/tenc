<?php
/**
 * Term single select field frontend template.
 *
 * @since 1.0.0
 */

// To maintain backward compatibility, transform every terms field to a 'terms-select'.
if ( $field['type'] !== 'term-select' ) {
	$field['type'] = 'term-select';
	return require locate_template( 'templates/add-listing/form-fields/term-select-field.php' );
}
?>

<div class="cts-term-select">
	<select
		class="custom-select"
		name="<?php echo esc_attr( isset( $field['name'] ) ? $field['name'] : $key ) ?>"
		id="<?php echo esc_attr( isset( $field['name'] ) ? $field['name'] : $key ) ?>"
		<?php if ( ! empty( $field['required'] ) ) echo 'required="required"'; ?>
		<?php if ( ! empty( $field['placeholder'] ) ) echo 'data-placeholder="' . esc_attr( $field['placeholder'] ) . '"'; ?>
		data-mylisting-ajax="true"
		data-mylisting-ajax-url="mylisting_list_terms"
		data-mylisting-ajax-params="<?php echo c27()->encode_attr( [ 'taxonomy' => $field['taxonomy'], 'listing-type-id' => $type_id ] ) ?>"
	>
		<option></option>

		<?php if ( ! empty( $selected ) && $selected[0] instanceof \WP_Term ): ?>
			<option value="<?php echo esc_attr( $selected[0]->term_id ) ?>" selected="selected">
				<?php echo esc_attr( $selected[0]->name ) ?>
			</option>
		<?php endif ?>
	</select>
</div>
