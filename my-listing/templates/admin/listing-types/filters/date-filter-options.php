<?php
/**
 * Render filter options for date filter.
 *
 * @since 2.5.0
 */
if ( ! defined('ABSPATH') ) {
	exit;
} ?>

<?php $this->get_label_field() ?>
<?php $this->get_source_field() ?>

<div class="form-group">
	<label>Type</label>
	<div class="select-wrapper">
		<select v-model="filter.option_type">
			<option value="exact">Exact Date</option>
			<option value="range">Date Range</option>
		</select>
	</div>
</div>

<div class="form-group">
	<label>Date format</label>
	<div class="select-wrapper">
		<select v-model="filter.format">
			<option value="ymd">Year + Month + Day</option>
			<option value="year">Years Only</option>
		</select>
	</div>
</div>
