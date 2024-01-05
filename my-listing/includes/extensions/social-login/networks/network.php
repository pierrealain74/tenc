<?php

namespace MyListing\Ext\Social_Login\Networks;

abstract class Network {
    use \MyListing\Src\Traits\Instantiatable;

	// Add the HTML code that will display the login button for this network.
	abstract public function display_button();

	// Transform the user data object to the format expected by the login() method.
	abstract public function transform_userdata( $data );

    // Check if this network is enabled and if it should be displayed. Return boolean.
    abstract public function is_enabled();

    /**
     * Handle the ajax login post request.
     *
     * @param array $request POST request object.
     * @since 1.6.3
     */
    public function handle_request( $request ) {
        $this->request = $request;
        $process = ! empty( $this->request['process'] ) ? $this->request['process'] : '';

        // To disconnect, we simply delete the attached user meta of the network account.
        if ( $process === 'disconnect' && is_user_logged_in() ) {
            // Delete user meta associated with this network account.
            delete_user_meta( get_current_user_id(), $this->user_key );
            foreach ( (array) $this->custom_fields as $field_key ) {
                delete_user_meta( get_current_user_id(), $field_key );
            }

            return wp_send_json( [
                'status'  => 'success',
            ] );
        }

        $this->get_user_data();

        if ( in_array( $process, ['login', 'connect'] ) ) {
            try {
                $this->{$process}();
                return wp_send_json( [
                    'status'  => 'success',
                ] );
            } catch (\Exception $e) {
                return wp_send_json( [
                    'status'  => 'error',
                    'message' => $e->getMessage(),
                ] );
            }
        }

        return wp_send_json( [
            'status'  => 'login_invalid',
            'message' => _x( 'Couldn\'t process request.', 'Social login connect account', 'my-listing' ),
        ] );
    }

    /**
     * Based on the fetched user data, either create a
     * new user, or log them in if they're already registered.
     *
     * @since 1.6.3
     */
    public function login() {
        if ( ! is_array( $this->userdata ) || ! isset( $this->userdata['email'] ) || empty( $this->userdata['connected_account'] ) ) {
            throw new \Exception( _x( 'Couldn\'t process request.', 'Social login connect account', 'my-listing' ) );
        }

        // See if this account is connected to an existing user.
        $users = get_users( [
           'meta_key' => $this->userdata['connected_account']['key'],
           'meta_value' => $this->userdata['connected_account']['value'],
           'number' => 1,
           'count_total' => false
        ] );

        // If so, log them in.
        if ( ! empty( $users ) ) {
            $this->update_meta( $users[0]->ID );
            if ( $this->login_existing_user( $users[0]->user_login ) ) {
                return true;
            }

            throw new \Exception( _x( 'Login failed.', 'Social login connect account', 'my-listing' ) );
        }

        // If a user with this email already exists, then log them in.
    	if ( $user = get_user_by( 'email', $this->userdata['email'] ) ) {
            // Save connected account information.
            update_user_meta( $user->ID, $this->userdata['connected_account']['key'], $this->userdata['connected_account']['value'] );
            $this->update_meta( $user->ID );

            if ( $this->login_existing_user( $user->user_login ) ) {
                return true;
            }

            throw new \Exception( _x( 'Login failed.', 'Social login connect account', 'my-listing' ) );
        }

        // Otherwise, insert a new user.
        $args = [];
        $email_parts = explode( '@', $this->userdata['email'] );
        $args['user_login'] = $email_parts[0];
        $args['user_email'] = $this->userdata['email'];
        $args['user_pass']  = wp_generate_password(16);

        if ( ! empty( $this->userdata['first_name'] ) ) {
            $args['first_name'] = $this->userdata['first_name'];
        }

        if ( ! empty( $this->userdata['last_name'] ) ) {
            $args['last_name'] = $this->userdata['last_name'];
        }

        // Edge case: if this user login is taken, append a random id for uniqueness.
        if ( $user = get_user_by( 'login', $args['user_login'] ) ) {
            $args['user_login'] = sprintf( '%s.%s', $args['user_login'], bin2hex( openssl_random_pseudo_bytes(2) ) );
        }

        // determine user role
        if ( mylisting()->get('roles.secondary.enabled') ) {
            if ( isset( $_POST['user_role'] ) && in_array( $_POST['user_role'], ['primary', 'secondary'], true ) ) {
                $args['role'] = $_POST['user_role'] === 'secondary' ? 'customer_alt' : 'customer';
            } else {
                $args['role'] = mylisting()->get('roles.default_form') === 'secondary' ? 'customer_alt' : 'customer';
            }
        } else {
            $args['role'] = 'customer';
        }

        $user_id = wp_insert_user( $args );

        // Save connected account information.
        update_user_meta( $user_id, $this->userdata['connected_account']['key'], $this->userdata['connected_account']['value'] );
        $this->update_meta( $user_id );

        // If user is being registered through social login, then automatically set the picture settings to use the network picture.
        update_user_meta( $user_id, 'mylisting_profile_picture', $this->name );

    	if ( ! is_wp_error( $user_id ) && $this->login_existing_user( $args['user_login'] ) ) {
            // add support for woocommerce new account email
            do_action( 'woocommerce_created_customer', $user_id, $args, true );
            return true;
        }

        throw new \Exception( _x( 'Registration failed.', 'Social login connect account', 'my-listing' ) );
    }

    /**
     * Connect logged in user to this social network account.
     *
     * @since 1.6.6
     */
    public function connect() {
        if ( ! is_array( $this->userdata ) || ! isset( $this->userdata['email'] ) || empty( $this->userdata['connected_account'] ) || ! is_user_logged_in() ) {
            throw new \Exception( _x( 'Couldn\'t process request.', 'Social login connect account', 'my-listing' ) );
        }

        // See if this account is connected to an existing user.
        $users = get_users( [
           'meta_key' => $this->userdata['connected_account']['key'],
           'meta_value' => $this->userdata['connected_account']['value'],
           'number' => 1,
           'count_total' => false
        ] );

        // If so, log them in.
        if ( ! empty( $users ) ) {
            throw new \Exception( _x( 'This account is in use by another site member.', 'Social login connect account', 'my-listing' ) );
        }

        // Save connected account information.
        update_user_meta( get_current_user_id(), $this->userdata['connected_account']['key'], $this->userdata['connected_account']['value'] );
        $this->update_meta( get_current_user_id() );
    }


    public function update_meta( $user_id ) {
        if ( ! is_numeric( $user_id ) || empty( $this->userdata['custom_fields'] ) ) {
            return false;
        }

        foreach ( (array) $this->userdata['custom_fields'] as $field_key => $field_value ) {
            update_user_meta( $user_id, $field_key, $field_value );
        }

        // if woo-confirmation-email plugin is active, all users registered through social login
        // should be marked as verified, without need of a confirmation email.
        if ( class_exists( 'XLWUEV_Core' ) ) {
            if ( get_user_meta( $user_id, 'wcemailverified', true ) !== 'true' ) {
                mlog()->note( 'Marking user #'.absint($user_id).' as verified.' );
                update_user_meta( $user_id, 'wcemailverified', 'true' );
            }
        }
    }

	/**
	 * Login an existing user.
	 *
	 * @since 1.6.3
	 */
	public function login_existing_user( $username ) {
		add_filter( 'authenticate', [ $this, 'allow_programmatic_login' ], 10, 3 );
		$user = wp_signon( array( 'user_login' => $username ) );
		remove_filter( 'authenticate', [ $this, 'allow_programmatic_login'], 10, 3 );

		if ( is_a( $user, 'WP_User' ) ) {
			wp_set_current_user( $user->ID, $user->user_login );

			if ( is_user_logged_in() ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Enable programmatic login for a specific user.
	 *
	 * @since 1.6.3
	 */
	public function allow_programmatic_login( $user, $username, $password ) {
		return get_user_by( 'login', $username );
	}
}
