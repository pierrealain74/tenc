<?php

namespace MyListing\Src\Admin;

if ( ! defined('ABSPATH') ) {
    exit;
}

class Admin {

    public static function boot() {
        new self;
    }

	public function __construct() {
        // Enqueue Admin Scripts and Styles.
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ], 30 );
        add_action( 'elementor/editor/after_enqueue_scripts', [ $this, 'enqueue_scripts' ] );

        // Output iconpicker markup in admin footer.
        add_action( 'admin_footer', [ $this, 'output_iconpicker_template' ] );

        // Add custom WP Admin menu pages.
        add_action( 'admin_menu', [ $this, 'admin_menu' ] );

        // Reorder WP Admin menu.
        add_action( 'admin_menu', [ $this, 'reorder_admin_menu' ], 999 );

        // init classes
        Single_Listing_Screen::instance();
        View_Listings_Screen::instance();
        Settings_Screen::instance();
        Profile_Screen::instance();
        Reports_Screen::instance();
        View_Claims_Screen::instance();

        // setup settings in wp admin > settings > permalinks
        add_action( 'current_screen', [ $this, 'add_permalink_settings' ] );

        foreach ( array_merge( [ 'job_listing_category', 'case27_job_listing_tags', 'region' ], mylisting_custom_taxonomies( 'slug', 'slug' ) ) as $taxonomy ) {
            add_filter( sprintf( 'manage_edit-%s_columns', $taxonomy ), [ $this, 'add_taxonomy_columns' ] );
            add_filter( sprintf( 'manage_%s_custom_column', $taxonomy ), [ $this, 'taxonomy_columns' ], 50, 3 );
        }

        // docs
        add_action( 'mylisting_ajax_cts_get_tip', [ $this, 'docs_get_file' ] );
        add_action( 'mylisting_ajax_cts_get_icons', [ $this, 'get_icon_packs' ] );
        add_action( 'admin_footer', [ $this, 'docs_output_template' ] );
	}

    /**
     * Enqueue theme assets in wp-admin.
     *
     * @since 1.0
     */
    public function enqueue_scripts() {
        wp_enqueue_script( 'jquery-ui-sortable' );
        wp_enqueue_script( 'vuejs' );

        // icons
        wp_enqueue_style( 'mylisting-icons' );
        wp_enqueue_style( 'mylisting-material-icons' );

        // select2
        wp_enqueue_script( 'select2' );
        wp_enqueue_style( 'select2' );

        // momentjs
        wp_enqueue_script( 'moment' );

        // assets
        wp_enqueue_style( 'mylisting-admin-general' );
        wp_enqueue_script( 'theme-script-vendor', c27()->template_uri( 'assets/dist/admin/vendor.js' ), ['jquery'], \MyListing\get_assets_version(), true );
        wp_enqueue_script( 'theme-script-main', c27()->template_uri( 'assets/dist/admin/admin.js' ), ['jquery'], \MyListing\get_assets_version(), true );

        // load font awesome in wp-admin
        if ( class_exists( '\Elementor\Plugin' ) ) {
            $icon_manager = \Elementor\Plugin::instance()->icons_manager;
            if ( $icon_manager instanceof \Elementor\Icons_Manager && method_exists( $icon_manager, 'enqueue_fontawesome_css' ) ) {
                $icon_manager->enqueue_fontawesome_css();
            }
        }
    }

    /**
     * Get list of classnames for icon packs used by the theme.
     *
     * @since 1.0
     */
    public function get_icon_packs() {
        if ( ! is_user_logged_in() ) {
            return;
        }

        return wp_send_json( [
            'material-icons' => array_values( \MyListing\Utils\Icons\Material_Icons::get() ),
            'theme-icons' => array_values( \MyListing\Utils\Icons\Theme_Icons::get() ),
            'font-awesome' => array_values( \MyListing\Utils\Icons\Font_Awesome::get() ),
        ] );
    }

    /**
     * Create custom menu pages in WP Admin.
     *
     * @since 1.0
     */
    public function admin_menu() {
        c27()->new_admin_page( 'menu', [
                __( '<strong>27 &mdash; </strong> Options', 'my-listing' ),
                __( '<strong>Theme Tools</strong>', 'my-listing' ),
                'manage_options',
                'case27/tools.php',
                '',
                c27()->image('27.png'),
                '0.527',
        ] );

        c27()->new_admin_page( 'submenu', [
                'case27/tools.php',
                __( 'Documentation', 'my-listing' ),
                __( 'Documentation', 'my-listing' ),
                'manage_options',
                'case27-tools-docs',
                function() {},
        ] );
    }

    /**
     * Reorder menu items in WP Admin.
     *
     * @since 1.0
     */
    public function reorder_admin_menu() {
        global $menu, $submenu;

        // Main menu (top) items.
        $main = [
            'case27/tools.php' => null,
            'edit.php?post_type=case27_listing_type' => null,
            'edit.php?post_type=job_listing' => null,
        ];

        // Theme Options submenu items.
        $theme_options = [
            'theme-general-settings'      => null,
            'mylisting-options'           => null,
            'theme-stats-settings'        => null,
            'theme-mapservice-settings'   => null,
            'mylisting-user-roles'        => null,
            'theme-messages-settings'     => null,
            'theme-social-login-settings' => null,
            'theme-integration-settings'  => null,
            'case27-tools-shortcodes'     => null,
            'mylisting-onboarding'        => null,
            'case27-tools-docs'           => null,
        ];

        // Reorder main menu items.
        foreach ( (array) $menu as $menu_key => $menu_item ) {
            if ( in_array( $menu_item[2], array_keys( $main ) ) ) {
                $main[ $menu_item[2] ] = apply_filters( 'mylisting/admin/menu-item:'.$menu_item[2], $menu_item );
                unset( $menu[ $menu_key ] );
            }
        }

        $counter = 0;
        foreach ( $main as $main_item ) { $counter++;
            if ( $main_item ) {
                $menu[ sprintf( '1.%d27', $counter ) ] = $main_item;
            }
        }

        // Make sure submenu items exist.
        if ( isset( $submenu['case27/tools.php'] ) ) {
            foreach ( $submenu['case27/tools.php'] as $submenu_item ) {
                if ( in_array( $submenu_item[2], array_keys( $theme_options ) ) ) {
                    $theme_options[ $submenu_item[2] ] = $submenu_item;
                }
            }
        }

        // Update submenu with existing items and new ordering.
        $submenu['case27/tools.php'] = array_filter( apply_filters( 'mylisting/admin-menu/theme-tools', $theme_options ) );
    }

    /**
     * Output the HTML markup for the Iconpicker component.
     *
     * @since 1.6.3
     */
    public function output_iconpicker_template() {
        c27()->get_partial( 'admin/iconpicker' );
        c27()->get_partial( 'admin/mediauploader' );
    }

    public function add_taxonomy_columns( $columns ) {
        $cols = [];
        foreach ( (array) $columns as $key => $label ) {
            $cols[ $key ] = $label;

            if ( $key === 'slug' ) {
                $cols[ 'listing-type' ] = _x( 'Listing Type(s)', 'WP Admin > Terms List > Listing Type column title', 'my-listing' );
            }
        }

        return $cols;
    }

    public function taxonomy_columns( $content, $column, $term_id ) {
        if ( $column !== 'listing-type' ) {
            return $content;
        }

        $types = get_term_meta( $term_id, 'listing_type', true );
        $output = [];
        foreach ( (array) $types as $type_id ) {
            if ( $type = \MyListing\Src\Listing_Type::get( $type_id ) ) {
                $output[] = sprintf( '<a href="%s">%s</a>', get_edit_post_link( $type_id ), $type->get_singular_name() );
            }
        }

        return $output ? join(', ', $output) : '&mdash;';
    }

    /**
     * Init Permalinks_Screen class on Settings > Permalinks page.
     *
     * @since 2.1
     */
    public function add_permalink_settings( $screen ) {
        if ( $screen->id === 'options-permalink' ) {
            Permalinks_Screen::instance();
        }
    }

    /**
     * Handle cts_get_tip Ajax request.
     *
     * @since  1.6.6
     */
    public function docs_get_file() {
        // capability `upload_files` means user is admin, editor, or author role
        if ( empty( $_GET['tip'] ) || ! current_user_can( 'upload_files' ) ) {
            return;
        }

        $doc_filename = sanitize_key( $_GET['tip'] );
        $doc_file = locate_template( 'includes/docs/'.$doc_filename.'.md' );
        if ( $doc_filename && $doc_file ) {
            $doc_contents = file_get_contents( $doc_file );
            $parsedown = new \MyListing\Utils\Parsedown;

            echo $parsedown->text( $doc_contents );
            exit;
        }
    }

    /**
     * Display tip wrapper markup in wp-admin footer.
     *
     * @since  1.6.6
     */
    public function docs_output_template() { ?>
        <div class="cts-tip-wrapper">
            <div class="cts-tip-container">
                <div class="tip-content"></div>
                <div class="tip-footer">
                    <div class="button button-primary close-dialog">Got it!</div>
                </div>
            </div>

            <?php c27()->get_partial( 'spinner', [
                'color' => '#fff',
                'size' => 24,
                'width' => 2.5,
            ] ) ?>
        </div>
    <?php }
}
