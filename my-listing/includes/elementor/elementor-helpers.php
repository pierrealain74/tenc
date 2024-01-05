<?php

namespace MyListing\Elementor;

if ( ! defined('ABSPATH') ) {
	exit;
}

/**
 * Apply common controls for content-block widgets.
 *
 * @since 2.4.3
 */
function apply_common_block_controls( $widget ) {
	$widget->start_controls_section( 'section_content_styling_block', [
		'label' => __( 'Styling', 'my-listing' ),
	] );

	$widget->add_control( 'heading_block_heading_styles', [
		'label' => __( 'Block Heading', 'my-listing' ),
		'type' => \Elementor\Controls_Manager::HEADING,
		'separator' => 'before',
	] );

	$widget->add_control( 'the_icon_style', [
		'label' => __( 'Icon Style', 'my-listing' ),
		'type' => \Elementor\Controls_Manager::SELECT2,
		'default' => 1,
		'options' => [
			1 => __( 'Default', 'my-listing' ),
			2 => __( 'Circular', 'my-listing' ),
			3 => __( 'No Icon', 'my-listing' ),
		],
	] );

	$widget->add_control( 'the_icon_color', [
		'label' => __( 'Icon Color', 'my-listing' ),
		'type' => \Elementor\Controls_Manager::COLOR,
		'default' => '#c7cdcf',
		'selectors' => [
			'{{WRAPPER}} .title-style-1 i' => 'color: {{VALUE}}',
		],
	] );

	$widget->add_control( 'the_icon_background', [
		'label' => __( 'Icon Background', 'my-listing' ),
		'type' => \Elementor\Controls_Manager::COLOR,
		'default' => '#f4f4f4',
		'selectors' => [
			'{{WRAPPER}} .title-style-2 i' => 'background: {{VALUE}}',
		],
		'condition' => [
			'the_icon_style' => '2',
		],
	] );

	$widget->add_control( 'the_title_color', [
		'label' => __( 'Title Color', 'my-listing' ),
		'type' => \Elementor\Controls_Manager::COLOR,
		'default' => '#242429',
		'selectors' => [
			'{{WRAPPER}} .title-style-1 h5' => 'color: {{VALUE}}',
		],
	] );

	$widget->add_control( 'heading_block_styles', [
		'label' => __( 'Block Styles', 'my-listing' ),
		'type' => \Elementor\Controls_Manager::HEADING,
		'separator' => 'before',
	] );

	$widget->add_control( 'the_block_background', [
		'label' => __( 'Block Background', 'my-listing' ),
		'type' => \Elementor\Controls_Manager::COLOR,
		'default' => '#ffffff',
		'selectors' => [
			'{{WRAPPER}} .element' => 'background: {{VALUE}}',
		],
	] );

	$widget->add_control( 'the_border_style', [
		'label' => __( 'Border Style', 'my-listing' ),
		'type' => \Elementor\Controls_Manager::SELECT2,
		'default' => 'solid',
		'options' => [
			'solid' => __( 'Solid', 'my-listing' ),
			'none' => __( 'None', 'my-listing' ),
		],
		'selectors' => [ '{{WRAPPER}} .element' => 'border-style: {{VALUE}}' ],
	] );

	$widget->add_control( 'the_border_color', [
		'label' => __( 'Border Color', 'my-listing' ),
		'type' => \Elementor\Controls_Manager::COLOR,
		'default' => '#e5e6e9 #dfe0e4 #d0d1d5',
		'selectors' => [ '{{WRAPPER}} .element' => 'border-color: {{VALUE}}' ],
		'condition' => [ 'the_border_style' => 'solid' ],
	] );

	$widget->end_controls_section();
}

/**
 * Control group to pick a gradient or solid color.
 *
 * @since 2.4.3
 */
function apply_overlay_controls( $widget, $key, $label = '' ) {
	if ( ! $label ) {
		$label = __( 'Set an overlay', 'my-listing' );
	}

	$widget->add_control( $key, [
		'label' => __( 'Overlay type', 'my-listing' ),
		'type' => \Elementor\Controls_Manager::SELECT,
		'default' => 'gradient',
		'options' => [
			'gradient' => __( 'Gradient', 'my-listing' ),
			'solid_color' => __( 'Solid Color', 'my-listing' ),
		],
	] );

	$gradients_html = '';
	foreach ( c27()->get_gradients() as $gradient_name => $gradient ) {
		$gradients_html .= "<div style=\"background: -webkit-linear-gradient(180deg, {$gradient['from']} 0%, {$gradient['to']} 100%);";
		$gradients_html .= "width: 33.33333%; height: 80px; display: inline-block; color: #fff;\">{$gradient_name}</div>";
	}

	$widget->add_control( $key . '__gradient_types', [
		'type'    => \Elementor\Controls_Manager::RAW_HTML,
		'raw' => __( 'Gradient Types ', 'my-listing' ) . "<br><br>" . $gradients_html,
		'content_classes' => 'your-class',
		'condition' => [$key => 'gradient'],
	] );

	$gradients = array_keys( c27()->get_gradients() );
	$widget->add_control( $key . '__gradient', [
		'label' => $label,
		'type' => \Elementor\Controls_Manager::SELECT,
		'options' => array_combine( $gradients, $gradients ),
		'condition' => [ $key => 'gradient' ],
	] );

	$widget->add_control( $key . '__solid_color', [
		'label' => $label,
		'type' => \Elementor\Controls_Manager::COLOR,
		'condition' => [$key => 'solid_color'],
	] );
}

/**
 * Control group to set column count at different breakpoints.
 *
 * @since 2.4.3
 */
function apply_column_count_controls( $widget, $key, $label, $options = [] ) {
	$options = array_replace_recursive( [
		'heading' => [
			'label' => $label,
			'type' => \Elementor\Controls_Manager::HEADING,
			'separator' => 'before',
		],
		'general' => [
			'type' => \Elementor\Controls_Manager::NUMBER,
			'default' => 3,
			'separator' => 'none',
		],
		'lg' => [],
		'md' => [],
		'sm' => [],
		'xs' => [],
	], $options );

	$breakpoints = [
		'lg' => __( 'Desktop', 'my-listing' ),
		'md' => __( 'Laptop', 'my-listing' ),
		'sm' => __( 'Tablet', 'my-listing' ),
		'xs' => __( 'Mobile', 'my-listing' ),
	];

	$widget->add_control( 'more_options', $options['heading'] );

	foreach ( $breakpoints as $breakpoint => $bp_label ) {
		$widget->add_control( "{$key}__{$breakpoint}", array_merge(
			[ 'label' => $bp_label ],
			$options['general'],
			$options[ $breakpoint ]
		) );
	}
}

/**
 * Is the Elementor page editor active.
 *
 * @since 2.3.4
 */
function is_edit_mode() {
	return \Elementor\Plugin::$instance->editor->is_edit_mode();
}
