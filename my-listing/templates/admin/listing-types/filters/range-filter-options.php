<?php
/**
 * Render filter options for range filter.
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
			<option value="range">Range slider</option>
			<option value="simple">Single slider</option>
		</select>
	</div>
</div>

<div class="form-group" v-if="filter.option_type === 'simple'">
	<label>Behavior</label>
	<div class="select-wrapper">
		<select v-model="filter.behavior">
			<option value="lower">Matches all listings with a value less than or equal to the slider value</option>
			<option value="upper">Matches all listings with a value greater than or equal to the slider value</option>
		</select>
	</div>
</div>

<div class="form-group">
	<label>Step size</label>
	<input type="number" v-model="filter.step" step="any">
</div>

<div class="form-group">
	<label>Prefix</label>
	<input type="text" v-model="filter.prefix">
</div>

<div class="form-group">
	<label>Suffix</label>
	<input type="text" v-model="filter.suffix">
</div>

<div class="form-group">
	<div class="mb5"></div>
	<label>
		<input type="checkbox" v-model="filter.format_value" class="form-checkbox">
		<span>Format the numeric value for display (e.g. 12500 becomes 12,500)</span>
	</label>
</div>