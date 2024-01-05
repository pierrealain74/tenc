<?php
/**
 * `Plain` quick action.
 *
 * @since 2.0
 */

if ( empty( $action['label'] ) ) {
	return;
}

?>

<li id="<?php echo esc_attr( $action['id'] ) ?>" class="<?php echo esc_attr( $action['class'] ) ?>">
    <a href="javascript:void(0)">
    	<?php echo c27()->get_icon_markup( $action['icon'] ) ?>
    	<span><?php echo $action['label'] ?></span>
    </a>
</li>