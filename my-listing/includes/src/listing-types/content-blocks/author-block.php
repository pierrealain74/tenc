<?php

namespace MyListing\Src\Listing_Types\Content_Blocks;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Author_Block extends Base_Block {

	public function props() {
		$this->props['type'] = 'author';
		$this->props['title'] = 'Author';
		$this->props['icon'] = 'mi account_circle';
	}

	public function get_editor_options() {
		$this->getLabelField();
	}
}