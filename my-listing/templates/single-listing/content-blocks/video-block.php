<?php
/**
 * Template for rendering a `video` block in single listing page.
 *
 * @since 1.0
 */
if ( ! defined('ABSPATH') ) {
	exit;
}

// get field value
$video_url = $listing->get_field( $block->get_prop( 'show_field' ) );
$video = \MyListing\Helpers::get_video_embed_details( $video_url );

// validate
if ( ! ( $video_url && $video ) ) {
	return;
}
?>

<div class="<?php echo esc_attr( $block->get_wrapper_classes() ) ?>" id="<?php echo esc_attr( $block->get_wrapper_id() ) ?>">
	<div class="element video-block">
		<div class="pf-head">
			<div class="title-style-1">
				<i class="<?php echo esc_attr( $block->get_icon() ) ?>"></i>
				<h5><?php echo esc_html( $block->get_title() ) ?></h5>
			</div>
		</div>
		<div class="pf-body video-block-body">
			<iframe src="<?php echo esc_attr( $video['url'] ) ?>" frameborder="0" allowfullscreen height="315"></iframe>
		</div>
	</div>
</div>
