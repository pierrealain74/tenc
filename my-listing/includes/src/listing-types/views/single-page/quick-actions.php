<?php
/**
 * Quick actions template for the listing type editor.
 *
 * @since 2.0
 */
if ( ! defined('ABSPATH') ) {
	exit;
} ?>
<div class="tab-content full-width" v-if="currentSubTab == 'quick-actions'">
	<div class="form-section">
		<h3>Add quick actions</h3>
		<p>Help users quickly access important listing details through quick actions. If the list is left empty, a default list of actions will be used instead.</p>
	</div>

	<div class="editor-column col-2-3 rows row-padding">
		<div class="form-section mb10">
			<h4 class="mb5">Active quick actions</h4>
			<p>Click on an action to edit. Drag & Drop to reorder.</p>
		</div>

		<draggable v-model="single.quick_actions" :options="{group: 'quick-actions-list', handle: '.row-head'}">
			<div v-for="action, key in single.quick_actions" class="row-item" :class="action === state.single.active_quick_action ? 'open' : ''">
				<div class="row-head" @click="state.single.active_quick_action = ( action !== state.single.active_quick_action ) ? action : null">
					<div class="row-head-toggle"><i class="mi chevron_right"></i></div>
					<div class="row-head-label">
						<h4>{{ action.label }}</h4>
						<div class="details">
							<div class="detail">{{ action.action }}</div>
						</div>
					</div>
					<div class="row-head-actions">
						<span title="Remove" @click.stop="quickActions().remove(action)" class="action red"><i class="mi delete"></i></span>
					</div>
				</div>
				<div class="row-edit">
					<div class="form-group">
						<label>Icon</label>
						<iconpicker v-model="action.icon"></iconpicker>
					</div>

					<div class="form-group full-width">
						<label>Label</label>
						<atwho v-model="action.label" template="input"></atwho>
					</div>

					<div class="form-group full-width" v-if="typeof action.active_label !== 'undefined'">
						<label>Active Label</label>
						<atwho v-model="action.active_label" template="input"></atwho>
					</div>

					<div class="form-group full-width" v-if="typeof action.link !== 'undefined'">
						<label>Link to</label>
						<atwho v-model="action.link" template="input" placeholder="e.g. `tel:[[phone]]`"></atwho>
					</div>

					<div class="form-group full-width" v-if="typeof action.open_new_tab !== 'undefined'">
						<label>
							<input type="checkbox" v-model="action.open_new_tab" class="form-checkbox">
							Open link in new tab
						</label>
					</div>

					<div class="form-group full-width" v-if="typeof action.track_custom_btn !== 'undefined'">
						<label>
							<input type="checkbox" v-model="action.track_custom_btn" class="form-checkbox">
							Enable Tracking
						</label>
					</div>
					
					<div class="text-right">
						<div class="btn" @click="state.single.active_quick_action = null">Done</div>
					</div>
				</div>
			</div>
		</draggable>

		<div v-if="!single.quick_actions.length" class="btn btn-plain btn-block mt20">
			<i class="mi playlist_add"></i>
			No quick actions added yet.
		</div>
	</div><!--
	--><div class="editor-column col-1-3">
		<div class="form-section mb10">
			<h4 class="mb5">Preset actions</h4>
			<p>Click on an action to use it.</p>

			<div
				v-for="action in blueprints.quick_actions"
				class="btn btn-block mb10"
				@click.prevent="quickActions().add( action.action )"
				v-if="action.action !== 'custom'"
			>{{ action.label }}</div>

			<div class="text-center mt20">
				<div class="btn btn-outline mb10" @click.prevent="quickActions().add( 'custom' )">Add Custom Action</div>
			</div>
		</div>
	</div>
</div>