<?php

namespace MyListing\Controllers;

use \MyListing\Src\User_Roles;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Account_Details_Form_Controller extends Base_Controller {

	protected function hooks() {
        $this->on( 'woocommerce_save_account_details_errors', '@validate_account_details', 30, 2 );
        $this->on( 'woocommerce_save_account_details', '@save_account_details', 30 );
		$this->on( 'template_redirect', '@handle_role_switch' );

        // unset woocommerce required fields (validation is handled by the theme since v2.5)
        $this->filter( 'woocommerce_save_account_details_required_fields', '__return_empty_array' );
	}

	protected function validate_account_details( $errors, $user ) {
		$fields = User_Roles\get_used_fields( User_Roles\get_current_user_role() );
		foreach ( $fields as $field ) {
			$field->form = $field::FORM_ACCOUNT_DETAILS;
			$field->user = $user;

			if ( ! $field->get_prop('show_in_account_details') ) {
				continue;
			}

			// username cannot be edited; password should not be required in account details form
			if ( $field->get_key() === 'username' || $field->get_key() === 'password' ) {
				$field->props['required'] = false;
			}

			// when social login is enabled, the profile picture can be retrieved from there instead of an upload
			// so a required check may not be necessary
			if ( $field->get_key() === 'profile_picture' && ! empty( $_POST['cts-user-picture-settings'] ) && $_POST['cts-user-picture-settings'] !== 'default' ) {
				$field->props['required'] = false;
			}

			try {
				$field->check_validity();
			} catch ( \Exception $e ) {
				$errors->add( 'validation_error', $e->getMessage() );
			}
		}

		// if these fields are hidden from the account details form for this user role, class-wc-form-handler
		// will treat them as having empty values and the current values will be deleted when users edit their profile;
		// as a workaround, before `wp_update_user` is called, we make sure this data preserves its values.
		$current_user = wp_get_current_user();
		if ( ! isset( $_POST['account_first_name'] ) ) {
			$user->first_name = $current_user->first_name;
		}

		if ( ! isset( $_POST['account_last_name'] ) ) {
			$user->last_name = $current_user->last_name;
		}

		if ( ! isset( $_POST['account_display_name'] ) ) {
			$user->display_name = $current_user->display_name;
		}
	}

	protected function save_account_details( $user_id ) {
		$fields = User_Roles\get_used_fields( User_Roles\get_current_user_role() );
		foreach ( $fields as $field ) {
			$field->form = $field::FORM_ACCOUNT_DETAILS;
			$field->user = get_user_by( 'id', $user_id );
			if ( ! $field->get_prop('show_in_account_details') ) {
				continue;
			}

			$field->update();
		}
	}

	protected function handle_role_switch() {
		if ( empty( $_POST['action'] ) || $_POST['action'] !== 'mylisting_switch_role' || ! is_user_logged_in() ) {
			return;
		}

		if ( ! wp_verify_nonce( $_POST['mylisting-switch-role-nonce'], 'mylisting_switch_role' ) ) {
			return;
		}

		if ( ! User_Roles\user_can_switch_role() ) {
			wc_add_notice( __( 'You\'re not allowed to do that.', 'my-listing' ), 'error' );
			return;
		}

		$user = wp_get_current_user();
		$role = User_Roles\get_current_user_role();
		$new_role = $role === 'primary' ? 'secondary' : 'primary';

		// switch from customer/subscriber to customer_alt
		if ( $new_role === 'secondary' ) {
			if ( in_array( 'customer', (array) $user->roles, true ) ) {
				$user->remove_role( 'customer' );
			}

			// backward compatibility
			if ( in_array( 'subscriber', (array) $user->roles, true ) ) {
				$user->remove_role( 'subscriber' );
			}

			$user->add_role( 'customer_alt' );
		}

		// switch from customer_alt to customer
		if ( $new_role === 'primary' ) {
			if ( in_array( 'customer_alt', (array) $user->roles, true ) ) {
				$user->remove_role( 'customer_alt' );
			}

			$user->add_role( 'customer' );
		}

		wc_add_notice( sprintf(
			__( 'You\'ve switched to a %s account.', 'my-listing' ),
			mylisting()->get( 'roles.'.$new_role.'.label' )
		), 'success' );
	}
}
