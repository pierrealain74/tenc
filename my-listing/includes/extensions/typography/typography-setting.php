<?php

namespace MyListing\Ext\Typography;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Typography_Setting extends \WP_Customize_Setting {

	public $typography_key;

	public $transport = 'postMessage';

	private static $typography_updated = false;

	public function __construct( $manager, $id, $args = [] ) {
		$this->typography_key = ! empty( $args['key'] ) ? $args['key'] : null;
		parent::__construct( $manager, $id, $args );
	}

	public function value() {
		// Use post value if previewed and a post value is present.
		if ( $this->is_previewed ) {
			$value = $this->post_value( null );
			if ( null !== $value ) {
				return $value;
			}
		}

		if ( empty( $this->typography_key ) ) {
			return '';
		}

		$typography = (array) json_decode( get_option( 'mylisting_typography', null ), ARRAY_A );
		return ! empty( $typography[ $this->typography_key ] ) ? $typography[ $this->typography_key ] : '';
	}

	protected function update( $value ) {
		// this should be run only once, as it updates all typography settings
		if ( self::$typography_updated ) {
			return true;
		}

		// get current settings
		$typography = (array) json_decode( get_option( 'mylisting_typography', null ), ARRAY_A );
        $settings = require locate_template( 'includes/extensions/typography/typography-settings.php' );
        $setting_keys = [];

        // run through all posted value (note: only modified settings are sent in the POST request)
		foreach ( $settings as $section_key => $section ) {
			foreach ( $section['settings'] as $setting_key => $setting ) {
				if ( $setting['type'] === 'divider' ) {
					continue;
				}

				$setting = $this->manager->get_setting( 'mylisting_typograpy_setting_'.$setting_key );
				$setting_keys[] = $setting_key;
				if ( $setting && $setting->post_value() !== null ) {
					$typography[ $setting_key ] = $setting->post_value();
				}
			}
		}

		foreach ( $typography as $setting_key => $setting_value ) {
			// cleanup any old data from the settings array
			if ( ! in_array( $setting_key, $setting_keys ) ) {
				unset( $typography[ $setting_key ] );
			}

			// don't store empty/default values
			if ( empty( trim( $setting_value ) ) ) {
				unset( $typography[ $setting_key ] );
			}
		}

		// regenerate styles
		$styles = \MyListing\Ext\Typography\Typography::instance()->generate_styles( $typography );

		// store in database
        update_option( 'mylisting_typography', wp_json_encode( $typography ) );
        update_option( 'mylisting_typography_style', $styles );
        self::$typography_updated = true;
        mlog()->note( 'udpated typography' );
        return true;
	}

}