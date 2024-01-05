<?php

namespace MyListing\Src\Listing_Types\Content_Blocks;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Categories_Block extends Base_Block {

	public function props() {
		$this->props['type'] = 'categories';
		$this->props['title'] = 'Categories';
		$this->props['icon'] = 'mi view_module';
	}

	public function get_editor_options() {
		$this->getLabelField();
	}

}