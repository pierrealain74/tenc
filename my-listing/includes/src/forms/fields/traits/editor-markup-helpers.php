<?php
/**
 * Helper functions to generate field settings for the listing type editor.
 *
 * @since 1.0
 */

namespace MyListing\Src\Forms\Fields\Traits;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

trait Editor_Markup_Helpers {

	/**
	 * Renders the label setting in the field settings in the listing type editor.
	 *
	 * @since 1.0
	 */
	protected function getLabelField() { ?>
		<div class="form-group">
			<label>Label</label>
			<input type="text" v-model="field.label" @input="field.is_custom && field.is_new ? ( field.slug = slugify( field.label ) ) : ''">
		</div>
	<?php }

	/**
	 * Renders the "field key" setting in the field settings in the listing type editor.
	 *
	 * @since 1.0
	 */
	protected function getKeyField() { ?>
		<div class="form-group" v-if="field.is_custom">
			<label>Key</label>
			<input type="text" v-model="field.slug" @input="field.slug = slugify( field.slug )">
		</div>
	<?php }

	/**
	 * Renders the placeholder setting in the field settings in the listing type editor.
	 *
	 * @since 1.0
	 */
	protected function getPlaceholderField() { ?>
		<div class="form-group">
			<label>Placeholder</label>
			<input type="text" v-model="field.placeholder">
		</div>
	<?php }

	/**
	 * Renders the description setting in the field settings in the listing type editor.
	 *
	 * @since 1.0
	 */
	protected function getDescriptionField() { ?>
		<div class="form-group">
			<label>Description</label>
			<input type="text" v-model="field.description">
		</div>
	<?php }

	/**
	 * Renders an icon picker setting in the field settings in the listing type editor.
	 *
	 * @since 1.0
	 */
	protected function getIconField() { ?>
		<div class="form-group">
			<label>Icon</label>
			<iconpicker v-model="field.icon"></iconpicker>
		</div>
	<?php }

	/**
	 * Renders a "is required?" setting in the field settings in the listing type editor.
	 *
	 * @since 1.0
	 */
	protected function getRequiredField() { ?>
		<div class="form-group" v-show="field.slug !== 'job_title'">
			<label>Required field</label>
			<label class="form-switch mb0">
				<input type="checkbox" v-model="field.required">
				<span class="switch-slider"></span>
			</label>
		</div>
	<?php }

	/**
	 * Renders a "is multiple?" setting in the field settings in the listing type editor.
	 *
	 * @since 1.0
	 */
	protected function getMultipleField() { ?>
		<div class="form-group">
			<div class="mb5"></div>
			<label><input type="checkbox" v-model="field.multiple" class="form-checkbox">Multiple?</label>
		</div>
	<?php }

	/**
	 * Renders a "show in submit" setting in the field settings in the listing type editor.
	 *
	 * @since 1.0
	 */
	protected function getShowInSubmitFormField() { ?>
		<div class="form-group" v-show="field.slug !== 'job_title'">
			<label>Show in submit form</label>
			<label class="form-switch mb0">
				<input type="checkbox" v-model="field.show_in_submit_form">
				<span class="switch-slider"></span>
			</label>
		</div>
	<?php }

	/**
	 * Renders a "show in admin" setting in the field settings in the listing type editor.
	 *
	 * @since 1.0
	 */
	protected function getShowInAdminField() { ?>
		<div class="form-group" v-show="field.slug !== 'job_title'">
			<label>Show in admin edit page</label>
			<label class="form-switch mb0">
				<input type="checkbox" v-model="field.show_in_admin">
				<span class="switch-slider"></span>
			</label>
		</div>
	<?php }

	/**
	 * Renders "options" setting for fields that support it in the listing type editor.
	 *
	 * @since 1.0
	 */
	protected function getOptionsField() { ?>
		<div class="form-group options-field field-options-input">
			<label>Options</label>

			<div class="options-list" v-show="!state.fields.editingOptions">
				<div class="form-group" v-for="(value, key, index) in field.options">
					<input type="text" v-model="field.options[key]" disabled="disabled" :title="key">
				</div>
			</div>

			<div v-show="!state.fields.editingOptions && !Object.keys(field.options).length">
				<small><em>No options added yet.</em></small>
			</div>

			<textarea
				id="custom_field_options"
				v-show="state.fields.editingOptions"
				placeholder="Add each option in a new line."
				@keyup="editFieldOptions($event, field)"
				cols="50" rows="7"
				>{{ Object.keys(field.options).map(function(el) { return el === field.options[el] ? field.options[el] : el + ' : ' + field.options[el]; }).join('\n') }}</textarea>
			<small v-show="state.fields.editingOptions"><em>Put each option in a new line. You can specify both a value and label like this: <code>red : Red</code></em></small>
			<br><br v-show="state.fields.editingOptions || Object.keys(field.options).length">
			<button @click.prevent="state.fields.editingOptions = !state.fields.editingOptions;" class="btn btn-primary">{{ state.fields.editingOptions ? 'Save Options' : 'Add/Edit Options' }}</button>
		</div>
	<?php }

	/**
	 * Renders a "allowed product types" setting in the field settings in the listing type editor.
	 *
	 * @since 1.0
	 */
	public function getAllowedProductTypesField() {
		$products = function_exists( 'wc_get_product_types' ) ? wc_get_product_types() : [];
		?>
		<div class="form-group">
			<label>Allowed product types</label>
			<select multiple="multiple" v-model="field['product-type']">
				<?php foreach ( $products as $type => $label ): ?>
					<option value="<?php echo esc_attr( $type ) ?>"><?php echo $label ?></option>
				<?php endforeach ?>
			</select>
			<p>Leave empty for all</p>
		</div>
	<?php }

	/**
	 * Renders a "format" setting in the field settings in the listing type editor.
	 *
	 * @since 1.0
	 */
	protected function getFormatField() { ?>
		<div class="form-group">
			<label>Format</label>
			<div class="select-wrapper">
				<select v-model="field.format">
					<option value="date">Date</option>
					<option value="datetime">Date + Time</option>
				</select>
			</div>
		</div>
	<?php }

	/**
	 * Renders a "minimum value" setting in the field settings in the listing type editor.
	 *
	 * @since 1.0
	 */
	protected function getMinField() { ?>
		<div class="form-group">
			<label>Minimum value</label>
			<input type="number" v-model="field.min" step="any">
		</div>
	<?php }

	/**
	 * Renders a "maximum value" setting in the field settings in the listing type editor.
	 *
	 * @since 1.0
	 */
	protected function getMaxField() { ?>
		<div class="form-group">
			<label>Maximum value</label>
			<input type="number" v-model="field.max" step="any">
		</div>
	<?php }

	/**
	 * Renders a "minlength" setting in the field settings in the listing type editor.
	 *
	 * @since 2.1
	 */
	protected function getMinLengthField() { ?>
		<div class="form-group">
			<label>Min length (characters)</label>
			<input type="number" v-model="field.minlength">
		</div>
	<?php }

	/**
	 * Renders a "maxlength" setting in the field settings in the listing type editor.
	 *
	 * @since 2.1
	 */
	protected function getMaxLengthField() { ?>
		<div class="form-group">
			<label>Max length (characters)</label>
			<input type="number" v-model="field.maxlength">
		</div>
	<?php }

	/**
	 * Renders a "step size" setting in the field settings in the listing type editor.
	 *
	 * @since 1.0
	 */
	protected function getStepField() { ?>
		<div class="form-group">
			<label>Step size</label>
			<input type="number" v-model="field.step" step="any">
		</div>
	<?php }

	/**
	 * Renders field visibility settings in the listing type editor.
	 *
	 * @since 1.5
	 */
	protected function get_visibility_settings() { ?>
		<div class="field-visibility form-group" v-show="field.slug != 'job_title'">
			<label>Enable package visibility</label>
			<label class="form-switch mb0">
				<input type="checkbox" v-model="field.conditional_logic">
				<span class="switch-slider"></span>
			</label>

			<div class="visibility-rules form-group" v-show="field.conditional_logic">
				<label>Show this field if</label>
				<p></p>
				<div class="conditions">
					<div class="condition-group" v-for="conditionGroup, groupKey in field.conditions" v-if="conditionGroup.length">
						<label class="or-divider mt10" v-if="groupKey !== 0">
							or
						</label>
						<div class="condition" v-for="condition in conditionGroup">
							<div class="condition-item condition-key">
								<select v-model="condition.key">
									<option value="__listing_package">Listing Package</option>
								</select>
							</div><!--
							--><div class="condition-item condition-compare">
								<select v-model="condition.compare">
									<option value="==">is equal to</option>
									<!-- <option value="!=">is not equal to</option> -->
								</select>
							</div><!--
							--><div class="condition-item select-wrapper condition-value">
								<select v-model="condition.value">
									<option value="--none--">No Package</option>
									<option v-for="package in settings.packages.used" :value="package.package">{{ getPackageTitle(package) }}</option>
								</select>
							</div><!--
							--><div class="remove-condition btn btn-xs" @click="conditions().deleteConditionGroup(conditionGroup, field)">
								<i class="mi delete"></i>
							</div>
						</div>
					</div>

					<button class="btn btn-outline btn-xs mt20" @click.prevent="conditions().addOrCondition(field)">Add Another Rule</button>
					<!-- <pre>{{ field.conditions }}</pre> -->
				</div>
			</div>
		</div>
	<?php }

	/**
	 * Renders a "Show in listing comparison popup" setting in the field settings in the listing type editor.
	 *
	 * @since 2.7
	 */
	protected function getShowInCompareField() { ?>
		<div class="form-group" v-show="field.slug !== 'job_title'">
			<label>Show in listing comparison popup</label>
			<label class="form-switch mb0">
				<input type="checkbox" v-model="field.show_in_compare">
				<span class="switch-slider"></span>
			</label>
		</div>
	<?php }

}