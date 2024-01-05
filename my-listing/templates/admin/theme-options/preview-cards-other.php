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
<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ) ?>" method="POST">
	<div class="form-group mb30">
		<h4 class="m-heading mb5">Background image/gallery picture quality</h4>
		<p class="mt0 mb10">Set what picture quality to use for the preview card image and gallery backgrounds. Default: <code>medium_large</code></p>
		<div class="select-wrapper dib">
			<select name="bg_size">
				<?php foreach ( MyListing\get_image_sizes() as $key => $size ): ?>
					<option value="<?php echo esc_attr( $key ) ?>" <?php selected( $key, $settings['bg_size'] ) ?>>
						<?php echo esc_html( sprintf( '%s (%s x %s)', $key, $size['width'], $size['height'] ?: 'auto' ) ) ?>
					</option>
				<?php endforeach ?>
			</select>
		</div>
	</div>

	<div class="form-group mb30">
		<h4 class="m-heading mb5">How many gallery slides to load</h4>
		<p class="mt0 mb10">Set how many gallery images to load for preview cards that are configured to display a background gallery.</p>
		<input type="number" class="m-input" style="max-width: 170px;" min="0" name="gallery_count" value="<?php echo absint( $settings['gallery_count'] ) ?>">
	</div>

	<div class="mt30">
		<input type="hidden" name="action" value="mylisting_preview_card_settings">
		<input type="hidden" name="_wpnonce" value="<?php echo esc_attr( wp_create_nonce( 'mylisting_preview_card_settings' ) ) ?>">
		<button type="submit" class="btn btn-primary-alt btn-xs">Save settings</button>
	</div>
</form>