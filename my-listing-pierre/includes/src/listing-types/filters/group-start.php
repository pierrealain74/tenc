<?php

namespace MyListing\Src\Listing_Types\Filters;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Group_Start extends Base_Filter {
	public function filter_props() {
		$this->props['type'] = 'group-start';
		$this->props['label'] = 'Group Start';
	}
}
