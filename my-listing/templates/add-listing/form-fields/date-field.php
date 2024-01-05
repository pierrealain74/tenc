<?php
$args = [
	'timepicker' => isset( $field['format'] ) && $field['format'] === 'datetime',
];
$argstring = htmlspecialchars( json_encode( $args ), ENT_QUOTES, 'UTF-8' );
?>
<div class="datepicker-wrapper submit-listing-datepicker-wrapper <?php echo isset($field['format']) && $field['format'] == 'datetime' ? 'datetime-picker' : '' ?>">
	<input
		type="text" class="input-text input-datepicker mylisting-datepicker"
		name="<?php echo esc_attr( isset( $field['name'] ) ? $field['name'] : $key ); ?>"
		id="<?php echo esc_attr( $key ); ?>"
		placeholder="<?php echo esc_attr( $field['placeholder'] ); ?>"
		value="<?php echo isset( $field['value'] ) ? esc_attr( $field['value'] ) : ''; ?>"
		<?php if ( ! empty( $field['required'] ) ) echo 'required'; ?>
		data-options="<?php echo $argstring ?>"
	>
</div>