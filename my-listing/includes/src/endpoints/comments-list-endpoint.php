<?php

namespace MyListing\Src\Endpoints;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Comments_List_Endpoint {

	public function __construct() {
		add_action( 'mylisting_ajax_list_comments', [ $this, 'handle' ] );
		add_action( 'mylisting_ajax_nopriv_list_comments', [ $this, 'handle' ] );
	}

	/**
	 * Retrieve listing comments for pagination.
	 *
	 * @since 2.6.1
	 */
	public function handle() {
		try {
			$page = ! empty( $_REQUEST['page'] ) ? ( absint( $_REQUEST['page'] ) - 1 ) : 0;
			$page_count = ! empty( $_REQUEST['page_count'] ) ? ( absint( $_REQUEST['page_count'] ) - 1 ) : 0;
			$post_id = ! empty( $_REQUEST['post_id'] ) ? absint( $_REQUEST['post_id'] ) : null;
			$direction = ! empty( $_REQUEST['direction'] ) && $_REQUEST['direction'] === 'upper' ? 'upper' : 'lower';
			$per_page = (int) get_option( 'comments_per_page' );
			$post = get_post( $post_id );
			if ( is_null( $post_id ) || ! $post instanceof \WP_Post ) {
				throw new \Exception( _x( 'Post not found.', 'Comments list', 'my-listing' ) );
			}

			$comments = get_comments( [
				'post_id' => $post_id,
				'offset' => $page * $per_page,
				'number' => $per_page,
				'order' => get_option( 'comment_order' ) === 'DESC' ? 'DESC' : 'ASC',
				'hierarchical' => 'flat',
				'status' => 'approve',
			] );

			if ( ! comments_open( $post_id ) || empty( $comments ) ) {
				echo ''; die;
			}

			ob_start();
			wp_list_comments( [
				'walker' => new \MyListing\Ext\Reviews\Walker,
				'type' => 'all',
				'reverse_top_level' => get_option( 'comment_order' ) === 'desc',
			], $comments );
			echo ob_get_clean();
			die;
		} catch ( \Exception $e ) {
			wp_send_json( [
				'success' => false,
				'message' => $e->getMessage(),
			] );
		}
	}
}
