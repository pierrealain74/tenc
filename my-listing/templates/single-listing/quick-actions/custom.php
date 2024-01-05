<?php
/**
 * `Plain` quick action.
 *
 * @since 2.0
 */

if ( empty( $action['label'] ) || empty( $action['link'] ) ) {
	return;
}

if ( ! ( $link = $listing->compile_string( $action['link'] ) ) ) {
	return;
}

$target = ! empty( $action['open_new_tab'] ) ? 'target="_blank"' : '';
if ( $link[0] === '#' ) {
	$action['class'] .= ' qa-internal-link ';
}
?>

<li id="<?php echo esc_attr( $action['id'] ) ?>" class="<?php echo esc_attr( trim( $action['class'] ) ) ?>">
    <a href="<?php echo esc_url( $link ) ?>" rel="nofollow" <?php echo $target ?>>
    	<?php echo c27()->get_icon_markup( $action['icon'] ) ?>
    	<span><?php echo $action['label'] ?></span>
    </a>
</li>