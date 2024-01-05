<?php

namespace MyListing\Elementor\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Testimonials extends \Elementor\Widget_Base {

	public function get_name() {
		return 'case27-testimonials-widget';
	}

	public function get_title() {
		return __( '<strong>27</strong> > Testimonials', 'my-listing' );
	}

	public function get_icon() {
		return 'eicon-slider-device';
	}

	protected function register_controls() {
		$this->start_controls_section(
			'the_testimonials_section',
			['label' => esc_html__( 'Testimonials', 'my-listing' ),]
		);

		$this->add_control(
			'the_testimonials',
			[
				'label' => __( 'Testimonials', 'my-listing' ),
				'type' => \Elementor\Controls_Manager::REPEATER,
				'fields' => [
					[
						'name' => 'author',
						'label' => __( 'Author', 'my-listing' ),
						'type' => \Elementor\Controls_Manager::TEXT,
						'default' => '',
					],
					[
						'name' => 'author_image',
						'label' => __( 'Author Image', 'my-listing' ),
						'type' => \Elementor\Controls_Manager::MEDIA,
					],
					[
						'name' => 'company',
						'label' => __( 'Company', 'my-listing' ),
						'type' => \Elementor\Controls_Manager::TEXT,
						'default' => '',
					],
					[
						'name' => 'content',
						'label' => __( 'Content', 'my-listing' ),
						'type' => \Elementor\Controls_Manager::TEXTAREA,
						'default' => '',
					],
				],
				'title_field' => '{{{ author }}}',
			]
		);

		$this->end_controls_section();
	}

	protected function render( $instance = [] ) {
		wp_print_styles( 'mylisting-testimonials-widget' );

		c27()->get_section( 'testimonials', [
			'testimonials' => $this->get_settings('the_testimonials'),
			'is_edit_mode' => \Elementor\Plugin::$instance->editor->is_edit_mode(),
		] );
	}

	protected function content_template() {}
	public function render_plain_content() {}
}
