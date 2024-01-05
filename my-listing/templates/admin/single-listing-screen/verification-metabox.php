<?php
/**
 * Display verification status and ability to modify it.
 *
 * @since 2.1.6
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>
<div class="verification_status">
	<input type="hidden" name="mylisting_modify_verified_status" value="yes">
	<input type="checkbox" name="mylisting_verification_status" id="mylisting_verification_status" value="verified" <?php checked( $listing->is_verified() ) ?>>
	<label for="mylisting_verification_status">
		<img src="<?php echo esc_url( c27()->image('tick.svg') ) ?>" class="verified-tick">
		<p class="verified-text">
			<?php _ex( 'Verified listing!', 'Edit Listing Page Verification Status', 'my-listing' ) ?>
			<span><?php _ex( 'Click to unverify', 'Edit Listing Page Verification Status', 'my-listing' ) ?></span>
		</p>
		<p class="unverified-text">
			<?php _ex( 'Unverified', 'Edit Listing Page Verification Status', 'my-listing' ) ?>
			<span><?php _ex( 'Click to mark verified', 'Edit Listing Page Verification Status', 'my-listing' ) ?></span>
		</p>
	</label>

	<a href="#" class="cts-show-tip" data-tip="verified-listings"><?php _ex( 'Learn More', 'Edit Listing Page Verification Status', 'my-listing' ) ?></a>
</div>
