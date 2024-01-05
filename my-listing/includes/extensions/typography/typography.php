<?php

namespace MyListing\Ext\Typography;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Typography {
    use \MyListing\Src\Traits\Instantiatable;

    public function __construct() {
        add_action( 'wp_head', [ $this, 'print_typography_styles' ], 1900 );
        add_action( 'customize_register', [ $this, 'customize_register' ] );
        add_action( 'admin_init', [ $this, 'maybe_migrate_options' ] );
    }

    public function print_typography_styles() {
    	$styles = get_option('mylisting_typography_style');

    	// we output the <style> tag even if there are no styles to make
    	// selective-refreshing possible in the customizer settings
    	printf( '<style type="text/css" id="mylisting-typography">%s</style>', ! empty( $styles ) ? $styles : '' );
    }

    public function generate_styles( $config ) {
        $styles = [];
        $settings = require locate_template( 'includes/extensions/typography/typography-settings.php' );

        foreach ( $settings as $section_key => $section ) {
        	foreach ( $section['settings'] as $setting_key => $setting ) {
        		if ( $setting['type'] === 'divider' || empty( $config[ $setting_key ] ) ) {
        			continue;
        		}

        		// validate value
        		$value = trim( $config[ $setting_key ] );
        		if ( empty( $value ) || $value === 'default' ) {
        			continue;
        		}

        		// add unit if necessary
        		if ( $setting['type'] === 'font-size' || $setting['type'] === 'line-height' ) {
        			$value .= 'px';
        		}

        		if ( $setting['type'] === 'font-weight' ) {
        			$value .= ' !important';
        		}

        		if ( ! isset( $styles[ $setting['selector'] ] ) ) {
        			$styles[ $setting['selector'] ] = [];
        		}

        		$styles[ $setting['selector'] ][ $setting['type'] ] = $value;
        	}
        }

        $stylestring = '';
        foreach ( $styles as $selector => $properties ) {
        	$values = '';
        	foreach ( $properties as $prop => $value ) {
        		$values .= sprintf( '%s:%s;', $prop, $value );
        	}

        	$stylestring .= sprintf( '%s{%s}', $selector, $values );
        }

        return trim( $stylestring );
    }

    /**
	 * Migrate typography options from ACF fields to `mylisting_typography`
	 * field in `wp_options`. Cleanup old ACF fields in the process.
	 *
	 * @since 2.2.3
     */
    public function maybe_migrate_options() {
    	if ( get_option( 'mylisting_typography', null ) !== null ) {
    		return;
    	}

    	mlog()->note( 'Migrating typography settings' );
        $settings = require locate_template( 'includes/extensions/typography/typography-settings.php' );
        $migrated = [];

        foreach ( $settings as $section_key => $section ) {
        	foreach ( $section['settings'] as $setting_key => $setting ) {
        		// 'option' contains the old field name used to store this setting in wp_options
        		if ( $setting['type'] === 'divider' || empty( $setting['option'] ) ) {
        			continue;
        		}

        		$value = get_option( $setting['option'] );
		        if ( ! trim( $value ) || trim( $value ) === 'default' ) {
		            continue;
		        }

		        // save value from old option
		        $migrated[ $setting_key ] = trim( $value );
        	}
        }

        // remove old options added by ACF (two for each setting)
        $old = require locate_template( 'includes/extensions/typography/typography-old.php' );
        foreach ( $old as $old_option ) {
	        delete_option( 'options_'.$old_option );
	        delete_option( '_options_'.$old_option );
        }

        update_option( 'mylisting_typography', wp_json_encode( $migrated ) );
        update_option( 'mylisting_typography_style', $this->generate_styles( $migrated ) );
    }

    public function customize_register( $wp_customize ) {
		$wp_customize->add_panel( 'mylisting_typography', [
			'title' => _x( 'Typography', 'Typography Settings', 'my-listing' ),
			'priority' => 140,
			'theme_supports' => '',
		] );

        $settings = require locate_template( 'includes/extensions/typography/typography-settings.php' );
        $setting_ids = [];

		foreach ( $settings as $section_key => $section ) {
			$section_id = 'mylisting_typography_'.$section_key;
			$wp_customize->add_section( $section_id, [
				'title' => $section['label'],
				'panel' => 'mylisting_typography',
			] );

			foreach ( $section['settings'] as $setting_key => $setting ) {
				$setting_id = 'mylisting_typograpy_setting_'.$setting_key;
				$setting_ids[] = $setting_id;

				if ( $setting['type'] === 'divider' ) {
			        $wp_customize->add_setting( $setting_id, [
			        	'type' => 'mylisting_typography_ui',
						'key' => $setting_key,
			        ] );

					$wp_customize->add_control( new Typography_Divider( $wp_customize, $setting_id, [
						'label' => $setting['label'],
			            'section' => $section_id,
					] ) );
				}

				if ( $setting['type'] === 'font-size' ) {
			        $wp_customize->add_setting( new Typography_Setting( $wp_customize, $setting_id, [
			        	'type' => 'mylisting_typography',
			        	'default' => '',
						'key' => $setting_key,
			        ] ) );

			        $wp_customize->add_control( $setting_id, [
			            'label' => $setting['label'].' (px)',
			            'section' => $section_id,
			            'settings' => $setting_id,
			            'type' => 'number',
			            'input_attrs' => [
			            	'min' => 8,
			            	'placeholder' => 'default',
			            ],
			        ] );
				}

				if ( $setting['type'] === 'line-height' ) {
			        $wp_customize->add_setting( new Typography_Setting( $wp_customize, $setting_id, [
			        	'type' => 'mylisting_typography',
			        	'default' => '',
						'key' => $setting_key,
			        ] ) );

			        $wp_customize->add_control( $setting_id, [
			            'label' => $setting['label'].' (px)',
			            'section' => $section_id,
			            'settings' => $setting_id,
			            'type' => 'number',
			            'input_attrs' => [
			            	'min' => 8,
			            	'placeholder' => 'default',
			            ],
			        ] );
				}

				if ( $setting['type'] === 'font-weight' ) {
			        $wp_customize->add_setting( new Typography_Setting( $wp_customize, $setting_id, [
			        	'type' => 'mylisting_typography',
			        	'default' => '',
						'key' => $setting_key,
			        ] ) );

			        $wp_customize->add_control( $setting_id, [
			            'label' => $setting['label'],
			            'section' => $section_id,
			            'settings' => $setting_id,
			            'type' => 'select',
			            'choices' => [
							'' => 'Default',
							100 => 'Thin',
							200 => 'Extra Light',
							300 => 'Light',
							400 => 'Regular',
							500 => 'Medium',
							600 => 'Semi-Bold',
							700 => 'Bold',
							800 => 'Extra-Bold',
							900 => 'Black',
						],
			        ] );
			    }

				if ( $setting['type'] === 'color' ) {
			        $wp_customize->add_setting( new Typography_Setting( $wp_customize, $setting_id, [
			        	'type' => 'mylisting_typography',
			        	'default' => '',
						'key' => $setting_key,
			        ] ) );

			        $wp_customize->add_control( new \WP_Customize_Color_Control( $wp_customize, $setting_id, [
			            'label' => $setting['label'],
			            'section' => $section_id,
			            'settings' => $setting_id,
			        ] ) );
			    }
			}
		}

		$wp_customize->selective_refresh->add_partial( 'mylisting_typography', [
			'selector' => '#mylisting-typography',
			'settings' => $setting_ids,
			'render_callback' => function( $partial, $context ) {
				$config = [];
				foreach ( $partial->settings as $setting_id ) {
					$setting = $partial->component->manager->get_setting( $setting_id );
					if ( $setting && ( $value = $setting->value() ) ) {
						$config[ $setting->typography_key ] = $value;
					}
				}

				echo $this->generate_styles( $config );
			},
		] );
    }
}
