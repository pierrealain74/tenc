<?php
/**
 * Listing title column in WP Admin > All Listings.
 *
 * @since 2.1
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$edit_link = admin_url( 'post.php?post=' . $listing->get_id() . '&action=edit' );
$type_filter_link = false;
if ( $listing->type ) {
	$type_filter_link = add_query_arg( [
		'post_type' => 'job_listing',
		'filter_by_type' => $listing->type->get_slug(),
	], admin_url( 'edit.php' ) );
}
?>
<div class="listing-details">
	<a href="<?php echo esc_url( $edit_link ) ?>" class="job_title" title="ID: <?php echo esc_attr( $listing->get_id() ) ?>">
		<?php echo esc_html( $listing->get_name() ) ?>
	</a>
	<?php if ( $listing->type ): ?>
		<br>
		<a href="<?php echo esc_url( $type_filter_link ) ?>"><?php echo esc_html( $listing->type->get_singular_name() ) ?></a>
	<?php endif ?>
</div>
<img class="listing-logo" src="<?php echo esc_url( $listing->get_logo() ?: c27()->image('marker.jpg') ) ?>">

<button type="button" class="toggle-row"><span class="screen-reader-text">Show more details</span></button>
