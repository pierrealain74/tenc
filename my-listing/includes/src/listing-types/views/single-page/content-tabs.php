<div class="tab-content full-width" v-if="currentSubTab == 'pages'">
	<div class="form-section">
		<h3>Create and organize listing content</h3>
		<p>Not sure what's this? <a href="https://docs.mylistingtheme.com/article/single-page-content-and-tabs/" target="_blank">View the docs</a>.</p>
	</div>

	<div class="editor-column col-2-3 rows row-padding">
		<div class="form-section mb10">
			<h4 class="mb5">Tabs</h4>
			<p>Click on an tab to edit. Drag & Drop to reorder.</p>

			<draggable v-model="single.menu_items" :options="{group: 'single-menu', handle: '.row-head'}">
				<div v-for="menu_item, key in single.menu_items" class="row-item">
					<div class="row-head" @click="setTab( 'single-page', 'edit-tab-'+key )">
						<div class="row-head-toggle"><i class="mi chevron_right"></i></div>
						<div class="row-head-label">
							<h4>{{ menu_item.label || '(no label)' }}</h4>
							<div class="details">
								<div class="detail">#{{ menu_item.slug || slugify( menu_item.label ) }}</div>
							</div>
						</div>
						<div class="row-head-actions">
							<span title="Remove" @click.stop="deleteMenuItem( menu_item )" class="action red"><i class="mi delete"></i></span>
						</div>
					</div>
				</div>
			</draggable>

			<div v-if="!single.menu_items.length" class="btn btn-plain btn-block mt20">
				<i class="mi playlist_add"></i>
				No tabs added yet.
			</div>
		</div>
	</div><!--
	--><div class="editor-column col-1-3">
		<div class="form-section mb10">
			<h4 class="mb5">Preset tabs</h4>
			<p>Click on a tab to use it.</p>

			<div
				v-for="label, key in { main: 'Profile', comments: 'Reviews', related_listings: 'Related Listings', store: 'Store', bookings: 'Bookings', custom: 'Custom' }"
				class="btn btn-block mb10"
				@click.prevent="addMenuItem( key )"
				v-if="key !== 'custom'"
			>{{ label }}</div>

			<div class="text-center mt20">
				<div class="btn btn-outline mb10" @click.prevent="addMenuItem( 'custom' )">Add Custom Tab</div>
			</div>
		</div>
	</div>
</div>

<div v-for="menu_item, key in single.menu_items" v-if="currentSubTab === 'edit-tab-'+key" class="tab-content full-width">

	<div class="mb20 text-center">
		<div class="btn btn-plain btn-xs" @click="setTab( 'single-page', 'pages' )"><i class="mi keyboard_backspace"></i> All Tabs</div>
		<div
			v-for="_menu_item, _key in single.menu_items"
			class="btn btn-xs mb10"
			@click.prevent="setTab( 'single-page', 'edit-tab-'+_key )"
			:class="menu_item === _menu_item ? 'btn-secondary' : 'btn-plain'"
			style="margin-right: 5px;"
		>{{ _menu_item.label || '(no label)' }}</div>
	</div>

	<div class="form-section mb20 full-width">
		<h3>{{ menu_item.label || '(no label)' }}</h3>
		<p>Configure settings for this tab.</p>
	</div>

	<div class="content-tab-settings">
		<div class="form-group">
			<label>Label</label>
			<input type="text" v-model="menu_item.label">
		</div><!--
	  --><div class="form-group">
			<label>URL slug</label>
			<input type="text" :value="menu_item.slug" @input="menu_item.slug = slugify( $event.target.value )" :placeholder="slugify( menu_item.label )">
			<p class="mt5 mb0">This value can be appended to the listing url to link directly to this tab.</p>
		</div><!--
	  --><div class="form-group" v-if="menu_item.page === 'store'">
	  		<label class="mb10">Hide tab if there are no products</label>
			<label class="form-switch">
				<input type="checkbox" v-model="menu_item.hide_if_empty">
				<span class="switch-slider"></span>
			</label>
		</div><!--
	  --><div class="form-group" v-if="['main', 'custom'].indexOf(menu_item.page) !== -1">
			<label>Layout</label>
			<div class="select-wrapper">
				<select v-model="menu_item.template">
					<option value="masonry">Masonry (Two columns)</option>
					<option value="two-columns">Two Columns</option>
					<option value="content-sidebar">Two thirds / One third</option>
					<option value="sidebar-content">One third / Two thirds</option>
					<option value="full-width">Single column</option>
				</select>
			</div>
		</div><!--
	  --><div class="form-group" v-if="menu_item.page == 'store'">
			<label>Display products from field:</label>
			<div class="select-wrapper">
				<select v-model="menu_item.field">
					<option v-for="field in fieldsByType(['select-products', 'select-product'])" :value="field.slug">{{ field.label }}</option>
				</select>
			</div>
		</div><!--
		--><div class="form-group" v-if="menu_item.page === 'related_listings'">
	  		<label class="mb10">Hide tab if there are no listings</label>
			<label class="form-switch">
				<input type="checkbox" v-model="menu_item.hide_empty_tab">
				<span class="switch-slider"></span>
			</label>
		</div><!--
	  --><div class="form-group" v-if="menu_item.page === 'related_listings'">
			<label>
				Related Listings Field
				<a href="#" class="cts-show-tip pull-right" data-tip="related-listings">Learn More</a>
			</label>
			<div class="select-wrapper">
				<select v-model="menu_item.related_listing_field">
					<option v-for="field in fieldsByType(['related-listing'])" :value="field.slug">{{ field.label }}</option>
				</select>
			</div>
		</div><!--
	  --><div class="form-group" v-if="menu_item.page == 'bookings'">
			<label>Booking Method:</label>
			<div class="select-wrapper">
				<select v-model="menu_item.provider">
					<option value="basic-form">Basic Form</option>
					<option value="timekit">Timekit</option>
				</select>
			</div>
		</div><!--
	  --><div class="form-group" v-if="menu_item.page == 'bookings' && menu_item.provider == 'basic-form'">
			<label>Submission sends email to:</label>
			<div class="select-wrapper">
				<select v-model="menu_item.field">
					<option v-for="field in fieldsByType(['email'])" :value="field.slug">{{ field.label }}</option>
				</select>
			</div>
		</div><!--
	  --><div class="form-group" v-if="menu_item.page == 'bookings' && menu_item.provider == 'basic-form'">
			<label>Contact Form ID:</label>
			<input type="text" v-model="menu_item.contact_form_id">
		</div><!--
	  --><div class="form-group" v-if="menu_item.page == 'bookings' && menu_item.provider == 'timekit'">
			<label>TimeKit Widget ID:</label>
			<div class="select-wrapper">
				<select v-model="menu_item.field">
					<option v-for="field in fieldsByType(['text'])" :value="field.slug">{{ field.label }}</option>
				</select>
			</div>
		</div>
	</div>

	<div v-if="menu_item.page == 'main' || menu_item.page == 'custom'" class="mt10">
		<div class="editor-column">
			<div class="tab-columns" :class="['template-'+menu_item.template, menu_item.sidebar.length ? 'sidebar-filled' : 'sidebar-empty']">
				<?php foreach ( ['layout', 'sidebar'] as $key => $column ):
					$layout_classes = "{
						'col-1-2': menu_item.template === 'two-columns',
						'col-2-3': menu_item.template === 'masonry' && '{$column}' === 'layout'
								   || menu_item.template === 'content-sidebar' && '{$column}' === 'layout'
								   || menu_item.template === 'sidebar-content' && '{$column}' === 'layout'
								   || menu_item.template === 'full-width' && '{$column}' === 'layout',
						'col-1-3': menu_item.template === 'masonry' && '{$column}' === 'sidebar'
								   || menu_item.template === 'content-sidebar' && '{$column}' === 'sidebar'
								   || menu_item.template === 'sidebar-content' && '{$column}' === 'sidebar'
								   || menu_item.template === 'full-width' && '{$column}' === 'sidebar',
						'pull-right': menu_item.template === 'sidebar-content' && '{$column}' === 'layout',
					}";
					?><div class="tab-column column-<?php echo $column ?> editor-column rows" :class="[ menu_item.<?php echo $column ?>.length ? 'filled' : 'empty', <?php echo $layout_classes ?> ]">
						<div class="column-inner">
							<?php if ( $column === 'layout' ): ?>
								<h4>Main Column</h4>
							<?php endif ?>

							<?php if ( $column === 'sidebar' ): ?>
								<h4>Sidebar Column</h4>
							<?php endif ?>

							<div class="text-center mt40 mb40" v-if="! menu_item.<?php echo $column ?>.length">
								<div class="btn btn-plain btn-xs">
									<i class="mi info_outline"></i>
									<?php if ( $column === 'layout' ): ?>
										You haven't added any content blocks yet.
									<?php endif ?>
									<?php if ( $column === 'sidebar' ): ?>
										You haven't added any content blocks to the sidebar.
									<?php endif ?>
								</div>
							</div>
							<draggable v-model="menu_item.<?php echo $column ?>" :options="{group: 'layout-blocks', handle: '.row-head'}">
								<div v-for="block in menu_item.<?php echo $column ?>" class="row-item" :class="(block === state.single.active_block ? 'open' : '')">
									<div @click="state.single.active_block = ( block !== state.single.active_block ) ? block : null" class="row-head">
										<div class="row-head-toggle"><i class="mi chevron_right"></i></div>
										<div class="row-head-label">
											<h4>{{ block.title }}</h4>
											<div class="details">
												<div class="detail">{{ block.type }}</div>
											</div>
										</div>
										<div class="row-head-actions">
											<span title="Move" @click.stop="moveBlock(block, '<?php echo $column ?>', menu_item)" class="action blue"><i class="mi compare_arrows"></i></span>
											<span title="Delete this field" @click.stop="deleteBlock(block, '<?php echo $column ?>', menu_item)" class="action red"><i class="mi delete"></i></span>
										</div>
									</div>

									<div class="row-edit">
										<?php foreach ( $designer->get_block_types() as $block ): ?>
											<?php echo $block->print_options() ?>
										<?php endforeach ?>

										<div class="text-right">
											<div class="btn" @click="state.single.active_block = null">Done</div>
										</div>
									</div>
								</div>
							</draggable>
						</div>
					</div><?php
				endforeach ?>
				<div style="clear:both;"></div>
			</div>
		</div><!--
		--><div class="editor-column add-new-content-block mt20">
			<h4 class="mb20">Insert a New Block</h4>

			<div class="content-blocks">
				<div
					v-for="block in blueprints.layout_blocks"
					class="btn btn-block mb10"
					@click.prevent="addBlock( block.type, menu_item )"
				>{{ block.title }}</div>
			</div>
		</div>
	</div>
</div>