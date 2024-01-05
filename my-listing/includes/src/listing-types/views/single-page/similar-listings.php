<?php
/**
 * Similar listings settings template for the listing type editor.
 *
 * @since 2.0
 */
if ( ! defined('ABSPATH') ) {
	exit;
} ?>
<div v-if="currentSubTab == 'similar-listings'" class="tab-content align-center">
	<div class="form-section mb40">
		<h3>Enable Similar listings</h3>
		<p>
			You can optionally display a list of similar listings in the single listing page.
			This section will appear at the end of the page, below the current listing information.
		</p>
		<div class="form-group">
			<label class="form-switch">
				<input type="checkbox" v-model="single.similar_listings.enabled">
				<span class="switch-slider"></span>
			</label>
		</div>
	</div>

	<div :class="!single.similar_listings.enabled?'ml-overlay-disabled':''">
		<div class="form-section mb40">
			<h4 class="mb10">Matching similar listings</h4>
			<p>Determine what should classify as a similar listing to the currently active one, based on the following attributes.</p>

			<div class="form-group mb10">
				<label>
					<input type="checkbox" v-model="single.similar_listings.match_by_type" class="form-checkbox">
					Must belong to the same listing type.
				</label>
			</div>

			<div class="form-group mb10">
				<label>
					<input type="checkbox" v-model="single.similar_listings.match_by_category" class="form-checkbox">
					Must have at least one category in common.
				</label>
			</div>

			<div class="form-group mb10">
				<label>
					<input type="checkbox" v-model="single.similar_listings.match_by_tags" class="form-checkbox">
					Must have at least one tag in common.
				</label>
			</div>

			<div class="form-group mb10">
				<label>
					<input type="checkbox" v-model="single.similar_listings.match_by_region" class="form-checkbox">
					Must belong to the same region (Regions taxonomy).
				</label>
			</div>
		</div>

		<div class="form-section mb40">
			<h4 class="mb10">Displaying similar listings</h4>
			<div class="form-group">
				<label>Order listings by</label>
				<div class="select-wrapper" style="display: inline-block; width: 160px;">
					<select v-model="single.similar_listings.orderby">
						<option value="priority">Priority</option>
						<option value="rating">Rating</option>
						<option value="proximity">Proximity</option>
						<option value="random">Random</option>
					</select>
				</div>
			</div>

			<div class="form-group" v-show="single.similar_listings.orderby === 'proximity'">
				<br>
				<label>Listing must be within radius (in kilometers)</label>
				<input type="number" v-model="single.similar_listings.max_proximity" style="display: inline-block; width: 100px;">
			</div>

			<div class="form-group">
				<br>
				<label>Number of listings to show</label>
				<input type="number" v-model="single.similar_listings.listing_count" style="display: inline-block; width: 100px;">
			</div>
		</div>
	</div>
</div>