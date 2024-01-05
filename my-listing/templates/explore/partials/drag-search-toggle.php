<?php
/**
 * Drag search toggle.
 *
 * @since 2.7.2
 */
if ( ! defined('ABSPATH') ) {
	exit;
} ?>
<?php if ( $data['drag_search'] ): ?>
	<div class="mapdrag-switch" :class="{'mb-skin': mapProvider === 'mapbox'}">
		<div class="md-checkbox">
			<input type="checkbox" v-model="dragSearch" id="explore-drag-toggle">
			<label for="explore-drag-toggle"><?php _ex( 'Search as I move the map', 'Explore', 'my-listing' ) ?></label>
		</div>
	</div>
<?php endif ?>
