<div class="tab-content full-width fields-tab">
	<div class="form-section">
		<h3>Select or create fields for this listing type</h3>
		<p>
			Need help? Read the <a href="https://docs.mylistingtheme.com/article/listing-type-fields-tab/" target="_blank">documentation</a>
			or open a ticket in our <a href="https://helpdesk.27collective.net/" target="_blank">helpdesk</a>.
		</p>
	</div>

	<div class="editor-column col-2-3 rows row-padding">
		<div class="form-section mb10">
			<h4 class="mb5">Used fields</h4>
			<p>Click on a field to edit. Drag & Drop to reorder.</p>
		</div>

		<draggable v-model="fields.used" :options="{group: 'listing-fields', handle: '.row-head'}">
			<div v-for="field in fields.used" :class="'row-item field-type-'+field.type+' field-name-'+field.slug+' '+(field === state.fields.active ? 'open' : '')">
				<div @click="state.fields.active = ( field !== state.fields.active ) ? field : null" class="row-head">
					<div class="row-head-toggle"><i class="mi chevron_right"></i></div>
					<div class="row-head-label">
						<h4>{{ field.label }}</h4>
						<div class="details">
							<div class="detail" v-if="!field.is_custom">{{ field.default_label ? field.default_label : field.type }}</div>
							<div class="detail" v-if="field.is_custom">{{ capitalize( field.type ) }}</div>
							<div class="detail" v-if="field.is_custom">Custom Field</div>
						</div>
					</div>
					<div class="row-head-actions">
						<span title="Form heading element" class="action violet" v-if="field.type === 'form-heading'"><i class="mi format_size"></i></span>
						<span title="Remove" @click.stop="deleteField(field.slug)" class="action red" v-if="field.slug !== 'job_title'"><i class="mi delete"></i></span>
						<span title="This field cannot be deleted" v-if="field.slug === 'job_title'" class="action gray"><i class="mi lock"></i></span>
					</div>
				</div>
				<div class="row-edit" v-if="state.fields.active === field">
					<?php foreach ( $designer->get_field_types() as $field ): ?>
						<?php echo $field->print_editor_options() ?>
					<?php endforeach ?>
					<div class="text-right">
						<div class="btn" @click="state.fields.active = null">Done</div>
					</div>
				</div>
			</div>
		</draggable>
	</div><!--
	--><div class="editor-column col-1-3">
		<div class="form-section mb10">
			<h4 class="mb5">Preset fields</h4>
			<p>Click on a field to use it.</p>
		</div>

		<div
			v-for="field in editor.preset_fields"
			@click="usePresetField( field.slug )"
			class="btn btn-secondary btn-block mb10"
			v-if="!field._used"
		>{{ field.default_label ? field.default_label : field.label }}</div>

		<div class="form-section mb10 mt40">
			<h4 class="mb5">Create a custom field</h4>
			<p>Click on the field type you want to create.</p>

			<div
				v-for="value, key in { all: 'All', input: 'Input', choice: 'Choice', relational: 'Relational', ui: 'UI', }"
				@click="state.custom_field_category = key"
				class="btn btn-xs mb10"
				:class="state.custom_field_category === key ? 'btn-secondary' : 'btn-plain'"
			>{{ value }}</div>
		</div>

		<div v-if="state.custom_field_category === 'all' || state.custom_field_category === 'input'">
			<div class="btn btn-secondary btn-block mb10" @click="addCustomField('text')">Text</div>
			<div class="btn btn-secondary btn-block mb10" @click="addCustomField('textarea')">Textarea</div>
			<div class="btn btn-secondary btn-block mb10" @click="addCustomField('wp-editor')">WP Editor</div>
			<div class="btn btn-secondary btn-block mb10" @click="addCustomField('password')">Password</div>
			<div class="btn btn-secondary btn-block mb10" @click="addCustomField('date')">Date</div>
			<div class="btn btn-secondary btn-block mb10" @click="addCustomField('recurring-date')">Recurring Date</div>
			<div class="btn btn-secondary btn-block mb10" @click="addCustomField('number')">Number</div>
			<div class="btn btn-secondary btn-block mb10" @click="addCustomField('url')">URL</div>
			<div class="btn btn-secondary btn-block mb10" @click="addCustomField('email')">Email</div>
			<div class="btn btn-secondary btn-block mb10" @click="addCustomField('file')">File Upload</div>
			<div class="btn btn-secondary btn-block mb10" @click="addCustomField('general-repeater')">General Repeater</div>
		</div>

		<div v-if="state.custom_field_category === 'all' || state.custom_field_category === 'choice'">
			<div class="btn btn-secondary btn-block mb10" @click="addCustomField('select')">Select</div>
			<div class="btn btn-secondary btn-block mb10" @click="addCustomField('multiselect')">Multiselect</div>
			<div class="btn btn-secondary btn-block mb10" @click="addCustomField('checkbox')">Checkboxes</div>
			<div class="btn btn-secondary btn-block mb10" @click="addCustomField('radio')">Radio Buttons</div>
		</div>

		<div v-if="state.custom_field_category === 'all' || state.custom_field_category === 'relational'">
			<div class="btn btn-secondary btn-block mb10" @click="addCustomField('related-listing')">Related Listings</div>
			<div class="btn btn-secondary btn-block mb10" @click="addCustomField('select-product')">Product Select</div>
			<div class="btn btn-secondary btn-block mb10" @click="addCustomField('select-products')">Product Multiselect</div>
		</div>

		<div v-if="state.custom_field_category === 'all' || state.custom_field_category === 'ui'">
			<div class="btn btn-secondary btn-block mb10" @click="addCustomField('form-heading')">Form Heading</div>
		</div>
	</div>
</div>

<!-- <pre>{{ fields.used }}</pre> -->
