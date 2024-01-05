<?php
/**
 * Store tab type, that can use content blocks to create the layout.
 *
 * @since 1.0
 */

namespace MyListing\Src\Listing_Types\Content_Tabs;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Store_Tab extends Base_Tab {

	public function tab_props() {
		$this->props['page'] = 'store';
		$this->props['field'] = '';
		$this->props['hide_if_empty'] = false;
		$this->props['default_label'] = 'Store';
	}

	public function get_editor_options() {
		$this->get_label_field();
		$this->get_slug_field();
		$this->get_hide_if_empty_field();
		$this->get_source_field();
	}

	public function get_hide_if_empty_field() { ?>
		<div class="form-group mb20">
			<label>
				<input type="checkbox" v-model="menu_item.hide_if_empty" class="form-checkbox">
				Hide tab if there are no products
			</label>
		</div>
	<?php }

	public function get_source_field() { ?>
		<div class="form-group mb20">
			<label>Display products from field:</label>
			<div class="select-wrapper">
				<select v-model="menu_item.field">
					<option v-for="field in fieldsByType(['select-products'])" :value="field.slug">{{ field.label }}</option>
				</select>
			</div>
		</div>
	<?php }
}