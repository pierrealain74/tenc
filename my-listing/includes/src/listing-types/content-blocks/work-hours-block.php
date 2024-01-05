<?php

namespace MyListing\Src\Listing_Types\Content_Blocks;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Work_Hours_Block extends Base_Block {

	public function props() {
		$this->props['type'] = 'work_hours';
		$this->props['title'] = 'Work Hours';
		$this->props['icon'] = 'mi alarm';
		$this->props['collapse'] = false;
	}

	public function get_editor_options() {
		$this->getLabelField();
		$this->collapseBlock();
	}

	protected function collapseBlock() { ?>
		<div class="form-group">
			<label>Expand block?</label>
			<label class="form-switch">
				<input v-model="block.collapse" type="checkbox" class="form-checkbox"><span class="switch-slider"></span>
			</label>
		</div>
	<?php }
}