<?php
/**
 * `Vist Website` quick action.
 *
 * @since 2.0
 */

if ( ! ( $website = $listing->get_field('website') ) ) {
	return;
}

?>

<li id="<?php echo esc_attr( $action['id'] ) ?>" class="<?php echo esc_attr( $action['class'] ) ?>">
    <a href="<?php echo esc_url( $website ) ?>" target="_blank" rel="nofollow">
    	<?php echo c27()->get_icon_markup( $action['icon'] ) ?>
    	<span><?php echo $action['label'] ?></span>
    </a>
</li>