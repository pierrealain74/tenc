<?php
/**
 * Helper functions to generate filter settings for the listing type editor.
 *
 * @since 2.2
 */

namespace MyListing\Src\Listing_Types\Filters\Traits;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

trait Editor_Markup_Helpers {
	protected function get_label_field() { ?>
		<div class="form-group">
			<label>Label</label>
			<input type="text" v-model="filter.label">
		</div>
	<?php }

	protected function get_source_field() {
		$allowed_fields = c27()->encode_attr( ! empty( $this->allowed_fields ) ? $this->allowed_fields : [] ); ?>
		<div class="form-group">
			<label>Use Field</label>
			<div class="select-wrapper">
				<select v-model="filter.show_field">
					<option v-for="field in $root.fieldsByType(<?php echo $allowed_fields ?>)" :value="field.slug">{{ field.label }}</option>
				</select>
			</div>
		</div>
	<?php }

	protected function get_is_primary_field() { ?>
		<div class="form-group" v-if="activeFormKey === 'advanced' && filter.is_primary !== undefined">
			<div class="mb5"></div>
			<label>
				<input type="checkbox" :checked="filter.is_primary"
					@change="setPrimaryFilter(filter)" class="form-checkbox">
				<span>Mark as primary filter</span>
			</label>
		</div>
	<?php }
}
