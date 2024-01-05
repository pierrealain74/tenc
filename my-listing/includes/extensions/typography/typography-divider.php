<?php

namespace MyListing\Ext\Typography;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Typography_Divider extends \WP_Customize_Control {

	public $type = 'mylisting_typography_divider';

	public function render_content() { ?>
		<h1 style="font-size:19px;margin-top:3px;"><?php echo $this->label ?></h1>
	<?php }

}
