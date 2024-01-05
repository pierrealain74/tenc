<?php
/**
 * Recurring Date importer form field.
 *
 * @since 2.6
 */
if ( ! defined('ABSPATH') ) {
	exit;
} ?>

<label><?php echo $field->get_label() ?></label>

<table class="event-import-table">
	<thead>
		<tr>
			<th>
				Start Date
				<a href="#help" class="wpallimport-help" title="Use any date that can be interpreted with strototime()">?</a>
			</th>
			<th>
				End Date
				<a href="#help" class="wpallimport-help" title="Use any date that can be interpreted with strototime()">?</a>
			</th>
			<?php if ( $field->get_prop( 'allow_recurrence' ) ): ?>
				<th>
					Repeat every
					<a href="#help" class="wpallimport-help" title="Set the repeat interval using a number followed by &quot;days&quot;, &quot;weeks&quot;, &quot;months&quot;, or &quot;years&quot;. Leave empty for non-recurring dates.<br>Example Values:<br>10 days<br>1 week<br>2 weeks<br>6 months<br>1 year">?</a>
				</th>
				<th>
					Repeat until
					<a href="#help" class="wpallimport-help" title="Use any date that can be interpreted with strototime(). Leave empty for non-recurring dates.">?</a>
				</th>
			<?php endif ?>
			<?php if ( $field->get_prop( 'allow_multiple' ) ): ?>
				<th></th>
			<?php endif ?>
		</tr>
	</thead>
	<tbody>
		<?php foreach ( array_values( $field_value ) as $key => $date ): ?>
			<tr>
				<td>
					<input
						type="text"
						class="block"
						placeholder="e.g. &quot;2020-09-23 13:00:00&quot;"
						name="<?php echo $field_name ?>[<?php echo $key ?>][start]"
						value="<?php echo $date['start'] ?? '' ?>"
					>
				</td>
				<td>
					<input
						type="text"
						class="block"
						placeholder="e.g. &quot;2020-09-30 21:00:00&quot;"
						name="<?php echo $field_name ?>[<?php echo $key ?>][end]"
						value="<?php echo $date['end'] ?? '' ?>"
					>
				</td>

				<?php if ( $field->get_prop( 'allow_recurrence' ) ): ?>
					<td>
						<input
							type="text"
							class="block"
							placeholder="e.g. &quot;2 weeks&quot;"
							name="<?php echo $field_name ?>[<?php echo $key ?>][frequency]"
							value="<?php echo $date['frequency'] ?? '' ?>"
						>
					</td>
					<td>
						<input
							type="text"
							class="block"
							placeholder="e.g. &quot;2021-01-01&quot;"
							name="<?php echo $field_name ?>[<?php echo $key ?>][until]"
							value="<?php echo $date['until'] ?? '' ?>"
						>
					</td>
				<?php endif ?>

				<?php if ( $field->get_prop( 'allow_multiple' ) ): ?>
					<td style="vertical-align:middle;">
						<a href="#" class="date-remove"><i class="mi delete"></i></a>
					</td>
				<?php endif ?>
			</tr>
		<?php endforeach ?>
	</tbody>
</table>

<?php if ( $field->get_prop( 'allow_multiple' ) ): ?>
	<div class="text-right mt10">
		<a href="#" class="date-add-new btn btn-secondary btn-xs" data-key="<?php echo $field_name ?>">Add a date</a>
	</div>
<?php endif ?>