<?php
/**
 * Template for rendering the "Listing Feed" custom Elementor widget.
 *
 * @var array  $listing_ids
 * @var bool   $invert_nav
 * @var bool   $hide_priority
 * @var string $template
 * @var string $listing_wrap
 *
 * @since 1.0
 */
if ( ! defined('ABSPATH') ) {
	exit;
} ?>

<?php if ( ! $template || in_array( $template, [ 'grid', 'fluid-grid' ], true ) ): ?>
	<section class="i-section listing-feed <?php echo $hide_priority?'hide-priority':'' ?>">
		<div class="container-fluid">
			<div class="row section-body grid">
				<?php foreach ( $listing_ids as $listing_id ): ?>
					<?php printf(
						'<div class="%s">%s</div>',
						$listing_wrap,
						\MyListing\get_preview_card( $listing_id )
					) ?>
				<?php endforeach ?>
			</div>
		</div>
	</section>
<?php endif ?>

<?php if ( $template === 'carousel' ): ?>
	<section class="i-section listing-feed-2 <?php echo $hide_priority?'hide-priority':'' ?>">
		<div class="container">
			<div class="row section-body">
				<div class="owl-carousel listing-feed-carousel">
					<?php foreach ( $listing_ids as $listing_id ): ?>
						<div class="item">
							<?php echo \MyListing\get_preview_card( $listing_id ) ?>
						</div>
					<?php endforeach ?>

					<?php if ( count( $listing_ids ) <= 3 ): ?>
						<?php foreach ( range( 0, absint( count( $listing_ids ) - 4 ) ) as $i ): ?>
							<div class="item c27-blank-slide"></div>
						<?php endforeach ?>
					<?php endif ?>
				</div>
			</div>
			<div class="lf-nav <?php echo $invert_nav?'lf-nav-light':'' ?>">
				<ul>
					<li>
						<a href="#" class="listing-feed-prev-btn">
							<i class="mi keyboard_arrow_left"></i>
						</a>
					</li>
					<li>
						<a href="#" class="listing-feed-next-btn">
							<i class="mi keyboard_arrow_right"></i>
						</a>
					</li>
				</ul>
			</div>
		</div>
	</section>
<?php endif ?>
