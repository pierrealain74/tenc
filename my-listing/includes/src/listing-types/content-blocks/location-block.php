<?php

namespace MyListing\Src\Listing_Types\Content_Blocks;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Location_Block extends Base_Block {

	public function props() {
		$this->props['type'] = 'location';
		$this->props['title'] = 'Location';
		$this->props['icon'] = 'mi map';
		$this->props['map_skin'] = 'skin1';
		$this->props['map_zoom'] = 11;
		$this->props['show_field'] = 'job_location';
		$this->allowed_fields = [ 'location' ];
	}

	public function get_editor_options() {
		$this->getLabelField();
		$this->getSourceField();
		$this->getMapSettings();
	}

	protected function getMapSettings() { ?>
		<div class="form-group">
			<label>Map Skin</label>
			<div class="select-wrapper">
				<select v-model="block.map_skin">
					<?php foreach ( \MyListing\Apis\Maps\get_skins() as $key => $label ): ?>
						<option value="<?php echo esc_attr( $key ) ?>"><?php echo esc_html( $label ) ?></option>
					<?php endforeach ?>
				</select>
			</div>
		</div>

		<div class="form-group">
			<label>Default map zoom level</label>
			<input type="number" min="0" max="22" v-model="block.map_zoom">
			<p class="mb0">Enter a value between 0 (no zoom) and 22 (maximum zoom). Default: 11.</p>
		</div>
	<?php }
}
