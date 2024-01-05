<?php

namespace MyListing\Src\Paid_Listings;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Migrate WP Job Manager WC Paid Listing DB to
 * MyListing Paid Listings format.
 *
 * @since 1.6
 */
class WCPL_Importer {
	use \MyListing\Src\Traits\Instantiatable;

	public function __construct() {
		if ( ! ( is_admin() && $this->should_run_migration() ) ) {
			return;
		}

		// add migration admin page
		add_action( 'admin_menu', [ $this, 'add_migration_page' ], 999 );

		// admin notice
		add_action( 'admin_notices', [ $this, 'migrate_admin_notice' ] );
	}

	/**
	 * Add Migration Page.
	 *
	 * @since 1.0.0
	 */
	public function add_migration_page() {
		// Add admin page menu.
		add_submenu_page(
			$parent_slug = 'edit.php?post_type=job_listing',
			$page_title = esc_html__( 'Migrate WP Job Manager WC Paid Listings Package', 'my-listing' ),
			$menu_title = esc_html__( 'Migrate Old Package', 'my-listing' ),
			$capability = 'administrator',
			$menu_slug = 'case27_migrate_wcpl',
			$function = array( $this, 'migration_page' )
		);

		// Remove to hide it.
		remove_submenu_page( 'edit.php?post_type=job_listing', 'case27_migrate_wcpl' );
	}

	/**
	 * Migration Page HTML
	 *
	 * @since 1.0.0
	 */
	public function migration_page() {
		$url = add_query_arg( array(
			'post_type' => 'job_listing',
			'page'      => 'case27_migrate_wcpl',
			'_nonce'    => wp_create_nonce( 'case27_migrate_wcpl' ),
		), admin_url( 'edit.php' ) );
		?>

		<h1><?php esc_html_e( 'Migrate WP Job Manager WC Paid Listings Package', 'my-listing' ); ?></h1>

		<?php if ( ! isset( $_GET['_nonce'] ) || ! wp_verify_nonce( $_GET['_nonce'], 'case27_migrate_wcpl' ) ) : ?>
			<p><?php esc_html_e( 'Invalid request.', 'my-listing' ); ?> <a href="<?php echo esc_url( $url ); ?>"><?php esc_html_e( 'Try Again.', 'my-listing' ); ?></a></p>
			<?php return; ?>
		<?php endif; ?>

		<?php $migrated = $this->run_migration(); ?>

		<p><?php printf( esc_html__( 'Migration complete. %d data migrated.', 'my-listing' ), count( $migrated ) ); ?> <a href="<?php echo esc_url( add_query_arg( 'post_type', 'case27_user_package', admin_url( 'edit.php' ) ) ); ?>"><?php esc_html_e( 'View All Packages.' ); ?></a></p>

		<?php
	}

	/**
	 * Migrate Admin Notice
	 *
	 * @since 1.0.0
	 */
	public function migrate_admin_notice() {
		$screen = get_current_screen();
		if ( 'edit-case27_user_package' !== $screen->id ) {
			return;
		}
		$url = add_query_arg( array(
			'post_type' => 'job_listing',
			'page'      => 'case27_migrate_wcpl',
			'_nonce'    => wp_create_nonce( 'case27_migrate_wcpl' ),
		), admin_url( 'edit.php' ) );
		?>
		<div class="notice notice-info is-dismissible">
			<p><?php _e( 'Old WC Paid Listing data found.', 'my-listing' ); ?> <a href="<?php echo esc_url( $url ); ?>"><?php _e( 'Start data migration.', 'my-listing' ); ?></a></p>
			<p><?php _e( 'IMPORTANT: Please backup all your database before performing this action.' ); ?></p>
		</div>
		<?php
	}

	/**
	 * Migrate/convert all WPCL User Packages to MyListing Paid Listings format.
	 *
	 * @since 1.6
	 * @return array Migrated Data. New Package ID as key, Old package ID as value.
	 */
	public function run_migration() {
		global $wpdb;

		// Bail if migration is not needed.
		if ( ! $this->should_run_migration() ) {
			return [];
		}

		// Check if database exists.
		$table_name = "{$wpdb->prefix}wcpl_user_packages";
		if ( $wpdb->get_var( "SHOW TABLES LIKE '{$table_name}'" ) !== $table_name ) {
			return [];
		}

		// Get all wpcl user packages DB that need to be migrated.
		$packages = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wcpl_user_packages WHERE package_type = %s;", 'job_listing' ), OBJECT_K );

		// Bail if no package found.
		if ( ! $packages || ! is_array( $packages ) ) {
			return [];
		}

		// Count packages before migration.
		$before = count( $packages );

		// Migrated packages.
		$migrated = [];

		// Foreach packages, migrate.
		foreach ( $packages as $wpjm_package ) {

			// Check if it's already imported.
			if ( $wpjm_package->id ) {
				$user_packages = get_posts( [
					'post_type'        => 'case27_user_package',
					'posts_per_page'   => 1,
					'post_status'      => 'any',
					'suppress_filters' => false,
					'fields'           => 'ids',
					'meta_key'         => '_wpjmpl_package_id',
					'meta_value'       => $wpjm_package->id,
				] );

				// If found, skip.
				if ( $user_packages ) {
					continue;
				}
			}

			// create new user package
			$package = \MyListing\Src\Package::create( [
				'user_id'           => absint( $wpjm_package->user_id ),
				'product_id'        => absint( $wpjm_package->product_id ),
				'order_id'          => absint( $wpjm_package->order_id ),
				'featured'          => $wpjm_package->package_featured ? 1 : 0,
				'limit'             => absint( $wpjm_package->package_limit ),
				'count'             => absint( $wpjm_package->package_count ),
				'duration'          => absint( $wpjm_package->package_duration ),
			] );

			if ( ! $package ) {
				continue;
			}

			update_post_meta( $package->get_id(), '_wpjmpl_package_id', absint( $wpjm_package->id ) );

			// Success. Delete old package.

			// Track migrated.
			$migrated[ $package->get_id() ] = $wpjm_package->id;

			// Delete old DB.
			if ( apply_filters( 'case27_paid_listing_migrate_wpcl_delete_old_db', false ) ) {
				$wpdb->delete( "{$wpdb->prefix}wcpl_user_packages", [
					'id' => $wpjm_package->id,
				] );
			}

			// Replace User Package ID in Listing to new one.
			$data = [
				'meta_key' => '_user_package_id',
				'meta_value' => $package->get_id(),
			];
			$where = [
				'meta_key' => '_user_package_id',
				'meta_value' => $wpjm_package->id,
			];
			$updated = $wpdb->update( $wpdb->postmeta, $data, $where );

			// update package status
			$package->maybe_update_status();
		}

		// Migrated.
		$after = count( $migrated );

		// Set to completed only if all data migrated.
		if ( $before === $after ) {
			update_option( 'case27_paid_listing_migration_completed', 1 );
		}

		return $migrated;
	}

	/**
	 * Check if Old Database Exists.
	 *
	 * @since 1.0.0
	 *
	 * @return bool True if migration needed.
	 */
	public function should_run_migration() {
		// allow force running migration through a filter
		if ( apply_filters( 'mylisting/paid-listings/force-run-wcpl-migration', false ) === true ) {
			delete_option( 'case27_paid_listing_migration_completed' );
		}

		// Option. Set if migration completed.
		if ( get_option( 'case27_paid_listing_migration_completed' ) ) {
			return false;
		}

		global $wpdb;

		// Check if old database version. Bail if not needed.
		$wpcl_version = get_option( 'wcpl_db_version', 0 );
		if ( ! $wpcl_version || version_compare( get_option( 'wcpl_db_version', 0 ), '2.1.2', '<' ) ) {
			return false;
		}

		// Check if database exists.
		$table_name = "{$wpdb->prefix}wcpl_user_packages";
		if ( $wpdb->get_var( "SHOW TABLES LIKE '{$table_name}'" ) !== $table_name ) {
			return false;
		}

		// Loop single database to determine if migration needed.
		$packages = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wcpl_user_packages WHERE package_type = %s;", 'job_listing' ), OBJECT_K );
		if ( ! $packages || ! is_array( $packages ) ) {
			return false;
		}

		return true;
	}
}
