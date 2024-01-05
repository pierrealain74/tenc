<?php

namespace MyListing\Controllers;

use \MyListing\Src\User_Roles;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Register_Form_Controller extends Base_Controller {

	protected function hooks() {
        $this->on( 'woocommerce_register_post', '@validate_recaptcha', 10, 3 );
        $this->on( 'woocommerce_register_post', '@validate_fields', 50, 3 );
        $this->on( 'woocommerce_created_customer', '@save_fields', 50, 3 );
        $this->on( 'woocommerce_new_customer_data', '@set_role', 50 );
        $this->on( 'woocommerce_login_form_end', '@set_redirect_url' );
        $this->on( 'woocommerce_register_form_end', '@set_redirect_url' );
        $this->on( 'woocommerce_register_form', '@display_terms_and_conditions', 19 );
        $this->on( 'woocommerce_register_post', '@validate_terms_and_conditions', 20, 3 );
        $this->filter( 'woocommerce_process_login_errors', '@validate_login_recaptcha', 10 );

        $this->on( 'lostpassword_post', '@validate_lostform_recaptcha', 10, 1);
	}

	protected function validate_lostform_recaptcha( $errors ) {

		if ( mylisting()->get('roles.lost_captcha') ) {
			try {
				\MyListing\validate_recaptcha();
			} catch ( \Exception $e ) {
				$errors->add( 'validation_error', $e->getMessage() );
			}
		}
	}

	protected function validate_recaptcha( $username, $email, $errors ) {
		if ( is_checkout() ) {
			return;
		}

		if ( mylisting()->get('roles.register_captcha') ) {
			try {
				\MyListing\validate_recaptcha();
			} catch ( \Exception $e ) {
				$errors->add( 'validation_error', $e->getMessage() );
			}
		}
	}

	protected function validate_login_recaptcha( $errors ) {
		if ( mylisting()->get('roles.login_captcha') ) {
			try {
				\MyListing\validate_recaptcha();
			} catch ( \Exception $e ) {
				$errors->add( 'validation_error', $e->getMessage() );
			}
		}

		return $errors;
	}

	protected function validate_fields( $username, $email, $errors ) {
		if ( is_checkout() ) {
			return;
		}

		$fields = User_Roles\get_used_fields( User_Roles\get_posted_role() );
		foreach ( $fields as $field ) {
			$field->form = $field::FORM_REGISTER;
			if ( ! $field->get_prop('show_in_register_form') ) {
				continue;
			}

			// skip if username is generated automatically
			if ( $field->get_key() === 'username' && get_option('woocommerce_registration_generate_username') !== 'no' ) {
				continue;
			}

			// skip if password is generated automatically
			if ( $field->get_key() === 'password' && get_option('woocommerce_registration_generate_password') !== 'no' ) {
				continue;
			}

			// skip if password is generated automatically
			if ( $field->get_key() === 'password_2' && get_option('woocommerce_registration_generate_password') !== 'no' ) {
				continue;
			}

			try {
				$field->check_validity();
			} catch ( \Exception $e ) {
				$errors->add( 'validation_error', $e->getMessage() );
			}
		}
	}

	protected function save_fields( $user_id ) {
		if ( is_checkout() ) {
			return;
		}

		$user = get_user_by( 'id', $user_id );
		if ( ! $user ) {
			return;
		}

		$fields = User_Roles\get_used_fields( User_Roles\get_posted_role() );
		foreach ( $fields as $field ) {
			$field->form = $field::FORM_REGISTER;
			$field->user = $user;
			if ( ! $field->get_prop('show_in_register_form') ) {
				continue;
			}

			$field->update();
		}
	}

	protected function set_role( $user_data ) {
		if ( is_checkout() ) {
			return $user_data;
		}

		// if secondary role is enabled and has been chosen in the registration form,
		// apply it to the new user; otherwise, the primary role is applied by default
		if ( User_Roles\get_posted_role() === 'secondary' && mylisting()->get('roles.secondary.enabled') ) {
			$user_data['role'] = 'customer_alt';
		}

		return $user_data;
	}

	protected function set_redirect_url() {

		if ( ! empty( $_REQUEST['redirect'] ) ) {
			$redirect_url = wp_validate_redirect( $_REQUEST['redirect'] );
		} elseif ( isset( $_REQUEST['password-reset'] ) && ! empty( $_REQUEST['password-reset'] ) ) {
		    $redirect_url = wc_get_page_permalink( 'myaccount' );
		} else {
		    $redirect_url = wp_get_referer();

		    if ( $redirect_url ) {
		    	$explode = explode( 'customer-logout/?', $redirect_url );

		    	if ( isset( $explode[0], $explode[1] ) ) {
		    		$redirect_url = wc_get_page_permalink( 'myaccount' );
		    	}
		    }
		}
		
		printf( '<input type="hidden" name="redirect" value="%s">', esc_url( $redirect_url ) );
	}

	protected function display_terms_and_conditions() {
		if ( apply_filters( 'mylisting/enable-terms-checkbox-in-registration', true ) === false ) {
			return;
		}

		if ( wc_terms_and_conditions_checkbox_enabled() ) { ?>
			<div class="terms-and-conditions">
				<div class="md-checkbox">
					<input id="terms_and_conditions" name="terms_and_conditions" type="checkbox" value="yes">
	                <label for="terms_and_conditions"><?php echo wc_terms_and_conditions_checkbox_text() ?></label>
	            </div>
            </div>
		<?php }
	}

	protected function validate_terms_and_conditions( $username, $email, $errors ) {
		if ( is_checkout() ) {
			return;
		}

		if ( apply_filters( 'mylisting/enable-terms-checkbox-in-registration', true ) === false ) {
			return;
		}

		if ( wc_terms_and_conditions_checkbox_enabled() ) {
			if ( empty( $_POST['terms_and_conditions'] ) ) {
				$errors->add(
					'terms_error',
					__( 'You must accept the terms and conditions to create an account.', 'my-listing' )
				);
			}
		}
	}
}
