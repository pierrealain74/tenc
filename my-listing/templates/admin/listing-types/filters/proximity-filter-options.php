<?php
/**
 * Render filter options for proximity filter.
 *
 * @since 2.5.0
 */
if ( ! defined('ABSPATH') ) {
	exit;
} ?>

<?php $this->get_label_field() ?>

<div class="form-group">
	<label>Units</label>
	<div class="select-wrapper">
		<select v-model="filter.units">
			<option value="metric">Kilometres</option>
			<option value="imperial">Miles</option>
		</select>
	</div>
</div>

<div class="form-group">
	<label>Step size</label>
	<input type="number" v-model="filter.step" step="any">
</div>

<div class="form-group">
	<label>Maximum value</label>
	<input type="number" v-model="filter.max" step="any">
</div>

<div class="form-group">
	<label>Default value</label>
	<input type="number" v-model="filter.default" step="any">
</div>

