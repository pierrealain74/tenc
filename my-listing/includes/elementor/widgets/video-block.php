<?php

namespace MyListing\Elementor\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Video_Block extends \Elementor\Widget_Base {

	public function get_name() {
		return 'case27-video-block-widget';
	}

	public function get_title() {
		return __( '<strong>27</strong> > Video Block', 'my-listing' );
	}

	public function get_icon() {
		return 'eicon-youtube';
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_content_block',
			[
				'label' => esc_html__( 'Content', 'my-listing' ),
			]
		);

		$this->add_control(
			'the_icon',
			[
			'label' => __( 'Icon', 'my-listing' ),
			'type' => \Elementor\Controls_Manager::ICON,
			]
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
			'the_url',
			[
				'label' => __( 'Video URL', 'my-listing' ),
				'type' => \Elementor\Controls_Manager::URL,
				'description' => __( 'Supported video sources are: Youtube, Vimeo, Dailymotion.', 'my-listing' ),
			]
		);

		$this->end_controls_section();

		\MyListing\Elementor\apply_common_block_controls( $this );
	}

	protected function render( $instance = [] ) {
		c27()->get_section( 'video-block', [
			'video_url' => $this->get_settings('the_url')['url'],
			'icon' => $this->get_settings('the_icon'),
			'icon_style' => $this->get_settings('the_icon_style'),
			'title' => $this->get_settings('the_title'),
		] );
	}

	protected function content_template() {}
	public function render_plain_content() {}
}
