<?php
/**
 * Display current package information, and ability
 * to switch package / change details.
 *
 * @since 2.1.6
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$package = $listing->get_package();
$product = $package ? $package->get_product() : false;

// check if we need to sync the product id saved in listing meta '_package_id' with the actual package product id.
$should_sync_product_id = ( $product && ( absint( $product->get_id() ) !== absint( $listing->get_product_id() ) ) );

$switch_confirm = _x(
	'This will modify the listing expiry date, priority level (won\'t affect promotions), and increase the assigned package\'s listing count by one. Do you want to proceed?',
	'Edit Listing Package Metabox',
	'my-listing'
);
?>

<div class="package_details">
	<?php if ( $package ): ?>
		<div class="payment_package">

			<?php if ( $image = $package->get_product_image() ): ?>
				<div class="payment_package_image">
					<img src="<?php echo esc_url( $image ) ?>">
				</div>
			<?php else: ?>
				<div class="payment_package_image">
					<div class="image_icon icon-box-2"></div>
				</div>
			<?php endif ?>

			<div class="payment_package_details">
				<div class="payment_package_name">
					<a href="<?php echo esc_url( c27()->get_edit_post_link( $package->get_id() ) ) ?>">
						<strong>#<?php echo $package->get_id() ?></strong>
					</a>
					<?php if ( $product ): ?>
						&middot;
						<a href="<?php echo esc_url( c27()->get_edit_post_link( $product->get_id() ) ) ?>">
							<?php echo $product->get_name() ?>
						</a>
					<?php endif ?>
				</div>
				<div class="payment_package_limit">
					<?php if ( $package->get_limit() ) {
						printf(
							_n( '%s listing posted out of %s', '%s listings posted out of %s', $package->get_count(), 'my-listing' ),
							$package->get_count(),
							$package->get_limit()
						);
					} else {
						printf(
							_n( '%s listing posted', '%s listings posted', $package->get_count(), 'my-listing' ),
							$package->get_count()
						);
					} ?>
					<?php if ( $package->get_status() === 'full' ): ?>
						&middot;
						<strong><?php _ex( 'Full', 'Edit Listing Package Metabox', 'my-listing' ) ?></strong>
					<?php endif ?>
				</div>
			</div>
		</div>
	<?php else: ?>
		<p><em><?php _ex( 'No package assigned.', 'Edit Listing Package Metabox', 'my-listing' ) ?></em></p>
	<?php endif ?>

	<input type="checkbox" name="mylisting_switch_package" id="mylisting_switch_package" value="yes">
	<a href="javascript:void(0)" class="switch_package_toggle">
		<label for="mylisting_switch_package" onclick="return confirm('<?php echo esc_js( $switch_confirm ) ?>')?this.onclick=null||true:false;"><?php _ex( 'Switch Package', 'Edit Listing Package Metabox', 'my-listing' ) ?></label>
	</a>
	<div class="change_package_dropdown">
		<select
			name="mylisting_payment_package" id="mylisting_payment_package" class="custom-select" data-mylisting-ajax="true"
			data-mylisting-ajax-url="mylisting_list_packages" placeholder="<?php _ex( 'Select Package', 'Edit Listing Package Metabox', 'my-listing' ) ?>"
		>
			<?php if ( $package ): ?>
				<option value="<?php echo esc_attr( $package->get_id() ) ?>" selected="selected">
					<?php echo sprintf( _x( 'Current Package: #%s', 'Edit Listing Package Metabox', 'my-listing' ), $package->get_id() ) ?>
				</option>
			<?php endif ?>
		</select>
		<br>
		<div class="package_dropdown_actions">
			<label for="mylisting_switch_package">
				<div class="button"><?php _ex( 'Cancel', 'Edit Listing Package Metabox', 'my-listing' ) ?></div>
			</label>
			<button type="submit" class="button button-primary"><?php _ex( 'Apply', 'Edit Listing Package Metabox', 'my-listing' ) ?></button>
		</div>
	</div>

	<?php if ( $should_sync_product_id ): ?>
		<input type="hidden" name="mylisting_sync_package" value="<?php echo esc_attr( $product->get_id() ) ?>">
	<?php endif ?>
</div>
<a href="#" class="cts-show-tip" data-tip="paid-listings"><?php _ex( 'Learn more', 'Edit Listing Package Metabox', 'my-listing' ) ?></a>
