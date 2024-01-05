<?php

namespace MyListing\Src\Notifications;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Notifications {

	private static $notifications = [
		'\MyListing\Src\Notifications\Listing_Submitted_Admin_Notification',
		'\MyListing\Src\Notifications\Listing_Updated_Admin_Notification',
		'\MyListing\Src\Notifications\Listing_Approved_User_Notification',
		'\MyListing\Src\Notifications\Claim_Status_User_Notification',
		'\MyListing\Src\Notifications\Claim_Status_Admin_Notification',
		'\MyListing\Src\Notifications\Expiring_Listings_User_Notification',
		'\MyListing\Src\Notifications\Expiring_Listings_Admin_Notification',
		'\MyListing\Src\Notifications\Message_Received_User_Notification',
		'\MyListing\Src\Notifications\Listing_Reported_Admin_Notification',
		'\MyListing\Src\Notifications\Review_Submitted_User_Notification',
		'\MyListing\Src\Notifications\Promotion_Expired_User_Notification',
	];

	public static function boot() {
		self::add_hooks();
		add_action( 'mylisting/settings-screen/notifications', [ __CLASS__, 'register_settings' ] );
	}

	/**
	 * Add notification hooks.
	 *
	 * @since 2.1
	 */
	public static function add_hooks() {
		foreach ( self::$notifications as $notification ) {
			call_user_func( $notification.'::hook' );
		}
	}

	public static function register_settings() {
		$values = get_option( 'mylisting_notifications', [] );

		foreach ( self::$notifications as $notification ) {
			$shortname = c27()->class2file( $notification );
			$settings = call_user_func( $notification.'::settings' );
			$send_email_value = isset( $values[ $shortname ], $values[ $shortname ]['send_email'] ) ? $values[ $shortname ]['send_email'] : 'enabled';
			$message = sprintf( 'You can modify this notification message using Loco Translate, or by creating <code>templates/emails/%s.php</code> in the child theme.', $shortname );
			?>
			<div class="form-group mb30">
				<h4 class="m-heading mb5"><?php echo $settings['name'] ?></h4>
				<p class="mt0 mb10"><?php echo $settings['description'] ?></p>
				<div class="select-wrapper dib">
					<select name="<?php echo esc_attr( sprintf( 'mylisting_notifications[%s][send_email]', $shortname ) ) ?>">
						<option value="enabled" <?php selected( 'enabled', $send_email_value ) ?>>Enable Notification</option>
						<option value="disabled" <?php selected( 'disabled', $send_email_value ) ?>>Disable Notification</option>
					</select>
				</div>
				<span class="tips ntf-tip" data-tip="<?php echo esc_attr( $message ) ?>"><i class="mi help_outline"></i></span>
			</div>
		<?php }
	}
}