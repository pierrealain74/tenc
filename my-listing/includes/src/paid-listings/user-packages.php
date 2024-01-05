<?php
/**
 * User Packages
 *
 * @since 1.6
 */

namespace MyListing\Src\Paid_Listings;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class User_Packages {
	use \MyListing\Src\Traits\Instantiatable;

	public function __construct() {

		// Register user package post type.
		add_action( 'init', [ $this, 'register_user_package_post_type' ] );

		// Register custom post statuses.
		add_action( 'init', [ $this, 'register_user_package_statuses' ], 7 );
		foreach ( [ 'post', 'post-new' ] as $hook ) {
			add_action( "admin_footer-{$hook}.php", [ $this, 'extend_submitdiv_post_status' ] );
		}

		// Add this menu to listings.
		add_action( 'admin_menu',  [ $this, 'add_user_packages_as_listings_submenu' ], 51 );

		// Fix active menu when visiting user package screen.
		add_filter( 'parent_file', [ $this, 'set_user_package_parent_menu_edit_screen' ] );
		add_filter( 'submenu_file', [ $this, 'set_user_package_submenu_edit_screen' ] );

		// Add title.
		add_filter( 'the_title', [ $this, 'user_package_title' ], 10, 2 );
		add_action( 'edit_form_after_title', [ $this, 'display_package_id_edit_screen' ] );

		// Admin columns.
		add_filter( 'manage_case27_user_package_posts_columns',  [ $this, 'user_package_posts_columns' ] );
		add_action( 'manage_case27_user_package_posts_custom_column',  [ $this, 'user_package_posts_custom_column' ], 5, 2 );
		add_filter( 'post_row_actions', [ $this, 'remove_user_package_quick_edit' ], 10, 2 );
		add_filter( 'bulk_actions-edit-case27_user_package', [ $this, 'remove_user_package_bulk_action_edit' ] );

		// Delete packages with user.
		add_action( 'deleted_user', [ $this, 'delete_user_packages_with_user' ], 10, 2 );

		// Save post action.
		add_action( 'save_post', [ $this, 'save_user_package' ], 99, 2 );

		// @todo: Handle cases when listing goes from published to pending when edited (new wpjm setting).
		// Decrease package count for listings that go from pending approval to trash.
		add_action( 'pending_to_trash', [ $this, 'pending_to_trash' ] );

		// Increase package count when listing is untrashed and status is set to pending approval.
		add_action( 'trash_to_pending', [ $this, 'trash_to_pending' ] );

		if ( is_admin() ) {
			add_action( 'request', [ $this, 'add_keyword_search' ] );
		}
	}

	/**
	 * Register Post Type for User Packages.
	 *
	 * @since 1.0.0
	 * @link https://codex.wordpress.org/Function_Reference/register_post_type
	 */
	public static function register_user_package_post_type() {
		$args = array(
			'description'           => '',
			'public'                => false, // Private.
			'publicly_queryable'    => false,
			'show_in_nav_menus'     => false,
			'show_in_admin_bar'     => false,
			'exclude_from_search'   => false, // Need this for WP_Query.
			'show_ui'               => true,
			'show_in_menu'          => false,
			//'menu_position'         => 99,
			'menu_icon'             => 'dashicons-screenoptions',
			'can_export'            => true,
			'delete_with_user'      => false,
			'hierarchical'          => false,
			'has_archive'           => false,
			'query_var'             => true,
			'rewrite'               => false,
			'capability_type'       => 'page',
			'supports'              => array( '' ),
			'labels'                => array(
				'name'                      => __( 'Packages', 'my-listing' ),
				'singular_name'             => __( 'Package', 'my-listing' ),
				'add_new'                   => __( 'Add New', 'my-listing' ),
				'add_new_item'              => __( 'Add New Package', 'my-listing' ),
				'edit_item'                 => __( 'Edit Package', 'my-listing' ),
				'new_item'                  => __( 'New Package', 'my-listing' ),
				'all_items'                 => __( 'All Packages', 'my-listing' ),
				'view_item'                 => __( 'View Package', 'my-listing' ),
				'search_items'              => __( 'Search Packages', 'my-listing' ),
				'not_found'                 => __( 'Not Found', 'my-listing' ),
				'not_found_in_trash'        => __( 'Not Found in Trash', 'my-listing' ),
				'menu_name'                 => __( 'Paid Listing Packages', 'my-listing' ),
			),
		);

		register_post_type( 'case27_user_package', apply_filters( 'case27_user_package_register_post_type_args', $args ) );
	}

	/**
	 * Register User Package Statuses
	 *
	 * @since 1.0.0
	 */
	public function register_user_package_statuses() {
		register_post_status( 'case27_full', array(
			'label'                     => esc_html__( 'Full', 'my-listing' ),
			'public'                    => false,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => false,
			'show_in_admin_status_list' => true,
			// translators: %s is label count.
			'label_count'               => _n_noop( 'Full <span class="count">(%s)</span>', 'Full <span class="count">(%s)</span>', 'my-listing' ),
		) );
		register_post_status( 'case27_cancelled', array(
			'label'                     => esc_html__( 'Order Cancelled', 'my-listing' ),
			'public'                    => false,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => false,
			'show_in_admin_status_list' => true,
			// translators: %s is label count.
			'label_count'               => _n_noop( 'Order Cancelled <span class="count">(%s)</span>', 'Order Cancelled <span class="count">(%s)</span>', 'my-listing' ),
		) );
	}


	/**
	 * Adds post status to the "submitdiv" Meta Box and post type WP List Table screens.
	 * Based on code by franz-josef-kaiser
	 *
	 * @since 1.0.0
	 *
	 * @link https://gist.github.com/franz-josef-kaiser/2930190
	 */
	public function extend_submitdiv_post_status() {
		global $post, $post_type;

		// Abort if we're on the wrong post type, but only if we got a restriction
		if ( 'case27_user_package' !== $post_type ) {
			return;
		}

		$statuses = \MyListing\Src\Package::get_statuses();

		// Get all non-builtin post status and add them as <option>
		$options = $display = '';
		foreach ( $statuses as $status => $name ) {
			$selected = selected( $post->post_status, $status, false );

			// If we one of our custom post status is selected, remember it
			$selected AND $display = $name;

			// Build the options
			$options .= "<option{$selected} value='{$status}'>{$name}</option>";
		}
		?>
		<script type="text/javascript">
			jQuery( document ).ready( function($) {
				<?php if ( ! empty( $display ) ) : ?>
					jQuery( '#post-status-display' ).html( '<?php echo $display; ?>' );
				<?php endif; ?>

				var select = jQuery( '#post-status-select' ).find( 'select' );
				jQuery( select ).html( "<?php echo $options; ?>" );
			} );
		</script>
		<?php
	}

	/**
	 * Add Listing Packages as Listings Submenu.
	 *
	 * @since 1.0.0
	 * @link https://shellcreeper.com/how-to-add-wordpress-cpt-admin-menu-as-sub-menu/
	 */
	public function add_user_packages_as_listings_submenu() {
		$cpt_obj = get_post_type_object( 'case27_user_package' );
		add_submenu_page(
			'users.php',                              // Parent slug.
			$cpt_obj->labels->name,                   // Page title.
			$cpt_obj->labels->menu_name,              // Menu title.
			$cpt_obj->cap->edit_posts,                // Capability.
			'edit.php?post_type=case27_user_package'  // Menu slug.
		);
	}

	/**
	 * Set user package parent menu edit screen.
	 *
	 * @since 1.0.0
	 *
	 * @param string $parent_file Parent menu slug.
	 * @return string
	 */
	public function set_user_package_parent_menu_edit_screen( $parent_file ) {
		global $current_screen;
		if ( in_array( $current_screen->base, [ 'post', 'edit' ] ) && 'case27_user_package' === $current_screen->post_type ) {
			$parent_file = 'users.php';
		}
		return $parent_file;
	}

	/**
	 * Set active sub menu when visiting parent menu edit screen.
	 *
	 * @since 1.0.0
	 *
	 * @param string $submenu_file Active submenu slug.
	 * @return string
	 */
	public function set_user_package_submenu_edit_screen( $submenu_file ) {
		global $current_screen;
		if ( in_array( $current_screen->base, [ 'post', 'edit' ] ) && 'case27_user_package' === $current_screen->post_type ) {
			$submenu_file = 'edit.php?post_type=case27_user_package';
		}
		return $submenu_file;
	}

	/**
	 * User Package Title.
	 * User package post type do not support title, but admin still show it.
	 *
	 * @since 1.0.0
	 *
	 * @param string $title The title string.
	 * @param int    $id    Post ID.
	 * @return string
	 */
	public function user_package_title( $title, $id = null ) {
		if ( ! $id || 'case27_user_package' !== get_post_type( $id ) ) {
			return $title;
		}

		$statuses = \MyListing\Src\Package::get_statuses();
		$status = get_post_status( $id );
		$status = isset( $statuses[ $status ] ) ? $statuses[ $status ] : $status;

		return "#{$id} - {$status}";
	}

	/**
	 * Display Package ID in Edit Screen
	 *
	 * @since 1.0.0
	 */
	public function display_package_id_edit_screen( $post ) {
		if ( $post && $post->ID && 'case27_user_package' === $post->post_type && isset( $_GET['action'] ) && 'edit' === $_GET['action'] ) {
			?>
			<h1 class="wp-heading-inline-package"><?php printf( __( 'Edit Package #%d', 'my-listing' ), $post->ID ); ?> <a href="<?php echo esc_url( add_query_arg( 'post_type','case27_user_package', admin_url( 'post-new.php' ) ) ); ?>" class="page-title-action"><?php esc_html_e( 'Add New', 'my-listing' ); ?></a></h1>
			<style>.wrap h1.wp-heading-inline {display:none;} .wrap > .page-title-action {display:none;} #poststuff {margin-top: 30px;}</style>
			<?php
		}
	}

	/**
	 * User Packages Columns
	 *
	 * @since 1.0.0
	 *
	 * @param array $columns Post Columns.
	 * @return array
	 */
	public function user_package_posts_columns( $columns ) {
		unset( $columns['date'] );
		$columns['title']         = esc_html__( 'Package ID', 'my-listing' );
		$columns['user']          = esc_html__( 'User', 'my-listing' );
		$columns['limit']         = esc_html__( 'Limit', 'my-listing' );
		$columns['duration']      = esc_html__( 'Duration', 'my-listing' );
		$columns['featured']      = esc_html__( 'Featured', 'my-listing' );
		$columns['use_for_claim'] = esc_html__( 'Use for Claim', 'my-listing' );
		$columns['product']       = esc_html__( 'Product', 'my-listing' );
		$columns['order']         = esc_html__( 'Order ID', 'my-listing' );
		return $columns;
	}

	/**
	 * User Packages Custom Columns.
	 *
	 * @since 1.0.0
	 *
	 * @param string $column  Column ID.
	 * @param int    $post_id Post ID.
	 */
	public function user_package_posts_custom_column(  $column, $post_id  ) {
		switch ( $column ) {

			case 'user':
				$title = esc_html__( 'n/a', 'my-listing' );
				$user_id = absint( get_post_meta( $post_id, '_user_id', true ) );
				if ( $user_id ) {
					$user = get_userdata( $user_id );
					if ( $user ) {
						$user_id = '<a target="_blank" href="' . esc_url( get_edit_user_link( $user_id ) ) . '">#' . $user_id . '</a>';
						$title = "{$user_id} - {$user->user_login} ({$user->user_email})";
					}
				}

				echo $title;
			break;

			case 'limit':
				$count = absint( get_post_meta( $post_id, '_count', true ) );
				$limit = absint( get_post_meta( $post_id, '_limit', true ) );

				$package_count = $count ? sprintf( __( '%s Posted', 'my-listing' ), $count ) : '';
				$package_limit = $limit ? $limit : __( 'Unlimited', 'my-listing' );

				$text = $package_count ? $package_count . ' / ' . $package_limit : $package_limit;

				$url = add_query_arg( array(
					'post_type'        => 'job_listing',
					'_user_package_id' => $post_id,
				), admin_url( 'edit.php' ) );

				echo '<a target="_blank" href="' . esc_url( $url ) . '">' . $text . '</a>';
			break;

			case 'duration':
				$duration = absint( get_post_meta( $post_id, '_duration', true ) );
				echo $duration ? sprintf( __( '%s Days', 'my-listing' ), $duration ) : '&ndash;';
			break;

			case 'featured':
				$featured = get_post_meta( $post_id, '_featured', true );
				echo $featured ? __( 'Yes', 'my-listing' ) : __( 'No', 'my-listing' );
			break;

			case 'use_for_claim':
				$claim = get_post_meta( $post_id, '_use_for_claims', true );
				echo $claim ? __( 'Yes', 'my-listing' ) : __( 'No', 'my-listing' );
			break;

			case 'product':
				$link = esc_html__( 'n/a', 'my-listing' );
				$product_id = get_post_meta( $post_id, '_product_id', true );
				if ( $product_id ) {
					$product = wc_get_product( $product_id );
					if ( $product ) {
						$link = '<a target="_blank" href="' . esc_url( get_edit_post_link( $product_id ) ) . '">' . $product->get_name() . '</a>';
					}
				}
				echo $link;
			break;

			case 'order':
				$link = esc_html__( 'n/a', 'my-listing' );
				$order_id = absint( get_post_meta( $post_id, '_order_id', true ) );
				if ( $order_id ) {
					$link = '<a target="_blank" href="' . esc_url( get_edit_post_link( $order_id ) ) . '">#' . $order_id . '</a>';
				}
				echo $link;
			break;
		}
	}

	/**
	 * Remove User Packages Quick Edit.
	 *
	 * @since 1.0.0
	 *
	 * @param array   $actions Row Actions.
	 * @param WP_Post #post    Post Object.
	 * @return array
	 */
	public function remove_user_package_quick_edit( $actions, $post ) {
		if ( 'case27_user_package' === $post->post_type ) {
			unset( $actions['inline hide-if-no-js'] );
		}
		return $actions;
	}

	/**
	 * Remove User Packages Edit Bulk Actions
	 *
	 * @since 1.0.0
	 *
	 * @param array $actions Actions list.
	 * @return array
	 */
	public function remove_user_package_bulk_action_edit( $actions ) {
		unset( $actions['edit'] );
		return $actions;
	}

	/**
	 * Delete User Packages when user is deleted.
	 *
	 * @since 1.0.0
	 *
	 * @param int      $id       ID of the deleted user.
	 * @param int|null $reassign ID of the user to reassign posts and links to.
	 */
	public function delete_user_packages_with_user( $id, $reassign ) {
		$packages = get_posts( [
			'post_type'        => 'case27_user_package',
			'post_status'      => 'any',
			'posts_per_page'   => -1,
			'suppress_filters' => false,
			'fields'           => 'ids',
			'meta_query' => [ [
				'key'     => '_user_id',
				'value'   => $id,
				'compare' => 'IN',
			] ],
		] );

		foreach ( (array) $packages as $package_id ) {
			wp_delete_post( $package_id, false ); // Move to trash.
		}
	}

	/**
	 * Save User Package
	 * Currently only to set post status.
	 *
	 * @param int     $post_id User Package ID.
	 * @param WP_Post $post    Post Object
	 */
	public function save_user_package( $post_id, $post = null ) {
		$package = \MyListing\Src\Package::get( $post_id );
		if ( ! $package ) {
			return;
		}

		remove_action( 'save_post', [ $this, __FUNCTION__ ] );
		$package->maybe_update_status();
		add_action( 'save_post', [ $this, __FUNCTION__ ] );
	}

	/**
	 * Decrease package count for listings that go from pending approval to trash.
	 * This should only have effect if done from the WP Admin backend, so regular users
	 * can't change package counts by deleting their listings.
	 *
	 * @since 1.6
	 */
	public function pending_to_trash( $post ) {
		$listing = \MyListing\Src\Listing::get( $post );
		if ( ! ( is_admin() && $listing && $listing->get_package() ) ) {
			return;
		}

		$listing->get_package()->decrease_count();
	}

	/**
	 * Increase package count when listing is untrashed and status is set to pending approval.
	 * This should only have effect if done from the WP Admin backend, so regular users
	 * can't change package counts by deleting their listings.
	 *
	 * @since 1.6
	 */
	public function trash_to_pending( $post ) {
		$listing = \MyListing\Src\Listing::get( $post );
		if ( ! ( is_admin() && $listing && $listing->get_package() ) ) {
			return;
		}

		$listing->get_package()->increase_count();
	}

	/**
	 * Search packages by keyword.
	 *
	 * @since 2.6.5
	 */
	public function add_keyword_search( $vars ) {
		$screen = get_current_screen();
		if ( ! ( $screen && $screen->id === 'edit-case27_user_package' ) || empty( $vars['s'] ) ) {
			return $vars;
		}

		add_filter( 'posts_join', function( $join ) {
			global $wpdb;
			$join .= " LEFT JOIN {$wpdb->users} AS user ON ( {$wpdb->posts}.post_author = user.ID ) ";
			return $join;
		} );

		add_filter( 'posts_search', function( $search ) use ( $vars ) {
			global $wpdb;
			$keyword = sanitize_text_field( $vars['s'] );
			$keyword_query = $wpdb->prepare( "
				OR (
					user.user_login LIKE %s OR
					user.user_nicename LIKE %s OR
					user.user_email LIKE %s OR
					user.display_name LIKE %s
				)
			", '%'.$keyword.'%', '%'.$keyword.'%', '%'.$keyword.'%', '%'.$keyword.'%' );

			$search = str_replace(
				"OR ({$wpdb->posts}.post_excerpt",
				"{$keyword_query} OR ({$wpdb->posts}.post_excerpt",
				$search
			);

			return $search;
		} );

		return $vars;
	}

}
