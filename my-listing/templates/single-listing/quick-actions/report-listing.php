<?php
/**
 * `Report Listing` quick action.
 *
 * @since 2.0
 */
if ( ! defined('ABSPATH') ) {
	exit;
}

$attrs = is_user_logged_in()
	? 'href="#" data-toggle="modal" data-target="#report-listing-modal"'
	: sprintf( 'href="%s"', esc_url( \MyListing\get_login_url() ) );
?>
<li id="<?php echo esc_attr( $action['id'] ) ?>" class="<?php echo esc_attr( $action['class'] ) ?>">
    <a <?php echo $attrs ?>>
    	<?php echo c27()->get_icon_markup( $action['icon'] ) ?>
    	<span><?php echo $action['label'] ?></span>
    </a>
</li>