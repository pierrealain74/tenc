<?php

namespace MyListing\Elementor\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Image extends \Elementor\Widget_Base {

	public function get_name() {
		return 'case27-image-widget';
	}

	public function get_title() {
		return __( '<strong>27</strong> > Image', 'my-listing' );
	}

	public function get_icon() {
		return 'eicon-image-before-after';
	}

	protected function register_controls() {
		$this->start_controls_section(
			'the_image_section',
			['label' => esc_html__( 'Image', 'my-listing' ),]
		);

		$this->add_control(
			'the_image',
			[
				'label' => __( 'Choose Image', 'my-listing' ),
				'type' => \Elementor\Controls_Manager::MEDIA,
				'default' => [
				'url' => \Elementor\Utils::get_placeholder_image_src(),
				],
			]
		);

		$this->add_control(
			'the_style',
			[
				'label' => __( 'Image Style', 'my-listing' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => '1',
				'options' => [
					'1' => __( 'Style 1', 'my-listing' ),
					'2' => __( 'Style 2', 'my-listing' ),
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render( $instance = [] ) {
		wp_print_styles( 'mylisting-image-widget' );

		c27()->get_section( 'image', [
			'image' => $this->get_settings('the_image'),
			'style' => $this->get_settings('the_style'),
		] );
	}

	protected function content_template() {}
	public function render_plain_content() {}
}
