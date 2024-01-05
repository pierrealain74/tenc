<?php
/**
 * Related Listings tab type, that can use content blocks to create the layout.
 *
 * @since 1.0
 */

namespace MyListing\Src\Listing_Types\Content_Tabs;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Related_Listings_Tab extends Base_Tab {

	public function tab_props() {
		$this->props['page'] = 'related_listings';
		$this->props['related_listing_field'] = 'related_listing';
		$this->props['hide_empty_tab'] = false;
		$this->props['default_label'] = 'Related Listings';
	}

	public function get_editor_options() {
		$this->get_label_field();
		$this->get_slug_field();
		$this->get_hide_empty_tab_field();
		$this->get_source_field();
	}

	public function get_hide_empty_tab_field() { ?>
		<div class="form-group mb20">
			<label>
				<input type="checkbox" v-model="menu_item.hide_empty_tab" class="form-checkbox">
				Hide tab if there are no products
			</label>
		</div>
	<?php }
	
	public function get_source_field() { ?>
		<div class="form-group mb20">
			<label>Related Listings Field:</label>
			<div class="select-wrapper">
				<select v-model="menu_item.related_listing_field">
					<option v-for="field in fieldsByType(['related-listing'])" :value="field.slug">{{ field.label }}</option>
				</select>
			</div>
		</div>
	<?php }
}