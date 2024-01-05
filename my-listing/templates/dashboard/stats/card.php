<?php
/**
 * Display a dashboard stat in card style.
 *
 * @since 2.0
 */

$data = c27()->merge_options( [
	'value' => '',
	'description' => '',
	'icon' => 'icon-window',
	'background' => '',
	'classes' => '',
	'link' => null,
], $data ) ?>

<div class="col-md-3 col-sm-6 stat-card <?php echo esc_attr( $data['classes'] ) ?>">
	<?php if ( $data['link'] ): ?>
		<a href="<?php echo esc_url( $data['link'] ) ?>">
	<?php endif ?>
		<div class="mlduo-stat-box second" style="background: <?php echo esc_attr( $data['background'] ) ?>;">
			<h2><?php echo $data['value'] ?></h2>
			<p><?php echo $data['description'] ?></p>
			<?php echo c27()->get_icon_markup( $data['icon'] ) ?>
		</div>
	<?php if ( $data['link'] ): ?>
		</a>
	<?php endif ?>
</div>