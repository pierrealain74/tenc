<?php
/**
 * Work hours importer form field.
 *
 * @since 2.6
 */
if ( ! defined('ABSPATH') ) {
	exit;
} ?>

<label><?php echo $field->get_label() ?></label>

<div class="pick-method">
	<label>
		<input type="radio" name="<?php echo $field_name ?>[method]" value="default" <?php checked( $method, 'default' ) ?>>
		Method 1
	</label>
	<label>
		<input type="radio" name="<?php echo $field_name ?>[method]" value="serialized" <?php checked( $method, 'serialized' ) ?>>
		Method 2
	</label>
</div>

<div class="import-method method-default mt15">
	<p class="mt5 mb5">
		Separate multiple opening/closing hours with commas, e.g.: 7:00am, 1:00pm. Use 'open' for Open all day, 'closed' for Closed all day, and 'appointment' for By appointment only.
	</p>

	<table class="work-hours-table">
		<thead>
			<tr>
				<th></th>
				<th>Monday</th>
				<th>Tuesday</th>
				<th>Wednesday</th>
				<th>Thursday</th>
				<th>Friday</th>
				<th>Saturday</th>
				<th>Sunday</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<th>From</th>
				<td>
					<input
						type="text" class="block" name="<?php echo $field_name ?>[mon_from]"
						value="<?php echo esc_attr( $field_value['mon_from'] ?? '' ) ?>">
				</td>
				<td>
					<input
						type="text" class="block" name="<?php echo $field_name ?>[tue_from]"
						value="<?php echo esc_attr( $field_value['tue_from'] ?? '' ) ?>">
				</td>
				<td>
					<input
						type="text" class="block" name="<?php echo $field_name ?>[wed_from]"
						value="<?php echo esc_attr( $field_value['wed_from'] ?? '' ) ?>">
				</td>
				<td>
					<input
						type="text" class="block" name="<?php echo $field_name ?>[thu_from]"
						value="<?php echo esc_attr( $field_value['thu_from'] ?? '' ) ?>">
				</td>
				<td>
					<input
						type="text" class="block" name="<?php echo $field_name ?>[fri_from]"
						value="<?php echo esc_attr( $field_value['fri_from'] ?? '' ) ?>">
				</td>
				<td>
					<input
						type="text" class="block" name="<?php echo $field_name ?>[sat_from]"
						value="<?php echo esc_attr( $field_value['sat_from'] ?? '' ) ?>">
				</td>
				<td>
					<input
						type="text" class="block" name="<?php echo $field_name ?>[sun_from]"
						value="<?php echo esc_attr( $field_value['sun_from'] ?? '' ) ?>">
				</td>
			</tr>
			<tr>
				<th>To</th>
				<td>
					<input
						type="text" class="block" name="<?php echo $field_name ?>[mon_to]"
						value="<?php echo esc_attr( $field_value['mon_to'] ?? '' ) ?>">
				</td>
				<td>
					<input
						type="text" class="block" name="<?php echo $field_name ?>[tue_to]"
						value="<?php echo esc_attr( $field_value['tue_to'] ?? '' ) ?>">
				</td>
				<td>
					<input
						type="text" class="block" name="<?php echo $field_name ?>[wed_to]"
						value="<?php echo esc_attr( $field_value['wed_to'] ?? '' ) ?>">
				</td>
				<td>
					<input
						type="text" class="block" name="<?php echo $field_name ?>[thu_to]"
						value="<?php echo esc_attr( $field_value['thu_to'] ?? '' ) ?>">
				</td>
				<td>
					<input
						type="text" class="block" name="<?php echo $field_name ?>[fri_to]"
						value="<?php echo esc_attr( $field_value['fri_to'] ?? '' ) ?>">
				</td>
				<td>
					<input
						type="text" class="block" name="<?php echo $field_name ?>[sat_to]"
						value="<?php echo esc_attr( $field_value['sat_to'] ?? '' ) ?>">
				</td>
				<td>
					<input
						type="text" class="block" name="<?php echo $field_name ?>[sun_to]"
						value="<?php echo esc_attr( $field_value['sun_to'] ?? '' ) ?>">
				</td>
			</tr>
		</tbody>
	</table>

	<div class="mb10">
		<p class="mb0">
			<strong>Timezone</strong><br>
			Leave blank to use site timezone.
			<a href="https://www.php.net/manual/en/timezones.php" target="_blank">List of supported timezones</a>
		</p>
		<input type="text" name="<?php echo $field_name ?>[timezone]" value="<?php echo esc_attr( $field_value['timezone'] ?? '' ) ?>">
	</div>
</div>

<div class="import-method method-serialized mt15">
	<p class="mt5 mb5">
		Enter serialized data. If you have exported work hours from another
		MyListing site, you can enter the serialized export field here.
	</p>
	<input type="text" name="<?php echo $field_name ?>[serialized]" value="<?php echo esc_attr( $field_value['serialized'] ?? '' ) ?>" class="block">
</div>
