<?php
/**
 * Helper functions to generate filter settings for the listing type editor.
 *
 * @since 2.2
 */

namespace MyListing\Src\Listing_Types\Content_Blocks\Traits;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

trait Editor_Markup_Helpers {

	protected function getLabelField() { ?>
		<div class="form-group">
			<label>Label</label>
			<input type="text" v-model="block.title">
		</div>
	<?php }

	protected function getSourceField() {
		$allowed_fields = htmlspecialchars( json_encode( ! empty( $this->allowed_fields ) ? $this->allowed_fields : [] ), ENT_QUOTES, 'UTF-8' ); ?>
		<div class="form-group">
			<label>Use Field</label>
			<div class="select-wrapper">
				<select v-model="block.show_field">
					<option v-for="field in fieldsByType(<?php echo $allowed_fields ?>)" :value="field.slug">{{ field.label }}</option>
				</select>
			</div>
		</div>
	<?php }

	protected function getCommonSettings() { ?>
		<div class="advanced-settings">
			<div class="form-group">
				<label>Block Icon</label>
				<iconpicker v-model="block.icon"></iconpicker>
			</div>

			<div class="form-group">
				<label>Custom Block ID</label>
				<input type="text" v-model="block.id">
			</div>

			<div class="form-group">
				<label>Custom Block Classes</label>
				<input type="text" v-model="block.class">
			</div>

			<?php // @todo: Refactor field and block visibility to use the same source code. ?>
			<div class="field-visibility form-group" v-if="block.type === 'raw' || block.type === 'google-ad'">
				<label>Enable package visibility</label>
				<label class="form-switch">
					<input type="checkbox" v-model="block.conditional_logic" class="form-checkbox">
					<span class="switch-slider"></span>
				</label>

				<div class="visibility-rules form-group" v-show="block.conditional_logic">
					<label>Show this block if</label>
					<p></p>
					<div class="conditions">
						<div class="condition-group" v-for="conditionGroup, groupKey in block.conditions" v-if="conditionGroup.length">
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
									</select>
								</div><!--
								--><div class="condition-item select-wrapper condition-value">
									<select v-model="condition.value">
										<option value="--none--">No Package</option>
										<option v-for="package in settings.packages.used" :value="package.package">{{ getPackageTitle(package) }}</option>
									</select>
								</div><!--
								--><div class="remove-condition btn btn-xs" @click="conditions().deleteConditionGroup(conditionGroup, block)">
									<i class="mi delete"></i>
								</div>
							</div>
						</div>

						<button class="btn btn-outline btn-xs mt20" @click.prevent="conditions().addOrCondition(block)">Add Rule</button>
						<!-- <pre>{{ block }}</pre> -->
					</div>
				</div>
			</div>
		</div>
	<?php }

	protected function numberOption( $option ) { ?>
		<div v-if="<?php echo $option ?>.type == 'number'" class="select-option">
			<label>{{ <?php echo $option ?>.label }}</label>
			<input type="number" v-model="<?php echo $option ?>.value" step="any">
		</div>
	<?php }

	protected function textareaOption( $option ) { ?>
		<div v-if="<?php echo $option ?>.type == 'textarea'">
			<label>{{ <?php echo $option ?>.label }}</label>
			<textarea rows="10" v-model="<?php echo $option ?>.value"></textarea>
		</div>
	<?php }

	protected function selectOption( $option ) { ?>
		<div v-if="<?php echo $option ?>.type == 'select'" class="select-option">
			<label>{{ <?php echo $option ?>.label }}</label>
			<div class="select-wrapper">
				<select v-model="<?php echo $option ?>.value">
					<option v-for="(choice_label, choice) in fieldsByTypeFormatted(<?php echo $option ?>.choices)" :value="choice">{{ choice_label }}</option>
				</select>
			</div>
		</div>
	<?php }

	protected function multiselectOption( $option ) { ?>
		<div v-if="<?php echo $option ?>.type == 'multiselect'" class="select-option">
			<label>{{ <?php echo $option ?>.label }}</label>
			<select v-model="<?php echo $option ?>.value" multiple="multiple">
				<option v-for="(choice_label, choice) in fieldsByTypeFormatted(<?php echo $option ?>.choices)" :value="choice">{{ choice_label }}</option>
			</select>
		</div>
	<?php }

}