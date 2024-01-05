<?php
/**
 * Icon Picker module for Advanced Custom Fields.
 *
 * @version 1.0
 * @author  27collective
 */

namespace MyListing\Ext\Advanced_Custom_Fields;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Iconpicker_Field extends \acf_field {

	function __construct() {
		$this->name = 'icon_picker';
		$this->label = __( 'Icon Picker', 'my-listing' );
		$this->category = 'choice';

    	parent::__construct();
	}

	/**
	 * Render the field.
	 */
	function render_field( $field ) {
		global $pagenow;
		$randomID = 'icon_picker__' . uniqid(); ?>

		<div id="<?php echo esc_attr( $randomID ) ?>" class="c27-acf-icon-picker-field-wrapper">
			<input type="hidden" name="<?php echo esc_attr($field['name']) ?>" v-model="value">
			<iconpicker v-model="value"></iconpicker>
			<div class="c27-reset-icon-picker" data-id="<?php echo esc_attr( "#{$randomID}" ) ?>" data-value="<?php echo esc_attr( htmlspecialchars(json_encode($field['value']), ENT_QUOTES, 'UTF-8') ) ?>"></div>
		</div>
		<script type="text/javascript">
			setInterval(function() {
				if (jQuery('.c27-icon-picker').length === 0) {
					jQuery('.c27-reset-icon-picker').click();
				}
			}, 2500);
		</script>

		<?php // @todo find a better way to use iconpicker Vue component. ?>
		<?php 
		if ( $pagenow === 'term.php' ) {
			ob_start() ?>
				new Vue({
					el: '#<?php echo esc_attr( $randomID ) ?>',
					data: { value: <?php echo json_encode(esc_attr($field['value'])) ?> }
				});
			<?php
			wp_add_inline_script('theme-script-main', ob_get_clean());
		}
	}
}
