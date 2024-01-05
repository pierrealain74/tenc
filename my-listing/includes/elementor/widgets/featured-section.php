<?php

namespace MyListing\Elementor\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Featured_Section extends \Elementor\Widget_Base {

	public function get_name() {
		return 'case27-featured-section-widget';
	}

	public function get_title() {
		return __( '<strong>27</strong> > Featured Section', 'my-listing' );
	}

	public function get_icon() {
		return 'eicon-call-to-action';
	}

	protected function register_controls() {
		$this->start_controls_section(
			'27_featured_section_widget',
			[
				'label' => esc_html__( 'Featured Section', 'my-listing' ),
			]
		);

		$this->add_control(
			'27_content',
			[
				'label' => __( 'Content', 'my-listing' ),
				'type' => \Elementor\Controls_Manager::WYSIWYG,
			]
		);

		$this->end_controls_section();
	}

	protected function render( $instance = [] ) {
		wp_print_styles( 'mylisting-featured-section-widget' );

		c27()->get_section( 'featured-section', [
			'content' => $this->get_settings('27_content'),
			'is_edit_mode' => \Elementor\Plugin::$instance->editor->is_edit_mode(),
		] );
	}

	protected function content_template() {}
	public function render_plain_content( $instance = [] ) {}
}
