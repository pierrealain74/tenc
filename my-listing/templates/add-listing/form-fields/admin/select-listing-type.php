<?php
global $thepostid;
$key = 'case27_listing_type';
$value = get_post_meta( $thepostid, '_case27_listing_type', true );
$options = $listing_types = \MyListing\get_posts_dropdown( 'case27_listing_type', 'post_name', 'post_title', true );

$onchange = empty( trim( $value ) ) ? 'onchange="this.form.submit();"' : '';
?>
<div class="listing-type-select form-group">
	<label for="cts-listing-type-select"><?php _ex( 'Listing Type', 'WP Admin > Edit Listing > Choose listing type', 'my-listing' ) ?>:</label>
	<select name="_case27_listing_type" id="cts-listing-type-select" <?php echo $onchange ?>>
		<option value="">&mdash; <?php _ex( 'Select Type', 'WP Admin > Edit Listing > Choose listing type', 'my-listing' ) ?> &mdash;</option>
		<?php foreach ( $options as $option_value => $option_label ) : ?>
			<option value="<?php echo esc_attr( $option_value ) ?>" <?php selected( $value, $option_value ) ?>>
				<?php echo esc_html( $option_label ); ?>
			</option>
		<?php endforeach; ?>
	</select>
	<p><?php _ex( 'Select a listing type and update the listing for additional fields to show.', 'WP Admin > Edit Listing > Choose listing type', 'my-listing' ) ?></p>
</div>