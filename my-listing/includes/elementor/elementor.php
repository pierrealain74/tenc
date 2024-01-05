<?php

namespace MyListing\Elementor;

use \Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Elementor {

	public
		$widgets,
		$controls;

	public static function boot() {
		new self;
	}

	public function __construct() {
		if ( ! defined( 'ELEMENTOR_VERSION' ) ) {
			return;
		}

		require locate_template( 'includes/elementor/elementor-helpers.php' );

		$this->widgets = [
			'Page_Heading',
			'Title_Bar',
			'Featured_Section',
			'Section_Heading',
			'Listing_Categories',
			'Listing_Feed',
			'Add_Listing',
			'Info_Cards',
			'Featured_Service',
			'Testimonials',
			'Team',
			'Image',
			'Clients_Slider',
			'Map',
			'Package_Selection',
			'Explore',
			'Basic_Search',
			'Blog_Feed',

			// Block Elements
			'Content_Block',
			'Gallery_Block',
			'Countdown_Block',
			'Table_Block',
			'Accordion_Block',
			'Tabs_Block',
			'Video_Block',
		];

		$this->controls = [
			'icon',
		];

        add_action( 'elementor/editor/after_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
        add_action( 'elementor/controls/register', [ $this, 'register_controls' ] );

		add_action( 'elementor/widgets/register', array( $this, 'widgets_registered' ) );
		add_action( 'elementor/init', array( $this, 'controls_registered' ) );

		add_action('elementor/documents/register_controls', [$this, 'elementor_page_settings_controls']);

		add_action( 'elementor/element/column/layout/before_section_end', function( $column ) {
			$column->add_control(
				'mylisting_link_to',
				[
					'label' => _x( 'Link to url', 'Elementor column settings', 'my-listing' ),
					'type' => Controls_Manager::URL,
					'show_external' => true,
					'default' => [
						'url' => '',
						'is_external' => false,
						'nofollow' => false,
					],
				]
			);
		} );

		add_action( 'elementor/element/after_add_attributes', function( $element ) {
			$link_to = $element->get_settings('mylisting_link_to');
			if ( ! is_array( $link_to ) || empty( trim( $link_to['url'] ) ) ) {
				return;
			}

			$element->add_render_attribute( '_wrapper', 'data-mylisting-link-to', wp_json_encode( $link_to ) );
		} );

		add_action( 'wp_enqueue_scripts', [ $this, 'load_custom_fonts' ], 10 );

		add_action( 'wp_enqueue_scripts', function() {
			if ( class_exists( '\Elementor\Frontend' ) ) {
				\Elementor\Plugin::instance()->frontend->enqueue_styles();
			}
		} );

		// add support for Elementor Pro custom headers & footers
		add_action( 'elementor/theme/register_locations', [ $this, 'register_locations' ] );

		add_filter( 'mylisting/header-config', [ $this, 'theme_header_config' ] );

		// keep support for font-awesome 4
		add_filter( 'pre_option_elementor_load_fa4_shim', function() {
			return 'yes';
		} );
	}

	public function widgets_registered( $widgets_manager ) {
		if ( ! defined( 'ELEMENTOR_PATH' ) || ! class_exists( '\Elementor\Widget_Base' ) || ! class_exists( '\Elementor\Plugin' ) ) {
			return false;
		}

		foreach ( $this->widgets as $widget ) {
			$classname = sprintf( '\MyListing\Elementor\Widgets\%s', $widget );
			if ( class_exists( $classname ) ) {
				$widgets_manager->register(
					new $classname()
				);
			}
		}
	}

	public function controls_registered() {
		if ( ! defined( 'ELEMENTOR_PATH' ) || ! class_exists( '\Elementor\Widget_Base' ) ) {
			return;
		}

		if ( ! class_exists( 'Elementor\Base_Data_Control' ) || ! class_exists( '\Elementor\Plugin' ) ) {
			return;
		}

		foreach ($this->controls as $control) {
			$template_file = locate_template( 'includes/extensions/elementor/controls/'.$control.'.php' );
			if ( $template_file ) {
				require_once $template_file;
			}
		}
	}

	public function elementor_page_settings_controls( $page ) {
		$page->start_controls_section(
			'mylisting_page_header_settings',
			[
				'label' => __( 'Header', 'my-listing' ),
				'tab' => Controls_Manager::TAB_SETTINGS,
			]
		);

		$page->add_control(
			'c27_hide_header',
			[
				'label' => __( 'Hide Header?', 'my-listing' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => __( 'Hide', 'my-listing' ),
				'label_off' => __( 'Show', 'my-listing' ),
			]
		);

		$page->add_control(
			'c27_header_blend_to_next_section',
			[
				'label' => __( 'Blend header to the next section?', 'my-listing' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => __( 'Yes', 'my-listing' ),
				'label_off' => __( 'No', 'my-listing' ),
			]
		);

		$page->add_control(
			'c27_show_title_bar',
			[
				'label' => __( 'Show Title Bar?', 'my-listing' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => c27()->get_setting('header_show_title_bar', false) ? 'yes' : '',
				'label_on' => __( 'Show', 'my-listing' ),
				'label_off' => __( 'Hide', 'my-listing' ),
				'condition' => ['c27_hide_header' => ''],
			]
		);

		$page->add_control(
			'c27_customize_header',
			[
				'label' => __( 'Customize Header?', 'my-listing' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => __( 'Yes', 'my-listing' ),
				'label_off' => __( 'No', 'my-listing' ),
				'condition' => ['c27_hide_header' => ''],
			]
		);

		$page->add_control(
			'c27_header_style',
			[
				'label' => __( 'Height', 'my-listing' ),
				'type' => Controls_Manager::SELECT,
				'default' => c27()->get_setting('header_style', 'default'),
				'options' => [
					'default' => __( 'Normal', 'my-listing' ),
					'alternate' => __( 'Extended', 'my-listing' ),
				],
				'condition' => ['c27_customize_header' => 'yes'],
			]
		);

		$page->add_control(
			'c27_header_width',
			[
				'label' => __( 'Width', 'my-listing' ),
				'type' => Controls_Manager::SELECT,
				'default' => c27()->get_setting('header_width', 'full-width'),
				'options' => [
					'full-width' => __( 'Full Width', 'my-listing' ),
					'boxed' => __( 'Boxed', 'my-listing' ),
				],
				'condition' => ['c27_customize_header' => 'yes'],
			]
		);

		$page->add_control(
			'c27_boxed_header_width',
			[
				'label' => __( 'Boxed header width (px)', 'my-listing' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1200,
						'step' => 1,
					]
				],
				'default' => [
					'unit' => 'px',
					'size' => c27()->get_setting('boxed_header_width', 1120),
				],
				'condition' => ['c27_customize_header' => 'yes', 'c27_header_width' => 'boxed'],
			]
		);

		$page->add_control(
			'c27_header_skin',
			[
				'label' => __( 'Text Color', 'my-listing' ),
				'type' => Controls_Manager::SELECT,
				'default' => c27()->get_setting('header_skin', 'dark'),
				'options' => [
					'dark' => __( 'Light', 'my-listing' ),
					'light' => __( 'Dark', 'my-listing' ),
				],
				'condition' => ['c27_customize_header' => 'yes'],
			]
		);

		$page->add_control(
			'c27_header_position',
			[
				'label' => __( 'Sticky header on scroll?', 'my-listing' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => c27()->get_setting('header_fixed', true) == true ? 'yes' : '',
				'label_on' => __( 'Yes', 'my-listing' ),
				'label_off' => __( 'No', 'my-listing' ),
				'return_value' => 'yes',
				'condition' => ['c27_customize_header' => 'yes'],
			]
		);

		$page->add_control(
		    'c27_header_background',
		    [
		        'label' => __( 'Background Color', 'my-listing' ),
		        'type' => Controls_Manager::COLOR,
		        'default' => c27()->get_setting('header_background_color', 'rgba(29, 29, 31, 0.95)'),
				'condition' => ['c27_customize_header' => 'yes'],
				'selectors' => [
					'{{WRAPPER}} .c27-main-header:not(.header-scroll) .header-skin' => 'background-color: {{VALUE}}',
				],
		    ]
		);

		$page->add_control(
		    'c27_header_border_color',
		    [
		        'label' => __( 'Border Color', 'my-listing' ),
		        'type' => Controls_Manager::COLOR,
		        'default' => c27()->get_setting('header_border_color', 'rgba(29, 29, 31, 0.95)'),
				'condition' => ['c27_customize_header' => 'yes'],
				'selectors' => [
					'{{WRAPPER}} .c27-main-header:not(.header-scroll) .header-skin' => 'border-color: {{VALUE}}',
				],
		    ]
		);

		$page->add_control(
			'c27_header_show_search_form',
			[
				'label' => __( 'Show Search Form?', 'my-listing' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => c27()->get_setting('header_show_search_form', true) == true ? 'yes' : '',
				'label_on' => __( 'Show', 'my-listing' ),
				'label_off' => __( 'Hide', 'my-listing' ),
				'return_value' => 'yes',
				'condition' => ['c27_customize_header' => 'yes'],
			]
		);

		$page->add_control(
			'c27_header_menu_location',
			[
				'label' => __( 'Main Menu Location', 'my-listing' ),
				'type' => Controls_Manager::SELECT,
				'default' => c27()->get_setting('header_menu_location', 'right'),
				'options' => [
					'left'   => __( 'Left', 'my-listing' ),
					'center' => __( 'Center', 'my-listing' ),
					'right'  => __( 'Right', 'my-listing' ),
				],
				'condition' => ['c27_customize_header' => 'yes'],
			]
		);

		$page->add_control(
			'c27_header_show_call_to_action',
			[
				'label' => __( 'Show Call to Action button?', 'my-listing' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => c27()->get_setting('header_show_call_to_action_button', false) == true ? 'yes' : '',
				'label_on' => __( 'Show', 'my-listing' ),
				'label_off' => __( 'Hide', 'my-listing' ),
				'return_value' => 'yes',
				'condition' => ['c27_customize_header' => 'yes'],
			]
		);

		$page->end_controls_section();

		$page->start_controls_section(
			'mylisting_page_footer_settings',
			[
				'label' => __( 'Footer', 'my-listing' ),
				'tab' => Controls_Manager::TAB_SETTINGS,
			]
		);

		$page->add_control(
			'c27_hide_footer',
			[
				'label' => __( 'Hide footer?', 'my-listing' ),
				'description' => __( 'Useful when you want to add a custom footer.', 'my-listing'),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => __( 'Hide', 'my-listing' ),
				'label_off' => __( 'Show', 'my-listing' ),
			]
		);

		$page->add_control(
			'c27_customize_footer',
			[
				'label' => __( 'Customize Footer?', 'my-listing' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => __( 'Yes', 'my-listing' ),
				'label_off' => __( 'No', 'my-listing' ),
				'condition' => ['c27_hide_footer' => ''],
			]
		);

		$page->add_control(
		    'c27_footer_background',
		    [
		        'label' => __( 'Background Color', 'my-listing' ),
		        'type' => Controls_Manager::COLOR,
		        'default' => c27()->get_setting('footer_background_color', 'rgba(29, 29, 31, 0.95)'),
				'condition' => ['c27_customize_footer' => 'yes'],
				'selectors' => [
					'{{WRAPPER}} footer.footer' => 'background-color: {{VALUE}}',
				],
		    ]
		);

		$page->add_control(
			'c27_footer_show_widgets',
			[
				'label' => __( 'Show Widgets', 'my-listing' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_on' => __( 'Show', 'my-listing' ),
				'label_off' => __( 'Hide', 'my-listing' ),
				'return_value' => 'yes',
				'condition' => ['c27_customize_footer' => 'yes'],
			]
		);

		$page->add_control(
			'c27_footer_widgets_per_row_d',
			[
				'label' => __( 'Widgets per row desktop', 'my-listing' ),
				'type' => Controls_Manager::SELECT,
				'default' => c27()->get_setting('footer_widgets_per_row_d', 'col-lg-4'),
				'options' => [
					'col-lg-12' => __( '1', 'my-listing' ),
					'col-lg-6' => __( '2', 'my-listing' ),
					'col-lg-4' => __( '3', 'my-listing' ),
					'col-lg-3' => __( '4', 'my-listing' ),
					'col-lg-20' => __( '5', 'my-listing' ),
				],
				'condition' => ['c27_customize_footer' => 'yes', 'c27_footer_show_widgets' => 'yes'],
			]
		);

		$page->add_control(
			'c27_footer_widgets_per_row_t',
			[
				'label' => __( 'Widgets per row tablet', 'my-listing' ),
				'type' => Controls_Manager::SELECT,
				'default' => c27()->get_setting('footer_widgets_per_row_t', 'col-sm-6'),
				'options' => [
					'col-sm-12' => __( '1', 'my-listing' ),
					'col-sm-6' => __( '2', 'my-listing' ),
					'col-sm-4' => __( '3', 'my-listing' ),
					'col-sm-3' => __( '4', 'my-listing' ),
					'col-sm-20' => __( '5', 'my-listing' ),
				],
				'condition' => ['c27_customize_footer' => 'yes', 'c27_footer_show_widgets' => 'yes'],
			]
		);

		$page->add_control(
			'c27_footer_widgets_per_row_m',
			[
				'label' => __( 'Widgets per row mobile', 'my-listing' ),
				'type' => Controls_Manager::SELECT,
				'default' => c27()->get_setting('footer_widgets_per_row_m', 'col-xs-12'),
				'options' => [
					'col-xs-12' => __( '1', 'my-listing' ),
					'col-xs-6' => __( '2', 'my-listing' ),
					'col-xs-4' => __( '3', 'my-listing' ),
				],
				'condition' => ['c27_customize_footer' => 'yes', 'c27_footer_show_widgets' => 'yes'],
			]
		);

		$page->add_control(
			'c27_footer_show_footer_menu',
			[
				'label' => __( 'Show Footer Menu', 'my-listing' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_on' => __( 'Show', 'my-listing' ),
				'label_off' => __( 'Hide', 'my-listing' ),
				'return_value' => 'yes',
				'condition' => ['c27_customize_footer' => 'yes'],
			]
		);

		$page->end_controls_section();
	}

	public function load_custom_fonts() {
		if ( ! class_exists( '\Elementor\Plugin' ) ) {
			return;
		}

		$elementor = \Elementor\Plugin::instance();
		$kit = $elementor->kits_manager->get_active_kit();
		$page_settings = $kit->get_meta( \Elementor\Core\Settings\Page\Manager::META_KEY );
		$vars = [];

		// if any of the font groups is set to default, load the default font
		if ( ! empty( $page_settings['system_typography'] ) ) {
			add_filter( 'mylisting/assets/load-default-font', '__return_false' );

			foreach ( (array) $page_settings['system_typography'] as $group ) {
				if ( empty( $group['typography_font_family'] ) ) {
					add_filter( 'mylisting/assets/load-default-font', '__return_true' );
					$vars[] = sprintf( '--e-global-typography-%s-font-family: GlacialIndifference;', $group['_id'] );
				}
			}
		}

		add_action( 'wp_enqueue_scripts', function() use ( $vars ) {
			wp_add_inline_style( 'theme-styles-default', sprintf( ':root{%s}', join( '', $vars ) ) );
		}, 50 );
	}

	public function register_locations( $location_manager ) {
		$location_manager->register_location( 'header' );
		$location_manager->register_location( 'footer' );
	}

	public function theme_header_config( $config ) {
		// Get the page settings manager
		$page_settings_manager = \Elementor\Core\Settings\Manager::get_settings_managers( 'page' );

		// Get the settings model for current post
		$page_settings_model = $page_settings_manager->get_model( get_queried_object_id() );

		if ( class_exists( '\ElementorPro\Modules\ThemeBuilder\Module' ) ) {
			if ( function_exists( 'is_shop' ) && is_shop() ) {
				$location = 'archive';
			} elseif ( is_archive() || is_tax() || is_home() || is_search() ) {
				$location = 'archive';
			} elseif ( is_singular() || is_404() ) {
				$location = 'single';
			}

			if ( $location ) {
				$module = \ElementorPro\Modules\ThemeBuilder\Module::instance();
				$conditions_manager = $module->get_conditions_manager();
				$documents = $conditions_manager->get_documents_for_location( $location );
				if ( ! empty( $documents ) ) {
					$page_settings_model = $documents[ key( $documents ) ];
				}
			}
		}

        $GLOBALS['c27_elementor_page'] = $page_settings_model; // @todo: rewrite

        $config['header']['show'] = false;
        $config['title-bar']['show'] = false;

        if ( $page_settings_model->get_settings( 'c27_hide_header' ) !== 'yes' ) {
            $config['header']['show'] = true;
            $config['title-bar']['show'] = $page_settings_model->get_settings( 'c27_show_title_bar' ) === 'yes';
            $config['header']['args'] = [];
            $config['header']['args']['blend_to_next_section'] = $page_settings_model->get_settings( 'c27_header_blend_to_next_section' ) === 'yes';

            if ( $page_settings_model->get_settings( 'c27_customize_header' ) === 'yes' ) {
            	$config['header']['args']['fixed']               = $page_settings_model->get_settings( 'c27_header_position' );
            	$config['header']['args']['style']               = $page_settings_model->get_settings( 'c27_header_style' );
            	$config['header']['args']['width']               = $page_settings_model->get_settings( 'c27_header_width' );
            	$config['header']['args']['boxed_width']         = $page_settings_model->get_settings( 'c27_boxed_header_width' )['size'];
            	$config['header']['args']['skin']                = $page_settings_model->get_settings( 'c27_header_skin' );
            	$config['header']['args']['menu_location']       = $page_settings_model->get_settings( 'c27_header_menu_location' );
            	$config['header']['args']['background_color']    = $page_settings_model->get_settings( 'c27_header_background' );
            	$config['header']['args']['border_color']        = $page_settings_model->get_settings( 'c27_header_border_color' );
            	$config['header']['args']['show_search_form']    = $page_settings_model->get_settings( 'c27_header_show_search_form' );
            	$config['header']['args']['show_call_to_action'] = $page_settings_model->get_settings( 'c27_header_show_call_to_action' );
            	$config['header']['args']['is_edit_mode']        = \Elementor\Plugin::$instance->editor->is_edit_mode();
            }
        }

	    $is_buddypress_profile = function_exists( 'bp_is_user' ) ? bp_is_user() : false;

	    if ( is_singular('job_listing') || is_page_template('templates/content-featured-image.php') || $is_buddypress_profile ) {
	        $config['header']['show'] = true;
	        $config['title-bar']['show'] = false;
	        $config['header']['args']['style'] = c27()->get_setting('single_listing_header_style', 'default');
	        $config['header']['args']['width'] = c27()->get_setting('single_listing_header_width', 'full-width');
	        $config['header']['args']['boxed_width'] = c27()->get_setting('single_listing_boxed_header_width', 1120);
	        $config['header']['args']['skin'] = c27()->get_setting('single_listing_header_skin', 'dark');
	        $config['header']['args']['background_color'] = c27()->get_setting('single_listing_header_background_color', 'rgba(29, 29, 31, 0.95)');
	        $config['header']['args']['border_color'] = c27()->get_setting('single_listing_header_border_color', 'rgba(29, 29, 31, 0.95)');
	        $config['header']['args']['fixed'] = true;
	        $config['header']['args']['blend_to_next_section'] = c27()->get_setting('single_listing_blend_header', true);
	    }

	    return $config;
	}

	public function enqueue_scripts() {
		wp_enqueue_script( 'mylisting-elementor' );
	}

	public function register_controls() {
		$controls_manager = \Elementor\Plugin::$instance->controls_manager;
		$controls_manager->register(
			new \MyListing\Elementor\Controls\Posts_Dropdown_Control
		);
	}
}
