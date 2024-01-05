<?php

namespace MyListing\Ext\Email_Verification;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WCE_Public {

	public static $pl_url;
	public static $my_account_id;
	private static $user_id;
	private static $email_id;
	public static $is_checkout_page = false;
	public static $user_login;
	public static $user_email;
	public static $is_user_verified = '';
	public static $is_user_created = false;
	public static $is_notice_shown_at_order_received_page = false;
	public static $is_user_already_verified = false;
	public static $is_new_user_email_sent = false;
	public static $is_user_made_from_myaccount_page = false;
	public static $should_verification_email_be_send = true;
	public static $should_notice_be_shown = true;

	public static function boot() {
        new self;
    }

	public function __construct() {

		// print_r( get_option( 'woocommerce_customer_new_account_settings' ) );exit();
		self::$my_account_id = get_option( 'woocommerce_myaccount_page_id' );

		if ( '' === self::$my_account_id ) {
			self::$my_account_id = get_option( 'page_on_front' );
		}

		add_action( 'woocommerce_created_customer_notification', array( __CLASS__, 'mlwev_new_custom_registration_form' ), 10, 3 );
		add_filter( 'woocommerce_registration_redirect', array( __CLASS__, 'redirect_new_user' ), 99 );
		add_action( 'wp', array( __CLASS__, 'authenticate_user_by_email' ) );
		add_action( 'wp', array( __CLASS__, 'show_notification_message' ) );
		add_action( 'wp', array( __CLASS__, 'resend_verification_email' ) );
		// add_action( 'wp_enqueue_scripts', array( __CLASS__, 'wuev_public_js' ) );
		add_action( 'wp_login', array( __CLASS__, 'custom_form_login_check' ), 10, 1 );
		// add_action( 'user_register', array( __CLASS__, 'custom_form_user_register' ), 10, 1 );
		add_action( 'woocommerce_checkout_update_user_meta', array( __CLASS__, 'new_user_registeration_from_checkout_form' ), 10, 2 );
		add_action( 'woocommerce_checkout_process', array( __CLASS__, 'set_checkout_page' ), 11, 1 );
		add_action( 'woocommerce_register_post', array( __CLASS__, 'woocommerce_my_account_page' ), 10, 1 );
		// add_action( 'set_auth_cookie', array( __CLASS__, 'custom_form_login_check_with_cookie' ), 10, 5 );
		add_filter( 'send_email_change_email', array( __CLASS__, 'unverify_user_account' ), 99, 2 );

		add_action( 'mlwev_on_email_verification', array( __CLASS__, 'ml_send_cofirm_notification' ), 99 );
	}

	public static function ml_send_cofirm_notification( $user_id ) {

		if ( $user_id ) {

			$email = new \WC_Emails();
			$email = $email->emails['WC_Register_Confirm_Email'];
			$email->trigger( $user_id );
		}
	}

	public static function woocommerce_my_account_page( $username ) {
		self::$is_user_made_from_myaccount_page = true;
	}

	public static function mlwev_new_custom_registration_form( $user_id, $new_customer_data = array(), $password_generated = false ) {
		if ( false === self::$is_new_user_email_sent && self::$should_verification_email_be_send ) {
			self::new_user_registration( $user_id );
			self::$is_new_user_email_sent = true;
		}
	}

	/*
	 * This function is executed when a new user is made from the checkout page of the woocommerce.
	 * Its hooked into 'woocommerce_checkout_update_user_meta' action.
	 */
	public static function new_user_registeration_from_checkout_form( $customer_id, $data ) {
		if ( is_array( $data ) && count( $data ) > 0 ) {
			if ( '0' != $customer_id ) {
				if ( '1' == $data['createaccount'] ) {
					if ( false === self::$is_new_user_email_sent && self::$should_verification_email_be_send ) {
						self::new_user_registration( $customer_id );
						self::$is_new_user_email_sent = true;
					}
				}
			}
		}
	}

	/*
	 * This function sends a new verification email upon user registration from any custom registration form.
	 */
	public static function custom_form_user_register( $user_id ) {
		$user   = get_user_by( 'id', $user_id );
		$status = get_user_meta( (int) $user_id, 'wcemailverified', true );

		if ( ! is_super_admin() && 'administrator' !== $user->roles[0] ) {
			if ( 'true' !== $status ) {
				if ( false === self::$is_new_user_email_sent ) {
					if ( false === self::$is_checkout_page && false === self::$is_user_made_from_myaccount_page && self::$should_verification_email_be_send ) {
						Email_Verification::$is_user_made_from_custom_form = true;
						self::new_user_registration( $user_id );
						self::$is_new_user_email_sent = true;
					}
				}
			}
		}
	}

	/*
	 * This function is executed just after a new user is made from woocommerce registration form in myaccount page.
	 * Its hooked into 'woocommerce_registration_redirect' filter.
	 * If restrict user setting is enabled from the plugin settings screen, then this function will logs out the user.
	 */
	public static function redirect_new_user( $redirect ) {
		if ( true === self::$is_new_user_email_sent && false === Email_Verification::$is_mlwev_resend_link_clicked && defined( 'WC_DOING_AJAX' ) === false && false === is_order_received_page() ) {
			$redirect                = add_query_arg( array(
				'mlwev' => base64_encode( self::$user_id ),
			), get_the_permalink( self::$my_account_id ) );
			wp_logout();
		}

		return $redirect;
	}

	/*
	 * This function verifies the user when the user clicks on the verification link in its email.
	 * If automatic login setting is enabled in plugin setting screen, then the user is forced loggedin.
	 */
	public static function authenticate_user_by_email() {

		if ( isset( $_GET['woo_confirmation_verify'] ) && '' !== $_GET['woo_confirmation_verify'] ) { // WPCS: input var ok, CSRF ok.
			$user_meta = explode( '@', base64_decode( $_GET['woo_confirmation_verify'] ) ); // WPCS: input var ok, CSRF ok.
			if ( 'true' === get_user_meta( (int) $user_meta[1], 'wcemailverified', true ) ) {
				self::$is_user_already_verified = true;
			}

			$verified_code = get_user_meta( (int) $user_meta[1], 'wcemailverifiedcode', true );
// print_r( $user_meta );exit();
			if ( ! empty( $verified_code ) && $verified_code === $user_meta[0] ) {
				Email_Verification::$mlwev_user_id = (int) $user_meta[1];
				
				update_user_meta( (int) $user_meta[1], 'wcemailverified', 'true' );

				self::allow_automatic_login( (int) $user_meta[1] );
				self::please_login_email_message();
			}
		}
	}

	/*
	 * This function shows the notification messages based on get parameters.
	 * Shows messages for new user registration, user restriction, verification success message, message in user dashboard.
	 */
	public static function show_notification_message() {
		if ( isset( $_GET['mlwev'] ) && '' !== $_GET['mlwev'] ) { // WPCS: input var ok, CSRF ok.
			Email_Verification::$mlwev_user_id = base64_decode( $_GET['mlwev'] ); // WPCS: input var ok, CSRF ok.
			$registration_message        = Email_Verification::maybe_parse_merge_tags( Email_Verification::get_setting_value( 'mlwev-messages', 'mlwev_email_registration_message' ) );
			if ( false === wc_has_notice( $registration_message, 'notice' ) ) {
				wc_add_notice( $registration_message, 'notice' );
			}
		} elseif ( ! is_admin() && is_user_logged_in() && defined( 'WC_DOING_AJAX' ) === false ) {
			global $current_user;
			$user_roles = $current_user->roles;
			$user_role  = array_shift( $user_roles );

			if ( 'customer' === $user_role ) {
				$user_id                               = get_current_user_id();
				Email_Verification::$mlwev_user_id           = $user_id;
				Email_Verification::$mlwev_myaccount_page_id = self::$my_account_id;
				self::$is_user_verified                = get_user_meta( $user_id, 'wcemailverified', true );
				$order_received_page                   = is_order_received_page();
				$order_pay_page                        = is_checkout_pay_page();

				if ( false === $order_received_page && empty( self::$is_user_verified ) ) {
					if ( false === $order_pay_page ) {
						wp_logout();
						self::please_confirm_email_message( $user_id );
						// if not order , then redirect to myaccount page
						if ( false === $order_received_page ) {
							$redirect_url = add_query_arg( array(
								'mlsm' => base64_encode( $user_id ),
							), get_the_permalink( self::$my_account_id ) );
							wp_safe_redirect( $redirect_url );
							exit;
						}
					}
				}

				if ( $order_received_page ) {
					$order_id = self::get_order_id();

					if ( 'true' !== self::$is_user_verified ) {
						if ( false === WC()->session->has_session() ) {
							WC()->session->set_customer_session_cookie( true );
						}

						$registration_message         = Email_Verification::maybe_parse_merge_tags( Email_Verification::get_setting_value( 'mlwev-messages', 'mlwev_email_registration_message' ) );

						if ( false === wc_has_notice( $registration_message, 'notice' ) ) {
							wc_add_notice( $registration_message, 'notice' );
						}
					}
				}
			}
		}
		if ( isset( $_GET['mlsm'] ) && '' !== $_GET['mlsm'] ) { // WPCS: input var ok, CSRF ok.
			Email_Verification::$mlwev_user_id = base64_decode( $_GET['mlsm'] ); // WPCS: input var ok, CSRF ok.

			$message = Email_Verification::maybe_parse_merge_tags( 'You need to verify your account before login. {{mlwev_resend_link}}' );

			if ( false === wc_has_notice( $message, 'notice' ) ) {
				wc_add_notice( $message, 'notice' );
			}

			if ( false === WC()->session->has_session() ) {
				WC()->session->set_customer_session_cookie( true );
			}
		}
		if ( isset( $_GET['xlvm'] ) && '' !== $_GET['xlvm'] ) { // WPCS: input var ok, CSRF ok.
			$success_message = Email_Verification::maybe_parse_merge_tags( Email_Verification::get_setting_value( 'mlwev-messages', 'mlwev_email_success_message' ) );
			wc_add_notice( $success_message, 'notice' );
		}
	}

	/*
	 * Return order id from get parameter
	 */
	public static function get_order_id() {
		if ( isset( $_GET['order-received'] ) ) { // WPCS: input var ok, CSRF ok.
			$order_id = $_GET['order-received']; // WPCS: input var ok, CSRF ok.
		} else {
			$url           = $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']; // WPCS: input var ok, CSRF ok.
			$template_name = strpos( $url, '/order-received/' ) === false ? '/view-order/' : '/order-received/';
			if ( strpos( $url, $template_name ) !== false ) {
				$start      = strpos( $url, $template_name );
				$first_part = substr( $url, $start + strlen( $template_name ) );
				$order_id   = substr( $first_part, 0, strpos( $first_part, '/' ) );
			}
		}

		return $order_id;
	}

	/*
	 * This function localizes the plugin version and plugin settings.
	 */
	public static function wuev_public_js() {
		wp_enqueue_script( XLWUEV_SLUG . '-custom-js', $this->pl_url . '/assets/js/woo-confirmation-email-admin.js', false, XLWUEV_VERSION, true );
		$wuev_version = array(
			'plugin_version' => XLWUEV_VERSION,
		);
		wp_localize_script( XLWUEV_SLUG . '-custom-js', 'xlwuev', $wuev_version );
		wp_localize_script( XLWUEV_SLUG . '-custom-js', 'mlwev_settings', preg_replace( '/\\\\/', '', json_encode( Email_Verification::$plugin_settings ) ) );
	}

	/*
	 * This function gets executed from different places when ever a new user is registered or resend verifcation email is sent.
	 */
	public static function new_user_registration( $user_id ) {
		$current_user                          = get_user_by( 'id', $user_id );
		self::$user_id                         = $current_user->ID;
		self::$email_id                        = $current_user->user_email;
		self::$user_login                      = $current_user->user_login;
		self::$user_email                      = $current_user->user_email;
		self::$is_user_created                 = true;
		Email_Verification::$mlwev_user_login        = $current_user->user_login;
		Email_Verification::$mlwev_display_name      = $current_user->display_name;
		Email_Verification::$mlwev_user_email        = $current_user->user_email;
		Email_Verification::$mlwev_user_id           = $current_user->ID;
		$is_secret_code_present                = get_user_meta( self::$user_id, 'wcemailverifiedcode', true );

		if ( '' === $is_secret_code_present ) {
			$secret_code = md5( self::$user_id . time() );
			update_user_meta( $user_id, 'wcemailverifiedcode', $secret_code );
		}

		Email_Verification::code_mail_sender( $current_user->user_email );
		self::$is_new_user_email_sent = true;
	}

	/*
	 * This function executes just after the user logged in. If restrict user setting is enabled in the plugin settings screen, the the user is force
	 * logged out.
	 */
	public static function custom_form_login_check( $user_login ) {
		$user = get_user_by( 'login', $user_login );
		if ( ! is_super_admin() && 'administrator' !== $user->roles[0] ) {
			if ( 'true' !== get_user_meta( $user->ID, 'wcemailverified', true ) ) {
				wp_logout();
				if ( false === is_order_received_page() && false === self::$is_checkout_page ) {
					$redirect_url = add_query_arg( array(
						'mlsm' => base64_encode( $user->ID ),
					), apply_filters( 'mlwev_custom_form_login_check_redirect_url', get_the_permalink( self::$my_account_id ) ) );
					wp_safe_redirect( $redirect_url );
					exit;
				}
			}
		}
	}

	/*
	 * This function executes just after if the user is force logged in. If restrict user setting is enabled in the plugin settings screen, the the user is force
	 * logged out.
	 */
	public static function custom_form_login_check_with_cookie( $auth_cookie, $expire, $expiration, $user_id, $scheme ) {
		$order_received_page   = is_order_received_page();
		$order_pay_page        = is_checkout_pay_page();
		$allow_automatic_login = Email_Verification::get_setting_value( 'wuev-general-settings', 'mlwev_automatic_user_login' );

		if ( false == $order_received_page && false == $order_pay_page && '1' == $allow_automatic_login ) {
			$user                      = get_user_by( 'ID', $user_id );
			$user_registered_timestamp = strtotime( $user->data->user_registered );
			$current_timestamp         = time();

			if ( $current_timestamp - $user_registered_timestamp < 60 ) {
				$is_new_user = true;
			} else {
				$is_new_user = false;
			}

			if ( ! is_super_admin() && 'administrator' !== $user->roles[0] ) {
				if ( 'true' !== get_user_meta( $user->ID, 'wcemailverified', true ) ) {
					$is_force_login_enabled = Email_Verification::get_setting_value( 'wuev-general-settings', 'mlwev_restrict_user' );

					if ( '1' == $is_force_login_enabled ) {
						wp_clear_auth_cookie();
						if ( false === is_order_received_page() && false === self::$is_checkout_page ) {
							if ( $is_new_user ) {
								$redirect_url = add_query_arg( array(
									'mlwev' => base64_encode( $user->ID ),
								), get_the_permalink( self::$my_account_id ) );
							} else {
								$redirect_url = add_query_arg( array(
									'mlsm' => base64_encode( $user->ID ),
								), get_the_permalink( self::$my_account_id ) );
							}

							$error_validation_page = Email_Verification::get_setting_value( 'wuev-general-settings', 'mlwev_verification_error_page' );
							if ( '2' == $error_validation_page ) {
								$error_validation_page_id = Email_Verification::get_setting_value( 'wuev-general-settings', 'mlwev_verification_error_page_id' );
								$redirect_url             = add_query_arg( array(
									'mlsm' => base64_encode( $user->ID ),
								), get_the_permalink( $error_validation_page_id ) );
							}

							wp_safe_redirect( $redirect_url );
							exit;
						}
					}
				}
			}
		}
	}

	/*
	 * This function unverifies a user's email because the user had changed its email ID. So user has to again verify its email.
	 */

	public static function unverify_user_account( $bool, $user ) {
		if ( $bool ) {
			delete_user_meta( $user['ID'], 'wcemailverified' );
		}

		return $bool;
	}

	/*
	 * This function sets the is_checkout_page to true if the current page is woocommerce checkout page.
	 */
	public static function set_checkout_page() {
		self::$is_checkout_page = true;
		if ( isset( $_POST['payment_method'] ) && '' != $_POST['payment_method'] ) {
			self::$should_verification_email_be_send = true;
		}
	}

	/*
	 * This function adds woocommerce notices.
	 */
	public static function please_confirm_email_message( $user_id ) {
		if ( false === WC()->session->has_session() ) {
			WC()->session->set_customer_session_cookie( true );
		}

		if ( empty( self::$is_user_verified ) ) {
			if ( self::$is_user_created ) {
				if ( true === self::$is_checkout_page ) {
					$registration_message = Email_Verification::maybe_parse_merge_tags( Email_Verification::get_setting_value( 'mlwev-messages', 'mlwev_email_registration_message' ) );
					if ( false === is_order_received_page() ) {
						wc_add_notice( $registration_message, 'notice' );
					}
				}
			} else {
				$message = 'You need to verify your account before login.';

				if ( false == wc_has_notice( $message, 'notice' ) ) {
					wc_add_notice( $message, 'notice' );
				}
			}
		}
	}

	/*
	 * This function shows the verification success messages.
	 */
	public static function please_login_email_message() {
		if ( false === WC()->session->has_session() ) {
			WC()->session->set_customer_session_cookie( true );
		}

		$verified = get_user_meta( Email_Verification::$mlwev_user_id, 'wcemailverified', true );
		
		if ( 'true' === $verified && self::$is_user_already_verified ) {

			$already_verified_message = Email_Verification::maybe_parse_merge_tags( Email_Verification::get_setting_value( 'mlwev-messages', 'mlwev_email_verification_already_done' ) );
			wc_add_notice( $already_verified_message, 'notice' );
		} else {
			do_action( 'mlwev_on_email_verification', (int) Email_Verification::$mlwev_user_id );
			$success_message = Email_Verification::maybe_parse_merge_tags( Email_Verification::get_setting_value( 'mlwev-messages', 'mlwev_email_success_message' ) );
			wc_add_notice( $success_message, 'notice' );
		}
	}

	/**
	 * This function sends a new verification email to user if the user clicks on 'resend verification email' link.
	 * If the email is already verified then it redirects to my-account page
	 */
	public static function resend_verification_email() {
		if ( isset( $_GET['wc_confirmation_resend'] ) && '' !== $_GET['wc_confirmation_resend'] ) { // WPCS: input var ok, CSRF ok.
			$user_id = base64_decode( $_GET['wc_confirmation_resend'] ); // WPCS: input var ok, CSRF ok.

			if ( false === WC()->session->has_session() ) {
				WC()->session->set_customer_session_cookie( true );
			}

			$verified = get_user_meta( $user_id, 'wcemailverified', true );

			if ( 'true' === $verified ) {
				$already_verified_message = Email_Verification::maybe_parse_merge_tags( Email_Verification::get_setting_value( 'mlwev-messages', 'mlwev_email_verification_already_done' ) );
				wc_add_notice( $already_verified_message, 'notice' );
			} else {
				Email_Verification::$mlwev_user_id                  = $user_id;
				Email_Verification::$mlwev_myaccount_page_id        = self::$my_account_id;
				Email_Verification::$is_mlwev_resend_link_clicked = true;
				self::new_user_registration( $user_id );
				$new_verification_link = Email_Verification::maybe_parse_merge_tags( Email_Verification::get_setting_value( 'mlwev-messages', 'mlwev_email_new_verification_link' ) );
				wc_add_notice( $new_verification_link, 'notice' );
			}
		}
	}

	/**
	 * @param mixed $user_id
	 */
	public static function set_user_id( $user_id ) {
		self::$user_id = $user_id;
	}

	/*
	 * This function force login a user.
	 */
	public static function allow_automatic_login( $user_id ) {
		wp_clear_auth_cookie();
		wp_set_current_user( $user_id );
		wp_set_auth_cookie( $user_id );
	}
}