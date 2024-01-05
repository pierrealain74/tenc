<?php
$map_options = [
	'skin' => ! empty( $field['map-skin'] ) ? $field['map-skin'] : false,
	'cluster_markers' => false,
	'scrollwheel' => true,
];

$options = [
	'default-lat' => ! empty( $field['map-default-location'] ) && ! empty( $field['map-default-location']['lat'] ) ? $field['map-default-location']['lat'] : 51.5072,
	'default-lng' => ! empty( $field['map-default-location'] ) && ! empty( $field['map-default-location']['lng'] ) ? $field['map-default-location']['lng'] : -0.1280,
	'default-zoom' => ! empty( $field['map-zoom'] ) ? absint( $field['map-zoom'] ) : 12,
];

if ( ! isset( $field['value'] ) ) {
	$field['value'] = [
		[
			'address'	=> '',
			'lat'		=> '',
			'lng'		=> ''
		]
	];
}
?>

<div class="repeater-custom social-networks-repeater" data-list="<?php echo esc_attr( wp_json_encode( isset( $field['value']) ? $field['value'] : [] ) ) ?>" data-max="<?php echo absint( ! empty( $field['max'] ) ? absint( $field['max'] ) : 3 ) ?>">
    <div data-repeater-list="<?php echo esc_attr( (isset($field['name']) ? $field['name'] : $key) ) ?>">
        <div data-repeater-item class="repeater-item">
        	<div class="location-field-wrapper" data-options="<?php echo c27()->encode_attr( $options ) ?>">
	            <input type="text" name="address" placeholder="<?php esc_attr_e( 'Enter Location', 'my-listing' ) ?>" class="address-field">
	            <i class="mi my_location cts-custom-get-location" data-map="location-picker-map"></i>
	            <div class="location-actions">
					<div class="lock-pin">
						<input id="location__lock_pin" type="checkbox" name="location__lock_pin" value="yes">
						<label for="location__lock_pin" class="locked"><i class="mi lock_outline"></i><?php _e( 'Unlock Pin Location', 'my-listing' ) ?></label>
						<label for="location__lock_pin" class="unlocked"><i class="mi lock_open"></i><?php _e( 'Lock Pin Location', 'my-listing' ) ?></label>
					</div>

					<div class="enter-coordinates-toggle">
						<span><?php _e( 'Enter coordinates manually', 'my-listing' ) ?></span>
					</div>
				</div>

				<div class="location-coords hide">
					<div class="form-group">
						<label for="lat"><?php _e( 'Latitude', 'my-listing' ) ?></label>
						<input type="text" name="lat" id="lat" class="latitude-input">
					</div>
					<div class="form-group">
						<label for="lng"><?php _e( 'Longitude', 'my-listing' ) ?></label>
						<input type="text" name="lng" id="lng" class="longitude-input">
					</div>
				</div>

				<div class="c27-custom-map picker location-picker-custom-map" data-options="<?php echo c27()->encode_attr( $map_options ) ?>"></div>
	            <button data-repeater-delete type="button" class="delete-repeater-item buttons button-5 icon-only small"><i class="material-icons delete"></i></button>
			</div>
        </div>
    </div>
    <input data-repeater-create class="add-location" type="button" value="<?php esc_attr_e( 'Add Location', 'my-listing' ) ?>">
</div>