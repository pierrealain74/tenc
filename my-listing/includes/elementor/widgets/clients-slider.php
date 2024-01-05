<?php

namespace MyListing\Elementor\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Clients_Slider extends \Elementor\Widget_Base {

	public function get_name() {
		return 'case27-logo-slider-widget';
	}

	public function get_title() {
		return __( '<strong>27</strong> > Clients Slider', 'my-listing' );
	}

	public function get_icon() {
		return 'eicon-carousel';
	}

	protected function register_controls() {
		$this->start_controls_section(
			'the_logo_slider_section',
			['label' => esc_html__( 'Clients Slider', 'my-listing' ),]
		);

		$this->add_control(
			'the_items',
			[
				'label' => __( 'Clients', 'my-listing' ),
				'type' => \Elementor\Controls_Manager::REPEATER,
				'fields' => [
					[
						'name' => 'client_name',
						'label' => __( 'Client Name', 'my-listing' ),
						'type' => \Elementor\Controls_Manager::TEXT,
						'default' => '',
					],
					[
						'name' => 'client_url',
						'label' => __( 'Client Website', 'my-listing' ),
						'type' => \Elementor\Controls_Manager::URL,
						'show_external' => true,
					],
					[
						'name' => 'client_logo',
						'label' => __( 'Client Logo', 'my-listing' ),
						'type' => \Elementor\Controls_Manager::MEDIA,
					],
				],
				'title_field' => '{{{ client_name }}}',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'slider_section_styling',
			['label' => esc_html__( 'Styling', 'my-listing' ),]
		);

		$this->add_control(
			'slider_section_overlay_color',
			[
				'label' => __( 'Overlay Color', 'my-listing' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => ['{{WRAPPER}} .overlay' => 'background: {{VALUE}}',],
			]
		);

		$this->add_control(
			'slider_section_overlay_opacity',
			[
				'label' => __( 'Overlay Opacity', 'my-listing' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'min' => 0,
				'max' => 1,
				'step' => 0.01,
				'default' => 0.4,
				'selectors' => ['{{WRAPPER}} .overlay' => 'opacity: {{VALUE}}',],
			]
		);

		$this->add_control(
			'slider_section_overlay_hover_opacity',
			[
				'label' => __( 'Overlay Color', 'my-listing' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'min' => 0,
				'max' => 1,
				'step' => 0.01,
				'default' => 0,
				'selectors' => ['{{WRAPPER}} .clients-logo:hover .overlay' => 'opacity: {{VALUE}}',],
			]
		);
		$this->end_controls_section();
	}

	protected function render( $instance = [] ) {
		wp_print_styles( 'mylisting-clients-slider-widget' );

		c27()->get_section( 'clients-slider', [
			'items' => $this->get_settings('the_items'),
			'is_edit_mode' => \Elementor\Plugin::$instance->editor->is_edit_mode(),
		] );
	}

	protected function content_template() {}
	public function render_plain_content() {}
}
