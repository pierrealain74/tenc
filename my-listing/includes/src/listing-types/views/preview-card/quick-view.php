<?php
$quick_view_templates = apply_filters( 'mylisting/type-editor/quick-view-templates', [
	'default' => 'Default',
	'alternate' => 'Alternate',
] );
?>
<div v-show="currentSubTab === 'quick-view'" class="tab-content full-width">
	<div class="form-section">
		<h3>Customize the quick view modal</h3>
		<p>
			Need help? Read the <a href="http://docs.mylistingtheme.com/article/configuring-the-preview-card-results-template/" target="_blank">documentation</a>
			or open a ticket in our <a href="https://helpdesk.27collective.net/" target="_blank">helpdesk</a>.
		</p>
	</div>

	<div class="editor-column col-1-3">
		<div class="form-section">
			<h4>Design</h4>

			<div class="form-group mb20">
				<label>Template</label>
				<div class="select-wrapper">
					<select v-model="result.quick_view.template">
						<?php foreach ( (array) $quick_view_templates as $key => $label ): ?>
							<option value="<?php echo esc_attr( $key ) ?>">
								<?php echo esc_html( $label ) ?>
							</option>
						<?php endforeach ?>
					</select>
				</div>
			</div>

			<div class="form-group mb20" v-show="result.quick_view.template == 'default'">
				<label>Map Skin</label>
				<div class="select-wrapper">
					<select v-model="result.quick_view.map_skin">
						<option v-for="(skin_name, skin_key) in blueprints.map_skins" :value="skin_key">{{ skin_name }}</option>
					</select>
				</div>
			</div>
			<div class="form-group mb20">
				<label>Taxonomy</label>
				<div class="select-wrapper">
					<select v-model="result.quick_view.taxonomy">
						<option value="none">None</option>
						<?php $taxonomies = get_taxonomies( [ 'object_type' => [ 'job_listing' ] ], 'objects' ) ?>
						<?php foreach ( (array) $taxonomies as $tax ): ?>
							<option value="<?php echo esc_attr( $tax->name ) ?>">
								<?php echo esc_html( $tax->label ) ?>
							</option>
						<?php endforeach ?>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label>Taxonomy label</label>
				<div>
					<input type="text" v-model="result.quick_view.taxonomy_label" placeholder="Categories">
				</div>
			</div>
		</div>
	</div><!--
	--><div class="editor-column col-2-3">
		<div class="quick-view-template" :class="'template-'+result.quick_view.template">
			<div class="background"></div>
			<div class="details">
				<div class="line"></div>
				<div class="line"></div>
				<div class="line"></div>
				<div class="line"></div>
			</div>
			<div class="map"></div>
		</div>
	</div>
</div>
