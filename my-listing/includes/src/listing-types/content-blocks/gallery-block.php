<?php

namespace MyListing\Src\Listing_Types\Content_Blocks;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Gallery_Block extends Base_Block {

	public function props() {
		$this->props['type'] = 'gallery';
		$this->props['title'] = 'Gallery';
		$this->props['icon'] = 'mi insert_photo';
		$this->props['gallery_type'] = 'carousel';
		$this->props['show_field'] = 'job_gallery';
		$this->allowed_fields = [ 'file' ];
	}

	public function get_editor_options() {
		$this->getLabelField();
		$this->getSourceField();
		$this->getGalleryTypeField();
	}

	protected function getGalleryTypeField() { ?>
		<div class="form-group">
			<label>Gallery Type</label>
			<div class="select-wrapper">
				<select v-model="block.gallery_type">
					<option value="carousel">Carousel</option>
					<option value="carousel-with-preview">Carousel with image preview</option>
					<option value="grid">Grid View</option>
				</select>
			</div>
		</div>
	<?php }
}