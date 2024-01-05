<?php
/**
 * Template for displaying related-listing field in Add Listing
 * and Edit Listing forms.
 *
 * @since 1.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Only logged in users can fill this field.
if ( ! is_user_logged_in() ) {
	return printf(
		'<br><em><small>%s</small></em>',
		_x( 'You must be logged in to add related listings.', 'Related listing field', 'my-listing'
	) );
}

// If it's the edit listing form, then get the listing owner's ID.
// Otherwise, it's the add listing form, so we can use the logged in user id, who'll eventually be the author.
$author_id = ( ! empty( $_REQUEST[ 'job_id' ] ) )
	? get_post_field( 'post_author', absint( $_REQUEST[ 'job_id' ] ) )
	: get_current_user_id();

if ( $field['author_restriction'] === 'any' ) {
	$author_id = 0;
}

$is_multiple = in_array( $field['relation_type'], [ 'has_many', 'belongs_to_many' ], true );

// Get field value.
$selected = ! empty( $field['value'] ) ? get_post( $field['value'] ) : false;
$imploded_ids = implode( ',', array_map( 'absint', (array) $field['value'] ) );
if ( ! empty( $field['value'] ) && $imploded_ids ) {
	global $wpdb;
	$listings = $wpdb->get_results( "SELECT ID, post_title FROM {$wpdb->posts} WHERE post_type = 'job_listing' AND ID IN ({$imploded_ids}) ORDER BY FIELD(ID,{$imploded_ids})", ARRAY_A );
} else {
	$listings = [];
}

if ( ! empty( $listings ) && ! $is_multiple ) {
	$listings = [ array_shift( $listings ) ];
}
?>

<select
	name="<?php echo esc_attr( isset( $field['name'] ) ? $field['name'] : $key ) ?><?php echo $is_multiple ? '[]' : '' ?>"
	id="<?php echo esc_attr( $key ) ?>"
	<?php if ( ! empty( $field['required'] ) ) echo 'required'; ?>
	class="custom-select"
	<?php echo $is_multiple ? 'multiple="multiple"' : '' ?>
	data-mylisting-ajax="true"
	data-mylisting-ajax-url="mylisting_list_posts"
	data-mylisting-ajax-params="<?php echo c27()->encode_attr( [ 'cts_author' => $author_id, 'listing-type' => (array) $field['listing_type'], 'post-status' => (array) $field['status_restriction'] ] ) ?>"
	placeholder="<?php echo esc_attr( ! empty( $field['placeholder'] ) ? $field['placeholder'] : _x( 'Select listing', 'Related listing field', 'my-listing' ) ) ?>"
>
	<?php if ( ! empty( $listings ) ): ?>
		<?php foreach ( (array) $listings as $listing ): ?>
			<option value="<?php echo esc_attr( $listing['ID'] ) ?>" selected="selected"><?php echo esc_attr( $listing['post_title'] ) ?></option>
		<?php endforeach ?>
	<?php endif ?>
</select>
