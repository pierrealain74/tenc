<?php

echo '<div class="actions">';
$admin_actions = apply_filters( 'post_row_actions', array(), $post );

if ( in_array( $post->post_status, array( 'pending', 'pending_payment' ), true ) && current_user_can( 'publish_post', $post->ID ) ) {
	$admin_actions['approve'] = array(
		'action' => 'approve',
		'name'   => __( 'Approve', 'my-listing' ),
		'url' => add_query_arg( [
			'action' => 'approve_listings',
			'post' => [ $post->ID ],
			'_wpnonce' => wp_create_nonce( 'bulk-posts' ),
		], admin_url( 'edit.php?post_type=job_listing' ) ),
	);
}

if ( 'trash' !== $post->post_status ) {
	if ( current_user_can( 'read_post', $post->ID ) ) {
		$admin_actions['view'] = array(
			'action' => 'view',
			'name'   => __( 'View', 'my-listing' ),
			'url'    => get_permalink( $post->ID ),
		);
	}
	if ( current_user_can( 'edit_post', $post->ID ) ) {
		$admin_actions['edit'] = array(
			'action' => 'edit',
			'name'   => __( 'Edit', 'my-listing' ),
			'url'    => get_edit_post_link( $post->ID ),
		);
	}
	if ( current_user_can( 'delete_post', $post->ID ) ) {
		$admin_actions['delete'] = array(
			'action' => 'delete',
			'name'   => __( 'Delete', 'my-listing' ),
			'url'    => get_delete_post_link( $post->ID ),
		);
	}
	if ( current_user_can( 'edit_post', $post->ID ) ) {
		$admin_actions['duplicate'] = array(
			'action' => 'duplicate',
			'name' => __( 'Duplicate', 'my-listing' ),
			'url' => add_query_arg( [
				'action' => 'mylisting_duplicate_item',
				'listing_id' => $post->ID,
			], wp_nonce_url( admin_url( 'admin-post.php' ), 'mylisting_duplicate_item' ) ),
		);
	}
}

$admin_actions = apply_filters( 'job_manager_admin_actions', $admin_actions, $post );

foreach ( $admin_actions as $action ) {
	if ( is_array( $action ) ) {
		printf(
			'<a class="button button-icon _icon-%1$s" href="%2$s" title="%3$s">%4$s</a>',
			esc_attr( $action['action'] ),
			esc_url( $action['url'] ),
			esc_attr( $action['name'] ),
			esc_html( $action['name'] )
		);
	} else {
		echo wp_kses_post( str_replace( 'class="', 'class="button ', $action ) );
	}
}

echo '</div>';