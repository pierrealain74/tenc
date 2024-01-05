<?php

namespace MyListing\Src\Listing_Types\Content_Blocks;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Raw_Block extends Base_Block {

	public function props() {
		$this->props['type'] = 'raw';
		$this->props['title'] = 'Static Code';
		$this->props['icon'] = 'mi view_module';
		$this->props['content'] = '';
		$this->props['conditional_logic'] = false;
		$this->props['conditions'] = [];
	}

	public function get_editor_options() {
		$this->getLabelField();
		$this->getContentField();
	}

	protected function getContentField() { ?>
		<div class="form-group">
			<label>Content</label>
			<textarea rows="10" v-model="block.content"></textarea>
			<p>
				Enter any shortcode here. This block isn't specific to the active listing, so it can be used for ads
				and similar stuff added through a shortcode or embed code.
			</p>
		</div>
	<?php }

}