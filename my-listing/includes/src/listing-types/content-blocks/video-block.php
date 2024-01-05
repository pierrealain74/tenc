<?php

namespace MyListing\Src\Listing_Types\Content_Blocks;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Video_Block extends Base_Block {

	public function props() {
		$this->props['type'] = 'video';
		$this->props['title'] = 'Video';
		$this->props['icon'] = 'mi videocam';
		$this->props['show_field'] = 'job_video_url';
		$this->allowed_fields = [ 'url' ];
	}

	public function get_editor_options() {
		$this->getLabelField();
		$this->getSourceField();
	}
}