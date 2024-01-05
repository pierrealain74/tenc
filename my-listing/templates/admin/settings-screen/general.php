<?php
/**
 * The template for WP Admin > Listings > Settings screen.
 *
 * @since 2.1
 */
if ( ! defined('ABSPATH') ) {
	exit;
} ?>
<div>
	<h3 class="m-heading mb40"><?php _ex( 'General', 'WP Admin > Listings > Settings', 'my-listing' ) ?></h3>

	<div class="form-group mb30">
		<h4 class="m-heading mb5"><?php _ex( 'Enable Paid Listings', 'WP Admin > Listings > Settings', 'my-listing' ) ?></h4>
		<p class="mt0 mb10">
			<?php _ex( 'This feature allows you to create paid listing plans using WooCommerce.', 'WP Admin > Listings > Settings', 'my-listing' ) ?>
			<?php _ex( 'An account is always required to submit paid listings.', 'WP Admin > Listings > Settings', 'my-listing' ) ?>
			<a href="#" class="cts-show-tip" data-tip="paid-listings"><strong><?php _ex( 'Learn more', 'WP Admin > Listings > Settings', 'my-listing' ) ?></strong></a>
		</p>
		<label class="form-switch">
			<input type="checkbox" name="case27_paid_listings" value="1" <?php checked( '1', $this->get_setting( 'paid_listings_enabled' ) ) ?>>
			<span class="switch-slider"></span>
		</label>
	</div>

	<div class="form-group mb30 account-required-group">
		<h4 class="m-heading mb5">
			<?php _ex( 'Require an account to submit listings', 'WP Admin > Listings > Settings', 'my-listing' ) ?>
		</h4>
		<p class="mt0 mb10">
			<?php _ex( 'Limits listing submissions to registered, logged-in users.', 'WP Admin > Listings > Settings', 'my-listing' ) ?>
		</p>
		<label class="form-switch">
			<input type="checkbox" name="job_manager_user_requires_account" value="1" <?php checked( '1', $this->get_setting( 'submission_requires_account' ) ) ?>>
			<span class="switch-slider"></span>
		</label>
	</div>

	<div class="form-group mb30">
		<h4 class="m-heading mb5">
			<?php _ex( 'Require admin approval of all new listing submissions', 'WP Admin > Listings > Settings', 'my-listing' ) ?>
		</h4>
		<p class="mt0 mb10">
			<?php _ex( 'Sets all new submissions to "pending." They will not appear on your site until an admin approves them.', 'WP Admin > Listings > Settings', 'my-listing' ) ?>
		</p>
		<label class="form-switch">
			<input type="checkbox" name="job_manager_submission_requires_approval" value="1" <?php checked( '1', $this->get_setting( 'submission_requires_approval' ) ) ?>>
			<span class="switch-slider"></span>
		</label>
	</div>

	<div class="form-group mb30">
		<h4 class="m-heading mb5"><?php _ex( 'Allow editing of pending listings', 'WP Admin > Listings > Settings', 'my-listing' ) ?></h4>
		<p class="mt0 mb10">
			<?php _ex( 'Users can continue to edit pending listings until they are approved by an admin.', 'WP Admin > Listings > Settings', 'my-listing' ) ?>
		</p>
		<label class="form-switch">
			<input type="checkbox" name="job_manager_user_can_edit_pending_submissions" value="1" <?php checked( '1', $this->get_setting( 'user_can_edit_pending_submissions' ) ) ?>>
			<span class="switch-slider"></span>
		</label>
	</div>

	<div class="form-group mb30">
		<h4 class="m-heading mb5"><?php _ex( 'Allow editing of published listings', 'WP Admin > Listings > Settings', 'my-listing' ) ?></h4>
		<p class="mt0 mb10">
			<?php _ex( 'Choose whether published listings can be edited and if edits require admin approval. When moderation is required, the original listings will be unpublished while edits await admin approval.', 'WP Admin > Listings > Settings', 'my-listing' ) ?>
		</p>
		<div class="select-wrapper dib">
			<select name="job_manager_user_edit_published_submissions">
				<?php $value = $this->get_setting( 'user_can_edit_published_submissions' ) ?>
				<option value="yes" <?php selected( 'yes', $value ) ?>><?php _ex( 'Users can edit without admin approval', 'WP Admin > Listings > Settings', 'my-listing' ) ?></option>
				<option value="yes_moderated" <?php selected( 'yes_moderated', $value ) ?>><?php _ex( 'Users can edit, but edits require admin approval', 'WP Admin > Listings > Settings', 'my-listing' ) ?></option>
				<option value="no" <?php selected( 'no', $value ) ?>><?php _ex( 'Users cannot edit', 'WP Admin > Listings > Settings', 'my-listing' ) ?></option>
			</select>
		</div>
	</div>

	<div class="form-group mb30">
		<h4 class="m-heading mb5"><?php _ex( 'Listing Duration', 'WP Admin > Listings > Settings', 'my-listing' ) ?></h4>
		<p class="mt0 mb10">
			<?php _ex( 'Listings will display for the set number of days, then expire. Leave this field blank if you don\'t want listings to have an expiration date.', 'WP Admin > Listings > Settings', 'my-listing' ) ?>
		</p>
		<input type="number" class="m-input" style="max-width: 170px;" placeholder="Unlimited" name="job_manager_submission_duration" value="<?php echo esc_attr( $this->get_setting( 'submission_default_duration' ) ) ?>">
	</div>
</div>

<div>
	<h3 class="m-heading mb40 mt60">
		<?php _ex( 'Claim Listings', 'WP Admin > Listings > Settings', 'my-listing' ) ?>
	</h3>

	<div class="form-group mb30">
		<h4 class="m-heading mb5"><?php _ex( 'Enable Claim Listings', 'WP Admin > Listings > Settings', 'my-listing' ) ?></h4>
		<p class="mt0 mb10">
			<?php _ex( 'Allow real owners of listings posted on your site to take ownership of the listing.', 'WP Admin > Listings > Settings', 'my-listing' ) ?>
			<a href="#" class="cts-show-tip" data-tip="claim-listings"><strong><?php _ex( 'Learn more', 'WP Admin > Listings > Settings', 'my-listing' ) ?></strong></a>
		</p>
		<label class="form-switch">
			<input type="checkbox" name="case27_claim_listings" value="1" <?php checked( '1', $this->get_setting( 'claims_enabled' ) ) ?>>
			<span class="switch-slider"></span>
		</label>
	</div>

	<div class="form-group mb30">
		<h4 class="m-heading mb5">
			<?php _ex( 'Require admin approval of all new claim submissions.', 'WP Admin > Listings > Settings', 'my-listing' ) ?>
		</h4>
		<p class="mt0 mb10">
			<?php _ex( 'Sets all new claims to "pending." They will not implemented until an admin approves them.', 'WP Admin > Listings > Settings', 'my-listing' ) ?>
		</p>
		<label class="form-switch">
			<input type="checkbox" name="case27_claim_requires_approval" value="1" <?php checked( '1', $this->get_setting( 'claims_require_approval' ) ) ?>>
			<span class="switch-slider"></span>
		</label>
	</div>

	<div class="form-group mb30">
		<h4 class="m-heading mb5"><?php _ex( 'Mark claimed listings as verified', 'WP Admin > Listings > Settings', 'my-listing' ) ?></h4>
		<p class="mt0 mb10">
			<?php _ex( 'After a listing has been successfully claimed, a verified badge will appear next to the listing title.', 'WP Admin > Listings > Settings', 'my-listing' ) ?>
		</p>
		<label class="form-switch">
			<input type="checkbox" name="mylisting_claims_mark_verified" value="1" <?php checked( '1', $this->get_setting( 'mylisting_claims_mark_verified' ) ) ?>>
			<span class="switch-slider"></span>
		</label>
	</div>

	<div class="form-group mb30">
		<h4 class="m-heading mb5"><?php _ex( 'Claim Listing Page', 'WP Admin > Listings > Settings', 'my-listing' ) ?></h4>
		<p class="mt0 mb10">
			<?php _ex( 'Select the page where you have placed the [claim_listing] shortcode (required).', 'WP Admin > Listings > Settings', 'my-listing' ) ?>
		</p>
		<div class="select-wrapper dib">
			<?php wp_dropdown_pages( [
				'name' => 'job_manager_claim_listing_page_id',
				'sort_column' => 'menu_order',
				'sort_order' => 'ASC',
				'show_option_none' => '&mdash;',
				'selected' => absint( $this->get_setting( 'claims_page_id' ) ),
				'echo' => true,
			] ) ?>
		</div>
	</div>
</div>

<div>
	<h3 class="m-heading mb40 mt60"><?php _ex( 'Notifications', 'WP Admin > Listings > Settings', 'my-listing' ) ?></h3>
	<?php do_action( 'mylisting/settings-screen/notifications' ) ?>
</div>

<div id="captcha-config">
	<h3 class="m-heading mb40 mt60"><?php _ex( 'reCAPTCHA', 'WP Admin > Listings > Settings', 'my-listing' ) ?></h3>

	<div class="form-group mb30">
		<h4 class="m-heading mb5"><?php _ex( 'reCAPTCHA Site Key', 'WP Admin > Listings > Settings', 'my-listing' ) ?></h4>
		<p class="mt0 mb10">
			<?php printf(
				_x( 'You can retrieve your site key from <a target="_blank" href="%s">Google\'s reCAPTCHA admin dashboard</a>.', 'WP Admin > Listings > Settings', 'my-listing' ),
				'https://www.google.com/recaptcha/admin#list'
			) ?>
		</p>
		<input type="text" class="m-input" style="max-width: 250px;" placeholder="site-key" name="job_manager_recaptcha_site_key" value="<?php echo esc_attr( $this->get_setting( 'recaptcha_site_key' ) ) ?>">
	</div>

	<div class="form-group mb30">
		<h4 class="m-heading mb5"><?php _ex( 'reCAPTCHA Secret Key', 'WP Admin > Listings > Settings', 'my-listing' ) ?></h4>
		<p class="mt0 mb10">
			<?php printf(
				_x( 'You can retrieve your secret key from <a target="_blank" href="%s">Google\'s reCAPTCHA admin dashboard</a>.', 'WP Admin > Listings > Settings', 'my-listing' ),
				'https://www.google.com/recaptcha/admin#list'
			) ?>
		</p>
		<input type="text" class="m-input" style="max-width: 250px;" placeholder="secret-key" name="job_manager_recaptcha_secret_key" value="<?php echo esc_attr( $this->get_setting( 'recaptcha_secret_key' ) ) ?>">
	</div>

	<div class="form-group mb30">
		<h4 class="m-heading mb5">
			<?php _ex( 'Display reCAPTCHA field on submission form.', 'WP Admin > Listings > Settings', 'my-listing' ) ?>
		</h4>
		<p class="mt0 mb10">
			<?php _ex( 'This will help prevent bots from submitting listings. You must have entered a valid site key and secret key above.', 'WP Admin > Listings > Settings', 'my-listing' ) ?>
		</p>
		<label class="form-switch">
			<input type="checkbox" name="job_manager_enable_recaptcha_job_submission" value="1" <?php checked( '1', $this->get_setting( 'recaptcha_show_in_submission' ) ) ?>>
			<span class="switch-slider"></span>
		</label>
	</div>
</div>
