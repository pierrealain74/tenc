<?php
/**
 * Template for rendering an outlined list.
 *
 * @since 1.0
 * @var  $items { name, icon, color, text_color, link }
 */
if ( ! defined('ABSPATH') ) {
	exit;
}

foreach ( $items as $key => $item ) {
	$items[ $key ]['id'] = $item_id = 'li_'.\MyListing\Utils\Random_Id::generate(7);
	\MyListing\Helpers::add_custom_style( ".details-list .{$item_id} a:hover i, .details-list .{$item_id} a:hover .term-icon {
		background-color: {$item['color']} !important;
		border-color: {$item['color']} !important;
		color: {$item['text_color']};
	}" );
}
?>

<ul class="outlined-list details-list social-nav item-count-<?php echo count( $items ) ?>">
	<?php foreach ( $items as $item ): ?>
		<li class="<?php echo esc_attr( $item['id'] ) ?>">
		<?php if ( isset( $links ) && $links || !isset( $links ) ): ?>
			<a href="<?php echo esc_url( $item['link'] ) ?>" <?php echo isset( $item['target'] ) && $item['target'] === '_blank' ? 'target="_blank"' : '' ?>>
				<?php echo $item['icon'] ?>
				<span><?php echo esc_html( $item['name'] ) ?></span>
			</a>
		<?php else: ?>
			<div class="terms-no-link">
				<?php echo $item['icon'] ?>
				<span><?php echo esc_html( $item['name'] ) ?></span>
			</div>
		<?php endif ?>
		</li>
	<?php endforeach ?>
</ul>
