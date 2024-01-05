<?php

namespace MyListing\Src\Listing_Types;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Editor {
    use \MyListing\Src\Traits\Instantiatable;

    /**
     * Used to cache method return values for multiple calls.
     *
     * @since 2.2
     */
    private $cache = [];

    public function __construct() {
        if ( ! is_admin() ) {
            return;
        }

        Revisions::instance();
        add_action( 'load-post.php', [ $this, 'init_metabox' ] );
        add_action( 'load-post-new.php', [ $this, 'init_metabox' ] );
        add_action( 'admin_notices', [ $this, 'regenerate_preview_cards_notice' ], 1000 );
    }

    public function init_metabox() {
        $screen = get_current_screen();
        if ( ! ( $screen && $screen->id === 'case27_listing_type' ) ) {
            return;
        }

        add_action( 'add_meta_boxes', [ $this, 'add_metabox' ] );
        add_action( 'save_post', [ $this, 'save_metabox' ], 10, 2 );

        // @todo: relocate, maybe add a hook for each filter to pass data to JS
        add_filter( 'mylisting/type-editor:config', function( $config ) {
            $config['recur_filter_ranges'] = apply_filters( 'mylisting/filters/recurring-date:ranges', [
                'all' => 'Any day',
                'today' => 'Today',
                'tomorrow' => 'Tomorrow',
                'this-week' => 'This week',
                'this-weekend' => 'This weekend',
                'next-week' => 'Next week',
                'this-month' => 'This month',
                'next-month' => 'Next month',
                'any' => 'Any day (including past days)',
            ] );

            return $config;
        } );
    }

    /**
     * Add a custom metabox in `case27_listing_type` post types to
     * render the listing type editor in.
     *
     * @since 1.0
     */
    public function add_metabox() {
        add_meta_box(
            'case27-listing-type-options',
            __( 'Listing Type Options', 'my-listing' ),
            function( $post ) {
                wp_nonce_field( 'save_type_editor', '_themenonce' );
                require_once locate_template( 'includes/src/listing-types/views/metabox.php' );
            },
            'case27_listing_type',
            'advanced',
            'high'
        );
    }

    /**
     * Save the listing type configuration on post save.
     *
     * @since 1.0
     */
    public function save_metabox( $post_id, $post ) {
        // Add nonce for security and authentication.
        $nonce_name   = isset( $_POST['_themenonce'] ) ? $_POST['_themenonce'] : '';
        $nonce_action = 'save_type_editor';

        // Check if nonce is set and valid.
        if ( ! ( isset( $nonce_name ) && wp_verify_nonce( $nonce_name, $nonce_action ) ) ) {
            return;
        }

        // Check if user has permissions to save data.
        if ( ! current_user_can( 'edit_post', $post_id ) || wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ) {
            return;
        }

        do_action( 'mylisting/admin/types/before-update', $post );

        // Fields TAB
        if ( ! empty( $_POST['case27_listing_type_fields'] ) ) {
            $decoded_fields = json_decode( stripslashes( $_POST['case27_listing_type_fields'] ), true );

            if ( json_last_error() === JSON_ERROR_NONE ) {
                // set field priorities to preserve order set in listing type editor through drag&drop.
                $updated_fields = [];
                foreach ( (array) $decoded_fields as $i => $field ) {
                    $field['priority'] = ($i + 1);
                    $updated_fields[ $field['slug'] ] = (array) $field;
                }
                update_post_meta( $post_id, 'case27_listing_type_fields', wp_slash( serialize( $updated_fields ) ) );
            }
        }

        // Single Page TAB
        if ( ! empty( $_POST['case27_listing_type_single_page_options'] ) ) {
            $options = (array) json_decode( stripslashes( $_POST['case27_listing_type_single_page_options'] ), true );
            if ( json_last_error() === JSON_ERROR_NONE ) {
                update_post_meta( $post_id, 'case27_listing_type_single_page_options', wp_slash( serialize( $options ) ) );
            }
        }

        // Result Template TAB
        if ( ! empty( $_POST['case27_listing_type_result_template'] ) ) {
            $result_template = (array) json_decode( stripslashes( $_POST['case27_listing_type_result_template'] ), true );
            if ( json_last_error() === JSON_ERROR_NONE ) {
                $cache_enabled = (bool) get_option( 'mylisting_cache_previews' );
                $old_result_template = get_post_meta( $post_id, 'case27_listing_type_result_template', true );
                if ( $cache_enabled && serialize( $result_template ) !== $old_result_template ) {
                    add_filter( 'redirect_post_location', function( $location ) {
                        return add_query_arg( [ 'regen_previews' => 1 ], $location );
                    } );
                }

                update_post_meta( $post_id, 'case27_listing_type_result_template', wp_slash( serialize( $result_template ) ) );
            }
        }

        // Search Forms TAB
        if ( ! empty( $_POST['case27_listing_type_search_page'] ) ) {
            $search_forms = (array) json_decode( stripslashes( $_POST['case27_listing_type_search_page'] ), true );
            if ( json_last_error() === JSON_ERROR_NONE ) {
                update_post_meta( $post_id, 'case27_listing_type_search_page', wp_slash( serialize( $search_forms ) ) );
            }
        }

        // Settings TAB
        if ( ! empty( $_POST['case27_listing_type_settings_page'] ) ) {
            $settings_page = (array) json_decode( stripslashes( $_POST['case27_listing_type_settings_page'] ), true );
            if ( json_last_error() === JSON_ERROR_NONE ) {
                update_post_meta( $post_id, 'case27_listing_type_settings_page', wp_slash( serialize( $settings_page ) ) );
            }
        }

        do_action( 'mylisting/admin/types/after-update', $post );
    }

    public function get_field_types() {
        if ( ! empty( $this->cache['field_types'] ) ) {
            return $this->cache['field_types'];
        }

        $fields = apply_filters( 'mylisting/listing-types/register-fields', [
            \MyListing\Src\Forms\Fields\Checkbox_Field::class,
            \MyListing\Src\Forms\Fields\Date_Field::class,
            \MyListing\Src\Forms\Fields\Email_Field::class,
            \MyListing\Src\Forms\Fields\File_Field::class,
            \MyListing\Src\Forms\Fields\Form_Heading_Field::class,
            \MyListing\Src\Forms\Fields\Links_Field::class,
            \MyListing\Src\Forms\Fields\Location_Field::class,
            \MyListing\Src\Forms\Fields\Multiselect_Field::class,
            \MyListing\Src\Forms\Fields\Number_Field::class,
            \MyListing\Src\Forms\Fields\Password_Field::class,
            \MyListing\Src\Forms\Fields\Radio_Field::class,
            \MyListing\Src\Forms\Fields\Related_Listing_Field::class,
            \MyListing\Src\Forms\Fields\Select_Field::class,
            \MyListing\Src\Forms\Fields\Select_Product_Field::class,
            \MyListing\Src\Forms\Fields\Select_Products_Field::class,
            \MyListing\Src\Forms\Fields\Term_Select_Field::class,
            \MyListing\Src\Forms\Fields\Text_Field::class,
            \MyListing\Src\Forms\Fields\Textarea_Field::class,
            \MyListing\Src\Forms\Fields\Texteditor_Field::class,
            \MyListing\Src\Forms\Fields\Url_Field::class,
            \MyListing\Src\Forms\Fields\Work_Hours_Field::class,
            \MyListing\Src\Forms\Fields\Wp_Editor_Field::class,
            \MyListing\Src\Forms\Fields\Recurring_Date_Field::class,
            \MyListing\Src\Forms\Fields\General_Repeater_Field::class,
        ] );

        foreach ( $fields as $field_class ) {
            if ( ! ( class_exists( $field_class ) && is_subclass_of( $field_class, \MyListing\Src\Forms\Fields\Base_Field::class ) ) ) {
                mlog()->warn( 'Listing type field: '.$field_class.' is invalid, skipping.' );
                continue;
            }

            $field = new $field_class;
            $this->cache['field_types'][ $field->props['type'] ] = $field;
        }

        return $this->cache['field_types'];
    }

    /**
     * Get list of field modifiers and modifier descriptions, to be
     * used with the `atwho` component in the listing type editor.
     *
     * @since 2.4.5
     */
    public function get_field_modifiers() {
        $modifiers = [];
        foreach ( $this->get_field_types() as $field ) {
            $modifiers[ $field->get_type() ] = [];
            if ( is_array( $field->modifiers ) && ! empty( $field->modifiers ) ) {
                $modifiers[ $field->get_type() ] = $field->modifiers;
            }

            $modifiers[ $field->get_type() ] = (object) apply_filters(
                sprintf( 'mylisting/%s-field/modifiers', $field->get_type() ),
                $modifiers[ $field->get_type() ]
            );
        }

        return $modifiers;
    }

    /**
     * Get list of available special keys to be shown in the
     * `atwho` component in the listing type editor.
     *
     * @since 2.4.5
     */
    public function get_special_keys() {
        return [
            ':id' => 'Listing ID',
            ':url' => 'Listing URL',
            ':authid' => 'Author ID',
            ':authname' => 'Author name',
            ':authlogin' => 'Author username',
            ':reviews-average' => 'Rating',
            ':reviews-count' => 'Review Count',
            ':reviews-mode' => 'Review mode',
            ':reviews-stars' => 'Star ratings',
            ':currentuserid' => 'Logged in user ID',
            ':currentusername' => 'Logged in user name',
            ':currentuserlogin' => 'Logged in user username',
            ':date' => 'Date posted (formatted)',
            ':rawdate' => 'Date posted',
            ':last-modified' => 'Date modified',
        ];
    }

    public function get_tab_types() {
        if ( ! empty( $this->cache['tab_types'] ) ) {
            return $this->cache['tab_types'];
        }

        $tabs = apply_filters( 'mylisting/listing-types/register-tabs', [
            \MyListing\Src\Listing_Types\Content_Tabs\Profile_Tab::class,
            \MyListing\Src\Listing_Types\Content_Tabs\Reviews_Tab::class,
            \MyListing\Src\Listing_Types\Content_Tabs\Reviews_Profile_Tab::class,
            \MyListing\Src\Listing_Types\Content_Tabs\Related_Listings_Tab::class,
            \MyListing\Src\Listing_Types\Content_Tabs\Store_Tab::class,
            \MyListing\Src\Listing_Types\Content_Tabs\Bookings_Tab::class,
        ] );

        foreach ( $tabs as $tab_class ) {
            if ( ! ( class_exists( $tab_class ) && is_subclass_of( $tab_class, \MyListing\Src\Listing_Types\Content_Tabs\Base_Tab::class ) ) ) {
                mlog()->warn( 'Listing type tab: '.$tab_class.' is invalid, skipping.' );
                continue;
            }

            $tab = new $tab_class;
            $this->cache['tab_types'][ $tab->type ] = $tab;
        }

        return $this->cache['tab_types'];
    }

    public function get_block_types() {
        if ( ! empty( $this->cache['block_types'] ) ) {
            return $this->cache['block_types'];
        }

        $blocks = apply_filters( 'mylisting/listing-types/register-blocks', [
            \MyListing\Src\Listing_Types\Content_Blocks\Text_Block::class,
            \MyListing\Src\Listing_Types\Content_Blocks\Gallery_Block::class,
            \MyListing\Src\Listing_Types\Content_Blocks\Categories_Block::class,
            \MyListing\Src\Listing_Types\Content_Blocks\Tags_Block::class,
            \MyListing\Src\Listing_Types\Content_Blocks\Terms_Block::class,
            \MyListing\Src\Listing_Types\Content_Blocks\Location_Block::class,
            \MyListing\Src\Listing_Types\Content_Blocks\Contact_Form_Block::class,
            \MyListing\Src\Listing_Types\Content_Blocks\Related_Listing_Block::class,
            \MyListing\Src\Listing_Types\Content_Blocks\Countdown_Block::class,
            \MyListing\Src\Listing_Types\Content_Blocks\Upcoming_Dates_Block::class,
            \MyListing\Src\Listing_Types\Content_Blocks\Table_Block::class,
            \MyListing\Src\Listing_Types\Content_Blocks\Details_Block::class,
            \MyListing\Src\Listing_Types\Content_Blocks\File_Block::class,
            \MyListing\Src\Listing_Types\Content_Blocks\Social_Networks_Block::class,
            \MyListing\Src\Listing_Types\Content_Blocks\Accordion_Block::class,
            \MyListing\Src\Listing_Types\Content_Blocks\Tabs_Block::class,
            \MyListing\Src\Listing_Types\Content_Blocks\Work_Hours_Block::class,
            \MyListing\Src\Listing_Types\Content_Blocks\Video_Block::class,
            \MyListing\Src\Listing_Types\Content_Blocks\Author_Block::class,
            \MyListing\Src\Listing_Types\Content_Blocks\Code_Block::class,
            \MyListing\Src\Listing_Types\Content_Blocks\Raw_Block::class,
            \MyListing\Src\Listing_Types\Content_Blocks\Google_Ad_Block::class,
            \MyListing\Src\Listing_Types\Content_Blocks\General_Repeater_Block::class,
        ] );

        foreach ( $blocks as $block_class ) {
            if ( ! ( class_exists( $block_class ) && is_subclass_of( $block_class, \MyListing\Src\Listing_Types\Content_Blocks\Base_Block::class ) ) ) {
                mlog()->warn( 'Listing type content block: '.$block_class.' is invalid, skipping.' );
                continue;
            }

            $block = new $block_class;
            $this->cache['block_types'][ $block->get_type() ] = $block;
        }

        return $this->cache['block_types'];
    }

    public function get_packages_dropdown() {
        $packages = (array) \MyListing\Src\Paid_Listings\Util::get_products( [ 'fields' => false ] );

        $items = [];
        foreach ( (array) $packages as $package ) {
            $items[ $package->ID ] = $package->post_title;
        }

        return $items;
    }

    /**
     * Print filter settings in search tab.
     *
     * @since 1.7.5
     */
    public function get_filter_types() {
        if ( ! empty( $this->cache['filter_types'] ) ) {
            return $this->cache['filter_types'];
        }

        $filters = apply_filters( 'mylisting/listing-types/register-filters', [
            \MyListing\Src\Listing_Types\Filters\Wp_Search::class,
            \MyListing\Src\Listing_Types\Filters\Text::class,
            \MyListing\Src\Listing_Types\Filters\Range::class,
            \MyListing\Src\Listing_Types\Filters\Location::class,
            \MyListing\Src\Listing_Types\Filters\Proximity::class,
            \MyListing\Src\Listing_Types\Filters\Dropdown::class,
            \MyListing\Src\Listing_Types\Filters\Date::class,
            \MyListing\Src\Listing_Types\Filters\Recurring_Date::class,
            \MyListing\Src\Listing_Types\Filters\Checkboxes::class,
            \MyListing\Src\Listing_Types\Filters\Related_Listing::class,
            \MyListing\Src\Listing_Types\Filters\Order::class,
            \MyListing\Src\Listing_Types\Filters\Heading_Ui::class,
            \MyListing\Src\Listing_Types\Filters\Open_Now::class,
            \MyListing\Src\Listing_Types\Filters\Group_Start::class,
            \MyListing\Src\Listing_Types\Filters\Group_End::class,
            \MyListing\Src\Listing_Types\Filters\Double_Checkbox::class,
        ] );

        foreach ( $filters as $filter_class ) {
            if ( ! ( class_exists( $filter_class ) && is_subclass_of( $filter_class, \MyListing\Src\Listing_Types\Filters\Base_Filter::class ) ) ) {
                mlog()->warn( 'Listing type filter: '.$filter_class.' is invalid, skipping.' );
                continue;
            }

            $filter = new $filter_class;
            $this->cache['filter_types'][ $filter->get_type() ] = $filter;
        }

        return $this->cache['filter_types'];
    }

    public function get_explore_tab_presets() {
        $presets = [
            'search-form' => [
                'type' => 'search-form',
                'label' => 'Filters',
                'icon' => 'mi filter_list',
                'orderby' => '',
                'order' => '',
                'hide_empty' => false,
            ],
            'categories' => [
                'type' => 'categories',
                'label' => 'Categories',
                'icon' => 'mi bookmark_border',
                'orderby' => 'count', 'order' => 'DESC',
                'hide_empty' => true,
            ],
            'regions' => [ 'type' => 'regions',
                'label' => 'Regions',
                'icon' => 'mi bookmark_border',
                'orderby' => 'count',
                'order' => 'DESC',
                'hide_empty' => true,
            ],
            'tags' => [ 'type' => 'tags',
                'label' => 'Tags',
                'icon' => 'mi bookmark_border',
                'orderby' => 'count',
                'order' => 'DESC',
                'hide_empty' => true,
            ],
        ];

        foreach ( mylisting_custom_taxonomies() as $key => $label ) {
            $presets[ $key ] = [ 'type' => $key,
                'label' => $label,
                'icon' => 'mi bookmark_border',
                'orderby' => 'count',
                'order' => 'DESC',
                'hide_empty' => true,
            ];
        }

        return $presets;
    }

    /**
     * Get all listing types present on the site, wrapped in
     * the custom Listing_Type class.
     *
     * @since 2.2
     */
    public function get_listing_types() {
        if ( ! empty( $this->cache['listing_types'] ) ) {
            return $this->cache['listing_types'];
        }

        $type_objs = get_posts( [
            'post_type' => 'case27_listing_type',
            'numberposts' => -1,
        ] );

        $this->cache['listing_types'] = array_map( function( $type_obj ) {
            return \MyListing\Src\Listing_Type::get( $type_obj );
        }, (array) $type_objs );

        return $this->cache['listing_types'];
    }

    public function get_quick_actions() {
        return require_once locate_template( 'includes/src/listing-types/quick-actions/quick-actions.php' );
    }

    public function regenerate_preview_cards_notice() {
        global $post, $current_screen;
        if ( empty( $_GET['regen_previews'] ) || ! $post || $current_screen->id !== 'case27_listing_type' ) {
            return;
        }

        $url = admin_url( 'admin.php?page=mylisting-options&active_tab=preview-cards&generate='.$post->post_name );
        ?>
        <div class="notice notice-warning is-dismissible">
            <p>You've made changes to the preview card template. To reflect changes on the site frontend, you must regenerate the cache files.</p>
            <p><a href="<?php echo esc_url( $url ) ?>" class="button button-primary">Regenerate cache</a></p>
        </div>
        <?php
    }
}
