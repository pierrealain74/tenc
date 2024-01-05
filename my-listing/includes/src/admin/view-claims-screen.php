<?php

namespace MyListing\Src\Admin;

if ( ! defined('ABSPATH') ) {
	exit;
}

class View_Claims_Screen {
	use \MyListing\Src\Traits\Instantiatable;

	public function __construct() {
		if ( ! ( is_admin() && mylisting_get_setting( 'claims_enabled' ) ) ) {
			return;
		}

		// add to wp admin menu
		add_action( 'admin_menu', [ $this, 'add_claim_as_listings_submenu' ], 99 );
		add_filter( 'parent_file', [ $this, 'set_claim_parent_menu_edit_screen' ] );

		// columns
		add_filter( 'manage_claim_posts_columns', [ $this, 'claim_posts_columns' ] );
		add_action( 'manage_claim_posts_custom_column',  [ $this, 'claim_posts_custom_column' ], 5, 2 );
		add_filter( 'post_row_actions', [ $this, 'remove_claim_quick_edit' ], 10, 2 );
		add_filter( 'bulk_actions-edit-claim', [ $this, 'remove_claim_bulk_action_edit' ] );
		add_action( 'restrict_manage_posts', [ $this, 'add_filters' ] );
		add_filter( 'request', [ $this, 'sort_columns' ] );

		// status metabox
		add_action( 'add_meta_boxes', [ $this, 'add_claim_status_meta_box' ] );
		add_action( 'save_post', [ $this, 'save_claim_status_meta_box' ], 10, 2 );
	}

	/**
	 * Add `Claims` page in WP Admin > Listings menu.
	 *
	 * @since 1.6
	 * @link  https://shellcreeper.com/how-to-add-wordpress-cpt-admin-menu-as-sub-menu/
	 */
	public function add_claim_as_listings_submenu() {
		$cpt_obj = get_post_type_object( 'claim' );
		if ( $cpt_obj ) {
			add_submenu_page(
				'edit.php?post_type=job_listing',
				$cpt_obj->labels->name,
				$cpt_obj->labels->menu_name,
				$cpt_obj->cap->edit_posts,
				'edit.php?post_type=claim'
			);
		}
	}

	/**
	 * Set claim parent menu edit screen.
	 *
	 * @since 1.6
	 */
	public function set_claim_parent_menu_edit_screen( $parent_file ) {
		global $current_screen;
		if ( in_array( $current_screen->base, [ 'post', 'edit' ] ) && 'claim' === $current_screen->post_type ) {
			$parent_file = 'edit.php?post_type=job_listing';
		}
		return $parent_file;
	}

	/**
	 * Claim Columns
	 *
	 * @since 1.6
	 */
	public function claim_posts_columns( $columns ) {
		return [
			'cb'           => $columns['cb'],
			'title'        => esc_html__( 'Claim ID', 'my-listing' ),
			'listing'      => esc_html__( 'Listing', 'my-listing' ),
			'claimer'      => esc_html__( 'Claimer', 'my-listing' ),
			'user_package' => esc_html__( 'Package', 'my-listing' ),
			'date'         => $columns['date'],
		];
	}

	/**
	 * Claim Custom Columns.
	 *
	 * @since 1.6
	 */
	public function claim_posts_custom_column(  $column, $post_id  ) {
		$listing_id = absint( get_post_meta( $post_id, '_listing_id', true ) );
		$listing = \MyListing\Src\Listing::get( $listing_id );
		$user_id = absint( get_post_meta( $post_id, '_user_id', true ) );
		$user = get_userdata( $user_id );

		switch ( $column ) {
			case 'listing':
				echo $listing
					? sprintf( '<a target="_blank" href="%s">#%d - %s</a>', esc_url( get_edit_post_link( $listing_id ) ), $listing_id, $listing->get_name() )
					: esc_html__( 'n/a', 'my-listing' );
			break;

			case 'claimer':
				if ( ! ( $user_id && $user ) ) {
					echo esc_html__( 'n/a', 'my-listing' );
				} else {
					printf( '<a target="_blank" href="%s">#%d</a> - %s (%s)', esc_url( get_edit_user_link( $user_id ) ), $user_id, $user->user_login, $user->user_email );
				}
			break;

			case 'user_package':
				$link = esc_html__( 'n/a', 'my-listing' );
				$package_id = absint( get_post_meta( $post_id, '_user_package_id', true ) );
				$package = $package_id ? get_post( $package_id ) : false;
				if ( $package && 'case27_user_package' === $package->post_type ) {
					$link = '<a target="_blank" href="' . esc_url( get_edit_post_link( $package_id ) ) . '">' . get_the_title( $package_id ) . '</a>';
				}
				echo $link;
			break;
		}
	}

	/**
	 * Remove Quick Edit.
	 *
	 * @since 1.6
	 */
	public function remove_claim_quick_edit( $actions, $post ) {
		if ( 'claim' === $post->post_type ) {
			unset( $actions['inline hide-if-no-js'] );
		}
		return $actions;
	}

	/**
	 * Remove Claim Edit Bulk Actions
	 *
	 * @since 1.6
	 */
	public function remove_claim_bulk_action_edit( $actions ) {
		unset( $actions['edit'] );
		return $actions;
	}

	/**
	 * Add Claim Status Meta Box
	 *
	 * @since 1.6
	 */
	public function add_claim_status_meta_box() {
		add_meta_box(
			$id         = 'case27_paid_listing_claim_status_meta_box',
			$title      = __( 'Claim Status', 'my-listing' ),
			$callback   = array( $this, 'claim_status_meta_box' ),
			$screen     = array( 'claim' ),
			$context    = 'side',
			$priority   = 'high'
		);
	}

	/**
	 * Claim Status Meta Box
	 *
	 * @since 1.6
	 */
	public function claim_status_meta_box( $post, $box ) {
		global $user_ID, $hook_suffix;
		$post_id = $post->ID;
		$statuses = \MyListing\Src\Claims\Claims::get_valid_statuses();
		$status = get_post_meta( $post_id, '_status', true );
		$status = isset( $statuses[ $status ] ) ? $status : 'pending';
		?>
		<p>
			<select id="claim-status" class="widefat" name="_status" autocomplete="off" data-old-status="<?php echo esc_attr( $status ); ?>">
				<?php foreach ( $statuses as $key => $label ) : ?>
					<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $status, $key ); ?>><?php echo esc_html( $label ); ?></option>
				<?php endforeach; ?>
			</select>
			<?php wp_nonce_field( "case27_claim_status_{$post_id}", '_claim_status_nonce' ); ?>
		</p>

		<?php if ( 'post.php' == $hook_suffix ) : // Post already saved, show notification option. ?>
			<div id="claim-notification-field">
				<ul>
					<li>
						<label><input name="_send_claim_email" type="checkbox" value="yes" checked="checked"> <?php esc_html_e( 'Send claimer status updates via email.', 'my-listing' ); ?></label>
					</li>
				</ul>
			</div>
		<?php endif; ?>
		<style>
			#misc-publishing-actions{display:none !important;}
			#minor-publishing-actions{padding:0 !important;}
			#major-publishing-actions{border:none !important;}
		</style>
		<?php
	}

	/**
	 * Save Claim Status Meta Box
	 *
	 * @since 1.6
	 */
	public function save_claim_status_meta_box( $post_id, $post ) {
		if ( ! isset( $_POST['_claim_status_nonce'], $_POST['_status'] ) || ! wp_verify_nonce( $_POST['_claim_status_nonce'], "case27_claim_status_{$post_id}" ) ) {
			return;
		}

		// Save status.
		$statuses   = \MyListing\Src\Claims\Claims::get_valid_statuses();
		$old_status = get_post_meta( $post_id, '_status', true );
		$new_status = $_POST['_status'];
		$new_status = isset( $statuses[ $new_status ] ) ? $new_status : $old_status;

		// Update Status.
		if ( $new_status && $new_status !== $old_status ) {
			update_post_meta( $post_id, '_status', $new_status );

			if ( 'approved' === $new_status ) {
				\MyListing\Src\Claims\Claims::approve( $post_id );
			}

			$should_send_email = isset( $_POST['_send_claim_email'] ) && $_POST['_send_claim_email'] === 'yes';
			do_action( 'mylisting/admin/claim:updated', $post_id, $should_send_email );
		}
	}

	/**
	 * Display Claims table filters.
	 *
	 * @since 2.1
	 */
	public function add_filters() {
		$screen = get_current_screen();
		if ( ! ( $screen && $screen->id === 'edit-claim' ) ) {
			return;
		}

		$status = ! empty( $_GET['claim_status'] ) ? $_GET['claim_status'] : 'pending';
		if ( ! in_array( $status, [ 'pending', 'approved', 'declined', 'any' ] ) ) {
			$status = 'pending';
		} ?>
		<select name="claim_status" id="claim_status">
			<option value="any" <?php selected( $status, 'any' ) ?>><?php _e( 'All Claims', 'my-listing' ) ?></option>
			<option value="approved" <?php selected( $status, 'approved' ) ?>><?php _e( 'Approved Claims', 'my-listing' ) ?></option>
			<option value="pending" <?php selected( $status, 'pending' ) ?>><?php _e( 'Pending Claims', 'my-listing' ) ?></option>
			<option value="declined" <?php selected( $status, 'declined' ) ?>><?php _e( 'Declined Claims', 'my-listing' ) ?></option>
		</select>
	<?php }

	/**
	 * Handle sort query for custom sortable columns.
	 *
	 * @since 2.1
	 */
	public function sort_columns( $vars ) {
		$screen = get_current_screen();
		if ( ! ( $screen && $screen->id === 'edit-claim' ) ) {
			return $vars;
		}

		$status = ! empty( $_GET['claim_status'] ) ? $_GET['claim_status'] : 'pending';
		if ( in_array( $status, [ 'pending', 'approved', 'declined' ] ) ) {
			$vars = array_merge( $vars, [
				'meta_key' => '_status',
				'meta_value' => $status,
			] );
		}

		return $vars;
	}
}
