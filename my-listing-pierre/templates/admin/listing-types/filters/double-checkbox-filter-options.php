<?php
/**
 * Render filter options for dropdown filter.
 *
 * @since 2.5.0
 */
if ( ! defined('ABSPATH') ) {
	exit;
} ?>

<?php $this->get_label_field() ?>

<div class="form-group">
	<label>Use Field</label>
	<p v-if="filter.show_field && getFieldType(filter.show_field) !== 'term-select'">
		For best filtering performance, consider using a
		<a href="#" class="cts-show-tip" data-tip="custom-taxonomies">custom taxonomy</a> instead.
	</p>
	<div class="select-wrapper">
		<select v-model="filter.show_field">
			<option v-for="field in $root.getFields('term-select')" :value="field.slug">
				{{ field.label }}
			</option>
		</select>
	</div>
</div>

<div v-if="getFieldType(filter.show_field) === 'term-select'" class="form-group">
	<label>Order By</label>
	<div class="select-wrapper">
		<select v-model="filter.order_by">
			<option value="name">Name</option>
			<option value="count">Count</option>
		</select>
	</div>
</div>
<div v-else-if="['select','multiselect','checkbox','radio'].indexOf(getFieldType(filter.show_field)) !== -1" class="form-group">
	<label>Order By</label>
	<div class="select-wrapper">
		<select v-model="filter.order_by">
			<option value="include">Included order (the order options were added in the field settings)</option>
			<option value="meta_value">Value (alphabetical)</option>
			<option value="meta_value_num">Value (numerical)</option>
			<option value="count">Count</option>
		</select>
	</div>
</div>
<div v-else class="form-group">
	<label>Order By</label>
	<div class="select-wrapper">
		<select v-model="filter.order_by">
			<option value="meta_value">Value (alphabetical)</option>
			<option value="meta_value_num">Value (numerical)</option>
			<option value="count">Count</option>
		</select>
	</div>
</div>

<div class="form-group">
	<label>Order</label>
	<div class="select-wrapper">
		<select v-model="filter.order">
			<option value="ASC">Ascending</option>
			<option value="DESC">Descending</option>
		</select>
	</div>
</div>

<div v-if="getFieldType(filter.show_field) === 'term-select'" class="form-group" class="form-group">
	<div class="mb5"></div>
	<label>
		<input type="checkbox" v-model="filter.hide_empty" class="form-checkbox">
		<span>Hide empty terms</span>
	</label>
	<p>Terms that don't yield any results will be hidden.</p>
</div>

<div>
	<div class="form-group">
		<div class="mb5"></div>
		<label>
			<input type="checkbox" v-model="filter.multiselect" class="form-checkbox">
			<span>Enable multiselect</span>
		</label>
	</div>
	<div class="form-group" v-if="activeFormKey === 'advanced'">
		<label>Multiselect behavior</label>
		<div class="select-wrapper">
			<select v-model="filter.behavior">
				<option value="any">Show listings matching ANY of the selected terms</option>
				<option value="all">Show listings matching ALL of the selected terms</option>
			</select>
		</div>
		<p>Set the search logic to be used when selecting multiple terms.</p>
	</div>
</div>