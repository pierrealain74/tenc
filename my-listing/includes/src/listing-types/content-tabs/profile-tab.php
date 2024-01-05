<?php
/**
 * Profile tab type, that can use content blocks to create the layout.
 *
 * @since 1.0
 */

namespace MyListing\Src\Listing_Types\Content_Tabs;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Profile_Tab extends Base_Tab {

	public function tab_props() {
		$this->props['page'] = 'main';
		$this->props['default_label'] = 'Profile';
		$this->props['layout'] = [];
		$this->props['sidebar'] = [];
		$this->props['template'] = 'masonry';
	}

	public function get_editor_options() {
		$this->get_label_field();
		$this->get_slug_field();
		$this->get_layout_field();
	}

}