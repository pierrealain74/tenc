<?php

namespace MyListing\Elementor\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Page_Heading extends \Elementor\Widget_Base {

	public function get_name() {
		return 'case27-page-heading-widget';
	}

	public function get_title() {
		return __( '<strong>27</strong> > Page Heading', 'my-listing' );
	}

	public function get_icon() {
		return 'eicon-image-box';
	}

	protected function register_controls() {
		$this->start_controls_section(
			'the_page_heading_controls',
			['label' => esc_html__( 'Page Heading', 'my-listing' ),]
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
				'selectors' => [
					'{{WRAPPER}} .page-head .ph-details h1' => 'color: {{VALUE}}',
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
			'the_subtitle_color',
			[
				'label' => __( 'Subitle Color', 'my-listing' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .page-head .ph-details p' => 'color: {{VALUE}} !important',
				],
			]
		);


		$this->end_controls_section();
	}

	protected function render( $instance = [] ) {
		c27()->get_section( 'page-heading', [
			'title' => $this->get_settings('the_title'),
			'subtitle' => $this->get_settings('the_subtitle'),
		] );
	}

	protected function content_template() {}
	public function render_plain_content() {}
}
