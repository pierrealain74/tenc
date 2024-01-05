<?php
/**
 * Compare button for the preview card template.
 *
 * @since 2.7
 */
if ( ! defined('ABSPATH') ) {
	exit;
}
?>
<li
   data-toggle="tooltip"
   class="compare-button-li"
   data-placement="top"
   data-original-title="<?php echo esc_attr( _x( 'Add to comparison', 'Preview card compare button', 'my-listing' ) ) ?>"
>
    <a class="c27-compare-button" onclick="MyListing.Handlers.Compare_Button(event, this)">
       <i class="mi add"></i>
    </a>
</li>