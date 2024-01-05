<?php
/**
 * Render filter options for related-listing filter.
 *
 * @since 2.5.0
 */
if ( ! defined('ABSPATH') ) {
	exit;
} ?>

<?php $this->get_label_field() ?>
<?php $this->get_source_field() ?>

<div class="form-group">
	<label>Allow multiple selections?</label>
	<label class="form-switch">
		<input type="checkbox" v-model="filter.multiselect">
		<span class="switch-slider"></span>
	</label>
</div>
