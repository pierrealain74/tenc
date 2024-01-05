<?php
/**
 * Render filter options for recurring-date filter.
 *
 * @since 2.5.0
 */
if ( ! defined('ABSPATH') ) {
	exit;
} ?>

<?php $this->get_label_field() ?>
<?php $this->get_source_field() ?>

<div class="form-group">
	<label>Ranges</label>
	<p>
		You can display a list of common date ranges to quickly filter listings by.
		The first item in this list will be the default filter value. Drag and drop to reorder.
	</p>

	<draggable v-model="filter.ranges" :options="{group: 'recur-ranges', handle: '.item-handle'}" class="recur-ranges">
		<div v-for="range, index in filter.ranges" class="recur-range">
			<i class="mi menu item-handle"></i>
			<input type="text" v-model="range.label" :placeholder="$root.editor.recur_filter_ranges[ range.key ]">
			<span>
				Range: {{ $root.editor.recur_filter_ranges[ range.key ] }}
				<a href="#" @click.prevent="filter.ranges.splice(index, 1)"><i class="mi delete"></i></a>
			</span>
		</div>
	</draggable>

	<div class="text-center" v-if="!filter.ranges.length">
		<div class="btn btn-plain btn-xs mb10">
			You haven't added any date ranges yet.
		</div>
	</div>

	<div class="text-right">
		<div class="select-wrapper dib" style="width:170px;">
			<select
				@change="filter.ranges.push( {
					key: $event.target.value,
					label: $root.editor.recur_filter_ranges[ $event.target.value ],
				} ); $event.target.value='';">
				<option value="" disabled selected>Add range</option>
				<option
					v-for="label, key in $root.editor.recur_filter_ranges" :value="key"
					v-if="!recurringRangeIsUsed(key, filter)">{{ label }}</option>
			</select>
		</div>
	</div>
</div>

<div class="form-group">
	<label>Show datepicker?</label>
	<p>Allows users to select a custom date range to filter listings by.</p>
	<label class="form-switch">
		<input type="checkbox" v-model="filter.datepicker">
		<span class="switch-slider"></span>
	</label>
</div>

<div class="form-group" v-if="filter.datepicker">
	<label>Show timepicker?</label>
	<p>Allows users to select a custom date range with hour and minute specificity.</p>
	<label class="form-switch">
		<input type="checkbox" v-model="filter.timepicker">
		<span class="switch-slider"></span>
	</label>
</div>