<?php

namespace MyListing\Src\Listing_Types\Content_Blocks;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Text_Block extends Base_Block {

	public function props() {
		$this->props['type'] = 'text';
		$this->props['title'] = 'Textarea';
		$this->props['icon'] = 'mi view_headline';
		$this->props['show_field'] = '';
		$this->allowed_fields = [ 'text', 'texteditor', 'wp-editor', 'checkbox', 'radio', 'select', 'multiselect', 'textarea', 'email', 'url', 'number', 'location' ];
	}

	public function get_editor_options() {
		$this->getLabelField();
		$this->getSourceField();
	}

}