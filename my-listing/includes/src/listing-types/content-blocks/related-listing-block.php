<?php

namespace MyListing\Src\Listing_Types\Content_Blocks;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Related_Listing_Block extends Base_Block {

	public function props() {
		$this->props['type'] = 'related_listing';
		$this->props['title'] = 'Related Listing';
		$this->props['icon'] = 'mi layers';
		$this->props['show_field'] = 'related_listing';
		$this->allowed_fields = [ 'related-listing' ];
	}

	public function get_editor_options() {
		$this->getLabelField();
		$this->getSourceField();
	}

}