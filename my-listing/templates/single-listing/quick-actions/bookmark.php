<?php
/**
 * `Bookmark` quick action.
 *
 * @since 2.0
 */

$is_bookmarked = MyListing\Src\Bookmarks::exists( $listing->get_id(), get_current_user_id() );
$active_label = ! empty( $action['active_label'] ) ? $action['active_label'] : $action['label'];
?>

<li id="<?php echo esc_attr( $action['id'] ) ?>" class="<?php echo esc_attr( $action['class'] ) ?>">
    <a
    	href="#"
    	class="mylisting-bookmark-item <?php echo $is_bookmarked ? 'bookmarked' : '' ?>"
    	data-listing-id="<?php echo esc_attr( $listing->get_id() ) ?>"
    	data-label="<?php echo esc_attr( $action['label'] ) ?>"
    	data-active-label="<?php echo esc_attr( $active_label ) ?>"
        onclick="MyListing.Handlers.Bookmark_Button(event, this)"
    >
    	<?php echo c27()->get_icon_markup( $action['icon'] ) ?>
    	<span class="action-label"><?php echo $is_bookmarked ? $active_label : $action['label'] ?></span>
    </a>
</li>