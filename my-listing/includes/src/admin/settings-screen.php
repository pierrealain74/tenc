<?php

namespace MyListing\Src\Admin;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Settings_Screen {
	use \MyListing\Src\Traits\Instantiatable;

	public $settings_group = 'mylisting_settings';
	public $settings;

	public function __construct() {
		$this->settings = apply_filters( 'mylisting/settings-screen/register-settings', [
			'submission_requires_account' => [
				'name' => 'job_manager_user_requires_account',
				'default' => '1',
			],
			'submission_requires_approval' => [
				'name' => 'job_manager_submission_requires_approval',
				'default' => '1',
			],
			'submission_default_duration' => [
				'name' => 'job_manager_submission_duration',
				'default' => '30',
			],
			'user_can_edit_pending_submissions' => [
				'name' => 'job_manager_user_can_edit_pending_submissions',
				'default' => '0',
			],
			'user_can_edit_published_submissions' => [
				'name' => 'job_manager_user_edit_published_submissions',
				'default' => 'yes',
			],
			'paid_listings_enabled' => [
				'name' => 'case27_paid_listings',
				'default' => '1',
			],
			'claims_enabled' => [
				'name' => 'case27_claim_listings',
				'default' => '1',
			],
			'claims_require_approval' => [
				'name' => 'case27_claim_requires_approval',
				'default' => '1',
			],
			'claims_page_id' => [
				'name' => 'job_manager_claim_listing_page_id',
				'default' => '',
			],
			'mylisting_claims_mark_verified' => [
				'name' => 'mylisting_claims_mark_verified',
				'default' => '1',
			],
			'recaptcha_site_key' => [
				'name' => 'job_manager_recaptcha_site_key',
				'default' => '',
			],
			'recaptcha_secret_key' => [
				'name' => 'job_manager_recaptcha_secret_key',
				'default' => '',
			],
			'recaptcha_show_in_submission' => [
				'name' => 'job_manager_enable_recaptcha_job_submission',
				'default' => '',
			],
			'mylisting_notifications' => [
				'name' => 'mylisting_notifications',
				'default' => '',
			],
		] );

		// add settings page
		add_action( 'admin_menu', [ $this, 'add_settings_page' ], 20 );
		$this->register_settings();
	}

	/**
	 * White list setting keys to be saved on the settings form.
	 *
	 * @since 2.1
	 */
	public function register_settings() {
		foreach ( $this->settings as $key => $setting ) {
			register_setting( $this->settings_group, $setting['name'], [
				'default' => $setting['default'],
			] );
		}
	}

	/**
	 * Add settings page in WP Admin > Listings > Settings.
	 *
	 * @since 2.1
	 */
	public function add_settings_page() {
		add_submenu_page(
			'edit.php?post_type=job_listing',
			_x( 'Settings', 'WP Admin > Listings > Settings', 'my-listing' ),
			_x( 'Settings', 'WP Admin > Listings > Settings', 'my-listing' ),
			'manage_options',
			'mylisting-settings',
			[ $this, 'render' ]
		);
	}

	/**
	 * Retrieve the value for a theme setting.
	 *
	 * @since 2.1
	 */
	public function get_setting( $name ) {
		if ( ! isset( $this->settings[ $name ] ) ) {
			return null;
		}

		return apply_filters( 'mylisting/settings/'.$name, get_option( $this->settings[ $name ][ 'name' ] ) );
	}

	/**
	 * Render the settings page.
	 *
	 * @since 2.1
	 */
	public function render() { ?>
		<div class="wrap mylisting-settings-wrap">
			<form class="mylisting-options" method="post" action="options.php">

				<?php
				if ( ! empty( $_GET['settings-updated'] ) ) {
					flush_rewrite_rules();
					echo '<div class="updated"><p>' . esc_html__( 'Settings successfully saved!', 'my-listing' ) . '</p></div>';
				}

				settings_fields( $this->settings_group );

				require locate_template( 'templates/admin/settings-screen/general.php' );
				?>
				<p class="submit">
					<input type="submit" class="button-primary" value="<?php esc_attr_e( 'Save Changes', 'my-listing' ); ?>" />
				</p>
			</form>
		</div>
	<?php }
}