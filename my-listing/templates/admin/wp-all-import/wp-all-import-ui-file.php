<?php
/**
 * File importer form field.
 *
 * @since 2.6
 */
if ( ! defined('ABSPATH') ) {
	exit;
}

$render_as = in_array( $field->get_key(), ['job_logo', 'job_cover', 'job_gallery'], true ) ? 'image' : 'file';
\PMXI_API::add_field( $render_as,  $field->get_label(), [
	'field_name' => $field_name.'[value]',
	'field_value' => $field_value['value'] ?? '',
	'field_key' => $field->get_key(),
	'addon_prefix' => 'mylisting-addon',
	'download_image' => ! empty( $values['download_image'][ $field->get_key() ] )
		? $values['download_image'][ $field->get_key() ]
		: 'yes',
] ); ?>

<div class="wpai-file-details">
	<input
		type="checkbox"
		value="yes"
		id="<?php echo $field_name.'_enabled' ?>"
		name="<?php echo $field_name ?>[enabled]"
		<?php echo ! empty( $field_value['enabled'] ) ? 'checked' : '' ?>
	>
	<label for="<?php echo $field_name.'_enabled' ?>">Set file details</label>
	<table>
		<tbody>
			<tr>
				<td><strong>Title</strong></td>
				<td>
					<input
						type="text"
						name="<?php echo $field_name ?>[title]"
						value="<?php echo $field_value['title'] ?? '' ?>"
					>
				</td>
			</tr>
			<tr>
				<td><strong>Description</strong></td>
				<td>
					<input
						type="text"
						name="<?php echo $field_name ?>[description]"
						value="<?php echo $field_value['description'] ?? '' ?>"
					>
				</td>
			</tr>
			<tr>
				<td><strong>Caption</strong></td>
				<td>
					<input
						type="text"
						name="<?php echo $field_name ?>[caption]"
						value="<?php echo $field_value['caption'] ?? '' ?>"
					>
				</td>
			</tr>
			<tr>
				<td><strong>Alt text</strong></td>
				<td>
					<input
						type="text"
						name="<?php echo $field_name ?>[alt]"
						value="<?php echo $field_value['alt'] ?? '' ?>"
					>
				</td>
			</tr>
		</tbody>
	</table>
</div>

<?php if ( $field->get_prop('multiple') ): ?>
	<p>Separate multiple values with <?php echo $delimiter_markup ?></p>
<?php endif ?>