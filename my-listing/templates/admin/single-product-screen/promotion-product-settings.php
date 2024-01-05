<?php
/**
 * Render product settings for Promotion_Product custom product type.
 *
 * @since 1.7
 */
if ( ! defined('ABSPATH') ) {
	exit;
} ?>
<div class="options_group show_if_promotion_package">
	<?php woocommerce_wp_text_input( [
		'id'                => '_promotion_duration',
		'label'             => __( 'Promotion duration (in days)', 'my-listing' ),
		'description'       => __( 'The number of days that the listing will be promoted.', 'my-listing' ),
		'value'             => get_post_meta( $post->ID, '_promotion_duration', true ),
		'placeholder'       => 14,
		'desc_tip'          => true,
		'type'              => 'number',
		'custom_attributes' => [
			'min'   => '',
			'step' 	=> '1',
		],
	] ) ?>

	<?php woocommerce_wp_text_input( [
		'id'                => '_promotion_priority',
		'label'             => __( 'Promotion priority', 'my-listing' ),
		'description'       => __( 'Higher value gives listing with this package more priority. Featured listings have priority set to 1.', 'my-listing' ),
		'value'             => get_post_meta( $post->ID, '_promotion_priority', true ),
		'placeholder'       => 2,
		'desc_tip'          => true,
		'type'              => 'number',
		'custom_attributes' => [
			'min'   => '',
			'step' 	=> '1',
		],
	] ) ?>

	<script type="text/javascript">
		jQuery( function() {
			jQuery( '.pricing' ).addClass( 'show_if_promotion_package' );
			jQuery( '._tax_status_field' ).closest( 'div' ).addClass( 'show_if_promotion_package' );
			jQuery( '#product-type' ).change();
		} );
	</script>
</div>
