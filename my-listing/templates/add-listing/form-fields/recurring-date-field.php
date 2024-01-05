<?php
/**
 * Template for displaying recurring-date field in Add Listing
 * and Edit Listing forms.
 *
 * @since 2.4
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>

<div
	class="event-picker"
	data-key="<?php echo esc_attr( $key ) ?>"
	data-dates="<?php echo c27()->encode_attr( (array) $field['value'] ) ?>"
	data-limit="<?php echo absint( $field->get_prop('max_date_count') ) ?>"
	data-timepicker="<?php echo $field->get_prop('enable_timepicker') ? 'yes' : 'no' ?>"
	data-l10n="<?php echo esc_attr( wp_json_encode( [
		'no_recurrences' => _x( 'No recurrences in this timeframe', 'Add Listing > Recurring Date field', 'my-listing' ),
		'next_recurrences' => _x( 'Next recurrences', 'Add Listing > Recurring Date field', 'my-listing' ),
		'next_five' => _x( 'Next 5 recurrences (%d total)', 'Add Listing > Recurring Date field', 'my-listing' ),
	] ) ) ?>"
	>
	<script type="text/template" class="datetpl">
		<div class="single-date">
			<div class="date-start date-empty">
				<label><?php _ex( 'From', 'Add Listing > Recurring Date field', 'my-listing' ) ?></label>
				<div class="datepicker-wrapper">
					<input
						type="text"
						class="input-datepicker mylisting-datepicker"
						placeholder="<?php echo esc_attr( _ex( 'Click to set date', 'Recurring date field', 'my-listing' ) ) ?>"
						name="{date}[start]"
					>
				</div>
			</div>

			<div class="date-end date-empty">
				<label><?php _ex( 'To', 'Add Listing > Recurring Date field', 'my-listing' ) ?></label>
				<div class="datepicker-wrapper">
					<input
						type="text"
						class="input-datepicker mylisting-datepicker"
						placeholder="<?php echo esc_attr( _ex( 'Click to set date', 'Recurring date field', 'my-listing' ) ) ?>"
						name="{date}[end]"
					>
				</div>
			</div>

			<?php if ( $field->get_prop( 'allow_recurrence' ) ): ?>
				<div class="recurrence">
					<div class="md-checkbox is-recurring">
						<input type="checkbox" id="{date}[repeat]" name="{date}[repeat]">
						<label for="{date}[repeat]">
							<?php _ex( 'Is recurring?', 'Add Listing > Recurring Date field', 'my-listing' ) ?>
						</label>
					</div>
					<div class="recur-details">
						<p class="rc-title"><?php _ex( 'Repeat every', 'Add Listing > Recurring Date field', 'my-listing' ) ?></p>
						<div class="repeat-frequency">
							<input type="number" step="1" value="1" min="1" name="{date}[frequency]">
						</div>
						<div class="repeat-unit">
							<div class="md-checkbox">
								<input type="radio" name="{date}[unit]" id="{date}_unitdays" value="days">
								<label for="{date}_unitdays">
									<?php _ex( 'Day(s)', 'Add Listing > Recurring Date field', 'my-listing' ) ?>
								</label>
							</div>

							<div class="md-checkbox">
								<input type="radio" name="{date}[unit]" id="{date}_unitweeks" value="weeks">
								<label for="{date}_unitweeks">
									<?php _ex( 'Week(s)', 'Add Listing > Recurring Date field', 'my-listing' ) ?>
								</label>
							</div>

							<div class="md-checkbox">
								<input type="radio" name="{date}[unit]" id="{date}_unitmonths" value="months">
								<label for="{date}_unitmonths">
									<?php _ex( 'Month(s)', 'Add Listing > Recurring Date field', 'my-listing' ) ?>
								</label>
							</div>

							<div class="md-checkbox">
								<input type="radio" name="{date}[unit]" id="{date}_unityears" value="years">
								<label for="{date}_unityears">
									<?php _ex( 'Year(s)', 'Add Listing > Recurring Date field', 'my-listing' ) ?>
								</label>
							</div>
						</div>
						<p class="rc-title"><?php _ex( 'Until', 'Add Listing > Recurring Date field', 'my-listing' ) ?></p>
						<div class="datepicker-wrapper repeat-end">
							<input type="text" class="input-datepicker mylisting-datepicker" name="{date}[until]">
						</div>
						<div class="repeat-message"></div>
					</div>
				</div>

			<?php endif ?>

			<?php if ( $field->get_prop( 'allow_multiple' ) ): ?>
				<div class="remove-date-container">
					<a href="#" class="remove-date"><i class="material-icons delete"></i></a>
				</div>
			<?php endif ?>
		</div>
	</script>

	<div class="dates-list"></div>

	<?php if ( $field->get_prop( 'allow_multiple' ) ): ?>
		<a href="#" class="date-add-new">
			<?php _ex( 'Add a date', 'Add Listing > Recurring Date field', 'my-listing' ) ?>
		</a>
	<?php endif ?>
</div>