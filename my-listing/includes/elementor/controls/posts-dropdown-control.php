<?php

namespace MyListing\Elementor\Controls;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Posts_Dropdown_Control extends \Elementor\Base_Data_Control {

	public function get_type() {
		return 'mylisting-posts-dropdown';
	}

	protected function get_default_settings() {
		return [
			'label' => '',
			'label_block' => true,
			'multiple' => false,
			'post_type' => 'post',
			'post_key' => 'id',
		];
	}

	public function content_template() {
		$control_uid = $this->get_control_uid();
		?>

		<div class="elementor-control-field">
			<# if ( data.label ) {#>
				<label for="<?php echo $control_uid; ?>" class="elementor-control-title">
					{{{ data.label }}}
				</label>
			<# } #>
			<div class="elementor-control-field-description block loading-selection">
				Loading...
			</div>
			<div class="elementor-control-input-wrapper hide">
				<# var multiple = ( data.multiple ) ? 'multiple' : ''; #>
				<select {{ multiple }}
					data-mylisting-ajax="true" data-mylisting-ajax-url="mylisting_list_posts">
				</select>
			</div>
		</div>

		<# if ( data.description ) { #>
			<div class="elementor-control-field-description">{{{ data.description }}}</div>
		<# } #>
		<?php
	}

	public function get_value( $control, $settings ) {
		if ( ! isset( $control['default'] ) ) {
			$control['default'] = $this->get_default_value();
		}

		if ( isset( $settings[ $control['name'] ] ) ) {
			$value = $settings[ $control['name'] ];
			if ( ! $control['multiple'] && is_array( $value ) ) {
				$value = array_shift($value);
			}
		} else {
			$value = $control['default'];
		}

		return $value;
	}
}
