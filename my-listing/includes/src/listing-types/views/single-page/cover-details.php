<?php
/**
 * Cover details and actions in the listing type editor.
 *
 * @since 2.0
 */
if ( ! defined('ABSPATH') ) {
	exit;
}

$detail_limit = absint( apply_filters( 'mylisting/type-editor/cover-details/limit', 3 ) );
?>
<div class="tab-content full-width" v-if="currentSubTab == 'cover-details'">
	<div class="form-section">
		<h3>Cover details and call-to-actions</h3>
		<p>Display important listing information and actions on the cover section of the listing. Up to <?php echo $detail_limit ?> items can be added.</p>
	</div>

	<div class="editor-column col-2-3 rows row-padding">
		<div class="form-section mb10">
			<h4 class="mb5">Active details</h4>
			<p>Click on a detail to edit. Drag & Drop to reorder.</p>
		</div>

		<draggable v-model="single.cover_details" :options="{group: 'cover-details-list', handle: '.row-head'}">
			<div v-for="detail, key in single.cover_details" class="row-item" :class="detail === state.single.active_detail ? 'open' : ''">
				<div class="row-head" @click="state.single.active_detail = ( detail !== state.single.active_detail ) ? detail : null">
					<div class="row-head-toggle"><i class="mi chevron_right"></i></div>
					<div class="row-head-label">
						<h4>{{ detail.label }}</h4>
						<div class="details">
							<div class="detail">Field: {{ fieldLabelBySlug( detail.field ) || 'None' }}</div>
						</div>
					</div>
					<div class="row-head-actions">
						<span title="Remove" @click.stop="coverDetails().remove(detail)" class="action red"><i class="mi delete"></i></span>
					</div>
				</div>
				<div class="row-edit">
					<div class="form-group full-width">
						<label>Label</label>
						<input type="text" v-model="detail.label">
					</div>

					<div class="form-group full-width">
						<label>Field</label>
						<div class="select-wrapper">
							<select v-model="detail.field">
								<option v-for="field in textFields()" :value="field.slug">{{ field.label }}</option>
							</select>
						</div>
					</div>

					<div class="form-group full-width">
						<label>Format</label>
						<div class="select-wrapper">
							<select v-model="detail.format">
								<option value="plain">None</option>
								<option value="number">Number</option>
								<option value="date">Date</option>
								<option value="datetime">Date & Time</option>
								<option value="time">Time</option>
							</select>
						</div>
					</div>

					<div class="form-group full-width">
						<label>Prefix</label>
						<input type="text" v-model="detail.prefix">
					</div>

					<div class="form-group full-width">
						<label>Suffix</label>
						<input type="text" v-model="detail.suffix">
					</div>

					<div class="text-right">
						<div class="btn" @click="state.single.active_detail = null">Done</div>
					</div>
				</div>
			</div>
		</draggable>
		<div v-if="single.cover_details.length + single.cover_actions.length >= <?php echo $detail_limit ?>" class="btn btn-plain btn-block">
			<i class="mi error_outline"></i>
			You've reached the maximum number of details allowed (<?php echo $detail_limit ?>).
		</div>
		<div v-else-if="!single.cover_details.length" class="btn btn-plain btn-block mt20">
			<i class="mi playlist_add"></i>
			No details added yet.
		</div>
	</div><!--
	--><div class="editor-column col-1-3" :class="single.cover_details.length + single.cover_actions.length >= <?php echo $detail_limit ?> ? 'ml-overlay-disabled' : ''">
		<div class="form-section mb10">
			<h4 class="mb5">Preset details</h4>
			<p>Click on a detail to use it.</p>

			<div class="btn btn-block mb10" @click.prevent="coverDetails().add( 'Event date', 'event_date', 'plain' )">Event date</div>
			<div class="btn btn-block mb10" @click.prevent="coverDetails().add( 'Date', 'job_date', 'date' )">Date</div>
			<div class="btn btn-block mb10" @click.prevent="coverDetails().add( 'Price', 'price_range', 'plain' )">Price</div>
			<div class="btn btn-block mb10" @click.prevent="coverDetails().add( 'Contact email', 'job_email', 'plain' )">Contact email</div>

			<div class="text-center mt20">
				<div class="btn btn-outline mb10" @click.prevent="coverDetails().add( 'New detail...' )">Add Custom Detail</div>
			</div>
		</div>
	</div>
	<div class="mb40"></div>
	<div class="editor-column col-2-3 rows row-padding">
		<div class="form-section mb10">
			<h4 class="mb5">Add Call-to-Action</h4>
			<p>Click on an action to edit. Drag & Drop to reorder.</p>
		</div>

		<draggable v-model="single.cover_actions" :options="{group: 'cover-actions-list', handle: '.row-head'}">
			<div v-for="action, key in single.cover_actions" class="row-item" :class="action === state.single.active_cover_action ? 'open' : ''">
				<div class="row-head" @click="state.single.active_cover_action = ( action !== state.single.active_cover_action ) ? action : null">
					<div class="row-head-toggle"><i class="mi chevron_right"></i></div>
					<div class="row-head-label">
						<h4>{{ action.label }}</h4>
						<div class="details">
							<div class="detail">{{ action.action }}</div>
						</div>
					</div>
					<div class="row-head-actions">
						<span title="Remove" @click.stop="coverActions().remove(action)" class="action red"><i class="mi delete"></i></span>
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
						<div class="btn" @click="state.single.active_cover_action = null">Done</div>
					</div>
				</div>
			</div>
		</draggable>
		<div v-if="single.cover_details.length + single.cover_actions.length >= <?php echo $detail_limit ?>" class="btn btn-plain btn-block">
			<i class="mi error_outline"></i>
			You've reached the maximum number of details allowed (<?php echo $detail_limit ?>).
		</div>
		<div v-else-if="!single.cover_actions.length" class="btn btn-plain btn-block mt20">
			<i class="mi playlist_add"></i>
			No details added yet.
		</div>
	</div><!--
	--><div class="editor-column col-1-3" :class="single.cover_details.length + single.cover_actions.length >= <?php echo $detail_limit ?> ? 'ml-overlay-disabled' : ''">
		<div class="form-section mb10">
			<h4 class="mb5">Preset actions</h4>
			<p>Click on an action to use it.</p>

			<div
				v-for="action in blueprints.quick_actions"
				class="btn btn-block mb10"
				@click.prevent="coverActions().add( action.action )"
				v-if="action.action !== 'custom'"
			>{{ action.label }}</div>

			<div class="text-center mt20">
				<div class="btn btn-outline mb10" @click.prevent="coverActions().add( 'custom' )">Add Custom Action</div>
			</div>
		</div>
	</div>
</div>
