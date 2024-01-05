<?php
/**
 * Compare bar.
 *
 * @since 2.7.2
 */
if ( ! defined('ABSPATH') ) {
	exit;
} ?>
<div class="compare-bar" v-if="compare.length >= 2">
	<a href="#" class="buttons button-2" @click.prevent="_compareListing()">
		<i title="mi compare" class="mi compare"></i>
		<?php _ex( 'Compare items', 'compare listings', 'my-listing' ) ?>
	</a>
	<a href="#" class="buttons button-5" @click.prevent="_clearCompareListing()">
		<?php _ex( 'Cancel', 'compare listings', 'my-listing' ) ?>
	</a>
</div>
