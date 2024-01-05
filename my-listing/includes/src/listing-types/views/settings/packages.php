<packages inline-template v-if="currentSubTab === 'packages'">
	<div class="tab-content settings-packages full-width">
		<div class="form-section">
			<h3>Paid listing packages</h3>
			<p>Set what packages the user can choose from when submitting a listing of this type.</p>
		</div>

		<div class="editor-column col-2-3 rows row-padding">
			<h4>Enable paid listing packages</h4>
			<label class="form-switch mb20">
				<input type="checkbox" v-model="$root.settings.packages.enabled">
				<span class="switch-slider"></span>
			</label>

			<div :class="! $root.settings.packages.enabled ? 'ml-overlay-disabled' : ''">
				<draggable v-model="$root.settings.packages.used" :options="{group: 'settings-packages', handle: '.row-head'}">
					<div v-for="package in $root.settings.packages.used" class="row-item" :class="isActive( package ) ? 'open' : ''">
						<div @click="activePackage = ( isActive( package ) ? null : package )" class="row-head">
							<div class="row-head-toggle"><i class="mi chevron_right"></i></div>
							<div class="row-head-label">
								<h4>{{ $root.getPackageTitle( package ) }}</h4>
								<div class="details">
									<div class="detail">Product: {{ $root.getPackageDefaultTitle( package ) }}</div>
								</div>
							</div>
							<div class="row-head-actions">
								<span title="This package is highlighted" class="action gold" v-show="package.featured"><i class="mi star"></i></span>
								<span title="Remove" @click.stop="remove( package )" class="action red"><i class="mi delete"></i></span>
							</div>
						</div>
						<div class="row-edit">
							<div class="form-group">
								<label>Label</label>
								<input type="text" v-model="package.label" :placeholder="$root.getPackageDefaultTitle( package )">
								<p class="mb0">Leave blank to use the default package label.</p>
							</div>

							<div class="form-group">
								<label>Description</label>
								<textarea v-model="package.description" placeholder="Put each feature in a new line"></textarea>
								<p class="mb0">Leave blank to use the default package description.</p>
							</div>

							<div class="form-group">
								<div class="mb5"></div>
								<label><input type="checkbox" v-model="package.featured" class="form-checkbox"> Featured?</label>
								<p class="mb0">Featured packages will be highlighted.</p>
							</div>

							<div class="text-right">
								<div class="btn" @click="activePackage = null">Done</div>
							</div>
						</div>
					</div>
				</draggable>

				<div class="text-center mt40" v-if="!$root.settings.packages.used.length">
					<div class="btn btn-plain">
						You haven't added any paid listing plans yet. Click on a product<br>
						on the right, or use the "Create New Product" button.
					</div>
				</div>
			</div>
		</div><!--
		--><div class="editor-column col-1-3" :class="! $root.settings.packages.enabled ? 'ml-overlay-disabled' : ''">
			<h4>List of packages</h4>
			<p>All WooCommerce products of type "Listing Package" or "Listing Subscription" appear here.</p>

			<div
				v-for="name, id in $root.state.settings.packages"
				@click="add( id )"
				class="btn btn-secondary btn-block mb10"
				v-if="! isUsed( id )"
			>{{ name }}</div>

			<div class="text-center mt40">
				<a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=product' ) ) ?>" target="_blank" class="btn btn-outline">Create New Product</a>
			</div>
		</div>
	</div>
</packages>