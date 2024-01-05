<?php

namespace MyListing\Elementor\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Map extends \Elementor\Widget_Base {

	public function get_name() {
		return 'case27-map-widget';
	}

	public function get_title() {
		return __( '<strong>27</strong> > Map', 'my-listing' );
	}

	public function get_icon() {
		return 'eicon-google-maps';
	}

	protected function register_controls() {
		$this->start_controls_section( 'section_map_block', [
			'label' => __( 'Map', 'my-listing' ),
		] );

		$this->add_control( 'the_template', [
			'label' => __( 'Template', 'my-listing' ),
			'type' => \Elementor\Controls_Manager::SELECT2,
			'default' => 'default',
			'options' => [
				'default' => __( 'Default', 'my-listing' ),
				'block' => __( 'Block', 'my-listing' ),
				'full_width_content' => __( 'Full width + Content Overlay', 'my-listing' ),
			],
		] );

		$this->add_control( 'the_content', [
			'label' => __( 'Content', 'my-listing' ),
			'type' => \Elementor\Controls_Manager::WYSIWYG,
			'condition' => ['the_template' => 'full_width_content'],
		] );

		$this->add_control( 'the_icon', [
			'label' => __( 'Icon', 'my-listing' ),
			'type' => \Elementor\Controls_Manager::ICON,
			'condition' => ['the_template' => 'block'],
		] );

		$this->add_control( 'the_title', [
			'label' => __( 'Title', 'my-listing' ),
			'type' => \Elementor\Controls_Manager::TEXT,
			'default' => '',
			'condition' => ['the_template' => 'block'],
		] );

		$this->add_control( 'show_get_directions', [
			'label' => __( 'Show "Get Directions" Link?', 'my-listing' ),
			'type' => \Elementor\Controls_Manager::SWITCHER,
			'default' => 'yes',
			'label_on' => __( 'Show', 'my-listing' ),
			'label_off' => __( 'Hide', 'my-listing' ),
			'return_value' => 'yes',
			'condition' => ['the_template' => 'block'],
		] );

		$this->end_controls_section();

		$this->start_controls_section( 'the_map_controls', [
			'label' => __( 'Map Options', 'my-listing' ),
		] );

		$this->add_control( 'the_skin', [
			'label' => __( 'Map Skin', 'my-listing' ),
			'type' => \Elementor\Controls_Manager::SELECT,
			'default' => 'skin',
			'options' => \MyListing\Apis\Maps\get_skins(),
		] );

		$this->add_control( 'the_zoom', [
			'label' => __( 'Zoom Level', 'my-listing' ),
			'type' => \Elementor\Controls_Manager::SLIDER,
			'default' => [ 'size' => 10 ],
			'range' => [
				'px' => [
					'min' => 1,
					'max' => 20,
				],
			],
		] );

		$this->add_control( 'height', [
			'label' => __( 'Height', 'my-listing' ),
			'type' => \Elementor\Controls_Manager::SLIDER,
	        'default' => ['size' => 500, 'unit' => 'px'],
	        'size_units' => [ 'px', 'vh' ],
			'range' => [
	            'px' => [
	                'min' => 0,
	                'max' => 2500,
	                'step' => 1,
	            ],
	            'vh' => [
	                'min' => 0,
	                'max' => 200,
	            ],
			],
			'selectors' => [
				'{{WRAPPER}} .contact-map' => 'height: {{SIZE}}{{UNIT}} !important;',
				'{{WRAPPER}} .featured-section-type-map' => 'height: {{SIZE}}{{UNIT}} !important;',
			],
		] );

		$this->add_control( 'the_cluster_markers', [
			'label' => __( 'Cluster Markers?', 'my-listing' ),
			'type' => \Elementor\Controls_Manager::SWITCHER,
			'default' => true,
			'label_on' => __( 'Yes', 'my-listing' ),
			'label_off' => __( 'No', 'my-listing' ),
			'return_value' => true,
		] );

		$this->end_controls_section();

		$this->start_controls_section( 'the_map_locations_controls', [
			'label' => __( 'Locations', 'my-listing' ),
		] );

		$this->add_control( 'the_map_items', [
			'label' => __( 'Map items', 'my-listing' ),
			'type' => \Elementor\Controls_Manager::SELECT,
			'default' => 'custom-locations',
			'options' => [
				'custom-locations' => __( 'Custom Locations', 'my-listing' ),
				'listings' => __( 'Listings', 'my-listing' ),
			],
		] );

		$this->add_control( 'the_locations', [
			'label' => __( 'Locations', 'my-listing' ),
			'type' => \Elementor\Controls_Manager::REPEATER,
			'fields' => [
				[
					'name' => 'marker_lat',
					'label' => __( 'Latitude', 'my-listing' ),
					'placeholder' => '41.376',
					'type' => \Elementor\Controls_Manager::NUMBER,
					'default' => '',
				],
				[
					'name' => 'marker_lng',
					'label' => __( 'Longitude', 'my-listing' ),
					'placeholder' => '2.114639',
					'type' => \Elementor\Controls_Manager::NUMBER,
					'default' => '',
				],
				[
					'name' => 'marker_image',
					'label' => __( 'Marker Image', 'my-listing' ),
					'type' => \Elementor\Controls_Manager::MEDIA,
				],
			],
			'title_field' => '{{{ marker_lat }}}, {{{ marker_lng }}}',
			'condition' => ['the_map_items' => 'custom-locations'],
		] );

		$this->add_control( '27_listings_lat', [
			'label' => __( 'Latitude', 'my-listing' ),
			'placeholder' => '41.376',
			'type' => \Elementor\Controls_Manager::NUMBER,
			'condition' => ['the_map_items' => 'listings'],
		] );

		$this->add_control( '27_listings_lng', [
			'label' => __( 'Longitude', 'my-listing' ),
			'placeholder' => '2.114639',
			'type' => \Elementor\Controls_Manager::NUMBER,
			'condition' => ['the_map_items' => 'listings'],
		] );

		$this->add_control( '27_listings_radius', [
			'label' => __( 'Radius', 'my-listing' ),
			'default' => 250,
			'type' => \Elementor\Controls_Manager::NUMBER,
			'condition' => ['the_map_items' => 'listings'],
		] );

		$this->add_control( '27_listings_type', [
			'label' => __( 'Listing Type', 'my-listing' ),
			'type' => 'mylisting-posts-dropdown',
			'post_type' => 'case27_listing_type',
			'post_key' => 'slug',
			'condition' => [ 'the_map_items' => 'listings' ],
		] );

		$this->add_control( '27_listings_count', [
			'label' => __( 'Count', 'my-listing' ),
			'description' => __( 'How many listings to show?', 'my-listing' ),
			'default' => c27()->get_setting( 'general_explore_listings_per_page', 9 ),
			'type' => \Elementor\Controls_Manager::NUMBER,
			'condition' => ['the_map_items' => 'listings'],
		] );

		$this->end_controls_section();
	}

	protected function render( $instance = [] ) {
		wp_print_styles( 'mylisting-featured-section-widget' );

		c27()->get_section( 'map', [
			'options' => [
				'items_type' => $this->get_settings('the_map_items'),
				'skin' => $this->get_settings('the_skin'),
				'zoom' => $this->get_settings('the_zoom')['size'],
				'locations' => $this->get_settings('the_locations'),
				'cluster_markers' => $this->get_settings('the_cluster_markers'),
				'listings_query' => [
					'lat' => $this->get_settings('27_listings_lat'),
					'lng' => $this->get_settings('27_listings_lng'),
					'radius' => $this->get_settings('27_listings_radius'),
					'listing_type' => $this->get_settings('27_listings_type'),
					'count' => $this->get_settings('27_listings_count'),
				],
				],
			'template' => $this->get_settings('the_template'),
			'title' => $this->get_settings('the_title'),
			'icon' => $this->get_settings('the_icon'),
			'icon_style' => $this->get_settings('the_icon_style'),
			'show_get_directions' => $this->get_settings('show_get_directions'),
			'content' => $this->get_settings('the_content'),
			'is_edit_mode' => \Elementor\Plugin::$instance->editor->is_edit_mode(),
		] );
	}

	protected function content_template() {}
	public function render_plain_content() {}
}
