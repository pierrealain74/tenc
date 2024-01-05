<?php

namespace MyListing\Src\Listing_Types\Filters;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Heading_Ui extends Base_Filter {
	public function filter_props() {
		$this->props['type'] = 'heading-ui';
		$this->props['label'] = '(UI) Heading';
	}
}
