<?php

namespace MyListing\Elementor\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Add_Listing extends \Elementor\Widget_Base {

	public function get_name() {
		return 'case27-add-listing-widget';
	}

	public function get_title() {
		return __( '<strong>27</strong> > Add Listing Form', 'my-listing' );
	}

	public function get_icon() {
		return 'eicon-form-horizontal';
	}

	protected function register_controls() {
		$this->start_controls_section( 'add_listing_choose_type', [
			'label' => __( 'Listing type selection step', 'my-listing' ),
		] );

		$this->add_control( 'size', [
			'label' => _x( 'Card Size', 'Elementor > Add Listing widget', 'my-listing' ),
			'type' => \Elementor\Controls_Manager::SELECT2,
			'default' => 'medium',
			'options' => [
				'small' => _x( 'Small', 'Elementor > Add Listing widget', 'my-listing' ),
				'medium' => _x( 'Regular', 'Elementor > Add Listing widget', 'my-listing' ),
				'large' => _x( 'Large', 'Elementor > Add Listing widget', 'my-listing' ),
			],
		] );

		$listing_types = is_admin()
			? \MyListing\get_posts_dropdown( 'case27_listing_type', 'post_name' )
			: [];

		$this->add_control( 'listing_types', [
			'label' => _x( 'Listing Type(s)', 'Elementor > Add Listing widget', 'my-listing' ),
			'type' => \Elementor\Controls_Manager::REPEATER,
			'title_field' => '{{{ listing_type.toUpperCase() }}}',
			'fields' => [
				[
					'name' => 'listing_type',
					'label' => _x( 'Listing Type', 'Elementor > Add Listing widget', 'my-listing' ),
					'type' => is_array( $listing_types )
						? \Elementor\Controls_Manager::SELECT2
						: \Elementor\Controls_Manager::TEXT,
					'options' => $listing_types,
					'label_block' => true,
					'default' => '',
				],
				[
					'name' => 'color',
					'label' => _x( 'Color', 'Elementor > Add Listing widget', 'my-listing' ),
					'type' => \Elementor\Controls_Manager::COLOR,
				],
			],
		] );

		$this->add_control( 'form_section_animation', [
			'label' => __( 'Enable form section animations', 'my-listing' ),
			'type' => \Elementor\Controls_Manager::SWITCHER,
			'label_on' => __( 'Yes', 'my-listing' ),
			'label_off' => __( 'no', 'my-listing' ),
			'return_value' => 'yes',
			'default' => 'yes',
		] );

		$this->end_controls_section();

		$this->start_controls_section( 'add_listing_choose_package', [
			'label' => __( 'Package selection step', 'my-listing' ),
		] );

		$this->add_control( 'packages_layout', [
			'label' => _x( 'Package layout', 'Elementor > Add Listing widget', 'my-listing' ),
			'type' => \Elementor\Controls_Manager::SELECT2,
			'default' => 'regular',
			'label_block' => true,
			'options' => [
				'regular' => _x( 'Show 3 packages per row', 'Elementor > Add Listing widget', 'my-listing' ),
				'compact' => _x( 'Show 4 packages per row', 'Elementor > Add Listing widget', 'my-listing' ),
			],
		] );

		$this->end_controls_section();
	}

	protected function render( $instance = [] ) {
		c27()->get_section( 'add-listing', [
			'listing_types' => $this->get_settings('listing_types'),
			'size' => $this->get_settings('size'),
			'packages_layout' => $this->get_settings('packages_layout'),
			'form_section_animation' => $this->get_settings('form_section_animation'),
			'is_edit_mode' => \Elementor\Plugin::$instance->editor->is_edit_mode(),
		] );
	}

	protected function content_template() {}
	public function render_plain_content() {}
}
