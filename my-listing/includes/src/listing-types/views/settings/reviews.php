<reviews inline-template v-if="currentSubTab === 'reviews'">
	<div class="tab-content full-width">
		<div class="form-section">
			<h3>Listing Reviews</h3>
			<p>Customize how listing reviews work, enable star ratings, add multiple rating categories, etc.</p>
		</div>

		<div class="editor-column col-1-2 rows row-padding">
			<div class="form-section mb40">
				<h4 class="mb10">Gallery upload</h4>
				<p>Allow users to attach image galleries to their reviews.</p>
				<label class="form-switch">
					<input type="checkbox" v-model="$root.settings.reviews.gallery.enabled">
					<span class="switch-slider"></span>
				</label>
			</div>

			<div class="form-section mb40">
				<h4 class="mb10">Star ratings</h4>
				<p>Allow users to submit a star-based rating alongside their review.</p>

				<label class="form-switch">
					<input type="checkbox" v-model="$root.settings.reviews.ratings.enabled">
					<span class="switch-slider"></span>
				</label>

				<div class="mt10" v-show="!$root.settings.reviews.ratings.enabled">
					<p>Allow users to submit multiple comments?</p>
					<label class="form-switch">
						<input type="checkbox" v-model="$root.settings.reviews.multiple">
						<span class="switch-slider"></span>
					</label>
				</div>
			</div>

			<div class="form-section" :class="!$root.settings.reviews.ratings.enabled ? 'ml-overlay-disabled' : ''">
				<h4 class="mb10">Stars mode</h4>
				<p>
					If set to half-stars, users will be able to leave ratings like 1.5, 2.5, 3.5 and 4.5.
					Otherwise, only full number ratings like 1, 2, 3, 4 and 5 will be possible.
				</p>
				<div class="form-group mb10">
					<label>
						<input type="radio" v-model="$root.settings.reviews.ratings.mode" value="5" class="form-radio">
						<span>Full stars</span>
					</label>
				</div>
				<div class="form-group">
					<label>
						<input type="radio" v-model="$root.settings.reviews.ratings.mode" value="10" class="form-radio">
						<span>Half stars</span>
					</label>
				</div>
			</div>
		</div><!--
		--><div class="editor-column col-1-2 rows row-padding" :class="!$root.settings.reviews.ratings.enabled ? 'ml-overlay-disabled' : ''">
			<h4>Rating Categories</h4>

			<draggable v-model="$root.settings.reviews.ratings.categories" :options="{group: 'settings-reviews-categories', handle: '.row-head'}">
				<div v-for="category in $root.settings.reviews.ratings.categories" class="row-item" :class="isActive( category ) ? 'open' : ''">
					<div @click="activeCategory = ( isActive( category ) ? null : category )" class="row-head">
						<div class="row-head-toggle"><i class="mi chevron_right"></i></div>
						<div class="row-head-label">
							<h4>{{ category.label }}</h4>
							<div class="details">
								<div class="detail">{{ category.id }}</div>
							</div>
						</div>
						<div class="row-head-actions">
							<span
								title="Remove" @click.stop="removeCategory( category )" class="action red"
								v-show="$root.settings.reviews.ratings.categories.length > 1 && category.id !== 'rating'"
							><i class="mi delete"></i></span>
						</div>
					</div>
					<div class="row-edit">
						<div class="form-group">
							<label>Label</label>
							<input type="text" v-model="category.label" @input="category.is_new ? category.id = $root.slugify( category.label ) : null">
						</div>

						<div class="form-group">
							<label>Key</label>
							<input type="text" v-model="category.id" @input="category.is_new ? category.id = $root.slugify( category.id ) : null" :disabled="!category.is_new">
							<p class="form-description" v-show="category.is_new">Needs to be unique. This isn't visible to the user.</p>
						</div>

						<div class="text-right">
							<div class="btn" @click="activeCategory = null">Done</div>
						</div>
					</div>
				</div>
			</draggable>

			<div class="form-group mt20">
				<button class="btn btn-outline pull-right" @click.prevent="addCategory">Add rating category</button>
			</div>
		</div>
	</div>
</reviews>