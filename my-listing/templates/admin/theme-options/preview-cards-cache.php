<?php
/**
 * Template for rendering preview-cards settings.
 *
 * @since 2.2.3
 */
if ( ! defined('ABSPATH') ) {
	exit;
}
?>
<div class="preview-cards" style="max-width:600px;">
	<?php if ( empty( $generate ) ): ?>
		<div class="form-group mb30">
			<h4 class="m-heading mb5">Preview Card Caching</h4>
			<p class="mt0 mb10">
				<a href=""></a>
				Creates a cached version of the preview card for every listing
				<a href="#" class="cts-show-tip" data-tip="preview-card-cache"><strong><?php _ex( 'Learn more', 'WP Admin > Listings > Settings', 'my-listing' ) ?></strong></a>
			</p>
			<?php if ( ! $enabled ): ?>
				<a class="btn btn-primary-alt btn-xxs" href="<?php echo esc_url( $enable_endpoint ) ?>">Enable Now</a>
			<?php else: ?>
				<a
					class="btn btn-secondary btn-xxs"
					title="Disable"
					href="<?php echo esc_url( $enable_endpoint ) ?>"
					onclick="return confirm('Are you sure you want to disable preview card caching?')"
				>Enabled <i class="mi check"></i></a>
			<?php endif ?>
		</div>

		<?php if ( $enabled ): ?>
			<div class="form-group mb30">
				<h4 class="m-heading mb5">Cache status</h4>
				<p class="mt0 mb10">Cache files are stored in the "wp-content/uploads/preview-cards/" directory.</p>
				<div class="btn btn-plain btn-xxs"><?php echo number_format_i18n( $cache_count ) ?> listings are currently cached</div>
			</div>

			<div class="form-group mb30">
				<form method="GET" action="<?php echo esc_url( admin_url( 'admin.php' ) ) ?>">
					<h4 class="m-heading mb5">Regenerate cache</h4>
					<p class="mt0 mb10">
						You can regenerate cache for all listings or only those specific to a listing type. Use this when making changes in the "Preview Card" tab of a listing type.
					</p>
					<div class="select-wrapper mb10" style="max-width:300px;">
						<select name="generate" required>
							<option value="">-- Choose listing type --</option>
							<option value="all">All Listing Types</option>
							<?php foreach ( $listing_types as $listing_type ): ?>
								<option value="<?php echo esc_attr( $listing_type['post_name'] ) ?>">
									<?php echo esc_html( $listing_type['post_title'] ) ?>
								</option>
							<?php endforeach ?>
						</select>
					</div>
					<input type="hidden" name="page" value="mylisting-options">
					<input type="hidden" name="active_tab" value="preview-cards">
					<button type="submit" class="btn btn-primary-alt btn-xs">Regenerate</button>
					<button type="submit" class="btn btn-secondary btn-xs full-regen hide" name="full-regen" value="1">
						Remove all existing cache and regenerate
					</button>
				</form>
			</div>
		<?php endif ?>
	<?php else: ?>
		<div class="form-group mb30 generate-previews" data-ajax="<?php echo esc_url( $generate_endpoint ) ?>">
			<h4 class="m-heading mb5">Generating previews</h4>
			<p class="mt0 mb10">This may take a few minutes to complete <i class="fa fa-spin fa-pulse"></i></p>
			<div class="progress-bar" style="max-width:600px;">
				<div class="progress-pct"></div>
				<span class="progress-info">0%</span>
			</div>
			<div class="generate-finished text-center mt40 hide">
				<h4 class="m-heading mb5" style="color:#689F38;">Previews generated successfully.</h4>
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=mylisting-options&active_tab=preview-cards' ) ) ?>">Go back</a>
			</div>
		</div>
	<?php endif ?>
</div>