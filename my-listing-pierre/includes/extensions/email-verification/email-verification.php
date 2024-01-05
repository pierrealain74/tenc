<?php

namespace MyListing\Ext\Email_Verification;

class Email_Verification {

    public static $is_new_version_saved = false;
    public static $is_new_version_activated = false;
    public static $plugin_settings = null;
    public static $is_plugin_settings_saved = false;
    public static $mlwev_user_login = null;
    public static $mlwev_display_name = null;
    public static $mlwev_user_email = null;
    public static $mlwev_user_id = null;
    public static $mlwev_myaccount_page_id;
    public static $is_mlwev_resend_link_clicked = false;

    public static function boot() {
        new self;
    }

    public function __construct() {
        // add_action( 'init', array( __CLASS__,  'mlwev_frontend_init' ), 0 );

        self::$mlwev_myaccount_page_id = get_option( 'woocommerce_myaccount_page_id' );

        if ( '' === self::$mlwev_myaccount_page_id ) {
            self::$mlwev_myaccount_page_id = get_option( 'page_on_front' );
        }

        add_filter( 'woocommerce_email_classes', array( __CLASS__, 'add_expedited_order_woocommerce_email' ) );

        //  admin Settings
        add_action( 'init', array( __CLASS__, 'check_plugin_settings' ), 10 );
        add_filter( 'mlwev_decode_html_content', array( __CLASS__, 'decode_html_content' ), 1 );
        add_filter( 'mlwev_the_content', array( __CLASS__, 'add_do_shortcode' ) );

        add_action( 'admin_menu',  [ __CLASS__,  'add_menus' ] );

        add_action( 'init', [ __CLASS__,  'save_tab_settings' ], 9 );

        add_filter( 'manage_users_columns', [ __CLASS__,  'add_column_users_list' ], 10, 1 );
        add_filter( 'manage_users_custom_column', [ __CLASS__,  'add_details_in_custom_users_list' ], 10, 3 );
    }

    public static function add_expedited_order_woocommerce_email( $email_classes ) {

        // include our custom email class
        require( 'class-wc-expedited-order-email.php' );

        // add the email class to the list of email classes that WooCommerce loads
        $email_classes['WC_Register_Confirm_Email'] = new \WC_Register_Confirm_Email();

        return $email_classes;
    }

    public static function mlwev_frontend_init() {
        // if ( class_exists( 'Woocommerce' ) ) {
            require_once get_stylesheet_directory() . '/includes/extensions/email-verification/wce-public.php';

            // require_once locate_template('includes/extensions/email-verification/wce-public.php');
        // }
    }

    public static function decode_html_content( $content ) {
        if ( empty( $content ) ) {
            return '';
        }
        $content = preg_replace( '#<script(.*?)>(.*?)</script>#is', '', $content );

        return html_entity_decode( stripslashes( $content ) );
    }

    public static function add_do_shortcode( $content ) {
        return do_shortcode( $content );
    }

    public static function add_menus() {
        add_theme_page( 'Email Verification', 'Email Verification', 'manage_options', 'ml-confirmation-email', [ __CLASS__,  'ml_email_page_content' ] );
    }

    public static function ml_email_page_content() {
        $admin_class_object = new self();
        $admin_class_object->create_settings_tabs();
    }

    public static function create_settings_tabs() {
        $my_plugin_tabs = self::get_default_plugin_settings_tabs();
        // print_r( $my_plugin_tabs );exit();
        echo '<div class="wrap">';
        echo '<h1>Woocommerce Email Verification</h1>';
        echo self::create_tabs( $my_plugin_tabs );
        echo '</div>';
    }

    /**
     * This function outputs the settings tabs in plugin settings screen.
     */
    public static function create_tabs( $tabs, $current = null ) {
        $plugin_default_options = self::get_default_plugin_options();
        if ( is_null( $current ) ) {
            if ( isset( $_GET['tab'] ) ) { // WPCS: input var ok, CSRF ok.
                $current = $_GET['tab']; // WPCS: input var ok, CSRF ok.
            } else {
                $current = 'mlwev-email-template';
            }
        }
        $content = '';
        $content .= '<h2 class="nav-tab-wrapper">';
        foreach ( $tabs as $location => $tabname ) {
            if ( $current === $location ) {
                $class           = ' nav-tab-active';
                $current_tabname = $tabname;
            } else {
                $class = '';
            }
            $content .= '<a class="nav-tab' . $class . '" href="?page=ml-confirmation-email&tab=' . $location . '">' . $tabname . '</a>';
        }
        $content .= '</h2>';

        switch ( $current ) {
            default:
                $submit_button_text = __( 'Save Changes', 'my-listing' );
                break;
        }

        $tab_options = self::get_tab_options( $current );

        ob_start();
        ?>
        <div id="poststuff">
            <div class="metabox-holder columns-2" id="post-content">
                <div id="post-body-content">
                    <div id="normal-sortables" class="meta-box-sortables ui-sortable mlwev_content">
                        <div id="dashboard_right_now" class="postbox">
                            <h2 class="hndle ui-sortable-handle"><span><?php echo $current_tabname; ?></span></h2>
                            <div class="inside">
                                <div class="main">
                                    <form method="post" class="mlwev-forms">
                                        <?php require_once get_stylesheet_directory() . '/includes/extensions/email-verification/'. $current .'.php'; ?>
                                        <input type="hidden" name="mlwev_form_type" value="<?php echo $current; ?>">
                                        <input type="hidden" name="_nonce" value="<?php echo wp_create_nonce( 'mlwev_form_submit' ); ?>">
                                        <?php
                                        submit_button( $submit_button_text );
                                        ?>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php
        $content .= ob_get_clean();

        return $content;
    }

    /**
     * This function saves the settings from plugin settings screen in options table.
     */
    public static function save_tab_settings() {
        if ( isset( $_POST['mlwev_form_type'] ) ) { // WPCS: input var ok, CSRF ok.

            check_admin_referer( 'mlwev_form_submit', '_nonce' );
            switch ( $_POST['mlwev_form_type'] ) { // WPCS: input var ok, CSRF ok.
                case 'mlwev-email-template':
                    self::maybe_save_tabs_settings();

                    self::$is_plugin_settings_saved = true;
                    break;

                case 'mlwev-messages':
                    self::maybe_save_tabs_settings();

                    self::$is_plugin_settings_saved = true;
                    break;
            }
        }
    }

    public static function maybe_save_tabs_settings() {
        $tab_slug = $_POST['mlwev_form_type']; // WPCS: input var ok, CSRF ok.

        $settings_array = $_POST; // WPCS: input var ok, CSRF ok.
        if ( isset( $settings_array['mlwev_email_header'] ) ) {
            $settings_array['mlwev_email_header'] = apply_filters( 'mlwev_decode_html_content', wp_kses_post( $settings_array['mlwev_email_header'] ) );
        }

        if ( is_array( $settings_array ) && count( $settings_array ) > 0 ) {
            $settings_array_temp = array();
            foreach ( $settings_array as $key1 => $value1 ) {
                $settings_array_temp[ $key1 ] = apply_filters( 'mlwev_decode_html_content', wp_kses_post( $value1 ) );
            }
            $settings_array = $settings_array_temp;
        }
        update_option( $tab_slug, $settings_array );
    }

    /**
     * This function shows admin notices on settings save.
     */
    public static function show_save_admin_notice() {
        if ( self::$is_plugin_settings_saved ) {
            ?>
            <div class="updated fade no-margin-left"><p><b><?php _e( 'Changes Saved', 'my-listing' ); ?>
                </p></b>
            </div>
            <?php
        }
    }

    public static function check_plugin_settings() {
        $plugin_tabs          = self::get_default_plugin_settings_tabs();
        $plugin_tabs_settings = array();
        foreach ( $plugin_tabs as $key1 => $value1 ) {
            $is_tab_have_settings = self::get_tab_options( $key1 );
            if ( is_array( $is_tab_have_settings ) && count( $is_tab_have_settings ) > 0 ) {
                $plugin_tabs_settings[ $key1 ] = $is_tab_have_settings;
            }
        }

        self::$plugin_settings = $plugin_tabs_settings;

        self::$is_new_version_activated = self::is_new_version_activated();
    }

    public static function get_default_plugin_settings_tabs() {
        $my_plugin_tabs = array(
            'mlwev-email-template'    => __( 'Email Template', 'my-listing' ),
            'mlwev-messages'          => __( 'Verification Messages', 'my-listing' ),
        );

        return $my_plugin_tabs;
    }

    /*
     * This function returns all the default options of the plugin.
     */

    public static function get_tab_options( $tab_slug ) {
        $tab_options            = array();
        $plugin_default_options = self::get_default_plugin_options();

        if ( isset( $plugin_default_options[ $tab_slug ] ) ) {
            $default_options    = $plugin_default_options[ $tab_slug ];
            $default_options_db = self::get_plugin_saved_settings( $tab_slug );
            if ( '' === $default_options_db ) {
                $tab_options = $default_options;
            } else {
                foreach ( $default_options as $key1 => $value1 ) {
                    if ( isset( $default_options_db[ $key1 ] ) && '' !== $default_options_db[ $key1 ] ) {
                        $tab_options[ $key1 ] = $default_options_db[ $key1 ];
                    } else {
                        $tab_options[ $key1 ] = $value1;
                    }
                }
            }
        }

        return $tab_options;
    }

    /*
     * This function returns those fields which are bypassed in wpml for conversion in other languages.
     */

    public static function get_default_plugin_options() {
        
        $default_options = array(
            'mlwev-email-template'   => array(
                'mlwev_verification_method' => 2,
                'mlwev_verification_type'   => 2,
                'mlwev_email_subject'       => __( 'Account Verification ({{mlwev_display_name}})', 'my-listing' ),
                'mlwev_email_heading'       => __( 'Please Verify Your Email Account ({{mlwev_display_name}})', 'my-listing' ),
                'mlwev_email_body'          => __( 'Please Verify your Email Account by clicking on the following link. {{wcemailverificationcode}}', 'my-listing' )
            ),
            'mlwev-messages'         => array(
                'mlwev_email_success_message'            => __( 'Your Email is verified!', 'my-listing' ),
                'mlwev_email_registration_message'       => __( 'We sent you a verification email. Check and verify your account. {{mlwev_resend_link}}', 'my-listing' ),
                'mlwev_email_resend_confirmation'        => __( 'Resend Confirmation Email', 'my-listing' ),
                'mlwev_email_verification_already_done'  => __( 'Your Email is already verified', 'my-listing' ),
                'mlwev_email_new_verification_link'      => __( 'A new verification link is sent. Check email. {{mlwev_resend_link}}', 'my-listing' ),
                'mlwev_email_new_verification_link_text' => __( 'Click here to verify', 'my-listing' ),
            ),

            'mlwev-confirm-email-template'   => array(
                'mlwev_confirm_email_subject'       => __( 'Account Verification ({{mlwev_display_name}})', 'my-listing' ),
                'mlwev_confirm_email_heading'       => __( 'Please Verify Your Email Account ({{mlwev_display_name}})', 'my-listing' ),
                'mlwev_confirm_email_body'          => __( 'Please Verify your Email Account by clicking on the following link. {{wcemailverificationcode}}', 'my-listing' )
            ),
        );

        return $default_options;
    }

    /*
     * This function returns all the tabs of the plugin.
     */

    public static function get_plugin_saved_settings( $option_key ) {
        return get_option( $option_key );
    }

    /*
     * This function returns the values of all the fields of a single tab.
     * It return default values if user has not saved the tab.
     */

    public static function is_new_version_activated() {
        return get_option( 'new_plugin_activated' );
    }

    public static function is_new_version_saved() {
        return get_option( 'is_new_version_saved', '0' );
    }

    public static function update_is_new_version() {
        update_option( 'is_new_version_saved', '1', false );
    }

    /*
     * Mergetag callback for showing verification link.
     */

    protected static function mlwev_user_login() {
        return self::$mlwev_user_login;
    }

    /*
     * Mergetag callback for showing verification link text.
     */

    protected static function mlwev_display_name() {
        return self::$mlwev_display_name;
    }

    /*
     * Mergetag callback for showing verification link.
     */

    protected static function mlwev_user_email() {
        return self::$mlwev_user_email;
    }

    public static function get_setting_value( $tab_slug, $field_key ) {
        return self::$plugin_settings[ $tab_slug ][ $field_key ];
    }

    public static function code_mail_sender( $email ) {
        $result                      = false;
        $email_subject               = self::maybe_parse_merge_tags( self::get_setting_value( 'mlwev-email-template', 'mlwev_email_subject' ) );
        $email_heading               = self::maybe_parse_merge_tags( self::get_setting_value( 'mlwev-email-template', 'mlwev_email_heading' ) );
        $email_body                  = self::maybe_parse_merge_tags( self::get_setting_value( 'mlwev-email-template', 'mlwev_email_body' ) );
        $email_body_temp             = $email_body;

        $email_body = apply_filters( 'mlwev_the_content', $email_body );
        $mailer = WC()->mailer();
        ob_start();
        $mailer->email_header( $email_heading );
        echo $email_body;
        $mailer->email_footer();
        $email_body            = ob_get_clean();
        $email_abstract_object = new \WC_Email();
        $email_body            = apply_filters( 'woocommerce_mail_content', $email_abstract_object->style_inline( wptexturize( $email_body ) ) );

        $email_body = apply_filters( 'mlwev_decode_html_content', $email_body );

        $filtered_values = apply_filters( 'mlwev_modify_before_email', array(
            'email'         => $email,
            'email_subject' => $email_subject,
            'email_body'    => $email_body,
        ) );
        extract( $filtered_values );

        $mailer = WC()->mailer();
        $result = $mailer->send( $email, $email_subject, $email_body );

        do_action( 'mlwev_trigger_after_email', $email );

        return $result;
    }

    public static function maybe_parse_merge_tags( $content = '' ) {
        $get_all      = self::get_all_tags();
        $get_all_tags = wp_list_pluck( $get_all, 'tag' );

        //iterating over all the merge tags
        if ( $get_all_tags && is_array( $get_all_tags ) && count( $get_all_tags ) > 0 ) {
            foreach ( $get_all_tags as $tag ) {
                $matches = array();
                $re      = sprintf( '/\{{%s(.*?)\}}/', $tag );
                $str     = $content;

                //trying to find match w.r.t current tag
                preg_match_all( $re, $str, $matches );

                //if match found
                if ( $matches && is_array( $matches ) && count( $matches ) > 0 ) {

                    //iterate over the found matches
                    foreach ( $matches[0] as $exact_match ) {

                        //preserve old match
                        $old_match        = $exact_match;
                        $single           = str_replace( '{{', '', $old_match );
                        $single           = str_replace( '}}', '', $single );
                        $get_parsed_value = call_user_func( array( __CLASS__, $single ) );
                        $content          = str_replace( $old_match, $get_parsed_value, $content );
                    }
                }
            }
        }

        return $content;
    }

    /*
     * Mergetag callback for showing sitename.
     */

    public static function get_all_tags() {
        $tags = array(
            array(
                'name' => __( 'User login', 'my-listing' ),
                'tag'  => 'mlwev_user_login',
            ),
            array(
                'name' => __( 'User display name', 'my-listing' ),
                'tag'  => 'mlwev_display_name',
            ),
            array(
                'name' => __( 'User email', 'my-listing' ),
                'tag'  => 'mlwev_user_email',
            ),
            array(
                'name' => __( 'Verification link', 'my-listing' ),
                'tag'  => 'mlwev_user_verification_link',
            ),
            array(
                'name' => __( 'Resend link', 'my-listing' ),
                'tag'  => 'mlwev_resend_link',
            ),
            array(
                'name' => __( 'Verification link', 'my-listing' ),
                'tag'  => 'wcemailverificationcode',
            ),
            array(
                'name' => __( 'Site Myaccount Page', 'my-listing' ),
                'tag'  => 'mlwev_site_login_link',
            ),
            array(
                'name' => __( 'Website Name', 'my-listing' ),
                'tag'  => 'sitename',
            ),
            array(
                'name' => __( 'Website Name with link', 'my-listing' ),
                'tag'  => 'sitename_with_link',
            ),
            array(
                'name' => __( 'Shows the verification link text', 'my-listing' ),
                'tag'  => 'mlwev_verification_link_text',
            ),
        );

        return $tags;
    }

    /*
     * Mergetag callback for showing resend verification link.
     */

    protected static function mlwev_user_verification_link() {
        $secret      = get_user_meta( self::$mlwev_user_id, 'wcemailverifiedcode', true );
        $create_link = $secret . '@' . self::$mlwev_user_id;
        $hyperlink   = add_query_arg( array(
            'woo_confirmation_verify' => base64_encode( $create_link ),
        ), get_the_permalink( self::$mlwev_myaccount_page_id ) );
        $link_text   = self::maybe_parse_merge_tags( self::get_setting_value( 'mlwev-messages', 'mlwev_email_new_verification_link_text' ) );
        $link        = '<a href="' . $hyperlink . '">' . $link_text . '</a>';

        return $link;
    }

    /*
     * This function actually sends the verification email.
     */

    protected static function mlwev_verification_link_text() {
        $secret      = get_user_meta( self::$mlwev_user_id, 'wcemailverifiedcode', true );
        $create_link = $secret . '@' . self::$mlwev_user_id;
        $hyperlink   = add_query_arg( array(
            'woo_confirmation_verify' => base64_encode( $create_link ),
        ), get_the_permalink( self::$mlwev_myaccount_page_id ) );
        $link_text   = '<span style="padding: 2px 10px;font-style: italic;font-size: 12px;display: inline-block;width: 100%;">' . $hyperlink . '</span>';

        return $link_text;
    }

    protected static function wcemailverificationcode() {
        $secret      = get_user_meta( self::$mlwev_user_id, 'wcemailverifiedcode', true );
        $create_link = $secret . '@' . self::$mlwev_user_id;
        $hyperlink   = add_query_arg( array(
            'woo_confirmation_verify' => base64_encode( $create_link ),
        ), get_the_permalink( self::$mlwev_myaccount_page_id ) );
        $link_text   = self::maybe_parse_merge_tags( self::get_setting_value( 'mlwev-messages', 'mlwev_email_new_verification_link_text' ) );
        $link        = '<a href="' . $hyperlink . '">' . $link_text . '</a>';

        return $link;
    }

    protected static function mlwev_resend_link() {
        $link                        = add_query_arg( array(
            'wc_confirmation_resend' => base64_encode( self::$mlwev_user_id ),
        ), get_the_permalink( self::$mlwev_myaccount_page_id ) );
        $resend_confirmation_message = self::get_setting_value( 'mlwev-messages', 'mlwev_email_resend_confirmation' );
        $mlwev_resend_link          = '<a href="' . $link . '">' . $resend_confirmation_message . '</a>';

        return $mlwev_resend_link;
    }

    public static function add_column_users_list( $column ) {
        $column['mlwev_verified'] = __( 'Email verification', 'my-listing' );
        return $column;
    }
    
    /**
     * This function adds custom values to custom columns in user listing screen in wp-admin area.
     */ 
    public static function add_details_in_custom_users_list( $val, $column_name, $user_id ) {
        
        wp_enqueue_script( 'jquery-blockui' );
        
        $user_role = get_userdata( $user_id );
        $verified  = get_user_meta( $user_id, 'wcemailverified', true );
        
        if ( 'mlwev_verified' === $column_name ) {
            if ( ! self::is_admin_user( $user_id ) ) {
                    
                $verified_btn_css   = ( 'true' == $verified ) ? 'display:none' : '';
                $unverified_btn_css = ( 'true' != $verified ) ? 'display:none' : '';
                
                $html = '<span style="' . $unverified_btn_css . '" class="dashicons dashicons-yes cev_5 mlwev_verified_admin_user_action" title="Verified"></span>';
                $html .= '<span style="' . $verified_btn_css . '" class="dashicons dashicons-no no-border cev_unverified_admin_user_action cev_5" title="Unverify"></span>';                    
                return $html;
            }
            return '-'; 
        }

        return $val;
    }

    public static function is_admin_user( $user_id ) {
        
        $user = get_user_by( 'id', $user_id );
        
        if ( !$user ) {
            return false;
        }
        
        $roles = $user->roles;
        
        if ( in_array( 'administrator', (array) $roles ) ) {
            return true;    
        }
        return false;
    }
}