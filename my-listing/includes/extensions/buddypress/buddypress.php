<?php
/**
 * Adds compatibility with BuddyPress plugin.
 *
 * @since 1.1
 */

namespace MyListing\Ext\Buddypress;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Buddypress {

	public static function boot() {
		new self;
	}

	public function __construct() {
		if ( ! function_exists( 'buddypress' ) ) {
			return;
		}

		// let buddypress handle avatars
		add_filter( 'mylisting/enable-user-avatars', '__return_false' );
		add_action( 'bp_setup_nav', [ $this, 'add_listings_page' ] );
		add_action( 'template_redirect', [ $this, 'redirect_author_page' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
		add_action( 'case27/user-menu/dashboard/before', [ $this, 'user_menu_items' ] );
	}

	/**
	 * Enqueue BuddyPress specific assets.
	 *
	 * @since 1.1
	 */
	public function enqueue_scripts() {
		if ( ! is_buddypress() ) {
			wp_dequeue_style( 'bp-mentions-css' );
			wp_dequeue_style( 'bp-legacy-css' );
			wp_dequeue_script( 'bp-confirm' );
			wp_dequeue_script( 'bp-widget-members' );
			wp_dequeue_script( 'bp-jquery-query' );
			wp_dequeue_script( 'bp-jquery-cookie' );
			wp_dequeue_script( 'bp-jquery-scroll-to' );
			wp_dequeue_script( 'bp-legacy-js' );
			wp_dequeue_script( 'jquery-atwho' );
			wp_dequeue_script( 'bp-mentions' );
			return;
		}

		$suffix = is_rtl() ? '-rtl' : '';
		wp_enqueue_style( 'mylisting-buddypress', c27()->template_uri( 'assets/dist/buddypress'.$suffix.'.css' ), [], \MyListing\get_assets_version() );
		wp_enqueue_script( 'mylisting-buddypress', c27()->template_uri( 'assets/dist/buddypress.js' ), ['jquery'], \MyListing\get_assets_version(), true );
	}

	/**
	 * Add a custom `Listings` tab in user's BuddyPress profile page.
	 *
	 * @since 1.1
	 */
	public function add_listings_page() {
		bp_core_new_nav_item( [
			'name' => __( 'Listings', 'my-listing' ),
			'slug' => 'listings',
			'default_subnav_slug' => 'listings',
			'position' => 22,
			'show_for_displayed_user' => true,
			'item_css_id' => 'c27-bp-listings',
			'screen_function' => function() {
				add_action( 'bp_template_content', function() {
					require locate_template( 'templates/buddypress/listings-tab.php' );
				} );

				bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
			},
		] );
	}

	/**
	 * With BuddyPress active, redirect all requests to a user's
	 * profile page to the BuddyPress profile page.
	 *
	 * @since 1.1
	 */
	public function redirect_author_page() {
		if ( is_author() && defined( 'BP_MEMBERS_SLUG' ) && ( $author_id = get_query_var( 'author' ) ) ) {
			wp_safe_redirect( bp_core_get_user_domain( $author_id ), 301 );
			exit;
		}
	}

	/**
	 * Add a custom `My Profile` link in the user menu.
	 *
	 * @since 1.1
	 */
	public function user_menu_items() {
		do_action( "case27/user-menu/buddypress-profile/before" ); ?>
			<li class="user-menu-buddypress-profile">
				<a href="<?php echo esc_url( bp_core_get_user_domain( get_current_user_id() ) ); ?>">
					<?php _e( 'My Profile', 'my-listing' ) ?>
				</a>
			</li>
		<?php do_action( "case27/user-menu/buddypress-profile/after" );
	}
}
