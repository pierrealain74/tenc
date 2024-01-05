<?php

namespace MyListing\Ext\Social_Login\Networks;

class Google extends Network {

    protected
        $request,
        $userdata,
        $app_id;

    public
        $name     = 'google',
        $user_key = 'mylisting_google_account_id',
        $custom_fields = ['mylisting_google_account_name', 'mylisting_google_account_picture'];

    /**
     * Include required scripts and setup settings for Google login.
     *
     * @since 1.6.3
     */
    public function __construct() {
        if ( ! $this->is_enabled() ) {
            return false;
        }

        $this->app_id = c27()->get_setting( 'social_login_google_client_id' );

        add_action( 'mylisting/after-auth-forms', function() {
            wp_enqueue_script(
                'google-platform-js',
                'https://apis.google.com/js/platform.js?onload=cts_google_login',
                ['jquery'], null, true
            );
        }, 50 );

        add_action( 'woocommerce_after_edit_account_form', function() {
            wp_enqueue_script(
                'google-platform-js',
                'https://apis.google.com/js/platform.js?onload=cts_google_login',
                ['jquery'], null, true
            );
        }, 50 );

        add_action( 'wp_head', function() { ?>
            <meta name="google-signin-client_id" content="<?php echo esc_attr( $this->app_id ) ?>">
        <?php } );
    }

    /**
     * Check if Sign-In with Google is enabled,
     * and a client ID has been provided.
     *
     * @since 1.6.3
     * @return bool
     */
    public function is_enabled() {
        return c27()->get_setting( 'social_login_google_enabled' ) && c27()->get_setting( 'social_login_google_client_id' );
    }

    /**
     * Display "Login with Google" button in auth forms.
     *
     * @since 1.6.3
     */
    public function display_button() { ?>
        <div class="buttons button-5 cts-google-signin"><i class="fa fa-google"></i> <?php _ex( 'Sign in with Google', 'Login with Google button', 'my-listing' ) ?></div>
    <?php }

    /**
     * Display connected account info in user account edit page.
     *
     * @since 1.6.6
     */
    public function display_connected_account() {
        $account_id = get_user_meta( get_current_user_id(), $this->user_key, true );
        $account_picture = $this->get_user_picture();
        $is_connected = ! empty( $account_id );
        ?>
        <div class="cts-connected-account cts-account-google cts-is-<?php echo $is_connected ? 'connected' : 'disconnected' ?>">
            <div class="cts-account-header">
                <i class="fa fa-google"></i>
                <?php _ex( 'Google', 'Connect to Google button', 'my-listing' ) ?>
            </div>
            <div class="cts-account-actions">
                <?php if ( $is_connected ): ?>

                    <div class="cts-account-details">
                        <?php if ( $account_picture ): ?>
                            <span style="background-image: url('<?php echo esc_url( $account_picture ) ?>');" class="cts-account-picture"></span>
                        <?php endif ?>
                        <?php echo $account_id ?>
                    </div>


                    <a href="#" class="cts-google-signin buttons button-5 full-width" data-process="disconnect"><?php _ex( 'Disconnect', 'Social login connected accounts', 'my-listing' ) ?></a>
                <?php else: ?>

                    <a href="#" class="cts-google-signin buttons button-5 full-width" data-process="connect"><?php _ex( 'Connect', 'Social login connected accounts', 'my-listing' ) ?></a>
                <?php endif ?>
            </div>
        </div>
    <?php }

    /**
     * Return the user picture url.
     *
     * @since 1.6.6
     */
    public function get_user_picture( $user_id = null ) {
        if ( ! $user_id ) {
            $user_id = get_current_user_id();
        }

        return get_user_meta( $user_id, 'mylisting_google_account_picture', true );
    }

    /**
     * Get user data from their Google profile.
     *
     * @since 1.6.3
     */
    public function get_user_data() {
        if ( empty( $this->request['token'] ) ) {
            return false;
        }

        $response = wp_remote_get( sprintf( 'https://www.googleapis.com/oauth2/v3/tokeninfo?id_token=%s', $this->request['token'] ) );
        $data = wp_remote_retrieve_body( $response );

        if ( is_wp_error( $data ) ) {
            return false;
        }

        $this->transform_userdata( json_decode( $data ) );
    }

    /**
     * Transform user data object to the format expected by login() method.
     * email      -> data.email
     * first_name -> data.given_name
     * last_name  -> data.family_name
     */
    public function transform_userdata( $data ) {
        $this->userdata = [];

        if ( ! is_object( $data ) || empty( $data->aud ) || $data->aud !== $this->app_id || empty( $data->email ) ) {
            return false;
        }

        $this->userdata['email'] = $data->email;

        if ( ! empty( $data->given_name ) ) {
            $this->userdata['first_name'] = $data->given_name;
        }

        if ( ! empty( $data->family_name ) ) {
            $this->userdata['last_name'] = $data->family_name;
        }

        // Used to tell if this account has already been connected to the user.
        $this->userdata['connected_account'] = [
            'key' => $this->user_key,
            'value' => $data->email,
        ];

        $this->userdata['custom_fields'] = [];

        if ( ! empty( $data->name ) ) {
            $this->userdata['custom_fields']['mylisting_google_account_name'] = $data->name;
        }

        if ( ! empty( $data->picture ) ) {
            $this->userdata['custom_fields']['mylisting_google_account_picture'] = $data->picture;
        }
    }
}