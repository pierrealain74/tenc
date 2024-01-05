<?php
/**
 * Template for rendering the `grid` template for gallery block in single listing page.
 *
 * @since 1.0
 */
if ( ! defined('ABSPATH') ) {
	exit;
} ?>
<div class="<?php echo esc_attr( $block->get_wrapper_classes() ) ?>" id="<?php echo esc_attr( $block->get_wrapper_id() ) ?>">
	<div class="element gallery-grid-block carousel-items-<?php echo count( $gallery_items ) ?>">
		<div class="pf-head">
			<div class="title-style-1">
				<i class="<?php echo esc_attr( $block->get_icon() ) ?>"></i>
				<h5><?php echo esc_html( $block->get_title() ) ?></h5>
			</div>
		</div>

		<div class="pf-body">
			<div class="gallery-grid photoswipe-gallery">

				<?php foreach ( $gallery_items as $item ): ?>
					<a class="gallery-item photoswipe-item" href="<?php echo esc_url( $item['full_size_url'] ) ?>">
						<img src="<?php echo esc_url( $item['url'] ) ?>" alt="<?php echo esc_attr( $item['alt'] ) ?>">
					</a>
				<?php endforeach ?>

			</div>
		</div>
	</div>
</div>