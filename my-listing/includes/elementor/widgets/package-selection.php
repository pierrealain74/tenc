<?php

namespace MyListing\Elementor\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Package_Selection extends \Elementor\Widget_Base {

	public function get_name() {
		return 'case27-package-selection-widget';
	}

	public function get_title() {
		return __( '<strong>27</strong> > Package Selection', 'my-listing' );
	}

	public function get_icon() {
		return 'eicon-price-table';
	}

	protected function register_controls() {
		$this->start_controls_section( 'the_package_selection', [
			'label' => __( 'Package Selection', 'my-listing' ),
		] );

		$packages = \MyListing\Src\Paid_Listings\Util::get_products( [ 'fields' => false ] );
		$packagesFormatted = [];

		foreach ($packages as $pckg) {
			$packagesFormatted[$pckg->ID] = $pckg->post_title;
		}

		$this->add_control( 'the_packages', [
			'label' => __( 'Select Packages', 'my-listing' ),
			'type' => \Elementor\Controls_Manager::REPEATER,
			'fields' => [
				[
					'name' => 'package',
					'label' => __( 'Choose package', 'my-listing' ),
					'type' => \Elementor\Controls_Manager::SELECT2,
					'options' => $packagesFormatted,
					'default' => '',
					'label_block' => true,
				],
				[
					'name' => 'featured',
					'label' => __( 'Featured?', 'my-listing' ),
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'default' => '',
					'label_on' => __( 'Yes', 'my-listing' ),
					'label_off' => __( 'No', 'my-listing' ),
					'return_value' => 'yes',
				],
			],
			'title_field' => 'Package ID: {{{ package }}}',
		] );

		$this->add_control( 'the_submit_page', [
			'label' => __( 'Submit to Page:', 'my-listing' ),
			'label_block' => true,
			'type' => 'mylisting-posts-dropdown',
			'post_type' => 'page',
			'post_key' => 'id',
		] );

		$this->end_controls_section();
	}

	protected function render( $instance = [] ) {
		wp_print_styles( 'mylisting-package-selection-widget' );

		c27()->get_section( 'package-selection', [
			'packages' => $this->get_settings('the_packages'),
			'submit_page' => $this->get_settings('the_submit_page'),
		] );
	}

	protected function content_template() {}
	public function render_plain_content() {}
}
