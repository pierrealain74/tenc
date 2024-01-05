<?php
/**
 * Shows the `select` form field on add listing forms.
 *
 * @since 1.0
 * @var   array $field
 */

$attrs = [];
$attrs[] = sprintf( 'name="%s"', esc_attr( isset( $field['name'] ) ? $field['name'] : $key ) );
$attrs[] = sprintf( 'id="%s"', esc_attr( $key ) );
// $attrs[] = sprintf( 'class="%s"', esc_attr( 'ignore-custom-select' ) );
$attrs[] = ! empty( $field['required'] ) ? 'required' : '';
$attrs[] = sprintf( 'placeholder="%s"', esc_attr( ! empty( $field['placeholder'] ) ? $field['placeholder'] : '' ) );

$price_list = get_option( '_price_list', true );

if ( ! $price_list ) {
    every_thirty_minutes_event_func();
}

$attrs[] = sprintf( 'data-rate="%s"', htmlspecialchars(json_encode(isset($price_list) ? $price_list : []), ENT_QUOTES, 'UTF-8') );


if ( empty( $field['value'] ) ) {
	$field['value'] = array_key_first( $field['options'] );
}
?>

<select <?php echo join( ' ', $attrs ) ?>>
	<?php foreach ( $field['options'] as $key => $value ) : ?>
		<option value="<?php echo esc_attr( $key ) ?>" <?php echo ! empty( $field['value'] ) ? selected( $field['value'], $key ) : '' ?>>
			<?php echo str_replace( 'EUR', '', $value ); ?>
		</option>
	<?php endforeach ?>
</select>
