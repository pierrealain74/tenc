<?php

namespace MyListing\Src\User_Roles;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function set_config( $config ) {
	$config = validate_config( $config );
	update_option( 'mylisting_roles', wp_json_encode( $config ) );
}

function get_default_config() {
	$default_fields = [
		[
			'slug' => 'email',
			'type' => 'email',
			'label' => 'Email',
			'required' => true,
			'show_in_register_form' => true,
			'show_in_account_details' => true,
		],
		[
			'slug' => 'username',
			'type' => 'text',
			'label' => 'Username',
			'required' => true,
			'show_in_register_form' => true,
			'show_in_account_details' => true,
		],
		[
			'slug' => 'password',
			'type' => 'password',
			'label' => 'Password',
			'required' => true,
			'show_in_register_form' => true,
			'show_in_account_details' => true,
		],
	];

	return [
		'login_captcha' => false,
		'register_captcha' => false,
		'default_form' => 'primary',

		'primary' => [
			'label' => 'Customer',
			'can_add_listings' => true,
			'can_switch_role' => false,
			'fields' => $default_fields,
		],

		'secondary' => [
			'enabled' => false,
			'label' => '',
			'can_add_listings' => false,
			'can_switch_role' => false,
			'fields' => $default_fields,
		],
	];
}

function validate_config( $new_config ) {
	$config = get_default_config();

	if ( isset( $new_config['primary'] ) ) {
		$primary = $new_config['primary'];
		if ( ! empty( $primary['label'] ) && is_string( $primary['label'] ) ) {
			$config['primary']['label'] = $primary['label'];
		}

		if ( isset( $primary['can_add_listings'] ) ) {
			$config['primary']['can_add_listings'] = (bool) $primary['can_add_listings'];
		}

		if ( isset( $primary['can_switch_role'] ) ) {
			$config['primary']['can_switch_role'] = (bool) $primary['can_switch_role'];
		}

		if ( ! empty( $primary['fields'] ) && is_array( $primary['fields'] ) ) {
			$config['primary']['fields'] = $primary['fields'];
		}
	}

	if ( isset( $new_config['secondary'] ) ) {
		$secondary = $new_config['secondary'];
		if ( isset( $secondary['enabled'] ) ) {
			$config['secondary']['enabled'] = (bool) $secondary['enabled'];
		}

		if ( ! empty( $secondary['label'] ) && is_string( $secondary['label'] ) ) {
			$config['secondary']['label'] = $secondary['label'];
		}

		if ( isset( $secondary['can_add_listings'] ) ) {
			$config['secondary']['can_add_listings'] = (bool) $secondary['can_add_listings'];
		}

		if ( isset( $secondary['can_switch_role'] ) ) {
			$config['secondary']['can_switch_role'] = (bool) $secondary['can_switch_role'];
		}

		if ( ! empty( $secondary['fields'] ) && is_array( $secondary['fields'] ) ) {
			$config['secondary']['fields'] = $secondary['fields'];
		}
	}

	$config['login_captcha'] = ! empty( $new_config['login_captcha'] );
	$config['register_captcha'] = ! empty( $new_config['register_captcha'] );
	if ( isset( $new_config['default_form'] ) && in_array( $new_config['default_form'], ['primary', 'secondary'], true ) ) {
		$config['default_form'] = $new_config['default_form'];
	}

	return $config;
}

function get_preset_fields() {
	return [
		'username' => new Profile_Fields\Text_Field( [
			'slug' => 'username',
			'label' => 'Username',
			'required' => true,
			'show_in_register_form' => true,
			'show_in_account_details' => true,
		] ),

		'email' => new Profile_Fields\Email_Field( [
			'slug' => 'email',
			'label' => 'Email',
			'required' => true,
			'show_in_register_form' => true,
			'show_in_account_details' => true,
		] ),

		'password' => new Profile_Fields\Password_Field( [
			'slug' => 'password',
			'label' => 'Password',
			'required' => true,
			'show_in_register_form' => true,
			'show_in_account_details' => true,
		] ),

		'first_name' => new Profile_Fields\Text_Field( [
			'slug' => 'first_name',
			'label' => 'First Name',
			'required' => false,
			'show_in_register_form' => true,
			'show_in_account_details' => true,
		] ),

		'last_name' => new Profile_Fields\Text_Field( [
			'slug' => 'last_name',
			'label' => 'Last Name',
			'required' => false,
			'show_in_register_form' => true,
			'show_in_account_details' => true,
		] ),

		'display_name' => new Profile_Fields\Text_Field( [
			'slug' => 'display_name',
			'label' => 'Display Name',
			'required' => false,
			'show_in_register_form' => true,
			'show_in_account_details' => true,
		] ),

		'description' => new Profile_Fields\Textarea_Field( [
			'slug' => 'description',
			'label' => 'About Yourself',
			'required' => false,
			'show_in_register_form' => true,
			'show_in_account_details' => true,
			'maxlength' => 140,
		] ),

		'profile_picture' => new Profile_Fields\Profile_Picture_Field( [
			'slug' => 'profile_picture',
			'label' => 'Profile Picture',
			'required' => false,
			'show_in_register_form' => true,
			'show_in_account_details' => true,
			'allowed_mime_types' => [
				'jpg' => 'image/jpeg',
				'jpeg' => 'image/jpeg',
				'gif' => 'image/gif',
				'png' => 'image/png',
			],
		] ),

		'social_links' => new Profile_Fields\Links_Field( [
			'slug' => 'social_links',
			'label' => 'Social Networks',
			'required' => false,
			'show_in_register_form' => true,
			'show_in_account_details' => true,
		] ),
	];
}

function get_used_fields( $role ) {
	$config = mylisting()->get('roles');
	$fields = [];

	if ( ! isset( $config[ $role ] ) || empty( $config[ $role ]['fields'] ) ) {
		return [];
	}

	foreach ( (array) $config[ $role ]['fields'] as $fieldarr ) {
		$fieldclass = sprintf(
			'\MyListing\Src\User_Roles\Profile_Fields\%s_Field',
			c27()->file2class( $fieldarr['type'] )
		);

		if ( ! class_exists( $fieldclass ) ) {
			mlog()->warn( sprintf(
				'No class handler for field type %s found.',
				c27()->file2class( $fieldarr['type'] )
			) );
			continue;
		}

		$field = new $fieldclass( $fieldarr );
		$fields[ $fieldarr['slug'] ] = $field;
	}

	return $fields;
}

function get_field_types() {
	static $field_types;
	if ( is_array( $field_types ) ) {
		return $field_types;
	}

	$field_types = [
		new Profile_Fields\Text_Field,
		new Profile_Fields\Textarea_Field,
		new Profile_Fields\Password_Field,
		new Profile_Fields\Links_Field,
		new Profile_Fields\Profile_Picture_Field,
		new Profile_Fields\Email_Field,
	];

	return $field_types;
}

function get_posted_role() {
	if ( ! mylisting()->get('roles.secondary.enabled') ) {
		return 'primary';
	}

	$role = ! empty( $_POST['mylisting_user_role'] ) ? $_POST['mylisting_user_role'] : '';
	if ( $role === 'primary' ) {
		return 'primary';
	}

	if ( $role === 'secondary' ) {
		return 'secondary';
	}

	return mylisting()->get('roles.default_form');
}

function get_active_form() {
	if ( isset( $_POST['register'] ) ) {
		return 'register';
	}

	if ( isset( $_GET['register'] ) ) {
		return 'register';
	}

	return 'login';
}

function get_user_role( $user_id ) {
	$user = get_user_by( 'id', $user_id );
	if ( ! $user ) {
		return false;
	}

	if ( ! mylisting()->get('roles.secondary.enabled') ) {
		return 'primary';
	}

	return in_array( 'customer_alt', (array) $user->roles, true )
		? 'secondary' : 'primary';
}

function get_current_user_role() {
	return get_user_role( get_current_user_id() );
}

function user_has_cap( $capability ) {
	$current_user = wp_get_current_user();
	if ( empty( $current_user ) ) {
		return false;
	}

	// check capabilities only for regular user roles
	if ( ! array_intersect( ['subscriber', 'customer', 'customer_alt'], (array) $current_user->roles )  ) {
		return true;
	}

	return current_user_can( $capability );
}

function user_can_add_listings() {
	return user_has_cap('mylisting_can_add_listings');
}

function user_can_switch_role() {
	$role = get_current_user_role();
	return user_has_cap('mylisting_can_switch_role') && ( $role === 'secondary' || (
		$role === 'primary' && mylisting()->get('roles.secondary.enabled')
	) );
}
