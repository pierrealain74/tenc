<?php

namespace MyListing\Elementor\Controls;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Icon extends \Elementor\Base_Data_Control {

	public static function get_icons() {
		$icons = [];
		foreach ( \MyListing\Utils\Icons\Material_Icons::get() as $icon ) {
			$icons[ $icon ] = str_replace( 'mi ', '', $icon );
		}

		foreach ( \MyListing\Utils\Icons\Theme_Icons::get() as $icon ) {
			$icons[$icon] = str_replace( 'icon-', '', $icon );
		}

		foreach ( \MyListing\Utils\Icons\Font_Awesome::get() as $icon ) {
			$icons[ $icon ] = str_replace( [ 'fa-', 'fa ', 'fab ' ], '', $icon );
		}

		return $icons;
	}

	/**
	 * Retrieve icon control type.
	 */
	public function get_type() {
		return 'icon';
	}

	/**
	 * Retrieve icons control default settings.
	 */
	protected function get_default_settings() {
		return [
			'options' => self::get_icons(),
		];
	}

	/**
	 * Render icons control output in the editor.
	 */
	public function content_template() {
		$control_uid = $this->get_control_uid();
		?>
		<div class="elementor-control-field">
			<label for="<?php echo $control_uid; ?>" class="elementor-control-title">{{{ data.label }}}</label>
			<div class="elementor-control-input-wrapper">
				<select id="<?php echo $control_uid; ?>" class="elementor-control-icon" data-setting="{{ data.name }}" data-placeholder="<?php _e( 'Select Icon', 'elementor' ); ?>">
					<option value=""><?php _e( 'Select Icon', 'elementor' ); ?></option>
					<# _.each( data.options, function( option_title, option_value ) { #>
					<option value="{{ option_value }}">{{{ option_title }}}</option>
					<# } ); #>
				</select>
			</div>
		</div>
		<# if ( data.description ) { #>
		<div class="elementor-control-field-description">{{ data.description }}</div>
		<# } #>
		<?php
	}
}

add_action('elementor/controls/register', function($el) {
	$el->register( new Icon );
});