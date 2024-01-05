<?php
/**
 * Reviews tab type, that can use content blocks to create the layout.
 *
 * @since 1.0
 */

namespace MyListing\Src\Listing_Types\Content_Tabs;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Reviews_Tab extends Base_Tab {

	public function tab_props() {
		$this->props['page'] = 'comments';
		$this->props['default_label'] = 'Reviews';
	}

	public function get_editor_options() {
		$this->get_label_field();
		$this->get_slug_field();
	}

}