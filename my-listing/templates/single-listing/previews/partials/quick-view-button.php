<?php
/**
 * Quick View button for the preview card template.
 *
 * @since 2.2
 */
if ( ! defined('ABSPATH') ) {
	exit;
} ?>
<li class="item-preview" data-toggle="tooltip" data-placement="top" data-original-title="<?php esc_attr_e( 'Quick view', 'my-listing' ) ?>">
    <a href="#" type="button" class="c27-toggle-quick-view-modal" data-id="<?php echo esc_attr( $listing->get_id() ) ?>">
    	<i class="mi zoom_in"></i>
    </a>
</li>