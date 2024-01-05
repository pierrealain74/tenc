<?php

namespace MyListing\Elementor\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Section_Heading extends \Elementor\Widget_Base {

	public function get_name() {
		return 'case27-section-heading-widget';
	}

	public function get_title() {
		return __( '<strong>27</strong> > Section Heading', 'my-listing' );
	}

	public function get_icon() {
		return 'eicon-animation-text';
	}

	protected function register_controls() {
		$this->start_controls_section(
			'the_section_heading_controls',
			['label' => esc_html__( 'Section Heading', 'my-listing' ),]
		);

		$this->add_control(
			'the_title',
			[
				'label' => __( 'Title', 'my-listing' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => '',
			]
		);

		$this->add_control(
    		'title_color',
    		[
    		    'label' => __( 'Title Color', 'my-listing' ),
    		    'type' => \Elementor\Controls_Manager::COLOR,
    		    'default' => '#000000',
    		    'scheme' => [
    		        'type' => \Elementor\Core\Schemes\Color::get_type(),
    		        'value' => \Elementor\Core\Schemes\Color::COLOR_1,
    		    ],
    		    'selectors' => [
    		        '{{WRAPPER}} .i-section .section-title h2' => 'color: {{VALUE}}',
    		    ],
    		]
		);


		$this->add_control(
			'the_subtitle',
			[
				'label' => __( 'Subtitle', 'my-listing' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => '',
			]
		);

		$this->add_control(
    		'subtitle_color',
    		[
    		    'label' => __( 'Subtitle Color', 'my-listing' ),
    		    'type' => \Elementor\Controls_Manager::COLOR,
    		    'default' => c27()->get_setting('general_brand_color', '#f24286'),
    		    'scheme' => [
    		        'type' => \Elementor\Core\Schemes\Color::get_type(),
    		        'value' => \Elementor\Core\Schemes\Color::COLOR_1,
    		    ],
    		    'selectors' => [
    		        '{{WRAPPER}} .i-section .section-title p' => 'color: {{VALUE}}',
    		    ],
    		]
		);


		$this->add_control(
			'the_content',
			[
				'label' => __( 'Content', 'my-listing' ),
				'type' => \Elementor\Controls_Manager::WYSIWYG,
				'default' => '',
			]
		);

		$this->add_control(
			'section_inner_spacing',
			[
			   	'label'   => __( 'Inner Padding', 'my-listing' ),
			   	'type'    => \Elementor\Controls_Manager::DIMENSIONS,
			   	'default' => ['top' => 65, 'left' => 0, 'right' => 0, 'bottom' => 65],
			   	'selectors' => [
    		        '{{WRAPPER}} .i-section' => 'padding: {{TOP}}px {{LEFT}}px {{BOTTOM}}px {{RIGHT}}px !important',
    		    ],
			]
		);

		\MyListing\Elementor\apply_overlay_controls(
			$this,
			'27_overlay',
			__( 'Set an overlay', 'my-listing' )
		);

		$this->end_controls_section();
	}

	protected function render( $instance = [] ) {
		c27()->get_section( 'section-heading', [
			'title' => $this->get_settings('the_title'),
			'subtitle' => $this->get_settings('the_subtitle'),
			'content' => $this->get_settings('the_content'),
			'overlay_type' => $this->get_settings('27_overlay'),
			'overlay_gradient' => $this->get_settings('27_overlay__gradient'),
			'overlay_solid_color' => $this->get_settings('27_overlay__solid_color'),
		] );
	}

	protected function content_template() {}
	public function render_plain_content() {}
}
