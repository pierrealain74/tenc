<?php
/**
 * Location importer form field.
 *
 * @since 2.6
 */
if ( ! defined('ABSPATH') ) {
	exit;
} ?>

<label><?php echo $field->get_label() ?></label>

<div class="pick-method">
	<label>
		<input type="radio" name="<?php echo $field_name ?>[method]" value="address" <?php checked( $method, 'address' ) ?>>
		Geocode by address
	</label>
	<label>
		<input type="radio" name="<?php echo $field_name ?>[method]" value="coordinates" <?php checked( $method, 'coordinates' ) ?>>
		Geocode by coordinates
	</label>
	<label>
		<input type="radio" name="<?php echo $field_name ?>[method]" value="manual" <?php checked( $method, 'manual' ) ?>>
		Enter manually (no geocoding)
	</label>
</div>

<div class="import-method method-address mt15">
	<p class="mt5 mb5">
		Your active map service provider will be used to retrieve listing
		coordinates based on the address. You must add a valid API key in
		<a href="<?php echo esc_url( admin_url('admin.php?page=theme-mapservice-settings') ) ?>" target="_blank">Map Services</a>.
	</p>
	<label><strong>Enter address</strong></label>
	<input type="text" name="<?php echo $field_name ?>[address]" value="<?php echo $field_value['address'] ?? '' ?>" class="block">
</div>

<div class="import-method method-coordinates mt15">
	<p class="mt5 mb5">
		Your active map service provider will be used to retrieve listing
		address based on the coordinates (reverse geocoding). You must add a valid API key in
		<a href="<?php echo esc_url( admin_url('admin.php?page=theme-mapservice-settings') ) ?>" target="_blank">Map Services</a>.
	</p>
	<div class="dibvt" style="width:45%;margin-right:3%;">
		<label><strong>Latitude</strong></label>
		<input type="text" name="<?php echo $field_name ?>[latitude]" value="<?php echo $field_value['latitude'] ?? '' ?>" class="block">
	</div>
	<div class="dibvt" style="width:45%;">
		<label><strong>Longitude</strong></label>
		<input type="text" name="<?php echo $field_name ?>[longitude]" value="<?php echo $field_value['longitude'] ?? '' ?>" class="block">
	</div>
</div>

<div class="import-method method-manual mt15">
	<p class="mt5 mb5">
		Manually enter the address and coordinates. No map/geocoding service is required.
	</p>
	<div class="mb10">
		<label><strong>Address</strong></label>
		<input type="text" name="<?php echo $field_name ?>[manual_address]" value="<?php echo $field_value['manual_address'] ?? '' ?>" class="block">
	</div>
	<div class="dibvt" style="width:45%;margin-right:3%;">
		<label><strong>Latitude</strong></label>
		<input type="text" name="<?php echo $field_name ?>[manual_latitude]" value="<?php echo $field_value['manual_latitude'] ?? '' ?>" class="block">
	</div>
	<div class="dibvt" style="width:45%;">
		<label><strong>Longitude</strong></label>
		<input type="text" name="<?php echo $field_name ?>[manual_longitude]" value="<?php echo $field_value['manual_longitude'] ?? '' ?>" class="block">
	</div>
</div>
