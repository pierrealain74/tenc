<?php
/**
 * Perform database updates, cleaning, and other optimizations
 * on theme updates.
 *
 * @since 2.2.3
 */

namespace MyListing\Src\Theme_Options\Data_Updater;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Data_Updater {
	use \MyListing\Src\Traits\Instantiatable;

	public function __construct() {
		if ( ! is_admin() ) {
			return;
		}

		// add tab in WP Admin > Theme Tools
		add_filter( 'mylisting/options-page', [ $this, 'add_settings_tab' ], 100 );
		add_action( 'mylisting/options-page/data-updater:main', [ $this, 'render_settings' ] );

		// add endpoint to run updater
		add_action( 'admin_post_mylisting_run_updater', [ $this, 'run_updater' ] );

		// run updater automatically on theme upgrades
		add_action( 'after_switch_theme', [ $this, 'auto_run_updater' ] );
	}

	public function add_settings_tab( $tabs ) {
		$tabs['data-updater'] = '<i class="mi update"></i> Data Updates';
		return $tabs;
	}

	public function render_settings() {
		$updates = $this->get_updates();

		// if an update has been run, show the update logs instead
		$messages = (array) json_decode( get_option( 'mylisting_data_updater_result', null ), ARRAY_A );
		delete_option( 'mylisting_data_updater_result' );

		require locate_template( 'templates/admin/theme-options/data-updater.php' );
	}

	private function get_updates() {
		$completed = (array) json_decode( get_option( 'mylisting_data_updates', null ), ARRAY_A );
		$updates = $this->get_available_updates();
		foreach ( $updates as $handler_key => $handler ) {
			$is_completed = isset( $completed[ $handler_key ] ) && absint( $completed[ $handler_key ] ) === absint( $handler['version'] );
			$updates[ $handler_key ]['completed'] = $is_completed;
		}

		return $updates;
	}

	private function get_updates_for_autorun() {
		$updates = $this->get_updates();
		foreach ( $updates as $handler_key => $handler ) {
			if ( ! $handler['autorun'] || $handler['completed'] ) {
				unset( $updates[ $handler_key ] );
			}
		}

		return $updates;
	}

	private function get_available_updates() {
		return [
			'update_term_counts' => [
				'label' => 'Update term counts',
				'description' => 'Re-count the number of listings each term belongs to.',
				'version' => 2,
				'autorun' => false,
			],

			'cleanup_transients' => [
				'label' => 'Clear transients',
				'description' => 'Removes old transients that are no longer used.',
				'version' => 1,
				'autorun' => true,
			],

			'remove_unused_options' => [
				'label' => 'Remove old options',
				'description' => 'Removes unused options from the wp_options table.',
				'version' => 4,
				'autorun' => true,
			],

			'remove_unused_user_meta' => [
				'label' => 'Remove unused user meta',
				'description' => 'Removes data that\'s no longer needed from the wp_usermeta table.',
				'version' => 1,
				'autorun' => true,
			],

			'migrate_listing_location' => [
				'label' => 'Migrate Listing Location',
				'description' => 'Removes location data that\'s no longer needed from the wp_postmeta table.',
				'version' => 1,
				'autorun' => true,
			],

			'migrate_work_hours' => [
				'label' => 'Migrate Work Hours',
				'description' => 'Removes Work Hours data that\'s no longer needed from the wp_postmeta table.',
				'version' => 1,
				'autorun' => true,
			],
		];
	}

	/**
	 * Handler for the `run_updater` endpoint.
	 *
	 * @since 2.2.3
	 */
	public function run_updater() {
		check_admin_referer( 'mylisting_run_updater' );
		if ( ! current_user_can( 'manage_options' ) ) {
			die;
		}

		if ( empty( $_GET['run'] ) ) {
			die;
		}

		// give it a few minutes to run if necessary
        @set_time_limit(300);

		// load update functions
		require locate_template( 'includes/src/theme-options/data-updater/handlers.php' );

		// get update to run
		$updates = $this->get_updates();
		$completed = (array) json_decode( get_option( 'mylisting_data_updates', null ), ARRAY_A );
		$messages = [];
		$handler_keys = array_map( 'sanitize_text_field', (array) $_GET['run'] );

		foreach ( $handler_keys as $handler_key ) {
			if ( ! isset( $updates[ $handler_key ] ) ) {
				continue;
			}

			// mlog()->warn( 'Running updater for '.$handler_key );
			try {
				// run the update
				$handler = $updates[ $handler_key ];
				$callback = sprintf( '\MyListing\Src\Theme_Options\Data_Updater\Handlers\%s', $handler_key );
				if ( ! function_exists( $callback ) ) {
					throw new \Exception( 'Could not run tool.' );
				}

				$message = $callback();

				// mark as completed (it must match the latest version of updater)
				$completed[ $handler_key ] = absint( $handler['version'] );

				// save message to display the result in wp-admin
				$messages[ $handler_key ] = $message;
			} catch ( \Exception $e ) {
				$messages[ $handler_key ] = $e->getMessage();
			}
		}

		// cleanup other updates
		foreach ( $completed as $update_key => $update_version ) {
			if ( ! isset( $updates[ $update_key ] ) ) {
				unset( $completed[ $update_key ] );
			}
		}

		// save completed tasks in the database
		update_option( 'mylisting_data_updates', wp_json_encode( $completed ) );
		update_option( 'mylisting_data_updater_result', wp_json_encode( $messages ), false );

		if ( ! empty( $_GET['is_ajax'] ) ) {
			return wp_send_json( $messages );
		}

		return wp_safe_redirect( admin_url( 'admin.php?page=mylisting-options&active_tab=data-updater' ) );
	}

	/**
	 * Automatically run updates on theme activation.
	 *
	 * @since 2.2.3
	 */
	public function auto_run_updater() {
		add_action( 'admin_footer', function() {
			$update_endpoint = wp_nonce_url( admin_url( 'admin-post.php?action=mylisting_run_updater&is_ajax=1' ), 'mylisting_run_updater' );
			$autorun_keys = array_keys( $this->get_updates_for_autorun() );
			if ( empty( $autorun_keys ) ) {
				return;
			}
			?>
			<script type="text/javascript">
				jQuery( function($) {
					$.ajax( "<?php echo wp_specialchars_decode( add_query_arg( 'run', $autorun_keys, $update_endpoint ) ) ?>" );
				} );
			</script>
		<?php } );
	}
}