<?php

namespace MyListing\Ext\Custom_Taxonomies;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Custom_Taxonomies {
    use \MyListing\Src\Traits\Instantiatable;

    /**
     * List of custom taxonomies
     * @var array
     */
    public $_custom_taxonomies = [];

    public function __construct() {

        $this->_custom_taxonomies = self::custom_taxonomies_list();

        $saved_option = get_option( 'job_manager_custom_taxonomy' );

        if ( ! $saved_option || ! is_array( $saved_option ) ) {
            add_option( 'job_manager_custom_taxonomy', [] );
        }

        // add settings page
        add_action( 'admin_menu', [ $this, 'add_settings_page' ], 30 );
        add_action( 'admin_init', [ $this, 'register_settings' ] );

        if ( ! $this->_custom_taxonomies ) {
            return $this->_custom_taxonomies = [];
        }

        add_action( 'init', [ $this, 'register_taxonomies' ], 0 );
        add_filter( 'mylisting/types/fields/presets', [ $this, 'add_term_select_field' ], 99 );
    }

    public function add_term_select_field( $default_fields ) {
        foreach ( $this->_custom_taxonomies as $key => $value ) {

            $default_fields[ $value['slug'] ] = new \MyListing\Src\Forms\Fields\Term_Select_Field( [
                'slug'           => $value['slug'],
                'label'          => $value['label'],
                'required'       => false,
                'priority'       => 5,
                'taxonomy'       => $value['slug'],
                'is_custom'      => false,
                'terms-template' => 'multiselect',
            ] );
        }

        return $default_fields;
    }

    public function register_taxonomies() {
        foreach ( $this->_custom_taxonomies as $ct => $value ) {

            $title = $value['label'];

            $labels = [
                'name'                  => _x( $title, 'Taxonomy plural name', 'my-listing' ),
                'singular_name'         => _x( $title, 'Taxonomy singular name', 'my-listing' ),
                'search_items'          => __( 'Search Items', 'my-listing' ),
                'popular_items'         => __( 'Popular Items', 'my-listing' ),
                'all_items'             => __( 'All Items', 'my-listing' ),
                'parent_item'           => __( 'Parent Item', 'my-listing' ),
                'parent_item_colon'     => __( 'Parent Item', 'my-listing' ),
                'edit_item'             => __( 'Edit Item', 'my-listing' ),
                'update_item'           => __( 'Update Item', 'my-listing' ),
                'add_new_item'          => __( 'Add New Item', 'my-listing' ),
                'new_item_name'         => __( 'New Item', 'my-listing' ),
                'add_or_remove_items'   => __( 'Add or remove Item', 'my-listing' ),
                'choose_from_most_used' => __( 'Choose from most used Items', 'my-listing' ),
                'menu_name'             => __( $title, 'my-listing' ),
            ];

            $args = [
                'labels'            => $labels,
                'public'            => true,
                'show_in_nav_menus' => true,
                'show_admin_column' => false,
                'hierarchical'      => true,
                'show_tagcloud'     => true,
                'show_ui'           => true,
                'query_var'         => true,
                'rewrite' => [
                    'slug' => $value['slug'],
                    'with_front'   => false,
                    'hierarchical' => false,
                ],
                'query_var'         => true,
                'capabilities'      => [],
            ];

            register_taxonomy( $value['slug'], [ 'job_listing' ], $args );
        }
    }

    public function get_custom_taxonomies_list( $key = 'slug', $value = 'label'  ) {
        $taxonomies = [];
        if ( ! in_array( $key, [ 'slug', 'label' ] ) || ! in_array( $value, [ 'slug', 'label' ] ) ) {
            return $taxonomies;
        }

        foreach ( $this->_custom_taxonomies as $taxonomy ) {
            if ( ! $taxonomy ) {
                continue;
            }

            $taxonomies[ $taxonomy[ $key ] ] = $taxonomy[ $value ];
        }

        return $taxonomies;
    }

    public function append_custom_taxonomies_slug( $taxonomies_list ) {

        foreach ( $this->_custom_taxonomies as $key => $value ) {

            if ( ! $value ) {
                continue;
            }

            $taxonomies_list[ $value['slug'] ] = $value['slug'];
        }

        return $taxonomies_list;
    }

    public static function custom_taxonomies_list() {
        $taxonomies = get_option( 'job_manager_custom_taxonomy' );

        if ( ! $taxonomies ) {
            return [];
        }

        $return_list = [];

        foreach ( (array) $taxonomies as $taxonomy ) {

            if ( empty( $taxonomy['slug'] ) || empty( $taxonomy['label'] ) ) {
                continue;
            }

            $return_list[] = [
                'slug'  => sanitize_title( $taxonomy['slug'] ),
                'label' => esc_html( $taxonomy['label'] ),
            ];
        }

        return $return_list;
    }

    private function _normalize_option_data( $option_data ) {

        $return_list = [];

        foreach ( $option_data as $data ) {
            $return_list[ $data['slug'] ] = $data['label'];
        }

        return array_filter( $return_list );
    }

    public function add_settings_page() {
        add_submenu_page(
            'edit.php?post_type=job_listing',
            _x( 'Taxonomies', 'Taxonomies link title', 'my-listing' ),
            _x( 'Taxonomies', 'Taxonomies link title', 'my-listing' ),
            'manage_options',
            'mylisting-custom-taxonomies',
            function() {
                // enqueue scripts
                wp_enqueue_script( 'mylisting-admin-custom-taxonomies' );
                wp_localize_script( 'mylisting-admin-custom-taxonomies', 'c27_custom_taxonomies', [
                    'deleteMsg'  => esc_html__('Are you sure you want to delete this taxonomy?', 'my-listing'),
                    'requiredMsg'=> esc_html__('All fields values are required.', 'my-listing'),
                    'taxonomies' => $this->_custom_taxonomies
                ] );

                // load template
                require locate_template( 'templates/admin/custom-taxonomies.php' );
            }
        );
    }

    public function register_settings() {
        register_setting( 'mylisting_custom_taxonomies', 'job_manager_custom_taxonomy' );
    }
}