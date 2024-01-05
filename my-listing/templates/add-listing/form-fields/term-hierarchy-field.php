<?php
/**
 * Term hierarchy field frontend template.
 *
 * @since 2.1
 */
if ( ! defined('ABSPATH') ) {
	exit;
}

$fieldkey = isset( $field['name'] ) ? $field['name'] : $key;
$selected_tree = [];
if ( ! empty( $selected ) && $selected[0] instanceof \WP_Term ) {
	$term_list = array_reverse( get_ancestors( $selected[0]->term_id, $field['taxonomy'], 'taxonomy' ) );
	$term_list[] = $selected[0]->term_id;

	foreach ( $term_list as $term_id ) {
		$term = get_term( $term_id );
		$selected_tree[] = [
			'value' => $term->term_id,
			'label' => $term->name,
		];
	}
} ?>
<div class="cts-term-hierarchy">
	<input
		type="text" class="term-hierarchy-input"
		data-selected="<?php echo c27()->encode_attr( $selected_tree ); ?>"
		name="<?php echo esc_attr( $fieldkey ) ?>"
		id="<?php echo esc_attr( $fieldkey ) ?>"
		<?php if ( ! empty( $field['placeholder'] ) ) echo 'data-placeholder="' . esc_attr( $field['placeholder'] ) . '"'; ?>
		data-mylisting-ajax-params="<?php echo c27()->encode_attr( [
			'taxonomy' => $field['taxonomy'],
			'listing-type-id' => $type_id,
			'parent' => 0,
		] ) ?>"
	>
</div>
