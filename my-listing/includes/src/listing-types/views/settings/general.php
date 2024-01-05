<div class="tab-content align-center" v-if="currentSubTab === 'general'">
	<div class="form-section">
		<h3 class="mb20">Labels</h3>

		<div class="form-group">
			<label>Icon source</label>
			<label>
				<input type="radio" v-model="settings.icon_type" value="icon" class="form-radio">
				<span>Icon Font</span>
			</label>
		</div>
		<div class="form-group mb20">
			<label>
				<input type="radio" v-model="settings.icon_type" value="image" class="form-radio">
				<span>Upload Image</span>
			</label>
		</div>

		<div class="form-group mb20" v-if="settings.icon_type === 'icon'">
			<label>Icon</label>
			<iconpicker v-model="settings.icon"></iconpicker>
		</div>

		<div class="form-group mb20" v-if="settings.icon_type === 'image'">
			<label>Image</label>
			<mediauploader v-model="settings.image"></mediauploader>
		</div>

		<div class="form-group mb20">
			<label>Singular name <small>(e.g. Business)</small></label>
			<input type="text" v-model="settings.singular_name">
		</div>

		<div class="form-group mb20">
			<label>Plural name <small>(e.g. Businesses)</small></label>
			<input type="text" v-model="settings.plural_name">
		</div>

		<div class="form-group mb20">
			<label>Permalink <a class="cts-show-tip" data-tip="permalink-docs" title="Click to learn more">[Learn More]</a></label>
			<input type="text" v-model="settings.permalink" placeholder="<?php echo esc_attr( urldecode( $type->get_permalink_name() ) ) ?>">
		</div>
	</div>
</div>