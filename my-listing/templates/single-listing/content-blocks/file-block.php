<?php
/**
 * Template for rendering a `file` block in single listing page.
 *
 * @since 1.0
 */
if ( ! defined('ABSPATH') ) {
	exit;
}

// get the field instance
if ( ! ( $field = $listing->get_field_object( $block->get_prop( 'show_field' ) ) ) ) {
	return;
}

// get file list
$files = array_filter( (array) $field->get_value() );
if ( empty( $files ) ) {
	return;
}
?>

<div class="<?php echo esc_attr( $block->get_wrapper_classes() ) ?>" id="<?php echo esc_attr( $block->get_wrapper_id() ) ?>">
	<div class="element files-block files-count-<?php echo count( $files ) ?>">
		<div class="pf-head">
			<div class="title-style-1">
				<i class="<?php echo esc_attr( $block->get_icon() ) ?>"></i>
				<h5><?php echo esc_html( $block->get_title() ) ?></h5>
			</div>
		</div>
		<div class="pf-body">
			<ul class="file-list">

				<?php foreach ( $files as $file ):
					if ( ! ( $basename = pathinfo( $file, PATHINFO_BASENAME ) ) || ! ( $extension = pathinfo( $file, PATHINFO_EXTENSION ) ) ) {
						continue;
					} ?>

					<a href="<?php echo esc_url( $file ) ?>" target="_blank">
						<li class="file">
							<span class="file-icon"><i class="<?php echo esc_attr( \MyListing\Helpers::get_extension_icon( $extension ) ) ?>"></i></span>
							<span class="file-name"><?php echo esc_html( $basename ) ?></span>
							<span class="file-link"><?php _e( 'View', 'my-listing' ) ?><i class="mi open_in_new"></i></span>
						</li>
					</a>
				<?php endforeach ?>

			</ul>
		</div>
	</div>
</div>
