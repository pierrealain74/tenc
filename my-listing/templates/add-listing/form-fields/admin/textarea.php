<?php
// Set the description field ID to '#content' so that it's
// recognizable by plugins like Yoast SEO.
$editor_id = ( $key === 'job_description' ) ? 'content' : $key;
?>
<textarea
	name="<?php echo esc_attr( $name ); ?>"
	id="<?php echo esc_attr( $editor_id ); ?>"
	placeholder="<?php echo esc_attr( $field['placeholder'] ); ?>"
><?php echo esc_html( $field['value'] ) ?></textarea>
