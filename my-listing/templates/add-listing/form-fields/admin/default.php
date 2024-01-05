<?php

global $thepostid;

$type = ! empty( $field['type'] ) ? $field['type'] : 'text';
$name = ! empty( $field['name'] ) ? $field['name'] : $key;
$classes = ! empty( $field['classes'] ) ? implode( ' ', (array) $field['classes'] ) : '';
$field['required'] = false; // we don't want required fields in backend edit form.
$_REQUEST[ 'job_id' ] = $thepostid; // For fields that rely on request params to retrieve current listing.

// Hidden fields should be visible in admin listing form.
if ( $type === 'hidden' ) {
	$type = 'text';
}

/**
 * First, check if there's a template for this form field made specifically for the backend listing form.
 * If there isn't, check if there's a template available for the frontend listing form, which can be used
 * in the backend too. Finally, see if WPJM can locate a template. If all fail, don't display this field type.
 */
if ( $_field_template = locate_template( 'templates/add-listing/form-fields/admin/'.$type.'.php' ) ) {
	$field_template = $_field_template;
} elseif ( $_field_template = locate_template( 'templates/add-listing/form-fields/'.$type.'-field.php' ) ) {
	$field_template = $_field_template;
} else {
	return;
}

// Check if there's a custom admin template for this form field. If not, use the same template as used in the frontend.
$field_template = locate_template( 'templates/add-listing/form-fields/admin/'.$type.'.php' )
	?: locate_template( 'templates/add-listing/form-fields/'.$type.'-field.php' );
?>

<div class="form-field ml-card listing-form-field">
	<label for="<?php echo esc_attr( $key ) ?>">
		<?php echo esc_html( $field['label'] ) ?>
		<?php if ( ! empty( $field['description'] ) ) : ?>
			<span class="tips" data-tip="<?php echo esc_attr( $field['description'] ); ?>">[?]</span>
		<?php endif; ?>
	</label>

	<div class="form-item-value">
		<?php require $field_template ?>
	</div>
</div>
