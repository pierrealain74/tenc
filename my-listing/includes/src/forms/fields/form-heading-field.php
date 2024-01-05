<?php

namespace MyListing\Src\Forms\Fields;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Form_Heading_Field extends Base_Field {

	public function get_posted_value() {
		return '';
	}

	public function validate() {
		//
	}

	public function field_props() {
		$this->props['type'] = 'form-heading';
		$this->props['label'] = 'Heading';
		$this->props['is_ui'] = true;
		$this->props['icon'] = 'icon-pencil-2';
	}

	public function get_editor_options() {
		$this->getLabelField();
		$this->getKeyField();
		$this->getIconField();
		$this->getShowInCompareField();
	}

}