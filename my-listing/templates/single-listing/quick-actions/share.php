<?php
/**
 * `Share Listing` quick action.
 *
 * @since 2.0
 */

$links = mylisting()->sharer()->get_links( [
    'permalink' => $listing->get_link(),
    'image' => $listing->get_share_image(),
    'title' => $listing->get_name(),
    'description' => $listing->get_share_description(),
    'icons' => true,
] );

if ( ! $links ) {
	return;
}
?>

<li id="<?php echo esc_attr( $action['id'] ) ?>" class="<?php echo esc_attr( $action['class'] ) ?>">
    <a href="#" id="<?php echo esc_attr( $action['id'].'-dd' ) ?>" data-toggle="modal" data-target="#social-share-modal">
        <?php echo c27()->get_icon_markup( $action['icon'] ) ?>
        <span><?php echo $action['label'] ?></span>
    </a>
</li>

<?php
/**
 * Output the markup for the share modal in the site footer,
 * to prevent layout issues/cutout modal.
 */
add_action( 'mylisting/get-footer', function() use ( $links, $action ) { ?>
    <div id="social-share-modal" class="social-share-modal modal modal-27">
        <ul class="share-options" aria-labelledby="<?php echo esc_attr( $action['id'].'-dd' ) ?>">
            <?php foreach ( $links as $link ):
                if ( empty( trim( $link ) ) ) continue; ?>
                <li><?php mylisting()->sharer()->print_link( $link ) ?></li>
            <?php endforeach ?>
        </ul>
    </div>
<?php } ) ?>