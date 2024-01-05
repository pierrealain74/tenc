<?php
/**
 * Display the listing expiry date and ability
 * to modify it.
 *
 * @since 2.1.6
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$expiry = $listing->get_expiry_date();
$product = $listing->get_product();
?>
<div class="expiry_details">
	<?php if ( $expiry ): ?>
		<div class="expiry_date">
			<?php echo date_i18n( get_option( 'date_format' ), $expiry->getTimestamp() ) ?>
		</div>
		<div class="expiry_date_diff">
			<?php $time_diff = human_time_diff( $expiry->getTimestamp(), current_time( 'timestamp' ) ); ?>
			<?php if ( $expiry->getTimestamp() >= current_time( 'timestamp' ) ): ?>
				<?php printf( _x( '%s from now', 'Expiry Date Metabox', 'my-listing' ), $time_diff ) ?>
			<?php else: ?>
				<?php printf( _x( '%s ago', 'Expiry Date Metabox', 'my-listing' ), $time_diff ) ?>
			<?php endif ?>
		</div>
	<?php elseif ( $product && $product->is_type( 'job_package_subscription' ) && $product->get_package_subscription_type() === 'listing' ): ?>
		<p><em><?php _ex( 'Listing will expire when the subscription ends.', 'Expiry Date Metabox', 'my-listing' ) ?></em></p>
	<?php else: ?>
		<?php if ( $listing->get_status() === 'publish' ): ?>
			<p><em><?php _ex( 'No expiration date set.', 'Expiry Date Metabox', 'my-listing' ) ?></em></p>
		<?php else: ?>
			<p><em><?php _ex( 'Publish listing to set an expiry date.', 'Expiry Date Metabox', 'my-listing' ) ?></em></p>
		<?php endif ?>
	<?php endif ?>

	<input type="checkbox" name="mylisting_modify_expiry" id="mylisting_modify_expiry" value="yes">
	<a href="javascript:void(0)" class="modify_expiry_toggle">
		<label for="mylisting_modify_expiry"><?php _ex( 'Modify', 'Expiry Date Metabox', 'my-listing' ) ?></label>
	</a>
	<div class="modify_expiry_setting">
		<div class="datepicker-wrapper">
			<input type="text" class="mylisting-datepicker" name="mylisting_expiry_date" value="<?php echo $expiry ? $expiry->format( 'Y-m-d' ) : '' ?>">
		</div>
		<div class="modify_expiry_actions">
			<label for="mylisting_modify_expiry">
				<div class="button"><?php _ex( 'Cancel', 'Expiry Date Metabox', 'my-listing' ) ?></div>
			</label>
			<button type="submit" class="button button-primary"><?php _ex( 'Apply', 'Expiry Date Metabox', 'my-listing' ) ?></button>
		</div>
	</div>
</div>