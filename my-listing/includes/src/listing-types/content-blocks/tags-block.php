<?php

namespace MyListing\Src\Listing_Types\Content_Blocks;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Tags_Block extends Base_Block {

	public function props() {
		$this->props['type'] = 'tags';
		$this->props['title'] = 'Tags';
		$this->props['icon'] = 'mi view_module';
	}

	public function get_editor_options() {
		$this->getLabelField();
	}

}