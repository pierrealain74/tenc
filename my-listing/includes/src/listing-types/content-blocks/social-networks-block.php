<?php

namespace MyListing\Src\Listing_Types\Content_Blocks;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Social_Networks_Block extends Base_Block {

	public function props() {
		$this->props['type'] = 'social_networks';
		$this->props['title'] = 'Social Networks';
		$this->props['icon'] = 'mi view_module';
		$this->props['style'] = 'outlined-icons';
	}

	public function get_editor_options() {
		$this->getLabelField();
		$this->getStyleField();
	}

	protected function getStyleField() { ?>
		<div class="form-group">
			<label>Style</label>
			<div class="select-wrapper">
				<select v-model="block.style">
					<option value="colored-icons">Colored Icons</option>
					<option value="outlined-icons">Outlined Icons</option>
				</select>
			</div>
		</div>
	<?php }
}