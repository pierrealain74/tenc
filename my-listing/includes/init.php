<?php

mylisting()->boot(
	MyListing\Src\Multiple_Locations\Multiple_Locations::class,
    MyListing\Src\Work_Hours\Work_Hours::class,
	MyListing\Src\Theme_Options\Theme_Options::class,
	MyListing\Controllers\User_Roles_Controller::class,
	MyListing\Controllers\Account_Details_Form_Controller::class,
	MyListing\Controllers\Register_Form_Controller::class,
	MyListing\Controllers\Dashboard_Listings_Controller::class,
	MyListing\Controllers\Promotions\Promotions_Controller::class,
	MyListing\Controllers\Promotions\Promotions_Admin_Controller::class,
	MyListing\Controllers\Promotions\Promotions_Dashboard_Controller::class,
	MyListing\Controllers\Promotions\Promotions_Order_Controller::class,
	MyListing\Controllers\Maps\Maps_Controller::class,
	MyListing\Controllers\Maps\Google_Maps_Controller::class,
	MyListing\Controllers\Maps\Mapbox_Controller::class,
	MyListing\Controllers\Wp_All_Import_Controller::class,
	MyListing\Ajax::class,
	MyListing\Ext\Advanced_Custom_Fields\Advanced_Custom_Fields::class,
	MyListing\Src\Notifications\Notifications::class,
	MyListing\Post_Types::class,
	MyListing\Src\Forms\Forms::class,
	MyListing\Src\Endpoints\Endpoints::class,
	MyListing\Src\Explore::class,
	MyListing\Src\Queries\Query::class,
	MyListing\Assets::class,
	MyListing\Ext\Buddypress\Buddypress::class,
	MyListing\Src\Admin\Admin::class,
	MyListing\Ext\Social_Login\Social_Login::class,
	MyListing\Src\Permalinks::class,
	MyListing\Elementor\Elementor::class,
	MyListing\Ext\Contact_Form_7\Contact_Form_7::class,
	MyListing\Src\Related_Listings\Related_Listings::class,
	MyListing\Ext\Visits\Visits::class,
	MyListing\Ext\Reviews\Reviews::class,
	MyListing\Src\Bookmarks::class,
	MyListing\Ext\Simple_Products\Simple_Products::class,
	MyListing\Src\Recurring_Dates\Recurring_Dates::class,
    MyListing\Src\Track_Button::class,
    MyListing\Src\Display_Contact_Info::class
);

/* @todo: refactor */
mylisting()->register( [
	'messages' => MyListing\Ext\Messages\Messages::instance(),
	'shortcodes' => MyListing\Shortcodes::instance(),
	'custom_taxonomies' => MyListing\Ext\Custom_Taxonomies\Custom_Taxonomies::instance(),
	'type_editor' => MyListing\Src\Listing_Types\Editor::instance(),
	'typography' => MyListing\Ext\Typography\Typography::instance(),
	'sharer' => MyListing\Ext\Sharer\Sharer::instance(),
	'stats' => MyListing\Ext\Stats\Stats::instance(),
    'icalendar' => MyListing\Ext\ical\iCalendar::instance(),
	'export_data' => MyListing\Ext\Data_Exporters\Exporter_Data::instance()
] );

MyListing\Ext\WooCommerce\WooCommerce::instance();
MyListing\Src\Paid_Listings\Paid_Listings::instance();

/**
 * Fired after MyListing theme extensions have all been loaded.
 *
 * @since 2.0
 */
do_action( 'mylisting/init' );

/*
 * Configure theme textdomain, supported features, nav menus, etc.
 */
add_action( 'after_setup_theme', function() {

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	// Add support for the WooCommerce plugin.
	add_theme_support( 'woocommerce' );

	// Let WordPress manage the document title.
	add_theme_support( 'title-tag' );

	// Set content width
	if ( ! isset( $content_width ) ) $content_width = 550;

	// Enable support for Post Thumbnails on posts and pages.
	add_theme_support( 'post-thumbnails' );

	// Remove WP Admin Bar inline styles.
	add_theme_support( 'admin-bar', [ 'callback' => '__return_false' ] );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus([
		'primary' 	  		  => esc_html__( 'Primary Menu', 'my-listing' ),
		'footer'	  		  => esc_html__( 'Footer Menu', 'my-listing' ),
		'mylisting-user-menu' => esc_html__( 'Woocommerce Menu', 'my-listing' )
	]);

	// Allow shortcodes in menu item labels.
	add_filter( 'wp_nav_menu_items', 'do_shortcode' );

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', [
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
	]);

	add_theme_support( 'custom-background', [
		'default-color' => '#fafafa',
	]);

	// Add support for "Header, Footer & Blocks for Elementor" plugin.
	add_theme_support( 'header-footer-elementor' );
});

add_action( 'after_switch_theme', function() {
	flush_rewrite_rules();
} );

/*
 * Register theme sidebars.
 */
add_action( 'init', function() {
	register_sidebar( [
		'name'          => __( 'Footer', 'my-listing' ),
		'id'            => 'footer',
		'before_widget' => '<div>',
		'after_widget'  => '</div>',
		'before_title'  => '<div class="c_widget_title"><h5>',
		'after_title'   => '</h5></div>',
	] );

	register_sidebar( [
		'name'          => __( 'Sidebar', 'my-listing' ),
		'id'            => 'sidebar',
		'before_widget' => '<div class="element c_widget woocommerce">',
		'after_widget'  => '</div>',
		'before_title'  => '<div class="pf-head"><div class="title-style-1"><h5>',
		'after_title'   => '</h5></div></div>',
	] );

	register_sidebar( [
		'name'          => __( 'Shop Page', 'my-listing' ),
		'id'            => 'shop-page',
		'before_widget' => '<div class="element c_widget woocommerce">',
		'after_widget'  => '</div>',
		'before_title'  => '<div class="pf-head"><div class="title-style-1"><h5>',
		'after_title'   => '</h5></div></div>',
	] );

	register_widget( '\MyListing\Widgets\Latest_Posts' );
	register_widget( '\MyListing\Widgets\Contact_Form' );
} );

add_action( 'dynamic_sidebar_params', function( $params ) {
	if ( $params[0]['id'] === 'footer' ) {
		$rows_d = c27()->get_setting( 'footer_widgets_per_row_d' );
		$e_rows_d = \MyListing\get_page_setting('c27_footer_widgets_per_row_d');
		$rows_t = c27()->get_setting( 'footer_widgets_per_row_t' );
		$e_rows_t = \MyListing\get_page_setting('c27_footer_widgets_per_row_t');
		$rows_m = c27()->get_setting( 'footer_widgets_per_row_m' );
		$e_rows_m = \MyListing\get_page_setting('c27_footer_widgets_per_row_m');
		$num_rows_d = ! empty( $e_rows_d ) ? $e_rows_d : ( ! empty( $rows_d ) ? $rows_d : 'col-lg-4' );
		$num_rows_t = ! empty( $e_rows_t ) ? $e_rows_t : ( ! empty( $rows_t ) ? $rows_t : 'col-sm-6' );
		$num_rows_m = ! empty( $e_rows_m ) ? $e_rows_m : ( ! empty( $rows_m ) ? $rows_m : 'col-xs-12' );
		$params[0]['before_widget'] = '<div class="'.$num_rows_d.' '.$num_rows_t.' '.$num_rows_m.' c_widget woocommerce">';
	}

	return $params;
} );

/**
 * Insert required code in site footer through get_footer hook, so it will
 * be added when using custom footer templates which completely override the theme footer.
 *
 * @since 1.6.6
 */
add_action( 'mylisting/get-footer', function() {
    c27()->get_partial( 'quick-view-modal' );
    c27()->get_partial( 'comparison-view-modal' );
    c27()->get_partial( 'shopping-cart-modal' );
    c27()->get_partial( 'photoswipe-template' );
    c27()->get_partial( 'dialog-template' );

    // 'Back to Top' button.
    if ( c27()->get_setting( 'footer_show_back_to_top_button', false ) ): ?>
        <a href="#" class="back-to-top">
            <i class="mi keyboard_arrow_up"></i>
        </a>
    <?php endif;

    printf(
    	'<style type="text/css">%s</style>',
    	$GLOBALS['case27_custom_styles']
    );

    if ( c27()->get_setting('custom_code') ) {
        echo c27()->get_setting('custom_code');
    }
}, 1 );

add_filter( 'comment_form_defaults', function( $fields ) {
    $fields['must_log_in'] = '<p class="must-log-in">' . sprintf(
        __( 'You must be <a href="%s">logged in</a> to post a comment.', 'my-listing' ),
        esc_url( \MyListing\get_login_url() )
    ) . '</p>';

    return $fields;
} );

add_filter( 'comment_reply_link', function( $link, $args, $comment, $post ) {
    if ( class_exists( 'WooCommerce' ) && get_option( 'comment_registration' ) && ! is_user_logged_in() ) {
        $link = sprintf( '<a rel="nofollow" class="comment-reply-login" href="%s">%s</a>',
            esc_url( \MyListing\get_login_url() ),
            $args['login_text']
        );
    }

    return $link;
}, 30, 4 );

/**
 * Include attachment guid in `wp.media.frames.file_frame`. Necessary
 * to add CDN and media offloading support for listing file fields.
 *
 * @since 2.4.5
 */
add_filter( 'wp_prepare_attachment_for_js', function( $response, $attachment, $meta ) {
    $response['guid'] = get_the_guid( $attachment->ID );
    $response['encoded_guid'] = 'b64:'.base64_encode( $response['guid'] );
    return $response;
}, 100, 3 );

/**
 * Add a way to link to the user profile from a WordPress menu item.
 *
 * @since 3.0
 */
add_filter( 'wp_nav_menu_objects', function( $menu_items ) {
	$username = is_user_logged_in() ? wp_get_current_user()->user_login : '';
	foreach ( $menu_items as $menu_item ) {
		$menu_item->url = str_replace( '#username#', $username, $menu_item->url );
	}

	return $menu_items;
} );

/**
 * Register theme required plugins using TGM Plugin Activation library.
 *
 * @since 1.0
 */
add_action( 'tgmpa_register', function() {
    $plugins = [
        [
            'name' => __( 'Elementor', 'my-listing' ),
            'slug' => 'elementor',

            // If false, the plugin is only 'recommended' instead of required.
            'required' => true,

            // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
            'force_activation' => true,
        ],
        [
            'name' => __( 'WooCommerce', 'my-listing' ),
            'slug' => 'woocommerce',
            'required' => true,
            'force_activation' => true,
        ],
        [
            'name' => __( 'Contact Form 7', 'my-listing' ),
            'slug' => 'contact-form-7',
            'required' => false,
            'force_activation' => false,
        ],
    ];

    // Array of configuration settings.
    $config = [
        'id' => 'case27',
        'default_path' => c27()->template_path('includes/plugins/'),
        'dismissable' => true,
        'is_automatic' => true,
    ];

    tgmpa( $plugins, $config );
} );

add_action( 'pre_get_posts', function( $query ) {
    if ( ! is_author() || ! $query->is_main_query() || is_admin() ) {
        return;
    }

    $query->set( 'post_type', 'job_listing' );
} );

add_filter( 'query_vars', function( $vars ) {
	$vars[] = 'listing_type';
	return $vars;
} );

add_filter( 'get_the_archive_title', function( $title ) {
    if ( ! class_exists('WooCommerce') ) {
    	return $title;
    }

    if ( is_woocommerce() ) {
        $title = woocommerce_page_title( false );
    } elseif ( is_cart() || is_checkout() || is_account_page() || is_page() ) {
        $title = get_the_title();
    } elseif ( is_home() ) {
        $title = apply_filters( 'the_title', get_the_title( get_option( 'page_for_posts' ) ), get_option( 'page_for_posts' ) );
    }

    return $title;
} );

add_filter( 'case27_featured_service_content', function( $content ) {
    if ( ! trim( $content ) ) {
        return $content;
    }

    $dom = new \DOMDocument;
    $dom->loadHTML( mb_convert_encoding( $content, 'HTML-ENTITIES', 'UTF-8' ) );

    foreach ( ['h1', 'h2', 'h3'] as $tagSelector) {
        foreach ( $dom->getElementsByTagName( $tagSelector ) as $tag ) {
            $tag->setAttribute( 'class', $tag->getAttribute( 'class' ) . ' case27-primary-text' );
        }
    }

    return $dom->saveHTML();
} );

add_filter( 'option_category_base', function( $base ) {
    if ( ! $base || $base == 'category' ) {
        return 'post-category';
    }

    return $base;
} );

add_filter( 'option_tag_base', function( $base ) {
    if ( ! $base || $base == 'tag' ) {
        return 'post-tag';
    }

    return $base;
} );

add_filter( 'pre_option_job_category_base', function( $base ) {
    if ( ! $base || $base == 'listing-category' || $base == 'job-category' ) {
        return 'category';
    }

    return $base;
} );

add_filter( 'body_class', function( $classes ) {
    $classes[] = 'my-listing';

    if ( is_singular( 'job_listing' ) ) {
        global $post;
        $listing = \MyListing\Src\Listing::get( $post );

        if ( $post->_case27_listing_type ) {
            $classes[] = 'single-listing';
            $classes[] = "type-{$post->_case27_listing_type}";
        }

        if ( $post->_package_id ) {
            $classes[] = "package-{$post->_package_id}";
        }

        if ( $listing->is_verified() ) {
            $classes[] = 'c27-verified';
        }

        if ( $listing->type ) {
            $layout = $listing->type->get_layout();
            $classes[] = esc_attr( sprintf( 'cover-style-%s', $layout['cover']['type'] ) );
        }
    }

    return $classes;
} );

add_filter( 'admin_menu', function() {
    $user = wp_get_current_user();
    if ( ! in_array( 'administrator', $user->roles ) ) {
        remove_menu_page( 'ai1wm_export' );
        remove_submenu_page( 'ai1wm_export', 'ai1wm_import' );
        remove_submenu_page( 'ai1wm_export', 'ai1wm_backups' );
    }
} );

/**
 * Fix menu items not being marked active when
 * using a custom WooCommerce user menu.
 *
 * @since 2.6.7
 */
add_filter( 'nav_menu_css_class', function( $classes, $menu_item, $args ) {
	if ( $args->theme_location !== 'mylisting-user-menu' ) {
		return $classes;
	}

	$current_endpoint = untrailingslashit( parse_url( $_SERVER['REQUEST_URI'] )['path'] );
	$endpoint = untrailingslashit( parse_url( $menu_item->url )['path'] );

	if ( $current_endpoint === $endpoint ) {
		$classes[] = 'current-menu-item';
	}

	return $classes;
}, 50, 3 );

/**
 * FIX: "_user_package_id" not present as a custom field
 * when performing a new export using WP All Export.
 *
 * @since 2.6.7
 */
add_filter( 'wp_all_export_available_data', function( $data ) {
	$data['existing_meta_keys'][] = '_user_package_id';
	return $data;
} );
