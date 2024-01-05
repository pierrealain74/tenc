<?php
if ( ! defined('ABSPATH') ) {
	exit;
}

$explore_tab_limit = absint( apply_filters( 'mylisting/type-editor/explore-tabs/limit', 3 ) );
?>

<div class="tab-content full-width" v-if="currentSubTab == 'explore-tabs'">
	<div class="form-section">
		<h3>Explore Tabs</h3>
		<p>Set what tabs should be shown in the Explore page sidebar for this listing type.</p>
	</div>

	<div class="editor-column col-2-3 rows row-padding">
		<div class="form-section mb10">
			<h4 class="mb5">Active Tabs</h4>
			<p>Click on a tab to edit. Drag & drop to reorder.</h4>
		</div>

		<draggable v-model="search.explore_tabs" :options="{group: 'search-explore-tabs', handle: '.row-head'}">
			<div v-for="tab in search.explore_tabs" class="row-item" :class="tab === state.search.active_explore_tab ? 'open' : ''">
				<div class="row-head" @click="state.search.active_explore_tab = ( tab !== state.search.active_explore_tab ) ? tab : null">
					<div class="row-head-toggle"><i class="mi chevron_right"></i></div>
					<div class="row-head-label">
						<h4>{{ tab.label }}</h4>
						<div class="details">
							<div class="detail">{{ capitalize( tab.type ) }}</div>
						</div>
					</div>
					<div class="row-head-actions">
						<span title="Remove" @click.stop="searchTab().removeTab(tab)" class="action red"><i class="mi delete"></i></span>
					</div>
				</div>
				<div class="row-edit">
					<div class="form-group">
						<label>Label</label>
						<input type="text" v-model="tab.label">
					</div>

					<div class="form-group">
						<label>Icon</label>
						<iconpicker v-model="tab.icon"></iconpicker>
					</div>

					<div v-show="tab.type !== 'search-form'">
						<div class="form-group">
							<label>Order by</label>
							<div class="select-wrapper">
								<select v-model="tab.orderby">
									<option value="name">Name</option>
									<option value="count">Count</option>
								</select>
							</div>
						</div>

						<div class="form-group">
							<label>Order</label>
							<div class="select-wrapper">
								<select v-model="tab.order">
									<option value="ASC">Ascending</option>
									<option value="DESC">Descending</option>
								</select>
							</div>
						</div>

						<div class="form-group">
							<label>
								<input type="checkbox" v-model="tab.hide_empty" class="form-checkbox">
								Hide empty terms?
							</label>
							<p>If checked, terms that won't retrieve any results will not be shown.</p>
						</div>
					</div>

					<div class="text-right">
						<div class="btn" @click="state.search.active_explore_tab = null">Done</div>
					</div>
				</div>
			</div>
		</draggable>
		<div v-if="search.explore_tabs.length >= <?php echo $explore_tab_limit ?>" class="mt40 text-center">
			<div class="btn btn-plain">
				<i class="mi error_outline"></i>
				You've reached the maximum number of tabs allowed (<?php echo $explore_tab_limit ?>).
			</div>
		</div>
		<div v-else-if="!search.explore_tabs.length" class="mt40 text-center">
			<div class="btn btn-plain">
				<i class="mi playlist_add"></i>
				No tabs added yet.
			</div>
		</div>
	</div><!--
	--><div class="editor-column col-1-3">
		<div class="form-section mb10" :class="search.explore_tabs.length >= <?php echo $explore_tab_limit ?> ? 'ml-overlay-disabled' : ''">
			<h4 class="mb5">Available Tabs</h4>
			<p>Click on a tab to use it.</h4>

			<div
				v-for="preset in blueprints.explore_tabs"
				class="btn btn-secondary btn-block mb10"
				@click.prevent="searchTab().addTab( preset )"
				v-if="search.explore_tabs.filter( function(t) { return t.type === preset.type } ).length === 0"
			>{{ preset.label }}</div>
		</div>
	</div>
</div>