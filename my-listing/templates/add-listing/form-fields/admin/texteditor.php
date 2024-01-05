<?php
/**
 * Texteditor admin template.
 *
 * @since 2.1
 */
if ( ! empty( $field['editor-type'] ) && ( $template = locate_template( "templates/add-listing/form-fields/admin/{$field['editor-type']}.php" ) ) ) {
	require $template;
} else {
	require locate_template( 'templates/add-listing/form-fields/admin/wp-editor.php' );
}
