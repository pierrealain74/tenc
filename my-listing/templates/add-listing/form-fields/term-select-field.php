<?php
/**
 * Term select field frontend template. If 'terms-template' option is provided,
 * display terms in the requested template. Default to 'multiselect' template.
 *
 * @since 1.5.1
 */

$listing_id = ! empty( $_REQUEST[ 'job_id' ] ) ? absint( $_REQUEST[ 'job_id' ] ) : 0;
$type_slug  = ! empty( $_GET['listing_type'] ) ? sanitize_text_field( $_GET['listing_type'] ) : false;
$type_id    = 0;

// In submit listing form, get the active listing type from the url.
if ( $type_slug && ( $type = get_page_by_path( $type_slug, OBJECT, 'case27_listing_type' ) ) ) {
	$type_id = $type->ID;
}

// In edit listing form, get the active listing type from the post meta.
if ( $listing_id && ( $type = get_page_by_path( get_post_meta( $listing_id, '_case27_listing_type', true ), OBJECT, 'case27_listing_type' ) ) ) {
	$type_id = $type->ID;
}

// Get selected terms.
$selected = [];
if ( ! empty( $field['value'] ) && ! is_wp_error( $field['value'] ) ) {
	// if the field value already includes the \WP_Term objects, use that instead.
	if ( is_array( $field['value'] ) && $field['value'][0] instanceof \WP_Term ) {
		$selected = $field['value'];
	} else {
		// handles add listing page (submit->validation fails)
		$selected = get_terms( [
			'taxonomy' => $field['taxonomy'],
			'orderby' => 'include',
			'order' => 'ASC',
			'hide_empty' => false,
			'include' => $field['value'],
		] );
	}
}

if ( is_wp_error( $selected ) ) {
	return;
}

if ( ! empty( $field['terms-template'] ) && ( $template = locate_template( "templates/add-listing/form-fields/term-{$field['terms-template']}-field.php" ) ) ) {
	require $template;
} else {
	require locate_template( 'templates/add-listing/form-fields/term-multiselect-field.php' );
}
