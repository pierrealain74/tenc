<?php
if ( ! defined('ABSPATH') ) {
	exit;
} ?>
<div class="tab-content full-width" v-if="currentSubTab === 'advanced' || currentSubTab === 'basic'">
	<div class="form-section" v-show="currentSubTab == 'advanced'">
		<h3>Customize the advanced search form for this listing type</h3>
		<p>Not sure what's this? <a href="https://docs.mylistingtheme.com/article/configuring-search-forms/" target="_blank">View the docs</a>.</p>
	</div>

	<div class="form-section" v-show="currentSubTab == 'basic'">
		<h3>Customize the basic search form for this listing type</h3>
		<p>Not sure what's this? <a href="https://docs.mylistingtheme.com/article/configuring-search-forms/" target="_blank">View the docs</a>.</p>
	</div>

	<form-filters inline-template>
		<div>
			<div class="editor-column col-2-3 rows row-padding">
				<div class="form-section mb10">
					<h4 class="mb5">Active filters</h4>
					<p>Click on a filter to edit. Drag & Drop to reorder.</p>
				</div>
				<draggable v-model="activeForm.facets" :options="{group: 'filter-types', handle: '.row-head'}">
					<div v-for="filter in activeForm.facets" class="row-item"
						:class="[isActive(filter)?'open':'', 'filter-type-'+filter.type]">
						<div class="row-head" @click="toggleActive(filter)">
							<div class="row-head-toggle"><i class="mi chevron_right"></i></div>
							<div class="row-head-label">
								<h4>{{ filter.label }}</h4>
								<div class="details">
									<div class="detail">{{ filter.type }}</div>
								</div>
							</div>
							<div class="row-head-actions">
								<span title="Primary filter on mobile" class="action gold" v-if="filter.is_primary">
									<i class="mi star"></i>
								</span>
								<span title="Remove" @click.stop="deleteFilter(filter)" class="action red"
									v-if="!(activeFormKey==='advanced' && filter.type==='order')">
									<i class="mi delete"></i>
								</span>
							</div>
						</div>
						<div class="row-edit">
							<?php foreach ( $designer->get_filter_types() as $filter ): ?>
								<?php echo $filter->print_options() ?>
							<?php endforeach ?>

							<div class="text-right">
								<div class="btn btn-xs" @click="activeFilter = null">Done</div>
							</div>
						</div>
					</div>
				</draggable>

				<div v-if="!activeForm.facets.length" class="mt40 text-center">
					<div class="btn btn-plain">
						<i class="mi playlist_add"></i>
						No search filters added yet.
					</div>
				</div>
			</div><!--
			--><div class="editor-column col-1-3">
				<div class="form-section mb10">
					<h4 class="mb5">Available filters</h4>
					<p>Click on a filter to use it.</p>
				</div>

				<div
				 	v-for="filter in filterTypes"
				 	v-if="canAddFilter( filter )"
					class="btn btn-block mb10"
					@click.prevent="addFilter( filter.type )"
				>{{ filter.label }}</div>
			</div>
		</div>
	</form-filters>
</div>