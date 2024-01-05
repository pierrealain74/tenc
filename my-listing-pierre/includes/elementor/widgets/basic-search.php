<?php

namespace MyListing\Elementor\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Basic_Search extends \Elementor\Widget_Base {

	public function get_name() {
		return 'case27-basic-search-widget';
	}

	public function get_title() {
		return __( '<strong>27</strong> > Basic Search Form', 'my-listing' );
	}

	public function get_icon() {
		return 'eicon-post';
	}

	public function get_script_depends() {
		return ['moment'];
	}

	protected function register_controls() {
		$this->start_controls_section( 'section_content_block', [
			'label' => esc_html__( 'Content', 'my-listing' ),
		] );

		$listing_types = is_admin()
			? \MyListing\get_posts_dropdown( 'case27_listing_type', 'post_name', 'post_title', true )
			: [];

		$this->add_control( 'cts_listing_types', [
			'label' => __( 'Listing Types', 'my-listing' ),
			'type' => \Elementor\Controls_Manager::REPEATER,
			'fields' => [ [
				'name' => 'type',
				'label' => __( 'Select Listing Type', 'my-listing' ),
				'type' => \Elementor\Controls_Manager::SELECT2,
				'options' => $listing_types,
				'default' => '',
				'label_block' => true,
			] ],
			'title_field' => sprintf( '{{{ (%s)[type] || "n/a" }}}', trim( wp_json_encode( $listing_types ), '"' ) ),
		] );

		$this->add_control( 'cts_types_display', [
			'label' => __( 'Display listing types as', 'my-listing' ),
			'type' => \Elementor\Controls_Manager::SELECT2,
			'options' => [
				'tabs' => __( 'Tabs', 'my-listing' ),
				'dropdown' => __( 'Dropdown', 'my-listing' ),
			],
			'default' => 'tabs',
			'label_block' => true,
		] );

		$this->add_control( 'cts_tab_style', [
			'label' => __( 'Tab style', 'my-listing' ),
			'type' => \Elementor\Controls_Manager::SELECT2,
			'options' => [
				'transparent' => __( 'Transparent', 'my-listing' ),
				'light' => __( 'Light', 'my-listing' ),
				'dark' => __( 'Dark', 'my-listing' ),
			],
			'default' => 'transparent',
			'label_block' => true,
			'condition' => [ 'cts_types_display' => 'tabs' ],
		] );

		$this->add_control( 'cts_box_shadow', [
			'label' => __( 'Box shadow', 'my-listing' ),
			'type' => \Elementor\Controls_Manager::SWITCHER,
			'default' => '',
			'label_on' => __( 'On', 'my-listing' ),
			'label_off' => __( 'Off', 'my-listing' ),
			'return_value' => 'yes',
		] );

		$this->add_control( 'cts_custom_form', [
			'label' => __( 'Custom Form', 'my-listing' ),
			'type' => \Elementor\Controls_Manager::SWITCHER,
			'default' => '',
			'label_on' => __( 'On', 'my-listing' ),
			'label_off' => __( 'Off', 'my-listing' ),
			'return_value' => 'yes',
		] );

		$this->add_control( 'cts_submit_to_page', [
			'label' => __( 'Submit to page', 'my-listing' ),
			'description' => __( 'Enter page ID. Leave blank to use the main explore page.', 'my-listing' ),
			'type' => \Elementor\Controls_Manager::TEXT,
		] );

		$this->end_controls_section();
	}

	protected function render( $instance = [] ) {
		wp_print_styles('mylisting-basic-search-form');

		$listing_types = \MyListing\get_basic_form_config_for_types( array_filter( array_unique(
			array_column( $this->get_settings('cts_listing_types'), 'type' )
		) ) );

		$types_config = $listing_types['config'];
		$types = $listing_types['types'];
		$submit_to = $this->get_settings('cts_submit_to_page');
		if ( empty( $submit_to ) ) {
			$submit_to = c27()->get_setting( 'general_explore_listings_page' );
		}

		$config = [
			'form_id' => sprintf( 'sform-%s', \MyListing\Utils\Random_Id::generate(5) ),
			'tabs_mode' => $this->get_settings('cts_tab_style'),
			'types_display' => $this->get_settings('cts_types_display'),
			'box_shadow' => $this->get_settings('cts_box_shadow') === 'yes',
			'custom_form' => $this->get_settings('cts_custom_form') === 'yes',
			'target_url' => is_numeric( $submit_to ) ? get_permalink( absint( $submit_to ) ) : $submit_to,
		];

		require locate_template( 'partials/search-form.php' );

		if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
			printf(
				'<script type="text/javascript">%s</script>',
				'document.dispatchEvent( new Event( "mylisting:refresh-basic-forms" ) ); case27_ready_script(jQuery);'
			);
		}
	}

	protected function content_template() {}
	public function render_plain_content( $instance = [] ) {}
}