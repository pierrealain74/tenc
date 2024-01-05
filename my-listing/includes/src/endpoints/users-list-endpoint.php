<?php

namespace MyListing\Src\Endpoints;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Users_List_Endpoint {

	public function __construct() {
		add_action( 'mylisting_ajax_mylisting_list_users', [ $this, 'handle' ] );
	}

	/**
	 * Retrieve a list of users with the given args.
	 * For use in select/multiselect fields.
	 *
	 * @since 2.0
	 */
	public function handle() {
		mylisting_check_ajax_referrer();

		try {
			$page = ! empty( $_REQUEST['page'] ) ? ( absint( $_REQUEST['page'] ) - 1 ) : 0;
			$search = ! empty( $_REQUEST['search'] ) ? sanitize_text_field( $_REQUEST['search'] ) : '';
			$per_page = 25;

			$args = [
				'number' => $per_page,
				'offset' => $page * $per_page,
				'fields' => [ 'ID', 'user_login', 'display_name' ],
				'orderby' => 'display_name',
				'order' => 'ASC',
			];

			if ( ! empty( trim( $search ) ) ) {
				$args['search'] = '*'.trim( $search ).'*';
			}

			$users = get_users( $args );
			if ( empty( $users ) || is_wp_error( $users ) ) {
				throw new \Exception( _x( 'No users found.', 'Users dropdown list', 'my-listing' ) );
			}

			$results = [];
			foreach ( $users as $user ) {
				$results[] = [
					'id' => $user->ID,
					'text' => $user->display_name,
				];
			}

			wp_send_json( [
				'success' => true,
				'results' => $results,
				'more' => count( $results ) === $per_page,
				'args' => \MyListing\is_dev_mode() ? $args : [],
			] );
		} catch ( \Exception $e ) {
			wp_send_json( [
				'success' => false,
				'message' => $e->getMessage(),
			] );
		}
	}
}