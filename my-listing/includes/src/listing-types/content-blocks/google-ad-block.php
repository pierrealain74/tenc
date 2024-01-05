<?php

namespace MyListing\Src\Listing_Types\Content_Blocks;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Google_Ad_Block extends Base_Block {

	public function props() {
		$this->props['type'] = 'google-ad';
		$this->props['title'] = 'Google Ad';
		$this->props['icon'] = 'mi view_module';
		$this->props['slot_id'] = '';
		$this->props['pub_id'] = '';
		$this->props['conditional_logic'] = false;
		$this->props['conditions'] = [];
	}
	
	public function get_editor_options() {
		$this->getLabelField();
		$this->getContentField();
	}

	protected function getContentField() { ?>
		<div class="form-group">
			<label>Publisher ID</label>
			<input type="text" v-model="block.pub_id"></input>
		</div>

		<div class="form-group">
			<label>Ad Slot ID</label>
			<input type="text" v-model="block.slot_id"></input>
		</div>
	<?php }

}