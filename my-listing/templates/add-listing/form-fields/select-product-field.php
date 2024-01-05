<?php
// Only logged in users can fill this field.
if ( ! is_user_logged_in() ) {
	return printf(
		'<br><em><small>%s</small></em>',
		_x( 'You must be logged in to add products.', 'Select product field', 'my-listing'
	) );
}

$listing_id = ! empty( $_REQUEST[ 'job_id' ] ) ? absint( $_REQUEST[ 'job_id' ] ) : 0;
$field_key = isset( $field['name'] ) ? $field['name'] : $key;

// If it's the edit listing form, then get the listing owner's ID.
// Otherwise, it's the add listing form, so we can use the logged in user id, who'll eventualle be the author.
$author_id = ( ! empty( $listing_id ) )
	? get_post_field( 'post_author', $listing_id )
	: get_current_user_id();

// Get selected product if available in edit form.
$selected = ( ! empty( $field['value'] ) && is_numeric( $field['value'] ) )
	? get_post( $field['value'] )
	: false;
?>

<select
	name="<?php echo esc_attr( $field_key ); ?>"
	id="<?php echo esc_attr( $key ) ?>"
	<?php if ( ! empty( $field['required'] ) ) echo 'required'; ?>
	class="form-control custom-select"
	data-mylisting-ajax="true"
	data-mylisting-ajax-url="mylisting_list_products"
	data-mylisting-ajax-params="<?php echo c27()->encode_attr( [ 'cts_author' => $author_id, 'product-type' => ! empty( $field['product-type'] ) ? (array) $field['product-type'] : [] ] ) ?>"
	placeholder="<?php echo esc_attr( ! empty( $field['placeholder'] ) ? $field['placeholder'] : _x( 'Select product', 'Select product field', 'my-listing' ) ) ?>"
>
	<?php if ( $selected instanceof \WP_Post ): ?>
		<option value="<?php echo esc_attr( $selected->ID ) ?>" selected="selected"><?php echo esc_attr( $selected->post_title ) ?></option>
	<?php endif ?>
</select>
