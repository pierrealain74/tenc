<?php
/**
 * Term checklist field frontend template.
 *
 * @since 1.0.0
 */

// To maintain backward compatibility, transform every terms field to a 'terms-select'.
if ( $field['type'] !== 'term-select' ) {
	$field['type'] = 'term-select';
	return require locate_template( 'templates/add-listing/form-fields/term-select-field.php' );
}

$terms = \MyListing\get_terms( [
	'taxonomy' => $field['taxonomy'],
	'orderby'    => 'name',
	'order'      => 'ASC',
	'hide_empty' => false,
	'listing_type' => $type_id,
	'return_key' => 'term_id',
	'return_value' => 'name',
	'hierarchical' => true,
	'cache_time' => 24 * HOUR_IN_SECONDS,
] );

$selected_ids = array_filter( array_map( function( $term ) {
	if ( $term instanceof \WP_Term ) {
		return $term->term_id;
	}

	return null;
}, $selected ) );
?>

<ul class="c27-term-checklist">
	<?php foreach ( $terms as $term_id => $term_name ): ?>
		<li class="c27-term">
			<div class="md-checkbox">
				<input
					name="<?php echo esc_attr( isset( $field['name'] ) ? $field['name'] : $key ); ?>[]"
					type="checkbox"
					id="<?php echo esc_attr( 'term-checklist-' . $term_id ) ?>"
					value="<?php echo esc_attr( $term_id ) ?>"
					<?php checked( in_array( $term_id, $selected_ids ), true ) ?>
				>
				<label for="<?php echo esc_attr( 'term-checklist-' . $term_id ) ?>"> <?php echo esc_attr( $term_name ) ?></label>
			</div>
		</li>
	<?php endforeach ?>
</ul>
