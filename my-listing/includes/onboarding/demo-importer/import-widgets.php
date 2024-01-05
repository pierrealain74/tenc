<?php

namespace MyListing\Onboarding\Demo_Importer;

if ( ! defined('ABSPATH') ) {
	exit;
}

function import_widgets() {
	$config = get_demo_file('widgets.json');

	// setup sidebars
	wp_set_sidebars_widgets( $config['sidebar_widgets'] );

	// import widget configs
	foreach ( $config['config'] as $widget_key => $widget_options ) {
		if ( $widget_key === 'widget_text' ) {
			foreach ( $widget_options as $option_id => $option_value ) {
				if ( is_array( $option_value ) && ! empty( $option_value['text'] ) ) {
					$widget_options[ $option_id ]['text'] = import_post_content( $option_value['text'] );
				}
			}
		}

		update_option( $widget_key, $widget_options );
	}
}