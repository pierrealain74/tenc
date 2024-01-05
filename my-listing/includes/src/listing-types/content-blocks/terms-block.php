<?php

namespace MyListing\Src\Listing_Types\Content_Blocks;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Terms_Block extends Base_Block {

	public function props() {
		$this->props['type'] = 'terms';
		$this->props['title'] = 'Terms';
		$this->props['icon'] = 'mi view_module';
		$this->props['taxonomy'] = 'job_listing_category';
		$this->props['style'] = 'listing-categories-block';
		$this->props['link'] = 'link-enable';
	}

	public function get_editor_options() {
		$this->getLabelField();
		$this->getTaxonomyField();
		$this->getStyleField();
		$this->disableLinks();
	}

	protected function getTaxonomyField() {
		$taxonomies = (array) get_taxonomies( [ 'object_type' => [ 'job_listing' ], ], 'objects' );
		?>
		<div class="form-group">
			<label>Taxonomy</label>
			<div class="select-wrapper">
				<select v-model="block.taxonomy">
					<?php foreach ( $taxonomies as $taxonomy ): ?>
						<option value="<?php echo esc_attr( $taxonomy->name ) ?>">
							<?php echo esc_html( $taxonomy->label ) ?>
						</option>
					<?php endforeach ?>
				</select>
			</div>
		</div>
	<?php }

	protected function getStyleField() { ?>
		<div class="form-group">
			<label>Style</label>
			<div class="select-wrapper">
				<select v-model="block.style">
					<option value="listing-categories-block">Colored Icons</option>
					<option value="list-block">Outlined Icons</option>
				</select>
			</div>
		</div>
	<?php }

	protected function disableLinks() { ?>
		<div class="form-group">
			<label>Disable links?</label>
			<div class="select-wrapper">
				<select v-model="block.link">
					<option value="link-enable">No</option>
					<option value="link-disable">Yes</option>
				</select>
			</div>
		</div>
	<?php }

}