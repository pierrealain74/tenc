<?php

namespace MyListing\Controllers;

use \MyListing\Src\User_Roles;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class User_Roles_Controller extends Base_Controller {

	protected function dependencies() {
		require_once locate_template('includes/src/user-roles/user-roles.php');
	}

	protected function hooks() {
		$this->on( 'admin_menu', '@add_settings_screen', 50 );
		$this->on( 'admin_post_mylisting_role_settings', '@save_settings' );
		$this->on( 'after_setup_theme', '@configure_roles' );
		$this->on( 'mylisting/show-add-listing-widget', '@should_show_add_listing_widget' );
		$this->filter( 'mylisting/load-options:roles', '@get_config', 50 );
		$this->filter( 'woocommerce_account_menu_items', '@hide_account_links', 50 );
	}

	protected function add_settings_screen() {
		add_submenu_page(
			'case27/tools.php',
			'User Roles',
			'User Roles',
			'administrator',
			'mylisting-user-roles',
			function() {
				wp_enqueue_script('sortablejs');
				wp_enqueue_script('vue-draggable');
				wp_enqueue_script('mylisting-admin-user-roles');
				printf(
					'<script type="text/javascript">var MyListing_User_Roles_Config = %s;</script>',
					wp_json_encode( [
						'roles' => mylisting()->get('roles'),
						'presets' => User_Roles\get_preset_fields(),
						'settings' => [
							'enable_registration' => get_option( 'woocommerce_enable_myaccount_registration' ) === 'yes',
							'generate_username' => get_option( 'woocommerce_registration_generate_username' ) === 'yes',
							'generate_password' => get_option( 'woocommerce_registration_generate_password' ) === 'yes',
						],
					] )
				);

				require locate_template('templates/admin/user-roles/user-roles-settings.php');
			}
		);
	}

	protected function get_config() {
		return User_Roles\validate_config( (array) json_decode(
			get_option( 'mylisting_roles', null ),
			ARRAY_A
		) );
	}

	protected function save_settings() {
		check_admin_referer( 'mylisting_role_settings' );
		if ( ! current_user_can( 'administrator' ) ) {
			die;
		}

		$roles = json_decode( wp_unslash( $_POST['roles_config'] ), true );
		if ( json_last_error() === JSON_ERROR_NONE ) {
			User_Roles\set_config( $roles );
		}

		$settings = json_decode( wp_unslash( $_POST['general_settings'] ), true );
		if ( json_last_error() === JSON_ERROR_NONE ) {
			update_option( 'woocommerce_enable_myaccount_registration', $settings['enable_registration'] ? 'yes' : 'no' );
			update_option( 'woocommerce_registration_generate_username', $settings['generate_username'] ? 'yes' : 'no' );
			update_option( 'woocommerce_registration_generate_password', $settings['generate_password'] ? 'yes' : 'no' );
		}

		return wp_safe_redirect( admin_url( 'admin.php?page=mylisting-user-roles&saved=1' ) );
	}

	protected function configure_roles() {
		global $wp_roles;
		if ( ! isset( $wp_roles ) ) {
			$wp_roles = new \WP_Roles;
		}

		// create secondary role if it doesn't already exist
		if ( ! get_role( 'customer_alt' ) ) {
			add_role( 'customer_alt', 'Customer (alt)', [
				'read' => true,
			] );
		}

		// if secondary role isn't enabled, disable it at runtime while keeping
		// the role in the database
		if ( ! mylisting()->get('roles.secondary.enabled') ) {
			unset( $wp_roles->roles['customer_alt'] );
			unset( $wp_roles->role_objects['customer_alt'] );
			unset( $wp_roles->role_names['customer_alt'] );
		}

		// primary role
		if ( isset( $wp_roles->roles['customer'] ) ) {
			$customer_role = get_role('customer');
			$subscriber_role = get_role('subscriber');

			// set role name
			$wp_roles->roles['customer']['name'] = mylisting()->get('roles.primary.label');
			$wp_roles->role_names['customer'] = mylisting()->get('roles.primary.label');

			// set custom caps; on older versions some users were assigned the "subscriber" role,
			// so we make sure they have the same custom caps as the "customer" role
			$can_add_listings = (bool) mylisting()->get('roles.primary.can_add_listings');
			$can_switch_role = (bool) mylisting()->get('roles.primary.can_switch_role');

			if ( $can_add_listings !== ( $customer_role->has_cap('mylisting_can_add_listings') ) ) {
				$customer_role->add_cap( 'mylisting_can_add_listings', $can_add_listings );
				if ( $subscriber_role ) {
					$subscriber_role->add_cap( 'mylisting_can_add_listings', $can_add_listings );
				}
			}

			if ( $can_switch_role !== ( $customer_role->has_cap('mylisting_can_switch_role') ) ) {
				$customer_role->add_cap( 'mylisting_can_switch_role', $can_switch_role );
				if ( $subscriber_role ) {
					$subscriber_role->add_cap( 'mylisting_can_switch_role', $can_switch_role );
				}
			}
		}

		// secondary role
		if ( isset( $wp_roles->roles['customer_alt'] ) ) {
			$customer_alt_role = get_role('customer_alt');

			// set role name
			$wp_roles->roles['customer_alt']['name'] = mylisting()->get('roles.secondary.label');
			$wp_roles->role_names['customer_alt'] = mylisting()->get('roles.secondary.label');

			// set custom caps
			$can_add_listings = (bool) mylisting()->get('roles.secondary.can_add_listings');
			$can_switch_role = (bool) mylisting()->get('roles.secondary.can_switch_role');

			if ( $can_add_listings !== ( $customer_alt_role->has_cap('mylisting_can_add_listings') ) ) {
				$customer_alt_role->add_cap( 'mylisting_can_add_listings', $can_add_listings );
			}

			if ( $can_switch_role !== ( $customer_alt_role->has_cap('mylisting_can_switch_role') ) ) {
				$customer_alt_role->add_cap( 'mylisting_can_switch_role', $can_switch_role );
			}
		}
	}

	protected function should_show_add_listing_widget() {
		if ( ! is_user_logged_in() || User_Roles\user_can_add_listings() ) {
			return true;
		}

		$role = User_Roles\get_current_user_role();
		$other_role = $role === 'primary' ? 'secondary' : 'primary';
		$can_switch_and_post =  User_Roles\user_can_switch_role()
			&& mylisting()->get( 'roles.'.$other_role.'.can_add_listings' );
		?>
		<div class="element">
			<?php if ( $can_switch_and_post ) {
				printf(
					__( 'You must switch to a <strong>%s</strong> account to post new listings.', 'my-listing' ),
					mylisting()->get( 'roles.'.$other_role.'.label' )
				);
			} else {
				echo __( 'You cannot add new listings.', 'my-listing' );
			} ?>
		</div>
		<?php
		return false;
	}

	protected function hide_account_links( $links ) {
		if ( ! User_Roles\user_can_add_listings() ) {
			if ( isset( $links['my-listings'] ) ) {
				unset( $links['my-listings'] );
			}

			if ( isset( $links['promotions'] ) ) {
	        	unset( $links['promotions'] );
	    	}
		}

		return $links;
	}
}
