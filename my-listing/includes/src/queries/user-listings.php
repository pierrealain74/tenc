<?php

namespace MyListing\Src\Queries;

class User_Listings extends Query {
	use \MyListing\Src\Traits\Instantiatable;

	public $action = 'get_listings_by_author';

	public function handle() {
		check_ajax_referer( 'c27_ajax_nonce', 'security' );

		if ( empty( $_POST['auth_id'] )) {
			return false;
		}

		$page = absint( isset( $_POST['page'] ) ? $_POST['page'] : 0 );
		$per_page = absint( isset( $_POST['per_page'] ) ? $_POST['per_page'] : 9 );

		return $this->send( [
			'order' => sanitize_text_field( isset($_POST['order']) ? $_POST['order'] : 'DESC' ),
			'offset' => $page * $per_page,
			'orderby' => sanitize_text_field( isset($_POST['orderby']) ? $_POST['orderby'] : 'date' ),
			'posts_per_page' => $per_page,
			'author' => absint( $_POST['auth_id'] ),
			'fields' => 'ids',
			'output' => [
				'item-wrapper' => 'col-md-4 col-sm-6 col-xs-12 grid-item',
			],
		] );
	}
}
