<?php
/**
 * Template for displaying Listing Package and Listing Package Subscription
 * settings in the Edit Product page in WP Admin.
 *
 * @since 2.1.6
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $post; ?>

<div class="options_group listing-package-options show_if_job_package <?php echo esc_attr( class_exists( '\WC_Subscriptions' ) ? 'show_if_job_package_subscription' : '' );?>">
	<?php
	if ( class_exists( '\WC_Subscriptions' ) ) {
		woocommerce_wp_select( [
			'id' => '_job_listing_package_subscription_type',
			'wrapper_class' => 'show_if_job_package_subscription',
			'label' => __( 'Subscription Type', 'my-listing' ),
			'description' => __( 'Choose how subscriptions affect this package', 'my-listing' ),
			'value' => get_post_meta( $post->ID, '_package_subscription_type', true ),
			'desc_tip' => true,
			'options' => [
				'listing' => __( 'Link the subscription to posted listings (renew posted listings every subscription term)', 'my-listing' ),
				'package' => __( 'Link the subscription to the package (renew listing limit every subscription term)', 'my-listing' ),
			],
		] );
	}

	woocommerce_wp_text_input( [
		'id'                => '_job_listing_limit',
		'label'             => __( 'Listing limit', 'my-listing' ),
		'description'       => __( 'The number of listings a user can post with this package.', 'my-listing' ),
		'value'             => ( $limit = get_post_meta( $post->ID, '_job_listing_limit', true ) ) ? $limit : '',
		'placeholder'       => __( 'Unlimited', 'my-listing' ),
		'type'              => 'number',
		'desc_tip'          => true,
		'custom_attributes' => [ 'min' => '', 'step' => '1' ],
	] );

	woocommerce_wp_text_input( [
		'id'                => '_job_listing_duration',
		'label'             => __( 'Listing duration', 'my-listing' ),
		'description'       => __( 'The number of days that the listing will be active.', 'my-listing' ),
		'value'             => get_post_meta( $post->ID, '_job_listing_duration', true ),
		'placeholder'       => mylisting_get_setting( 'submission_default_duration' ),
		'desc_tip'          => true,
		'type'              => 'number',
		'custom_attributes' => [ 'min' => '', 'step' => '1' ],
	] );

	woocommerce_wp_checkbox( [
		'id'          => '_job_listing_featured',
		'label'       => __( 'Feature Listings?', 'my-listing' ),
		'description' => __( 'Feature this listing - it will be styled differently and sticky.', 'my-listing' ),
		'value'       => get_post_meta( $post->ID, '_job_listing_featured', true ),
	] );

	woocommerce_wp_checkbox( [
		'id'          => '_listing_mark_verified',
		'label'       => __( 'Mark as verified?', 'my-listing' ),
		'description' => __( 'Listings with this package will have a verified badge show next to their title.', 'my-listing' ),
		'value'       => get_post_meta( $post->ID, '_listing_mark_verified', true ),
	] );

	woocommerce_wp_checkbox( [
		'id'          => '_use_for_claims',
		'label'       => __( 'Use for Claim?', 'my-listing' ),
		'description' => __( 'Allow this package to be an option for claiming a listing.', 'my-listing' ),
		'value'       => get_post_meta( $post->ID, '_use_for_claims', true ),
	] );

	woocommerce_wp_checkbox( [
		'id'          => '_disable_repeat_purchase',
		'label'       => __( 'Disable repeat purchase?', 'my-listing' ),
		'description' => __( 'If checked, this package can only be bought once per user. This can be useful for free listing packages, where you only want to allow the free package to be used once.', 'my-listing' ),
		'value'       => get_post_meta( $post->ID, '_disable_repeat_purchase', true ),
	] ); ?>
	<script type="text/javascript">
		jQuery( function( $ ) {
			$( '.pricing' ).addClass( 'show_if_job_package' );
			$( '._tax_status_field' ).closest( 'div' ).addClass( 'show_if_job_package' );
			$( '#product-type' ).change( function(e) {
				$('#_job_listing_package_subscription_type').change();
			} ).change();
			<?php if ( class_exists( '\WC_Subscriptions' ) ) : ?>
				$('._tax_status_field').closest('div').addClass( 'show_if_job_package_subscription' );
				$('.show_if_subscription, .options_group.pricing').addClass( 'show_if_job_package_subscription' );
				$('#_job_listing_package_subscription_type').change(function() {
					if ( $( '#product-type' ).val() === 'job_package' ) {
						$('#_job_listing_duration').closest('.form-field').show();
						return;
					}

					if ( $(this).val() === 'listing' ) {
						$('#_job_listing_duration').closest('.form-field').hide().val('');
					} else {
						$('#_job_listing_duration').closest('.form-field').show();
					}
				}).change();
				$( 'select#product-type' ).trigger('change');
			<?php endif; ?>
		} );
	</script>
</div>