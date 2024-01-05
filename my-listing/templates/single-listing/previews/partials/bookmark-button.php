<?php
/**
 * Bookmark button for the preview card template.
 *
 * @since 2.2
 */
if ( ! defined('ABSPATH') ) {
	exit;
}

$bookmarked = \MyListing\Src\Bookmarks::exists( $listing->get_id(), get_current_user_id() );
$classes = $bookmarked ? 'bookmarked' : '';
if ( $is_caching ) {
	$classes = '<var #saved></var>';
} ?>
<li data-toggle="tooltip" data-placement="top" title="" data-original-title="<?php esc_attr_e( 'Bookmark', 'my-listing' ) ?>">
    <a class="c27-bookmark-button <?php echo $classes ?>"
       data-listing-id="<?php echo esc_attr( $listing->get_id() ) ?>" onclick="MyListing.Handlers.Bookmark_Button(event, this)">
       <i class="mi favorite_border"></i>
    </a>
</li>