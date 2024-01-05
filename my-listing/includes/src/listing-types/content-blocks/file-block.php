<?php

namespace MyListing\Src\Listing_Types\Content_Blocks;

if ( ! defined('ABSPATH') ) {
	exit;
}

class File_Block extends Base_Block {

	public function props() {
		$this->props['type'] = 'file';
		$this->props['title'] = 'Files';
		$this->props['icon'] = 'mi attach_file';
		$this->props['show_field'] = '';
		$this->allowed_fields = [ 'file' ];
	}

	public function get_editor_options() {
		$this->getLabelField();
		$this->getSourceField();
	}
}