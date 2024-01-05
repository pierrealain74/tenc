<?php
/**
 * Template for rendering a colored list.
 *
 * @since 1.0
 * @var  $items { name, icon, color, text_color, link }
 */
if ( ! defined('ABSPATH') ) {
	exit;
} ?>

<div class="listing-details item-count-<?php echo count( $items ) ?>">
	<ul>
		<?php foreach ( $items as $item ): ?>
			<li>
			<?php if ( isset( $links ) && $links || !isset( $links ) ): ?>
				<a href="<?php echo esc_url( $item['link'] ) ?>" <?php echo isset( $item['target'] ) && $item['target'] === '_blank' ? 'target="_blank"' : '' ?>>
					<span class="cat-icon" style="background-color: <?php echo esc_attr( $item['color'] ) ?>;">
						<?php echo $item['icon'] ?>
					</span>
					<span class="category-name"><?php echo esc_html( $item['name'] ) ?></span>
				</a>
			<?php else: ?>
				<div class="terms-no-link">
					<span class="cat-icon" style="background-color: <?php echo esc_attr( $item['color'] ) ?>;">
						<?php echo $item['icon'] ?>
					</span>
					<span class="category-name"><?php echo esc_html( $item['name'] ) ?></span>
				</div>
			<?php endif ?>
			</li>
		<?php endforeach ?>
	</ul>
</div>
