<?php

namespace MyListing\Src\Listing_Types\Filters;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Group_End extends Base_Filter {
	public function filter_props() {
		$this->props['type'] = 'group-end';
		$this->props['label'] = 'Group End';
	}
}
