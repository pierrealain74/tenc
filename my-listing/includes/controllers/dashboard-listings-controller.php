<?php

namespace MyListing\Controllers;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Dashboard_Listings_Controller extends Base_Controller {

	private $pending_orders = [];

	protected function hooks() {
		$this->on( 'mylisting/dashboard/endpoints-init', '@add_endpoint' );
		$this->on( 'template_redirect', '@handle_actions' );
		$this->on( 'mylisting/user-listings/handle-action:delete', '@delete_listing' );
		$this->on( 'mylisting/user-listings/handle-view:edit', '@edit_listing' );
		$this->on( 'mylisting/user-listings/actions', '@display_edit_action', 20 );
		$this->on( 'mylisting/user-listings/actions', '@display_duplicate_action', 70 );
		$this->on( 'mylisting/user-listings/actions', '@display_delete_action', 90 );
		$this->on( 'mylisting/user-listings/actions', '@display_pending_payment_actions', 80 );
	}

	protected function add_endpoint( $endpoints ) {
		$endpoints->add_page( [
			'endpoint' => \MyListing\my_listings_endpoint_slug(),
			'title' => __( 'My Listings', 'my-listing' ),
			'template' => function() {
				$this->get_template();
			},
			'show_in_menu' => true,
			'order' => 2,
		] );
	}

	private function get_template() {
		// if doing an action, show conditional content if needed
		if ( ! empty( $_REQUEST['action'] ) ) {
			$action = sanitize_title( $_REQUEST['action'] );
			if ( has_action( 'mylisting/user-listings/handle-view:'.$action ) ) {
				return do_action( 'mylisting/user-listings/handle-view:'.$action );
			}
		}

		$allowed_statuses = [ 'publish', 'pending', 'pending_payment', 'expired', 'preview' ];
		$default_statuses = apply_filters(
			'mylisting/dashboard/default-listing-statuses',
			$allowed_statuses
		);

		// filter by listing status
		if ( ! empty( $_GET['status'] ) && in_array( $_GET['status'], $allowed_statuses, true ) ) {
			$active_status = $_GET['status'];
		} else {
			$active_status = 'all';
		}

		if ( $active_status === 'pending_payment' ) {
			$this->pending_orders = $this->get_pending_orders();
		}

		// get user listings
		$query = new \WP_Query;
		$query_args = [
			'post_type' => 'job_listing',
			'post_status' => $active_status === 'all' ? $default_statuses : $active_status,
			'ignore_sticky_posts' => 1,
			'posts_per_page' => 12,
			'paged' => ! empty( $_GET['pg'] ) ? absint( $_GET['pg'] ) : 1,
			'orderby' => 'date',
			'order' => 'DESC',
			'author' => get_current_user_id(),
		];

		// filter by listing type
		$active_type = '';
		if ( ! empty( $_GET['filter_by_type'] ) ) {
			if ( $type = \MyListing\Src\Listing_Type::get_by_name( $_GET['filter_by_type'] ) ) {
				$query_args['meta_key'] = '_case27_listing_type';
				$query_args['meta_value'] = $type->get_slug();
				$active_type = $type->get_slug();
			}
		}

		// get listing types relevant to the current user and sort alphabetically
		$listing_types = $this->get_types_relevant_to_current_user();
		usort( $listing_types, function( $a, $b ) {
			strcmp( $a->get_plural_name(), $b->get_plural_name() );
		} );

		// get listings and convert WP_Posts to Listing objects
		$listings = array_filter( array_map( function( $item ) {
			return \MyListing\Src\Listing::get( $item );
		}, $query->query( $query_args ) ) );

		// user stats for dashboard cards
		$stats = mylisting()->stats()->get_user_stats( get_current_user_id() );

		$endpoint = wc_get_account_endpoint_url( \MyListing\my_listings_endpoint_slug() );

		// load template
		mylisting_locate_template( 'templates/dashboard/my-listings.php', [
			'query' => $query,
			'listings' => $listings,
			'stats' => $stats,
			'endpoint' => $endpoint,
			'listing_types' => $listing_types,
			'active_status' => $active_status,
			'active_type' => $active_type,
		] );
	}

	protected function handle_actions() {
		if ( ! ( class_exists( '\WooCommerce' ) && is_wc_endpoint_url( \MyListing\my_listings_endpoint_slug() ) ) ) {
			return;
		}

		if ( empty( $_REQUEST['action'] ) || empty( $_REQUEST['job_id'] ) ) {
			return;
		}

		try {
			if ( empty( $_REQUEST['_wpnonce'] ) || ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'mylisting_dashboard_actions' ) ) {
				throw new \Exception( _x( 'Invalid request.', 'User Dashboard > Listings > Actions', 'my-listing' ) );
			}

			$action = sanitize_title( $_REQUEST['action'] );
			$listing = \MyListing\Src\Listing::get( $_REQUEST['job_id'] );
			if ( ! ( $listing && $listing->editable_by_current_user() ) ) {
				throw new \Exception( _x( 'Invalid listing.', 'User Dashboard > Listings > Actions', 'my-listing' ) );
			}

			// run action
			do_action( 'mylisting/user-listings/handle-action:'.$action, $listing );
		} catch ( \Exception $e ) {
			$this->add_notice( $e->getMessage() );
		}
	}

	protected function delete_listing( $listing ) {
		wp_trash_post( $listing->get_id() );
		$this->add_notice( sprintf(
			_x( '%s has been deleted', 'User Dashboard > Listings > Actions', 'my-listing' ),
			$listing->get_name()
		), 'error' );
	}

	protected function edit_listing() {
		\MyListing\Src\Forms\Edit_Listing_Form::instance()->render();
	}

	/**
	 * Display the `Edit` action for listings in in User Dashboard > My Listings.
	 *
	 * @since 1.0
	 */
	protected function display_edit_action( $listing ) {
		$status = $listing->get_status();
		$can_edit_listing = ( $status === 'publish' || (
			mylisting_get_setting( 'user_can_edit_pending_submissions' ) && $status === 'pending'
		) );

		// only display for published, or pending listings if editing is allowed
		if ( ! $can_edit_listing ) {
			return;
		}

		$edit_url = add_query_arg( [
			'action' => 'edit',
			'job_id' => $listing->get_id(),
		], wc_get_account_endpoint_url( \MyListing\my_listings_endpoint_slug() ) );

		printf(
			'<li class="cts-listing-action-edit">
				<a href="%s" class="job-dashboard-action-edit">%s</a>
			</li>',
			esc_url( $edit_url ),
			__( 'Edit', 'my-listing' )
		);
	}


	/**
	 * Display the `Duplicate` action for listings in in User Dashboard > My Listings.
	 *
	 * @since 2.7.2
	 */
	protected function display_duplicate_action( $listing ) {
		if ( ! in_array( $listing->get_status(), ['publish', 'pending', 'pending_payment', 'expired'], true ) ) {
			return;
		}

		printf(
			'<li class="cts-listing-action-duplicate">
				<a href="#" data-listing-id="%d">%s</a>
			</li>',
			absint( $listing->get_id() ),
			__( 'Duplicate', 'my-listing' )
		);
	}

	/**
	 * Display the `Delete` action for listings in in User Dashboard > My Listings.
	 *
	 * @since 1.0
	 */
	protected function display_delete_action( $listing ) {
		if ( $listing->get_status() === 'pending_payment' && ! empty( $this->pending_orders[ $listing->get_id() ] ) ) {
			return;
		}

		$delete_url = add_query_arg( [
			'action' => 'delete',
			'job_id' => $listing->get_id()
		], wc_get_account_endpoint_url( \MyListing\my_listings_endpoint_slug() ) );

		printf(
			'<li class="cts-listing-action-delete">
				<a href="%s" class="job-dashboard-action-delete">%s</a>
			</li>',
			esc_url( wp_nonce_url( $delete_url, 'mylisting_dashboard_actions' ) ),
			__( 'Delete', 'my-listing' )
		);
	}

	protected function display_pending_payment_actions( $listing ) {
		$add_listing_page = c27()->get_setting( 'general_add_listing_page' );
		if ( ! in_array( $listing->get_status(), [ 'pending_payment', 'preview' ], true ) ) {
			return;
		}

		if ( $listing->get_status() === 'pending_payment' && ! empty( $this->pending_orders[ $listing->get_id() ] ) ) { ?>
			<li class="cts-listing-action-view-order">
				<a href="<?php echo esc_url( add_query_arg( 'order_in', $this->pending_orders[ $listing->get_id() ], wc_get_account_endpoint_url( 'orders' ) ) ) ?>">
					<?php _ex( 'Order details', 'User dashboard', 'my-listing' ) ?>
				</a>
			</li>
		<?php } else {
			$resume_url = add_query_arg( [
				'listing_type' => $listing->type->get_slug(),
				'job_id' => $listing->get_id(),
			], $add_listing_page );

			printf(
				'<li class="cts-listing-action-resume">
					<a href="%s">%s</a>
				</li>',
				esc_url( $resume_url ),
				_x( 'Resume Submission', 'User listings dashboard', 'my-listing' )
			);
		}
	}

	private function add_notice( $message, $type = 'message' ) {
		add_action( 'mylisting/user-listings/before', function() use ( $message, $type ) {
			printf( '<div class="job-manager-%s">%s</div>', esc_attr( $type ), $message );
		} );
	}

	private function get_pending_orders() {
		global $wpdb;

		$list = $wpdb->get_results( $wpdb->prepare( "
			SELECT
			    {$wpdb->posts}.ID AS order_id,
			    {$wpdb->prefix}woocommerce_order_itemmeta.order_item_id AS order_item_id,
			    {$wpdb->prefix}woocommerce_order_itemmeta.meta_value AS listing_id
			FROM `{$wpdb->posts}`

			JOIN {$wpdb->prefix}woocommerce_order_items
				ON {$wpdb->prefix}woocommerce_order_items.order_id = {$wpdb->posts}.ID

			JOIN {$wpdb->prefix}woocommerce_order_itemmeta ON (
				{$wpdb->prefix}woocommerce_order_itemmeta.order_item_id
					= {$wpdb->prefix}woocommerce_order_items.order_item_id
				AND {$wpdb->prefix}woocommerce_order_itemmeta.meta_key = '_job_id'
			)

			WHERE {$wpdb->posts}.post_status IN ( 'wc-pending', 'wc-on-hold' )
			AND {$wpdb->posts}.post_author = %d
			ORDER BY {$wpdb->posts}.post_date DESC
		", get_current_user_id() ), ARRAY_A );

		$orders = [];
		foreach ( (array) $list as $order ) {
			if ( ! isset( $orders[ $order['listing_id'] ] ) ) {
				$orders[ $order['listing_id'] ] = [];
			}

			$orders[ $order['listing_id'] ][] = $order['order_id'];
		}

		return $orders;
	}

	private function get_types_relevant_to_current_user() {
		global $wpdb;

		$results = $wpdb->get_col( $wpdb->prepare( "
			SELECT {$wpdb->postmeta}.meta_value
			FROM {$wpdb->posts}
			INNER JOIN {$wpdb->postmeta} ON (
				{$wpdb->posts}.ID = {$wpdb->postmeta}.post_id
				AND {$wpdb->postmeta}.meta_key = '_case27_listing_type'
			)
			WHERE {$wpdb->posts}.post_type = 'job_listing'
				AND {$wpdb->posts}.post_author = %d
			GROUP BY {$wpdb->postmeta}.meta_value
		", get_current_user_id() ) );

		return array_filter( array_map( function( $slug ) {
			if ( empty( $slug ) ) {
				return null;
			}

			return \MyListing\Src\Listing_Type::get_by_name( $slug );
		}, $results ) );
	}
}
