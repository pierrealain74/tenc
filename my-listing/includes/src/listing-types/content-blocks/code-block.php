<?php

namespace MyListing\Src\Listing_Types\Content_Blocks;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Code_Block extends Base_Block {

	public function props() {
		$this->props['type'] = 'code';
		$this->props['title'] = 'Shortcode';
		$this->props['icon'] = 'mi view_headline';
		$this->props['content'] = '';
	}

	public function get_editor_options() {
		$this->getLabelField();
		$this->getContentField();
	}

	protected function getContentField() {  ?>
		<div class="form-group">
			<label>Content</label>
			<atwho v-model="block.content" placeholder="Example use:
&lt;iframe src=&quot;https://facebook.com/[[facebook-id]]&quot; title=&quot;[[listing-name]]&quot;&gt;&lt;/iframe&gt;
or
[show_tweets username=&quot;[[twitter-username]]&quot;]"></atwho>
		</div>
	<?php }

}