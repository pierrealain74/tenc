<?php
/**
 * Display listing priority settings in listing
 * edit page in wp-admin.
 *
 * @since 1.7.0
 * @var \MyListing\Src\Listing $listing Current listing instance.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$priority = $listing->get_priority();
$is_custom_priority = false;
$package_label = '';

$choices = [
	[ 'value' => 0, 'label' => 'Normal' ],
	[ 'value' => 1, 'label' => 'Featured' ],
	[ 'value' => 2, 'label' => 'Promoted' ],
];

// Check if this listing has a custom priority value.
if ( ! in_array( $priority, array_column( $choices, 'value' ) ) ) {
	$is_custom_priority = true;
}

if ( ( $package = get_post( $listing->get_data( '_promo_package_id' ) ) ) && $package->post_type === 'cts_promo_package' ) {
	$expires = get_post_meta( $package->ID, '_expires', true );
	$expiry_time = strtotime( $expires, current_time( 'timestamp' ) );

	$package_label .= sprintf(
		'&ndash; <a href="%s" target="_blank">Package #%d</a>',
		esc_url( get_edit_post_link( $package ) ),
		$package->ID
	); ?>

	<div class="priority-package">
		<i class="priority-icon icon-flash"></i>
		<div class="priority-package-info">
			<a href="<?php echo esc_url( get_edit_post_link( $package->ID ) ) ?>">
				<?php printf( _x( 'Promotion Package #%d', 'Priority Settings Metabox', 'my-listing' ), $package->ID ) ?>
			</a>
			<?php if ( $expiry_time ): ?>
				<?php printf(
					'<span>'._x( 'Expires: %s', 'Priority Settings Metabox', 'my-listing' ).'</span>',
					date_i18n( get_option('date_format'), $expiry_time )
				) ?>
			<?php endif ?>
		</div>
	</div>

	<script type="text/javascript">
		jQuery( function( $ ) {
			var confirmed = false;
			$( '.set-listing-priority .cts-radio-item' ).on( 'click', function(e) {
				if ( confirmed ) {
					return true;
				}

				confirmed = confirm( 'Modifying listing priority here will override the one set by package #<?php echo $package->ID ?>. Proceed anyway?' );
				return confirmed;
			});
		} );
	</script>
<?php } ?>

<a href="#" class="cts-show-tip" data-tip="priority-docs"><?php _ex( 'Learn More', 'Priority Settings Metabox', 'my-listing' ) ?></a>

<div class="set-listing-priority cts-setting">
	<div class="cts-radio-list">

		<?php foreach ( $choices as $choice ): ?>
			<div class="cts-radio-item">
				<input
					type="radio"
					name="cts-listing-priority"
					id="cts-listing-priority-<?php echo esc_attr( $choice['value'] ) ?>"
					value="<?php echo esc_attr( $choice['value'] ) ?>"
					<?php checked( $choice['value'], $priority ) ?>
				>
				<label for="cts-listing-priority-<?php echo esc_attr( $choice['value'] ) ?>">
					<?php echo $choice['label'] ?>
					<i class="mi radio_button_unchecked"></i>
					<i class="mi radio_button_checked"></i>
				</label>
			</div>
		<?php endforeach ?>

		<div class="cts-radio-item custom-priority">
			<input
				type="radio"
				name="cts-listing-priority"
				id="cts-listing-priority-custom"
				value="custom"
				<?php checked( $is_custom_priority, true ) ?>
			>
			<label for="cts-listing-priority-custom">
				Custom priority
				<input type="number" name="cts-listing-custom-priority" min="0" value="<?php echo esc_attr( $priority ) ?>">
			</label>
		</div>

	</div>
	<p class="description">
		Set what priority will be given to this listing in search results.
	</p>
</div>
