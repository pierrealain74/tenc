<?php

namespace MyListing\Elementor\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Blog_Feed extends \Elementor\Widget_Base {

	public function get_name() {
		return 'case27-blog-feed-widget';
	}

	public function get_title() {
		return __( '<strong>27</strong> > Blog Feed', 'my-listing' );
	}

	public function get_icon() {
		return 'eicon-posts-masonry';
	}

	protected function register_controls() {
		$this->start_controls_section( 'the_blog_feed', [
			'label' => esc_html__( 'Blog Feed', 'my-listing' ),
		] );

		$this->add_control( 'the_template', [
			'label' => __( 'Template', 'my-listing' ),
			'type' => \Elementor\Controls_Manager::SELECT2,
			'default' => 'col3',
			'options' => [
				'col2' => __( 'Two Columns', 'my-listing' ),
				'col3' => __( 'Three Columns', 'my-listing' ),
			],
		] );

		$this->add_control( 'posts_per_page', [
			'label'   => __( 'Number of items to show', 'my-listing' ),
			'type'    => \Elementor\Controls_Manager::NUMBER,
			'default' => 6,
		] );

		$categories = is_admin()
			? c27()->get_terms_dropdown_array( [ 'taxonomy' => 'category', 'hide_empty' => false ] )
			: [];

		$this->add_control( 'select_categories', [
			'label' => __( 'Filter by Categories', 'my-listing' ),
			'type' => \Elementor\Controls_Manager::SELECT2,
			'options' => $categories,
			'multiple' => true,
			'label_block' => true,
		] );

		$this->add_control( 'select_posts', [
			'label' => __( 'Filter by post', 'my-listing' ),
			'type' => 'mylisting-posts-dropdown',
			'multiple' => true,
			'label_block' => true,
			'post_type' => 'post',
			'post_key' => 'id',
		] );

		$this->end_controls_section();
	}

	protected function render( $instance = [] ) {
		wp_print_styles('mylisting-blog-feed-widget');

		if ( get_query_var( 'paged' ) ) {
			$page = get_query_var( 'paged' );
		} elseif ( get_query_var( 'page' ) ) {
			$page = get_query_var( 'page' );
		} else {
			$page = 1;
		}

		c27()->get_section( 'blog-feed', [
			'template' => $this->get_settings('the_template'),
			'posts_per_page' => $this->get_settings('posts_per_page'),
			'category' => $this->get_settings('select_categories'),
			'include' => $this->get_settings('select_posts'),
			'paged' => $page,
		] );
	}

	protected function content_template() {}
	public function render_plain_content() {}
}
